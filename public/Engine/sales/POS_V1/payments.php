<?
/*
	this is where we can view payment details and modify things if needed


*/
$binder_name = 'POS';
$access_type = 'WRITE';
$page_title = 'Customer Payment';
require_once('retail_sales_invoice_functions.php');
$payments_javascript = 'payments.2015.06.11.js';

$pos_customer_payment_id = getPostOrGetID('pos_customer_payment_id');
//check if it is in the system..
if (!checkForValidIDinPOS($pos_customer_payment_id, 'pos_customer_payments', 'pos_customer_payment_id'))
{
	include (HEADER_FILE);
		echo 'attempting accesses of missing payment';
		include (FOOTER_FILE);
	exit();
}


//technically a payment can span across many invoices... but essentially we do not work that way...
$pos_sales_invoice_id_array = getSQL("Select pos_sales_invoice_id FROM pos_sales_invoice_to_payment WHERE pos_customer_payment_id = $pos_customer_payment_id");
if (sizeof($pos_sales_invoice_id_array)>1)
{
	trigger_error('WTF - there are more than one sales invoice tied to this payment');
}
else
{
	$pos_sales_invoice_id = $pos_sales_invoice_id_array[0]['pos_sales_invoice_id'];
	$invoice_status = getSingleValueSQL("SELECT invoice_status FROM pos_sales_invoice WHERE pos_sales_invoice_id = $pos_sales_invoice_id");
}
	

if (isset($_GET['type']))
{
	$type = $_GET['type'];
}
else if (isset($_POST['type']))
{
	$type = $_POST['type'];
}
else
{
	//error no type
	trigger_error('missing type');
}

if (isset($_POST['submit']))
{
	//form handler....
	$dbc = startTransaction();
	$table_def_array = deserializeTableDef($_POST['table_def']);
	$insert = tableDefArraytoMysqlInsertArray($table_def_array);	

	//this is an update
	$pos_customer_payment_id = getPostOrGetID('pos_customer_payment_id');
	$key_val_id['pos_customer_payment_id'] = $pos_customer_payment_id;
	$results[] = simpleTransactionUpdateSQL($dbc,'pos_customer_payments', $key_val_id, $insert);
	$message = urlencode('Payment ID ' . $pos_customer_payment_id . " has been updated");
	
	simpleCommitTransaction($dbc);
	header('Location: '.addGetToUrl($_POST['complete_location'] ,'message=' . $message));
	exit();
}
else
{
	if ($type == 'view')
	{
		$edit_location = 'payments.php?pos_customer_payment_id='.$pos_customer_payment_id.'&type=edit';
		$delete_location = 'payments.php?pos_customer_payment_id='.$pos_customer_payment_id.'&type=delete';
		$db_table = 'pos_customer_payments';
		$key_val_id['pos_customer_payment_id']  = $pos_customer_payment_id;
		$data_table_def = createCustomerPaymentTableDef();
		$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def);
		$html = printGetMessage('message');
		$html .=  '<script src="'.$payments_javascript.'"></script>'.newline();

		$html .= '<p>Invoice Status: ' . $invoice_status .'</p>';
		$html .= '<p>View Customer Payment For invoice # '. $pos_sales_invoice_id . ' status: '. $invoice_status.'</p>';
		$html .= confirmDelete($delete_location);
		$html .= createHTMLTableForMYSQLData($data_table_def);
	
		//options: depending on who you are: void, delete, edit
		//pos_allow_voids
		//pos_allow_refunds??? not on this page....
		//pos_edit_closed_payments
		
		if($invoice_status =='CLOSED')
		{
			$html .='<p>Invoice is Closed, with proper access users can still void, edit, and delete payments</p>';
			if(checkUserGroupAccess($_SESSION['pos_user_id'], 'pos_allow_voids'))
			{
				$html .= checkVoid($pos_customer_payment_id);
			}
			if(checkUserGroupAccess($_SESSION['pos_user_id'], 'pos_edit_closed_payments'))
			{
				$html .= '<p><input class = "button group_button"  type="button" name="edit"  value="Edit" onclick="open_win(\''.$edit_location.'\')"/>';
				$html .= '<input class = "button group_button" type="button" name="delete" value="Delete Payment" onclick="confirmDelete();"/>';
			}
		}
		else
		{
			$html .= '<p><input class = "button"  type="button" name="edit"  value="Edit" onclick="open_win(\''.$edit_location.'\')"/>';
			$html .= '<input class = "button" type="button" name="delete" value="Delete Payment" onclick="confirmDelete();"/>';
			$html .= '<p><input class = "button"  type="button" name="edit"  value="Edit" onclick="open_win(\''.$edit_location.'\')"/>';
			if(checkUserGroupAccess($_SESSION['pos_user_id'], 'pos_allow_voids'))
			{
				// can this be voided?
				
				$html .= checkVoid($pos_customer_payment_id);
			}
		}
	
	
		if (checkIfUserIsAdmin($_SESSION['pos_user_id']))
		{
			
		}
		include (HEADER_FILE);

		echo $html;
		include (FOOTER_FILE);
		exit();
	
	
	}
	elseif ($type == 'edit')
	{
		//who can edit??
		
	
	if (checkIfUserIsAdmin($_SESSION['pos_user_id']))
	{
	}
	else if(  $invoice_status == 'CLOSED')
	{
		trigger_error('Attempting to access payments for a closed invoice:' .$pos_sales_invoice_id);
		exit();
		
	}
		$header = '<p>EDIT Payment</p>';
		$page_title = 'Edit Payment';
		$data_table_def_no_data = createCustomerPaymentTableDef();
		$db_table = 'pos_customer_payments';
		$key_val_id['pos_customer_payment_id']  = $pos_customer_payment_id;
		$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def_no_data);
		$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);
		$big_html_table .= createHiddenInput('type', $type);
	
		$html = $header;
		$form_handler = 'payments.php';
		$complete_location = 'payments.php?type=view&pos_customer_payment_id='.$pos_customer_payment_id;
		$cancel_location = $complete_location;
		$html .= createFormForMYSQLInsert($data_table_def, $big_html_table, $form_handler, $complete_location, $cancel_location);
		include (HEADER_FILE);
		echo $html;
		include (FOOTER_FILE);
		exit();
	}
	elseif ($type == 'delete')
	{
		
		if(  $invoice_status == 'CLOSED')
		{
			if(checkUserGroupAccess($_SESSION['pos_user_id'], 'pos_edit_closed_payments'))
			{
				//good to go
			}
			elseif (checkIfUserIsAdmin($_SESSION['pos_user_id']))
			{
				//good to go....
			}
			else
			{
				//problem...
				trigger_error('Attempting to access payments for a closed invoice:' .$pos_sales_invoice_id);
				exit();
			}
			
		}
		
		$dbc = startTransaction();
		//preprint(getTransactionSQL($dbc,"SELECT * FROM pos_sales_invoice_to_payment WHERE pos_sales_invoice_id = $pos_sales_invoice_id"));
		runTransactionSQL($dbc,"DELETE FROM pos_sales_invoice_to_payment WHERE pos_customer_payment_id = $pos_customer_payment_id");
		runTransactionSQL($dbc,"DELETE FROM pos_customer_payments WHERE pos_customer_payment_id= $pos_customer_payment_id");
		//preprint(getTransactionSQL($dbc,"SELECT * FROM pos_sales_invoice_to_payment WHERE pos_sales_invoice_id = $pos_sales_invoice_id"));
		$grand_total_from_contents = getSalesInvoiceGrandeTotalFromExtension($pos_sales_invoice_id);
		$total_payments = getTransactionSingleValueSQL($dbc,"SELECT coalesce(sum(applied_amount),0) FROM pos_sales_invoice_to_payment WHERE pos_sales_invoice_id = $pos_sales_invoice_id");
		if(abs($grand_total_from_contents - $total_payments)<0.0001)
		{
			
			//fully paid, close the invoice.
			runTransactionSQL($dbc, "UPDATE pos_sales_invoice SET payment_status = 'PAID', invoice_status = 'CLOSED' WHERE pos_sales_invoice_id = $pos_sales_invoice_id");
		}
		else
		{
			//fully paid, close the invoice.
			runTransactionSQL($dbc, "UPDATE pos_sales_invoice SET payment_status = 'UNPAID', invoice_status = 'DRAFT' WHERE pos_sales_invoice_id = $pos_sales_invoice_id");
		}
		
		
		simpleCommitTransaction($dbc);
		//where to go?
		include (HEADER_FILE);
		echo 'Payment '. $pos_customer_payment_id . ' Deleted';
		include (FOOTER_FILE);
		exit();

	}
	elseif ($type == 'void')
	{
		//check again.....
		if($invoice_status == 'CLOSED')
		{
			if(checkUserGroupAccess($_SESSION['pos_user_id'], 'pos_edit_closed_payments'))
			{
				//good to go
			}
			elseif (checkIfUserIsAdmin($_SESSION['pos_user_id']))
			{
				//good to go....
			}
			else
			{
				//problem...
				trigger_error('Attempting to access payments for a closed invoice:' .$pos_sales_invoice_id);
				exit();
			}
			
		}
		
		//void sets the amount to zero, however the payment stays.....
		$pos_payment_gateway_id = getSingleValueSQL("SELECT pos_payment_gateway_id FROM pos_customer_payments WHERE pos_customer_payment_id = $pos_customer_payment_id");
		$transaction_id = getSingleValueSQL("SELECT transaction_id FROM pos_customer_payments WHERE pos_customer_payment_id = $pos_customer_payment_id");
		
		//what is the payment gateway id?
		$api_login = getAPILoginID($pos_payment_gateway_id);
		$transaction_key = getTrasactionKey($pos_payment_gateway_id);
		
		
		$post_values = array(

		// the API Login ID and Transaction Key must be replaced with valid values
		"x_login"			=> $api_login,
		"x_tran_key"		=> $transaction_key,
		"x_device_type"		=> "4",
		"x_cpversion"		=> "1.0",
		"x_market_type" 	=> "2",

		"x_delim_data"		=> "TRUE",
		"x_delim_char"		=> "|",
		"x_relay_response"	=> "FALSE",
		
		"x_type"			=> "VOID",
		"x_trans_id"			=> $transaction_id,
		

		);
		$response_array = process_cc_payment($post_values);
		//preprint($response_array);
		//trigger_error();
		
		//now what? set the payment value to zero....
		$dbc = startTransaction();			
		$customer_payment_inset['payment_amount'] = 0;
			$customer_payment_inset['transaction_status'] = 'VOIDED';
			$key_val['pos_customer_payment_id'] = $pos_customer_payment_id;
			simpleTransactionUpdateSQL($dbc,'pos_customer_payments', $key_val, $customer_payment_inset);
	
			runTransactionSQL($dbc,"UPDATE pos_sales_invoice_to_payment SET applied_amount = 0 WHERE pos_customer_payment_id=$pos_customer_payment_id");
	
			simpleCommitTransaction($dbc);
			
			
			//now we cant accept payment?
			$sales_invoices = getSQL("SELECT pos_sales_invoice_id FROM pos_sales_invoice_to_payment WHERE pos_customer_payment_id=$pos_customer_payment_id");
			for($si=0;$si<sizeof($sales_invoices);$si++)
			{
				finalizePaymentTransaction($sales_invoices[$si]['pos_sales_invoice_id']);
			}
			$message = urlencode($response_array[3]);
			header('Location: payments.php?type=view&pos_customer_payment_id='.$pos_customer_payment_id.'&message=' . $message);
	exit();
			
		
		
	}
	else
	{
		trigger_error('wrong type');
	}

}


function checkVoid($pos_customer_payment_id)
{	
	if(checkUserGroupAccess($_SESSION['pos_user_id'], 'pos_allow_voids'))
	{
		
		$void_data = getSQL("SELECT transaction_id,batch_id, transaction_status FROM pos_customer_payments WHERE pos_customer_payment_id = $pos_customer_payment_id");
		if($void_data[0]['batch_id'] == '' && $void_data[0]['transaction_id'] != '' && $void_data[0]['transaction_status'] != 'VOIDED')
		{
			$loc = 'payments.php?pos_customer_payment_id='.$pos_customer_payment_id.'&type=void';
			$html = '<p><input class = "button group_button"  type="button" name="edit"  value="VOID" onclick="confirmVoid(\''.$loc.'\');"/>';
			return $html;
		}
		else
		{
			return '';
		}
		
	}
	else
	{	
		return '';
	}
	
}	
function createCustomerPaymentTableDef()
{
	/*
	
	differences.... 
	cash - change given
	check number, license number
	credit card number, transaction id?, batch 
	store credit id linked to number
	
	*/
	return array( 
						array( 'db_field' => 'pos_customer_payment_id',
								'type' => 'input',
								'tags' => ' readonly = "readonly" ',
								'caption' => 'Payment ID',
								//'value' => $pos_promotion_id,
								'validate' => 'none'
								
								),
						array('db_field' => 'date',
								'caption' => 'Date Time',
								'type' => 'input',
								),	
						array('db_field' =>  'pos_customer_payment_method_id',
								'type' => 'select',
								'caption' => 'Payment Mehod',
								'html' => createCustomerPaymentTypeSelect('pos_customer_payment_method_id', 'false'),
								'validate' => 'none'						
								),
						array('db_field' =>  'pos_payment_gateway_id',
								'type' => 'input',
								'caption' => 'Payment Gateway ID',
								'validate' => 'none'),
						
						array('db_field' =>  'transaction_status',
								'type' => 'input',
								'caption' => 'Transaction Status',
								'validate' => 'none'),
						array('db_field' =>  'transaction_id',
								'type' => 'input',
								'caption' => 'Transaction ID',
								'validate' => 'none'),
						array('db_field' =>  'batch_id',
								'type' => 'input',
								'caption' => 'Batch ID',
								'validate' => 'none'),
						array('db_field' =>  'card_number',
								'type' => 'input',
								'caption' => 'Card Number',
								'validate' => 'none'),
						array('db_field' =>  'pos_store_credit_id',
								'type' => 'input',
								'caption' => 'Store Credit System ID',
								'validate' => 'none'),
						array('db_field' =>  'reference_number',
								'type' => 'input',
								'caption' => 'Reference Number',
								'validate' => 'none'),
						array('db_field' =>  'payment_status',
								'type' => 'input',
								'caption' => 'Payment Status',
								'validate' => 'none'),
						
						
						array('db_field' =>  'payment_amount',
								'type' => 'input',
								'caption' => 'Payment Amount',
								'validate' => 'number'),
						
						array( 'db_field' => 'deposit_account_id',
								'type' => 'select',
								'caption' => 'Deposit Account',
								'html' => createAccountSelect('deposit_account_id', 'false'),
								'validate' =>array('select_value' => 'false')),	
	
						array('db_field' =>  'comments',
								'type' => 'input',
								'caption' => 'Comments'),

						);		
} 
function createCustomerPaymentTypeSelect($name, $pos_customer_payment_method_id, $tags = ' onchange="needToConfirm=true" ' )
{	
	$payment_methods = getSQL("SELECT * FROM pos_customer_payment_methods");
	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= $tags;
	$html .= '>';
	//Add an option for not selected
	//$html .= '<option value="false">Select Account</option>';
	//add an option for all accounts
	
	for($i = 0;$i < sizeof($payment_methods); $i++)
	{
		$html .= '<option value="' . $payment_methods[$i]['pos_customer_payment_method_id'] . '"';
		
		if ( ($payment_methods[$i]['pos_customer_payment_method_id'] == $pos_customer_payment_method_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $payment_methods[$i]['payment_type'] . '</option>';
	}
	$html .= '</select>';
	return $html;
}
?>
