<?php
$page_title = 'Email a Purchase Order';
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
require_once ('../po_functions.php');
$pos_purchase_order_id = getPostOrGetID('pos_purchase_order_id');
if (checkForValidIDinPOS($pos_purchase_order_id, 'pos_purchase_orders', 'pos_purchase_order_id'))
{

	
	$from = getUserEmail(getLoggedInUserId());
	
	if (LIVE)
	{		
		$to =  getSalesRepEmailFromPO($pos_purchase_order_id);
		$cc = getSetting('purchase_orders_cc_email');
	}
	else
	{
		//$from = ADMIN_EMAIL;
		
		$to =  getSalesRepEmailFromPO($pos_purchase_order_id);
		$cc = getSetting('purchase_orders_cc_email');
		$to =  ADMIN_EMAIL;
	}
	
	
	$html = '';
	//add a form here for user comments
	$form_handler = 'confirm_po_email.form.handler.php';
	$html = '<form action="' . $form_handler.'" method="post" >';
	
	
	$html .= '<table style = "width:100%;">';
	$html .= '<tr>';
	//$html .= '<td style = "width:5%;">From:</td><td> <input id="from" style = "width:100%;" class="lined_input" name = "from" value="' . $from .'" /></td>';
	$html .= '<td style = "width:5%;">From:</td><td>'. $from .'</td>';
	$html .= '</tr>';
	$html .= '<tr>';
	
	$html .= '<td>To:</td><td> ' . $to .'</td>';
	$html .= '</tr>';
	$html .= '<tr>';
	
	$html .= '<td>CC:</td><td> <input id="cc" name = "cc" style = "width:100%;" class="lined_input" value="' . $cc .'" /></td>';
	$html .= '</tr>';
	$html .= '<tr>';
	
	$html .= '<td>Subject:</td><td> <input id="subject" name = "subject" style = "width:100%;" class="lined_input" value="' .getSetting('default_send_po_email_subject') . ' PO# ' .$pos_purchase_order_id . ' ' . getPONumber($pos_purchase_order_id)  . ' Sent by ' . getUserFullName(getLoggedInUserId()) .'" /></td>';
	$html .= '</tr>';
	$html .= '</table>';
	
	
	$html .= '<p>Include the following note:</p>';
	$html .= '<textarea class="textarea_comments" type ="text" id="comments" name ="comments" >';
	$html .= getSetting('default_send_po_email_subject') . ' PO# ' .$pos_purchase_order_id . ' ' . getPONumber($pos_purchase_order_id)  . ' Sent by ' . getUserFullName(getLoggedInUserId()).newline();
	$html .= getSetting('default_send_po_email_text');
	$html .= '</textarea>';
	
	$html .= createHTMLBIGPO($pos_purchase_order_id);
	$html .=  createHTMLEmailPOC($pos_purchase_order_id);
	$html .= getSetting('po_email_footer');
	
	
	$html .= createHiddenInput('pos_purchase_order_id', $pos_purchase_order_id);
		//$html .= '<p><input class ="button" type="submit" style="width:80px" name="submit" value="Send" />' .newline();
	$html .= '<p><input class ="button" type="submit" style="width:400px" name="submit_to_rep" value="Email PO To: ' . $to . '" />' .newline();
	$html .= '<input class ="button" type="submit" style="width:400px" name="submit_to_self" value="Email PO To: ' . $from . '" />' .newline();
	$html .= '<input class = "button" type="button" name="cancel" value="Cancel"/>';
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