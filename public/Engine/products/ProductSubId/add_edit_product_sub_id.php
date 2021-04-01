<?php 

/*
	The process to create a new db entry for things like mfg, employee, etc is to define what we want in the table, then create that table....event post the table in json format for the handler to process?
	
	Craig Iannazzi 1-23-12
	
*/

$binder_name = 'Products';
$access_type = 'WRITE';
require_once ('../product_functions.php');

$pos_product_id = getPostOrGetValue('pos_product_id');
$complete_location = '../ViewProduct/view_product.php?pos_product_id='.$pos_product_id;
$cancel_location = $complete_location;


$type = getPostOrGetValue('type');
if (strtoupper($type) =='EDIT')
{
	//when editing we are only editing the invoice, not payment info
	$pos_product_sub_id = getPostOrGetID('pos_product_sub_id');
	$table_type = 'Edit';
	$header = '<p>EDIT Product Sub Id (NOTE THIS IS CURRENTLY DANGEROUS - do not add extra characters to the attributes List)</p>';
	$page_title = 'Edit Sub Id';
	$data_table_def_no_data = createProductSUBIDTableDef($table_type, $pos_product_sub_id, $pos_product_id);	
	$db_table = 'pos_products_sub_id';
	$key_val_id['pos_product_sub_id'] = $pos_product_sub_id;
	$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def_no_data);
}
else
{
	$table_type = 'New';
	$pos_product_sub_id = 'TBD';
	$header = '<p>Add Product Sub ID (NOTE THIS IS DANGEROUS)</p>';
	$page_title = 'Add Product Sub Id';
	$data_table_def = createProductSUBIDTableDef($table_type, $pos_product_sub_id, $pos_product_id);
}

$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);
$big_html_table .= createHiddenInput('type', $type);
$big_html_table .= createHiddenInput('pos_product_id', $pos_product_id);
$html = $header;
$form_handler = 'add_edit_product_sub_id.form.handler.php';
$html .= createFormForMYSQLInsert($data_table_def, $big_html_table, $form_handler, $complete_location, $cancel_location);

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);

?>

	