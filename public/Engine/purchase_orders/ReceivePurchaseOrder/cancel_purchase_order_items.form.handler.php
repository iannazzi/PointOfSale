<?php
/*
	*cancel the goods - this removes goods off of a purchase order - actually just sets them to canceled
	no money is being exchanged so we do not need transactions
	*Craig Iannazzi 2-14-2012
*/
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
require_once ('../po_functions.php');
require_once(PHP_LIBRARY);
$pos_purchase_order_id = getPostOrGetID('pos_purchase_order_id');
$key_val_id['pos_purchase_order_id'] = $pos_purchase_order_id;
$array_table_def = deserializeTableDef($_POST['table_def']);
$table_data = getArrayOfPostDataUsingTableDef($array_table_def);
if ($_POST['submit']=='Submit')
{

	$date = date('Y:m:d H:i:s');
	$dbc = startTransaction();
	//lock the po contents for update so every one else has to wait....
	$sql = "SELECT * FROM pos_purchase_order_contents WHERE pos_purchase_order_id=$pos_purchase_order_id FOR UPDATE";
	$result[0] = @mysqli_query($dbc,$sql);
	for($row=0;$row<sizeof($table_data);$row++)
	{
		$pos_purchase_order_content_id = $table_data[$row]['pos_purchase_order_content_id'];
		$original_quantity_canceled = getQuantityCanceled($dbc,$pos_purchase_order_content_id);
		$new_quantity_canceled = $table_data[$row]['quantity_canceled'];
		$poc_update_quantity = $original_quantity_canceled+$new_quantity_canceled;
		$poc_update_array[$row]['pos_purchase_order_content_id'] = $pos_purchase_order_content_id;
		$poc_update_array[$row]['quantity_canceled'] = $poc_update_quantity;
	}
	$result[1] = updatePOCQuantityCanceled($dbc, $poc_update_array);
	$result[2] = createRecievedStatus($dbc, $pos_purchase_order_id);
	$result[3] = tryToClosePOTransaction($dbc, $pos_purchase_order_id);

	// now check if the purchase order invoice status can be set to complete - this is where the total invoiced matches the total ordered
	$po_total =  getTotalOrderedFromPurchaseOrderDBC($dbc,$pos_purchase_order_id);
	$invoice_total = getPurchaseOrderInvoiceAmountApplied($pos_purchase_order_id);
	if(abs($po_total - $invoice_total) < 0.00001)
		{
			$po_update_array['invoice_status'] = 'COMPLETE';
		}
		else
		{
			$po_update_array['invoice_status'] = 'INCOMPLETE';
		}
	
	$results[] = simpleTransactionUpdateSQL($dbc,'pos_purchase_orders', $key_val_id, $po_update_array);
	$close_transaction = simpleCommitTransaction($dbc);
	$received_status = getPurchaseOrderReceivedStatus($pos_purchase_order_id);
	$purchase_order_status = getPurchaseOrderStatus($pos_purchase_order_id);
		$status = tryToCompletePurchaseOrderInvoiceStatus($pos_purchase_order_id);

	updatePOLog($pos_purchase_order_id, "Items Canceled");
	
	$message = urlencode("Receive Status: " . $received_status . ", PO Status: " .$purchase_order_status);
	header('Location: '.$_POST['complete_location'] .'&message=' . $message);
}
else
{
	$message = 'Canceled';
	header('Location: '.$_POST['cancel_location'] .'&message=' . $message);
}



?>
