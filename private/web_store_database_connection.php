<?php
function webStoreConnection()
{
	// This file contains the database access information. 
	// This file also establishes a connection to MySQL 
	// and selects the database.
	set_error_handler ('db_error_handler2');
	// Set the database access information as constants:
	/*DEFINE ('DB_USER_PCART', 'embrasse');
	DEFINE ('DB_PASSWORD_PCART', 'Doc#1264-6010');
	DEFINE ('DB_HOST_PCART', 'localhost');
	DEFINE ('DB_NAME_PCART', 'embrasse_pinnaclecart_3_7_8');*/
	
	$db_user_pos = 'yourname';
	$db_password_pos = 'yourpasseword';
	$db_host_pos = 'yourhose';
	$db_name_pos = 'dbname';
	// Make the connection:
	$dbc = @mysqli_connect ($db_host_pos, $db_user_pos, $db_password_pos, $db_name_pos) OR die ('Could not connect to MySQL: ' . mysqli_connect_error() );

	if (!$dbc) 
		{
		trigger_error ('Could not connect to MySQL: ' . mysqli_connect_error() );
		} else
		{
			set_error_handler ('pos_error_handler');
			return $dbc;
		}
}
function db_error_handler2()
{
	echo 'Web Database connection error ';
	
}
?>