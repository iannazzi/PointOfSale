<?php
/*
	we need to be able to reduce quantities and add items
	we cannot delete items however
*/

$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
$page_title = 'Adjust Pricing';
require_once ('../po_functions.php');
$pos_purchase_order_id =  getPostOrGetID('pos_purchase_order_id');
$unlock_location = POS_ENGINE_URL . '/purchase_orders/CreatePurchaseOrder/adjust_pricing.php?pos_purchase_order_id='.$pos_purchase_order_id;
$complete_location = POS_ENGINE_URL . '/purchase_orders/ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id='.$pos_purchase_order_id;
$cancel_location = $complete_location;
$form_handler = 'adjust_quantities.form.handler.php';
check_lock('pos_purchase_orders', array('pos_purchase_order_id' => $pos_purchase_order_id),$unlock_location, $cancel_location);


if (checkForValidIDinPOS($pos_purchase_order_id, 'pos_purchase_orders', 'pos_purchase_order_id'))
{
	
	lock_entry('pos_purchase_orders', array('pos_purchase_order_id' => $pos_purchase_order_id));
	//for updating the price we just want to display the unique product style numbers and color codes....
	$html = createMiniPOOverview($pos_purchase_order_id, 'true');
	$table_def = createAdjustQuantityTableDef($pos_purchase_order_id);
	$purchase_order_unique_products = getPurchaseOrderContentsLimitedByStyleNumber($pos_purchase_order_id);	
	$html_table = createStaticArrayHTMLTable($table_def, $purchase_order_unique_products);
	$html_table .= createHiddenInput('pos_purchase_order_id', $pos_purchase_order_id);
	$html .= createFormWithNoCancelJavascript($table_def, $html_table, $form_handler, $complete_location, $cancel_location);

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
function createAdjustQuantityTableDef($pos_purchase_order_id)
{
	$array_table_def= array(	
					array(
							'th' => 'pos_purchase_order_content_id',
							'db_field' => 'pos_purchase_order_content_id',
							'type' => 'td_hidden_input'),
					array(
							'th' => 'Style Number',
							'db_field' => 'style_number',
							'type' => 'td'),
					array(
							'th' => 'Title',
							'db_field' => 'title',
							'type' => 'td'),
					array(
							'th' => 'Color Description',
							'db_field' => 'color_description',
							'type' => 'td'),
					array(
							'th' => 'Color Code',
							'db_field' => 'color_code',
							'type' => 'td'),
					array(
							'th' => 'Size',
							'db_field' => 'size',
							'type' => 'td'),

					array(	'th' => 'Quantity',
							'db_field' => 'quantity',
							'type' => 'input',
							'tags' => ' style="background-color:yellow" ')
					);
	return $array_table_def;
}