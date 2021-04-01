<?php 

/*
	The process to create a new db entry for things like mfg, employee, etc is to define what we want in the table, then create that table....event post the table in json format for the handler to process?
	
	Craig Iannazzi 10-23-12
	
*/


$binder_name = 'Product Attributes';
$access_type = 'READ';
require_once ('../product_functions.php');
$pos_product_attribute_id = getPostOrGetID('pos_product_attribute_id');

$attribute_ame = getAttributeName($pos_product_attribute_id);
$page_title = 'Attribute: ' . $attribute_ame;


$complete_location = 'list_attributes.php';
$cancel_location = 'list_attributes.php?message=Canceled';
$edit_location = 'add_edit_attribute.php?pos_product_attribute_id='.$pos_product_attribute_id.'&type=edit';
//$delete_location = 'delete_location.form.handler.php?pos_location_id='.$pos_location_id;

$db_table = 'pos_product_attributes';
$key_val_id['pos_product_attribute_id']  = $pos_product_attribute_id;
$data_table_def = createProductAttributeTableDef('View', $pos_product_attribute_id);
$table_def_w_data = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def);


	//now the delete
	
	

$html = printGetMessage('message');
$html .= '<p>View Attribute</p>';
//$html .= confirmDelete($delete_location);
$html .= createHTMLTableForMYSQLData($table_def_w_data);
$html .= '<p><input class = "button"  type="button" name="edit"  value="Edit" onclick="open_win(\''.$edit_location.'\')"/>';
	// $html .= '<input class = "button" type="button" name="delete" value="Delete Room" onclick="confirmDelete();"/>';


$html .= '<p>';

$html .= '<INPUT class = "button" type="button" style ="width:200px" value="Back to Attributes" onclick="window.location = \''.$complete_location.'\'" />';

$html .= '</p>';



include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);






?>