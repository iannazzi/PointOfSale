<?php
	/* address form handler
	craig iannazzi 2-9-13 my bro is 40!! piece of shizz
	*/
//$binder_name = 'Discounts';
$access_type = 'WRITE';
require_once ('../sales_functions.php');
if (isset($_POST['submit'])) 
{
	$dbc = startTransaction();
	$table_def_array = deserializeTableDef($_POST['table_def']);
	$insert = tableDefArraytoMysqlInsertArray($table_def_array);	
	// add some other stuff to the basic array
	//take out things we don't want to insert to mysql
	unset($insert['pos_address_id']);
	//if it is new then insert, otherwise update.
	if($_POST['pos_address_id'] == 'TBD')
	{
		//$insert['date_added'] = getCurrentTime();
		$pos_address_id = simpleTransactionInsertSQLReturnID($dbc,'pos_addresses', $insert);
		$message = urlencode('Address Id ' . $pos_address_id . " has been added");
		
		//insert it to the customer lookup
		if($_POST['pos_customer_id'] !=0)
		{
			$add_insert['pos_address_id'] = $pos_address_id;
			$add_insert['pos_customer_id'] = $_POST['pos_customer_id'];
			 simpleTransactionInsertSQL($dbc,'pos_customer_addresses', $add_insert);
		}
		
	}
	else
	{
		//this is an update
		$pos_address_id = getPostOrGetID('pos_address_id');
		$key_val_id['pos_address_id'] = $pos_address_id;
		$results[] = simpleTransactionUpdateSQL($dbc,'pos_addresses', $key_val_id, $insert);
		$message = urlencode('Address ID ' . $pos_address_id . " has been updated");
	}
	simpleCommitTransaction($dbc);
	$complete = $_POST['ref'];
	$complete = addGetToUrl($complete,'pos_address_id=' . $pos_address_id);
	$complete = addGetToUrl($complete,'message=' . $message);
	header('Location: '.$complete);
}
else
{
	//header('Location: '.$_POST['cancel_location']);
	//cancel comes from javascript
}

	
?>
