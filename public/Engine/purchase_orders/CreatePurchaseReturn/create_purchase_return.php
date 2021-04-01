<?php 
/*
	*edit_product.php
	*main page used to edit existing a manufacturer to the pos system
	Craig Iannazzi 1-23-12
	
*/
$page_title = 'Create A Return';
require_once('../po_functions.php');
$db_table = 'pos_purchase_returns';
$pos_manufacturer_id = getPostOrGetID('pos_manufacturer_id');

	$complete_location = '../purchase_orders.php';
	$cancel_location = '../purchase_orders.php';

$table_def = createNewPReturnTableDef($db_table,$pos_manufacturer_id);


$big_html_table = convertTableDefToHTMLForMYSQLInsert($table_def);
$big_html_table .= createHiddenInput('pos_manufacturer_id', $pos_manufacturer_id);
//echo $big_html_table;
$table_def_for_post = convertArrayTableDefToPostTableDef($table_def);


include (HEADER_FILE);
$html = '';
//$html =  '<script src="../CreatePurchaseOrder/create_purchase_order.js"></script>'.newline();
$html .= '<h2>Create A Purchase Return</h2>';
$form_handler = 'create_purchase_return.form.handler.php';
$html .= createFormForMYSQLInsert($table_def_for_post, $big_html_table, $form_handler, $complete_location, $cancel_location);
$html .= createBrandCodeBrandIDLookup() .newline();
echo $html;
include (FOOTER_FILE);
?>