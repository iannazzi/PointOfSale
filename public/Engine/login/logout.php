<?php # Script 11.6 - logout.php

require_once ('../../Config/config.inc.php');
ini_set('session.save_path',SESSION_PATH);
//ini_set('session.gc_maxlifetime', 86400);
session_start(); // Start the session.

if (!isset($_SESSION['pos_user_id'])) 
{

	$url = POS_URL . '/index.php';
	//echo $url;
	header("Location: $url");
	exit(); // Quit the script.
	
} 
else 
{ // Cancel the session.
	//setcookie('inventory_store_id', '', time()-3600, '/', '', 0, 0); 
	//setcookie('default_store_id', '', time()-3600, '/', '', 0, 0); 
	//setcookie('company', '', time()-3600, '/', '', 0, 0); 
	require_once(MYSQL_POS_CONNECT_FILE);
	$dbc = pos_connection();
	$sql = "DELETE FROM pos_users_logged_in WHERE pos_user_id = ".$_SESSION['pos_user_id'];
			$result = @mysqli_query ($dbc, $sql);
			if (!$result) 
			{ 
				trigger_error( '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $sql . '</p>', E_USER_WARNING);
				exit();
			}	
	mysqli_close($dbc);
	$_SESSION = array(); // Clear the variables.
	session_destroy(); // Destroy the session itself.
	setcookie('PHPSESSID', '', time()-3600, '/', '', 0, 0); // Destroy the cookie.
}	
	$url =POS_ENGINE_URL . '/login/login.php';
	//echo $url;
	header("Location: $url");
	exit(); // Quit the script.
?>

