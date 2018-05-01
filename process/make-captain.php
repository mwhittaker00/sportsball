<?php
require_once('../includes/_connect.inc.php');
require_once('../functions/init.func.php');
$msg = "There was a problem submitting this form. Please try again.";
$success = false;


$player = $_POST['player'];
$team = $_POST['team'];

// get the team id for this player to make sure it's the right team
$stmt = $db->prepare(
	"SELECT team_id
		FROM player_team
		WHERE player_id = ?
		AND team_id = ?
		LIMIT 1"
	);
$stmt->bind_param("ii", $player,$_SESSION['user']['team_id']);
$stmt->execute();
$stmt->bind_result($fetchTeam);
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

  // Set this player to be a team captain. Set all other players to 0
	$stmt = $db->prepare(
		"UPDATE player_team
			SET team_captain = 0
			WHERE team_id = ?"
		);
	$stmt->bind_param("i",$team);
	$stmt->execute();
	$stmt->close();

	$stmt = $db->prepare(
		"UPDATE player_team
			SET team_captain = 1
			WHERE player_id = ?"
		);
	$stmt->bind_param("i",$player);
	$stmt->execute();
	$stmt->close();
	$msg = "Player promoted to captain.";
	$_SESSION['status'] = $msg;
		if ($_SERVER['HTTP_REFERER']){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
		else{
			header("location:/team.php");
		}
    exit();
  }
else{
	$_SESSION['status'] = $msg;
	if ($_SERVER['HTTP_REFERER']){
		header("location:".$_SERVER['HTTP_REFERER']);
	}
	else{
		header("location:/team.php");
	}
}
?>
