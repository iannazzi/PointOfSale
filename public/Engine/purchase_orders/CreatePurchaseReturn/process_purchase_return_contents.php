<?php
/* this function will update the product price, cost, title for each POC in a PO */
$page_title = 'Process Purchase Order';
require_once('../po_functions.php');
require_once(PHP_LIBRARY);

$pos_purchase_order_id =  getPostOrGetID('pos_purchase_order_id');
$continue_location = 'generate_product_and_sub_ids.php?pos_purchase_order_id='.$pos_purchase_order_id;
$cancel_location = '../ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id='.$pos_purchase_order_id;

//first need to view the contents, and the mfg barcodes. Then we need a edit poc button and a continue button....	
$html = saveDraftOrder($pos_purchase_order_id);
$future_products = getFutureProductsAndProductSubIdsFromPOC($pos_purchase_order_id);
//$products = getProductsandSubProductsFromPOC($pos_purchase_order_id);
$html = '<p>The Following Product Sub ID\'s were generated from the Purchase Order. If UPC data is missing it means one of two things: the UPC file is not loaded or out of date, or a product is ordered incorrectly. For example a size may have been ordered that the manufacturer does not make. Use the Edit button to fix the purchase order contents before creating the prepared order.</p><p></p>'.newline(); 
$html .= createHTMLTableFromMYSQLReturnArray($future_products, 'linedTable');
$html .= '<p><INPUT class = "button" type="button" style = "width:180px" value="Edit Contents" onclick="window.location = \'purchase_order_contents.php?pos_purchase_order_id=' .$pos_purchase_order_id. '\'" />';
$html .= '<INPUT class = "button" type="button" style = "width:100px" value="Continue" onclick="window.location =\''.$continue_location . '\'" />';
$html .= '<INPUT class = "button" type="button" style = "width:180px" value="Do Not Process (Exit)" onclick="window.location =\''.$cancel_location . '\'" /></p>';


include (HEADER_FILE);	
echo $html;
include (FOOTER_FILE);
?>