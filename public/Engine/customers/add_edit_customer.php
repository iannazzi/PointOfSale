<?php 

/*
	The process to create a new db entry for things like mfg, employee, etc is to define what we want in the table, then create that table....event post the table in json format for the handler to process?
	
	Craig Iannazzi 1-23-12
	
*/
$binder_name = 'Customers';
$access_type = 'WRITE';
require_once ('customer_functions.php');
$type = getPostOrGetValue('type');
if($type == 'ADD_SELECT' || $type == 'EDIT_SELECT')
{
	$complete_location = urldecode(getPostOrGetValue('complete_location'));
	$cancel_location = $complete_location;
}
else
{
	$complete_location = 'list_customers.php';
	$cancel_location = 'list_customers.php?message=Canceled';
}



if ($type =='edit' || $type == 'EDIT_SELECT')
{
	//when editing we are only editing the invoice, not payment info
	$pos_customer_id = getPostOrGetID('pos_customer_id');
	$customer_data = getCustomerData($pos_customer_id);
	$table_type = 'Edit';
	$header = '<p>EDIT Customer</p>';
	$page_title = 'Edit Customer ' . $customer_data[0]['pos_customer_id'];
	$data_table_def_no_data = createCustomerTableDef($table_type, $pos_customer_id);	
	$db_table = 'pos_customers';
	$key_val_id['pos_customer_id'] = $pos_customer_id;
	$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def_no_data);
}
else
{
	$table_type = 'New';
	$pos_customer_id = 'TBD';
	$header = '<p>ADD Customer</p>';
	$page_title = 'Add Customer';
	$data_table_def = createCustomerTableDef($table_type, $pos_customer_id);
}

//create the customer form
$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);

// add some hidden stuff for form processing
$big_html_table .= createHiddenInput('type', $type);
if($type == 'ADD_SELECT' || $type == 'EDIT_SELECT')
{
	$big_html_table .= createHiddenInput('complete_location', $complete_location);
}
$html = $header;
$form_handler = 'add_edit_customer.form.handler.php';
$table_array = array($data_table_def);
$html .= createMultiPartFormForMultiMYSQLInsert($table_array, $big_html_table, $form_handler, $complete_location, $cancel_location);
$html .= '<script>document.getElementsByName("first_name")[0].focus();</script>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);

?>

	