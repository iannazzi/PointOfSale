<?php 

/*
	The process to create a new db entry for things like mfg, employee, etc is to define what we want in the table, then create that table....event post the table in json format for the handler to process?
	
	Craig Iannazzi 1-23-12
	
*/


$binder_name = 'Product Attributes';
$access_type = 'WRITE';
require_once ('../product_functions.php');


$complete_location = 'list_attributes.php';
$cancel_location = $complete_location. '?message=Canceled';
$type = getPostOrGetValue('type');
if (strtoupper($type) =='EDIT')
{
	//when editing we are only editing the invoice, not payment info
	$pos_product_attribute_id = getPostOrGetID('pos_product_attribute_id');
	$table_type = 'Edit';
	$page_title = ' Edit Attribute';
	$header = '<p>EDIT Attribute</p>';
	$data_table_def_no_data = createProductAttributeTableDef($table_type, $pos_product_attribute_id);	
	$db_table = 'pos_product_attributes';
	$key_val_id['pos_product_attribute_id'] = $pos_product_attribute_id;
	$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def_no_data);
}
else
{
	$table_type = 'New';
	$pos_product_attribute_id = 'TBD';
	$header = '<p>Add Attribute</p>';
	$page_title = 'Add Attribute';
	$data_table_def = createProductAttributeTableDef($table_type, $pos_product_attribute_id);
}

$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);
$big_html_table .= createHiddenInput('type', $type);

$html = $header;
$form_handler = 'add_edit_attribute.form.handler.php';
$html .= createFormForMYSQLInsert($data_table_def, $big_html_table, $form_handler, $complete_location, $cancel_location);
$html .= '<script>document.getElementsByName("attribute_name")[0].focus();</script>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);

?>

	