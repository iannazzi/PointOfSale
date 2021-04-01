<?php
/*
Craig Iannazzi 4-23-11\
*/
$binder_name = 'Accounts';
$access_type = 'WRITE';
require_once ('../accounting_functions.php');
require_once(PHP_LIBRARY);
$pos_account_balance_id['pos_account_balance_id'] = getPostOrGetID('pos_account_balance_id');
if (isset($_POST['submit'])) 
{
	$update_data = postedTableDefArraytoMysqlUpdateArray($_POST['table_def'], 'pos_account_balance_id');	
	$result = simpleUpdateSQL('pos_account_balances', $pos_account_balance_id, $update_data);
	$message = urlencode(getAccountName($update_data['pos_account_id']) . " balance has been updated");
	header('Location: '.$_POST['complete_location'] .'&message=' . $message);		
}

	
?>
