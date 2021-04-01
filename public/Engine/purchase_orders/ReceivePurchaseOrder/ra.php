<?PHP
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
require_once ('../../../Config/config.inc.php');
require_once(PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);
require_once ('../po_functions.php');
$pos_purchase_order_id =  getPostOrGetID('pos_purchase_order_id');
$page_title = 'RA for PO# ' .$pos_purchase_order_id;
$edit = getPostOrGetDataIfAvailable('edit');
if ( $edit != 'false')
{
	$current_ra_numbers = getRANumber($pos_purchase_order_id);
	$credit_checked = (getPOCreditMemoRequired($pos_purchase_order_id) == 1) ? ' checked = "checked" ' : '';
	$ra_checked = (getPORARequest($pos_purchase_order_id) == 1) ? ' checked = "checked" ' : '';

}
else
{
	$current_ra_numbers = '';
	$credit_checked = 'checked = "checked"';
	
	$ra_checked = '';
}
$html = '<form id = "ra" name="ra" action="ra.form.handler.php" method="post" >';
$html .= 'Return Authorization Number (you can separate multiple RA#\'s using ;): <INPUT TYPE="TEXT" class="lined_input"  id="ra_number" style = "width:200px;" value="'.$current_ra_numbers.'" NAME="ra_number"/><br>';
$html .= '<input type="checkbox" name="ra_required" value="ra_required" '.$ra_checked.'>Check if an RA is still required<br>';
$html .= '<input type="checkbox" name="credit_memo_required" value="credit_memo_required" '.$credit_checked.'>Check if Credit Memo is required<br>';
$html.= createHiddenInput('pos_purchase_order_id', $pos_purchase_order_id);
$html.= createHiddenInput('edit', $edit);
$html .= '<p><input class = "button" type="submit" name="submit" value="Submit" />';
$html .= '<input class = "button" type="submit" name="cancel" value="Cancel" /></p>';
$html .='</form>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);

?>