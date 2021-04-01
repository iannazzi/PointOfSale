<?php
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
$page_title = 'Email a Purchase Order';
require_once ('../po_functions.php');

$pos_purchase_order_id = getPostOrGetID('pos_purchase_order_id');
$to = urldecode(getPostOrGetValue('email'));
/*
	Email needs to have in-line css, emogrifier tried to convert to inline but could not, needed to do it manually
	for this process.... The Less css the better.....
*/

if (checkForValidIDinPOS($pos_purchase_order_id, 'pos_purchase_orders', 'pos_purchase_order_id'))
{
	$html = '';
	$html = $html . createHTMLBIGPO($pos_purchase_order_id);
	$html = $html . createHTMLEmailPOC($pos_purchase_order_id);
	$html .= '<p><span style="font-size:0.7em;font-color:rgb(128,128,128);">Embrasse-Moi is currently developing software systems for ordering and inventory management. This email is HTML formatted and includes a purchase order table. The table should be neatly viewable in most email programs. The table should be able to be accurately copied into spreadsheet programs. If you are having problems veiwing this order, or if alternate methods are needed for data transmission, please email craig.iannazzi@embrasse-moi.com. Merci!</span></p>';
	if (LIVE)
	{
		//$from = getUserFromPO($pos_purchase_order_id);
		//$to =  getSalesRepEmailFromPO($pos_purchase_order_id);
		$from['email'] = getUserEmail(getLoggedInUserId());
		$from['full_name'] = getUserFullName(getLoggedInUserId());
	}
	else
	{
		$from['email'] = ADMIN_EMAIL;
		$from['full_name'] = 'ADMIN TEST';
		$to = ADMIN_EMAIL;
		//$to = 'craig.iannazzi@embrasse-moi.com';
	}
	
	
	$subject = getSetting('company_name') . ' : PO# ' .$pos_purchase_order_id . ' ' . getPONumber($pos_purchase_order_id);
	email_po($to, $from['full_name'], $from['email'], $subject, $html);
	setPOStatus($pos_purchase_order_id, 'OPEN');
	setOrderStatus($pos_purchase_order_id, 'EMAILED');
	$date = date("Y-m-d H:i:s");
	setPurchaseOrderPlacedDate($pos_purchase_order_id, $date);
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




function email_po($to, $from_name, $from_email, $subject, $msg)
{

	if (LIVE) $cc = getSetting('purchase_orders_cc_email');

	switfMailIt($from, $to, $subject, $msg, $cc);


//	// Make sure to escape quotes
//	$headers = "From: " . $from_email . "\r\n";
//	$headers .= "Reply-To: ". $from_email . "\r\n";
//	$headers .= "Return-Path: " . "\r\n";
//	if (LIVE) $headers .= "CC: " . getSetting('purchase_orders_cc_email') . "\r\n";
//	$headers  .= 'MIME-Version: 1.0' . "\r\n";
//
//	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
//	$headers .=  getSetting('company_name') . ' : PO# Placed By ' . $from_name . ' <' . $from_email . '>' . "\r\n";
//
//	mail($to, $subject, $msg, $headers);

}

?> 