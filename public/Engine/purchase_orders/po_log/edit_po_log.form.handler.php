<?PHP
$page_level = 5;
$page_navigation = 'purchase_order';
$page_title = 'ra';
require_once ('../../../Config/config.inc.php');
require_once(PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);
require_once ('../po_functions.php');
$pos_purchase_order_id =  getPostOrGetID('pos_purchase_order_id');
$complete_location = '../ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id='.$pos_purchase_order_id;
if (isset($_POST['submit']))
{
	$new_log = scrubInput($_POST['po_log']);
	$update_sql = "UPDATE pos_purchase_orders SET log = '$new_log' WHERE pos_purchase_order_id=$pos_purchase_order_id";
	$result = runSQL($update_sql);
	$message = urlencode("Log Updated");
	header('Location: '.$complete_location .'&message=' . $message);

}
else
{
	header('Location: '.$complete_location);
}


?>