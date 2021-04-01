<?php
$binder_name = 'Sales Invoices';
$access_type = 'WRITE';
$page_title = 'Sales Invoice';
require_once('../sales_functions.php');
$pos_sales_invoice_id = getPostOrGetID('pos_sales_invoice_id');
$db_table = 'pos_sales_invoice';
$key_val_id['pos_sales_invoice_id'] = $pos_sales_invoice_id;
if(checkForValidIDinPOS($pos_sales_invoice_id, $db_table, 'pos_sales_invoice_id'))
{
	//rediaplay the invoice table...
	$invoice_contents_table_def = createRetailSalesInvoiceContentsTableDef($pos_sales_invoice_id);
	$data = getInvoiceContents($pos_sales_invoice_id);
	$html = createStaticViewDynamicTable($invoice_contents_table_def, $data);
	
	include (HEADER_FILE);
	echo $html;
	include (FOOTER_FILE);
}
else
{
	include (HEADER_FILE);
	echo 'Not A Valid Id';
	include (FOOTER_FILE);
}
?>