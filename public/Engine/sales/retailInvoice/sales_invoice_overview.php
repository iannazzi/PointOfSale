<?php 

/*
	I think we will just create the invoice, set it to INIT, the re-direct to the invoice page....
	
	Craig Iannazzi 10-24-12
	
*/
$binder_name = 'Sales Invoices';
$access_type = 'WRITE';
$page_title = 'Sales';
require_once ('../sales_functions.php');
$type = getPostOrGetValue('type');
//need to check to make sure the user is set up...
//we need at least a store id
check_user_store_set();

//terminal check
if (!getTerminalCookie())
{
	include(HEADER_FILE);
	echo '<p class="error">ERROR - POS Terminal is not set-up. Contact System Admin to add this system as a terminal. A system consists of both a computer and a web-browser. Safari, Firefox, and Chrome all need to be independantly set up on a computer.</p>';
	include(FOOTER_FILE);
	exit();
}
else
{	
	$pos_terminal_id = getTerminalID(getTerminalCookie());
	if(getDefaultTerminalPrinter($pos_terminal_id)	== 0)
	{
		include(HEADER_FILE);
		echo '<p class="error">ERROR - POS Terminal Printer is not set-up. Contact System Admin to add a printer to this system. </p>';
		include(FOOTER_FILE);
		exit();
	}
}





if(strtoupper($type) =='SIMPLE')
{
	$complete_location = 'retail_sales_invoice.php';
	$dbc = startTransaction();
	$insert['pos_user_id'] = $_SESSION['pos_user_id'];
	$insert['pos_store_id'] = $_SESSION['store_id'];
	$insert['invoice_date'] = getCurrentTime();
	$insert['invoice_status'] = 'INIT';
	$insert['payment_status'] = 'UNPAID';
	$pos_sales_invoice_id = simpleTransactionInsertSQLReturnID($dbc,'pos_sales_invoice', $insert);
	//now get the next maximum value....right?
	//$max = getTransactionSingleValueSQL($dbc, "SELECT MAX(invoice_number) FROM pos_sales_invoice");
	//runTransactionSQL($dbc, "UPDATE pos_sales_invoice SET invoice_number = $max +1 WHERE pos_sales_invoice_id =  $pos_sales_invoice_id");
	simpleCommitTransaction($dbc);
	$complete_location = 'select_customer.php?complete_location=' . urlencode(POS_ENGINE_URL . '/sales/retailInvoice/retail_sales_invoice.php?type=edit&pos_sales_invoice_id='. $pos_sales_invoice_id);
	header('Location: '.$complete_location );
	exit();
}
elseif(strtoupper($type) =='ADD')
{
	$pos_sales_invoice_id = 'TBD';
	//this wont work, will do in the form handler
	$complete_location = 'retail_sales_invoice.php';
	$cancel_location = 'list_retail_sales_invoices.php';
	$header = '<p>Add Sales Invoice</p>';
	$page_title = 'Add Sales Invoice';
	$data_table_def = createSalesInvoiceTableDef($type, $pos_sales_invoice_id);
	
}
elseif(strtoupper($type) =='EDIT')
{
	$pos_sales_invoice_id = getPostOrGetID('pos_sales_invoice_id');
	$complete_location = 'sales_invoice_overview.php?type=view&pos_sales_invoice_id='.$pos_sales_invoice_id;
	$cancel_location = $complete_location;
	$header = '<p>EDIT Sales Invoice</p>';
	$page_title = 'Edit Sales Invoice';
	$table_def = createSalesInvoiceTableDef($type, $pos_sales_invoice_id);
	$db_table = 'pos_sales_invoice';
	$key_val_id['pos_sales_invoice_id'] = $pos_sales_invoice_id;
	$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $table_def);
}
elseif(strtoupper($type) =='VIEW')
{
	
	$pos_sales_invoice_id = getPostOrGetID('pos_sales_invoice_id');
	$complete_location = 'list_retail_sales_invoices.php';
	$cancel_location = $complete_location;
	$header = '<p>View Sales Invoice</p>';
	$page_title = 'View Sales Invoice';
	$edit_location = 'sales_invoice_overview.php?pos_sales_invoice_id='.$pos_sales_invoice_id.'&type=edit';
	//$delete_location = 'delete_promotion.form.handler.php?pos_promotion_id='.$pos_promotion_id;
	$db_table = 'pos_sales_invoice';
	$key_val_id['pos_sales_invoice_id'] = $pos_sales_invoice_id;
	$data_table_def = createSalesInvoiceTableDef($type, $pos_sales_invoice_id);
	$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def);
}
else
{
	echo 'Type is not set correctly';
}


//build the html page
if (strtoupper($type) == 'VIEW')
{
	$html = printGetMessage('message');
	$html .= $header;
	//$html .= confirmDelete($delete_location);
	$html .= createHTMLTableForMYSQLData($data_table_def);
	$html .= '<p><input class = "button"  type="button" name="edit"  value="Edit" onclick="open_win(\''.$edit_location.'\')"/>';
// $html .= '<input class = "button" type="button" name="delete" value="Delete promotion" onclick="confirmDelete();"/>';
	
	$html .= '<p>';
	$html .= '<INPUT class = "button" type="button" style ="width:200px" value="View Invoice" onclick="window.location = \'retail_sales_invoice.php?type=view&pos_sales_invoice_id='.$pos_sales_invoice_id. '\';" />';
	$html .= '<INPUT class = "button" type="button" style ="width:200px" value="Back To Invoices" onclick="window.location = \''.$complete_location.'\'" />';
	
	$html .= '</p>';
}
else
{
	$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);
	$big_html_table .= createHiddenInput('type', $type);
	
	$html = $header;
	$form_handler = 'sales_invoice_overview.form.handler.php';
	$html .= createFormForMYSQLInsert($data_table_def, $big_html_table, $form_handler, $complete_location, $cancel_location);
	$html .= '<script>document.getElementsByName("invoice_number")[0].focus();</script>';
}


include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);

function createSalesInvoiceTableDef($type, $pos_sales_invoice_id)
{
	if ($pos_sales_invoice_id =='TBD')
	{
		$unique_validate = array('unique' => 'invoice_number', 'min_length' => 1);
	}
	else
	{
		$key_val_id['pos_sales_invoice_id'] = $pos_sales_invoice_id;
		$unique_validate = array('unique' => 'invoice_number', 'min_length' => 1, 'id' => $key_val_id);
	}
	
	return array( 
						array( 'db_field' => 'pos_sales_invoice_id',
								'type' => 'input',
								'tags' => ' readonly = "readonly" ',
								'caption' => 'System Invoice ID',
								'value' => $pos_sales_invoice_id,
								'validate' => 'none'
								
								),
						array('db_field' =>  'invoice_number',
								'type' => 'input',
								'db_table' => 'pos_sales_invoice',
								'caption' => 'Invoice Number',
								
								'validate' => 'none'),	
						
								array('db_field' => 'invoice_date',
								'caption' => 'Invoice Date',
								'type' => 'date',
								'separate_date' => 'date',
								'tags' => ' ',
								'html' => dateSelect('invoice_date',''),
								'validate' => 'date'),
									
						/*array('db_field' => 'invoice_date',
								'caption' => 'Invoice Time',
								'type' => 'time',
								'separate_date' => 'time',
								'post_name' => 'invoice_time',
								'tags' => ' ',
								//'html' => timeSelect('invoice_time',''),
								'validate' => 'none'),*/
								array('db_field' =>  'pos_store_id',
								'type' => 'select',
								'html' => createStoreSelect('pos_store_id', $_SESSION['store_id'],  'off'),
								'caption' => 'Store',
								'validate' => 'false'),
						/*array('db_field' =>  'pos_employee_id',
								'type' => 'select',
								'caption' => 'Employee',
								'html' => createEmployeeSelect('pos_employee_id', $_SESSION['pos_employee_id'],  'off'),
),
						
					
						array('db_field' => 'sales_invoice_status',
								'caption' => 'Invoice Status',
								'type' => 'select',
								'html' => createEnumSelect('sales_invoice_status','pos_sales_invoice', 'sales_invoice_status', 'false')),
								array('db_field' => 'customer_payment_status',
								'caption' => 'Payment Status',
								'type' => 'select',
								'html' => createEnumSelect('customer_payment_status','pos_sales_invoice', 'customer_payment_status', 'UNPAID')),*/
						/*array('db_field' => 'tax_calculation_method',
								'caption' => 'Tax Calulation Method',
								'type' => 'select',
								'html' => createEnumSelect('tax_calculation_method','pos_sales_invoice', 'tax_calculation_method', 'minimum')),*/
						
						array('db_field' =>  'comments',
								'type' => 'input',
								'caption' => 'Comments')
						);	

}
?>

	