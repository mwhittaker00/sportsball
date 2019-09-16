<?php
require_once('../includes/_connect.inc.php');
require_once('../functions/init.func.php');
$msg = "There was a problem submitting this form. Please try again.";
$success = false;

if (isset($_POST['id'])){
    $trade = $_POST['id'];
    $success = true;
}
$team = $_SESSION['user']['team_id'];

// find out if trade is ACCEPT or DECLINE
// accept is true/affirmative
// decline is false/negative
if (isset($_POST['accept'])){
  $decision = 1;
} else if (isset($_POST['decline'])) {
  $decision = 0;
} else {
  $msg = "No valid decision was selected.";
  $success = false;
}

if ( $success ){
  // Get the details for this trade offer
  $stmt = $db->prepare(
    "SELECT team_id_1, trade_player_id, player_id_1, player_id_2, player_id_3, player_id_4, player_id_5, credits
    FROM player_team_trade
    WHERE team_id_2 = ?
    AND trade_id = ?
    LIMIT 1"
  );
  $stmt->bind_param("ii", $team, $trade);
  $stmt->execute();
  $stmt->bind_result($offer_team, $trade_player, $offer1, $offer2, $offer3, $offer4, $offer5, $credits);
  $stmt->fetch();
  $stmt->close();

  // update this offer to status of the decision
  $stmt = $db->prepare(
    "UPDATE player_team_trade
      SET trade_open = 0,
        trade_status = ?
      WHERE trade_id = ?"
    );
  $stmt->bind_param("ii", $decision, $trade);
  $stmt->execute();
  $stmt->close();

  // IF DEAL HAS BEEN ACCEPTED
  if ($decision) {
    // update player_team to set the new teams and update the being_offered value for the player(s) being OFFERED in this trade
    $stmt = $db->prepare(
      "UPDATE player_team
        SET team_id = ?,
          being_offered = 0
        WHERE player_id IN (?, ?, ?, ?, ?)"
      );
    $stmt->bind_param("iiiiii", $team, $offer1, $offer2, $offer3, $offer4, $offer5);
    $stmt->execute();
    $stmt->close();

    // add any credits to the trading team
    $stmt = $db->prepare(
      "UPDATE team_money
        SET credits = credits + ?
        WHERE team_id = ?
        LIMIT 1"
      );
    $stmt->bind_param("ii", $credits, $team);
    $stmt->execute();
    $stmt->close();

    // Update the TRADE BLOCK player to the offering team, update being_traded
    $stmt = $db->prepare(
      "UPDATE player_team
        SET team_id = ?,
          being_traded = 0
        WHERE player_id = ?"
      );
    $stmt->bind_param("ii", $offer_team, $trade_player);
    $stmt->execute();
    $stmt->close();

  } else {
    // IF THE DEAL WAS DECLINED
    // Return the players and the team's money
    // update player_team to reset the being_offered status to 0 for the OFFERED PLAYERS
    $stmt = $db->prepare(
      "UPDATE player_team
        SET being_offered = 0
        WHERE player_id IN (?, ?, ?, ?, ?)"
      );
    $stmt->bind_param("iiiii", $offer1, $offer2, $offer3, $offer4, $offer5);
    $stmt->execute();
    $stmt->close();

    // return any credits to the OFFERING team
    $stmt = $db->prepare(
      "UPDATE team_money
        SET credits = credits + ?
        WHERE team_id = ?
        LIMIT 1"
      );
    $stmt->bind_param("ii", $credits, $offer_team);
    $stmt->execute();
    $stmt->close();

    // We don't update the player's trade block status here, that's either done in a cron job or when the user manually removes their player from the block
  }


  $msg = "Your decision has been submitted.";
  $_SESSION['status'] = $msg;
	header("location:/lineup.php");
	exit();
}
else{
	$_SESSION['status'] = $msg;
	header("location:/lineup.php");
}
?>
