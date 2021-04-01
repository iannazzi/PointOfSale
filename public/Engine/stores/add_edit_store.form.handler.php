<?php
require_once ('store_functions.php');

$db_table = 'pos_stores';
if (isset($_POST['submit'])) 
{
	$dbc = startTransaction();
	$table_def_array = deserializeTableDef($_POST['table_def']);
	if(!isset($_POST['pos_tax_jurisdiction_id'])) $_POST['pos_tax_jurisdiction_id'] = 0;
	$insert = tableDefArraytoMysqlInsertArray($table_def_array);	
	// add some other stuff to the basic array
	//take out things we don't want to insert to mysql
	unset($insert['pos_store_id']);
	//if it is new then insert, otherwise update.
	if($_POST['pos_store_id'] == 'TBD')
	{
		//$insert['date_added'] = getCurrentTime();
		$pos_store_id = simpleTransactionInsertSQLReturnID($dbc,$db_table, $insert);
		$message = urlencode('Store ' . $pos_store_id . " has been added");
	}
	else
	{
		//this is an update
		$pos_store_id = getPostOrGetID('pos_store_id');
		$key_val_id['pos_store_id'] = $pos_store_id;
		$results[] = simpleTransactionUpdateSQL($dbc,$db_table, $key_val_id, $insert);
		$message = urlencode('Store ID ' . $pos_store_id . " has been updated");
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