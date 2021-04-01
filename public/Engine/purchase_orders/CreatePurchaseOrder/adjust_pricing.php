<?php
/*
	*recieve_purchase_order.php
	*Craig Iannazzi 2-14-12
	*This form is used to recieve the purchase order
*/
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
$page_title = 'Adjust Pricing';
require_once ('../po_functions.php');
$pos_purchase_order_id =  getPostOrGetID('pos_purchase_order_id');
$unlock_location = POS_ENGINE_URL . '/purchase_orders/CreatePurchaseOrder/adjust_pricing.php?pos_purchase_order_id='.$pos_purchase_order_id;
$complete_location = POS_ENGINE_URL . '/purchase_orders/ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id='.$pos_purchase_order_id;
$cancel_location = $complete_location;
$form_handler = 'adjust_pricing.form.handler.php';
check_lock('pos_purchase_orders', array('pos_purchase_order_id' => $pos_purchase_order_id),$unlock_location, $cancel_location);


if (checkForValidIDinPOS($pos_purchase_order_id, 'pos_purchase_orders', 'pos_purchase_order_id'))
{
	
	lock_entry('pos_purchase_orders', array('pos_purchase_order_id' => $pos_purchase_order_id));
	//for updating the price we just want to display the unique product style numbers and color codes....
	//$html = createMiniPOOverview($pos_purchase_order_id, 'true');
	$table_def = createAdjustPricingTableDef($pos_purchase_order_id);
	$purchase_order_unique_products = getPurchaseOrderContentsLimitedByStyleNumber($pos_purchase_order_id);	
	$html_table = createStaticArrayHTMLTable($table_def, $purchase_order_unique_products);
	$html_table .= createHiddenInput('pos_purchase_order_id', $pos_purchase_order_id);
	$html = createFormWithNoCancelJavascript($table_def, $html_table, $form_handler, $complete_location, $cancel_location);
	$html.='Notice: I am updating the cost for the purchase order only - I will not automatically update your product cost as well. Product cost can be updated upon completing a purchase order if the cost on the order is different than the product cost, or in the product itself. The issue with automatically updating product cost is that if you are working with an older order where the cost on the order truly is different than a new order, then the old cost ordering error will create a new product cost inaccuracy, and then new products ordered will pull from the product which has the cost set wrong. The correct action is to order with the right cost. If you have the wrong cost, then you will need to fix both the PO and the product. ';
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
function createAdjustPricingTableDef($pos_purchase_order_id)
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
							'th' => 'Style Number',
							'db_field' => 'style_number',
							'type' => 'td'),
					array(
							'th' => 'Title',
							'db_field' => 'title',
							'type' => 'td'),

					array(	'th' => 'Cost',
							'db_field' => 'cost',
							'type' => 'input',
							'tags' => ' style="background-color:yellow" ',
							'round' => 2)
					);
	return $array_table_def;
}


?>
