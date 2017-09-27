<?php
session_start();
session_unset();
setcookie('user', null, -1, '/');
setcookie("userKey", null, -1, '/');
$success = true;
header('Location:/');
?>
