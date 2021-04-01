<?php
$binder_name = 'Product Attributes';
$access_type = 'WRITE';
require_once ('../product_functions.php');
if (isset($_POST['submit'])) 
{
	$dbc = startTransaction();
	$table_def_array = deserializeTableDef($_POST['table_def']);
	$insert = tableDefArraytoMysqlInsertArray($table_def_array);	
	// add some other stuff to the basic array
	//take out things we don't want to insert to mysql
	unset($insert['pos_product_attribute_id']);
	//if it is new then insert, otherwise update.
	if($_POST['pos_product_attribute_id'] == 'TBD')
	{
		//$insert['date_added'] = getCurrentTime();
		$pos_product_attribute_id = simpleTransactionInsertSQLReturnID($dbc,'pos_product_attributes', $insert);
		$message = urlencode('Attribute' . $pos_product_attribute_id . " has been added");
	}
	else
	{
		//this is an update
		$pos_product_attribute_id = getPostOrGetID('pos_product_attribute_id');
		$key_val_id['pos_product_attribute_id'] = $pos_product_attribute_id;
		$results[] = simpleTransactionUpdateSQL($dbc,'pos_product_attributes', $key_val_id, $insert);
		$message = urlencode('Attribute ID ' . $pos_product_attribute_id . " has been updated");
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