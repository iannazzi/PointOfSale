<?php
/*	delete_po.php
	this will remove a po
*/
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
$page_title = 'Delete a purchase Order - Please Fill Out All Fields';

require_once ('../po_functions.php');
require_once (MYSQL_DELETE_FUNCTIONS);

$pos_purchase_order_id = getPostOrGetID('pos_purchase_order_id');
// Show the header
//deletePurchaseOrder($pos_purchase_order_id);
setPurchaseOrderStatusToDELETED($pos_purchase_order_id);
$message = urlencode('Deleted');

header('Location: ../ListPurchaseOrders/list_purchase_orders.php?message='.$message);

?>