<?php
/* this function will update the product price, cost, title for each POC in a PO */
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
require_once('../po_functions.php');
require_once(PHP_LIBRARY);
$pos_purchase_order_id =  getPostOrGetID('pos_purchase_order_id');
$page_title = 'Reload Products';

if(checkForProductSubIds($pos_purchase_order_id))
{

	$result = updatePOCRetailPrice($pos_purchase_order_id);
	$result2 = updatePOCCategories($pos_purchase_order_id);
	$result3 = updatePOCTitles($pos_purchase_order_id);
	$result3 = updatePOCStyleNumbers($pos_purchase_order_id);
	$result3 = updatePOCColorCodes($pos_purchase_order_id);
	$result3 = updatePOCColorDescriptions($pos_purchase_order_id);
	//sizes?
	$complete_location = '../ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id='.$pos_purchase_order_id;
	header('Location: '.$complete_location);	
}
else
{
	$message = 'message=' . urlencode('Products are not linked to Purchase Order - Try to re-generate');
	$complete_location = '../CreatePurchaseOrder/regenerate_product_and_sub_ids.php?pos_purchase_order_id='.$pos_purchase_order_id;
	header('Location: '. addGetToURL($complete_location,$message));	
}

?>