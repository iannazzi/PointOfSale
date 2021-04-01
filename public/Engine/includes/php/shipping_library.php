<?php
function getShippingSalesTaxCategoryId($pos_shipping_option_id)
{
	$sql = "SELECT pos_sales_tax_category_id FROM pos_shipping_options WHERE pos_shipping_option_id = $pos_shipping_option_id";
	return getSingleValueSQL($sql);
}
function getShippingName($pos_shipping_option_id)
{
	$sql = "SELECT concat(carrier_name, ' ', method_name) FROM pos_shipping_options WHERE pos_shipping_option_id = $pos_shipping_option_id";
	return getSingleValueSQL($sql);
}
function getZipCode($pos_address_id)
{
	$sql = "SELECT zip FROM pos_addresses WHERE pos_address_id = $pos_address_id";
	return getSingleValueSQL($sql);
}
function getAddressStateId($pos_address_id)
{
	$sql = "SELECT pos_state_id FROM pos_addresses WHERE pos_address_id = $pos_address_id";
	$pos_state_id =  getSingleValueSQL($sql);
	if(!$pos_state_id)
	{
		$pos_state_id = 0;
	}
	return $pos_state_id;
}
function getStateShortName($pos_state_id)
{
	$sql = "SELECT short_name FROM pos_states WHERE pos_state_id = $pos_state_id";
	return getSingleValueSQL($sql);
}

?>