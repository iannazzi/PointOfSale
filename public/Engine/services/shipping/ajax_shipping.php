<?
$binder_name = 'Services';
$access_type = 'WRITE';
require_once('../services_functions.php');

/*

	to get shipping we need 
	weight
	destination
	dimensions
	location
	serivice type
	
	all this should come from the sales invoice.....
	however the address might change, so we would update based on that....

*/
$pos_shipping_option_id = 7;
$shipping_description = getShippingName($pos_shipping_option_id);
$pos_address_id = scrubInput(getPostOrGetValue('pos_address_id'));
$pos_sales_invoice_id = getPostOrGetID('pos_sales_invoice_id');


$shipping_data['content_type'] = 'SHIPPING';
$shipping_data['quantity'] = 1;
$shipping_data['title'] = $shipping_description;
$shipping_data['checkout_description'] = $shipping_description;


$pos_sales_tax_category_id = getShippingSalesTaxCategoryId($pos_shipping_option_id);
$shipping_data['pos_sales_tax_category_id'] = $pos_sales_tax_category_id;


$shipping_data['retail_price'] = 0;
$shipping_data['sale_price'] = '-';
if($pos_address_id != 'false')
{
	$zip_code = getZipCode($pos_address_id);
	$pos_state_id = getAddressStateId($pos_address_id);
	$pos_local_tax_jurisdiction_id = getTaxJurisdictionFromZipCode($zip_code);
	if($pos_local_tax_jurisdiction_id != false)
	{
		$invoice_date = getSalesInvoiceDate($pos_sales_invoice_id);
		$tax = getProductTaxArray($pos_local_tax_jurisdiction_id, $pos_state_id, $pos_sales_tax_category_id, $invoice_date);
		$shipping_data = array_merge($shipping_data,$tax);
	}
}

echo json_encode($shipping_data) . "\n";





?>