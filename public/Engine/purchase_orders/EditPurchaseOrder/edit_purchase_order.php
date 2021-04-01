<?php 
/*
	Craig Iannazzi 4-23-12
	
*/
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
$page_title = 'Create Purchase Order';
require_once('../po_functions.php');
$db_table = 'pos_purchase_orders';
$id['pos_purchase_order_id'] = getPostOrGetID('pos_purchase_order_id');
$pos_purchase_order_id = $id['pos_purchase_order_id'];
$complete_location = '../ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id='.$id['pos_purchase_order_id'];
$cancel_location = '../ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id='.$id['pos_purchase_order_id'];

$table_def = createPOTableDef($db_table);
//preprint($table_def);
$table_def_with_data = loadDataToTableDef($table_def, $db_table, $id);
//preprint($table_def_with_data);
$big_html_table = convertTableDefToHTMLForMYSQLInsert($table_def_with_data);
if(!checkifUserIsAdmin($_SESSION['pos_user_id']))
{
	//the select will be disabled so we will need to post a hidden value....
	$big_html_table .= createHiddenInput('pos_manufacturer_brand_id', getBrandIdFromPOId($pos_purchase_order_id));
}
//echo $big_html_table;
$table_def_for_post = convertArrayTableDefToPostTableDef($table_def);


include (HEADER_FILE);
$html =  '<script src="../CreatePurchaseOrder/create_purchase_order.js"></script>'.newline();
$html .= '<h2>Edit Purchase Order</h2>';
$form_handler = 'edit_purchase_order.form.handler.php';
$html .= createFormForMYSQLInsert($table_def_for_post, $big_html_table, $form_handler, $complete_location, $cancel_location);
$html .= createBrandCodeBrandIDLookup() .newline();
echo $html;
include (FOOTER_FILE);


?>