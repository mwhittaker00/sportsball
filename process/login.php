<?php
require_once('../includes/_connect.inc.php');

$username = $_POST['username'];
$inPassword = $_POST['password'];

$stmt = $db->prepare(
	"SELECT user_password, user.user_id, user_name,
	user_joined, user_verified, team_name,
    team.team_id, team_color1, team_color2,
    division.division_id, division_name, credits,
	    (SELECT COUNT(win_team_id) FROM game
	     	JOIN division_slot
	     	ON division_slot.team_id = win_team_id
	     	WHERE is_current_season = 1
	    	AND win_team_id = team.team_id) as win,
	    (SELECT COUNT(lose_team_id) FROM game
	     	JOIN division_slot
	     	ON division_slot.team_id = lose_team_id
	     	WHERE is_current_season = 1
	    	AND lose_team_id = team.team_id) as loss
		FROM user
    LEFT JOIN user_team
    ON user.user_id = user_team.user_id
    LEFT JOIN team
    ON team.team_id = user_team.team_id
		LEFT JOIN team_division
		ON team.team_id = team_division.team_id
		LEFT JOIN division
		ON team_division.division_id = division.division_id
		LEFT JOIN team_money
		ON team_money.team_id = team.team_id
	WHERE user_name = ?
	LIMIT 1");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->bind_result($fetchPassword,$user_id, $user_name, $user_joined, $user_verified, $team_name, $team_id, $pColor, $sColor, $division_id, $division_name,$credits,$win,$loss);
$stmt->fetch();
$stmt->close();

$passwordVerified = password_verify($inPassword, $fetchPassword);

if ( $passwordVerified ){
	//Check to make sure $user_verified == 1. If so, continue with the log in.
	// If not, redirect to a page where user can resend the verification email.

	if ( $user_verified != 1 ){
		$success = false;
		$_SESSION['status'] = "This account has not been verified. Check your email for verification instructions.";
		header("location:/");
	}

	// user authenticated and verified email.
	// Set array of $_SESSION['user'][] array. Need to get other information first.
 else{
	 if ( file_exists('/resources/images/uploads/avatar/'.$user_id.'.jpg') ){
     $image = $user_id;
   } else{
     $image = 'default';
   }

		$_SESSION['user'] = [
			'id' => $user_id,
			'name' => $user_name,
			'joined' => $user_joined,
			'verified' => $user_verified,
			'login' => 1,
			'team_name' => $team_name,
			'team_id' => $team_id,
			'color1' => $pColor,
			'color2' => $sColor,
			'division_id' => $division_id,
			'division_name' => $division_name,
			'win' => $win,
			'loss' => $loss,
			'credits' => $credits
		];

    //

		// SET PERSISTENT LOG IN
		$check = $_SESSION['user']['id'].$_SESSION['user']['joined'].$_SESSION['user']['name'];
		$check = md5($check);

		setcookie("user", $_SESSION['user']['id'], time()+36000000, "/");
		setcookie("userKey", $check, time()+36000000, "/");

		//if the user has a team set, send them to home page
		if ( $_SESSION['user']['team_id']){
			header("location:/home.php");
		}

		//else send them to the new team page
		header("location:/new-team.php");
		exit();
	}
}
else{
	$success = false;
	$_SESSION['status'] = "We couldn't find your username or password in the system.";
	//header("location:/");
}
?>
