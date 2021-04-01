<?php
/*
	*purchase_order_receive_form.handler.php
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
	// We need to update received, the inventory, and the general ledger as a transaction
	// if one fails then they will all fail
	// need to use FOR UPDATE in sql statement to lock things down.....

	$dbc = startTransaction();
	$counter = 0;
	//lock the po contents for update so every one else has to wait....
	$sql = "SELECT * FROM pos_purchase_order_contents WHERE pos_purchase_order_id=$pos_purchase_order_id FOR UPDATE";
	$result[$counter] = @mysqli_query($dbc,$sql);
	$counter++;
	$unscrubbed_log = '';
	$total_quantity_returning = 0;
	$value_returning = 0;
	for($row=0;$row<sizeof($table_data);$row++)
	{
		$pos_purchase_order_content_id = $table_data[$row]['pos_purchase_order_content_id'];
		$original_quantity_returning = getQuantityReturn($dbc, $pos_purchase_order_content_id);
		$new_quantity_returning = $table_data[$row]['quantity_returning'];
		$poc_update_quantity = $original_quantity_returning+$new_quantity_returning;
		$value_returning = $value_returning + getTransactionPOCCost($dbc, $pos_purchase_order_content_id)*$new_quantity_returning;
		$inventory_array[$row]['pos_purchase_order_content_id'] = $pos_purchase_order_content_id;
		$inventory_array[$row]['available_qty'] = -$new_quantity_returning;
		$poc_update_array[$row]['pos_purchase_order_content_id'] = $pos_purchase_order_content_id;
		$poc_update_array[$row]['quantity_returning'] = $poc_update_quantity;
		if($new_quantity_returning != 0)
		{
			$unscrubbed_log .= 'Returning: ' . getProductSubIDFROMPOC($pos_purchase_order_content_id) .'<br>' .newline();
		}
	}
	
	if (isset($_POST['ra_required']))
	{
		$po_update_data['ra_required'] =1;
		$result[] = simpleTransactionUpdateSQL($dbc,'pos_purchase_orders', $key_val_id, $po_update_data);
	}
	
	$result[] = updatePOCQuantityReturned($dbc,$poc_update_array);

	/*$hmmmm = updateInventoryLogNewInventoryReceived($dbc,$inventory_array);
	if ($hmmmm)
	{
	}*/
	
	

	$close_transaction = commitTransaction($dbc, $result);	
	if ($close_transaction)
	{
		$po_status = tryToClosePO($pos_purchase_order_id);
		$received_status = getPurchaseOrderReceivedStatus($pos_purchase_order_id);
		$purchase_order_status = getPurchaseOrderStatus($pos_purchase_order_id);
		updatePOLog($pos_purchase_order_id, $unscrubbed_log);
		$message = urlencode("Receive Status: " . $received_status . ", PO Status: " .$purchase_order_status);
	}
	else
	{
		$message = urlencode("Transaction Failed, Merchandise Was Not Received!");
	}
	header('Location: '.$_POST['complete_location'] .'&message=' . $message);
}
else
{
	$message = 'Canceled';
	header('Location: '.$_POST['cancel_location'] .'&message=' . $message);
}




?>
