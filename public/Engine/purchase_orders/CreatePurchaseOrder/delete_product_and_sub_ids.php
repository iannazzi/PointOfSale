<?php
/*

*/
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
$page_title = 'Delete Product IDs and Subids From PO';
require_once ('../po_functions.php');
require_once ('../../../Config/config.inc.php');
require_once (PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);

$pos_purchase_order_id =  getPostOrGetID('pos_purchase_order_id');
$complete_location = '../ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id=' . $pos_purchase_order_id;
$cancel_location = $complete_location;

$products = getProductsandSubProductsFromPOC($pos_purchase_order_id);
$html = '<h3> The Following Products will attempt to be Deleted. Product Sub Ids Linked to Inventory, Sales Records, and other purchase orders will not be deleted. This is a good place to be if you made a wrong Size, Color, etc. </h3>';
$html .= createHTMLTableFromMYSQLReturnArray($products, 'linedTable');

$form_handler = "delete_products.form.handler.php";
$html .= '<form action="' . $form_handler.'" method="post" >';
$html .= createHiddenInput('complete_location', $complete_location);
$html .= createHiddenInput('cancel_location', $cancel_location);
$html .= createHiddenInput('pos_purchase_order_id', $pos_purchase_order_id);
$html .= '<p><input class ="button" type="submit" name="submit" value="Delete" />' .newline();
$html .= '<input class = "button" type="submit" name="cancel" value="Cancel"/>';
$html .= '</form>' .newline();

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);

?>

