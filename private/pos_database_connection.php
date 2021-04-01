<?php
function pos_connection()
{
	$db_user_pos = 'yourname';
	$db_password_pos = 'yourpassword';
	$db_host_pos = 'hostname';
	$db_name_pos = 'databasename';
	
	


	$dbc = @mysqli_connect ($db_host_pos, $db_user_pos, $db_password_pos, $db_name_pos) OR die ('Could not connect to MySQL: ' . mysqli_connect_error() );
	if (!$dbc) 
	{
		
		//trigger_error ('Could not connect to MySQL: ' . mysqli_connect_error() );
		echo 'Could not connect to MySQL: ' . mysqli_connect_error();
		exit();
	} 
	else
	{
		//set_error_handler ('pos_error_handler');
		return $dbc;
	}
}
?>