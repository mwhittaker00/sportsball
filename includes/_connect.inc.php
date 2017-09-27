<?php
session_start();
$db_server = 'localhost';
	$db_name = 'manager';
	/* sportsball */
	$db_pass = '';
	/* 2Z\}eQM-;u8VE3W+~yx~U */
	$db_user = 'root';
	/* sportsball */

$db = new mysqli($db_server,$db_user,$db_pass,$db_name);
if($db->connect_errno > 0){
 die('Unable to connect to database [' . $db->connect_error . ']');
}
?>
