<?php

/*
	this is thecode to process login....
	
*/
require_once ('../../Config/config.inc.php');
require_once(PHP_LIBRARY);

//require_once ('../../../private/config.inc.php');
//require_once ('login_functions.inc.php');


if (isset($_POST['submitted'])) {

	// For processing the login:
	// Need the database connection:
	//require_once (MYSQL_pos01);
	echo MYSQL_POS_CONNECT_FILE;
	require_once(MYSQL_POS_CONNECT_FILE);
	$dbc = pos_connection();
		
	// Check the login:
	list ($check, $data) = check_login($dbc, $_POST['login'], $_POST['password']);
	
	if ($check ) { // OK!
		if 	($data['active'] != 0)
		{
			$pos_user_id = $data['pos_user_id'];
			$ip_address = $_SERVER['REMOTE_ADDR'];
			$browser = scrubInput($_SERVER['HTTP_USER_AGENT']);
			$md5 = md5($_SERVER['HTTP_USER_AGENT']);
			$db_check = true;
			
			
			//kill this user login if it is the system
			$sql = "DELETE FROM pos_users_logged_in WHERE pos_user_id = $pos_user_id AND http_user_agent ='$md5'AND ip_address ='$ip_address'AND browser ='$browser'";
			runSQL($sql);
			/*$result = @mysqli_query ($dbc, $sql);
			if (!$result) 
			{ 
				trigger_error( '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $sql . '</p>', E_USER_WARNING);
				exit();
			}	*/
			
			
			//now lets check
			if(!UserIPAddressOK($pos_user_id))
			{
				//this ip address is not ok for the user.. do not let in...
				$db_check = false;
			}
			else
			{
				//echo 'ip address ok';
			}
			// IP address change does not matter as we are re-logging in if(UserIPAddressChange())
			
			//next the browser....
			//if the user is required to login when the browser changes then we need to kill other login data so that machine will log them out...
			if (UserBrowserChange($pos_user_id))
			{
				$sql = "DELETE FROM pos_users_logged_in WHERE pos_user_id = $pos_user_id";
				runSQL($sql);
			}
			else
			{
				//echo "multi-broswer ok";
				$sql = "DELETE FROM pos_users_logged_in WHERE pos_user_id = $pos_user_id AND http_user_agent = '$md5'";
				runSQL($sql);
			}
			
			if ($db_check)
			{
				//register the login
				$sql = "INSERT INTO pos_users_logged_in (pos_user_id, http_user_agent, ip_address, browser) VALUES ('$pos_user_id', '$md5', '$ip_address', '$browser')";
				runSQl($sql);
				/*$result = @mysqli_query ($dbc, $sql);
				if (!$result) 
				{ 
					trigger_error( '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $sql . '</p>', E_USER_WARNING);
					exit();
				}	*/
				// Set the cookies:
				//setcookie ('default_store_id', $data['default_store_id'], time()+(7 * 24 * 60 * 60), '/', '', 0, 0);
				// run a sql to set the default company name
				$default_store_id = $data['default_store_id'];
				$default_store_sql = "SELECT * FROM pos_stores WHERE pos_store_id ='$default_store_id'";
				$default_selected_store_r = @mysqli_query ($dbc, $default_store_sql);
				$default_selected_store = mysqli_fetch_array ($default_selected_store_r, MYSQLI_ASSOC);
				//setcookie ('company', $default_selected_store['company'], time()+(7 * 24 * 60 * 60), '/', '', 0, 0);

				ini_set('session.save_path',SESSION_PATH);
				//ini_set('session.gc_maxlifetime', 86400);
				session_start();
				$_SESSION['current_page_accessed'] = 'INDEX';
				$_SESSION['last_page_accessed'] = 'LOGIN PAGE';

				$_SESSION['pos_user_id'] = $data['pos_user_id'];
				$_SESSION['pos_employee_id'] = $data['pos_employee_id'];
				$_SESSION['first_name'] = $data['first_name'];
				$_SESSION['last_name'] = $data['last_name'];
				$_SESSION['level'] = $data['level'];
				$_SESSION['active'] = $data['active'];
				$_SESSION['store_id'] = $data['default_store_id'];
				//set the session time
				$_SESSION['last_accessed'] = time();
				$_SESSION['timeout'] = $data['timeout_minutes']*60;
				// Store the HTTP_USER_AGENT:
				$_SESSION['agent'] = md5($_SERVER['HTTP_USER_AGENT']);
				//$_SESSION['write_access'] = $data['database_access'];	
				//$_SESSION['page_rights'] = $data['rights'];	

				$last_room = $data['last_room'];
				// Redirect:
				if($last_room == '' || strtoupper($last_room) ==strtoupper('home'))
				{
					$url = POS_URL .'/index.php';
				}
				else
				{
					$url = POS_URL .'/index.php?r='.$last_room;
				}
				header("Location: $url");
				exit(); // Quit the script.
			}
			else
			{
				$errors[] = 'Login errors';
				//let the script continue to the login page
			}
				
		} 
		elseif ($data['active'] != 1) 
		{ // This user is not active
			$errors[] = $data['first_name'] . " " . $data['last_name'] . " is not active!";
		}
	}
	else { // Unsuccessful!

		// Assign $data to $errors for error reporting
		// in the login_page.inc.php file.
		$errors = $data;

	}
		
	mysqli_close($dbc); // Close the database connection.

} 

include ('login_page.inc.php');
?>
