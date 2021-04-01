<?php
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
$page_title = 'Email a Purchase Order';
require_once ('../po_functions.php');

$pos_purchase_order_id = getPostOrGetID('pos_purchase_order_id');
if (checkForValidIDinPOS($pos_purchase_order_id, 'pos_purchase_orders', 'pos_purchase_order_id'))
{
	
	$to = $_POST['to'];
	$cc = $_POST['cc'];
	$from = getUserEmail(getLoggedInUserId());
	if (isset($_POST['submit']))
	{
	}
	else if (isset($_POST['submit_to_rep']))
	{
		//$to =  getSalesRepEmailFromPO($pos_purchase_order_id);
	}
	elseif (isset($_POST['submit_to_self']))
	{
		$to =  getUserEmail($_SESSION['pos_user_id']);
	}
	else if(isset($_POST['cancel']))
	{
		$message = urlencode("Cancel Email");
		header('Location: ../ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id='.$pos_purchase_order_id.'&message=' . $message);
		exit();	
	}
	else
	{
	}

	

	$html = '';
	if (scrubInput($_POST['comments']) != '')
	{
		$html .= '<p>' .nl2br($_POST['comments']) .'</p>';
	}
	$html .= createHTMLBIGPO($pos_purchase_order_id);
	$html .= createHTMLEmailPOCStatus($pos_purchase_order_id);
	
	$subject = $_POST['subject'];
	//email_po_update($to, $from, $cc,$subject, $html);
	$msg = $html;
	switfMailIt($from, $to, $subject,  $msg, $cc);

	$log = 'Update Sent To: ' . $to . ' From: '  . $from . ' Subject: ' .scrubInput($subject) . newline();
	$log .= '<p>' .nl2br($_POST['comments']) .'</p>';
	$new_log = updatePOLog($pos_purchase_order_id, $log);
	
	$message = urlencode('Update Emailed');
	header('Location: ../ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id='.$pos_purchase_order_id.'&message=' . $message);	
}
else
{
	//no valid  id
	include (HEADER_FILE);
	echo '<p>error - not a valid ID</p>';
	include (FOOTER_FILE);
}


function email_po_update($to, $from	, $cc, $subject, $msg)
{


//	// Make sure to escape quotes
//	$headers = "From: " . $from . "\r\n";
//	$headers .= "Reply-To: ". $from . "\r\n";
//	$headers .= "Return-Path: " . "\r\n";
//	if ($cc !='') $headers .= "CC: " . $cc . "\r\n";
//	$headers  .= 'MIME-Version: 1.0' . "\r\n";
//
//	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
//	$headers .= getSetting('company_name') . ' Order Communication Request From ' . $from . ' <' . $from . '>' . "\r\n";

//	mail($to, $subject, $msg, $headers);











}



?> 