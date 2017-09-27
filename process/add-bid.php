<?php
require_once('../includes/_connect.inc.php');
require_once('../functions/init.func.php');
$error = "There was a problem submitting this form. Please try again.";
$success = false;


$player = $_POST['player'];
$bid = $_POST['bid'];
$user = $_SESSION['user']['id'];

if ( empty($bid) || empty($player) || empty($user) ){
	$success = false;
	$error = "A field was left emtpy.";
}

// get the team id for this player to make sure it's not on a team
// and that player is in the bid table
$stmt = $db->prepare(
	"SELECT bid.team_id, bid_id, bid.player_id, bid_amount, bid_show
		FROM player_team
    JOIN bid
    ON bid.player_id = player_team.player_id
		WHERE player_team.player_id = ?
		AND player_team.team_id = 0
		LIMIT 1"
	);
$stmt->bind_param("i", $player);
$stmt->execute();
$stmt->bind_result($fetchTeam, $bid_id, $player_id, $fetchBid_high, $bid_show);
$stmt->fetch();
$stmt->close();

// Make sure the team has enough money
$stmt = $db->prepare(
	"SELECT credits
		FROM team_money
		WHERE team_id = ?
		LIMIT 1"
	);
$stmt->bind_param("i", $_SESSION['user']['team_id']);
$stmt->execute();
$stmt->bind_result($credits);
$stmt->fetch();
$stmt->close();


// validate that selected player isn't on a team
if ( $fetchTeam == 0 ){
	$success = true;
}
else{
	$error = "This player is not a free agent.";
	$success = false;
}

// check if team has enough money for this bid
if ( $bid > $credits ){
	$error = "You don't have enough money.";
	$success = false;
}
else{
	$success = true;
}

// passed the team check, made sure team had enough money, now check new bid against current cost
if ( $success ){
	// is the current bid higher than the max bid on this player?
	$team = 0; // the team that will be assigned to this bid
	if ( $bid > $fetchBid_high){
		$success = true;
		$team = $_SESSION['user']['team_id']; // set winner to current team

		// if the current team is just raising their own bid, don't raise the show bid
		if ( $fetchTeam != $_SESSION['user']['team_id'] ){
			$bid_show = $fetchBid_high+1; // set the new bid_show to lowest possible value
		}
	}
	// if the bid is higher than the showing bid, but less than the max bid, and does not belong to the current bidder?
	else if ( $bid > $bid_show && $bid <= $fetchBid_high && $fetchTeam != $_SESSION['user']['team_id'] ){
		$success = true;
		$team = $fetchTeam; // keep the same team in control of this bid
		$bid_show = $bid; // Raise the showing bid to this current bid
		$bid = $fetchBid_high; // don't want to lower the controlling bid,

		//Prep notification that the current bid is still too high
		$error = "You bid below the controlling bid.";
		$_SESSION['status'] = $error;
	}
	// is the bid higher than the current showing bid, less than the highest bid, but for the winning bidder?
	else if ( $bid > $bid_show && $bid <= $fetchBid_high && $fetchTeam == $_SESSION['user']['team_id'] ){
		$success = false;
		$error = "You already control the winning bid.";
		$_SESSION['status'] = $error;
		header("location:/freeagent.php");
	}
	// if it's too low kick them out
	else{
		$success = false;
		$error = "Your bid is lower than the current bid.";
		$_SESSION['status'] = $error;
		header("location:/freeagent.php");
	}
}

// passed validation tests, do the work
if ( $success && $team != 0 ){
	  // update the bid table
		$stmt = $db->prepare(
			"UPDATE bid
			 	SET bid_amount =  ?,
						bid_show = ?,
						team_id = ?
				WHERE player_id = ?
				LIMIT 1"
			);
		$stmt->bind_param("iiii", $bid,$bid_show,$team,$player_id);
		$stmt->execute();
		$stmt->close();

		// update team money - withdraw total cost of bid
		// We only need to do money transfers if the current team
		//  has placed a controlling bid.
		if ( $team == $_SESSION['user']['team_id'] ){
			// refund the current high bid back to the that controlling team
			// If the current team IS the fetchTeam that's okay, we'll take their
			// new charge away in the next step

			$stmt = $db->prepare(
				"UPDATE team_money
					SET credits = credits + ?
					WHERE team_id = ?
					LIMIT 1"
				);
			$stmt->bind_param("ii", $fetchBid_high, $fetchTeam);
			$stmt->execute();
			$stmt->close();


			// deduct the bid from the current team's credits
			$stmt = $db->prepare(
				"UPDATE team_money
					SET credits = credits - ?
					WHERE team_id = ?
					LIMIT 1"
				);
			$stmt->bind_param("ii", $bid, $_SESSION['user']['team_id']);
			$stmt->execute();
			$stmt->close();
		} else{}

  	header("location:/freeagent.php");
    exit();
  }

else{
	$_SESSION['status'] = $error;
	header("location:/freeagent.php");
}

?>
