<?PHP
$page_level = 5;
$binder_name = 'Purchase Orders';
$page_navigation = 'purchase_order';

require_once ('../../../Config/config.inc.php');
require_once(PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);
require_once ('../po_functions.php');
$pos_purchase_order_id =  getPostOrGetID('pos_purchase_order_id');
$page_title = 'Labels for PO# ' . $pos_purchase_order_id;
//pdf_hello_world();
//imageDoc();
//NEED A CELL OFFSET
//NEED TO PRINT FULL ORDER OR PARTIAL ORDER
//WOULD LIKE A DISPLAY TABLE OF THE LABELS
//NEED TO PRINT COMMENTS, OR CUSTOMER INFORMATION
//NEED TO SELECT A TEMPLATE....



$html = '<form id = "print_po_labels" name="print_label_form" action="print_po_labels.form.handler.php" method="post" >';

$html .= '<h2>Listen up! Receiving merchandise is an Accounting Event and must be done accurately. The amount received has to match the invoice. Checking in the product received incorrectly therefore makes more work in the future.</h2>';
	$html .= '<div class = "tight_divider">';
$html .= '<p>Currently code allows printing stickers for the whole order.</p><p><b> The fastest method to receive a large shipment is to print all stickers, sticker product, then "Receive Complete" followed by checking the Subtract Quantites checkbox and scanning the stickers that remain on the page. </b> </p>';
$html .= '<p>If we have large errors in receiveing merchandise we will have to remove this awesome functionality and force a manual count, giving you only one sticker per checked in product. Misery. So please be very accurate in receiveing product.</p>';

$html .= '</div>';
	$html .= '<div class = "tight_divider">';
$html .= '<p>Label Starting Row & Column - Use to print onto an incomplete label sheet. Only works for the first page of labels</p>';


$html .= 'Starting column: <INPUT TYPE="TEXT" value="1" class="lined_input"  '. numbersOnly() . ' id="column_offset" style = "width:20px;" NAME="column_offset"/>'.newline();
$html .= 'Starting row: <INPUT TYPE="TEXT" value="1" class="lined_input"  '. numbersOnly() . ' id="row_offset" style = "width:20px;" NAME="row_offset"/>'.newline();
$html .= '<br>';
//$html .= '<input type="radio" class="radio" name="sex" value="all" checked="checked">Print All Labels<br>'.newline();
//$html .= '<input type="radio" class="radio" name="sex" value="received">Print Only Items Received<br>'.newline(); 
$html .= createReceviedDateSelect('receive_date_select', $pos_purchase_order_id).newline();
$html .= '<br>';
$html .= '<input type="checkbox" class="checkbox" checked="checked" name="print_labels" value="print_labels">Print Labels'.newline();
$html .= '<input type="checkbox" class="checkbox" name="print_comments" value="print_comments">Print Comments'.newline();
$html .= '<br>';
$html.= createHiddenInput('pos_purchase_order_id', $pos_purchase_order_id).newline();
$html .= '<p><input class = "button" type="submit" name="submit" value="Open Label File" onclick="needToConfirm=false;"/>'.newline();
$html .= '<input class = "button" type="submit" name="cancel" value="Return" onclick="needToConfirm=false;"/></p>'.newline();

$html .='</form>';
$html .= '</div>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);

function createReceviedDateSelect($name, $pos_purchase_order_id, $option_all='on')
{

	$dates = getSQL("SELECT pos_purchase_order_receive_event_id, receive_date, 
	(SELECT sum(pos_purchase_order_receive_contents.received_quantity)  FROM pos_purchase_order_receive_contents WHERE pos_purchase_order_receive_contents.pos_purchase_order_receive_event_id = pos_purchase_order_receive_event.pos_purchase_order_receive_event_id) as total_quantity_received
	
	 FROM pos_purchase_order_receive_event WHERE pos_purchase_order_id = $pos_purchase_order_id");
	$html = 'Select option: <select style="padding:0px;margin:5px;" id = "'.$name.'" name="'.$name.'" ';
	$html .= '>';
	//Add an option for not selected
	//$html .= '<option value="false">Select Date</option>';
	//add an option for all employees
	$html .= '<option value ="all_ordered"';
		$html .= '>All Ordered </option>';
	if ($option_all != 'off')
	{
		$html .= '<option value ="all_received"';
		$html .= '>All Received </option>';
	}
	for($i = 0;$i < sizeof($dates); $i++)
	{
		$html .= '<option value="' . $dates[$i]['pos_purchase_order_receive_event_id'] . '"';
		
			
		$html .= '>Received On ' . $dates[$i]['receive_date'] . ' Quantity Received: ' . $dates[$i]['total_quantity_received'] .'</option>';
	}
	$html .= '</select>';
	return $html;
}

?>