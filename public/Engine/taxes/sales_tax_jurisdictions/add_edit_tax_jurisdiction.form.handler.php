<?php
require_once ('../tax_functions.php');

$db_table = 'pos_tax_jurisdictions';
if (isset($_POST['submit'])) 
{
	$dbc = startTransaction();
	$table_def_array = deserializeTableDef($_POST['table_def']);
	$insert = tableDefArraytoMysqlInsertArray($table_def_array);	
	// add some other stuff to the basic array
	//take out things we don't want to insert to mysql
	unset($insert['pos_tax_jurisdiction_id']);
	//if it is new then insert, otherwise update.
	if($_POST['pos_tax_jurisdiction_id'] == 'TBD')
	{
		//$insert['date_added'] = getCurrentTime();
		$pos_tax_jurisdiction_id = simpleTransactionInsertSQLReturnID($dbc,$db_table, $insert);
		$message = urlencode('Category' . $pos_tax_jurisdiction_id . " has been added");
	}
	else
	{
		//this is an update
		$pos_tax_jurisdiction_id = getPostOrGetID('pos_tax_jurisdiction_id');
		$key_val_id['pos_tax_jurisdiction_id'] = $pos_tax_jurisdiction_id;
		$results[] = simpleTransactionUpdateSQL($dbc,$db_table, $key_val_id, $insert);
		$message = urlencode('Sale Tax Jurisdiction ID ' . $pos_tax_jurisdiction_id . " has been updated");
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