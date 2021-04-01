<?php
/*
	this is the code I am writing while sick as a dog shit 
	
*/
$binder_name = 'Sales Invoices';
$access_type = 'WRITE';
$page_title = 'Sales';
require_once ('../sales_functions.php');
$type = getPostOrGetValue('type');
if (isset($_POST['submit'])) 
{
	$dbc = startTransaction();
	$table_def_array = deserializeTableDef($_POST['table_def']);
	$insert = tableDefArraytoMysqlInsertArray($table_def_array);	
	// add some other stuff to the basic array
	//take out things we don't want to insert to mysql
	unset($insert['pos_sales_invoice_id']);
	
	//$insert['invoice_date'] = $_POST['invoice_date'] . ' ' . $_POST['invoice_time'];
	//if it is new then insert, otherwise update.
	//recombine the date_time
	
	
	if($_POST['pos_sales_invoice_id'] == 'TBD')
	{
		//$insert['date_added'] = getCurrentTime();
		$insert['pos_user_id'] = $_SESSION['pos_user_id'];
		//we will also need the terminal id here....
		$insert['pos_terminal_id'] = getTerminalId(getTerminalCookie());
		$insert['invoice_status'] = 'INIT';
		$insert['payment_status'] = 'UNPAID';
		$pos_sales_invoice_id = simpleTransactionInsertSQLReturnID($dbc,'pos_sales_invoice', $insert);
		//$message = urlencode('promotion Id ' . $pos_promotion_id . " has been added");
		simpleCommitTransaction($dbc);
		$complete_location = 'select_customer.php?complete_location=' . urlencode(POS_ENGINE_URL . '/sales/retailInvoice/retail_sales_invoice.php?type=edit&pos_sales_invoice_id='. $pos_sales_invoice_id);
		
		//header('Location: '.$_POST['complete_location'] .'?pos_sales_invoice_id=' . $pos_sales_invoice_id);
		header('Location: ' . $complete_location);

		exit();
	}
	else
	{
		//this is an update
		$pos_sales_invoice_id = getPostOrGetID('pos_sales_invoice_id');
		$key_val_id['pos_sales_invoice_id'] = $pos_sales_invoice_id;
		$results[] = simpleTransactionUpdateSQL($dbc,'pos_sales_invoice', $key_val_id, $insert);
		$message = 'message=' .urlencode('Invoice ID ' . $pos_sales_invoice_id . " has been updated");
		simpleCommitTransaction($dbc);
		header('Location: '.addgetToURL($_POST['complete_location'] , $message));
		exit();
	}

}
else
{
	//header('Location: '.$_POST['cancel_location']);
	//cancel comes from javascript
}


?>