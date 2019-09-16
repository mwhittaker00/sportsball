<?php
require_once('../includes/_connect.inc.php');
require_once('../functions/init.func.php');
$msg = "There was a problem submitting this form. Please try again.";
$success = false;


$player = $_POST['player'];
$team = $_SESSION['user']['team_id'];



// get the team id for this player to make sure it's the right team
$stmt = $db->prepare(
	"SELECT player_team.team_id, position_id, player_team.is_active, ptt.trade_open
		FROM player_team
		JOIN player_position
		ON player_position.player_id = player_team.player_id
		LEFT JOIN player_team_trade ptt
		ON player_team.player_id = ptt.trade_player_id
			AND ptt.trade_status IS NULL
		WHERE player_team.player_id = ?
		AND player_team.team_id = ?
		LIMIT 1"
	);
$stmt->bind_param("ii", $player,$_SESSION['user']['team_id']);
$stmt->execute();
$stmt->bind_result($fetchTeam,$position,$is_active,$trade_open);
$stmt->fetch();
$stmt->close();
// validate that submitted team is the logged in team
if ( $fetchTeam == $team && $is_active == 0 ){
	$success = true;
}
else{
	$success = false;
}

if ($trade_open) {
	$msg = "You cannot remove a player from the trade block while there are offers in place.";
	$success = false;
}

if ( $success ){
  // get the current trade status for this player
  $stmt = $db->prepare(
    "SELECT being_traded
      FROM player_team
      WHERE player_id = ?
      LIMIT 1"
    );
  $stmt->bind_param("i", $player);
	$stmt->execute();
  $stmt->bind_result($trade_status);
  $stmt->fetch();
	$stmt->close();
  // Switch the player's trade status
  if ($trade_status) {
    $trade_status = 0;
    $msg = "Your player has been removed from trade block.";
  } else {
    $trade_status = 1;
    $msg = "Your player is on the trade block.";
  }
	// update player_team to set player's trade status to 1
	$stmt = $db->prepare(
		"UPDATE player_team
			SET being_traded = ?
			WHERE player_id = ?"
		);
	$stmt->bind_param("ii", $trade_status, $player);
	$stmt->execute();
	$stmt->close();

	$_SESSION['status'] = $msg;
	if ($_SERVER['HTTP_REFERER']){
		header("location:".$_SERVER['HTTP_REFERER']);
	} else {
		header("location:/player.php?player=$player");
	}
	exit();
}
else{
	$_SESSION['status'] = $msg;
	header("location:/player.php?player=$player");
}
?>
