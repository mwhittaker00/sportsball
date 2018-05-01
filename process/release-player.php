<?php
require_once('../includes/_connect.inc.php');
require_once('../functions/init.func.php');
$msg = "There was a problem submitting this form. Please try again.";
$success = false;


$player = $_POST['player'];
$team = $_SESSION['user']['team_id'];



// get the team id for this player to make sure it's the right team
$stmt = $db->prepare(
	"SELECT player_team.team_id, position_id, player_team.is_active
		FROM player_team
		JOIN player_position
		ON player_position.player_id = player_team.player_id
		WHERE player_team.player_id = ?
		AND player_team.team_id = ?
		LIMIT 1"
	);
$stmt->bind_param("ii", $player,$_SESSION['user']['team_id']);
$stmt->execute();
$stmt->bind_result($fetchTeam,$position,$is_active);
$stmt->fetch();
$stmt->close();
// validate that submitted team is the logged in team
if ( $fetchTeam == $team && $is_active == 0 ){
	$success = true;
}
else{
	$success = false;
}

if ( $success ){
	// update player_team to set player's team to 0
	$stmt = $db->prepare(
		"UPDATE player_team
			SET team_id = 0
			WHERE player_id = ?"
		);
	$stmt->bind_param("i", $player);
	$stmt->execute();
	$stmt->close();

	// add player to bids table
	$stmt = $db->prepare(
	  "INSERT INTO bid
	    (player_id, bid_amount, bid_show, days_left)
	  VALUES (?,500,500,10)"
	);
	$stmt->bind_param("i",$player);
	$stmt->execute();
	$stmt->close();

	// update bidding price for new free agents
	// update the bid table to reflect their value
	$stmt = $db->prepare(
	  "UPDATE bid
		SET bid_amount = (SELECT base_cost FROM contract WHERE contract.player_id = ?),
	    	bid_show = (SELECT base_cost FROM contract WHERE contract.player_id = ?)
	    WHERE team_id IS NULL"
  );
	$stmt->bind_param("ii",$player,$player);
  $stmt->execute();
  $stmt->close();

	$msg = "You've released your player.";

	$_SESSION['status'] = $msg;
	header("location:/lineup.php");
	exit();
}
else{
	$_SESSION['status'] = $msg;
	header("location:/lineup.php");
}
?>
