<?php
$binder_name = 'Customers';
$access_type = 'WRITE';
require_once ('customer_functions.php');
require_once(PHP_LIBRARY);

if (isset($_POST['submit'])) 
{
	$dbc = startTransaction();
	$table_def_array = deserializeTableDef($_POST['table_def_array']);
	$insert = tableDefArraytoMysqlInsertArray($table_def_array[0]);	
	// add some other stuff to the basic array
	$insert['pos_user_id'] = $_SESSION['pos_user_id'];
	//take out things we don't want to insert to mysql
	unset($insert['pos_customer_id']);
	//if it is new then insert, otherwise update.
	if($_POST['pos_customer_id'] == 'TBD')
	{
		$insert['date_added'] = getCurrentTime();
		$pos_customer_id = simpleTransactionInsertSQLReturnID($dbc,'pos_customers', $insert);
		$message = urlencode('Customer ID ' . $pos_customer_id . " has been added");
	}
	else
	{
		//this is an update
		$pos_customer_id = getPostOrGetID('pos_customer_id');
		$key_val_id['pos_customer_id'] = $pos_customer_id;
		$results[] = simpleTransactionUpdateSQL($dbc,'pos_customers', $key_val_id, $insert);
		$message = urlencode('Customer ID ' . $pos_customer_id . " has been updated");
	}
	simpleCommitTransaction($dbc);
	if($_POST['type'] == 'ADD_SELECT' || $_POST['type'] == 'EDIT_SELECT')
	{
		//$pos_sales_invoice_id = getPostOrGetId('pos_sales_invoice_id');
		//write the customer id:
		//$update['pos_customer_id'] = $pos_customer_id;
		//$results[] = simpleUpdateSQL('pos_sales_invoice', array('pos_sales_invoice_id' => $pos_sales_invoice_id), $update);
		header('Location: '.addgettourl($_POST['complete_location'], 'pos_customer_id='.$pos_customer_id));
	}
	else
	{
		header('Location: '.$_POST['complete_location'] .'?message=' . $message);
	}
}
else
{
	//header('Location: '.$_POST['cancel_location']);
}						
								
?>