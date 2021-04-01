<?php
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
$page_title = 'Open a Purchase Order';
require_once ('../po_functions.php');
$pos_purchase_order_id = getPostOrGetID('pos_purchase_order_id');
if (checkForValidIDinPOS($pos_purchase_order_id, 'pos_purchase_orders', 'pos_purchase_order_id'))
{

	setPOStatus($pos_purchase_order_id, 'PREPARED');
	$message = 'PO Prepared';
	header('Location: ../ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id='.$pos_purchase_order_id.'&message=' . $message);	
}
else
{
	//no valid  id
	include (HEADER_FILE);
	echo '<p>error - not a valid ID</p>';
	include (FOOTER_FILE);
}