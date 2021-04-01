<?php
/*

*/
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
$page_title = 'Generate Product IDs and Subids From PO';
require_once ('../po_functions.php');

$pos_purchase_order_id =  getPostOrGetID('pos_purchase_order_id');
$complete_location = '../ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id='.$pos_purchase_order_id;

//$html = writeProductsandSubProductsFromPOC($pos_purchase_order_id);
//$pos_purchase_order_id =  getPostOrGetID('pos_purchase_order_id');
$html = writeProductsandSubProductsFromPOC($pos_purchase_order_id);
if(getPurchaseOrderStatus($pos_purchase_order_id)=='INIT' || getPurchaseOrderStatus($pos_purchase_order_id)=='DRAFT' )
{
	setPOStatus($pos_purchase_order_id, 'PREPARED');
}

header('Location: '.$complete_location);
?>

