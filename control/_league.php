<?php
//check if GET['team'] is set. If not, default to current player's team
$team_id = '';
if(!isset($_GET['division'])){
  $division_id = $_SESSION['user']['division_id'];
}
else{
  $division_id = $_GET['division'];
}

// information for this division
$stmt = $db->prepare(
  "SELECT division_name
    FROM division
    WHERE division_id = ?
    LIMIT 1"
  );
  $stmt->bind_param('i',$division);
  $stmt->execute();
  $stmt->bind_result($division_name);
  $stmt->close();


// information for the teams in this division
$stmt = $db->prepare(
  "SELECT DISTINCT team_name, team.team_id, team_color1,team_color2,
  (SELECT COUNT(win_team_id) FROM game
    JOIN division_slot
    ON division_slot.team_id = win_team_id
    WHERE is_current_season = 1
    AND win_team_id = team.team_id) as win,
  (SELECT COUNT(lose_team_id) FROM game
    JOIN division_slot
    ON division_slot.team_id = lose_team_id
    WHERE is_current_season = 1
    AND lose_team_id = team.team_id) as loss,
  (
   (SELECT COUNT(win_team_id) FROM game
    JOIN division_slot
    ON division_slot.team_id = win_team_id
    WHERE is_current_season = 1
    AND win_team_id = team.team_id)
   -
   (SELECT COUNT(lose_team_id) FROM game
    JOIN division_slot
    ON division_slot.team_id = lose_team_id
    WHERE is_current_season = 1
    AND lose_team_id = team.team_id)
   ) as difference
	FROM team
    JOIN user_team
    ON team.team_id = user_team.team_id
    JOIN user
    ON user.user_id = user_team.user_id
    JOIN team_division
		ON team.team_id = team_division.team_id
		JOIN division
		ON team_division.division_id = division.division_id
    JOIN player_team
    ON player_team.team_id = team.team_id
	WHERE team_division.division_id = ?
    ORDER BY difference DESC");
$stmt->bind_param("s", $division_id);
$stmt->execute();
$stmt->store_result();
$team_num = $stmt->num_rows;
$stmt->bind_result($team_name, $team_id, $pColor, $sColor, $win,$loss,$difference);

$team_result = array();
$i = 0;
while($stmt->fetch()){
  $tmp = [
    'name' => $team_name,
    'id' => $team_id,
    'color1' => $pColor,
    'color2' => $sColor,
    'win' => $win,
    'loss' => $loss,
    'difference' => $difference
  ];
	$team_result[$i] = $tmp;
  $i++;
}

?>
