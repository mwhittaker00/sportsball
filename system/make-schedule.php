<?php
// select the division IDs
$stmt = $db->prepare(
	"SELECT division_id FROM division WHERE 1"
);
$stmt->execute();
$stmt->store_result();
$div_count = $stmt->num_rows();
$stmt->bind_result($division_id);
// prepare the list to use in the division_slot query
$divString = '';
$divArray = [];
$i = 0;
while($stmt->fetch()){
	$divString = $divString.$division_id;
	array_push($divArray,$division_id);
	if ( $i + 1 < $div_count ){
		$divString = $divString.',';
	}
	$i++;
}
$stmt->close();
// Clear the division_slot table and rebuild for any division changes.
$stmt = $db->prepare(
	"UPDATE division_slot
	SET is_current_season = 0
	WHERE 1"
);
$stmt->execute();
$stmt->close();
// now rebuild it with existing teams
$stmt = $db->prepare(
	"INSERT INTO division_slot (division_id, group_id, team_id, is_current_season)
		SELECT division_id, 0, team_id, 1 FROM team_division"
	);
	$stmt->execute();
	$stmt->close();
// a division can have up to 12 players, but might have fewer
// need to add NULL placeholder spots for any new players who might join
// the division after a season starts
$totalTeamsCount = 12;
$stmt = $db->prepare(
	"SELECT team_id FROM team_division WHERE division_id = 1"
);
$stmt->execute();
$stmt->store_result();
$actualTeamsCount = $stmt->num_rows();
$remainingTeamsCount = $totalTeamsCount - $actualTeamsCount;
$insertString = '';
if ( $remainingTeamsCount > 0 ){
	for($r = $remainingTeamsCount; $r > 0; $r--){
		$insertString.="(1,0,1)";
		if ( $r > 1 ){
			$insertString.=",";
		}
	}
}
// now add the null team slots
$stmt = $db->prepare(
	"INSERT INTO division_slot (division_id, group_id, is_current_season)
	VALUES".$insertString
);
$stmt->execute();
$stmt->close();
// Select all of the slots from division_slot
$stmt = $db->prepare(
	"SELECT slot_id, division_id
		FROM division_slot
		WHERE is_current_season = 1"
);

$stmt->execute();
$stmt->store_result();
$slot_count = $stmt->num_rows();
$stmt->bind_result($slot_id,$slotDivision_id);

// prepare for up to seven seasons
$season = [
	0 => array(),
	1 => array(),
	2 => array(),
	3 => array(),
	4 => array(),
	5 => array(),
	6 => array()
];

// we need to break the selected slots out and prepare them for
// a db insert
//
// This will also created a round robin seas for each division
// and then shuffles the season.
$gamesArray = array();
while($stmt->fetch()){

	for($s = 0; $s < count($divArray); $s++){
		if ( $slotDivision_id == $divArray[$s]){
			array_push($season[$s],$slot_id);
		}
	}
}

// create an array of the game days to add to the set games
// after they've been shuffled
$gameDayArray = [];
foreach($season as $teams){
	$games = array();
	for ($i=0; $i < count($teams); $i++){

		for ($s=0; $s < count($teams); $s++){
			if ( $teams[$i] != $teams[$s]){
				array_push($games,[$teams[$i],$teams[$s]]);
				// inserts as 0 0 0 0 1 1 1 1 2 2 2 2 etc
				array_push($gameDayArray,$i);
			}
		}
	}
	shuffle($games);
	// need to assign a gameday to each game now after they've been shuffled
	$g = 0;
	$nextSeason = $currentSeason+1;
	foreach($games AS $round => $game){
		$gamesPrep = [];
		$gamesPrep = "(".$game[0].",".$game[1].",".$gameDayArray[$g].",".$nextSeason.")";
		$g++;
		array_push($gamesArray,$gamesPrep);
	}

}

$stmt->close();
// implode the array for an insert-friendly string
$gameString = implode(',',$gamesArray);

$stmt = $db->prepare(
	"INSERT INTO schedule
		(slot_id_1, slot_id_2, game_day, season_number)
		VALUES
		".$gameString
	);
$stmt->execute();
$stmt->close();

//
// Subtract 1 contract season for every player
//
$stmt = $db->prepare(
  "UPDATE contract
    SET seasons_left = seasons_left - 1
    WHERE seasons_left > 0"
  );
$stmt->execute();
$stmt->close();

//
// Any players with 0 seasons need to be taken off of that team
//
$stmt = $db->prepare(
  "UPDATE player_team
	   SET team_id = 0,
		 is_active = 0,
		 team_captain = 0
     WHERE player_id IN (
       SELECT player_id
        FROM contract
        WHERE seasons_left = 0
    )"
  );
  $stmt->execute();
  $stmt->close();

	//
	// Add the new players
	//
	$stmt = $db->prepare(
		"INSERT INTO bid
	(player_id, bid_amount, bid_show)
    SELECT contract.player_id, base_cost, base_cost, player_team.team_id
    	FROM contract
        JOIN player_team
        ON player_team.player_id = contract.player_id
        WHERE player_team.team_id = 0
        AND contract.player_id NOT IN (SELECT player_id FROM bid)"
		);
		$stmt->execute();
	  $stmt->close();

?>
