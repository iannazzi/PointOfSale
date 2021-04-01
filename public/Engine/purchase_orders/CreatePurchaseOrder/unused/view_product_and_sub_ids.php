<?php
/*

*/
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
$page_title = 'View Product IDs and Subids From PO';
require_once ('../po_functions.php');
require_once ('../../../Config/config.inc.php');
require_once(PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);
include (HEADER_FILE);
$pos_purchase_order_id =  getPostOrGetID('pos_purchase_order_id');
$products = getProductsandSubProductsFromPOC($pos_purchase_order_id);
$html = printGetMessage();
$html .= createHTMLTableFromMYSQLReturnArray($products, 'linedTable');
$html .= ajaxHTMLButtonReGenerateProductAndSubIds();
$tags = generatePOPreparedEnableTag($pos_purchase_order_id);
$html .= '<INPUT class = "button" style="width:200px" type="button" ' .$tags.' value="Regenerate Product Ids From PO" onclick="generateProductAndSubIds('.$pos_purchase_order_id.')" />';
$html .= '<INPUT class = "button" style="width:200px" type="button" ' .$tags.' disabled="disabled" value="Delete Products Generated From PO" onclick="open_win(\'delete_product_and_sub_ids.php?pos_purchase_order_id=' . $pos_purchase_order_id .'\')" />';
//$html .= '<INPUT class = "button" style="width:200px" type="button" ' .$tags.' " value="Deactivate Products Generated From PO" onclick="open_win(\'delete_product_and_sub_ids.php?pos_purchase_order_id=' . $pos_purchase_order_id .'\')" />';
$html .= '<input class = "button" type="button" name="upcs" value="Back" onclick="open_win(\'../ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id=' . $pos_purchase_order_id .'\')"/>';
echo $html;
include (FOOTER_FILE);
function ajaxHTMLButtonReGenerateProductAndSubIds()
{
	$html = '<script>function generateProductAndSubIds(pos_purchase_order_id)
	{
		$.get("regenerate_product_and_sub_ids.php",{ pos_purchase_order_id: pos_purchase_order_id } ,
		function(response) 
		{
			window.location.reload();
		});
	}</script>';
	return $html;
}
?>

