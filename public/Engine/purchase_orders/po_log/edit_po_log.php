<?PHP
$page_level = 5;
$page_navigation = 'purchase_order';

require_once ('../../../Config/config.inc.php');
require_once(PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);
require_once ('../po_functions.php');
$pos_purchase_order_id =  getPostOrGetID('pos_purchase_order_id');
$page_title = 'Log for PO# ' .$pos_purchase_order_id;

$html = '<form id = "log" name="log" action="edit_po_log.form.handler.php" method="post" >';
$html .= 'Purchase Order Log<br>';
$html .= '<textarea class="textarea_comments" type ="text" id="po_log" name ="po_log" >';
$html .=  str_replace('<br />',newline(), str_replace('</p>','',str_replace('<p>',newline(), str_replace('<br>',newline(), getPOLOG($pos_purchase_order_id)))));
$html .= '</textarea>';
$html.= createHiddenInput('pos_purchase_order_id', $pos_purchase_order_id);
$html .= '<p><input class = "button" type="submit" name="submit" value="Submit" />';
$html .= '<input class = "button" type="submit" name="cancel" value="Cancel" /></p>';
$html .='</form>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);

?>