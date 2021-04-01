<?php
/* this function will update the product price, cost, title for each POC in a PO */

require_once('../po_functions.php');
require_once(PHP_LIBRARY);
$sql = "SELECT pos_purchase_order_id FROM pos_purchase_orders";
$purchase_orders = getSQL($sql);
for ($i=0;$i<sizeof($purchase_orders);$i++)
{
	pprint($purchase_orders[$i]['pos_purchase_order_id']);
	$result2 = updatePOCCategories($purchase_orders[$i]['pos_purchase_order_id']);
}
echo "complete";

?>