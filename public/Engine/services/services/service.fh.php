<?php
	/* discount form handler
	craig iannazzi 2-9-13 my bro is 40!! piece of shizz
	*/
$binder_name = 'Services';
$access_type = 'WRITE';
require_once ('../services_functions.php');
if (isset($_POST['submit'])) 
{
	$dbc = startTransaction();
	$table_def_array = deserializeTableDef($_POST['table_def']);
	$insert = tableDefArraytoMysqlInsertArray($table_def_array);	
	// add some other stuff to the basic array
	//take out things we don't want to insert to mysql
	unset($insert['pos_service_id']);
	//if it is new then insert, otherwise update.
	//create the barcode:
	
	
	if($_POST['pos_service_id'] == 'TBD')
	{
		//$insert['date_added'] = getCurrentTime();
		$pos_service_id = simpleTransactionInsertSQLReturnID($dbc,'pos_services', $insert);
		$update['barcode'] = strtoupper('S-' . $pos_service_id);
		$key_val_id['pos_service_id'] = $pos_service_id;
		$results[] = simpleTransactionUpdateSQL($dbc,'pos_services', $key_val_id, $update);
		$message = urlencode('Service Id ' . $pos_service_id . " has been added");
	}
	else
	{
		//this is an update
		$pos_service_id = getPostOrGetID('pos_service_id');
		$insert['barcode'] = strtoupper('S-' . $pos_service_id);
		$key_val_id['pos_service_id'] = $pos_service_id;
		$results[] = simpleTransactionUpdateSQL($dbc,'pos_services', $key_val_id, $insert);
		$message = urlencode('Service ID ' . $pos_service_id . " has been updated");
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
