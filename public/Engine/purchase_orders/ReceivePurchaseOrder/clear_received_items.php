<?php
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';

$page_title = 'Clear Received Contents';
require_once ('../po_functions.php');
$pos_purchase_order_id =  getPostOrGetID('pos_purchase_order_id');
$key_val_id['pos_purchase_order_id'] = $pos_purchase_order_id;
$complete_location = '../ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id='.$pos_purchase_order_id;
$cancel_location = $complete_location;
$form_handler = 'clear_received_items.php';

if (checkForValidIDinPOS($pos_purchase_order_id, 'pos_purchase_orders', 'pos_purchase_order_id'))
{
	if (isset($_POST['submit']) && $_POST['submit']=='Clear Items Received')
	{
		$dbc=startTransaction();
		
		//lets see, delete the missing items and wrong items from the po
		$po_update_data['wrong_items_qty'] =0;
		$po_update_data['purchase_order_status'] ='DRAFT';
		$po_update_data['ordered_status'] ='REVISED';
		$po_update_data['received_status'] ='';
		$result[] = simpleTransactionUpdateSQL($dbc, 'pos_purchase_orders', $key_val_id, $po_update_data);
		$sql = "UPDATE pos_purchase_order_contents SET quantity_canceled = 0 WHERE pos_purchase_order_id = $pos_purchase_order_id";
			runTransactionSQL($dbc,$sql);
		//get the po contents
		$purchase_order_contents = getPurchaseOrderContentsInArray($pos_purchase_order_id);
		$sql = "DELETE FROM pos_purchase_order_receive_event WHERE pos_purchase_order_id = $pos_purchase_order_id";
		runTransactionSQL($dbc,$sql);
		for($i=0;$i<sizeof($purchase_order_contents);$i++)
		{
			$pos_purchase_order_content_id = $purchase_order_contents[$i]['pos_purchase_order_content_id']; 
			$sql = "DELETE FROM pos_purchase_order_receive_contents WHERE pos_purchase_order_content_id = $pos_purchase_order_content_id";
			runTransactionSQL($dbc,$sql);
			
			
			
		
		}


		
		$close_transaction = commitTransaction($dbc, $result);
		if ($close_transaction)
		{
			$message = 'Cleared Received Items, Cleaned Inventory Log, Set PO Status to DRAFT, and Set Submitted to REVISED';
			header('Location: '.$complete_location .'&message=' . $message);
			exit();
		}
	}
	elseif(isset($_POST['cancel']) && $_POST['cancel']=='Cancel')
	{
		$message = 'Canceled';
		header('Location: '.$complete_location .'&message=' . $message);
		exit();
	}
	
	//show the form
	$html = '<p class = error> This will back out and remove inventory received, back out and remove all damaged items counted, remove all items canceled, remove incorrect items removed, Set the PO to prepared so the PO contents can be edited. You will have to re-check in this order.	Continue? </p>';
	$html .= '<form id = "clear_received" name="search_form" action="'.$form_handler.'" method="POST">';
	$html .= '<p><input class = "button" style="Width:250px" type="submit" name="submit" value="Clear Items Received" />';
	$html .= '<input class = "button" type="submit" name="cancel" value="Cancel" /></p>';
	$html .= createHiddenInput('pos_purchase_order_id', $pos_purchase_order_id);
	$html .= '</form>';
	INCLUDE(HEADER_FILE);
	echo $html;
	include(FOOTER_FILE);
}
function updatePOCQuantityReceived($dbc,$poc_array)
{
	/*	ex:
	$mysql_data_array[0]['db_field'] = 'cost';
	$mysql_data_array[0]['id'] = 'pos_purchase_order_content_id';
	$mysql_data_array[0]['data_array']['3789'] = 30.75;
	$mysql_data_array[0]['data_array']['3790'] = 40.75;
	$mysql_data_array[1]['db_field'] = 'retail';
	$mysql_data_array[1]['id'] = 'pos_purchase_order_content_id;
	$mysql_data_array[1]['data_array']['3789'] = 60.75;
	$mysql_data_array[1]['data_array']['3790'] = 80.75;
	*/
	$mysql_data_array[0]['id'] = 'pos_purchase_order_content_id';
	$mysql_data_array[0]['db_field'] = 'quantity_received';
	for($i=0;$i<sizeof($poc_array);$i++)
	{
		$mysql_data_array[0]['data_array'][$poc_array[$i]['pos_purchase_order_content_id']] = $poc_array[$i]['quantity_received'];
	}
	$mysql_data_array[1]['id'] = 'pos_purchase_order_content_id';
	$mysql_data_array[1]['db_field'] = 'received_date_qty';
	for($i=0;$i<sizeof($poc_array);$i++)
	{
		$mysql_data_array[1]['data_array'][$poc_array[$i]['pos_purchase_order_content_id']] = $poc_array[$i]['received_date_qty'];
	}
	$mysql_data_array[2]['id'] = 'pos_purchase_order_content_id';
	$mysql_data_array[2]['db_field'] = 'receive_comments';
	for($i=0;$i<sizeof($poc_array);$i++)
	{
		$mysql_data_array[2]['data_array'][$poc_array[$i]['pos_purchase_order_content_id']] = $poc_array[$i]['receive_comments'];
	}
	
	return runTransactionSQL($dbc,arrayUpdateSQLString('pos_purchase_order_contents', $mysql_data_array));
}
function getQuantityReceived($dbc, $pos_purchase_order_content_id)
{
	$qty_array = getTransactionSQL($dbc, "SELECT quantity_received FROM pos_purchase_order_contents WHERE
							pos_purchase_order_content_id = '$pos_purchase_order_content_id'");
	return $qty_array[0]['quantity_received'];
}
?>