<?php
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
$page_title = 'Email a Purchase Order Update';
require_once ('../po_functions.php');

$pos_purchase_order_id = getPostOrGetID('pos_purchase_order_id');
if (checkForValidIDinPOS($pos_purchase_order_id, 'pos_purchase_orders', 'pos_purchase_order_id'))
{
	if (LIVE)
	{		
		$from = getUserEmail(getLoggedInUserId());
		$to =  getSalesRepEmailFromPO($pos_purchase_order_id);
		$cc = getSetting('purchase_orders_cc_email');
	}
	else
	{
		$from = ADMIN_EMAIL;
		$to =  ADMIN_EMAIL;
		$cc = getSetting('purchase_orders_cc_email');
	}
	
	$html = '';
	//add a form here for user comments
	$form_handler = 'email_po_update.form.handler.php';
	$html = '<form action="' . $form_handler.'" method="post" >';
	$html .= '<table style = "width:100%;">';
	$html .= '<tr>';
	$html .= '<td style = "width:5%;">From:</td><td>'.$from .'</td>';
	$html .= '</tr>';
	$html .= '<tr>';
	
	$html .= '<td>To:</td><td> <input id="to" name = "to" style = "width:100%;" class="lined_input" value="' . $to .'" /></td>';
	$html .= '</tr>';
	$html .= '<tr>';
	
	$html .= '<td>CC:</td><td> <input id="cc" name = "cc" style = "width:100%;" class="lined_input" value="' . $cc .'" /></td>';
	$html .= '</tr>';
	$html .= '<tr>';
	$html .= '<td>Subject:</td><td> <input id="subject" name = "subject" style = "width:100%;" class="lined_input" value="' . 'PO# ' .$pos_purchase_order_id . ' ' . getPONumber($pos_purchase_order_id)  . ' ' . getSetting('email_update_subject') . ' Sent by ' . getUserFullName(getLoggedInUserId()) .'" /></td>';
	$html .= '</tr>';
	$html .= '</table>';
	
	$html .= '<p>Include the following note:</p>';
	$html .= '<textarea class="textarea_comments" type ="text" id="comments" name ="comments" >';
	$html .= getSetting('company_name') . ' Order Communication Request From ' . getUserFullName(getLoggedInUserId()) . ' <' . getUserEmail(getLoggedInUserId()) . '>' . "\r\n";;
	$html .= getSetting('po_update_default_message');
	$html .= '</textarea>';
	$html .= createHTMLBIGPO($pos_purchase_order_id);
	$html .= createHTMLEmailPOCStatus($pos_purchase_order_id);
	$html .= createHiddenInput('pos_purchase_order_id', $pos_purchase_order_id);
	$html .= '<p><input class ="button" type="submit" style="width:80px" name="submit" value="Send" />' .newline();
	//$html .= '<p><input class ="button" type="submit" style="width:400px" name="submit_to_rep" value="Email Update To: ' . getSalesRepEmailFromPO($pos_purchase_order_id) . '" />' .newline();
	//$html .= '<input class ="button" type="submit" style="width:400px" name="submit_to_self" value="Email Update To: ' . getUserEmail(getLoggedInUserId()) . '" />' .newline();
	$html .= '<input class = "button" type="submit" name="cancel" value="Cancel"/>';
	$html .= '</p>';
	$html .= '</form>';
	$html .= '<script>document.getElementsByName("comments")[0].focus();</script>';
	
	include(HEADER_FILE);
	echo $html;
	//update Status
	include(FOOTER_FILE);
	
}
else
{
	//no valid  id
	include (HEADER_FILE);
	echo '<p>error - not a valid ID</p>';
	include (FOOTER_FILE);
}

?>