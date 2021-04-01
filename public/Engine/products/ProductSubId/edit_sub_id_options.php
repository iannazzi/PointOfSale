<?php

$page_title = 'View Product Sub ID';
$binder_name = 'Products';
$access_type = 'READ';
require_once ('../product_functions.php');


$pos_product_sub_id = getPostOrGetID('pos_product_sub_id');


	$data = getProductSubIdOptions($pos_product_sub_id);
	$table_def = createDynamicSubIdOptionTableDef($pos_product_sub_id);
	$html_table = createHiddenInput('pos_product_sub_id', $pos_product_sub_id);
	$html_table .= createDynamicTable($table_def, $data);
	$html = '<h3>BEEEEEE Careful here - only one of each option, and there can\'t be already claimed by other subids</h3>';
	$form_handler = 'edit_sub_id_options.form.handler.php';
	$complete_location = 'view_product_sub_id.php?pos_product_sub_id='.$pos_product_sub_id;
	$cancel_location = $complete_location;
	$html .= createFormForDynamicTableMYSQLInsert($table_def, $html_table, $form_handler, $complete_location, $cancel_location);
	
	
	$html .=  '<script src="product_subid_options.js"></script>'.newline();
	$html .=  '<script> var pos_product_sub_id = '.$pos_product_sub_id.';</script>'.newline();
	include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);

?>