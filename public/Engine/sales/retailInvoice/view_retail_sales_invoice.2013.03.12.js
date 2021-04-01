function lookupCustomer()
{
	 open_win(POS_ENGINE_URL + "/customers/select_customer.php?complete_location=" + encodeURI(POS_ENGINE_URL + '/sales/retailInvoice/view_retail_sales_invoice.php?pos_sales_invoice_id='+pos_sales_invoice_id));
}
function editCustomer(url, pos_customer_id)
{
	 open_win(POS_ENGINE_URL + "/customers/select_customer.php?pos_customer_id=" +pos_customer_id+"&complete_location=" + encodeURI(url));
}