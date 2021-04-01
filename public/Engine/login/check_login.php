<?php 
/*

		 check_login.php
		 
		a user can have multiple browsers running on multiple computers
		a mobile computer might change ip adressess often
		
		In general restric login to boot when ip address or browser changes
		
		 re-login if the ip address changes
		 relogin if the broswer changes
		 
		 
		 
		 
*/
//echo 'hello from login-check_login.php';
//exit();

ini_set('session.save_path',SESSION_PATH);
//ini_set('session.gc_maxlifetime', 86400);
session_start(); // Start the session.
require_once(MYSQL_POS_CONNECT_FILE);
require_once(PHP_LIBRARY);
if(!isset($_SESSION['pos_user_id']))
{
	$url = POS_ENGINE_URL . '/login/login.php';
	header("Location: $url");
	exit(); 
}
$_SESSION['last_page_accessed'] = $_SESSION['current_page_accessed'];
$_SESSION['current_page_accessed'] = getPageUrl();
$pos_user_id = $_SESSION['pos_user_id'];
$ip_address = $_SERVER['REMOTE_ADDR'];
$browser = scrubInput($_SERVER['HTTP_USER_AGENT']);
//$ip_address = '123.43.23.1';
$md5 = md5($_SERVER['HTTP_USER_AGENT']);
$dbc = pos_connection();
//log the access data
$user_log_sql = "INSERT INTO pos_user_log (pos_user_id, time, url, ip_address, browser) VALUES ($pos_user_id, NOW(), '".scrubInput(getPageURL())."','$ip_address', '$browser')";
$result = runSQL($user_log_sql);

$sql = "SELECT max_connections, ip_address_restrictions, relogin_on_ip_address_change, relogin_on_browser_change FROM pos_users WHERE pos_user_id = $pos_user_id";
$login_limitations = getSQL($sql);
if($login_limitations[0]['relogin_on_ip_address_change'] == 1)
{
	$sql = "SELECT pos_user_id, ip_address FROM pos_users_logged_in WHERE ip_address = '$ip_address' AND http_user_agent = '$md5' AND pos_user_id = $pos_user_id";
}
else
{
	$sql = "SELECT pos_user_id,ip_address FROM pos_users_logged_in WHERE  http_user_agent = '$md5' AND pos_user_id = $pos_user_id";
}


$logged_in_data = getSQL($sql);
if( sizeof($logged_in_data) == 1)
{
	//ip_address = '$ip_address' AND http_user_agent = '$md5' AND
	// the user_id, computer/browser and ip address is a match - good to go
	$db_check = true;
	if($logged_in_data[0]['ip_address'] != $ip_address)
	{
	$sql = "UPDATE pos_users_logged_in SET ip_address = '$ip_address' WHERE pos_user_id = $pos_user_id";
	runSQL($sql);
	}
	
}
else
{
	$db_check = false;
}
$login_ok = true;
if($login_limitations[0]['max_connections'] < 1)
{
	//too many connections for the user - do not use this code
}
if($login_limitations[0]['ip_address_restrictions'] != '')
{
	$ip_address_restrictions = explode(',', $login_limitations[0]['ip_address_restrictions']);

	if (!in_array($ip_address, $ip_address_restrictions))
	{
		$login_ok = false;
	}
}
if($login_limitations[0]['relogin_on_browser_change'] == 1)
{
	//not using this
}

mysqli_close($dbc);



// If no session value is present, or employee is not active redirect the user:
if ( (!isset($_SESSION['agent']))  || ($_SESSION['agent'] != md5($_SERVER['HTTP_USER_AGENT'])) || ($db_check==false) || ($login_ok==false)) 
{
	//require_once ('login_functions.inc.php');
	//echo 'boot';
	$url = POS_ENGINE_URL . '/login/login.php';
	header("Location: $url");
	exit(); 
}
// check the timeout next
if(isset($_SESSION['last_accessed']) && isset($_SESSION['timeout']) ) 
{
	$session_life = time() - $_SESSION['last_accessed'];
	//echo '<p> session life: ' . $session_life . '</p>';
	//echo '<p> session timeout: ' . $_SESSION['timeout'] . '</p>'; 
	if($session_life > $_SESSION['timeout'])
	{ 
		$dbc = pos_connection();
		$sql = "DELETE FROM pos_users_logged_in WHERE pos_user_id = ".$pos_user_id;
			$result = @mysqli_query ($dbc, $sql);
			if (!$result) 
			{ 
				trigger_error( '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $sql . '</p>', E_USER_WARNING);
				exit();
			}	
		mysqli_close($dbc);
		session_destroy(); 
		
		$url = POS_ENGINE_URL . '/login/login.php';
		header("Location: $url");
		exit(); 
	}
}
//update time
$_SESSION['last_accessed'] = time();
	$isHTTPS = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on");
	$port = (isset($_SERVER["SERVER_PORT"]) && ((!$isHTTPS && $_SERVER["SERVER_PORT"] != "80") || ($isHTTPS && $_SERVER["SERVER_PORT"] != "443")));
	$port = ($port) ? ':'.$_SERVER["SERVER_PORT"] : '';
	$url = ($isHTTPS ? 'https://' : 'http://').$_SERVER["SERVER_NAME"].$port.$_SERVER["REQUEST_URI"];
//update the login status
//$dbc = pos_connection();
$sql = "UPDATE pos_users_logged_in SET last_accessed = NOW(), current_page = '" . scrubInput($url) ."' 
			WHERE ip_address = '$ip_address' AND http_user_agent = '$md5' AND pos_user_id = $pos_user_id";
runSQL($sql);

//check the function access
//check the page access


// Check the page level....

/*$user_level = $_SESSION['level'];
if ($user_level >= $page_level)
{
	// we are ok
}
else
{
	// boot off the page....
	// Need the functions to create an absolute URL:
	//require_once ('../../../Config/config.inc.php');
	//require_once ('login_functions.inc.php');
	$url =  POS_ENGINE_URL . '/includes/page_level.php';
	header("Location: $url");
	exit(); // Quit the script.
}*/

//check page access
//courtesy check to see if the binder name is correct
if(isset($binder_name))
{
	//$_SESSION[$binder_name] = $_SESSION['room'];
	checkValidBinderName($binder_name);
	if (checkIfUserIsAdmin($pos_user_id))
	{
	}
	else
	{
		if (checkUserBinderAccess($pos_user_id, getBinderIDFRomBinderName($binder_name)) == false)
		{
			echo "User Access Denied - Contact System Admin For Access";
			exit();
		}
		else
		{
		}
	}
}
else
{
	//index does not have a binder, so one might not be avialable...
	//echo ' binder_name not defined';
}
unset($browser);
unset($result);
unset($dbc);
unset($db_check);
unset($sql);
unset($pos_user_id);
unset($ip_address);
unset($md5);
unset($url);	
unset($port);
unset($isHTTPS);
unset($max_connections);
unset($login_limitations);	
?>
