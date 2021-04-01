<?php
require_once ('../../../Config/config.inc.php');
ini_set('session.save_path',SESSION_PATH);
session_start(); // Start the session.
if(isset($_SESSION['last_accessed']) && isset($_SESSION['timeout']) ) 
{
	//sleep(5);
	$session_life = time() - $_SESSION['last_accessed'];
	//echo '<p> session life: ' . $session_life . '</p>';
	//echo '<p> session timeout: ' . $_SESSION['timeout'] . '</p>';
	$time_left = $_SESSION['timeout'] - $session_life;
	$ip_address = $_SERVER['REMOTE_ADDR'];
	$md5 = md5($_SERVER['HTTP_USER_AGENT']);

	$pos_user_id = $_SESSION['pos_user_id'];
	require_once(MYSQL_POS_CONNECT_FILE);
	require_once(PHP_LIBRARY);

	$dbc = pos_connection();

	// If no session value is present, or employee is not active redirect the user:
	if (!isset($_SESSION['agent']))
	{
		echo '-2';
		mysqli_close($dbc);
		exit(); 
	}
	if($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT']))
	{
		echo '-3';
		mysqli_close($dbc);
		exit(); 
	}	
	if(UserIPAddressChange($pos_user_id))
	{
		echo '-4';
		session_destroy(); 
		exit(); 
	}

	if(!UserIPAddressOK($pos_user_id))
	{
		echo '-5';
		exit(); 
	}



	
	
	
	
	//check the session timeout
	if($session_life > $_SESSION['timeout'])
	{ 
		$sql = "DELETE FROM pos_users_logged_in WHERE pos_user_id = ".$pos_user_id;
			$result = @mysqli_query ($dbc, $sql);
			if (!$result) 
			{ 
				trigger_error( '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $sql . '</p>', E_USER_WARNING);
				exit();
			}	
		mysqli_close($dbc);
		session_destroy(); 
		
		echo '-6';
		exit(); 
	}
	

	
	$sql = "UPDATE pos_users_logged_in SET session_time_remaining = '".$time_left."'
			WHERE ip_address = '$ip_address' AND http_user_agent = '$md5' AND pos_user_id = $pos_user_id";
	$result = @mysqli_query ($dbc, $sql);
	if (!$result) 
	{ 	
		echo mysqli_error($dbc); 
	}
	mysqli_close($dbc);
	echo  $_SESSION['timeout'] - $session_life;
	/*if($session_life > $_SESSION['timeout'])
	{ 
		echo 'TIMEOUT';
	}
	else
	{
		echo 'OK';
	}*/
}
else
{
	echo '-7';
}
?>