<?php
require_once('../includes/_connect.inc.php');
require_once('../functions/init.func.php');
$error = "There was a problem submitting this form. Please try again.";
$success = false;


$name = $_POST['name'];
$pColor = $_POST['primary-color'];
$sColor = $_POST['secondary-color'];

if (substr(strtolower($name),0,4) == 'the '){
	$error = 'You can not use "the" to start your team name.';
	$success = false;
}

// check for correct color hex codes
if (strlen($pColor) != 7 || strlen($sColor) != 7 ||
	!preg_match('/#([a-f0-9])/', $pColor) ||
	!preg_match('/#([a-f0-9])/', $sColor)){
		$error = "You entered an incorrect color value. Don't worry, we won't tell";
		$success = false;
  }

if (empty($name) || empty($pColor) || empty($sColor) ){
			$error = "You left a field blank. Please try again.";
      $success = false;
		}
else{
	// passed all the checks
	$success = true;
}

if ( $success ){

  // Check if team name is already in the database
	// compare against lowercase
	$lowername = strtolower($name);
  $stmt = $db->prepare("SELECT LOWER(team_name)
					FROM team
						WHERE team_name = ?
						LIMIT 1");
	$stmt->bind_param("s",$lowername);
	$stmt->execute();
	$stmt->bind_result($fetchTeamname);
	$stmt->fetch();
	$stmt->close();
  if ($fetchTeamname){
		$error = "This team name is already in use. Please try another name.";
		$success = false;
	}
	else{}

  if($success){
  	// Add team to database
  	$stmt = $db->prepare(
  		"INSERT INTO team
  			(team_name, team_color1, team_color2)
  		VALUES
  			(?,?,?)");
  	$stmt->bind_param("sss", $name,$pColor,$sColor);
  	$stmt->execute();
		$insertID = $stmt->insert_id;
  	$stmt->close();

		// link team to user
		$stmt = $db->prepare(
  		"INSERT INTO user_team
  			(user_id, team_id)
  		VALUES
  			(?,?)");
  	$stmt->bind_param("ii", $_SESSION['user']['id'], $insertID);
  	$stmt->execute();
  	$stmt->close();

		// put team in starter division
		$stmt = $db->prepare(
  		"INSERT INTO team_division
  			(division_id, team_id)
  		VALUES
  			(1,?)");
  	$stmt->bind_param("i",$insertID);
  	$stmt->execute();
  	$stmt->close();

		// put team in a schedule slot
		// Update a row in the new division with a NULL team_id
		$stmt = $db->prepare(
			"UPDATE division_slot
				SET team_id = ?
				WHERE division_id = 1
				AND team_id IS NULL
				AND is_current_season = 1
				LIMIT 1"
			);
		$stmt->bind_param("i",$insertID);
  	$stmt->execute();
  	$stmt->close();

		//set team variables to user session and redirect to home page
  	$_SESSION['user']['team_name'] = $name;
		$_SESSION['user']['team_id'] = $insertID;
		$_SESSION['user']['color1'] = $pColor;
		$_SESSION['user']['color2'] = $sColor;
		$_SESSION['user']['division_id'] = 1;
		$_SESSION['user']['division_name'] = "Scrub Tier";

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
  	$stmt->bind_param("iiiiiiiiiiii", $players[0],$_SESSION['user']['team_id'],
		$players[1],$_SESSION['user']['team_id'],
		$players[2],$_SESSION['user']['team_id'],
		$players[3],$_SESSION['user']['team_id'],
		$players[4],$_SESSION['user']['team_id'],
		$players[5],$_SESSION['user']['team_id']);
  	$stmt->execute();
  	$stmt->close();

  	header("location:/home.php");
    exit();
  }
}
$_SESSION['status'] = $error;
header("location:/");
?>
