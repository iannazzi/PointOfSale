<?php
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
$page_title = 'Email a Purchase Order';
require_once ('../po_functions.php');

$pos_purchase_order_id = getPostOrGetID('pos_purchase_order_id');
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
		$to = getSalesRepEmailFromPO($pos_purchase_order_id);
		$from = getUserFromPO($pos_purchase_order_id);
	}
	else
	{
		$to = ADMIN_EMAIL;
		//$to = 'craig.iannazzi@embrasse-moi.com';
				$from['email'] = ADMIN_EMAIL;
		$from['full_name'] = 'ADMIN TEST';
	}
	
	
	$subject = getSetting('company_name') . ' PO# ' .getPONumber($pos_purchase_order_id);

	include(HEADER_FILE);
	$email = '<p> The following email has been sent</p>';
	$email .= "<p>To: " . $to . "\r\n</p>";
	$email .= "<p>From: " . $from['email'] . "\r\n</p>";
	$email .= "<p>Reply-To: ". $from['email'] . "\r\n</p>";
	if (LIVE) {$email .= "<p>CC: " . getSetting('purchase_orders_cc_email')  . "\r\n</p>";}
	$email .= $html;
	echo $email;
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