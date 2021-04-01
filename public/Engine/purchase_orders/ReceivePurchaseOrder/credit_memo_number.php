<?PHP
$page_level = 5;
$page_navigation = 'purchase_order';
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
require_once ('../../../Config/config.inc.php');
require_once(PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);
require_once ('../po_functions.php');
$pos_purchase_order_id =  getPostOrGetID('pos_purchase_order_id');
$page_title = 'Credit Memo for PO# ' .$pos_purchase_order_id;

$edit = getPostOrGetDataIfAvailable('edit');
if ( $edit != 'false')
{
	$current_credit_numbers = getCreditMemoNumber($pos_purchase_order_id);
	$credit_checked = (getPOCreditMemoRequired($pos_purchase_order_id) == 1) ? ' checked = "checked" ' : '';

}
else
{
	$current_credit_numbers = '';
	$credit_checked = '';
}

$html = '<form id = "credit_memo" name="credit_memo_required" action="credit_memo.form.handler.php" method="post" >';
$html .= 'Credit Memo Invoice Number (you can separate multiple invoice #\'s using ;): <INPUT TYPE="TEXT" class="lined_input"  id="credit_memo" style = "width:200px;" NAME="credit_memo" value="'.$current_credit_numbers.'"/><br>';
$html .= '<input type="checkbox" name="credit_memo_required" value="credit_memo_required" '.$credit_checked.'>Check if Credit Memo is still required';
$html.= createHiddenInput('pos_purchase_order_id', $pos_purchase_order_id);
$html.= createHiddenInput('edit', $edit);

$html .= '<p><input class = "button" type="submit" name="submit" value="Submit" />';
$html .= '<input class = "button" type="submit" name="cancel" value="Cancel" /></p>';
$html .='</form>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);

?>