<?php
/*
*/
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
$page_title = 'Edit a Purchase Order';
require_once('../po_functions.php');
require_once(PHP_LIBRARY);
$db_table = 'pos_purchase_orders';
$key_val_id['pos_purchase_order_id'] = getPostOrGetID('pos_purchase_order_id');
$pos_purchase_order_id = $key_val_id['pos_purchase_order_id'];
$complete_location = '../ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id='.$pos_purchase_order_id;
if (isset($_POST['submit'])) 
{
	
	$update_data = postedTableDefArraytoMysqlUpdateArray($_POST['table_def'], 'pos_purchase_order_id');
	$result = simpleUpdateSQL($db_table, $key_val_id, $update_data);
	//what about logging the changes?
	//$pos_employee = array('pos_user_id' => $_SESSION['pos_user_id']);
	//$data = array_merge($data,$pos_employee);
	header('Location: '.$complete_location);			
}
?>
