<?php
require_once('../includes/_connect.inc.php');
require_once('../functions/init.func.php');
$msg = "There was a problem submitting this form. Please try again.";
$success = false;

$division = $_SESSION['user']['division_id'];
$post = htmlspecialchars($_POST['comm']);
$team = $_SESSION['user']['team_id'];

if ( empty($post) || empty($division) || empty($team) ){
	$msg = "The message cannot be empty.";
}
// get the division id for this team to make sure it's the right match
$stmt = $db->prepare(
	"SELECT team_id
		FROM team_division
		WHERE team_id = ?
		AND division_id = ?
		LIMIT 1"
	);
$stmt->bind_param("ii",$team, $division);
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

  // Insert the new post
	$stmt = $db->prepare(
		"INSERT INTO post
			(post_content)
			VALUES
			(?)"
		);
	$stmt->bind_param("s",$post);
	$stmt->execute();
	$insertID = $stmt->insert_id;
	$stmt->close();

	// Add division_post link
	$stmt = $db->prepare(
		"INSERT INTO division_post
			(division_id, post_id)
			VALUES
			(?,?)"
		);
	$stmt->bind_param("ss",$division,$insertID);
	$stmt->execute();
	$stmt->close();

	// Add team_post link
	$stmt = $db->prepare(
		"INSERT INTO team_post
			(team_id, post_id)
			VALUES
			(?,?)"
		);
	$stmt->bind_param("ss",$team,$insertID);
	$stmt->execute();
	$stmt->close();

		if ($_SERVER['HTTP_REFERER']){
			header("location:".$_SERVER['HTTP_REFERER']);
		}
		else{
			header("location:/division.php");
		}
    exit();
  }
else{
	$_SESSION['status'] = $msg;
	if ($_SERVER['HTTP_REFERER']){
		//header("location:".$_SERVER['HTTP_REFERER']);
	}
	else{
		header("location:/division.php");
	}
}
?>
