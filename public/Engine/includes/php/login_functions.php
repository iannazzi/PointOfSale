<?php
function check_login($dbc, $login = '', $pass = '') 
{

	$errors = array(); // Initialize error array.
	
	// Validate the email address:
	if (empty($login)) {
		$errors[] = 'You forgot to enter your login ID.';
	} else {
		$e = scrubLoginInput($dbc,$login);
	}
	
	// Validate the password:
	if (empty($pass)) {
		$errors[] = 'You forgot to enter your password.';
	} else {
		$p = scrubLoginInput($dbc,$pass);
	}

	if (empty($errors)) { // If everything's OK.

		// Retrieve the user_id and first_name for that email/password combination:
		$q = "SELECT * FROM pos_users WHERE login='$e' AND password=SHA1('$p')";		
		$r = @mysqli_query ($dbc, $q); // Run the query.
		
		// Check the result:
		if (mysqli_num_rows($r) == 1) {
		
			// Fetch the record:
			$row = mysqli_fetch_array ($r, MYSQLI_ASSOC);
			
			// Return true and the record:
			return array(true, $row);
			
		} else { // Not a match!
			$errors[] = 'The user name and password entered do not match those on file.';
			$errors[] = "The user name provided was " . $e;
			
		}
		
	} // End of empty($errors) IF.
	
	// Return false and the errors:
	return array(false, $errors);

}
function scrubLoginInput($dbc, $input)
{
	//echo '<p>' . $input . '</p>';
	if (get_magic_quotes_gpc()) 
	{
		$input = stripslashes($input);
	}
	// Quote if not integer
	if (!is_numeric($input)) 
	{
		$input =  mysqli_real_escape_string($dbc, trim($input));
	}
	//this is added in case there are a shit ton of backslashes added - doesn't seem correct?
	//$input = str_replace("\\","", $input);
	//echo '<p>' . $input . '</p>';
	return $input;
	
}
function getUserLoginRestrictions($pos_user_id)
{
	$sql = "SELECT max_connections, ip_address_restrictions, relogin_on_ip_address_change, relogin_on_browser_change FROM pos_users WHERE pos_user_id = $pos_user_id";
	return getSQL($sql);
}
function UserIPAddressChange($pos_user_id )
{
	$md5 = md5($_SERVER['HTTP_USER_AGENT']);
	//$pos_user_id = $_SESSION['pos_user_id'];
	$ip_address = $_SERVER['REMOTE_ADDR'];
	$login_limitations = getUserLoginRestrictions($pos_user_id);
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
		$address_change = false;
		if($logged_in_data[0]['ip_address'] != $ip_address)
		{
			$sql = "UPDATE pos_users_logged_in SET ip_address = '$ip_address' WHERE pos_user_id = $pos_user_id";
			runSQL($sql);
		}
		
	}
	else
	{
		$address_change = true;
	}
	return $address_change;
}
function UserIPAddressOK($pos_user_id)
{
	$ip_address_ok = true;
	$ip_address = $_SERVER['REMOTE_ADDR'];
	$login_limitations = getUserLoginRestrictions($pos_user_id);
	if($login_limitations[0]['ip_address_restrictions'] != '')
	{
		$ip_address_restrictions = explode(',', $login_limitations[0]['ip_address_restrictions']);
	
		if (!in_array($ip_address, $ip_address_restrictions))
		{
			$ip_address_ok = false;
		}
	}
	return $ip_address_ok;
	
}
function UserMaxConnectionsOK($pos_user_id)
{
	/*if($login_limitations[0]['max_connections'] < 1)
	{
		//too many connections for the user - do not use this code
	}*/
	return true;
}
function UserBrowserChange($pos_user_id)
{
	//if the user is required to login when the browser is change
	
	$login_limitations = getUserLoginRestrictions($pos_user_id);
	if($login_limitations[0]['relogin_on_browser_change'] == 1)
	{
		return true;
	}
	else
	{
		return false;
	}
}

?>