<?php
/* Mysql delete functions
*/
require_once(PHP_LIBRARY);
function deletePurchaseOrderContents($pos_purchase_order_id)
{
	$sql = "DELETE FROM pos_purchase_order_contents WHERE pos_purchase_order_id = '$pos_purchase_order_id'";
	runSQL($sql);
}
function deletePurchaseOrder($pos_purchase_order_id)
{
	deletePurchaseOrderContents($pos_purchase_order_id);
	$sql = "DELETE FROM pos_purchase_orders WHERE pos_purchase_order_id = '$pos_purchase_order_id'";
	runSQL($sql);
}
function deleteMessage($pos_message_id)
{
	$sql = "DELETE FROM pos_messages WHERE pos_message_id = '$pos_message_id'";
	return runSQL($sql);
}
function mysqlDelete($table, $key_val_id)
{
	$sql = "DELETE FROM ".$table." WHERE ".key($key_val_id)." = '".$key_val_id[key($key_val_id)]."'";
	return runSQL($sql);
}
