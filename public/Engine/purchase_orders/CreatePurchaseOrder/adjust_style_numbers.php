<?php
/*
	*recieve_purchase_order.php
	*Craig Iannazzi 2-14-12
	*This form is used to recieve the purchase order
*/
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
$page_title = 'Adjust Style Number';
require_once ('../po_functions.php');
$pos_purchase_order_id =  getPostOrGetID('pos_purchase_order_id');
$complete_location = POS_ENGINE_URL . '/purchase_orders/ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id='.$pos_purchase_order_id;
$cancel_location = $complete_location;
$form_handler = 'adjust_style_numbers.form.handler.php';
$unlock_location = POS_ENGINE_URL . '/purchase_orders/CreatePurchaseOrder/adjust_style_numbers.php?pos_purchase_order_id='.$pos_purchase_order_id;
check_lock('pos_purchase_orders', array('pos_purchase_order_id' => $pos_purchase_order_id),$unlock_location, $cancel_location);


if (checkForValidIDinPOS($pos_purchase_order_id, 'pos_purchase_orders', 'pos_purchase_order_id'))
{
	
	lock_entry('pos_purchase_orders', array('pos_purchase_order_id' => $pos_purchase_order_id));
	$table_def = createAdjustStyleNumberTableDef($pos_purchase_order_id);
	$purchase_order_unique_products = getPurchaseOrderContentsLimitedByStyleNumber($pos_purchase_order_id);	
	$html_table = createStaticArrayHTMLTable($table_def, $purchase_order_unique_products);
	$html_table .= createHiddenInput('pos_purchase_order_id', $pos_purchase_order_id);
	$html = '<h4>This will update the style numbers on the purchase order form AND for the Corresponding Product. You risk having unmatched style numbers between other purchase orders and the products that are going to be updated. I don\'t see this as a big deal.</h4>';
	$html .= createFormForMYSQLInsert($table_def, $html_table, $form_handler, $complete_location, $cancel_location);

	include (HEADER_FILE);
	echo $html;
	include (FOOTER_FILE);
}
else
{
	include (HEADER_FILE);
	echo 'error - not a valid ID';
	include (FOOTER_FILE);
}
function createAdjustStyleNumberTableDef($pos_purchase_order_id)
{
	$array_table_def= array(	
					array(
							'th' => 'Row',
							'db_field' => 'row_number',
							'type' => 'row_number'),
					array(
							'th' => 'Product ID',
							'db_field' => 'pos_product_id',
							'type' => 'link',
							'get_url_link' => POS_ENGINE_URL . '/products/ViewProduct/view_product.php',
							'get_id_link' => 'pos_product_id'
							),
					array(
							'th' => 'Title',
							'db_field' => 'title',
							'type' => 'td'),
					array(
							'th' => 'Style Number',
							'db_field' => 'style_number',
							'type' => 'input',
							'tags' => ' style="background-color:yellow" ')
					
					);
	return $array_table_def;
}


?>
