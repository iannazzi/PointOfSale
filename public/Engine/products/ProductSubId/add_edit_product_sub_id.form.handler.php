<?php
$page_title = 'View SubId';
$binder_name = 'Products';
$access_type = 'WRITE';
require_once ('../product_functions.php');
if (isset($_POST['submit'])) 
{
	$dbc = startTransaction();
	$table_def_array = deserializeTableDef($_POST['table_def']);
	$insert = tableDefArraytoMysqlInsertArray($table_def_array);	
	// add some other stuff to the basic array
	//take out things we don't want to insert to mysql
	unset($insert['pos_product_sub_id']);
	//if it is new then insert, otherwise update.
	if($_POST['pos_product_sub_id'] == 'TBD')
	{
		$insert['pos_product_id'] = $_POST['pos_product_id'];
		$pos_product_sub_id = simpleTransactionInsertSQLReturnID($dbc,'pos_products_sub_id', $insert);
		$message = urlencode('Sub Id' . $pos_product_sub_id . " has been added");
	}
	else
	{
		//this is an update
		$pos_product_sub_id = getPostOrGetID('pos_product_sub_id');
		$key_val_id['pos_product_sub_id'] = $pos_product_sub_id;
		$results[] = simpleTransactionUpdateSQL($dbc,'pos_products_sub_id', $key_val_id, $insert);
		$message = urlencode('Product Sub Id' . $pos_product_sub_id . " has been updated");
	}
	simpleCommitTransaction($dbc);
	header('Location: '.$_POST['complete_location'] .'&message=' . $message);
}
else
{
	//header('Location: '.$_POST['cancel_location']);
	//cancel comes from javascript
}						
								
?>