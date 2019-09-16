<?php
require_once('../includes/_connect.inc.php');
require_once('../functions/init.func.php');
$msg = "There was a problem submitting this form. Please try again.";
$success = false;

$offer_credits = $_POST['credits'];
$offer_players = $_POST['players'];
$offer_team = $_SESSION['user']['team_id'];


$trade_player = $_POST['player'];
$trade_team = $_POST['team'];

// The players are a comma seperated list, need to split and turn into array
// we can have up to five players, so build out an array of five elements
$tmp = explode(',', $offer_players);
$offer_players = [];
for ($i = 0; $i <= 4; $i++) {
  if ($tmp) {
    $offer_players = $tmp;
  } else {
    $offer_players = '';
  }
}

// if no players are ever submitted, the first element will return 0
// if players are submitted and then all cleared, it will be empty
// either options evaluate as false
$offerContainsPlayers = $offer_players[0];

// get the team id for these players to make sure it's the right team and that they actually belong to the logged in user
$stmt = $db->prepare(
	"SELECT DISTINCT team_id
		FROM player_team
		WHERE (player_id = ?
  		OR player_id = ?
  		OR player_id = ?
  		OR player_id = ?
  		OR player_id = ?)
    AND (being_traded = 0
      AND being_offered = 0
      AND is_active = 0)"
	);
$stmt->bind_param("iiiii", $offer_players[0], $offer_players[1], $offer_players[2], $offer_players[3], $offer_players[4]);
$stmt->execute();
$stmt->store_result();
$offer_count = $stmt->num_rows;
$stmt->bind_result($fetch_offer_team_id);
$stmt->fetch();
$stmt->close();
// Now look for the player on the trade block and get their team id so we can compare that to the submitted team id
// data validation. YAY!
$stmt = $db->prepare(
	"SELECT DISTINCT team_id
		FROM player_team
		WHERE player_id = ?
    AND team_id = ?
    AND being_traded = 1
    LIMIT 1"
	);
$stmt->bind_param("ii", $trade_player, $trade_team);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($fetch_trade_team_id);
$stmt->fetch();
$stmt->close();
// does the offering team have enough credits to make this offer?
$stmt = $db->prepare(
	"SELECT credits
		FROM team_money
		WHERE team_id = ?
		LIMIT 1"
	);
$stmt->bind_param("i", $offer_team);
$stmt->execute();
$stmt->bind_result($fetch_credits);
$stmt->fetch();
$stmt->close();
// if we have more than one row, that means we grabbed players from multiple teams
// also check to make sure we've matched with the right team
if ($offer_count == 1
  && $fetch_offer_team_id == $offer_team
  && $fetch_trade_team_id
  && $fetch_credits >= $offer_credits
  && $offerContainsPlayers) {
  $success = true;
} else {
  $msg = "Something went wrong - Invalid team/player combination detected.";
}

if ( $success ){
  // remove credits from the offering team's account
  $stmt = $db->prepare(
    "UPDATE team_money
      SET credits = credits - ?
      WHERE team_id = ?
      LIMIT 1"
    );
  $stmt->bind_param("ii", $offer_credits, $_SESSION['user']['team_id']);
  $stmt->execute();
  $stmt->close();
  // insert this offer into player_team_trade
  $trade_open = 1;
  $stmt = $db->prepare(
    "INSERT INTO player_team_trade
      (team_id_1, team_id_2, trade_player_id, player_id_1, player_id_2, player_id_3, player_id_4, player_id_5, credits, trade_open)
      VALUES
      (?, ?, ?, ?, ?, ?, ?, ?, ?, ? )");
  $stmt->bind_param("iiiiiiiiii", $offer_team, $trade_team, $trade_player, $offer_players[0], $offer_players[1], $offer_players[2], $offer_players[3], $offer_players[4], $offer_credits, $trade_open);
  $stmt->execute();
  $stmt->close();
  // Switch the being_offered value for the offered players
	$stmt = $db->prepare(
		"UPDATE player_team
			SET being_offered = 1
			WHERE player_id IN (?, ?, ?, ?, ?)
      AND team_id = ?"
		);
	$stmt->bind_param("iiiiii", $offer_players[0], $offer_players[1], $offer_players[2], $offer_players[3], $offer_players[4], $offer_team);
	$stmt->execute();
	$stmt->close();

  $msg = "Your offer has been submitted.";

	$_SESSION['status'] = $msg;
	if ($_SERVER['HTTP_REFERER']){
		header("location:".$_SERVER['HTTP_REFERER']);
	} else {
		header("location:/trade.php");
	}
	exit();
}
else{
	$_SESSION['status'] = $msg;
	header("location:/trade.php");
}
?>
*/
