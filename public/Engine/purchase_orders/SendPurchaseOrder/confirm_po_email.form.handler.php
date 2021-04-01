<?php
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
$page_title = 'Email a Purchase Order';
require_once ('../po_functions.php');

$pos_purchase_order_id = getPostOrGetID('pos_purchase_order_id');
if (checkForValidIDinPOS($pos_purchase_order_id, 'pos_purchase_orders', 'pos_purchase_order_id'))
{

	$from = getUserEmail(getLoggedInUserId());
	$cc = $_POST['cc'];
	if (isset($_POST['submit_to_rep']))
	{
		$to =  getSalesRepEmailFromPO($pos_purchase_order_id);
		if(!LIVE)
		{
			$to = ADMIN_EMAIL;
		}
	}
	elseif (isset($_POST['submit_to_self']))
	{
		$to =  getUserEmail($_SESSION['pos_user_id']);
	}
	else if (isset($_POST['cancel']))
	{
		$message = urlencode("Cancel Email");
		header('Location: ../ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id='.$pos_purchase_order_id.'&message=' . $message);
		exit();	
	}
	else
	{}
	
	
	
	$html = '';
	if (scrubInput($_POST['comments']) != '')
	{
		$html .= '<p>' .nl2br($_POST['comments']) .'</p>';
	}
	$html .=  createHTMLBIGPO($pos_purchase_order_id);
	$html .=  createHTMLEmailPOC($pos_purchase_order_id);
	$html .= getSetting('po_email_footer');
	
	
	$subject = $_POST['subject'];
	//email_po($to, $from, $cc, $subject, $html);
	$msg = $html;
	switfMailIt($from, $to, $subject, $msg, $cc);
	if (isset($_POST['submit_to_rep']))
	{
		setPOStatus($pos_purchase_order_id, 'OPEN');
		setOrderStatus($pos_purchase_order_id, 'EMAILED');
		$date = date("Y-m-d H:i:s");
		setPurchaseOrderPlacedDate($pos_purchase_order_id, $date);
		$log = 'PO Sent To: ' . $to . ' From: ' . $from . ' ' . getUserFullName($_SESSION['pos_user_id']) . ' Subject: ' .$subject . newline();
		$log .= '<p>' .nl2br($_POST['comments']) .'</p>';
		//$log .= 'Message: ' .$html;
		$new_log = updatePOLog($pos_purchase_order_id, $log);
	}
	elseif (isset($_POST['submit_to_self']))
	{
		
	}
	
	
	
	$po_status = tryToClosePO($pos_purchase_order_id);
	$ordered_status = getPurchaseOrderOrderedStatus($pos_purchase_order_id);
	$message = urlencode("Ordered Status: " . $ordered_status . ", PO Status: " .$po_status);
	header('Location: ../ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id='.$pos_purchase_order_id.'&message=' . $message);	
}
else
{
	//no valid  id
	include (HEADER_FILE);
	echo '<p>error - not a valid ID</p>';
	include (FOOTER_FILE);
}


function email_po($to, $from, $cc, $subject, $msg)
{

	//mail(SUPPORT_EMAIL, $subject, $message, 'From: ' . SUPPORT_EMAIL);

//	// Make sure to escape quotes
//	$headers = "From: " . $from . "\r\n";
//	$headers .= "Reply-To: ". $from . "\r\n";
//	if ($cc != '') $headers .= "CC: " . $cc . "\r\n";
//	$headers  .= 'MIME-Version: 1.0' . "\r\n";
//
//	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
//
//	mail($to, $subject, $msg, $headers);

}
?> 