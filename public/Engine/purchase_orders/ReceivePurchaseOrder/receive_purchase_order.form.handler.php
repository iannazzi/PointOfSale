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
	
	$pick_ticket = scrubInput($_POST['pick_ticket']);
	$recieve_event_comments = scrubInput($_POST['receive_event_comments']);
	
	

	$counter = 0;
	$dbc = startTransaction();
	//lock the po contents for update so every one else has to wait....
	$sql = "SELECT * FROM pos_purchase_order_contents WHERE pos_purchase_order_id=$pos_purchase_order_id FOR UPDATE";
	$result[$counter] = @mysqli_query($dbc,$sql);
	$counter++;
	$total_quantity_received = 0;
	$value_received = 0;
	$upc_counter=0;
	$upc_update_array = array();

	$receive_insert['pos_purchase_order_id'] = $pos_purchase_order_id;
	$receive_insert['receive_date'] = $date;
	$receive_insert['pick_ticket'] = $pick_ticket;
	$receive_insert['comments'] = $recieve_event_comments;
	$receive_insert['wrong_items_comments'] =scrubInput($_POST['wrong_items_comments']);
	$receive_insert['pos_user_id'] = $_SESSION['pos_user_id'];
	//to track the receiveed merchandise we need to set a terminal id 
	//$receive_insert['pos_terminal_id'] = $_SESSION['pos_terminal_id'];
	$receive_insert['pos_store_id'] = $_SESSION['store_id'];
	
	$pos_purchase_order_receive_event_id = simpleTransactionInsertSQLReturnID($dbc,'pos_purchase_order_receive_event', $receive_insert);
	for($row=0;$row<sizeof($table_data);$row++)
	{
		$pos_purchase_order_content_id = $table_data[$row]['pos_purchase_order_content_id'];
		$receive_content_insert = array();
		$receive_content_insert['pos_purchase_order_receive_event_id'] = $pos_purchase_order_receive_event_id;
		$receive_content_insert['received_quantity'] = $table_data[$row]['new_received_quantity'];
		$receive_content_insert['receive_comments'] = $table_data[$row]['receive_comments'];
		$receive_content_insert['pos_purchase_order_content_id'] = $pos_purchase_order_content_id;
	$pos_purchase_order_receive_content_id = simpleTransactionInsertSQLReturnID($dbc,'pos_purchase_order_receive_contents', $receive_content_insert);
	
		if (isset($table_data[$row]['product_upc']))
		{
			$upc_update_array[$upc_counter]['product_upc'] = $table_data[$row]['product_upc'];
			$pos_product_sub_id = getTransactionSingleValueSQL($dbc,"SELECT pos_product_sub_id FROM pos_purchase_order_contents WHERE pos_purchase_order_content_id = $pos_purchase_order_content_id");
			
			$upc_update_array[$upc_counter]['pos_product_sub_id'] = $pos_product_sub_id;
			$upc_counter++;

		}
		
	}
	
	
	$po_update_data['wrong_items_qty'] =$_POST['wrong_items_qty'];
	$po_update_data['wrong_items_comments'] =scrubInput($_POST['wrong_items_comments']);
	if (isset($_POST['ra_required']))
	{
		$po_update_data['ra_required'] =1;
	}
	$result[] = simpleTransactionUpdateSQL($dbc,'pos_purchase_orders', $key_val_id, $po_update_data);
	
	if(sizeof($upc_update_array)>0)
	{
		$result[] = updateMFGUPCs($dbc, $upc_update_array);
	}
	$result[] = createRecievedStatus($dbc, $pos_purchase_order_id);
	$close_transaction = commitTransaction($dbc, $result);	
	if ($close_transaction)
	{
		$po_status = tryToClosePO($pos_purchase_order_id);
		$received_status = getPurchaseOrderReceivedStatus($pos_purchase_order_id);
		$purchase_order_status = getPurchaseOrderStatus($pos_purchase_order_id);
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

function updateMFGUPCs($dbc,$upc_array)
{
	$mysql_data_array[0]['id'] = 'pos_product_sub_id';
	$mysql_data_array[0]['db_field'] = 'product_upc';
	for($i=0;$i<sizeof($upc_array);$i++)
	{
		$mysql_data_array[0]['data_array'][$upc_array[$i]['pos_product_sub_id']] = $upc_array[$i]['product_upc'];
	}
	return runTransactionSQL($dbc,arrayUpdateSQLString('pos_products_sub_id', $mysql_data_array));

}











?>
