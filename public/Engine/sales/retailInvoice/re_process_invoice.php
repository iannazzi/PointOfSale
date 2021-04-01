<?
$binder_name = 'Sales Invoices';
$access_type = 'WRITE';
$page_title = 'Sales Invoice';
require_once('../sales_functions.php');

//we probably will call this from an ajax call...
$pos_sales_invoice_id = getPostOrGetID('pos_sales_invoice_id');

reloadInvoiceTaxContents($pos_sales_invoice_id);
$location = "retail_sales_invoice.php?pos_sales_invoice_id=".$pos_sales_invoice_id;
header('Location: ' . $location);		

?>