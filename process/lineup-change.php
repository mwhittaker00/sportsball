<?php
require_once('../includes/_connect.inc.php');
require_once('../functions/init.func.php');
$error = "There was a problem submitting this form. Please try again.";
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
	if ( $active == 0 ){

		$oldPlayer = 0;
		// if there's only one player in this position, set that player id as the one to send to the bench
		if ( $player_count == 1 ){
			$oldPlayer = $fetchPlayer;
		}
		// else there's more than 1, so we need to find the lowest average
		else{
			$lowAverage = 10000000;
			while ($stmt->fetch()){
				// is this current average lower than the one we've set? Make it our new low
				// also set the current "old player" to this player for now
				if ( $average < $lowAverage ){
					$lowAverage = $average;
					$oldPlayer = $fetchPlayer;
				}
			}
		}
		$stmt->close();

		// Now we need to take the old player set them is_active = 0
		$stmt = $db->prepare(
			"UPDATE player_team
				SET is_active = 0
				WHERE player_id = ?
				LIMIT 1"
			);
		$stmt->bind_param("i",$oldPlayer);
		$stmt->execute();
		$stmt->close();
		// set the POST player to the new spl_autoload_register
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

	header("location:/lineup.php");
	exit();
}
else{
	$_SESSION['status'] = $error;
	header("location:/lineup.php");
}
?>
