<?php
/*

*/
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
$page_title = 'Process PO';
require_once ('../po_functions.php');
require_once ('../../../Config/config.inc.php');
require_once(PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);
$pos_purchase_order_id =  getPostOrGetID('pos_purchase_order_id');
$complete_location = '../ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id='.$pos_purchase_order_id;
$cancel_location = '../ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id='.$pos_purchase_order_id;
if (isset($_POST['submit']))
{
	writeUPCtoProductFromPOC($pos_purchase_order_id);

	
	header('Location: '.$complete_location);
}
else
{
	header('Location: '.$cancel_location);
}
?>

