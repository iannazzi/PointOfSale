<?php
	/* discount form handler
	craig iannazzi 2-9-13 my bro is 40!! piece of shizz
	*/
$binder_name = 'Discounts';
$access_type = 'WRITE';
require_once ('../sales_functions.php');
if (isset($_POST['submit'])) 
{
	$dbc = startTransaction();
	$table_def_array = deserializeTableDef($_POST['table_def']);
	$insert = tableDefArraytoMysqlInsertArray($table_def_array);	
	// add some other stuff to the basic array
	//take out things we don't want to insert to mysql
	unset($insert['pos_discount_id']);
	//if it is new then insert, otherwise update.
	if($_POST['pos_discount_id'] == 'TBD')
	{
		//$insert['date_added'] = getCurrentTime();
		$pos_discount_id = simpleTransactionInsertSQLReturnID($dbc,'pos_discounts', $insert);
		$message = urlencode('Discount Id ' . $pos_discount_id . " has been added");
	}
	else
	{
		//this is an update
		$pos_discount_id = getPostOrGetID('pos_discount_id');
		$key_val_id['pos_discount_id'] = $pos_discount_id;
		$results[] = simpleTransactionUpdateSQL($dbc,'pos_discounts', $key_val_id, $insert);
		$message = urlencode('Discount ID ' . $pos_discount_id . " has been updated");
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
