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
	$insert = postedTableDefArraytoMysqlInsertArray($_POST['table_def']);	
	unset($insert['pos_account_balance_id']);
	$key_val_id['pos_account_balance_id'] = simpleInsertSQLReturnID('pos_account_balances', $insert);
	$message = urlencode(getAccountName($insert['pos_account_id']) . " balance has been added");
	header('Location: '.$_POST['complete_location'] .'?message=' . $message);		
}
	
?>
