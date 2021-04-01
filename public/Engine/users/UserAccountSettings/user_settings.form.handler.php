<?php
/*
Craig Iannazzi 7-02-12
*/
$binder_name = 'User Account Settings';
require_once ('../user_functions.php');
require_once(PHP_LIBRARY);
$db_table = 'pos_users';
$primary_key = 'pos_user_id';
$message_key = 'last_name';
if (isset($_POST['submit'])) 
{
	
	//this is either an add or edit
	if ($_POST['type'] == 'New')
	{
		$data = postedTableDefArraytoMysqlInsertArray($_POST['table_def']);
		unset($data[$primary_key]);
		$key_val_id[$primary_key] = simpleInsertSQLReturnID($db_table, $data);
		$message = urlencode($data[$message_key] . " has been added");
	}
	elseif ($_POST['type'] == 'Edit')
	{
		$data = postedTableDefArraytoMysqlInsertArray($_POST['table_def']);
		unset($data[$primary_key]);
		$key_val_id[$primary_key] = getPostOrGetID($primary_key);
		$result = simpleUpdateSQL($db_table, $key_val_id, $data);
		$message = urlencode(getUserFullName($key_val_id[$primary_key])  . " has been updated");
	}
	//password:
	elseif ($_POST['type'] == 'password')
	{
		if($_POST['password1'] !='')
		{
			$key_val_id[$primary_key] = getPostOrGetID($primary_key);
			$password_original = scrubinput($_POST['password_original']);
			$password1 = scrubinput($_POST['password1']);
			$password2 = scrubinput($_POST['password2']);
			if(checkPasswordIsValid($password1, $password2))
			{
				$result = updatePassword($key_val_id[$primary_key], $password1);
				$message = 'Password Update Complete';
				
			}
			else
			{
				$message = 'Password Update Failed';
				header('Location: '.$_POST['complete_location'] .'&message=' . $message);
				exit();
			}
		}
		else
		{
			$message = 'Password Update Incomplete';
		}
	}
	header('Location: '.$_POST['complete_location'] .'&message=' . $message);		
}
	
?>
