<?php
/*
*
*	update_poc_data_to_server.php
* 	http://www.embrasse-moi.com/POS/purchase_orders/update_poc_data_to_server.php
	http://www.craigIannazzi.com/POS_TEST/purchase_orders/CreatePurchaseOrder/update_poc_data_to_server.php?pos_purchase_order_id=35
*	This file is used to send purchase order content data to the server
*	this is need to restore the purchase order in case of accident, fire, murder
*	we are using this because you can only write a limited number of cookies to the browser, then the session dies. of course
*/
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
// some basic checking...
$page_title = 'update poc data to server';
require_once ('../po_functions.php');
require_once ('../../../Config/config.inc.php');
require_once(PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);

$pos_purchase_order_id =  getPostOrGetID('pos_purchase_order_id');

$html = saveDraftOrder($pos_purchase_order_id);
if(getPurchaseOrderStatus($pos_purchase_order_id)=='INIT' || getPurchaseOrderStatus($pos_purchase_order_id)=='DRAFT' )
{
	setPOStatus($pos_purchase_order_id, 'PREPARED');
}
$html .= writeProductsandSubProductsFromPOC($pos_purchase_order_id);
echo $html;

?>

