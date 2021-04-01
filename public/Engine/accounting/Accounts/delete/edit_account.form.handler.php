<?php
/*
Craig Iannazzi 4-23-11\
*/
$binder_name = 'Accounts';
$access_type = 'WRITE';
require_once ('../accounting_functions.php');
require_once(PHP_LIBRARY);
$pos_account_id['pos_account_id'] = getPostOrGetID('pos_account_id');
//preprint($pos_account_id['pos_account_id']);
if (isset($_POST['submit'])) 
{
	$_POST['account_number'] = craigsEncryption($_POST['account_number']);
	$update_data = postedTableDefArraytoMysqlUpdateArray($_POST['table_def'], 'pos_account_id');
	//preprint($update_data);	
	$result = simpleUpdateSQL('pos_accounts', $pos_account_id, $update_data);
	$result = getAndInsertMultiSelect('pos_chart_of_accounts_id', 'pos_accounts_to_chart_of_accounts', $pos_account_id);
	$message = urlencode($update_data['company'] . " has been updated");
	header('Location: '.$_POST['complete_location'] .'&message=' . $message);		
}

	
?>
