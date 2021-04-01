<?php
function getDefaultCategorySalesTaxCategoryId($pos_category_id)
{
	$sql = "SELECT pos_sales_tax_category_id FROM pos_categories WHERE pos_category_id = $pos_category_id";
	return getSingleValueSQL($sql);
}
function getSalesTaxData($pos_sales_tax_rate_id)
{
	$sql = "SELECT * FROM pos_sales_tax_rates WHERE pos_sales_tax_rate_id = $pos_sales_tax_rate_id";
	return getSQL($sql);
}
function getTaxJurisdictionData($pos_tax_jurisdiction_id)
{
	$sql = "SELECT * FROM pos_tax_jurisdictions WHERE pos_tax_jurisdiction_id = $pos_tax_jurisdiction_id";
	return getSQL($sql);
}
function getSalesTaxCategoryData($pos_sales_tax_category_id)
{
	$sql = "SELECT * FROM pos_sales_tax_categories WHERE pos_sales_tax_category_id = $pos_sales_tax_category_id";
	return getSQL($sql);
}
function getSalesTaxCategories()
{
	$sql = "SELECT * FROM pos_sales_tax_categories";

	return getSQL($sql);
}
function getSalesTaxCategoriesIdsAndNames()
{
	$sql = "SELECT pos_sales_tax_category_id,tax_category_name FROM pos_sales_tax_categories";

	 return getFieldRowSql($sql);
}
function getSalesTaxCategoryNames()
{
	$sql = "SELECT tax_category_name FROM pos_sales_tax_categories";
	return convert_mysql_data_to_indexed_array(getSQL($sql));
}
function getSalesTaxCategoryIDs()
{
	$sql = "SELECT pos_sales_tax_category_id FROM pos_sales_tax_categories";
	return convert_mysql_data_to_indexed_array(getSQL($sql));
}


function createSalesTaxCategorySelect($name, $pos_sales_tax_category_id, $option_all = 'off', $select_events ='')
{
	$categories = getSalesTaxCategories();

	$html = '<select style="width:100%;" id = "'.$name .'" name="'.$name.'" ';
	$html .= $select_events;
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Category</option>';
	//add an option for all product categories
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_sales_tax_category_id  == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Categories</option>';
	}
	for($i = 0;$i < sizeof($categories); $i++)
	{
		$html .= '<option value="' . $categories[$i]['pos_sales_tax_category_id'] . '"';
		if ( ($categories[$i]['pos_sales_tax_category_id'] == $pos_sales_tax_category_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $categories[$i]['tax_category_name'] .'</option>';
	}
	$html .= '</select>';
	return $html;
}
function getTaxTypes()
{
	$sql = "SELECT COLUMN_TYPE
			FROM information_schema.columns
WHERE TABLE_NAME = 'pos_sales_tax_rates'
AND COLUMN_NAME = 'tax_type'
LIMIT 1
";
	$r_array = getSQL($sql);
	$result = str_replace(array("enum('", "')", "''"), array('', '', "'"), $r_array[0]['COLUMN_TYPE']);
	$arr = explode("','", $result);
	return $arr;
}
function createTaxTypeSelect($name, $type, $option_all = 'off', $select_events ='')
{
	$categories = getTaxTypes();

	$html = '<select style="width:100%;" id = "'.$name .'" name="'.$name.'" ';
	$html .= $select_events;
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Category</option>';
	//add an option for all product categories
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_category_id  == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Categories</option>';
	}
	for($i = 0;$i < sizeof($categories); $i++)
	{
		$html .= '<option value="' . $categories[$i] . '"';
		if ( ($categories[$i] == $type) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $categories[$i] .'</option>';
	}
	$html .= '</select>';
	return $html;
}
function getStates()
{
	$sql = "SELECT pos_state_id, name, short_name FROM pos_states";
	return getSQL($sql);
}
function createStateSelect($name, $pos_state_id, $option_all = 'off', $select_events ='')
{
	$states = getStates();

	$html = '<select style="width:100%;" id = "'.$name .'" name="'.$name.'" ';
	$html .= $select_events;
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select State</option>';
	//add an option for all product categories
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_state_id  == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All</option>';
	}
	for($i = 0;$i < sizeof($states); $i++)
	{
		$html .= '<option value="' . $states[$i]['pos_state_id'] . '"';
		if ( ($states[$i]['pos_state_id'] == $pos_state_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $states[$i]['short_name'] .'</option>';
	}
	$html .= '</select>';
	return $html;
}
function getCounties($pos_state_id)
{
	$sql = "SELECT pos_tax_jurisdiction_id, jurisdiction_name FROM pos_tax_jurisdictions WHERE pos_state_id = $pos_state_id AND local_or_state = 'Local'";
	return getSQL($sql);
}
function getStateTaxJurisdictions()
{
	$sql = "SELECT pos_tax_jurisdiction_id, jurisdiction_name FROM pos_tax_jurisdictions WHERE  local_or_state = 'State'";
	return getSQL($sql);
}
function getAllCounties()
{
		$sql = "SELECT pos_tax_jurisdiction_id, jurisdiction_name FROM pos_tax_jurisdictions WHERE local_or_state = 'Local'";
	return getSQL($sql);
}
function createStateTaxJurisdictionSelect($name, $pos_tax_jurisdiction_id, $option_all = 'off', $select_events ='')
{
	$states = getStateTaxJurisdictions();

	$html = '<select style="width:100%;" id = "'.$name .'" name="'.$name.'" ';
	$html .= $select_events;
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select...</option>';
	//add an option for all product categories
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_tax_jurisdiction_id  == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All</option>';
	}
	for($i = 0;$i < sizeof($states); $i++)
	{
		$html .= '<option value="' . $states[$i]['pos_tax_jurisdiction_id'] . '"';
		if ( ($states[$i]['pos_tax_jurisdiction_id'] == $pos_tax_jurisdiction_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $states[$i]['jurisdiction_name'] .'</option>';
	}
	$html .= '</select>';
	return $html;
}
function createLocalTaxJurisdictionSelect($name, $pos_tax_jurisdiction_id, $pos_state_id, $option_all = 'off', $select_events ='')
{
	if ($pos_state_id == 'all')
	{
		$counties = getAllCounties();
	}
	else
	{
		$counties = getCounties($pos_state_id);
	}

	$html = '<select style="width:100%;" id = "'.$name .'" name="'.$name.'" ';
	$html .= $select_events;
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select...</option>';
	//add an option for all product categories
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_tax_jurisdiction_id  == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All</option>';
	}
	for($i = 0;$i < sizeof($counties); $i++)
	{
		$html .= '<option value="' . $counties[$i]['pos_tax_jurisdiction_id'] . '"';
		if ( ($counties[$i]['pos_tax_jurisdiction_id'] == $pos_tax_jurisdiction_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $counties[$i]['jurisdiction_name'] .'</option>';
	}
	$html .= '</select>';
	return $html;
}

function getStateIDFromSalesTaxRate($pos_sales_tax_rate_id)
{
	$sql = "SELECT pos_state_id FROM pos_tax_jurisdictions
			LEFT JOIN pos_sales_tax_rates USING(pos_tax_jurisdiction_id)
			WHERE pos_sales_tax_rate_id = $pos_sales_tax_rate_id";
	return getSingleValueSQL($sql);
}
function getProductSalesTaxCategoryId($pos_product_id)
{
	$sql = "SELECT pos_sales_tax_category_id FROM pos_products WHERE pos_product_id = $pos_product_id";
	return getSingleValueSQL($sql);
}
function getTaxJurisdictionOfStore($pos_store_id)
{
	$sql = "SELECT pos_tax_jurisdiction_id FROM pos_stores WHERE pos_store_id=$pos_store_id";
	return getSingleValueSQL($sql);
}
function getTaxJurisdictionStateID($pos_tax_jurisdiction_id)
{
	$sql="SELECT pos_state_id from pos_tax_jurisdictions WHERE pos_tax_jurisdiction_id=$pos_tax_jurisdiction_id";
	return getSingleValueSQL($sql);
}
function getStateTaxJurisdictionId($pos_state_id)
{
	$sql="SELECT pos_tax_jurisdiction_id FROM pos_tax_jurisdictions WHERE pos_state_id=$pos_state_id AND local_or_state='State'";
	return getSingleValueSQL($sql);
}
function getTaxRates($pos_tax_jurisdiction_id, $pos_sales_tax_category_id, $invoice_date)
{
	$rtn = array();
	
	//next, get the regular tax rate - if there is one then there is a tax rate, otherwise, 0.
	$regular_sql = "SELECT pos_sales_tax_rate_id, pos_sales_tax_category_id, start_date, end_date, tax_rate, tax_type, exemption_value
			FROM pos_sales_tax_rates
			WHERE tax_type = 'Regular' AND pos_tax_jurisdiction_id = '$pos_tax_jurisdiction_id' AND pos_sales_tax_category_id ='$pos_sales_tax_category_id' AND start_date <= '$invoice_date'
			ORDER BY start_date DESC
			";
	$rate_data = getSQL($regular_sql);
	if (sizeof($rate_data) >0)
	{
		$rtn['regular_tax_rate'] = $rate_data[0]['tax_rate'];
		$rtn['pos_regular_sales_tax_rate_id'] = $rate_data[0]['pos_sales_tax_rate_id'];
	}
	else
	{
		$rtn['regular_tax_rate'] = 0;
		$rtn['pos_regular_sales_tax_rate_id'] = 0;
	}
	
	
	
	//if there is one of these then there is an exemption, pick the most recent
	$sql = "SELECT pos_sales_tax_rate_id, pos_sales_tax_category_id, start_date, end_date, tax_rate, tax_type, exemption_value 
				FROM pos_sales_tax_rates
				WHERE tax_type = 'Exemption' AND pos_tax_jurisdiction_id = '$pos_tax_jurisdiction_id' AND (pos_sales_tax_category_id ='$pos_sales_tax_category_id' OR pos_sales_tax_category_id = 0) AND start_date <= '$invoice_date'
				ORDER BY start_date DESC
				";
	
	$exemptions = getSQL($sql);
	if (sizeof($exemptions) >0)
	{
		$rtn['exemption_tax_rate'] = $exemptions[0]['tax_rate'];
		$rtn['exemption_value'] = $exemptions[0]['exemption_value'];
		$rtn['pos_exemption_sales_tax_rate_id'] = $exemptions[0]['pos_sales_tax_rate_id'];
	}
	else
	{
		$rtn['exemption_tax_rate'] = $rtn['regular_tax_rate'];
		$rtn['exemption_value'] = 0;
		$rtn['pos_exemption_sales_tax_rate_id'] = 0;
	}
	
	
	return $rtn;
	
}
function getTaxJursidictionDefaultTaxRate($pos_tax_jurisdiction_id)
{
	return getSingleValueSQL("SELECT default_tax_rate FROM pos_tax_jurisdictions WHERE pos_tax_jurisdiction_id = $pos_tax_jurisdiction_id");
}
function getProductTaxArray($pos_local_tax_jurisdiction_id, $pos_state_id, $pos_sales_tax_category_id, $invoice_date)
{
	//we can assume a 0 value for local tax jurisdiction if it is out of state. therefore the state should come back 0 and no tax will be assigned....
	
	//$pos_state_id = getTaxJurisdictionStateID($pos_local_tax_jurisdiction_id);
	$state_tax_jurisdiction = getStateTaxJurisdictionId($pos_state_id);
	
	$state_tax = getTaxRates($state_tax_jurisdiction, $pos_sales_tax_category_id, $invoice_date);
	$tax['pos_state_tax_jurisdiction_id'] = $state_tax_jurisdiction;
	$tax['state_exemption_value'] = $state_tax['exemption_value'];
	$tax['state_regular_tax_rate'] = $state_tax['regular_tax_rate'];
	$tax['state_exemption_tax_rate'] = $state_tax['exemption_tax_rate'];
	$tax['pos_state_regular_sales_tax_rate_id'] = $state_tax['pos_regular_sales_tax_rate_id'];
	$tax['pos_state_exemption_sales_tax_rate_id'] = $state_tax['pos_exemption_sales_tax_rate_id'];
	
	$local_tax = getTaxRates($pos_local_tax_jurisdiction_id, $pos_sales_tax_category_id, $invoice_date);
	$tax['pos_local_tax_jurisdiction_id'] = $pos_local_tax_jurisdiction_id;
	//$tax['local_tax_rate'] =$local_tax['tax_rate'];
	//$tax['local_tax_type'] =$local_tax['tax_type'];
	$tax['local_exemption_value'] =$local_tax['exemption_value'];
	$tax['local_regular_tax_rate'] = $local_tax['regular_tax_rate'];
	$tax['local_exemption_tax_rate'] =$local_tax['exemption_tax_rate'];
	$tax['pos_local_regular_sales_tax_rate_id'] = $local_tax['pos_regular_sales_tax_rate_id'];
	$tax['pos_local_exemption_sales_tax_rate_id'] = $local_tax['pos_exemption_sales_tax_rate_id'];
	return $tax;
}
function getTaxJurisdictionFromZipCode($zip_code)
{
	//1 zip code to .... county ID
	//2 county id to jurisdiction id
	$sql = "SELECT pos_tax_jurisdiction_id FROM pos_tax_jurisdictions
			LEFT JOIN pos_counties USING (pos_tax_jurisdiction_id)
			LEFT JOIN pos_zip_codes USING (pos_county_id)
			WHERE zip_code = '$zip_code'";
	$pos_tax_jurisdiction_id =  getSingleValueSQL($sql);
	if(!$pos_tax_jurisdiction_id)
	{
		$pos_tax_jurisdiction_id = 0;
	}
	return $pos_tax_jurisdiction_id;
}
function getStoreTaxJurisdictionID($pos_store_id)
{
	return getSingleValueSQL("SELECT pos_tax_jurisdiction_id FROM pos_stores WHERE pos_store_id = $pos_store_id");
}
function getStoreStateId($pos_store_id)
{
	return getSingleValueSQL("SELECT pos_state_id FROM pos_stores WHERE pos_store_id = $pos_store_id");
}

function calculateInvoiceContentTaxRate($sales_invoice_content_array, $tax_array)
{
	//what I want to do here is check the 'final' sale price, and the tax array to get the tax rate.
	//return the tax rate
	$price = calculateSalePrice($sales_invoice_content_array);
	if(abs($price - $tax_array['state_exemption_value']) < 0.0001)
	{
		$tax_rate = $tax_array['local_exemption_tax_rate'] + $tax_array['state_exemption_tax_rate'];
	}
	else
	{
		$tax_rate = $tax_array['local_regular_tax_rate'] + $tax_array['state_regular_tax_rate'];
	}
	return $tax_rate;

}
function caclulateInvoiceContentTaxAmount($sales_invoice_content_array, $tax_array)
{
	$price = calculateSalePrice($sales_invoice_content_array);
	$tax_rate = calculateInvoiceContentTaxRate($sales_invoice_content_array, $tax_array);
	return $price*$tax_rate/100;
}
?>