<?
$binder_name = 'Sales Invoices';
$access_type = 'WRITE';
require_once('../sales_functions.php');
$pos_sales_tax_category_id = getPostOrGetID('pos_sales_tax_category_id');
$pos_sales_invoice_id = getPostOrGetID('pos_sales_invoice_id');
//the address id is needed to get the tax rate...

$pos_address_id = getPostOrGetValue('pos_address_id');
$ship = getPostOrGetValue('ship');
$invoice_date = getSalesInvoiceDate($pos_sales_invoice_id);
//need to code the tax jurisdiction ids based on shipping or store...
//sold in store
if($pos_address_id == 0 OR $pos_address_id == 'false' OR $ship == 'false')
{
	//sold in store
	$pos_store_id = $_SESSION['store_id'];
	$pos_local_tax_jurisdiction_id = getTaxJurisdictionOfStore($pos_store_id);
	$pos_state_id = getTaxJurisdictionStateID($pos_local_tax_jurisdiction_id);
}
else
{
	$pos_address_id = getSalesInvoiceAddress($pos_sales_invoice_id);
	$zip_code = getZipCode($pos_address_id);
	$pos_state_id = getAddressStateId($pos_address_id);
				//preprint('state' . $pos_state_id);
	if($zip_code != '')
	{
		$pos_local_tax_jurisdiction_id = getTaxJurisdictionFromZipCode($zip_code);
					//preprint('pos_local_tax_jurisdiction_id: ' . $pos_local_tax_jurisdiction_id);
	}
	else
	{
					//address is effed so default to local jurisdiction
		$pos_local_tax_jurisdiction_id = getStoreTaxJurisdictionID($pos_store_id);
		$pos_state_id = getStoreStateId($pos_store_id);
	}
}

//$tax = getProductTaxArray($pos_sales_tax_category_id, $price, $invoice_date);
	$tax = getProductTaxArray($pos_local_tax_jurisdiction_id, $pos_state_id, $pos_sales_tax_category_id, $invoice_date);
$tax['pos_sales_tax_category_id'] = $pos_sales_tax_category_id;
echo json_encode($tax) . "\n";


?>