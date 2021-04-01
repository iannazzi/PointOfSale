<?php
function getCustomerData($pos_customer_id)
{
	$sql="SELECT * FROM pos_customers WHERE pos_customer_id = $pos_customer_id";
	return GetSQL($sql);
}
function getCustomerFirstName($pos_customer_id)
{
	$sql="SELECT first_name FROM pos_customers WHERE pos_customer_id = $pos_customer_id";
	return getSingleValueSQL($sql);
}
function getCustomerLastName($pos_customer_id)
{
	$sql="SELECT last_name FROM pos_customers WHERE pos_customer_id = $pos_customer_id";
	return getSingleValueSQL($sql);
}
function getCustomerFullName($pos_customer_id)
{
	$sql="SELECT CONCAT(first_name, ' ', last_name) FROM pos_customers WHERE pos_customer_id = $pos_customer_id";
	return getSingleValueSQL($sql);
}
function getCustomerEmail($pos_customer_id)
{
	$sql="SELECT email1 FROM pos_customers WHERE pos_customer_id = $pos_customer_id";
	return getSingleValueSQL($sql);
}
function getCustomerPhone($pos_customer_id)
{
	$sql="SELECT phone FROM pos_customers WHERE pos_customer_id = $pos_customer_id";
	return getSingleValueSQL($sql);
}
function getCustomerAddress($pos_customer_id)
{
	return getSingleValueSQL("SELECT pos_addresses.pos_address_id FROM pos_addresses LEFT JOIN pos_customer_addresses ON pos_customer_addresses.pos_address_id = pos_addresses.pos_address_id WHERE pos_customer_addresses.pos_customer_id = $pos_customer_id ORDER BY pos_address_id DESC LIMIT 1");
}
function getFullAddress($pos_address_id)
{
	return getSingleValueSQL("SELECT CONCAT(address1, ' ', address2, ' ', city, ' ', zip) FROM pos_addresses  WHERE pos_address_id = $pos_address_id");
}
function getCustomerAddresses($pos_customer_id)
{
	$addresses = getSQL("SELECT pos_addresses.*, concat(address1, ' ', address2, ' ', city, ',', state, ' ' ,zip) as full_address FROM pos_addresses LEFT JOIN pos_states USING (pos_state_id) LEFT JOIN pos_customer_addresses ON pos_customer_addresses.pos_address_id = pos_addresses.pos_address_id WHERE pos_customer_addresses.pos_customer_id = $pos_customer_id");
	return $addresses;
}
function createCustomerAddressSelect($name, $pos_address_id, $pos_customer_id, $option_all = 'off', $select_events ='', $add = 'off')
{
	$addresses = getSQL("SELECT pos_addresses.* FROM pos_addresses LEFT JOIN pos_customer_addresses ON pos_customer_addresses.pos_address_id = pos_addresses.pos_address_id WHERE pos_customer_addresses.pos_customer_id = $pos_customer_id");
	
	$html = '<select style="width:100%;" id = "'.$name .'" name="'.$name.'" ';
	$html .= $select_events;
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Address</option>';
	//add an option for all product categories
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_address_id  == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All</option>';
	}
	for($i = 0;$i < sizeof($addresses); $i++)
	{
		$html .= '<option value="' . $addresses[$i]['pos_address_id'] . '"';
		if ( ($addresses[$i]['pos_address_id'] == $pos_address_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $addresses[$i]['address1'] . ' ' . $addresses[$i]['address2'] . ' ' .$addresses[$i]['city']  . ' ' . $addresses[$i]['zip'] .  '</option>';
	}
	if ($add != 'off')
	{
		$html .= '<option value ="add"';

		$html .= '>Add a New Address</option>';
	}
	$html .= '</select>';
	return $html;
}
?>