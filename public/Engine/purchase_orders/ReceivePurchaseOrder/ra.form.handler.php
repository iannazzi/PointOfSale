<?PHP
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
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
	$key_val_id['pos_purchase_order_id'] = $pos_purchase_order_id;
	$current_ra_numbers = ($_POST['edit'] = 'edit') ? '' : getRANumber($pos_purchase_order_id);
	if (isset($_POST['ra_required']))
	{
		$update['ra_required'] =1;
	}
	else
	{
		$update['ra_required'] =0;
	}
	if (isset($_POST['credit_memo_required']))
	{
		$update['credit_memo_required'] =1;
	}
	else
	{
		$update['credit_memo_required'] =0;
	}
	if($current_ra_numbers == '')
	{
		$update['ra_number'] = scrubInput($_POST['ra_number']);
	}
	else
	{
		$update['ra_number'] = scrubInput($current_ra_numbers .';' . $_POST['ra_number']);
	}
	$result[] = simpleUpdateSQL('pos_purchase_orders', $key_val_id, $update);
	header('Location: '.$complete_location);
	$po_status = tryToClosePO($pos_purchase_order_id);
	$status = tryToCompletePurchaseOrderInvoiceStatus($pos_purchase_order_id);

	$purchase_order_status = getPurchaseOrderStatus($pos_purchase_order_id);
	updatePOLog($pos_purchase_order_id, 'RA Added');
	$message = urlencode("PO Status: " .$purchase_order_status);
	header('Location: '.$complete_location .'&message=' . $message);

}
else
{
	header('Location: '.$complete_location);
}


?>