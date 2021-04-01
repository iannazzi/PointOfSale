<?
$binder_name = 'Sales Invoices';
$access_type = 'READ';
require_once('../sales_functions.php');
require_once('retail_sales_invoice_functions.php');
$pos_sales_invoice_id = getPostOrGetID('pos_sales_invoice_id');
$type = getPostOrGetValue('type');

			 
/*if($type =='email')
{	
	$pos_customer_id = getCustomerFromSalesInvoice($pos_sales_invoice_id);
	if($pos_customer_id == 0)
	{
		//we need to add an email
		echo 'Please add a customer and email address';
		exit();
	}
	else
	{
		
		$to = getCustomerEmail($pos_customer_id);
		$from = getSetting('invoice_from_email');
		
		$html = emailInvoiceHtml($pos_sales_invoice_id);
		


	// Make sure to escape quotes
	$headers = "From: " . $from . "\r\n";
	$headers .= "Reply-To: ". $from . "\r\n";
	if ($cc != '') $headers .= "CC: " . $cc . "\r\n";
	$headers  .= 'MIME-Version: 1.0' . "\r\n";
	
	$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
	
	mail($to, $subject, $msg, $headers);


		
		
		echo 'Emailed To ' .$email;
		exit();
	}
	
	
}
*/
if ($type=='customer')
{
	printCustomerCopySalesInvoice($pos_sales_invoice_id);
	echo 'Sent to Printer';
	exit();
	
}
elseif ($type=='store')
{

	printStoreCopyMemoSalesInvoice($pos_sales_invoice_id);
	echo 'Sent to Printer';
	exit();
}
elseif ($type =='customer_inline')
{
	openInlineCustomerCopySalesInvoice($pos_sales_invoice_id);
	echo 'Sent Inline';
	exit();
}
elseif ($type =='email_pdf')
{
	$email_status = emailInvoicePDF($pos_sales_invoice_id);
	echo $email_status;
	exit();
}
elseif ($type =='email_html')
{
	//don't use.... not fully coded, need payments, promotions, etc... booooring.
	$email_status = emailInvoiceHtml($pos_sales_invoice_id);
	echo $email_status;
	exit();
}
elseif ($type =='gift_receipt')
{
	printCustomerCopyGiftReceipt($pos_sales_invoice_id);
	echo 'Sent to Printer';
	exit();
}
else
{	
	echo 'No Type';
	exit();
}


/*
$html =  emailPDFInvoiceHtml($pos_sales_invoice_id, 'email');
include(HEADER_FILE);
echo '<h2>This should come out of the printer </h2>';
echo $html;
include(FOOTER_FILE);*/





?>