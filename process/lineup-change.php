<?php
require_once('../includes/_connect.inc.php');
require_once('../functions/init.func.php');
$msg = "There was a problem submitting this form. Please try again.";
$success = false;


$player = $_POST['player'];
$active = $_POST['active'];
$team = $_SESSION['user']['team_id'];



// get the team id for this player to make sure it's the right team
$stmt = $db->prepare(
	"SELECT player_team.team_id, position_id
		FROM player_team
		JOIN player_position
		ON player_position.player_id = player_team.player_id
		WHERE player_team.player_id = ?
		AND player_team.team_id = ?
		LIMIT 1"
	);
$stmt->bind_param("ii", $player,$_SESSION['user']['team_id']);
$stmt->execute();
$stmt->bind_result($fetchTeam,$position);
$stmt->fetch();
$stmt->close();
// validate that submitted team is the logged in team
if ( $fetchTeam == $team ){
	$success = true;
}
else{
	$success = false;
}

if ( $success ){
	// find out how many active players are in this position on this team
	// we need this count for both cases, so do it first
	$stmt = $db->prepare(
		"SELECT player_team.player_id, position_id,
		((`player_speed`+`player_end`+`player_str`+`player_pass`+`player_block`+`player_shot`+`player_catch`+`player_aware`+`player_charisma`)/9) as average_score
			FROM player_team
			JOIN player_position
			ON player_position.player_id = player_team.player_id
			JOIN player
			ON player.player_id = player_team.player_id
			WHERE player_position.position_id = ?
			AND player_team.team_id = ?
			AND player_team.player_id <> ?
			AND player_team.is_active = 1"
		);
	$stmt->bind_param("iii", $position,$_SESSION['user']['team_id'],$player);
	$stmt->execute();
	$stmt->store_result();
	$player_count = $stmt->num_rows;
	$stmt->bind_result($fetchPlayer,$fetchPosition,$average);
	$result = array();

// if active is 0, we're promoting them to 1

	$oldPlayer = 0;
	$replace = false;
	if ( $active == 0 ){
		// what's the position? We only have one center and keeper at a time
		// position_id 3 = center, 4 = keeper

		// if player count is less than 2, but they are a forward or defender, we'll go ahead and add this person anyway
		// if player count is less than 1, but they are a center or keeper, add them without replacing
		if ( ($player_count < 2 && ( $position == 1 || $position == 2) )
	 		||
			($player_count < 1 && ( $position == 3 || $position == 4))
		){
			$replace = false;
		}
		// else there's more than 1, so we need to find the lowest average
		else{

			$tmp = array();
			while ($stmt->fetch()){
				$tmp["$fetchPlayer"] = $average;
			}

			asort($tmp);
			$oldPlayer = key($tmp);
			$replace = true;
		}
		$stmt->close();
		if ( $replace ){
			// Now we need to take the old player set them is_active = 0
			$stmt = $db->prepare(
				"UPDATE player_team
					SET is_active = 0,
						team_captain = 0
					WHERE player_id = ?
					LIMIT 1"
				);
			$stmt->bind_param("i",$oldPlayer);
			$stmt->execute();
			$stmt->close();
		}
		// set the POST player to the new starter
		$stmt = $db->prepare(
			"UPDATE player_team
				SET is_active = 1
				WHERE player_id = ?
				LIMIT 1"
			);
		$stmt->bind_param("i",$player);
		$stmt->execute();
		$stmt->close();

		$msg = "You've promoted your player.";
	} // end if active = 0 check
// if active == 1 then we're demoting them
	else if ( $active == 1 ){
		$stmt = $db->prepare(
			"UPDATE player_team
				SET is_active = 0,
						team_captain = 0
				WHERE player_id = ?
				LIMIT 1"
			);
		$stmt->bind_param("i",$player);
		$stmt->execute();
		$stmt->close();

		$msg = "You've demoted your player.";
	}
	$_SESSION['status'] = $msg;
	header("location:/lineup.php");
	exit();
}
else{
	$_SESSION['status'] = $msg;
	header("location:/lineup.php");
}
?>
