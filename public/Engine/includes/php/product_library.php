<?php
/*function getProductSubIdAttributes($pos_product_sub_id)
{
	$attribute_list =  getSQL("SELECT attributes_list FROM pos_product_sub_id WHERE pos_product_sub_id = $pos_product_sub_id");
	$data[0]['attributes_list'];
	$attributes = explode(newline(),$attribute_list[0]['attributes_list']);
	$all_attributes = array();
	for($i=0;$i<sizeof($attributes);$i++)
	{
		$att_arr = explode('::', $attributes[$i]);
		$all_attributes[$att_arr[0]] = $att_arr[1];
	}
	return $all_attributes;
}
function getProductSubIdSize($pos_product_sub_id)
{
	
	$all_attributes = getProductSubIdAttributes($pos_product_sub_id);
	return $all_attributes['size'];
}
function getProductSubIdColorCode($pos_product_sub_id)
{
	
	$all_attributes = getProductSubIdAttributes($pos_product_sub_id);
	return $all_attributes['color_code'];
}
function getProductSubIdColorDescription($pos_product_sub_id)
{
	
	$all_attributes = getProductSubIdAttributes($pos_product_sub_id);
	return $all_attributes['color_description'];
}*/
function getProductID($pos_manufacturer_brand_id, $style_number)
{
	$product_exists_q = "SELECT pos_product_id FROM pos_products WHERE pos_manufacturer_brand_id ='$pos_manufacturer_brand_id' AND style_number = '$style_number'";
	$product_exists = getSQL($product_exists_q);
	if (sizeof($product_exists)==0)
	{
		return false;
	}
	else
	{
		return $product_exists[0]['pos_product_id'];
	}
}
function getProduct($pos_product_id)
{
	$sql = "SELECT * FROM pos_products WHERE pos_product_id = $pos_product_id";
	return getSQL($sql);
}
function getPorductIDFromProductColorId($pos_product_color_id)
{
	$sql = "SELECT pos_product_id FROM pos_product_colors WHERE pos_product_color_id = '$pos_product_color_id'";
	$product_id = getSQL($sql);
	return $product_id[0]['pos_product_id'];
}
function getProductColorID($pos_product_id, $color_code)
{
	$sql = "SELECT pos_product_color_id FROM pos_product_colors WHERE pos_product_id = '$pos_product_id' AND color_code = '$color_code'";
	$color_exists = getSQL($sql);
	if (sizeof($color_exists)==0)
	{
		return false;
	}
	else
	{
		return $color_exists[0]['pos_product_color_id'];
	}
}
function getProductIdFromProductSubId($pos_product_sub_id)
{
	$sql = "SELECT pos_product_id FROM pos_products_sub_id WHERE pos_product_sub_id = '$pos_product_sub_id'";
	return getSingleValueSQL($sql);
}
function getProductSubIds($pos_product_id)
{
	$sql = "SELECT * from pos_products_sub_id WHERE pos_product_id = '$pos_product_id'";
	return getSQL($sql);
}
function getProductSubID($product_subid_name)
{
	$sql = "SELECT pos_product_sub_id FROM pos_products_sub_id WHERE product_subid_name ='$product_subid_name'";
	$product_sub_id_exists = getSQL($sql);
	if (sizeof($product_sub_id_exists)==0)
	{
		return false;
	}
	else
	{
		return $product_sub_id_exists[0]['pos_product_sub_id'];
	}
}

function getUniqueWebProductColors($pos_product_id)
{
	/*return getSQL("SELECT *
			FROM pos_product_colors
			WHERE pos_product_id = '$pos_product_id' AND unique_web_product = 1");*/
	return getSQL("SELECT *
			FROM pos_product_options
			LEFT JOIN pos_product_attributes USING (pos_product_attribute_id)
			WHERE pos_product_id = '$pos_product_id' AND unique_web_product = 1 AND attribute_name = 'Color'");		
			
}
function getProductColorsFromAttribute($pos_product_id)
{
	$delimeter = "\r\n";
	$color_sql = "SELECT options FROM pos_products_attributes WHERE attribute_name = 'Color' AND pos_product_id ='$pos_product_id'";
	$options = getSQL($color_sql);
	if ($options[0]['options'] == NULL)
	{
		$colors = array();
	}
	else
	{
		$colors = explode($delimeter, $options[0]['options']);
	}
	return $colors;
}

function getProductRetail($pos_product_id)
{
	$sql = "SELECT retail_price FROM pos_products WHERE pos_product_id = $pos_product_id";
	return getSingleValueSQL($sql);
}
function getProductCost($pos_product_id)
{
	$sql = "SELECT cost FROM pos_products WHERE pos_product_id = $pos_product_id";
	return getSingleValueSQL($sql);
}

function getProductCategory($pos_product_id)
{
	$sql = "SELECT pos_category_id FROM pos_products WHERE pos_product_id = $pos_product_id";
	return getSingleValueSQL($sql);
}
function getProductCategoryName($pos_product_id)
{
	return getCategoryName(getProductCategory($pos_product_id));
}
function getAllProductCategories($pos_product_id)
{
	$categories = getFieldRowSQL("
	SELECT name FROM pos_products LEFT JOIN pos_categories USING (pos_category_id) WHERE pos_product_id = $pos_product_id
	UNION
	SELECT name FROM pos_product_secondary_categories LEFT JOIN pos_categories USING (pos_category_id) WHERE pos_product_id = $pos_product_id ");
	return implode('||', $categories['name']);
}
function getProductTitle($pos_product_id)
{
	$sql = "SELECT title FROM pos_products WHERE pos_product_id = $pos_product_id";
	return getSingleValueSQL($sql);
}
function getProductWeight($pos_product_id)
{
	$sql = "SELECT weight FROM pos_products WHERE pos_product_id = $pos_product_id";
	return getSingleValueSQL($sql);
}
function getProductPriority($pos_product_id)
{
	$sql = "SELECT priority FROM pos_products WHERE pos_product_id = $pos_product_id";
	return getSingleValueSQL($sql);
}
function getProductDescription($pos_product_id)
{
	$sql = "SELECT description FROM pos_products WHERE pos_product_id = $pos_product_id";
	return getSingleValueSQL($sql);
}
function getProductOverview($pos_product_id)
{
	$sql = "SELECT overview FROM pos_products WHERE pos_product_id = $pos_product_id";
	return getSingleValueSQL($sql);
}

function getProductName($pos_product_id)
{
	return getProductBrandName($pos_product_id) . '-' . getProductStyleNumber($pos_product_id) . '-'. getProductTitle($pos_product_id);
}
function getProductBrandID($pos_product_id)
{
	return getSingleValueSQL("SELECT pos_manufacturer_brand_id FROM pos_products WHERE pos_product_id = $pos_product_id");
}
function getProductBrandName($pos_product_id)
{
	return getSingleValueSQL("SELECT brand_name FROM pos_products LEFT JOIN pos_manufacturer_brands USING (pos_manufacturer_brand_id) WHERE pos_product_id = $pos_product_id");
}
function getProductBrandCode($pos_product_id)
{
	return getSingleValueSQL("SELECT brand_code FROM pos_products LEFT JOIN pos_manufacturer_brands USING (pos_manufacturer_brand_id) WHERE pos_product_id = $pos_product_id");
}
function checkAttributeExists($attribute, $pos_product_id)
{
	$attribute_exists_q = "SELECT pos_product_attribute_id FROM pos_products_attributes WHERE attribute_name = '$attribute' AND pos_product_id ='$pos_product_id'";
	$pos_product_attribute_id = getSQL($attribute_exists_q);
	if (sizeof($pos_product_attribute_id) == 0)
	{
		return false;
	}
	else
	{
		return $pos_product_attribute_id[0]['pos_product_attribute_id'];
	}
}
function getProductSizesOld($pos_product_id)
{
	$delimeter = "\r\n";
	$size_sql = "SELECT options FROM pos_products_attributes WHERE attribute_name = 'Size' AND pos_product_id ='$pos_product_id'";
	$options = getSQL($size_sql);
	if (sizeof($options) == 0)
	{
		$sizes = array('No Sizes');
	}
	else
	{
		$sizes = explode($delimeter, $options[0]['options']);
	}
	return $sizes;
}
function writeStringToAttribute($pos_product_attribute_id, $new_options)
{
	$id['pos_product_attribute_id'] = $pos_product_attribute_id;
	$mysql_data = array('options' => $new_options);
	$result = simpleUpdateSQL('pos_products_attributes', $id, $mysql_data);
	return $result;
}
function getOldProductAttributeId($pos_product_id, $attribute_name)
{
	$sql = "SELECT pos_product_attribute_id FROM pos_products_attributes WHERE pos_product_id = $pos_product_id AND attribute_name = '$attribute_name'";
	return getSingleValueSQL($sql);
}
function checkOrAppendStringToAttribute($pos_product_attribute_id, $attribute_string)
{
	$delimeter = "\r\n";
	$sql = "SELECT options FROM pos_products_attributes WHERE pos_product_attribute_id = '$pos_product_attribute_id'";
	$attribute_data = getSQL($sql);
	if ($attribute_data[0]['options'] == NULL)
	{
		$strArray = array();
	}
	else
	{
		$strArray = explode($delimeter, $attribute_data[0]['options']);
	}
	//echo "exploded array" . var_dump($strArray);
	if (!in_array($attribute_string, $strArray))
	{
		$strArray[] = $attribute_string;
	}
	return implode($strArray, $delimeter);
}
function createProductAttribute($name, $pos_product_id)
{
	$mysql_data = array('attribute_name' => $name,
	'caption' => $name,
	'pos_product_id' => $pos_product_id);
	return simpleInsertSQLReturnID('pos_products_attributes', $mysql_data);
}
function createUnSortedProductSize($pos_product_id, $size)
{
	$pos_product_attribute_id = checkAttributeExists('Size', $pos_product_id);
	if ($pos_product_attribute_id==false)
	{
		$pos_product_attribute_id = createProductAttribute('Size', $pos_product_id);
	}
	$unsorted_options = checkOrAppendStringToAttribute($pos_product_attribute_id, $size);
	writeStringToAttribute($pos_product_attribute_id, $unsorted_options);
	return $pos_product_attribute_id;
}
function createProductSize($pos_product_id, $size, $size_sort)
{
	$pos_product_attribute_id = checkAttributeExists('Size', $pos_product_id);
	if ($pos_product_attribute_id==false)
	{
		$pos_product_attribute_id = createProductAttribute('Size', $pos_product_id);
	}
	$unsorted_options = checkOrAppendStringToAttribute($pos_product_attribute_id, $size);
	//echo 'unsorted options ' . var_dump($unsorted_options);
	$sorted_options = sortSizes($unsorted_options, $size_sort);
	//echo 'sorted options ' .var_dump($sorted_options);
	writeStringToAttribute($pos_product_attribute_id, $sorted_options);
	return $pos_product_attribute_id;
}
function writeProductSizes($pos_product_attribute_id, $size_array)
{
	$delimeter = "\r\n";
	return writeStringToAttribute($pos_product_attribute_id, implode($size_array, $delimeter));
}
function generateCategoryLevel($parent_pos_category_id)
{
	$temp_id = $parent_pos_category_id;
	$level = 0;
	while($temp_id != 0)
	{
		$temp_id = getCategoryParent($temp_id);
		$level++;
	}
	return $level;	
}
function generateCategoryURL($name, $parent_pos_category_id)
{
	$full_name = $name.'/';
	$test_id = $parent_pos_category_id;
	while($test_id != 0)
	{
		$full_name = getCategoryName($test_id) . '/' .$full_name;
		$test_id = getCategoryParent($test_id);
	}
	return strtolower(str_replace(' ', '-', $full_name));
}
function generateDefaultProductPriority($pos_category_id)
{

	//get the default category priority
	$sql = "SELECT default_product_priority FROM pos_categories WHERE pos_category_id = $pos_category_id";
	$priority = getSingleValueSQL($sql);
	return $priority;
	//modify the priority by the date added?
	//newer products should be placed on the top of the category
	
}
function getProductColors($pos_product_id)
{
	$sql ="SELECT * FROM pos_product_colors WHERE pos_product_id =$pos_product_id";
	return getSQL($sql);
}

function getProductStyleNumber($pos_product_id)
{
	$sql ="SELECT style_number FROM pos_products WHERE pos_product_id = $pos_product_id";
	return getSingleValueSQL($sql);
}
function createProductSubIds($pos_product_id)
{
	$product = getProduct($pos_product_id);
	$pos_manufacturer_brand_id = $product[0]['pos_manufacturer_brand_id'];
	$brand_code = getProductBrandCode($pos_product_id);
	$style_number = getProductStyleNumber($pos_product_id);
	$colors = getProductColors($pos_product_id);
	$sizes = getProductSizesOld($pos_product_id);
	for ($clr = 0;$clr<sizeof($colors);$clr++)
	{
		for($sz=0;$sz<sizeof($sizes);$sz++)
		{
			$product_subid_name = $brand_code . '-' .$style_number .'-' .$colors[$clr]['color_code'].'-'.$sizes[$sz];
			
			if (getProductSubID($product_subid_name) == false)
			{
				$upc_code = getUPCCode(getManufacturerIDFromBrandId($pos_manufacturer_brand_id), $style_number, $colors[$clr]['color_code'], $sizes[$sz]);
				$pos_product_sub_id = addProductSubIDToPOS($pos_product_id,$product_subid_name, $upc_code);
			}
		}
	}
}
function updateProductRetailFromPO($pos_purchase_order_id)
{

	$poc = getPurchaseOrderContentsInArray($pos_purchase_order_id);
	$update_data = array();
	$update_data[0]['db_field'] = 'retail_price';
	$update_data[0]['id'] = 'pos_product_id';	
	for($i=0;$i<sizeof($poc);$i++)
	{
		/*
		$mysql_data_array[0]['db_field'] = 'cost';
		$mysql_data_array[0]['id'] = 'pos_purchase_order_content_id';
		$mysql_data_array[0]['data_array']['3789'] = 30.75;
		$mysql_data_array[0]['data_array']['3790'] = 40.75;
		$mysql_data_array[1]['db_field'] = 'retail';
		$mysql_data_array[1]['id'] = 'pos_purchase_order_content_id;
		$mysql_data_array[1]['data_array']['3789'] = 60.75;
		$mysql_data_array[1]['data_array']['3790'] = 80.75;
		*/
		if ($poc[$i]['pos_product_id'] != 0)
		{
			$update_data[0]['data_array'][$poc[$i]['pos_product_id']] = $poc[$i]['retail'];
		}
	}
	return arrayUpdateSQL('pos_products', $update_data);
}
function updateProductCostFromPO($pos_purchase_order_id)
{

	$poc = getPurchaseOrderContentsInArray($pos_purchase_order_id);
	$update_data = array();
	$update_data[0]['db_field'] = 'cost';
	$update_data[0]['id'] = 'pos_product_id';	
	for($i=0;$i<sizeof($poc);$i++)
	{
		/*
		$mysql_data_array[0]['db_field'] = 'cost';
		$mysql_data_array[0]['id'] = 'pos_purchase_order_content_id';
		$mysql_data_array[0]['data_array']['3789'] = 30.75;
		$mysql_data_array[0]['data_array']['3790'] = 40.75;
		$mysql_data_array[1]['db_field'] = 'retail';
		$mysql_data_array[1]['id'] = 'pos_purchase_order_content_id;
		$mysql_data_array[1]['data_array']['3789'] = 60.75;
		$mysql_data_array[1]['data_array']['3790'] = 80.75;
		*/
		if ($poc[$i]['pos_product_id'] != 0)
		{
			$update_data[0]['data_array'][$poc[$i]['pos_product_id']] = $poc[$i]['cost'];
		}
	}
	return arrayUpdateSQL('pos_products', $update_data);
}
function updateProductSizeChartFromPO($pos_purchase_order_id)
{
	$po_array = getPurchaseOrderDataInArray($pos_purchase_order_id);
	$pos_manufacturer_brand_id = $po_array[0]['pos_manufacturer_brand_id'];
	$poc = getPurchaseOrderContentsInArray($pos_purchase_order_id);
	$update_data = array();
	$update_data[0]['db_field'] = 'pos_manufacturer_brand_size_id';
	$update_data[0]['id'] = 'pos_product_id';	
	for($i=0;$i<sizeof($poc);$i++)
	{
		/*
		$mysql_data_array[0]['db_field'] = 'cost';
		$mysql_data_array[0]['id'] = 'pos_purchase_order_content_id';
		$mysql_data_array[0]['data_array']['3789'] = 30.75;
		$mysql_data_array[0]['data_array']['3790'] = 40.75;
		$mysql_data_array[1]['db_field'] = 'retail';
		$mysql_data_array[1]['id'] = 'pos_purchase_order_content_id;
		$mysql_data_array[1]['data_array']['3789'] = 60.75;
		$mysql_data_array[1]['data_array']['3790'] = 80.75;
		*/
		if ($poc[$i]['pos_product_id'] != 0)
		{
			$update_data[0]['data_array'][$poc[$i]['pos_product_id']] = getManufacturerBrandSizeRowID($pos_purchase_order_id, $poc[$i]['size_row']);
		}
	}
	return arrayUpdateSQL('pos_products', $update_data);
}
function writeUPCtoProductFromPOC($pos_purchase_order_id)
{
	$html ='The Following Products and Sub Products Were Generated & UPC codes were assigned if possible' .newline();
	$update_array = array();
	$po_array = getPurchaseOrderDataInArray($pos_purchase_order_id);
	$pos_manufacturer_brand_id = $po_array[0]['pos_manufacturer_brand_id'];
	$brand_code = getBrandCode($pos_manufacturer_brand_id);
	$size_data = loadStoredSizeChart($pos_purchase_order_id);
	$poc_array = getPurchaseOrderContentsInArray($pos_purchase_order_id);
	for ($i=0;$i<sizeof($poc_array);$i++)
	{
		$style_number = $poc_array[$i]['style_number'];
		$color_code =  $poc_array[$i]['color_code'];
		$color_description = $poc_array[$i]['color_description'];
		$size = $poc_array[$i]['size'].$poc_array[$i]['cup'].$poc_array[$i]['inseam'];
		$upc_code = getUPCCode(getManufacturerIDFromBrandId($pos_manufacturer_brand_id), $style_number, $color_code, $size);
		$result[] = updateProductUPC($poc_array[$i]['pos_product_sub_id'], $upc_code);
	}
	//return $result;
}
function writeProductsandSubProductsFromPOC($pos_purchase_order_id)
{
	$po_array = getPurchaseOrderDataInArray($pos_purchase_order_id);
	$pos_manufacturer_brand_id = $po_array[0]['pos_manufacturer_brand_id'];
	$brand_code = getBrandCode($pos_manufacturer_brand_id);
	$size_data = loadStoredSizeChart($pos_purchase_order_id);
	$poc_array = getPurchaseOrderContentsInArray($pos_purchase_order_id);
	$date = getDateTime();
	for ($i=0;$i<sizeof($poc_array);$i++)
	{
		//$pos_manufacturer_brand_size_id = getManufacturerBrandSizeRowID($pos_manufacturer_brand_size_id, $poc_array[$i]['size_row'];
		$color_code =  $poc_array[$i]['color_code'];
		$color_description = $poc_array[$i]['color_description'];
		$size = $poc_array[$i]['size'].$poc_array[$i]['cup'].$poc_array[$i]['inseam'];
		
		//this is coming from the purchase order contents
		$mysql_data = array('cost' => $poc_array[$i]['cost'],
							'title' => scrubInput($poc_array[$i]['title']),
							'style_number' => $poc_array[$i]['style_number'],
							'retail_price' => $poc_array[$i]['retail'],
							'pos_category_id' => $poc_array[$i]['pos_category_id'],
							'pos_sales_tax_category_id' => getDefaultCategorySalesTaxCategoryId($poc_array[$i]['pos_category_id']),
							'active' => 1,
							'pos_manufacturer_brand_id' => $pos_manufacturer_brand_id,
							'pos_manufacturer_brand_size_id' => getManufacturerBrandSizeRowID($pos_purchase_order_id, $poc_array[$i]['size_row']),
							'description' => 'New Product! Description is coming soon.',
							'added' => $date,
							'weight' => 1,
							'overview' => scrubInput(getBrandName($pos_manufacturer_brand_id) . ' ' . $poc_array[$i]['title']),
							'priority' => generateDefaultProductPriority($poc_array[$i]['pos_category_id'])
			);
			
		//the size chart will have the attributes used for ordering
		//the attributes will then be separated by ::
		//example
		//wolford with cup and inseam
		//attributes will be A::32
		//names should b cup::inseam
		$attribute_array = array();
		$attribute_values = array();
		if(isset($size_data['attributes']) && sizeof($size_data['attributes'])>0)
		{
			if (strpos($poc_array[$i]['attributes'], '::'))
			{
				$attribute_values = explode('::',$poc_array[$i]['attributes']);
			}
			else
			{
				$attribute_values[0] = $poc_array[$i]['attributes'];
			}
			$attribute_names = $size_data['attributes'];
			for($atr=0;$atr<sizeof($attribute_values);$atr++)
			{
				if($attribute_values[$atr] !='')
				{
					$attribute_array[$attribute_names[$atr]] = array('option_code' => $attribute_values[$atr], 'option_name' =>$attribute_values[$atr]);
				}
			}
			
		}
		
		$options_array = array('Color' => array('option_code' => $color_code, 'option_name' => $color_description),
				'Size' => array('option_code' => $size, 'option_name' => $size, 'size_sort' => $size_data['sizes'][$poc_array[$i]['size_row']]),
				'Cup' => array('option_code' => $poc_array[$i]['cup'], 'option_name' => $poc_array[$i]['cup']));
		$options_array = array_merge(	$options_array, 	$attribute_array);
				
		$pos_product_sub_id = fashionClothingProductCreator($mysql_data, $options_array);
		$result = writePurchaseOrderContentValue($poc_array[$i]['pos_purchase_order_content_id'], 'pos_product_sub_id', $pos_product_sub_id);
		
		/*
		if ($style_number != '')
		{
			$pos_product_id = getProductID($pos_manufacturer_brand_id, $style_number);
			if ($pos_product_id==false)
			{
				$pos_product_id = addPOCProductToPOS($poc_array[$i], $pos_manufacturer_brand_id, $pos_purchase_order_id);
			}
			$result = writePurchaseOrderContentValue($poc_array[$i]['pos_purchase_order_content_id'], 'pos_product_id', $pos_product_id);
			$result = setToActive('pos_products', 'pos_product_id', $pos_product_id);
			if ($color_code != '')
			{
				//data looks good, lets process
				//check and add the color
				$pos_product_color_id = checkAndCreateProductColor($pos_product_id, $color_code, $color_description);
				$pos_product_color_option_id = checkAndCreateProductOption($pos_product_id, 'Color', $color_code, $color_description, 1);
				//Now the sizes - size row may be undefined:
				if ($poc_array[$i]['size_row'] != 'undefined' && $size != '')
				{
					$pos_product_attribute_id = createProductSize($pos_product_id, $size, $size_data['sizes'][$poc_array[$i]['size_row']]);
					$pos_product_size_option_id = checkAndCreateProductOption($pos_product_id, 'Size', $size, $size, 0);
					
					//now create the sub id
					$pos_product_sub_id = getProductSubID($product_subid_name);
					if ($pos_product_sub_id==false)
					{
						$upc_code = getUPCCode(getManufacturerIDFromBrandId($pos_manufacturer_brand_id), $style_number, $color_code, $size);
						//do not change this list...
						$attributes = array('color_code' => $color_code, 'color_description' => $color_description, 'size' => $size);
						$pos_product_sub_id = addProductSubIDToPOS($pos_product_id,$product_subid_name, $upc_code, $attributes);
						//now link the sub id to the options
						
						linkSubIdToOption($pos_product_sub_id, $pos_product_color_option_id);
						linkSubIdToOption($pos_product_sub_id, $pos_product_size_option_id);
						
						$html .= "<p>Added Product-Sub-Id: " . getBrandCode($pos_manufacturer_brand_id) . "-" . $style_number ."-" . $color_code . "-" .$size ." With UPC Code: " .$upc_code."</p>".newline();
					}
					
					
				}
			}
		}*/
	}
	return 'Done';
}
function linkSubIdToOption($pos_product_sub_id, $pos_product_option_id)
{
	$current_options = getSingleValueSQL("SELECT pos_product_option_id FROM pos_product_sub_id_options WHERE pos_product_sub_id = $pos_product_sub_id AND pos_product_option_id = $pos_product_option_id");
	if($current_options == false)
	{
		$insert = array('pos_product_sub_id' => $pos_product_sub_id, 'pos_product_option_id' => $pos_product_option_id);
		simpleInsertSQL('pos_product_sub_id_options', $insert);
		return false;
	}
	else
	{
		return true;
	}
}
function linkSubIdToOptions($pos_product_sub_id, $pos_product_options)
{
	for($i=0;$i<sizeof($pos_product_options);$i++)
	{
		$pos_product_option_id = $pos_product_options[$i];
		$current_options = getSingleValueSQL("SELECT pos_product_option_id FROM pos_product_sub_id_options WHERE pos_product_sub_id = $pos_product_sub_id AND pos_product_option_id = $pos_product_option_id");
		if($current_options == false)
		{
			$insert = array('pos_product_sub_id' => $pos_product_sub_id, 'pos_product_option_id' => $pos_product_option_id);
			simpleInsertSQL('pos_product_sub_id_options', $insert);
			
		}
		else
		{
			
		}
	}
}
//old code here
function checkAndCreateProductColor($pos_product_id, $color_code, $color_description)
{
	$pos_product_color_id = getProductColorID($pos_product_id, $color_code);
	if ($pos_product_color_id == false)
	{
		//create the color
		$mysql_data=array('pos_product_id' => $pos_product_id, 'color_code' => $color_code, 'color_name' => $color_description, 'active' => 1, 'unique_web_product' => 1);
		$pos_product_color_id = simpleInsertSQLReturnID('pos_product_colors', $mysql_data);
		return $pos_product_color_id;
	}
	else
	{
		//update the description?
		$mysql_data=array('pos_product_id' => $pos_product_id,'color_name' => $color_description, 'active' => 1);
		$id['pos_product_color_id'] = $pos_product_color_id;
		simpleUpdateSQL('pos_product_colors', $id, $mysql_data);
		return $pos_product_color_id;		
	}
}
function getProductIDFromProductOptionId($pos_product_option_id)
{
	return getSingleValueSQL("SELECT pos_product_id FROM pos_product_options WHERE pos_product_option_id = $pos_product_option_id");
}
function getProductOptionCode($pos_product_sub_id, $pos_product_attribute_id)
{
	$data =  getSingleValueSQL("SELECT option_code FROM pos_product_options 
								LEFT JOIN pos_product_sub_id_options USING (pos_product_option_id)
								WHERE pos_product_attribute_id = $pos_product_attribute_id AND pos_product_sub_id = $pos_product_sub_id"); 
	if($data == false)
	{
		return '';
	}
	else
	{
		return $data;
	}
}
function getProductOptionName($pos_product_sub_id, $pos_product_attribute_id)
{
	return getSingleValueSQL("SELECT option_name FROM pos_product_options 
								LEFT JOIN pos_product_sub_id_options USING (pos_product_option_id)
								WHERE pos_product_attribute_id = $pos_product_attribute_id AND pos_product_sub_id = $pos_product_sub_id"); 
}
function getProductOptionIdFromProductSubId($pos_product_sub_id, $attribute_name)
{
	//to get color id from the subb id we need to look at the product options table
	
	//get the product id from the subif
	//get attribute_id
	$pos_product_attribute_id = getProductAttributeId($attribute_name);
	
	$sql = "SELECT pos_product_options.pos_product_option_id FROM pos_product_sub_id_options
	LEFT JOIN pos_product_options ON pos_product_options.pos_product_option_id = pos_product_sub_id_options.pos_product_option_id
	LEFT JOIN pos_product_attributes ON pos_product_options.pos_product_attribute_id
	
	WHERE pos_product_sub_id = $pos_product_sub_id AND pos_product_attributes.pos_product_attribute_id = $pos_product_attribute_id";
	return getsinglevaluesql($sql);
	
}
function getProductOptionId($pos_product_id, $pos_product_attribute_id, $option_code)
{
	return getSingleValueSQL("SELECT pos_product_option_id FROM pos_product_options WHERE pos_product_id = $pos_product_id AND pos_product_attribute_id= $pos_product_attribute_id AND option_code = '$option_code'");
}
function checkIfProductSubIdOptionExists($pos_product_sub_id, $pos_product_option_id)
{
	return getSingleValueSQL("SELECT pos_product_option_id FROM pos_product_sub_id_options WHERE pos_product_sub_id = $pos_product_sub_id AND pos_product_option_id=$pos_product_option_id");
}
function getProductAttributeName($pos_product_attribute_id)
{
	$sql = "SELECT attribute_name FROM pos_product_attributes WHERE pos_product_attribute_id = '$pos_product_attribute_id'";
	return getSingleValueSQL($sql);
}
function getProductAttributeId($attribute_name)
{
	$sql = "SELECT pos_product_attribute_id FROM pos_product_attributes WHERE attribute_name = '$attribute_name'";
	return getSingleValueSQL($sql);
}
function checkAndCreateProductColorOption($pos_product_id, $attribute_name, $option_code, $option_name, $unique_web_product)
{
	$pos_product_attribute_id = getProductAttributeId($attribute_name);
	$pos_product_option_id = getProductOptionId($pos_product_id, $pos_product_attribute_id, $option_code);
	if ($pos_product_option_id == false)
	{
		$mysql_data=array('pos_product_attribute_id' => $pos_product_attribute_id, 'pos_product_id' => $pos_product_id, 'option_code' => $option_code, 'option_name' => $option_name, 'active' => 1, 'unique_web_product' => $unique_web_product);
		$pos_product_option_id = simpleInsertSQLReturnID('pos_product_options', $mysql_data);
		
	}
	else
	{
			//	do nothing		
			//if a po or bulk product creation description is here it will not update the product..
	}
	return $pos_product_option_id;
}
function checkAndCreateProductSizeOption($pos_product_id, $attribute_name, $option_code, $option_name, $size_sort)
{

	$sort_index = array_search($option_code, $size_sort);
	//the size needs a sort index to make any sense
	$pos_product_attribute_id = getProductAttributeId($attribute_name);
	$pos_product_option_id = getProductOptionId($pos_product_id, $pos_product_attribute_id, $option_code);
	if ($pos_product_option_id == false)
	{
		$mysql_data=array('sort_index' => $sort_index, 'pos_product_attribute_id' => $pos_product_attribute_id, 'pos_product_id' => $pos_product_id, 'option_code' => $option_code, 'option_name' => $option_name, 'active' => 1);
		$pos_product_option_id = simpleInsertSQLReturnID('pos_product_options', $mysql_data);
		
	}
	else
	{
			//	do nothing		
			//if a po or bulk product creation description is here it will not update the product..
	}
	
		
	//now sorting....
	//preprint($size_sort);
	//$unsorted_sizes = getProductSizes($pos_product_id);
	//preprint($unsorted_sizes);
	
	
	
	
	return $pos_product_option_id;
}
function checkAndCreateProductOption($pos_product_id, $attribute_name, $option_code, $option_name, $unique_web_product)
{
	$pos_product_attribute_id = getProductAttributeId($attribute_name);
	$pos_product_option_id = getProductOptionId($pos_product_id, $pos_product_attribute_id, $option_code);
	if ($pos_product_option_id == false)
	{
		$mysql_data=array('pos_product_attribute_id' => $pos_product_attribute_id, 'pos_product_id' => $pos_product_id, 'option_code' => $option_code, 'option_name' => $option_name, 'active' => 1, 'unique_web_product' => $unique_web_product);
		$pos_product_option_id = simpleInsertSQLReturnID('pos_product_options', $mysql_data);
		
	}
	else
	{
			//	do nothing		
			//if a po or bulk product creation description is here it will not update the product..
	}
	

	
	
	return $pos_product_option_id;
}
function addProductSubIDToPOS($pos_product_id,$product_subid_name, $manufacturer_upc, $attributes)
{
	$attribute_array = array();
	foreach($attributes as $key => $value)
	{
		$attribute_array[] = $key .'::'.$value;
	}
	$attribute_string = implode(newline(),$attribute_array);
	$mysql_data=array('pos_product_id'=>$pos_product_id, 'product_subid_name' =>$product_subid_name, 'product_upc' => $manufacturer_upc, 'active' => 1, 'attributes_list' => $attribute_string);
	
	//final check => if the name is already in there should we update it? yes for now
	$pos_product_sub_id = getProductSubId($product_subid_name);
	if($pos_product_sub_id)
	{
		simpleUpdateSQL('pos_products_sub_id', array('pos_product_sub_id' => $pos_product_sub_id), $mysql_data);
	}
	else
	{
		$pos_product_sub_id = simpleInsertSQLReturnID('pos_products_sub_id', $mysql_data);
		
	}
	//set the barcode to the sub_id
	runSQL("UPDATE pos_products_sub_id SET barcode = $pos_product_sub_id WHERE pos_product_sub_id = $pos_product_sub_id");
	return $pos_product_sub_id;
}
function updateProductUPC($pos_product_sub_id, $manufacturer_upc)
{
	$update_array['product_upc'] = $manufacturer_upc;
	$key_val_id['pos_product_sub_id'] = $pos_product_sub_id;
	return simpleUpdateSQL('pos_products_sub_id', $key_val_id, $update_array);
}
function addPOCProductToPOS($product_array, $pos_manufacturer_brand_id, $pos_purchase_order_id)
{
	//this is coming from the purchase order contents
	$date = getDateTime();
	$mysql_data = array('cost' => $product_array['cost'],
	'title' => scrubInput($product_array['title']),
	'style_number' => $product_array['style_number'],
	'retail_price' => $product_array['retail'],
	'pos_category_id' => $product_array['pos_category_id'],
	'pos_sales_tax_category_id' => getDefaultCategorySalesTaxCategoryId($product_array['pos_category_id']),
	'active' => 1,
	'pos_manufacturer_brand_id' => $pos_manufacturer_brand_id,
	'pos_manufacturer_brand_size_id' => getManufacturerBrandSizeRowID($pos_purchase_order_id, $product_array['size_row']),
	'description' => 'New Product! Description is coming soon.',
	'added' => $date,
	'weight' => 1,
	'overview' => scrubInput(getBrandName($pos_manufacturer_brand_id) . ' ' . $product_array['title']),
	'priority' => generateDefaultProductPriority($product_array['pos_category_id'])
	);
	
	return simpleInsertSQLReturnID('pos_products', $mysql_data);
}


function getProductsandSubProductsFromPOC($pos_purchase_order_id)
{
	$purchase_order_contents_sql = "
		SELECT pos_purchase_order_contents.pos_purchase_order_content_id ,pos_products_sub_id.pos_product_id, pos_purchase_order_contents.style_number, pos_purchase_order_contents.color_description, pos_purchase_order_contents.color_code, pos_purchase_order_contents.pos_product_sub_id,  pos_products_sub_id.product_subid_name, pos_products_sub_id.product_upc
		FROM pos_purchase_order_contents LEFT JOIN pos_products_sub_id
		ON pos_products_sub_id.pos_product_sub_id = pos_purchase_order_contents.pos_product_sub_id
		WHERE pos_purchase_order_id = '$pos_purchase_order_id' AND pos_purchase_order_contents.quantity_ordered > 0
		ORDER BY pos_products_sub_id.pos_product_sub_id ASC";
	$contents=  getSQL($purchase_order_contents_sql);
	for($i=0;$i<sizeof($contents);$i++)
	{
		$contents[$i]['size'] = getPOCSize($contents[$i]['pos_purchase_order_content_id']);
	}
	return $contents;
	
}	
function getProductSubIDFromName($product_subid_name)
{
	$sql = "SELECT pos_product_sub_id FROM pos_products_sub_id WHERE product_subid_name = '$product_subid_name'";
	$result = getSQL($sql);
	return $result[0]['pos_product_sub_id'];
}
function getProductSubIDName($pos_product_sub_id)
{
	$sql = "SELECT product_subid_name FROM pos_products_sub_id WHERE pos_product_sub_id = '$pos_product_sub_id'";
	return getSingleValueSQL($sql);
}
function getProductSubIDFROMPOC($pos_purchase_order_content_id)
{
	$sql = "SELECT pos_products_sub_id.product_subid_name FROM pos_purchase_order_contents 
	LEFT JOIN pos_products_sub_id
	ON pos_products_sub_id.pos_product_sub_id = pos_purchase_order_contents.pos_product_sub_id
	WHERE pos_purchase_order_content_id = '$pos_purchase_order_content_id'";
	$result=getSQL($sql);
	return $result[0]['product_subid_name'];
}
function getTransactionProductSubIDFROMPOC($dbc, $pos_purchase_order_content_id)
{
	$sql = "SELECT pos_products_sub_id.product_subid_name FROM pos_purchase_order_contents 
	LEFT JOIN pos_products_sub_id
	ON pos_products_sub_id.pos_product_sub_id = pos_purchase_order_contents.pos_product_sub_id
	WHERE pos_purchase_order_content_id = '$pos_purchase_order_content_id'";
	$result=getTransactionSQL($dbc, $sql);
	return $result[0]['product_subid_name'];
}
function getTransactionProductSubIDFromName($dbc, $product_subid_name)
{
	$sql = "SELECT pos_product_sub_id FROM pos_products_sub_id WHERE product_subid_name = '$product_subid_name'";
	$result = getTransactionSQL($dbc, $sql);
	return $result[0]['pos_product_sub_id'];
}
function getPurchaseOrderProductLinks($pos_purchase_order_id)
{
	// This function will return the product ID , the product sub id, the the mfg barcode
	$po_array = getPurchaseOrderDataInArray($pos_purchase_order_id);
	$pos_manufacturer_brand_id = $po_array[0]['pos_manufacturer_brand_id'];
	$brand_code = getBrandCode($pos_manufacturer_brand_id);
	$size_data = loadStoredSizeChart($pos_purchase_order_id);
	$poc_array = getPurchaseOrderContentsInArray($pos_purchase_order_id);
	$return_array = array();
	for ($i=0;$i<sizeof($poc_array);$i++)
	{
		$return_array[$i]['pos_purchase_order_content_id'] = $poc_array[$i]['pos_purchase_order_content_id'];
		$return_array[$i]['style_number'] = $poc_array[$i]['style_number'];
		$return_array[$i]['color_code'] =  $poc_array[$i]['color_code'];
		$return_array[$i]['size'] = $poc_array[$i]['size'].$poc_array[$i]['cup'].$poc_array[$i]['inseam'];
		$return_array[$i]['size_array'] = getManufacturerBrandSizesForSizeRow($pos_purchase_order_id, $poc_array[$i]['size_row']);
		$return_array[$i]['title'] = $poc_array[$i]['title'];
		$return_array[$i]['color_description'] = $poc_array[$i]['color_description'];
		if ($return_array[$i]['style_number'] != '')
		{
			$pos_product_id = getProductID($pos_manufacturer_brand_id, $return_array[$i]['style_number']);
			if ($pos_product_id)
			{
				$return_array[$i]['Product Creation'] = 'Existing Product';
				$return_array[$i]['pos_product_id'] = $pos_product_id;
				//sub id's?
				if ($return_array[$i]['color_code'] != '' && $return_array[$i]['size'] != '')
				{
					//get each product_option_id
					$size = $return_array[$i]['size'];
					$color_code = $return_array[$i]['color_code'];
					$pos_product_size_option_id = getProductOptionId($pos_product_id, getProductAttributeId('Size'), $size);
					$pos_product_color_option_id = getProductOptionId($pos_product_id, getProductAttributeId('Color'), $color_code);
					
					
					if($pos_product_color_option_id && $pos_product_size_option_id)
					{
						$pos_product_option_ids = array();
						$pos_product_option_ids[] = $pos_product_color_option_id;
						$pos_product_option_ids[] = $pos_product_size_option_id;
						$pos_product_sub_id = getProductSubIdFromOptionIds($pos_product_option_ids);
					}
					else
					{
						$pos_product_sub_id = false;
					}
					
				
					if ($pos_product_sub_id != false)
					{
						$return_array[$i]['Product Sub Id Creation'] = 'Existing Subid';
						$return_array[$i]['pos_product_sub_id'] = $pos_product_sub_id;
						$return_array[$i]['existing_product_subid_name'] = getProductSubIdName($pos_product_sub_id);
					}
					else
					{
						$return_array[$i]['Product Sub Id Creation'] = 'New Subid';
						$return_array[$i]['pos_product_sub_id'] = 0;
						$return_array[$i]['new_product_subid_name'] = $brand_code.'::'.$return_array[$i]['style_number'].'::'.$return_array[$i]['color_code'].'::'. $return_array[$i]['size'];
					}	
				}
				else
				{
					
					$return_array[$i]['Product Sub Id Creation'] = '<span class="error" >No Sub Id Will Be Created</span>';
				}	
			}
			else
			{
				$return_array[$i]['Product Creation'] = 'New Product';
				$return_array[$i]['pos_product_id'] = 0;
				$return_array[$i]['Product Sub Id Creation'] = 'New Subid Will Be Created';
				$return_array[$i]['pos_product_sub_id'] = 0;
				$return_array[$i]['new_product_subid_name'] = $brand_code.'::'.$return_array[$i]['style_number'].'::'.$return_array[$i]['color_code'].'::'. $return_array[$i]['size'];
			}

		}
		else
		{
			$return_array[$i]['Product Creation'] = '<span class = "error"> No Product Will Be Created</span>	';
		}
		$return_array[$i]['mfg_upc'] = getUPCCode(getManufacturerIDFromBrandId($pos_manufacturer_brand_id), $return_array[$i]['style_number'], $return_array[$i]['color_code'], $return_array[$i]['size']);		
	}
	return $return_array;
}
function getUPCFromPOCFromPOC($pos_purchase_order_id)
{
	// This function will return the product ID , the product sub id, the the mfg barcode
	$po_array = getPurchaseOrderDataInArray($pos_purchase_order_id);
	$pos_manufacturer_brand_id = $po_array[0]['pos_manufacturer_brand_id'];
	$brand_code = getBrandCode($pos_manufacturer_brand_id);
	$size_data = loadStoredSizeChart($pos_purchase_order_id);
	$poc_array = getPurchaseOrderContentsInArray($pos_purchase_order_id);
	$return_array = array();
	for ($i=0;$i<sizeof($poc_array);$i++)
	{
		$return_array[$i]['System Id'] = $poc_array[$i]['pos_purchase_order_content_id'];
		$return_array[$i]['Style Number'] = $poc_array[$i]['style_number'];
		$return_array[$i]['Color Code'] =  $poc_array[$i]['color_code'];
		$return_array[$i]['Size'] = $poc_array[$i]['size'].$poc_array[$i]['cup'].$poc_array[$i]['inseam'];
		$return_array[$i]['size_array'] = getManufacturerBrandSizesForSizeRow($pos_purchase_order_id, $poc_array[$i]['size_row']);
		$return_array[$i]['Sub Id Name'] = $brand_code.'-'.$return_array[$i]['Style Number'].'-'.$return_array[$i]['Color Code'].'-'. $return_array[$i]['Size'];
		$return_array[$i]['UPC Available'] = getUPCCode(getManufacturerIDFromBrandId($pos_manufacturer_brand_id), $return_array[$i]['Style Number'], $return_array[$i]['Color Code'], $return_array[$i]['Size']);		
	}
	return $return_array;
}
function checkifProductPricingShouldBeUpdatedFromPO($pos_purchase_order_id)
{
	$po_array = getPurchaseOrderDataInArray($pos_purchase_order_id);
	$poc_array = getPurchaseOrderContentsInArray($pos_purchase_order_id);
	$update_cost = false;
	$update_retail = false;
	$update_array = array();
	for ($i=0;$i<sizeof($poc_array);$i++)
	{
			$pos_product_id = getProductID($po_array[0]['pos_manufacturer_brand_id'], $poc_array[$i]['style_number']);
			if ($pos_product_id!=false)
			{
				$product = getProduct($pos_product_id);
				if ($product[0]['cost'] != $poc_array[$i]['cost'])
				{
					$update_cost = true;
					$update_array['cost'][$i] = true;
				}
				else
				{
					$update_array['cost'][$i] = false;
				}
				if ($product[0]['retail_price'] != $poc_array[$i]['retail'])
				{
					$update_retail = true;
					$update_array['retail'][$i] = true;
				}
				else
				{
					$update_array['retail'][$i] = false;
				}
			}
		
	}
	return array('update_retail' => $update_retail, 'update_cost' => $update_cost, 'update_array' => $update_array);
}

function getPurchaseOrderContentsLimitedByStyleNumber($pos_purchase_order_id)
{
	$contents = "SELECT DISTINCT style_number, title, cost, pos_products_sub_id.pos_product_id FROM pos_purchase_order_contents LEFT JOIN pos_products_sub_id USING(pos_product_sub_id) WHERE pos_purchase_order_id = $pos_purchase_order_id";
	return getSQL($contents);
}
function getProductSubIdsFromPurchaseOrder($pos_purchase_order_id)
{
	return getSQL("SELECT pos_purchase_order_content_id, pos_product_sub_id FROM pos_purchase_order_contents 
					 
					WHERE pos_purchase_order_id = $pos_purchase_order_id");
}

function deleteProduct($dbc, $pos_product_id)
{
	
	//this is called when an error is made that generated a batch of bad products....
	//check nothing links to the product first....
	
	$sql1 = "DELETE FROM pos_product_sub_ids WHERE pos_product_id=$pos_product_id";
	runTransactionSQL($dbc, $sql1);
	$sql1 = "DELETE FROM pos_product_colors WHERE pos_product_id=$pos_product_id";
	runTransactionSQL($dbc, $sql2);
	$sql1 = "DELETE FROM pos_product_options WHERE pos_product_id=$pos_product_id";
	runTransactionSQL($dbc, $sql3);
	$sql1 = "DELETE FROM pos_products_attributes WHERE pos_product_id=$pos_product_id";
	runTransactionSQL($dbc, $sql4);
	$sql1 = "DELETE FROM pos_products WHERE pos_product_id=$pos_product_id";
	runTransactionSQL($dbc, $sql5);
	
	

}
function deleteProductSubID($dbc, $pos_product_sub_id)
{
	
	//this is called when an error is made that generated a batch of bad products....
	//check nothing links to the product first....
	
	$sql1 = "DELETE FROM pos_product_sub_ids WHERE pos_product_sub_id=$pos_product_sub_id";
	runTransactionSQL($dbc, $sql1);

	$sql1 = "DELETE FROM pos_product_options WHERE pos_product_id=$pos_product_id";
	runTransactionSQL($dbc, $sql3);
	$sql1 = "DELETE FROM pos_products_attributes WHERE pos_product_id=$pos_product_id";
	runTransactionSQL($dbc, $sql4);
	$sql1 = "DELETE FROM pos_products WHERE pos_product_id=$pos_product_id";
	runTransactionSQL($dbc, $sql5);
	
	

}
function getBrandFromProductId($pos_product_id)
{
	return getSingleValueSQL("SELECT pos_manufacturer_brand_id FROM pos_products WHERE pos_product_id = $pos_product_id");
}
function getStyleNumber($pos_product_id)
{
	return getSingleValueSQL("SELECT style_number FROM pos_products WHERE pos_product_id = $pos_product_id");
}
function getColorDescription($pos_product_id, $color_code)
{
	$sql = "SELECT color_name FROM pos_product_colors WHERE pos_product_id = $pos_product_id AND color_code = '$color_code'";
	return getsinglevalueSQL($sql);
}

/**********************************INVENTORY*************************************/
function getSimpleAvailableInventoryQTYInStore($pos_product_sub_id, $pos_store_id)
{
	$sql="SELECT available_qty FROM pos_merchandise_inventory_simple WHERE pos_store_id = '$pos_store_id' AND pos_product_sub_id = '$pos_product_sub_id'";
	$qty_result = getSQL($sql);
	if (sizeof($qty_result)>0)
	{
		return $qty_result[0]['available_qty'];
	}
	else
	{
		return 0;
	}
}	




function getOnOrderStockDeliveryDate($pos_store_id, $pos_product_sub_id)
{
	$qty_on_order = getOnOrderStock($pos_store_id, $pos_product_sub_id);
	if ($qty_on_order > 0)
	{
		$sql = "
	SELECT MIN(pos_purchase_orders.delivery_date) AS delivery_date
	FROM pos_purchase_order_contents 
	LEFT JOIN pos_purchase_orders
	ON pos_purchase_orders.pos_purchase_order_id = pos_purchase_order_contents.pos_purchase_order_id
	WHERE pos_purchase_order_contents.pos_product_sub_id = $pos_product_sub_id AND pos_purchase_orders.pos_store_id='$pos_store_id' AND pos_purchase_order.purchase_order_status = 'OPEN'
	";
		$result = getSQL($sql);
		if ($result[0]['delivery_date'] == NULL )
		{
			return '';
		}
		else
		{
			return $result[0]['delivery_date'];
		}	
	}
	else
	{
		return '';
	}
}
function getOnOrderStock($pos_store_id, $pos_product_sub_id)
{
	$sql = "
	SELECT SUM(pos_purchase_order_contents.quantity_ordered - pos_purchase_order_contents.quantity_canceled - pos_purchase_order_contents.quantity_damaged) - 
	sum((SELECT sum(pos_purchase_order_receive_contents.received_quantity) FROM pos_purchase_order_receive_contents
			LEFT JOIN  pos_purchase_order_receive_contents USING (pos_purchase_order_content_id)
			WHERE pos_purchase_order_contents.pos_purchase_order_content_id = pos_purchase_order_receive_contents.pos_purchase_order_content_id))
	

	
	AS quantity_ordered
	FROM pos_purchase_order_contents 
	LEFT JOIN pos_purchase_orders
	ON pos_purchase_orders.pos_purchase_order_id = pos_purchase_order_contents.pos_purchase_order_id
	WHERE pos_purchase_order_contents.pos_product_sub_id = $pos_product_sub_id AND pos_purchase_orders.pos_store_id='$pos_store_id'";
	
	$result = getSQL($sql);
	if ($result[0]['quantity_ordered'] == NULL )
	{
		return 0;
	}
	else
	{
		return $result[0]['quantity_ordered'];
	}	
}
function getProductSubIdColorDescription($pos_product_sub_id)
{
	$attributes = getProductSubIdAttributes($pos_product_sub_id);
	return $attributes['color_description'];

}
function getProductSubIdColorCode($pos_product_sub_id)
{
	$attributes = getProductSubIdAttributes($pos_product_sub_id);
	return $attributes['color_code'];
}
function getProductSubIdSize($pos_product_sub_id)
{
	$attributes = getProductSubIdAttributes($pos_product_sub_id);
	return $attributes['size'];
}
function getProductSubIdAttributes($pos_product_sub_id)
{
	$data = getSQL("SELECT * FROM pos_products_sub_id WHERE pos_product_sub_id = $pos_product_sub_id");
	$attribute_list =  $data[0]['attributes_list'];
	$attributes = explode(newline(),$attribute_list);
	$all_attributes = array();
	for($i=0;$i<sizeof($attributes);$i++)
	{
		$att_arr = explode('::', $attributes[$i]);
		$all_attributes[$att_arr[0]] = $att_arr[1];
	}
	return $all_attributes;
}
function printProductLabelsForm($data, $filename)
{
		//pass in row number, subid, subidname, quantity
		$array_table_def= array(	
			
					/*array(
							'th' =>  'Print',
							'db_field' => 'row_checkbox',
							'th_width' => "15",
							'type' => 'row_checkbox',
							'value' =>1
							),*/
					array(
							'th' => 'Row',
							'db_field' => 'row_number',
							'type' => 'row_number'),
					array(
							'th' => 'System ID',
							'db_field' => 'pos_product_sub_id',
							'type' => 'td_hidden_input'),
					array(
							'th' => 'Subid Name <br>(Barcode)',
							'db_field' => 'product_subid_name',
							'type' => 'td'),

					array(	'th' => 'Quantity',
							'db_field' => 'quantity',
							'type' => 'input',
							'round' => 0,
							'tags' => ' style="background-color:yellow" ')
					);
		$html = '<p>Select the Labels Needed For The Newly Added Products</p>';
		$form_handler = POS_ENGINE_URL . '/products/PrintLabels/print_labels.form.handler.php';
		$html .= '<form action="' . $form_handler.'" method="post">';
		
		$html .= 'Starting column: <INPUT TYPE="TEXT" value="1" class="lined_input"  '. numbersOnly() . ' id="column_offset" style = "width:20px;" NAME="column_offset"/>'.newline();
		$html .= 'Starting row: <INPUT TYPE="TEXT" value="1" class="lined_input"  '. numbersOnly() . ' id="row_offset" style = "width:20px;" NAME="row_offset"/>'.newline();
		$html .= createStaticArrayHTMLTable($array_table_def, $data);
		$html .= createHiddenInput('filename', $filename);
		$html .= '<input class = "button" style="width:150px" type="submit" name="select" value="Open Label File"/>';
		$html .= '</form>';
		
		return $html;
}
function fashionClothingProductCreator($product_array, $options_array)
{
	//this creates a full fashion product with color and size
	
	//the option array should look like this:
	//array('Color' => array('option_code' => 'Blue', 'option_name' => 'Blueberry', etc
	$delimeter = '-';
	$pos_manufacturer_brand_id = $product_array['pos_manufacturer_brand_id'];
	$pos_category_id = $product_array['pos_category_id'];
	$style_number= $product_array['style_number'];
	$brand_code = getBrandCode($pos_manufacturer_brand_id);
	
	$color_code = scrubInput($options_array['Color']['option_code']);
	$color_description =scrubInput($options_array['Color']['option_name']);
	$size =$options_array['Size']['option_code'];
	$cup = (isset($options_array['Cup']['option_code'])) ? $options_array['Cup']['option_code'] : '';
	$size_sort = (isset($options_array['Size']['size_sort'])) ? $options_array['Size']['size_sort'] : array();
	
	//set the sub id name here, however we shoul donly check if the sub-id for the color and size attribute exist, not based on name.....
	//$pos_product_sub_id = getProductSubID($product_subid_name);	
	// to get to the sub id, we need to know the color code, the size, the product id, and the manufacturer id....but that is what all of this is for....
	if ($style_number != '')
	{
		$pos_product_id = getProductID($pos_manufacturer_brand_id, $style_number);
		//because this might get 'unlinked' to the sub id, we want to push this product id to the sub id..
		if ($pos_product_id==false)
		{						
				//add some other things in I usually forget
			$product_array['weight'] = (isset($product_array['weight'])) ? $product_array['weight'] : 1;
			$product_array['active'] = (isset($product_array['active'])) ? $product_array['active'] : 1;
			$product_array['pos_sales_tax_category_id'] = (isset($product_array['pos_sales_tax_category_id'])) ? $product_array['pos_sales_tax_category_id'] : getDefaultCategorySalesTaxCategoryId($product_array['pos_category_id']);
			$product_array['description'] = (isset($product_array['description'])) ? $product_array['description'] : 'New Product! Description is coming soon.';
			$product_array['overview'] = (isset($product_array['overview'])) ? $product_array['overview'] : scrubInput(getBrandName($product_array['pos_manufacturer_brand_id']) . ' ' . $product_array['title']);
			$product_array['priority'] = (isset($product_array['priority'])) ? $product_array['priority'] : generateDefaultProductPriority($product_array['pos_category_id']);
			
			
			$pos_product_id = simpleInsertSQLReturnID('pos_products', $product_array);
		}
		if ($color_code != '')
		{
			//this next line should be going away...
			$pos_product_color_id = checkAndCreateProductColor($pos_product_id, $color_code, $color_description);
			$pos_product_option_ids[] = checkAndCreateProductColorOption($pos_product_id, 'Color', $color_code, $color_description, 1);
			//Now the sizes may be undefined:
			if ($size != '')
			{
				//the next line should be going away
				$pos_product_attribute_id = createUnSortedProductSize($pos_product_id, $size);
				//size sorting?
				$pos_product_option_ids[] = checkAndCreateProductSizeOption($pos_product_id, 'Size', $size, $size, $size_sort);
				
				if ($cup !='')
				{
					$pos_product_option_ids[] = checkAndCreateProductOption($pos_product_id, 'Cup', $cup, $cup, 0);
				}
				
				foreach($options_array as $key => $value)
				{
				
					if($key == 'Size')
					{
					}
					elseif ($key == 'Cup')
					{
					}
					elseif ($key == 'Color')
					{
					}
					else
					{
						$pos_product_option_ids[] = checkAndCreateProductOption($pos_product_id, $key, $value['option_name'], $value['option_name'], 0);
					}
				}
				
				
				
				///all of the options should now be created with a value...
				
				
				//if($pos_product_color_option_id && $pos_product_size_option_id)
				//{
					//$pos_product_option_ids = array();
					//$pos_product_option_ids[] = $pos_product_color_option_id;
					//$pos_product_option_ids[] = $pos_product_size_option_id;
					$pos_product_sub_id = getProductSubIdFromOptionIds($pos_product_option_ids);
				/*}
				else
				{
					$pos_product_sub_id = false;
				}*/
				if ($pos_product_sub_id==false)
				{
					$product_subid_name = $brand_code.$delimeter.$style_number.$delimeter.$color_code.$delimeter. $size;

					foreach($options_array as $key => $value)
					{
				
						if($key == 'Size')
						{
						}
						elseif ($key == 'Cup')
						{
						}
						elseif ($key == 'Color')
						{
						}
						else
						{
							if($value['option_name'] != '')
							{
								$product_subid_name .= $delimeter.$value['option_name'];
							}
						}
					}
					$upc_code = getUPCCode(getManufacturerIDFromBrandId($pos_manufacturer_brand_id), $style_number, $color_code, $size);
					//this order is super important for the list to work.... otherwise we need to add  \r\n after size and it will be safer....
					$attributes = array('color_code' => $color_code, 'color_description' => $color_description, 'size' => $size);
					$pos_product_sub_id = addProductSubIDToPOS($pos_product_id,$product_subid_name, $upc_code, $attributes);					
					linkSubIdToOptions($pos_product_sub_id, $pos_product_option_ids);

					//linkSubIdToOption($pos_product_sub_id, $pos_product_color_option_id);
					//linkSubIdToOption($pos_product_sub_id, $pos_product_size_option_id);
				}
				else
				{
				 	//does it have the correct information?
				 	$current_product_id = getSingleValueSQL("SELECT pos_product_id FROM pos_products_sub_id WHERE pos_product_sub_id = $pos_product_sub_id");
				 	if ($pos_product_id != $current_product_id)
				 	{
				 		$upadte_array = array('pos_product_id' => $pos_product_id);
				 		simpleUpdateSQL('pos_products_sub_id',  array('pos_product_sub_id'=>$pos_product_sub_id), $upadte_array);
				 	}
				 	//link it any way??
				 	linkSubIdToOptions($pos_product_sub_id, $pos_product_option_ids);
				 	//linkSubIdToOption($pos_product_sub_id, $pos_product_color_option_id);
					//linkSubIdToOption($pos_product_sub_id, $pos_product_size_option_id);
					
					//update upc if different
					$upc_code = getUPCCode(getManufacturerIDFromBrandId($pos_manufacturer_brand_id), $style_number, $color_code, $size);
					$existing_upc_code = getSingleValueSQL("SELECT product_upc FROM pos_products_sub_id WHERE pos_product_sub_id = $pos_product_sub_id");
					
					if($existing_upc_code != $upc_code)
					{
						$sql = "UPDATE pos_products_sub_id SET product_upc = '$upc_code'";
						runSQL($sql);
					}
				 	
				 	
				}
			}
		}
	}
	return $pos_product_sub_id;
}
function getProductSubIdOptionsList($pos_product_sub_id)
{
	

	
	$sql="	SELECT group_concat(concat(attribute_name,':',option_name) SEPARATOR ':') 
			FROM pos_product_sub_id_options 
			LEFT JOIN pos_product_options ON pos_product_sub_id_options.pos_product_option_id = pos_product_options.pos_product_option_id 
			LEFT JOIN pos_product_attributes ON pos_product_options.pos_product_attribute_id = pos_product_attributes.pos_product_attribute_id
			WHERE pos_product_sub_id_options.pos_product_sub_id = $pos_product_sub_id";
	
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,'SET SESSION group_concat_max_len=1000');
	$data = getTransactionSingleValueSQL($dbc,$sql);
	closeDB($dbc);
	return $data;
	

	
	
}
function getProductSubIdOptionsListNoDescription($pos_product_sub_id)
{
	
	
	$sql="	SELECT group_concat(option_name SEPARATOR ',') 
			FROM pos_product_sub_id_options 
			LEFT JOIN pos_product_options ON pos_product_sub_id_options.pos_product_option_id = pos_product_options.pos_product_option_id 
			LEFT JOIN pos_product_attributes ON pos_product_options.pos_product_attribute_id = pos_product_attributes.pos_product_attribute_id
			WHERE pos_product_sub_id_options.pos_product_sub_id = $pos_product_sub_id";
	
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,'SET SESSION group_concat_max_len=1000');
	$data = getTransactionSingleValueSQL($dbc,$sql);
	closeDB($dbc);
	return $data;
	

	
	
}
function getProductSubIdOptionsCodeListNoDescription($pos_product_sub_id)
{
	
	
	$sql="	SELECT group_concat(option_code SEPARATOR ',') 
			FROM pos_product_sub_id_options 
			LEFT JOIN pos_product_options ON pos_product_sub_id_options.pos_product_option_id = pos_product_options.pos_product_option_id 
			LEFT JOIN pos_product_attributes ON pos_product_options.pos_product_attribute_id = pos_product_attributes.pos_product_attribute_id
			WHERE pos_product_sub_id_options.pos_product_sub_id = $pos_product_sub_id";
	
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,'SET SESSION group_concat_max_len=1000');
	$data = getTransactionSingleValueSQL($dbc,$sql);
	closeDB($dbc);
	return $data;
	

	
	
}
function getProductSubIdNameFromAttributeNames($pos_manufacturer_brand_id, $style_number, $options_array)
{

	$pos_product_id = getProductID($pos_manufacturer_brand_id, $style_number);
	//now we need an array of option ids
	for($i=0;$i<sizeof($options_array);$i++)
	{
		$pos_product_options_ids[] =  getProductOptionId($pos_product_id, getProductAttributeId($options_array[$i]['attribute_name']), $options_array[$i]['option_code']);
	}
	$option_array = '(' . implode(',',$pos_product_options_ids) . ')';

	$sql = "SELECT product_subid_name  FROM pos_product_sub_id_options
			LEFT JOIN pos_products_sub_id ON pos_product_sub_id_options.pos_product_sub_id = pos_products_sub_id.pos_product_sub_id WHERE pos_product_option_id IN ".$option_array . " GROUP BY pos_product_sub_id_options.pos_product_sub_id HAVING COUNT(*) = " . sizeof($pos_product_options_ids);
			
	//echo($sql);
	return getSQL($sql);
}
function getProductSubIdFromAttributeNames($pos_manufacturer_brand_id, $style_number, $options_array)
{

	$pos_product_id = getProductID($pos_manufacturer_brand_id, $style_number);
	//now we need an array of option ids
	for($i=0;$i<sizeof($options_array);$i++)
	{
		$pos_product_options_ids[] =  getProductOptionId($pos_product_id, getProductAttributeId($options_array[$i]['attribute_name']), $options_array[$i]['option_code']);
	}
	$option_array = '(' . implode(',',$pos_product_options_ids) . ')';

	$sql = "SELECT pos_products_sub_id.pos_product_sub_id  FROM pos_product_sub_id_options
			LEFT JOIN pos_products_sub_id ON pos_product_sub_id_options.pos_product_sub_id = pos_products_sub_id.pos_product_sub_id WHERE pos_product_option_id IN ".$option_array . " GROUP BY pos_product_sub_id_options.pos_product_sub_id HAVING COUNT(*) = " . sizeof($pos_product_options_ids);
			
	//echo($sql);
	return getSingleValueSQL($sql);
}
function getProductSubIdNameFromOptionIds($pos_product_option_ids)
{
	//pass in an array like: (1123,3456)
	$option_array = '(' . implode(',',$pos_product_option_ids) . ')';
	$sql = "SELECT product_subid_name  FROM pos_product_sub_id_options
			LEFT JOIN pos_products_sub_id ON pos_product_sub_id_options.pos_product_sub_id = pos_products_sub_id.pos_product_sub_id WHERE pos_product_option_id IN ".$option_array . " GROUP BY pos_product_sub_id_options.pos_product_sub_id HAVING COUNT(*) = " . sizeof($pos_product_option_ids);

	return getSingleValueSQL($sql);
}
function getProductSubIdFromOptionIds($pos_product_option_ids)
{
	//pass in an array like: (1123,3456)
	$option_array = '(' . implode(',',$pos_product_option_ids) . ')';
	$sql = "SELECT pos_products_sub_id.pos_product_sub_id  FROM pos_product_sub_id_options
			LEFT JOIN pos_products_sub_id ON pos_product_sub_id_options.pos_product_sub_id = pos_products_sub_id.pos_product_sub_id WHERE pos_product_option_id IN ".$option_array . " GROUP BY pos_product_sub_id_options.pos_product_sub_id HAVING COUNT(*) = " . sizeof($pos_product_option_ids);

	return getSingleValueSQL($sql);
}	
function getProductSubIdsFromBrand($pos_manufacturer_brand_id)
{
	$sql = "
	SELECT pos_products_sub_id.product_subid_name FROM pos_products_sub_id 
	LEFT JOIN pos_products
	ON pos_products.pos_product_id = pos_products_sub_id.pos_product_id
	WHERE pos_products.pos_manufacturer_brand_id = $pos_manufacturer_brand_id
	";
	return getSQL($sql);
}
function getStyleNumbersFromBrandId($pos_manufacturer_brand_id)
{
	$sql = "SELECT style_number FROM pos_products WHERE pos_manufacturer_brand_id = $pos_manufacturer_brand_id";
	return getFieldRowSQL($sql);
}
function getProductIdsAndStyleNumbersFromBrandId($pos_manufacturer_brand_id)
{
	$sql = "SELECT style_number, pos_product_id FROM pos_products WHERE pos_manufacturer_brand_id = $pos_manufacturer_brand_id";
	return getFieldRowSQL($sql);
}
function getProductColorOptions($pos_product_id)
{
	$pos_product_attribute_id = getProductAttributeID('Color');
	$sql = "SELECT pos_product_option_id, option_code, option_name, concat(option_code,':', option_name) as option_code_name FROM pos_product_options where pos_product_id = $pos_product_id AND pos_product_attribute_id = $pos_product_attribute_id";
	return getFieldRowSql($sql);
}
function getProductOptions($pos_product_id, $pos_product_attribute_id)
{
	//$pos_product_attribute_id = getProductAttributeID($attribute_name);
	$sql = "SELECT pos_product_option_id, option_code, option_name, concat(option_code,':', option_name) as option_code_name FROM pos_product_options where pos_product_id = $pos_product_id AND pos_product_attribute_id = $pos_product_attribute_id";
	return getFieldRowSql($sql);
}

function getProductSizes($pos_product_id)
{
	
	//the sub id knows that there is a cup option.... however the size does not seem to know... why not?
	//hmmmmm
	//a size really needs to be a combination of all 'parameters'
	// this way 32A but not 38A can exist
	//the sub id will know that it is a '32A' and has a cup of A
	//why? In case I need to populate the purchase order, I will need to find the "size" which is will be the "size" less the cup, inseam, etc...
	// so essentially that will need to be parsed. 
	
	$sql = "SELECT pos_product_option_id, option_name as size_name,option_code as size_code,sort_index 
					FROM pos_product_options
					INNER JOIN pos_product_attributes  USING (pos_product_attribute_id)
					WHERE pos_product_id = $pos_product_id AND attribute_name = 'Size' ORDER by sort_index ASC";
					
	$sql2 = "SELECT size_options.pos_product_option_id, size_options.option_name as size_name, size_options.option_code as size_code, size_options.sort_index , cup_options.option_code as cup_code		
					FROM pos_product_options size_options
					INNER JOIN pos_product_options cup_options USING (pos_product_id) 
					INNER JOIN pos_product_attributes size ON size_options.pos_product_attribute_id = size.pos_product_attribute_id
					INNER JOIN pos_product_attributes cup ON cup_options.pos_product_attribute_id = cup.pos_product_attribute_id
					WHERE pos_product_id = $pos_product_id AND size.attribute_name = 'Size' AND cup.attribute_name = 'Cup' ORDER by sort_index ASC";
					

		
		
					
	//try again...
	//we need to get the size and cup options for the product.... 
	// the size and cup option are not linked...
	//however they should be?
	//
	
	
	
	
	
	
	
$data = getSQL($sql);
	return $data;
}
function getProductSizesAsArray($pos_product_id)
{
	//the product sizes are part of the product options
	
	//the size inventory is part of the sub_id
	
	//so get the options...
		$sql = "SELECT pos_product_options.pos_product_option_id, pos_product_options.option_name, pos_product_options.option_code, pos_product_options.sort_index		
					FROM pos_product_options
					INNER JOIN pos_product_attributes  ON pos_product_options.pos_product_attribute_id = pos_product_attributes.pos_product_attribute_id
					WHERE pos_product_id = $pos_product_id AND pos_product_attributes.attribute_name = 'Size' ORDER by pos_product_options.sort_index ASC";
					
					return getFieldRowSQL($sql);
	
}
function getSizesAsList($pos_product_id)
{
	$sizes = getProductSizesAsArray($pos_product_id);
	return implode('||', $sizes['option_name']);
}
function sortSizes($unsorted_sizes, $size_chart)
{
	//we could try an array sort - 
	//we could also look at the size chart - if the size is in the chart then we will sort by the chart. If not then we will sort by the array
	$delimeter = "\r\n";
	$strArray = explode($delimeter, $unsorted_sizes);
	$sort_by_size_chart = true;
	$at_least_one_size_in_size_chart = false;
	for($i=0;$i<sizeof($strArray);$i++)
	{	
		if (!in_array($strArray[$i], $size_chart))
		{
			$sort_by_size_chart = false;
		}
		else
		{
			$at_least_one_size_in_size_chart = true;
		}
	}
	if ($sort_by_size_chart)
	{
		//sort by the size array
		$sorted_sizes = sortArrayValueByArrayValue($strArray, $size_chart);
	}
	else
	{
		if (!$at_least_one_size_in_size_chart)
		{
			//sort numerically
			asort($strArray);
			$sorted_sizes = $strArray;
		}
		else
		{
			//don't sort - we have some sizes in the options that are not in the size chart
			$sorted_sizes = $strArray;
		}
	}
	
	return implode($sorted_sizes, $delimeter);
}
function sortProductOptionSizes($unsorted_sizes, $size_chart)
{
// unsorted sizes come in as an array
	$sort_by_size_chart = true;
	$at_least_one_size_in_size_chart = false;
	for($i=0;$i<sizeof($unsorted_sizes);$i++)
	{	
		if (!in_array($unsorted_sizes[$i], $size_chart))
		{
			$sort_by_size_chart = false;
		}
		else
		{
			$at_least_one_size_in_size_chart = true;
		}
	}
	if ($sort_by_size_chart)
	{
		//sort by the size array
		$sorted_sizes = sortArrayValueByArrayValue($unsorted_sizes, $size_chart);
	}
	else
	{
		if (!$at_least_one_size_in_size_chart)
		{
			//sort numerically
			asort($unsorted_sizes);
			$sorted_sizes = $unsorted_sizes;
		}
		else
		{
			//don't sort - we have some sizes in the options that are not in the size chart
			$sorted_sizes = $unsorted_sizes;
		}
	}
	
	return implode($sorted_sizes, $delimeter);
}
function sortArrayValueByArrayValue($unorderd_array,$orderArray) 
{
	$sorted_array = array();
	for($i=0;$i<sizeof($orderArray);$i++)
	{
		if (in_array($orderArray[$i], $unorderd_array))
		{
			$sorted_array[] = $orderArray[$i];
		}
	}
    return $sorted_array;
}
function checkProductSubIdCanBeDeleted($pos_product_sub_id)
{
	//check the links....
		$sql_po = "SELECT pos_purchase_order_id, pos_product_sub_id FROM pos_purchase_order_contents
		
		WHERE pos_product_sub_id = $pos_product_sub_id";
		$purchase_order_links = getSQL($sql_po);
		prerprint($purchase_order_links);
		//and check the inventory log
		$sql_inv = "SELECT pos_product_sub_id FROM pos_inventory_event_contents
		
		WHERE pos_product_sub_id = $pos_product_sub_id";
		$inventory_links = getSQL($sql_inv);
		if(sizeof($purchase_order_links) == 0 AND sizeof($purchase_order_links) == 0)
		{
			return true;
		}
		else
		{
			$errors[] = preprint($purchase_order_links,'true');
			$errors[] = preprint($inventory_links,'true');
			return $errors;
		}
}
function getBrandIdFromProductId($pos_product_id)
{
	return getSingleValueSql("SELECT pos_manufacturer_brand_id FROM pos_products WHERE pos_product_id = $pos_product_id");
}

function getProductsLinkedToImage($pos_product_image_id)
{
	$products = getSQL("SELECT pos_products_sub_id.product_subid_name as barcode, pos_products.overview, pos_products.description, pos_product_image_lookup.pos_product_sub_id, pos_products.pos_product_id, CONCAT(brand_name, ' ', title) as big_title FROM pos_products 
				LEFT JOIN pos_product_image_lookup ON pos_products.pos_product_id = pos_product_image_lookup.pos_product_id 
				LEFT JOIN pos_products_sub_id ON pos_product_image_lookup.pos_product_sub_id = pos_products_sub_id.pos_product_sub_id
				LEFT JOIN pos_manufacturer_brands ON pos_products.pos_manufacturer_brand_id = pos_manufacturer_brands.pos_manufacturer_brand_id
				WHERE pos_product_image_lookup.pos_product_image_id = $pos_product_image_id");
	return $products;
}

function getImageProducts($pos_product_image_id)
{
	//this needs to get all the 'attributes' 
	
	//need a name: brand_name + title + 'in' + color
	
	$sql = "SELECT pos_products_sub_id.pos_product_sub_id 
			FROM  pos_products_sub_id
			LEFT JOIN pos_product_image_lookup ON pos_product_image_lookup.pos_product_sub_id = pos_products_sub_id.pos_product_sub_id
			WHERE pos_product_image_lookup.pos_product_image_id = $pos_product_image_id";
	$data = getSQL($sql);
	for($i=0;$i<sizeof($data);$i++)
	{
		$pos_product_sub_id = $data[$i]['pos_product_sub_id'];
		$return[$i] =  getPinnacleCartValues($pos_product_sub_id);
		$return[$i]['pos_product_id'] = getProductIdFromProductSubId($pos_product_sub_id);
		$return[$i]['pos_product_sub_id'] = $pos_product_sub_id;
	}	
	return $return;
}
function getPinnacleCartValues($pos_product_sub_id)
{
	$pos_product_id = getProductIdFromProductSubId($pos_product_sub_id);
	
	$data['web_product_id'] = slugify(strtolower(getProductBrandCode($pos_product_id) .'-' .getProductStyleNumber($pos_product_id) . '-' .getProductOptionCode($pos_product_sub_id, getProductAttributeId('Color'))));
	
	//for better SEO use this feature
	$data['web_product_id'] = strtolower(slugify(getProductBrandName($pos_product_id) .'-' . getProductTitle($pos_product_id) . '-' . getProductStyleNumber($pos_product_id) . '-' . getProductOptionCode($pos_product_sub_id, getProductAttributeId('Color'))));
		$data['web_product_id'] = substr($data['web_product_id'],0,255);
		
		$data['Manufacturer name'] = getBrandName(getBrandFromProductId($pos_product_id));
		$data['Manufacturer code'] = $data['Manufacturer name'];
		$data['name'] = $data['Manufacturer name'] . ' ' . getProductTitle($pos_product_id)  .'-' .getProductStyleNumber($pos_product_id) . ' in ' . getProductOptionName($pos_product_sub_id, getProductAttributeId('Color'));
		$data['description'] = getProductDescription($pos_product_id);
		$data['description'] = getProductOverview($pos_product_id);
		$data['price'] = getProductRetail($pos_product_id);
		$data['catagories'] = getAllProductCategories($pos_product_id);
		//size is on everything...
		$data['attribute1_name'] = 'Size';
		$data['attribute1_list'] = getSizesAsList($pos_product_id);
		$data['attribute2_name'] = '';
		$data['attribute2_list'] = '';
		$data['attribute3_name'] = '';
		$data['attribute3_list'] = '';
		
		
		$data['Tax class name'] = ($data['price']>110) ? 'Regular' : 'Exempt';
		$data['Tax class ID'] = ($data['price']>110) ? 'Regular' : 'Exempt';
		$data['Priority'] = getProductPriority($pos_product_id);
		$data['weight'] = getProductWeight($pos_product_id);
		return $data;
}
function getAllProductAttributes()
{
	return getFieldRowSQL("SELECT pos_product_attribute_id, attribute_name FROM pos_product_attributes");

}
function createProductAttributeSelect($name, $pos_product_attribute_id, $option_all ='off', $tags = ' onchange="needToConfirm=true" ' )
{	
	$attributes = getAllProductAttributes();
	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= $tags;
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Attribute</option>';
	//add an option for all accounts
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_product_attributes == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Attributes</option>';
	}
	for($i = 0;$i < sizeof($attributes['pos_product_attribute_id']); $i++)
	{
		$html .= '<option value="' . $attributes['pos_product_attribute_id'][$i] . '"';
		
		if ( ($attributes['pos_product_attribute_id'][$i] == $pos_product_attribute_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $attributes['attribute_name'][$i] . '</option>';
	}
	$html .= '</select>';
	return $html;
}
function getProductSubidBrandTitleStyleOptions($pos_product_sub_id)
{
	$sql = "SELECT pos_products_sub_id.pos_product_sub_id, concat(pos_manufacturer_brands.brand_name,':',pos_products.title,':',pos_products.style_number,':',
		
			(SELECT group_concat(concat(attribute_name,':',option_code,'-',option_name) SEPARATOR ' ') 
			FROM pos_product_sub_id_options 
			LEFT JOIN pos_product_options ON pos_product_sub_id_options.pos_product_option_id = pos_product_options.pos_product_option_id 
			LEFT JOIN pos_product_attributes ON pos_product_options.pos_product_attribute_id = pos_product_attributes.pos_product_attribute_id
			WHERE pos_products_sub_id.pos_product_sub_id = pos_product_sub_id_options.pos_product_sub_id )
		
			) as long_name
		
		FROM pos_products_sub_id 
		LEFT JOIN pos_products ON pos_products_sub_id.pos_product_id = pos_products.pos_product_id
		LEFT JOIN pos_manufacturer_brands ON pos_products.pos_manufacturer_brand_id = pos_manufacturer_brands.pos_manufacturer_brand_id
		WHERE pos_products_sub_id.pos_product_sub_id = $pos_product_sub_id";
		
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,'SET SESSION group_concat_max_len=1000');
	$data = getTransactionSingleValueSql($dbc,$sql);
	closeDB($dbc);
	return $data;
		
}

// CATEGORIES ################################
/*******************CATEGORIES***************/
function getCategory($pos_category_id)
{
	$dbc = openPOSDatabase();
	$category_sql = "SELECT * FROM pos_categories WHERE pos_category_id = '" . $pos_category_id . "'"; 
	$category_sql_result = @mysqli_query ($dbc, $category_sql);
	$selected_category=  convert_mysql_result_to_array($category_sql_result);
	mysqli_close($dbc);
	return $selected_category;
}

function getCategoryName($pos_category_id)
{
	$category = getCategory($pos_category_id);
	$category_name = getSingleValueSql("SELECT name FROM pos_categories WHERE pos_category_id = $pos_category_id");
	return $category_name;
}
function getCategoryParent($pos_category_id)
{
	$category = getCategory($pos_category_id);
	$parent = $category[0]['parent'];
	return $parent;
}
function getCategoriesWithNoParents()
{
	return "SELECT * FROM pos_categories WHERE active =1 AND pos_category_id NOT IN(SELECT parent FROM pos_categories) ORDER BY name ASC";
}
function getNoParentCategoryArray()
{
	$dbc = openPOSDatabase();

	//$category_q = "SELECT * FROM pos_categories WHERE active ='1'";
	$category_q = getCategoriesWithNoParents();
	$category_r = @mysqli_query ($dbc, $category_q);

	$category_data=array();
	$i=0;
	$category_names = array();
	$category_ids = array();
	while($category_row=mysqli_fetch_array($category_r, MYSQLI_ASSOC)) 
	{
		$category_names[$i] = $category_row['name'];
		$category_ids[$i] = $category_row['pos_category_id'];
		$i = $i + 1;
	}
	$categories['name'] = $category_names;
	$categories['pos_category_id'] = $category_ids;
	mysqli_close($dbc);
	return $categories;
}
function getCategoryArray()
{
	$dbc = openPOSDatabase();

	$category_q = "SELECT * FROM pos_categories WHERE active ='1'";
	//$category_q = getCategoriesWithNoParents();
	$category_r = @mysqli_query ($dbc, $category_q);

	$category_data=array();
	$i=0;
	$category_names = array();
	$category_ids = array();
	while($category_row=mysqli_fetch_array($category_r, MYSQLI_ASSOC)) 
	{
		$category_names[$i] = $category_row['name'];
		$category_ids[$i] = $category_row['pos_category_id'];
		$i = $i + 1;
	}
	$categories['name'] = $category_names;
	$categories['pos_category_id'] = $category_ids;
	mysqli_close($dbc);
	return $categories;
}
function getCategoryAssociativeArray()
{
	$unassoc_array = getCategoryArray();
	$assoc_array =  array();
	for($i =0; $i<sizeof($unassoc_array['name']);$i++)
	{
		$assoc_array[$unassoc_array['pos_category_id'][$i]] = $unassoc_array['name'][$i];
	
	}
	return $assoc_array;
}
function getCategories()
{
	$dbc2 = openPOSDatabase();
	$category_q = "SELECT * FROM pos_categories WHERE active ='1'";
	$product_category_result= my_sql_query($dbc2, $category_q);
	$product_categories = convert_mysql_result_to_array($product_category_result);
	mysqli_close($dbc2);
	return $product_categories;
}
function getFirstLevelCategories()
{
	$category_q = "SELECT * FROM pos_categories WHERE active ='1' AND level = 1";
	return getSQL($category_q);
}
function getZeroLevelCategories()
{
	$category_q = "SELECT * FROM pos_categories WHERE active ='1' AND level = 0";
	return getSQL($category_q);
}
function getProductCategories()
{
	$dbc2 = openPOSDatabase();
	$category_q = "SELECT * FROM pos_categories WHERE active ='1'";
	$product_category_result= my_sql_query($dbc2, $category_q);
	$product_categories = convert_mysql_result_to_array($product_category_result);
	mysqli_close($dbc2);
	return $product_categories;
}
function createCategorySelect($name, $pos_category_id, $option_all = 'off', $select_events ='')
{
	$categories = getCategories();

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
		$html .= '<option value="' . $categories[$i]['pos_category_id'] . '"';
		if ( ($categories[$i]['pos_category_id'] == $pos_category_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $categories[$i]['name'] .'</option>';
	}
	$html .= '</select>';
	return $html;
}
function createZeroLevelCategorySelect($name, $pos_category_id, $option_all = 'off', $select_events ='')
{
	$categories = getZeroLevelCategories();

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
		$html .= '<option value="' . $categories[$i]['pos_category_id'] . '"';
		if ( ($categories[$i]['pos_category_id'] == $pos_category_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $categories[$i]['name'] .'</option>';
	}
	$html .= '</select>';
	return $html;
}
function createFirstLevelCategorySelect($name, $pos_category_id, $option_all = 'off', $select_events ='')
{
	$categories = getFirstLevelCategories();

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
		$html .= '<option value="' . $categories[$i]['pos_category_id'] . '"';
		if ( ($categories[$i]['pos_category_id'] == $pos_category_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $categories[$i]['name'] .'</option>';
	}
	$html .= '</select>';
	return $html;
}

function getRecusiveCategoriesIDS($pos_category_id, $array, $level)
{
	$return = getSql("SELECT pos_category_id, name FROM pos_categories WHERE parent = $pos_category_id");
	for($i=0;$i<sizeof($return);$i++)
	{
		$sub_cat = $return[$i]['pos_category_id'];
		$name = $return[$i]['name'];
		for($i2=0;$i2<$level;$i2++)
		{
			$name = '&nbsp' . $name;
			$name = '&nbsp' . $name;
		}
		//$array[] = $name;
		$array[] = $sub_cat;
		$array = getRecusiveCategoriesIDS($sub_cat, $array,$level+1);
	}
	return $array;
}
function getRecusiveCategoriesNames($pos_category_id, $array, $level)
{
	$return = getSql("SELECT pos_category_id, name FROM pos_categories WHERE parent = $pos_category_id");
	for($i=0;$i<sizeof($return);$i++)
	{
		$sub_cat = $return[$i]['pos_category_id'];
		$name = $return[$i]['name'];
		for($i2=0;$i2<$level;$i2++)
		{
			$name = '&nbsp' . $name;
			$name = '&nbsp' . $name;
		}
		$array[] = $name;
		//$array[] = $sub_cat;
		$array = getRecusiveCategoriesNames($sub_cat, $array,$level+1);
	}
	return $array;
}
function getRecusiveCategories($pos_category_id, $array, $level)
{
	$return = getSql("SELECT pos_category_id, name FROM pos_categories WHERE parent = $pos_category_id");
	for($i=0;$i<sizeof($return);$i++)
	{
		$sub_cat = $return[$i]['pos_category_id'];
		$name = $return[$i]['name'];
		for($i2=0;$i2<$level;$i2++)
		{
			//this is specific for the old ass po code to get it to display...
			$name = '\u00A0' . $name;
			$name = '\u00A0' . $name;
		}
		$array['name'][] = $name;
		$array['pos_category_id'][] = $sub_cat;
		$array = getRecusiveCategories($sub_cat, $array,$level+1);
	}
	return $array;
}
// recursive category tree
function createCategoryTreeSelectChildlessness($name, $selected_category, $select_events ='')
{
	$categories = getCategoryTree();

	$html = '<select style="width:100%;" id = "'.$name .'" name="'.$name.'" ';
	$html .= $select_events;
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Category</option>';
	
	$level = 0;
	//add an option for all product categories
	
		//add the main categoy
	$html .= addCategorySelectOptionsChildlessness($categories, $level, $selected_category);
		
	
	$html .= '</select>';
	return $html;
}
function addCategorySelectOptionsChildlessness($category, $level, $selected_category)
{
	$html = '';
	//if($level > 0) $html .= '<optgroup>';

	foreach($category as $key => $value)
	{
		
		
	
	
		if($value['children'] != false)
		{
			
			$html .= '<option value="' . $key . '"';
			if ( ($key == $selected_category) ) 
			{
				$html .= ' selected="selected"';
			}
			//$html .= 'style="padding-left:' .($level*20) .'px;"';
			if($level > 0) $html .=' style="font-weight:bold" ';
		
		
			$html .= '>';
		
			for($i=0;$i<$level;$i++)
			{
				$html.= '&nbsp';
				$html.= '&nbsp';
			}
		
			$html .= $value['name'] .'</option>';
			
			
			$html .= addCategorySelectOptionsChildlessness($value['children'], $level+1, $selected_category);
		}
	
	}
	//if($level > 0)  $html .= '</optgroup>';
	return $html;
	
}
function createCategoryTreeSelect($name, $selected_category, $select_events ='')
{
	$categories = getCategoryTree();

	$html = '<select style="width:100%;" id = "'.$name .'" name="'.$name.'" ';
	$html .= $select_events;
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Category</option>';
	
	$level = 0;
	//add an option for all product categories
	
		//add the main categoy
	$html .= addCategorySelectOptions($categories, $level, $selected_category);
		
	
	$html .= '</select>';
	return $html;
}
function addCategorySelectOptions($category, $level, $selected_category)
{
	$html = '';
	//if($level > 0) $html .= '<optgroup>';

	foreach($category as $key => $value)
	{
		
		$html .= '<option value="' . $key . '"';
		if ( ($key == $selected_category) ) 
		{
			$html .= ' selected="selected"';
		}
		//$html .= 'style="padding-left:' .($level*20) .'px;"';
		if($level > 0) $html .=' style="font-weight:bold" ';
		
		
		$html .= '>';
		
		for($i=0;$i<$level;$i++)
		{
			$html.= '&nbsp';
			$html.= '&nbsp';
		}
		
		$html .= $value['name'] .'</option>';
	
	
		if($value['children'] != false)
		{
			$html .= addCategorySelectOptions($value['children'], $level+1, $selected_category);
		}
	
	}
	//if($level > 0)  $html .= '</optgroup>';
	return $html;
	
}
function getCategoryTree()
{
	$categories = getsql("SELECT key_name, pos_category_id, parent FROM pos_categories WHERE active = 1 ORDER BY Priority ASC");


	$cat_array = array();
	$level = 0;
	//first find the parents....
	for($c=0;$c<sizeof($categories);$c++)
	{
		if($categories[$c]['parent'] == 0)
		{
			$pos_category_id = $categories[$c]['pos_category_id'];
			$cat_name = $categories[$c]['key_name'];
			$children = findCategoryChildren($categories, $pos_category_id, $level+1);
			$cat_array[$pos_category_id] = array(
				'name' => $cat_name,
				'children'=>$children
				);
		}
	}
	return $cat_array;
}
function findCategoryChildren($categories, $pos_parent_category_id, $level)
{
	//exit condition
	//echo '<p>Level: ' . $level . '</p>';
	$cat_array = array();
	//go through all the categories...
	for($c=0;$c<sizeof($categories);$c++)
	{
		if($categories[$c]['parent'] == $pos_parent_category_id)
		{
			$pos_category_id = $categories[$c]['pos_category_id'];
			$cat_name = $categories[$c]['key_name'];
			$children = findCategoryChildren($categories, $pos_category_id, $level+1);
			if (sizeof($children) == 0) $children = false;
			$cat_array[$pos_category_id] = array(
			'name' => $cat_name,
			'children'=>$children
			);
			
		}
	}
	
	return $cat_array;
}

function createCategorySubArray()
{
	$categories = getSQL("SELECT pos_category_id FROM pos_categories");
	$cat_list = array();
	for($i=0;$i<sizeof($categories);$i++)
	{
		//here we need to find all sub categories
		$pos_category_id = $categories[$i]['pos_category_id'];
		$cat_list[$pos_category_id] = recursiveCategory($pos_category_id,array());
	
	}
	return $cat_list;

}
function recursiveCategory($pos_category_id, $array)
{
	$return = getSql("SELECT pos_category_id FROM pos_categories WHERE parent = $pos_category_id");
	for($i=0;$i<sizeof($return);$i++)
	{
		$sub_cat = $return[$i]['pos_category_id'];
		$array[] = $sub_cat;
		$array = recursiveCategory($sub_cat, $array);
	}
	return $array;
}
?>