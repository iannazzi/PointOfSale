<?php
$page_title = 'View Location';
$binder_name = 'Locations';
$access_type = 'WRITE';
require_once ('../inventory_functions.php');
if (isset($_POST['submit'])) 
{
	$dbc = startTransaction();
	$table_def_array = deserializeTableDef($_POST['table_def']);
	$insert = tableDefArraytoMysqlInsertArray($table_def_array);	
	// add some other stuff to the basic array
	//take out things we don't want to insert to mysql
	unset($insert['pos_location_group_id']);
	//if it is new then insert, otherwise update.
	if($_POST['pos_location_group_id'] == 'TBD')
	{
		//$insert['date_added'] = getCurrentTime();
		$pos_location_group_id = simpleTransactionInsertSQLReturnID($dbc,'pos_location_groups', $insert);
		$message = urlencode('Location' . $pos_location_group_id . " has been added");
	}
	else
	{
		//this is an update
		$pos_location_group_id = getPostOrGetID('pos_location_group_id');
		$key_val_id['pos_location_group_id'] = $pos_location_group_id;
		$results[] = simpleTransactionUpdateSQL($dbc,'pos_location_groups', $key_val_id, $insert);
		$message = urlencode('Location ID ' . $pos_location_group_id . " has been updated");
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