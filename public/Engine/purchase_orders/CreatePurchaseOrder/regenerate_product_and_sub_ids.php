<?php
/*

*/
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
$page_title = 'Regenerate Product IDs and Subids From PO';
require_once ('../po_functions.php');

require_once (CHECK_LOGIN_FILE);
include (HEADER_FILE);
$pos_purchase_order_id =  getPostOrGetID('pos_purchase_order_id');
$html = writeProductsandSubProductsFromPOC($pos_purchase_order_id);
$html .= '<p><input class = "button" type="button" name="upcs" value="Return" onclick="open_win(\'../ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id=' . $pos_purchase_order_id .'\')"/>';
echo $html;
include (FOOTER_FILE);

?>

