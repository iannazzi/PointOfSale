<?php
	/* discount form handler
	craig iannazzi 2-9-13 my bro is 40!! piece of shizz
	*/
$binder_name = 'Printers';
$access_type = 'WRITE';
require_once ('../system_functions.php');
if (isset($_POST['submit'])) 
{
	$dbc = startTransaction();
	$table_def_array = deserializeTableDef($_POST['table_def']);
	$insert = tableDefArraytoMysqlInsertArray($table_def_array);	
	// add some other stuff to the basic array
	//take out things we don't want to insert to mysql
	unset($insert['name']);
	//if it is new then insert, otherwise update.
	
	
	if($_POST['name'] == 'TBD')
	{
		//$insert['date_added'] = getCurrentTime();
		$pos_printer_id = simpleTransactionInsertSQLReturnID($dbc,'pos_printers', $insert);
		$message = urlencode('Printer Name ' . $insert['printer_name'] . ' and ID '.$pos_printer_id.' has been added');
	}
	else
	{
		//this is an update
		$name = getPostOrGetID('name');
		$key_val_id['name'] = $name;
		$results[] = simpleTransactionUpdateSQL($dbc,'pos_settings', $key_val_id, $insert);
		$message = urlencode('Setting' . $name . " has been updated");
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
