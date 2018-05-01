<?php
session_start();
$db_server = 'localhost';
	$db_name = 'manager';
	$db_pass = '';
	$db_user = 'root';

$db = new mysqli($db_server,$db_user,$db_pass,$db_name);
if($db->connect_errno > 0){
 die('Unable to connect to database [' . $db->connect_error . ']');
}
?>
