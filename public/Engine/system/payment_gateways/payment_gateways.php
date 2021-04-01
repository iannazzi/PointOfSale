<?php
/*
	Multiple payment gateways can be used - one for each store... or....multiple per store.
	
	probably want a check on gateway per store...
*/
$type = $_GET['type'];
$page_title = 'Payment Gateways';
$binder_name = 'Payment Gateways';
$access_type = (strtoupper($type)=='VIEW') ? 'READ' : 'WRITE';
require_once ('../system_functions.php');

$complete_location = 'list_payment_gateways.php';
$cancel_location = 'list_payment_gateways.php?message=Canceled';

if (isset($_POST['submit'])) 
{
	$dbc = startTransaction();
	$table_def_array = deserializeTableDef($_POST['table_def']);
	$insert = tableDefArraytoMysqlInsertArray($table_def_array);	
	// add some other stuff to the basic array
	//take out things we don't want to insert to mysql
	unset($insert['pos_payment_gateway_id']);
	//if it is new then insert, otherwise update.
	$insert['transaction_key'] = craigsEncryption(scrubInput($_POST['transaction_key']));
	$insert['login_id'] = craigsEncryption(scrubInput($_POST['login_id']));
	$insert['user_name'] = craigsEncryption(scrubInput($_POST['user_name']));
	$insert['password'] = craigsEncryption(scrubInput($_POST['password']));
	
	if($_POST['pos_payment_gateway_id'] == 'TBD')
	{		
		$pos_payment_gateway_id = simpleTransactionInsertSQLReturnID($dbc,'pos_payment_gateways', $insert);
		$message = urlencode('Gateway ID '.$pos_payment_gateway_id.' has been added');
	}
	else
	{
		//this is an update
		$pos_payment_gateway_id = getPostOrGetID('pos_payment_gateway_id');
		$key_val_id['pos_payment_gateway_id'] = $pos_payment_gateway_id;
		$results[] = simpleTransactionUpdateSQL($dbc,'pos_payment_gateways', $key_val_id, $insert);
		$message = urlencode('Gateway ID ' . $pos_payment_gateway_id . " has been updated");
	}
	simpleCommitTransaction($dbc);
	header('Location: '.$_POST['complete_location'] .'?message=' . $message);
}
elseif(isset($_POST['cancel'])) 
{
}
else
{
	if(strtoupper($type) == 'ADD')
	{
		$pos_payment_gateway_id = 'TBD';
		$header = '<p>Add Payment Gateway</p>';
		$page_title = 'Add Payment Gateway';
		$data_table_def = createPaymentGatewayTableDef($type, $pos_payment_gateway_id);
	}
	elseif (strtoupper($type) == 'EDIT')
	{
		$pos_payment_gateway_id = getPostOrGetID('pos_payment_gateway_id');
		$header = '<p>EDIT Payment Gateway</p>';
		$page_title = 'Edit Payment Gateway';
		$data_table_def_no_data = createPaymentGatewayTableDef($type, $pos_payment_gateway_id);	
		$db_table = 'pos_payment_gateways';
		$key_val_id['pos_payment_gateway_id'] = $pos_payment_gateway_id;
		$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def_no_data);
	}
	elseif (strtoupper($type) == 'VIEW')
	{
		$pos_payment_gateway_id = getPostOrGetID('pos_payment_gateway_id');
		$edit_location = 'payment_gateways.php?pos_payment_gateway_id='.$pos_payment_gateway_id.'&type=edit';
		//$delete_location = 'delete_discount.form.handler.php?pos_discount_id='.$pos_discount_id;
		$db_table = 'pos_payment_gateways';
		$key_val_id['pos_payment_gateway_id']  = $pos_payment_gateway_id;
		$data_table_def = createPaymentGatewayTableDef($type, $pos_payment_gateway_id);
		$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def);
	}
	else
	{
	}

	//build the html page
	if (strtoupper($type) == 'VIEW')
	{
		$html = printGetMessage('message');
		$html .= '<p>View Payment Gateway</p>';
		//$html .= confirmDelete($delete_location);
		$html .= createHTMLTableForMYSQLData($data_table_def);
		$html .= '<p><input class = "button"  type="button" name="edit"  value="Edit" onclick="open_win(\''.$edit_location.'\')"/>';
	// $html .= '<input class = "button" type="button" name="delete" value="Delete Discount" onclick="confirmDelete();"/>';
	
		$html .= '<p>';
		$html .= '<INPUT class = "button" type="button" style ="width:200px" value="Back To Payment Gateways" onclick="window.location = \''.$complete_location.'\'" />';
		$html .= '</p>';
	}
	else
	{
		$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);
		$big_html_table .= createHiddenInput('type', $type);
	
		$html = $header;
		$form_handler = 'payment_gateways.php';
		$html .= createFormForMYSQLInsert($data_table_def, $big_html_table, $form_handler, $complete_location, $cancel_location);
		$html .= '<script>document.getElementsByName("printer_name")[0].focus();</script>';
	}


	include (HEADER_FILE);
	echo $html;
	include (FOOTER_FILE);
}

function createPaymentGatewayTableDef($type, $pos_payment_gateway_id)
{
	$db_table = 'pos_gateway_providers';
	if ($pos_payment_gateway_id =='TBD')
	{
		$unique_validate = array('unique_group' => array('login_id', 'transaction_key'), 'min_length' => 1);
	}
	else
	{
		$key_val_id['pos_payment_gateway_id'] = $pos_payment_gateway_id;
		$unique_validate = array('unique_group' => array('login_id', 'transaction_key'), 'min_length' => 1, 'id' => $key_val_id);
	}
	
	return array( 
						array( 'db_field' => 'pos_payment_gateway_id',
								'type' => 'input',
								'tags' => ' readonly = "readonly" ',
								'caption' => 'Printer ID',
								'value' => $pos_payment_gateway_id,
								'validate' => 'none'
								),
						array('db_field' => 'pos_account_id',
								'caption' => 'Credit Card Processor Receivable Account',
								'type' => 'select',
								'html' => createCCAccountReceivableSelect('pos_account_id', 'false'),
								'validate' => 'false'),
						array('db_field' =>  'gateway_provider',
								'type' => 'select',
								'caption' => 'Gateway Provider',
								'html' => createEnumSelect('gateway_provider','pos_payment_gateways', 'gateway_provider', 'false',  'off')),
								
						array('db_field' =>  'model_name',
								'type' => 'input',
								'caption' => 'Version, Name, or Model'),		
						array('db_field' => 'pos_store_id',
								'caption' => 'Store',
								'type' => 'select',
								'html' => createStoreSelect('pos_store_id', $_SESSION['store_id'],  'off'),
								'value' => $_SESSION['store_id'],
								'validate' => 'false'),
						array( 'db_field' => 'login_id',
								'type' => 'input',
								'encrypted' => 1,
								'db_table' => $db_table),
						array( 'db_field' => 'transaction_key',
								'type' => 'input',
								'encrypted' => 1,
								'db_table' => $db_table),	
						array( 'db_field' => 'website_url',
								'type' => 'input',
								),	
						array( 'db_field' => 'user_name',
								'type' => 'input',
								'encrypted' => 1,
								'db_table' => $db_table),
						array( 'db_field' => 'password',
								'type' => 'input',
								'encrypted' => 1,
								'db_table' => $db_table),
						array('db_field' =>  'line',
								'type' => 'select',
								'caption' => 'Status',
								'html' => createEnumSelect('line','pos_payment_gateways', 'line', 'false',  'off')),
						
						
						
				
						array('db_field' =>  'active',
								'type' => 'checkbox',
								'caption' => 'Active',
								'value' => '1'),
						array('db_field' =>  'comments',
								'type' => 'textarea',
								'tags' => ' class="regular_textarea" ',
								'caption' => 'Comments'),
						);	
					
}

?>