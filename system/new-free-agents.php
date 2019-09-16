<?php
require_once('../includes/_init.inc.php');
require_once('../functions/init.func.php');


		//We need to create 6 new players for the team
		// newPlayer($position) adds a new player to the database and returns the insert ID that we can use to insert into the player_team linking table
		$players = [];
		array_push($players,newPlayer('forward',$db));
		array_push($players,newPlayer('forward',$db));
		array_push($players,newPlayer('center',$db));
		array_push($players,newPlayer('defender',$db));
		array_push($players,newPlayer('defender',$db));
		array_push($players,newPlayer('keeper',$db));

		// add the players to this team
		$stmt = $db->prepare(
  		"INSERT INTO player_team
  			(player_id, team_id)
  		VALUES
  			(?,?),
				(?,?),
				(?,?),
				(?,?),
				(?,?),
				(?,?)");
		$team_id = 0;
  	$stmt->bind_param("iiiiiiiiiiii", $players[0],$team_id,
		$players[1],$team_id,
		$players[2],$team_id,
		$players[3],$team_id,
		$players[4],$team_id,
		$players[5],$team_id);
  	$stmt->execute();
  	$stmt->close();

?>
