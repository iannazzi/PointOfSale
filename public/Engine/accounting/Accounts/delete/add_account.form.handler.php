<?php
/*
Craig Iannazzi 4-23-11
*/
$binder_name = 'Accounts';
$access_type = 'WRITE';
require_once ('../accounting_functions.php');
require_once(PHP_LIBRARY);

if (isset($_POST['submit'])) 
{
	$errors= array();
	
	$account_number = craigsEncryption($_POST['account_number']);
	$sql = "SELECT * FROM pos_accounts where company='".scrubInput($_POST['company'])."' AND account_number ='$account_number'";
	if (sizeof(getSQL($sql))>0)
	{
		$errors[] = "Account not created - Matching Name & Number is already in the system";
	}
	if (sizeof($errors)>0)
	{
		$message = '';
		for($i=0;$i<sizeof($errors);$i++)
		{
			$message.= '<p>'.$errors[$i].'</p>';
		}
		header('Location: '.$_POST['complete_location'] .'?message=' . $message);	
	}
	else
	{
		$_POST['account_number'] = $account_number;
		$insert = postedTableDefArraytoMysqlInsertArray($_POST['table_def']);	
		unset($insert['pos_account_id']);
		$key_val_id['pos_account_id'] = simpleInsertSQLReturnID('pos_accounts', $insert);
		$result = getAndInsertMultiSelect('pos_chart_of_accounts_id', 'pos_accounts_to_chart_of_accounts', $key_val_id);
		$message = urlencode($insert['company'] . " has been added");
		header('Location: '.$_POST['complete_location'] .'?message=' . $message);	
	}	
}
	
?>
