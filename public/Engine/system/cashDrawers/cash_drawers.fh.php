<?php
	/* discount form handler
	craig iannazzi 2-9-13 my bro is 40!! piece of shizz
	*/
$binder_name = 'Cash Drawers';
$access_type = 'WRITE';
require_once ('../system_functions.php');
if (isset($_POST['submit'])) 
{
	$dbc = startTransaction();
	$table_def_array = deserializeTableDef($_POST['table_def']);
	$insert = tableDefArraytoMysqlInsertArray($table_def_array);	
	// add some other stuff to the basic array
	//take out things we don't want to insert to mysql
	unset($insert['pos_cash_drawer_id']);
	//if it is new then insert, otherwise update.
	
	
	if($_POST['pos_cash_drawer_id'] == 'TBD')
	{
		//$insert['date_added'] = getCurrentTime();
		$pos_cash_drawer_id = simpleTransactionInsertSQLReturnID($dbc,'pos_cash_drawers', $insert);
		$message = urlencode('Cahs Drawer Name ' . $insert['cash_drawer_name'] . ' and ID '.$pos_cash_drawer_id.' has been added');
	}
	else
	{
		//this is an update
		$pos_cash_drawer_id = getPostOrGetID('pos_cash_drawer_id');
		$key_val_id['pos_cash_drawer_id'] = $pos_cash_drawer_id;
		$results[] = simpleTransactionUpdateSQL($dbc,'pos_cash_drawers', $key_val_id, $insert);
		$message = urlencode('Cash Drawer ID ' . $pos_cash_drawer_id . " has been updated");
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
