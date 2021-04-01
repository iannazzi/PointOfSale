<?php
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
$page_title = 'Draft Purchase Order';
require_once ('../po_functions.php');
$pos_purchase_order_id = getPostOrGetID('pos_purchase_order_id');
if (checkForValidIDinPOS($pos_purchase_order_id, 'pos_purchase_orders', 'pos_purchase_order_id'))
{

	setPOStatus($pos_purchase_order_id, 'DRAFT');
	setOrderStatus($pos_purchase_order_id, 'NOT SUBMITTED');
	//$po_status = tryToClosePO($pos_purchase_order_id);
	//$ordered_status = getPurchaseOrderOrderedStatus($pos_purchase_order_id);
	$message = urlencode("Set Back To Draft - Warning any receive data can now be lost or overwritten");
	header('Location: ../ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id='.$pos_purchase_order_id.'&message=' . $message);	
}
else
{
	//no valid  id
	include (HEADER_FILE);
	echo '<p>error - not a valid ID</p>';
	include (FOOTER_FILE);
}