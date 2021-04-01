<?php
	/* address form handler
	craig iannazzi 2-9-13 my bro is 40!! piece of shizz
	*/
//$binder_name = 'Discounts';
$access_type = 'WRITE';
require_once ('../office_functions.php');
if (isset($_POST['submit'])) 
{
	$dbc = startTransaction();
	$table_def_array = deserializeTableDef($_POST['table_def']);
	$insert = tableDefArraytoMysqlInsertArray($table_def_array);	
	// add some other stuff to the basic array
	//take out things we don't want to insert to mysql
	unset($insert['pos_email_address_id']);
	//if it is new then insert, otherwise update.
	if($_POST['pos_email_address_id'] == 'TBD')
	{
		//$insert['date_added'] = getCurrentTime();
		$pos_email_address_id = simpleTransactionInsertSQLReturnID($dbc,'pos_email_addresses', $insert);
		$message = urlencode('Address Id ' . $pos_email_address_id . " has been added");
		
		//insert it to the customer lookup
		if($_POST['pos_customer_id'] !=0)
		{
			$add_insert['pos_email_address_id'] = $pos_email_address_id;
			$add_insert['pos_customer_id'] = $_POST['pos_customer_id'];
			 simpleTransactionInsertSQL($dbc,'pos_customer_emails', $add_insert);
		}
		
	}
	else
	{
		//this is an update
		$pos_email_address_id = getPostOrGetID('pos_email_address_id');
		$key_val_id['pos_email_address_id'] = $pos_email_address_id;
		$results[] = simpleTransactionUpdateSQL($dbc,'pos_email_addresses', $key_val_id, $insert);
		$message = urlencode('Address ID ' . $pos_email_address_id . " has been updated");
	}
	simpleCommitTransaction($dbc);
	header('Location: '.addGetToUrl($_POST['ref'],'message=' . $message));
}
else
{
	//header('Location: '.$_POST['cancel_location']);
	//cancel comes from javascript
}

	
?>
