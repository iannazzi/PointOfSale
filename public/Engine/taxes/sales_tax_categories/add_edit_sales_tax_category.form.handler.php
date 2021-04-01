<?php
require_once ('../tax_functions.php');

if (isset($_POST['submit'])) 
{
	$dbc = startTransaction();
	$table_def_array = deserializeTableDef($_POST['table_def']);
	$insert = tableDefArraytoMysqlInsertArray($table_def_array);	
	// add some other stuff to the basic array
	//take out things we don't want to insert to mysql
	unset($insert['pos_sales_tax_category_id']);
	//if it is new then insert, otherwise update.
	if($_POST['pos_sales_tax_category_id'] == 'TBD')
	{
		//$insert['date_added'] = getCurrentTime();
		$pos_sales_tax_category_id = simpleTransactionInsertSQLReturnID($dbc,'pos_sales_tax_categories', $insert);
		$message = urlencode('Category' . $pos_sales_tax_category_id . " has been added");
	}
	else
	{
		//this is an update
		$pos_sales_tax_category_id = getPostOrGetID('pos_sales_tax_category_id');
		$key_val_id['pos_sales_tax_category_id'] = $pos_sales_tax_category_id;
		$results[] = simpleTransactionUpdateSQL($dbc,'pos_sales_tax_categories', $key_val_id, $insert);
		$message = urlencode('Sale Tax Category ID ' . $pos_sales_tax_category_id . " has been updated");
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