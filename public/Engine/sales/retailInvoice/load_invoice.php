<?
$binder_name = 'Sales Invoices';
$access_type = 'WRITE';
require_once('../sales_functions.php');
$pos_sales_invoice_id = getPostOrGetID('pos_sales_invoice_id');
$data =  getInvoiceContents($pos_sales_invoice_id);
if (sizeof($data)>0)
{
	echo json_encode($data);
}
else
{
	echo 'No Data';
}



?>