<?php
//We need to create new players for the offseason

// $positions[] is an array of the available positions. Use this to randomly select a position for new players
$positions = ['forward','forward','center','defender','defender','keeper'];
$numberOfPlayers = rand(6,12);
// newPlayer($position) adds a new player to the database and returns the insert ID that we can use to insert into the player_team linking table
$players = [];
$playersTeam = [];
for ($i=0; $i < $numberOfPlayers; $i++){
  $baseSkill = rand(10,35);
  $playerPosition = $positions[rand(0,5)];
  array_push($players,newPlayer($playerPosition,$db,$baseSkill));
}
// add each player to the player_team table with team id of 0 for free agent
$player_teamInsert = '';
$bidInsert = '';
for ( $i=0; $i < count($players); $i++ ){
  // player_team insert build
  $player_teamInsert .= "(".$players[$i].",0)";

  // bid insert build
  $bidInsert .= "(".$players[$i].",500,500,12)";

  if ( $i + 1 != count($players) ){
    $player_teamInsert .= ',';
    $bidInsert .= ',';
  }
}
// add the players to db
$stmt = $db->prepare(
  "INSERT INTO player_team
    (player_id, team_id)
  VALUES". $player_teamInsert
);
$stmt->execute();
$stmt->close();

// now the players need to be added to the bid table
$stmt = $db->prepare(
  "INSERT INTO bid
    (player_id, bid_amount, bid_show, days_left)
  VALUES ".$bidInsert
);
$stmt->execute();
$stmt->close();

// update the bid table to reflect their value
$stmt = $db->prepare(
  "UPDATE bid
	SET bid_amount = (SELECT base_cost FROM contract WHERE contract.player_id = bid.player_id),
    	bid_show = (SELECT base_cost FROM contract WHERE contract.player_id = bid.player_id)
    WHERE team_id IS NULL"
  );
  $stmt->execute();
  $stmt->close();
?>
