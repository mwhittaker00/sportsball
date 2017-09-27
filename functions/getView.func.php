<?php

function getView($path,$db){
	$displayName ='';
	if ( isset($_SESSION['user'] )){
		if ( empty($_SESSION['user']['fname'] )){
			$displayName = $_SESSION['user']['email'];
		}
		else{
			$displayName = $_SESSION['user']['fname'];
		}
	}
	
	$_uri = $_SERVER['REQUEST_URI'];
	if (!isset($_GET['view']) && !isset($_GET['method']) ){
		$view = 'page';
		$method = 'home';
	}
	if ( isset($_GET['view']) ){
		$view = $_GET['view'];

		if ( isset($_GET['method']) ){
			$method = $_GET['method'];
		}
		if ( isset($_GET['var']) ){
			$var = $_GET['var'];
			// For pages that use a numeric variable to pull from DB
			if ( ctype_digit($var) && ( $method == 'regions' || $method == 'players') ){
				$method = $method."-view";
			}
			// Edit Region page uses SESSION variable, don't allow ID to be passed.
			else if ( $method == 'regions' && $var == 'edit' ){
				$method = $method."-edit";
			}

			// Temporary BETA ACCESS KEY statement. Remove at launch.
			else if ( $method == 'register' && strlen($var) == 32 ){
				$method = 'registerCopy';
			}
			else{
				simFail();
			}
		}
	}
	if ( !isset($view) ){
	}

	// Set default page title. Can be changed in CONTORL pages
	$pageTitle = ucfirst($_GET['method']);

	require_once($path.'/control/'.$method.'.php');
	require_once($path.'/includes/dochead.inc.php');
	require_once($path.'/views/'.$method.'.php');
	require_once($path.'/includes/footer.inc.php');
}

?>
