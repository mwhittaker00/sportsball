<?php

function processForm($_PATH,$script,$db){

	require_once($_PATH.'/process/'.$script.'.php');

	// $success defined in above script

	// $target may be redefined in above script if
	// // destination is conditional on user data
	if ( isset($success) ){
		// if processed correctly, carry on to next page
		if ( $success ){
			//header('location:'.$target);
		}
		// If the script fails go back and display the error message
		else if ( !$success ){
			//header('location:'.$referer);
		}
	}
	else{
		// $success wasn't set, so some undefined problem was encountered during processing.
		// // Attempt to fail gracefully
		$_SESSION['error'] = "The form was not processed correctly. Please try again.";
		header('location:/page/fail');
	}
	// If script is a success, forward to target page
	// If script fails, redirect referrer page
}

?>
