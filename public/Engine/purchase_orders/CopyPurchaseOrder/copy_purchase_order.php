<?php
/*
*/
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
$page_title = 'Copy a Purchase Order';
require_once('../po_functions.php');
require_once(PHP_LIBRARY);

$db_table = 'pos_purchase_orders';
$key_val_id['pos_purchase_order_id'] = getPostOrGetID('pos_purchase_order_id');
$pos_purchase_order_id = $key_val_id['pos_purchase_order_id'];
if (checkForValidIdInPOS($pos_purchase_order_id,'pos_purchase_orders',  'pos_purchase_order_id'))
{
	$po_data = getAllPurchaseOrderData($pos_purchase_order_id);
	$poc_data = getPurchaseOrderContentsInArray($pos_purchase_order_id);
	
	$dbc = startTransaction();
	//rebuild the po
	$po_insert = array('pos_manufacturer_brand_id' => $po_data[0]['pos_manufacturer_brand_id'],
					'pos_category_id' => $po_data[0]['pos_category_id'],
					'pos_store_id' => $po_data[0]['pos_store_id'],
					'comments' => scrubInput($po_data[0]['comments']),
					'pos_user_id' => $_SESSION['pos_user_id'],
					'create_date' => date('Y-m-d H:i:s'),
					'purchase_order_type' => 'ORDER',
					'stored_size_chart' => $po_data[0]['stored_size_chart'],
					'po_title' =>  scrubInput($po_data[0]['po_title']),
					'purchase_order_status' => 'PREPARED');
	$new_pos_purchase_order_id = simpleTransactionInsertSQLReturnID($dbc,'pos_purchase_orders', $po_insert);
	for($i=0;$i<sizeof($poc_data);$i++)
	{
		$poc_insert = array('pos_purchase_order_id' => $new_pos_purchase_order_id,
						'poc_row_number' => $poc_data[$i]['poc_row_number'],
						'size_row'=> $poc_data[$i]['size_row'],
						'size_column'=> $poc_data[$i]['size_column'],
						'style_number'=> $poc_data[$i]['style_number'],
						'style_number_source'=> $poc_data[$i]['style_number_source'],
						'color_code'=> scrubInput($poc_data[$i]['color_code']),
						'color_description'=> scrubInput($poc_data[$i]['color_description']),
						'title'=> scrubInput($poc_data[$i]['title']),
						'pos_category_id'=> $poc_data[$i]['pos_category_id'],
						'cup'=> scrubInput($poc_data[$i]['cup']),
						'inseam'=> scrubInput($poc_data[$i]['inseam']),
						'size'=> scrubInput($poc_data[$i]['size']),
						'cost'=> scrubInput($poc_data[$i]['cost']),
						'retail'=> scrubInput($poc_data[$i]['retail']),
						'pos_product_sub_id'=> $poc_data[$i]['pos_product_sub_id'],
						'quantity_ordered'=> $poc_data[$i]['quantity_ordered'],
						'comments'=> scrubInput($poc_data[$i]['comments']));
		$new_pos_purchase_order_content_id[$i] = simpleTransactionInsertSQLReturnID($dbc,'pos_purchase_order_contents', $poc_insert);				
						
	}
	simpleCommitTransaction($dbc);
	$complete_location = '../EditPurchaseOrder/edit_purchase_order.php?pos_purchase_order_id='.$new_pos_purchase_order_id;
	header('Location: '.$complete_location);			
}
else //no valid mfg ID
{
	//no valid manufacturer id
	//Header
	include (HEADER_FILE);
	echo 'error - no valid ID';
	include (FOOTER_FILE);
}
?>
