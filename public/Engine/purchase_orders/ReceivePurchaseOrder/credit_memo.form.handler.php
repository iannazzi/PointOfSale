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

	if (isset($_POST['credit_memo_required']))
	{
		$update['credit_memo_required'] =1;
	}
	else
	{
		$update['credit_memo_required'] =0;
	}
	$result[] = updateCreditMemoNumber($pos_purchase_order_id, $_POST['credit_memo']);
	$po_status = tryToClosePO($pos_purchase_order_id);
						$status = tryToCompletePurchaseOrderInvoiceStatus($pos_purchase_order_id);

	$purchase_order_status = getPurchaseOrderStatus($pos_purchase_order_id);
	updatePOLog($pos_purchase_order_id, 'Credit Memo Added');
	$message = urlencode("PO Status: " .$purchase_order_status);
	header('Location: '.$complete_location .'&message=' . $message);
}
else
{
	header('Location: '.$complete_location);
}


?>