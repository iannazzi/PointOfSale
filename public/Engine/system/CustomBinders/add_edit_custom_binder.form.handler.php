<?php
require_once ('../user_functions.php');
$db_table = 'pos_custom_binders';
$pos_system_id = getPostorGetValue('pos_custom_binder_id');
if (isset($_POST['submit'])) 
{
	$dbc = startTransaction();
	$table_def_array = deserializeTableDef($_POST['table_def']);
	$insert = tableDefArraytoMysqlInsertArray($table_def_array);	
	// add some other stuff to the basic array
	//take out things we don't want to insert to mysql
	unset($insert['pos_custom_binder_id']);
	//if it is new then insert, otherwise update.
	if($_POST['pos_custom_binder_id'] == 'TBD')
	{
		//$insert['date_added'] = getCurrentTime();
		$pos_custom_binder_id = simpleTransactionInsertSQLReturnID($dbc,$db_table, $insert);
		$message = urlencode('Binder ' . $insert['binder_name'] . " has been added");
	}
	else
	{
		//this is an update
		$pos_custom_binder_id = getPostOrGetID('pos_custom_binder_id');
		$key_val_id['pos_custom_binder_id'] = $pos_custom_binder_id;
		$results[] = simpleTransactionUpdateSQL($dbc,$db_table, $key_val_id, $insert);
		$message = urlencode('Binder ' . $insert['binder_name']  . " has been updated");
		
	}
	
	simpleCommitTransaction($dbc);
	$get = 'message=' . $message;
	header('Location: '. addGetToUrl($_POST['complete_location'], $get));
}
else
{
	//header('Location: '.$_POST['cancel_location']);
	//cancel comes from javascript
}						
								
?>