<?php
require_once('../includes/_connect.inc.php');
$error = "There was a problem submitting this form. Please try again.";
$success = false;


$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
$password2 = $_POST['confirm-password'];
$terms = $_POST['terms'];


if (empty($email) || empty($password) || empty($password2) || empty($terms) || empty($username)){
			$error = "You left a field blank. Please try again.";
      $success = false;
		}
if ($password !== $password2){
			$error = "You did not enter matching passwords. Please try again.";
      $success = false;
		}
else{
	$success = true;
	// prepare hashed password
	$passwordHash = password_hash($password, PASSWORD_DEFAULT);
}

if ( $success ){

	// Check if email is already in the database
	$stmt = $db->prepare("SELECT user_email
					FROM user
						WHERE user_email = ?
						LIMIT 1");
	$stmt->bind_param("s",$email);
	$stmt->execute();
	$stmt->bind_result($fetchEmail);
	$stmt->fetch();
	$stmt->close();

  // Check if username is already in the database
  $stmt = $db->prepare("SELECT user_name
					FROM user
						WHERE user_name = ?
						LIMIT 1");
	$stmt->bind_param("s",$username);
	$stmt->execute();
	$stmt->bind_result($fetchUsername);
	$stmt->fetch();
	$stmt->close();
	if ($fetchEmail){
		$error = "This email address is already in use. Try the <a href='/page/login'>Log In</a> page instead.";
		$success = false;
	}
  if ($fetchUsername){
		$error = "This username is already in use. Try to <a href='/'>Log In</a>  instead.";
		$success = false;
	}
	else{
		$keyHash = md5($email.time());
	}

  if($success){
  	// Add person to database
  	$stmt = $db->prepare(
  		"INSERT INTO user
  			(user_email, user_password, user_verificationKey, user_name)
  		VALUES
  			(?,?,?,?)");
  	$stmt->bind_param("ssss", $email,$passwordHash,$keyHash,$username);
  	$stmt->execute();
  	$insertID = $stmt->insert_id;
  	$stmt->close();

  	// PREPARE VERIFICATION email
  	$url = "".$keyHash."/".$insertID."/";
  	$to = $email;
  	$subject = "Welcome to SportsBall!";
  	$message = "Welcome to the league, manager!<br /><br />";
  	$message .= "We need you to verify this email account for us.<br /><br />";
  	$message .= "Use the link below or paste the URL into your browser's address bar to continue.<br /><br />";
  	$message .= $url;

  	$headers = "From: SportsBall <no-reply@fromashes.co>"."\r\n";
  	$headers = "Reply-To: no-reply@sportsball.fromashes.co"."\r\n";
  	$headers .= "MIME-Version: 1.0" . "\r\n";
  	$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";


  	//mail($to,$subject,$message,$headers);
  	$_SESSION['registered'] = 1;
  	$_SESSION['status'] = "Almost ready to go! Check your email for a verification link! It might take a minute or two to arrive.";
  	header("location:/");
    exit();
  }
}
$_SESSION['status'] = $error;
header("location:/");
?>
