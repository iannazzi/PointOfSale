<?php 

/*
	The process to create a new db entry for things like mfg, employee, etc is to define what we want in the table, then create that table....event post the table in json format for the handler to process?
	
	Craig Iannazzi 1-23-12
	
*/

require_once ('../sales_functions.php');

$complete_location = 'list_payment_methods.php';
$cancel_location = 'list_payment_methods.php?message=Canceled';
$type = getPostOrGetValue('type');
if ($type =='edit')
{
	$pos_customer_payment_method_id = getPostOrGetID('pos_customer_payment_method_id');
	$table_type = 'Edit';
	$header = '<p>EDIT Payment Method</p>';
	$page_title = 'Edit Payment Method ';
	$data_table_def_no_data = createCustomerPyamentMethodTableDef($table_type, $pos_customer_payment_method_id);
	$db_table = 'pos_customer_payment_method';
	$key_val_id['pos_customer_payment_method_id'] = $pos_customer_payment_method_id;
	$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def_no_data);
}
else if($type == 'Add')
{
	$table_type = 'New';
	$pos_customer_payment_method_id = 'TBD';
	$header = '<p>ADD Payment Method</p>';
	$page_title = 'Add Payment Method';
	$data_table_def = createCustomerPyamentMethodTableDef($table_type, $pos_customer_payment_method_id);
}
else if($type == 'view')
{
	$pos_customer_payment_method_id = getPostOrGetID('pos_customer_payment_method_id');
	$edit_location = 'add_edit_view_payment_method.php?pos_sales_tax_rate_id='.$pos_sales_tax_rate_id.'&type=edit';
	$primary_id_name = 'pos_customer_payment_method_id';
	$primary_id_value = $pos_customer_payment_method_id;
	$delete_message = urlencode('Confirm Delete this method?');
	$db_table = 'pos_customer_payment_method';

	$delete_location =  POS_ENGINE_URL . '/includes/php/delete_mysql_entry.php?db_table='.$db_table.'&primary_id_name='.$primary_id_name.'&primary_id_value='.$primary_id_value.'&delete_message='.$delete_message.'&complete_location='.POS_ENGINE_URL.'/taxes/sales_tax_rates/'.$complete_location.'&cancel_location='.POS_ENGINE_URL.'/taxes/sales_tax_rates/'.$cancel_location;
	$page_title = 'Customer Payment Method';
	$key_val_id['pos_customer_payment_method_id']  = $pos_customer_payment_method_id;
	$data_table_def = createCustomerPyamentMethodTableDef('View', $pos_customer_payment_method_id);
	$table_def_w_data = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def);
}
if($type == 'view')
{
	$html = printGetMessage('message');
	$html .= '<p>View Customer Payment Method</p>';
	
	$html .= createHTMLTableForMYSQLData($table_def_w_data);
	$html .= '<p><input class = "button"  type="button" name="edit"  value="Edit" onclick="open_win(\''.$edit_location.'\')"/>';
	$html .= '<input class = "button"  type="button" name="edit"  value="Delete" onclick="open_win(\''.$delete_location.'\')"/>';
	$html .= '</p>';
	$html .= '<p>';
	$html .= '<INPUT class = "button" type="button" style ="width:200px" value="Back" onclick="window.location = \''.$complete_location.'\'" />';
	$html .= '</p>';
}
else
{
	$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);
	$big_html_table .= createHiddenInput('type', $type);
	$html = $header;
	$form_handler = 'add_edit_payment_method.form.handler.php';
	$html .= createFormForMYSQLInsert($data_table_def, $big_html_table, $form_handler, $complete_location, $cancel_location);
	$html .= '<script>document.getElementsByName("payment_method_name")[0].focus();</script>';
}

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);

?>

	