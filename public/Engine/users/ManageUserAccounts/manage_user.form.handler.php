<?php
/*
Craig Iannazzi 7-02-12
*/
$binder_name = 'System User Accounts';
require_once ('../user_functions.php');
require_once(PHP_LIBRARY);
$db_table = 'pos_users';
$primary_key = 'pos_user_id';
$message_key = 'last_name';

if (isset($_POST['submit'])) 
{
	
	
	//this is either an add or edit
	$data = postedTableDefArraytoMysqlInsertArray($_POST['table_def']);
	//deal with the special user rights and levels
	$rights = array();
	foreach($_POST as $key => $value)
	{
		if(strpos($key,'rights_')!==false)
		{
			$right = str_replace('rights_','',$key);
			$level = ($_POST['level_'.$right] == '') ? 1 : $_POST['level_'.$right];
			$rights[] = $right . ':' . $level;	
		}
	}
	$data['rights'] = implode(',',$rights);
	if ($_POST['type'] == 'New')
	{
		unset($data[$primary_key]);
		$pos_user_id = simpleInsertSQLReturnID($db_table, $data);
		$message = 'message=' .urlencode($data[$message_key] . " has been added");
		$complete_location = 'manage_user.php?type=password&pos_user_id='.$pos_user_id;
	}
	elseif ($_POST['type'] == 'Edit')
	{
		$key_val_id[$primary_key] = getPostOrGetID($primary_key);
		$result = simpleUpdateSQL($db_table, $key_val_id, $data);
		$message = 'message=' .urlencode($data[$message_key] . " has been updated");
		$complete_location = 'manage_user.php?type=view&pos_user_id='.$data['pos_user_id'];
	}
	//password:
	else if ($_POST['type'] == 'password')
	{
		if($_POST['password1'] !='')
		{
			$pos_user_id = getPostOrGetID($primary_key);
			$password1 = scrubinput($_POST['password1']);
			$password2 = scrubinput($_POST['password2']);
			$check = checkPasswordIsValid($password1, $password2);
			if($check == 'Password Updated')
			{
				$result = updatePassword($pos_user_id, $password1);
				$message = 'message='. urlencode($check);
				$complete_location = 'manage_user.php?type=view&pos_user_id='.$pos_user_id;
			}
			else
			{
				$message = 'message=' . urlencode($check);
				//header('Location: '.$_POST['complete_location'] .'?' . $message);
				//exit();
				$complete_location = 'manage_user.php?type=view&pos_user_id='.$pos_user_id;
			}
		}
	}
	
	header('Location: '.addGetToUrl($complete_location, $message));		
}
	
?>
