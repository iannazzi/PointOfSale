<?
//direct the page to go here before doing anything else
//this way we can unlock the invoice and do some validating....

$binder_name = 'Sales Invoices';
$access_type = 'WRITE';
$page_title = 'Sales Invoice';
require_once('../sales_functions.php');
$pos_sales_invoice_id = getPostOrGetID('pos_sales_invoice_id');
$db_table = 'pos_sales_invoice';
$key_val_id['pos_sales_invoice_id'] = $pos_sales_invoice_id;
unlock_entry($db_table, $key_val_id);

if($_GET['next']=='pay')
{
	$go_url = 'retail_sales_invoice_payments.php?pos_sales_invoice_id='.$pos_sales_invoice_id;
}
else if( $_GET['next'] == 'view')
{
		$go_url = POS_ENGINE_URL . '/sales/retailInvoice/retail_sales_invoice.php?type=view&pos_sales_invoice_id='.$pos_sales_invoice_id;
}

	header('LOCATION: '.$go_url);

?>