<?php
require_once ('../sales_functions.php');

if (isset($_POST['submit'])) 
{
	$dbc = startTransaction();
	$table_def_array = deserializeTableDef($_POST['table_def']);
	$insert = tableDefArraytoMysqlInsertArray($table_def_array);	
	// add some other stuff to the basic array
	//take out things we don't want to insert to mysql
	unset($insert['pos_customer_payment_method_id']);
	//if it is new then insert, otherwise update.
	if($_POST['pos_customer_payment_method_id'] == 'TBD')
	{
		//$insert['date_added'] = getCurrentTime();
		$pos_customer_payment_method_id = simpleTransactionInsertSQLReturnID($dbc,'pos_customer_payment_methods', $insert);
		$message = urlencode('Method ' . $pos_customer_payment_method_id . " has been added");
	}
	else
	{
		//this is an update
		$pos_sales_tax_category_id = getPostOrGetID('pos_customer_payment_method_id');
		$key_val_id['pos_customer_payment_method_id'] = $pos_customer_payment_method_id;
		$results[] = simpleTransactionUpdateSQL($dbc,'pos_customer_payment_methods', $key_val_id, $insert);
		$message = urlencode('Payment Method ID ' . $pos_customer_payment_method_id . " has been updated");
	}
	simpleCommitTransaction($dbc);
	header('Location: '.$_POST['complete_location'] .'?message=' . $message);
}
else
{
	//header('Location: '.$_POST['cancel_location']);
	//cancel comes from javascript
}						
								
?>