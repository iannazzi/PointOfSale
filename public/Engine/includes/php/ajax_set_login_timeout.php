<?php
require_once ('../../../Config/config.inc.php');
ini_set('session.save_path',SESSION_PATH);
session_start(); // Start the session.
if(isset($_SESSION['last_accessed']) && isset($_SESSION['timeout']) ) 
{
	$_SESSION['last_accessed'] = time();
	echo $_SESSION['last_accessed'];
}
else
{
	echo 'NULL';
}
?>