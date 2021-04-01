<?php
/*
	*Craig Iannazzi 8-02-2012
*/
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
require_once ('../po_functions.php');
require_once(PHP_LIBRARY);
$pos_purchase_order_id = getPostOrGetID('pos_purchase_order_id');
if (isset($_POST['submit']))
{
	
	//need to update the contents for the $POST['cost']
	//need to re-query the data? or should this include the data? 
	
	$purchase_order_unique_products = getPurchaseOrderContentsLimitedByStyleNumber($pos_purchase_order_id);	
	for($row=0;$row<sizeof($_POST['cost']);$row++)
	{
		$quantity = $_POST['quantity'][$row];
		$pos_purchase_order_content_id = $_POST['pos_purchase_order_content_id'][$row];
		$sql = "UPDATE pos_purchase_order_contents SET quantity = '$quantity' WHERE pos_purchase_order_content_id = $pos_purchase_order_content_id ";
		$result[] = runSQL($sql);
	}
	
	//now we should try to re-close invoice status....
	$po_status[] = trytoclosepo($pos_purchase_order_id);
	$status = tryToCompletePurchaseOrderInvoiceStatus($pos_purchase_order_id);
	unlock_entry('pos_purchase_orders', array('pos_purchase_order_id' => $pos_purchase_order_id));
	$message = urlencode("Quantity Updated");
	header('Location: '.addGetToUrl($_POST['complete_location'], $message));
}
else
{
	$message = 'Canceled';
			unlock_entry('pos_purchase_orders', array('pos_purchase_order_id' => $pos_purchase_order_id));

	header('Location: '.$_POST['cancel_location'] .'&message=' . $message);
}













?>
