<?php

/***********************************MANUFACTURERS***************************************/
function getSalesRepEmail($pos_manufacturer_id)
{
	$mfg = getManufacturer($pos_manufacturer_id);
	$rep_email = $mfg[0]['email'];
	return $rep_email;
}
function getManufacturers()
{
	$sql = "SELECT pos_manufacturer_id, company FROM pos_manufacturers WHERE active = 1 ORDER BY company ASC";
	return getSQL($sql);
}
function getUPCCode($pos_manufacturer_id, $style_number, $color_code, $size)
{
	$manufacturer_upc_sql = "SELECT upc_code FROM pos_manufacturer_upc WHERE style_number ='$style_number' AND pos_manufacturer_id = '$pos_manufacturer_id' AND size = '$size' AND color_code = '$color_code'";
	if (checkSQLIfExists($manufacturer_upc_sql))
	{
		$upc_code_array = getSQL($manufacturer_upc_sql);
		$upc_code = $upc_code_array[0]['upc_code'];
	}
	else
	{
		$upc_code ='';
	}
	return $upc_code;
}
function getManufacturerEmails()
{
	$sql = "SELECT email FROM pos_manufacturers WHERE active = 1";
	$emails = getSQL($sql);
	return $emails;
}
function getManufacturer($pos_manufacturer_id)
{
	// Get the manufacturer information
	$dbc = openPOSDatabase();
	$manufacturer_sql = "SELECT * FROM pos_manufacturers WHERE pos_manufacturer_id = '" . $pos_manufacturer_id . "'"; 
	$manufacturer_sql_result = @mysqli_query ($dbc, $manufacturer_sql);
	$selected_manufacturer =  convert_mysql_result_to_array($manufacturer_sql_result);
	mysqli_close($dbc);
	return $selected_manufacturer;
}
function getManufacturerName($pos_manufacturer_id)
{
	$mfg = getManufacturer($pos_manufacturer_id);
	return $mfg[0]['company'];
}
function getManufacturerIDFromBrandId($pos_manufacturer_brand_id)
{
	$brand = getBrand($pos_manufacturer_brand_id);
	return $brand[0]['pos_manufacturer_id'];
}
function getBrand($pos_manufacturer_brand_id)
{
	$dbc = openPOSDatabase();
	// get the brand info
	$brand_sql = "SELECT * FROM pos_manufacturer_brands WHERE pos_manufacturer_brand_id = '" . $pos_manufacturer_brand_id . "'"; 
	$brand_sql_result = @mysqli_query ($dbc, $brand_sql);
	$selected_brand = convert_mysql_result_to_array($brand_sql_result);
	mysqli_close($dbc);
	return $selected_brand;
}
function getBrands()
{
	$dbc = openPOSDatabase();
	// get the brand info
	$brand_sql = "SELECT * FROM pos_manufacturer_brands WHERE active='1' ORDER BY brand_name ASC"; 
	$brand_sql_result = @mysqli_query ($dbc, $brand_sql);
	$brands = convert_mysql_result_to_array($brand_sql_result);
	mysqli_close($dbc);
	return $brands;
}
function getBrandName($pos_manufacturer_brand_id)
{
	$brand = getBrand($pos_manufacturer_brand_id);
	return $brand[0]['brand_name'];
}
function getBrandCode($pos_manufacturer_brand_id)
{
	$brand_sql = "SELECT brand_code FROM pos_manufacturer_brands WHERE pos_manufacturer_brand_id = '$pos_manufacturer_brand_id'";
	$brand_array = getSQL($brand_sql);
	$brand_code = $brand_array[0]['brand_code'];
	return $brand_code;
}
function getBrandIDFromBrandCode($brand_code)
{
	$sql = "SELECT pos_manufacturer_brand_id FROM pos_manufacturer_brands WHERE brand_code ='".$brand_code."'";
	$brand_array = getSQL($brand_sql);
	$brand_id = $brand_array[0]['pos_manufacturer_brand_id'];
	return $brand_id;
}
function getBrandSizes($pos_manufacturer_brand_id)
{
	//Want to start by getting the brand size chart.. put it in an array
	$dbc = openPOSDatabase();
	$size_chart_q = "SELECT pos_category_id, cup, inseam, sizes
					FROM pos_manufacturer_brand_sizes 
					WHERE  pos_manufacturer_brand_id = '$pos_manufacturer_brand_id'";
	$size_chart_r = @mysqli_query ($dbc, $size_chart_q);
	$size_array = array();
	$size_row_counter = 0;
	while ($brand_size_row = mysqli_fetch_array($size_chart_r, MYSQLI_ASSOC)) 
	{
		$strArray = explode("\r\n", $brand_size_row['sizes']);
		$tmp_num_sizes = sizeof($strArray);
		if ($tmp_num_sizes == 0)
		{
			//one size?
			$size_array[$size_row_counter][0] = 'OS';
		}
		else
		{
			for($sz_counter=0;$sz_counter<$tmp_num_sizes;$sz_counter++)
			{
				$size_array[$size_row_counter][$sz_counter] = $strArray[$sz_counter];
			}
		}
		$size_row_counter++;
	}
	mysqli_close($dbc);
	return $size_array;
}
function getBrandSizesForSelect($pos_manufacturer_brand_id)
{
	$sizes = "SELECT pos_manufacturer_brand_size_id, sizes
					FROM pos_manufacturer_brand_sizes 
					WHERE  pos_manufacturer_brand_id = '$pos_manufacturer_brand_id'";
	return getSQL($sizes);
}
function getBrandSizeChart($pos_manufacturer_brand_id)
{
	$dbc = openPOSDatabase();
	//Need to check the brand to get the sizing chart
	//cup is Yes or No and inseam is Yes or No
	$size_chart_q = "SELECT pos_manufacturer_brand_size_id, pos_category_id, cup, case_qty, inseam, sizes, pos_product_attribute_id
					FROM pos_manufacturer_brand_sizes 
					WHERE  pos_manufacturer_brand_id = '$pos_manufacturer_brand_id'";
	$size_chart_r = @mysqli_query ($dbc, $size_chart_q);
	//Need to get the number of rows
	$num_size_rows = mysqli_num_rows($size_chart_r);
	//the size chart will have the attributes used for ordering
		//the attributes will then be separated by ::
		//example
		//wolford with cup and inseam
		//attributes will be A::32
		//names should b cup::inseam
	$attributes = array();
	if ($num_size_rows == 0)
	{
		//manufacturer did not set up a size chart....
		$num_sizes = 0;
		$num_size_rows = 1;
		$bln_cup = 0;
		$bln_inseam = 0;
		$case_qty = 0;
	}
	else
	{
		//Need to get the number of columns
		$brand_size_row = mysqli_fetch_array($size_chart_r, MYSQLI_ASSOC);
		$strArray = explode("\r\n", $brand_size_row['sizes']);
		if($brand_size_row['pos_product_attribute_id'] != 0)
		{
			$attributes = array(getProductAttributeName($brand_size_row['pos_product_attribute_id']));
		}
		$num_sizes = sizeof($strArray);
	}
	//Find out if there is a cup or inseam set to Yes
	$bln_cup = 0;
	$bln_inseam = 0;
	$case_qty = 0;
	$size_chart_r = @mysqli_query ($dbc, $size_chart_q);
	
	$size_category_ids = array();
	$brand_size_ids = array();
	$counter = 0;
	while ($brand_size_row = mysqli_fetch_array($size_chart_r, MYSQLI_ASSOC)) 
	{
		if ($brand_size_row['cup'] == 1) 
		{
			$bln_cup = 1;
		}
		if ($brand_size_row['inseam'] == 1) 
		{
			$bln_inseam = 1;
		}
		if ($brand_size_row['case_qty'] == 1) 
		{
			$case_qty = 1;
		}
		$brand_size_ids[$counter] = $brand_size_row['pos_manufacturer_brand_size_id'];
		$size_category_ids[$counter] = $brand_size_row['pos_category_id'];
		$counter ++;
	}
	$size_chart_array = array(
			'attributes' => $attributes,
			'case_qty' => $case_qty,
			'num_sizes' => $num_sizes,
			'num_size_rows' => $num_size_rows,
			'bln_cup' => $bln_cup,
			'bln_inseam' => $bln_inseam,
			'size_categories' => $size_category_ids,
			'pos_manufacturer_brand_size_id' => $brand_size_ids,
			'sizes' => getBrandSizes($pos_manufacturer_brand_id));
			
	mysqli_close($dbc);
	return $size_chart_array;
}
function getManufacturerBrandSizesForSizeRow($pos_purchase_order_id, $index)
{
	if ($index =='undefined')
	{	
		return 'undefined';
	}
	else
	{
		//$sizes = getBrandSizesForSelect($pos_manufacturer_brand_id);
		//need to get sizes from the stored size chart, not the current!
		$brand_size_chart = selectNewOrStoredBrandSizeChart($pos_purchase_order_id);
	/*	$size_chart_array = array(
				'num_sizes' => $num_sizes,
				'num_size_rows' => $num_size_rows,
				'bln_cup' => $bln_cup,
				'bln_inseam' => $bln_inseam,
				'size_categories' => $size_category_ids,
				'pos_manufacturer_brand_size_id' => $brand_size_ids,
				'sizes' => getBrandSizes($pos_manufacturer_brand_id));
				
	*/
		//$size_comma_sep = implode(',' , explode("\r\n", $sizes[$index]['sizes']));
		$size_comma_sep = implode(',', $brand_size_chart['sizes'][$index]);
	}
	return $size_comma_sep;
}
function getManufacturerBrandSizeRowID($pos_purchase_order_id, $index)
{
	if($index == 'undefined')
	{
		return 'undefined';
	}
	else
	{
		//$sizes = getBrandSizesForSelect($pos_manufacturer_brand_id);
		$brand_size_chart = selectNewOrStoredBrandSizeChart($pos_purchase_order_id);
		return $brand_size_chart['pos_manufacturer_brand_size_id'][$index];
		//return $sizes[$index]['pos_manufacturer_brand_size_id'];
	}
}


function createManufacturerSelect($name, $pos_manufacturer_id, $option_all = 'off', $select_events = '')
{
	$mfgs = getManufacturers();

	$html = '<select style="width:100%" id = "'.$name .'" name="'.$name.'" ';
	$html .= $select_events;
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Manufacturer</option>';
	//add an option for all product categories
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_manufacturer_id  == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Manufacturers</option>';
	}
	for($i = 0;$i < sizeof($mfgs); $i++)
	{
		$html .= '<option value="' . $mfgs[$i]['pos_manufacturer_id'] . '"';
		
		if ( ($mfgs[$i]['pos_manufacturer_id'] == $pos_manufacturer_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $mfgs[$i]['company'] .'</option>';
	}
	$html .= '</select>';
	return $html;
}
function createManufacturerBrandSizeChartSelect($name, $pos_manufacturer_brand_id, $pos_manufacturer_brand_size_id = 'false', $select_events = ' onchange="needToConfirm=true" ')
{
	$sizes = getBrandSizesForSelect($pos_manufacturer_brand_id);

	$html = '<select style = "width:100%" id = "'.$name .'" name="'.$name.'" ';
	$html .= $select_events;
	$html .= '>';
	$html .= '<option value="false">Select Size Row</option>';
	for($i = 0;$i < sizeof($sizes); $i++)
	{
		$size_comma_sep = implode(',' , explode("\r\n", $sizes[$i]['sizes']));
		
		$html .= '<option value="' . $sizes[$i]['pos_manufacturer_brand_size_id'] . '"';
		
		if ( ($sizes[$i]['pos_manufacturer_brand_size_id'] == $pos_manufacturer_brand_size_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $size_comma_sep  .'</option>';
	}
	$html .= '</select>';
	
	return $html;
}
function createManufacturerBrandSelect($name, $pos_manufacturer_brand_id, $option_all = 'off', $select_events = ' onchange="needToConfirm=true" ')
{
	$brands = getBrands();

	$html = '<select style = "width:100%" id = "'.$name .'" name="'.$name.'" ';
	$html .= $select_events;
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Brand</option>';
	//add an option for all product categories
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_manufacturer_brand_id  == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Brands</option>';
	}
	for($i = 0;$i < sizeof($brands); $i++)
	{
		$html .= '<option value="' . $brands[$i]['pos_manufacturer_brand_id'] . '"';
		
		if ( ($brands[$i]['pos_manufacturer_brand_id'] == $pos_manufacturer_brand_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $brands[$i]['brand_name'] .'</option>';
	}
	$html .= '</select>';
	
	return $html;
}



?>