<?php
$_PATH =  $_SERVER['DOCUMENT_ROOT'];


function parseUri(){
	$uri = $_SERVER['REQUEST_URI'];
	$parts = explode('/',$uri);
	return $parts;
}

//
/* Will need to change this to the CAS check later on. */
//
function isLoggedIn(){
	// Simple function to make sure user is logged in
	if ( isset($_SESSION['user']['login']) && $_SESSION['user']['login'] === 1 ){
		return true;
	}
	else{
		return false;
	}
}

// Deprecated in SUU implementation. Find use and remove.
function isVerified(){
	// make sure user has verified their email address
	if ( isset($_SESSION['user']['verified']) && $_SESSION['user']['verified'] === 1){
		return true;
	}
	else{
		return false;
	}
}


function simFail(){
	header('location:/page/fail/');
}

//
// Get the highest of the provided skills
//
function highSkill($skillsArray){
	$highest = 0;
	$current = 0;
	$previous = 0;
	for ($i = 0; $i < count($skillsArray); $i++){
		$current = $skillsArray[$i];
		// make sure we're not on the last one
		if ( $current > $highest ){
			$highest = $current;
		}
	}
	return $highest;
}

// creates base stats for a new player
// returns the insert_id for the player
function newPlayer($position,$db,$modifier=0){
	$bonus = [];
	$penalty = [];
	$min = 19+$modifier;
	$max = 26+$modifier;

	// get random first and last name from db
	$stmt = $db->prepare(
		"SELECT name_id FROM `first_name` ORDER BY RAND() LIMIT 0, 1");
	$stmt->execute();
	$stmt->bind_result($firstName);
	$stmt->fetch();
	$stmt->close();

	$stmt = $db->prepare(
		"SELECT last_name_id FROM `last_name` ORDER BY RAND() LIMIT 0, 1");
	$stmt->execute();
	$stmt->bind_result($lastName);
	$stmt->fetch();
	$stmt->close();

	if ($position == "forward"){
		$bonus = ['speed','endurance','shot'];
		$penalty = ['strength','block','catch'];
	}
	else if ($position == "center"){
		$bonus = ['speed','pass','aware'];
		$penalty = ['shot','catch'];
	}
	else if ($position == "defender"){
		$bonus = ['strength','block','pass'];
		$penalty = ['shot','speed','catch'];
	}
	else if ($position == "keeper"){
		$bonus = ['catch','aware','pass'];
		$penalty = ['shot','endurance','strength'];
	}

	if ( rand(1,20) == 1){
		array_push($bonus,'charisma');
	}
	// the skills
	$stat = array('first_name'=>$firstName,
	'last_name'=>$lastName,
	'age'=> rand(19,22),
	'speed' => rand($min,$max),
	'endurance' => rand($min,$max),
	'strength' => rand($min,$max),
	'pass' => rand($min,$max),
	'block' => rand($min,$max),
	'shot' => rand($min,$max),
	'catch' => rand($min,$max),
	'aware' => rand($min,$max),
	'charisma' => rand($min,$max),
	'potential'  => rand(0,100));

	foreach($bonus as $key){
		$stat[$key] = $stat[$key]+7;
	}
	foreach($penalty as $key){
		$stat[$key] = $stat[$key]-7;
	}
	// CREATE A BASE COST for new player's contract
	// find the player's average
	$avg = $stat['speed']+$stat['endurance']+$stat['strength']+$stat['pass']+$stat['block']+$stat['shot']+$stat['catch']+$stat['aware']+$stat['charisma'];
	$potential = $stat['potential'];

	$avg = $avg/9;
	$potential = $potential/10;
	$cost = floor($avg*$potential);

// create the player in the db
	$stmt = $db->prepare(
		"INSERT INTO `player`
		(`first_name`, `last_name`, `player_age`, `player_speed`, `player_end`, `player_str`, `player_pass`, `player_block`, `player_shot`, `player_catch`, `player_aware`, `player_charisma`, `player_potential`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?)");
	$stmt->bind_param("iiiiiiiiiiiii", $stat['first_name'],$stat['last_name'],$stat['age'],
	$stat['speed'],$stat['endurance'],$stat['strength'],
	$stat['pass'],$stat['block'],$stat['shot'],$stat['catch'],
	$stat['aware'],$stat['charisma'],$stat['potential']);
	$stmt->execute();
	$insertID = $stmt->insert_id;
	$stmt->close();

// get the ID for the submitted position_id
	$stmt = $db->prepare(
		"SELECT position_id FROM position WHERE position_name = ? LIMIT 1"
	);
	$stmt->bind_param('s',$position);
	$stmt->execute();
	$stmt->bind_result($position_id);
	$stmt->fetch();
	$stmt->close();

// link the player to the player_position table
	$stmt = $db->prepare(
		"INSERT INTO player_position
			(player_id, position_id)
			VALUES
			(?,?)"
		);
	$stmt->bind_param('ii',$insertID,$position_id);
	$stmt->execute();
	$stmt->close();

// add player to the contract table
	$stmt = $db->prepare(
		"INSERT INTO contract
		(player_id,base_cost)
		VALUES
		(?,?)"
	);
	$stmt->bind_param('ii',$insertID,$cost);
	$stmt->execute();
	$stmt->close();

	return($insertID);

}
?>
