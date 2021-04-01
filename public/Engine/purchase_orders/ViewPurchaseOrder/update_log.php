<?php  
/*
To test:
	www.craigiannazzi.com/POS_TEST/Engine/purchase_orders/ViewPurchaseOrder/update_log.php?pos_purchase_order_id=338&log=hellop
*/

$page_level = 5;
$page_navigation = 'purchase_order';
$page_title = 'load style number';
require_once ('../../../Config/config.inc.php');
require_once(PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);
require_once ('../po_functions.php');
if ( (isset($_POST['pos_purchase_order_id'])) && (isset($_POST['log'])) ) 
{
	$log = $_POST['log'];
	$pos_purchase_order_id = scrubInput($_POST['pos_purchase_order_id']);
}
elseif ( (isset($_GET['pos_purchase_order_id'])) && (isset($_GET['log'])) ) 
{
	$log = $_GET['log'];
	$pos_purchase_order_id = scrubInput($_GET['pos_purchase_order_id']);
}
else
{ 
	echo 'Error, no manufacturer_id AND upc supplied';
	exit();
}
	
	echo updatePOLog($pos_purchase_order_id, $log);



?>
