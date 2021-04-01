<?php
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
/* this function will update the product price, cost, title for each POC in a PO */
$page_title = 'Process Purchase Order';
require_once('../po_functions.php');
require_once(PHP_LIBRARY);

$pos_purchase_order_id =  getPostOrGetID('pos_purchase_order_id');
$products = getUPCFromPOCFromPOC($pos_purchase_order_id);
$html = '<p>The Following Product Sub ID\'s were generated from the Purchase Order. If UPC data is missing it means one of two things: the UPC file is not loaded or out of date, or a product is ordered incorrectly. For example a size may have been ordered that the manufacturer does not make.</p><p></p>'.newline(); 
$html .= createHTMLTableFromMYSQLReturnArray($products, 'linedTable');
$html .= '<form id = "process" name="print_label_form" action="reload_upc_data.form.handler.php" method="post" >';
$html.= createHiddenInput('pos_purchase_order_id', $pos_purchase_order_id);
$html .= '<input class = "button" type="submit" name="submit" value="Update"/>';
$html .= '<input class = "button" type="submit" name="cancel" style = "width:200px" value="Do Not Process (Exit)"/></p>';
$html .='</form>';

include (HEADER_FILE);	
echo $html;
include (FOOTER_FILE);
?>