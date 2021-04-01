<?php

require_once ('../../../Config/config.inc.php');
require_once(PHP_LIBRARY);
require_once (CHECK_LOGIN_FILE);


//Form stuff
function createProductSelect()
{	
	// need a brand id, color_id, style number and color name lookup.
	$sql = "SELECT pos_product_color_id, style_number, pos_manufacturer_brand_id FROM pos_product_colors
			LEFT JOIN pos_products USING(pos_product_id)
			";
	//select the brand
	//select the style number
	//click add
	$html = createManufacturerBrandSelect('pos_manufacturer_brand_id', 'false', 'off', ' onchange="UpdateProducts()" ');
	$html .= '<p><input class = "button" type="button" style="width:200px;" name="add_product_set" value="Add to Set" onclick=""/>';
	//$html .= createProductSelect('pos_product_color_id', 'false');
	return $html;
}

function createProductColorTableDef($db_table, $pos_product_option_id)
{
	$type = 'Edit';
	if ($type == 'New')
	{
		$pos_product_option_id = 'TBD';
		$unique_validate = array('unique_group' => array('account_number', 'company'), 'min_length' => 1);
	}
	else
	{
		$key_val_id['pos_product_option_id'] = $pos_product_option_id;
		$unique_validate = array('unique_group' => array('pos_product_id', 'option_code', 'pos_attribute_id'), 'min_length' => 1, 'id' => $key_val_id);
	}

$db_table = 'pos_product_options';
	
	
	
	
	$product_color_basics = array(
				
					array( 'db_field' => 'pos_product_id',
									'type' => 'input',
									'caption' => 'System Product ID',
									'tags' => 'readonly="readonly"'),
					array( 'db_field' => 'pos_product_option_id',
									'type' => 'input',
									'caption' => 'System Product Option ID',
									'tags' => 'readonly="readonly"'),
					array( 'db_field' => 'option_code',
									'type' => 'input',
									'caption' => 'Color Code',
									'validate' => $unique_validate,
									'db_table' => $db_table),
					array( 'db_field' => 'option_name',
									'type' => 'input',
									'caption' => 'Color Name',
									'validate' => 'none'),
					/*array('db_field' => 'fashion_color',
									'type' => 'checkbox',
									'caption' => 'Fashion Color',
									'validate' => 'none'),*/
					array('db_field' => 'active',
									'type' => 'checkbox',
									'validate' => 'none'),
					array('db_field' => 'unique_web_product',
									'type' => 'checkbox',
									'caption' => 'Unique Web Product',
									'validate' => 'none'),
					/*array('db_field' => 'web_product_id',
									'type' => 'input',
									'caption' => 'Web Product Id',
									'validate' => 'none'),
					array('db_field' => 'web_product_url',
									'type' => 'input',
									'caption' => 'Web Product URL',
									'validate' => 'none'),*/
					array('db_field' => 'price_adjustment',
							'tags' => integersOnly(),
									'type' => 'input',
									'value' => 0.00,
									'validate' => 'number'),
					array('db_field' => 'sort_index',
							'tags' => integersOnly(),
									'type' => 'input',
									'validate' => 'number',
									'caption' => 'Sort Index (lowest sorts first)'));
	
	
	return $product_color_basics;						
}
function createProductTableDef($db_table, $id)
{
	/* each array could be another 'table'? 
	$table_def = 
	array(
			array(            <----- These go vertical and are unique tables
					array()     <---- Thes would go horizontal
					array()     <---- Thes would go horizontal
					array()     <---- Thes would go horizontal
				)
			array()            <----- These go vertical and are unique tables
			array()            <----- These go vertical and are unique tables
		)*/
$pos_product_id = $id['pos_product_id'];
$pos_manufacturer_brand_id = getBrandFromProductId($pos_product_id);
			

	$product_basics = array( 
							array( 'db_field' => 'pos_product_id',
									'type' => 'input',
									'caption' => '(AUTO) SYSTEM ID',
									'tags' => 'readonly="readonly"'),
							array( 'db_field' => 'pos_category_id',
									'type' => 'select',
									'caption' => 'Primary Category',
									'html' => createCategorySelect('pos_category_id', 'false'),
									'validate' => array('select_value' => 'false')),
							array( 'db_field' => 'pos_sales_tax_category_id',
								'type' => 'select',
								'caption' => 'Default Sales Tax Category',
								'html' => createSalesTaxCategorySelect('pos_sales_tax_category_id', 'false')),
							array('db_field' => 'pos_manufacturer_brand_id',
									'caption' => 'Brand Name',
									'type' => 'select',
									'html' => createManufacturerBrandSelect('pos_manufacturer_brand_id', 'false', 'off', ' onchange="updateBrandSizeChart()" '),
									'validate' => array('select_value' => 'false')),
							array('db_field' =>  'style_number',
									'type' => 'input',
									'validate' => array('unique_group' => array('style_number', 'pos_manufacturer_brand_id'), 'min_length' => 1,'id' => $id),
									'db_table' => $db_table),
							array('db_field' => 'pos_manufacturer_brand_size_id',
									'caption' => 'Brand Size Chart Row (used for ordering)',
									'type' => 'select',
									'html' => createManufacturerBrandSizeChartSelect('pos_manufacturer_brand_size_id', $pos_manufacturer_brand_id, 'false'),
									'validate' => 'none'),
							array('db_field' => 'active',
									'type' => 'checkbox',
									'validate' => 'none'),
							array('db_field' => 'priority',
									'type' => 'input',
									'tags' => numbersOnly(),
									'validate' => 'none'));
	$product_pricing = array( 							
							array('db_field' => 'cost',
							'tags' => numbersOnly(),
									'type' => 'input',
									'validate' => 'number'),
							array('db_field' => 'retail_price',
							'tags' => numbersOnly(),
									'type' => 'input',
									'validate' => 'number'),
							array('db_field' => 'sale_price',
							'tags' => numbersOnly(),
									'type' => 'input',
									'validate' => 'number'),
							array('db_field' =>  'bulk_retail_quantity',
									'type' => 'input',
									'tags' => numbersOnly(),
									'validate' => 'number'),
							array('db_field' =>  'bulk_retail_price',
									'type' => 'input',
									'tags' => numbersOnly(),
									'validate' => 'number')
							/*array('db_field' => 'employee_price',
							'tags' => numbersOnly(),
									'type' => 'input',
									'validate' => 'number')*/);
	$product_extras = array( 
							array('db_field' =>  'weight',
									'type' => 'input',
									'tags' => numbersOnly(),
									'validate' => 'number'),
							array('db_field' =>  'case_quantity',
									'type' => 'input',
									'tags' => numbersOnly(),
									'validate' => 'number'),
							array('db_field' =>  'case_price',
									'type' => 'input',
									'tags' => numbersOnly(),
									'validate' => 'number'),
							/*array('db_field' =>  'tax_rate',
									'type' => 'input',
									'tags' => numbersOnly(),
									'validate' => 'number')*/
						);
	$title = array(array('db_field' => 'title',
									'type' => 'input',
									'tags' => ' size="84" ',
									'validate' => 'none'),
					array('db_field' => 'overview',
									'type' => 'input',
									'tags' => ' size="255" ',
									'validate' => 'none'));
	$description = array(array('db_field' => 'description',
									'type' => 'textarea',
									'tags' => ' cols="84" ',
									'validate' => 'none'));
									

									
	$table_def = array($title,array($product_basics,$product_pricing,$product_extras),$description);
	return $table_def;
								
}
function createNewProductTableDef($db_table)
{
	/* each array could be another 'table'? 
	$table_def = 
	array(
			array(            <----- These go vertical and are unique tables
					array()     <---- Thes would go horizontal
					array()     <---- Thes would go horizontal
					array()     <---- Thes would go horizontal
				)
			array()            <----- These go vertical and are unique tables
			array()            <----- These go vertical and are unique tables
		)*/
			

	$product_basics = array( 
							array( 'db_field' => 'pos_product_id',
									'type' => 'input',
									'caption' => '(AUTO) SYSTEM ID',
									'tags' => 'readonly="readonly"'),
							array( 'db_field' => 'pos_category_id',
									'type' => 'select',
									'caption' => 'Primary Category',
									'html' => createCategorySelect('pos_category_id', 'false'),
									'validate' => array('select_value' => 'false')),
							array( 'db_field' => 'pos_sales_tax_category_id',
								'type' => 'select',
								'caption' => 'Default Sales Tax Category',
								'html' => createSalesTaxCategorySelect('pos_sales_tax_category_id', 'false')),
							array('db_field' => 'pos_manufacturer_brand_id',
									'caption' => 'Brand Name',
									'type' => 'select',
									'html' => createManufacturerBrandSelect('pos_manufacturer_brand_id', 'false'),
									'validate' => array('select_value' => 'false')),
							array('db_field' =>  'style_number',
									'type' => 'input',
									'validate' => array('unique_group' => array('style_number', 'pos_manufacturer_brand_id'), 'min_length' => 1),
									'db_table' => $db_table),
							array('db_field' => 'active',
									'type' => 'checkbox',
									'value' => 1,
									'validate' => 'none'));
	$product_pricing = array( 							
							array('db_field' => 'cost',
							'tags' => numbersOnly(),
									'type' => 'input',
									'validate' => 'number'),
							array('db_field' => 'retail_price',
							'tags' => numbersOnly(),
									'type' => 'input',
									'validate' => 'number'),
							array('db_field' => 'sale_price',
							'tags' => numbersOnly(),
									'type' => 'input',
									'validate' => 'number'),
							/*array('db_field' => 'employee_price',
							'tags' => numbersOnly(),
									'type' => 'input',
									'validate' => 'number')*/);
	$product_extras = array( 
							array('db_field' =>  'weight',
									'type' => 'input',
									'tags' => numbersOnly(),
									'validate' => 'number'),
							array('db_field' =>  'case_quantity',
									'type' => 'input',
									'tags' => numbersOnly(),
									'validate' => 'number'),
							array('db_field' =>  'case_price',
									'type' => 'input',
									'tags' => numbersOnly(),
									'validate' => 'number'),
							array('db_field' =>  'tax_rate',
									'type' => 'input',
									'tags' => numbersOnly(),
									'validate' => 'number')
						);
	$title = array(array('db_field' => 'title',
									'type' => 'input',
									'tags' => ' size="84" ',
									'validate' => 'none'));
	$overview = array(array('db_field' => 'overview',
									'type' => 'textarea',
									'tags' => ' cols="84" ',
									'validate' => 'none'));
	$description = array(array('db_field' => 'description',
									'type' => 'textarea',
									'tags' => ' cols="84" ',
									'validate' => 'none'));

									
	$table_def = array($title,array($product_basics,$product_pricing,$product_extras),$overview,$description);
	return $table_def;
								
}
function createProductSizesHTMLTable($pos_product_id, $class='mysqlTable')
{
	$sizes = getProductSizesOld($pos_product_id);
	$html = '<table class="'.$class.'"><tbody><tr>';
	for($i=0;$i<sizeof($sizes);$i++)
	{
		$html.= '<td>' . $sizes[$i] .'</td>';
	}
	$html.= '</tr></tbody></table>';
	return $html;
}
function createProductSizeTableDef()
{
	return array(array('db_field' => 'options',
						'caption' => 'Sizes',
						'type' => 'textarea',
						'tags' => ' class="regular_textarea" ',
						'validate' => 'none')
			);
}
function createProductColorHTMLTable($pos_product_id)
{
	$list_table_columns = array(
			 array(
				'th' => '',
				'type' => 'url_button',
				'mysql_field' => 'pos_product_option_id',
				'location' => '../EditProductColor/edit_product_color.php',
				'button_caption' => 'Edit',
				'get_id_link' => 'pos_product_option_id'),
			array(
				'th' => 'Color Name',
				'type' => 'input',
				'mysql_field' => 'option_name'),
			array(
				'th' => 'Color Code',
				'type' => 'input',
				'mysql_field' => 'option_code'),
			

			array(
				'th' => 'System<br>ID',
				'type' => 'text',
				'mysql_field' => 'pos_product_option_id'),
			array(
				'th' => 'Secondary Categories',
				'type' => 'text',
				'mysql_field' => 'secondary_categories'),
			/*array(
				'th' => 'Recommended Products',
				'type' => 'text',
				'mysql_field' => 'recommended_products'),*/
			array(
				'th' => 'Active',
				'type' => 'checkbox',
				'mysql_field' => 'active'),
			/*array(
				'th' => 'Fashion Color',
				'type' => 'checkbox',
				'mysql_field' => 'fashion_color'),*/
			array(
				'th' => 'Unique Web Product',
				'type' => 'checkbox',
				'mysql_field' => 'unique_web_product'),
			array(
				'th' => 'Price Adjustment',
				'type' => 'input',
				'mysql_field' => 'price_adjustment'),
			
			);
	$list_sql = "
	
	SELECT pos_product_color_id, color_name, color_code, active, fashion_color, unique_web_product, web_product_id, web_product_url, ROUND(cost,2) as cost, ROUND(retail_price,2) AS retail_price, ROUND(sale_price,2) AS sale_price, ROUND(employee_price,2) as employee_price, 
	(SELECT GROUP_CONCAT(pos_categories.name) 
			FROM pos_product_secondary_categories
			LEFT JOIN pos_categories
			ON pos_product_secondary_categories.pos_category_id = pos_categories.pos_category_id
			WHERE pos_product_colors.pos_product_color_id = pos_product_secondary_categories.pos_product_color_id) as secondary_categories
			FROM pos_product_colors
			WHERE pos_product_id = '$pos_product_id'
			";
		$list_sql = "
	
	SELECT pos_product_option_id, option_name, option_code, pos_product_options.active,  price_adjustment, extra_tags, unique_web_product,  
	(SELECT GROUP_CONCAT(pos_categories.name) 
			FROM pos_product_secondary_categories
			LEFT JOIN pos_categories
			ON pos_product_secondary_categories.pos_category_id = pos_categories.pos_category_id
		
			WHERE pos_product_options.pos_product_option_id = pos_product_secondary_categories.pos_product_option_id) as secondary_categories
			FROM pos_product_options
			LEFT JOIN pos_product_attributes
			ON pos_product_options.pos_product_attribute_id = pos_product_attributes.pos_product_attribute_id
			WHERE pos_product_id = '$pos_product_id'
			AND attribute_name = 'Color'
			";
	$table_def_w_data = loadMYSQLResultsIntoTableDefinition($list_sql, $list_table_columns);
	if ($table_def_w_data)
	{
		return createHorizontalHTMLTableForMYSQLData($list_sql, $table_def_w_data);
	}
	else
	{
		return "<p>no records</p>";
	}
}
function createProductSubIdTable($pos_product_id)
{
	//this is the array style table like the receive table
	$table_def_array = createProductSubIDTableArrayDef();
	$sql = "SELECT * FROM pos_products_sub_id WHERE pos_product_id = '$pos_product_id'";
	$data = getSQL($sql);
	$class = "linedTable";
	if (sizeof($data)>0)
	{
		$table_def_array_with_data = loadMYSQLArrayIntoTableArray($table_def_array, $data);
		
		$html = createMYSQLArrayHTMLTable($table_def_array_with_data, $class, 'sub_id_table');
	}
	else
	{
		$html = createNoRecordsTable($table_def_array,$class, 'sub_id_table');
	}
	
	return $html;
	
}
function createSimpleProductInventoryTable($pos_product_id)
{
	$data=array();
	$stores = getStoresAndCompanies();
	$product_subs = getProductSubIds($pos_product_id);
	$counter=0;
	for ($i=0;$i<sizeof($stores);$i++)
	{
		for ($pid = 0;$pid<sizeof($product_subs);$pid++)
		{
			$data[$counter]['pos_product_inventory_id'] = getInventoryId($stores[$i]['pos_store_id'], $product_subs[$pid]['pos_product_sub_id']);
			$data[$counter]['product_subid_name'] = $product_subs[$pid]['product_subid_name'];
			$data[$counter]['store_name'] = getStoreName($stores[$i]['pos_store_id']);
			$data[$counter]['available_qty'] = getSimpleAvailableInventoryQTYInStore($product_subs[$pid]['pos_product_sub_id'],$stores[$i]['pos_store_id']);
			$data[$counter]['committed_qty'] = getCommittedStock($stores[$i]['pos_store_id'], $product_subs[$pid]['pos_product_sub_id']);
			$data[$counter]['quantity_ordered'] = getOnOrderStock($stores[$i]['pos_store_id'], $product_subs[$pid]['pos_product_sub_id']);	
			$data[$counter]['delivery_date'] = getOnOrderStockDeliveryDate($stores[$i]['pos_store_id'], $product_subs[$pid]['pos_product_sub_id']);
			$counter++;
		}
	}
	/*$sql = "
	SELECT pos_products_inventory.pos_product_inventory_id, pos_products_inventory.in_stock_qty, pos_products_inventory.committed_qty, pos_stores.store_name, pos_products_sub_id.product_subid_name FROM pos_products_sub_id 
	LEFT JOIN pos_products_inventory
	ON pos_products_inventory.pos_product_sub_id = pos_products_sub_id.pos_product_sub_id
	JOIN pos_stores
	ON pos_products_inventory.pos_store_id = pos_stores.pos_store_id
	WHERE pos_products_sub_id.pos_product_id = '$pos_product_id'
	";
	$data = getSQL($sql);*/
	$table_def_array = createProductInventoryTableArrayDef();
	$class = "linedTable";
	if (sizeof($data)>0)
	{
		$table_def_array_with_data = loadMYSQLArrayIntoTableArray($table_def_array, $data);
		
		$html = createMYSQLArrayHTMLTable($table_def_array_with_data, $class, 'inventory_table');
	}
	else
	{
		$html = createNoRecordsTable($table_def_array,$class, 'inventory_table');
	}
	return $html;
	
}




function createProductSubIDTableArrayDef()
{
	$array_table_def= array(	
					array(	'th' => 'System ID',
			 				'type' => 'td',
							'mysql_result_field' => 'pos_product_sub_id',
							'mysql_post_field' => ''),
					array(
							'th' => 'Product Subid Name',
							'mysql_result_field' => 'product_subid_name',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(
							'th' => 'Active',
							'mysql_result_field' => 'active',
							'type' => 'checkbox',
							'mysql_post_field' => ''),
					array(
							'th' => 'Inventory Warning',
							'mysql_result_field' => 'inventory_warning',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(
							'th' => 'Product Universal Part Code',
							'mysql_result_field' => 'product_upc',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(	'th' => 'Product Stock <br> Keeping Unit Id',
							'mysql_result_field' => 'product_sku',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(	'th' => 'Attributes List',
							'mysql_result_field' => 'attributes_list',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(	'th' => 'Inventory',
			 				'type' => 'td',
							'mysql_result_field' => 'pos_product_sub_id',
							'mysql_post_field' => ''),
					);
	return $array_table_def;
}
function createProductInventoryTableArrayDef()
{
	

	$array_table_def= array(	
					array(	'th' => 'System ID',
			 				'type' => 'td',
							'mysql_result_field' => 'pos_product_inventory_id',
							'mysql_post_field' => ''),
					array(
							'th' => 'Product Subid Name',
							'mysql_result_field' => 'product_subid_name',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(
							'th' => 'Store Name',
							'mysql_result_field' => 'store_name',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(
							'th' => 'In Stock <br> Available Quantity',
							'mysql_result_field' => 'available_qty',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(
							'th' => 'In Stock <br> Committed Quantity',
							'mysql_result_field' => 'committed_qty',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(
							'th' => 'On Order <br> Available Quantity',
							'mysql_result_field' => 'quantity_ordered',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(
							'th' => 'On Order <br> Committed Quantity',
							'mysql_result_field' => '',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(
							'th' => 'Expected <br> Delivery Date',
							'mysql_result_field' => 'delivery_date',
							'type' => 'td',
							'mysql_post_field' => '')
					);
	return $array_table_def;
}

function createProductSUBIDTableDef($type, $pos_product_sub_id, $pos_product_id)
{
	if ($pos_product_sub_id =='TBD')
	{
		$unique_validate = array('unique' => 'product_subid_name', 'min_length' => 1);

	}
	else
	{
		$key_val_id['pos_product_sub_id'] = $pos_product_sub_id;
		$unique_validate = array('unique' => 'product_subid_name', 'min_length' => 1, 'id' => $key_val_id);


	}
	return array( 
						array( 'db_field' => 'pos_product_sub_id',
								'type' => 'input',
								'tags' => ' readonly = "readonly" ',
								'caption' => 'System ID',
								'value' => $pos_product_sub_id,
								'validate' => 'none'
								
								),
						array('db_field' =>  'product_subid_name',
								'type' => 'input',
								'caption' => 'Product SUBID Name (barcode)',
								'db_table' => 'pos_products_sub_id',
								'validate' => $unique_validate),
						array('db_field' =>  'attributes_list',
								'type' => 'textarea',
								'tags' => ' class ="regular_textarea" ',
								'caption' => 'Attributes List'),
						array('db_field' =>  'comments',
								'type' => 'input',
								'caption' => 'Comments'),
						array('db_field' =>  'active',
								'type' => 'checkbox',
								'caption' => 'Active',
								'value' => '1')
						);	

}





/************* NEW ATTRIBUTE OPTIONS>>>> ********************/
function getAttributeName($pos_product_attribute_id)
{
	$sql = "SELECT attribute_name FROM pos_product_attributes where pos_product_attribute_id = $pos_product_attribute_id";
	return getSingleValueSQL($sql);
}
function createProductAttributeTableDef($type, $pos_product_attribute_id)
{
	if ($pos_product_attribute_id =='TBD')
	{
		$unique_validate = array('unique' => 'attribute_name', 'min_length' => 1);

	}
	else
	{
		$key_val_id['pos_product_attribute_id'] = $pos_product_attribute_id;
		$unique_validate = array('unique' => 'attribute_name', 'min_length' => 1, 'id' => $key_val_id);


	}
	return array( 
						array( 'db_field' => 'pos_product_attribute_id',
								'type' => 'input',
								'tags' => ' readonly = "readonly" ',
								'caption' => 'System ID',
								'value' => $pos_product_attribute_id,
								'validate' => 'none'
								
								),
						array('db_field' =>  'attribute_name',
								'type' => 'input',
								'caption' => 'Attribute name',
								'db_table' => 'pos_product_attributes',
								'validate' => $unique_validate),
						array('db_field' =>  'comments',
								'type' => 'input',
								'caption' => 'Comments'),
						array('db_field' =>  'priority',
								'type' => 'input',
								'caption' => 'Priority',
								'value' => '1'),
						array('db_field' =>  'active',
								'type' => 'checkbox',
								'caption' => 'Active',
								'value' => '1')
						);	
}
function createSizeTable()
{
		
		//$attribute_options = getAllProductAttributes();
		
		$columns = array(
					array('db_field' => 'none',
						'POST' => 'no',
						'caption' => '',
						'th_width' => '14px',
						'type' => 'checkbox',
						'element' => 'input',
						'element_type' => 'checkbox',
						'properties' => array(	'onclick' => 'function(){setSingleCheck(this);}'
												)),
					array(
					'db_field' => 'row_number',
					'caption' => 'Row',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'none',
					'properties' => array(	'onclick' => 'function(){setCurrentRow(this);}',
											'readOnly' => '"true"',
											'size' => '"3"',
											'tabIndex' => '"-1"')
						),
					array(
					'db_field' => 'sort_index',
					'caption' => 'Sort Index',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'none',
					'properties' => array(	'onclick' => 'function(){setCurrentRow(this);}',
											'readOnly' => '"true"',
											'size' => '"3"',
											'tabIndex' => '"-1"')
						),
					/*array('db_field' => 'pos_product_attribute_id',
						'caption' => 'Attribute',
						'type' => 'select',
						'select_names' => $attribute_options['attribute_name'],
						'select_values' => $attribute_options['pos_product_attribute_id'],
						'properties' => array(	'style.width' => '"15em"',
												'className' => '"nothing"',
												'onclick' => 'function(){setCurrentRow(this);}',
												'onblur' => 'function(){updateTableData(this);}',
												'onkeyup' => 'function(){checkValidInput(this);}',
												'onmouseup' => 'function(){updateTableData(this);}',
												'onkeypress' => 'function(){changeRowAndColumnWithArrow(event, this, tbody_id);return noEnter(event);}',)),*/
						array('caption' => 'Product Option System Id',
						'db_field' => 'pos_product_option_id',
						'type' => 'input',
						'element' => 'input',
						'element_type' => 'text',
						'properties' => array(	'readOnly' => '"true"', 'className' => '"readonly"', 'size'=> '"15"',
						'onclick' => 'function(){setCurrentRow(this);}',
												'onblur' => 'function(){updateTableData(this);}',
												'onkeyup' => 'function(){checkValidInput(this);}',
												'onkeypress' => 'function(){changeRowAndColumnWithArrow(event, this, tbody_id);return noEnter(event);}',
												'onmouseup' => 'function(){updateTableData(this);}'
												)
						),
					array('caption' => 'Size Code',
						'db_field' => 'size_code',
						'type' => 'input',
						'element' => 'input',
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"','onclick' => 'function(){setCurrentRow(this);}',
												'onblur' => 'function(){updateTableData(this);}',
												'onkeyup' => 'function(){checkValidInput(this);}',
												'onkeypress' => 'function(){changeRowAndColumnWithArrow(event, this, tbody_id);return noEnter(event);}',
												'onmouseup' => 'function(){updateTableData(this);}'
												)),
					array('caption' => 'Size Name',
						'db_field' => 'size_name',
						'type' => 'input',
						'element' => 'input',
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"','onclick' => 'function(){setCurrentRow(this);}',
												'onblur' => 'function(){updateTableData(this);}',
												'onkeyup' => 'function(){checkValidInput(this);}',
												'onkeypress' => 'function(){changeRowAndColumnWithArrow(event, this, tbody_id);return noEnter(event);}',
												'onmouseup' => 'function(){updateTableData(this);}'
												)),
					/*array('caption' => 'Cup',
						'db_field' => 'cup_code',
						'type' => 'input',
						'element' => 'input',
						'element_type' => 'text',
						'properties' => array(	'size' => '"15"','onclick' => 'function(){setCurrentRow(this);}',
												'onblur' => 'function(){updateTableData(this);}',
												'onkeyup' => 'function(){checkValidInput(this);}',
												'onkeypress' => 'function(){changeRowAndColumnWithArrow(event, this, tbody_id);return noEnter(event);}',
												'onmouseup' => 'function(){updateTableData(this);}'
												))*/
												
					);
	
	return $columns;
}

function getProductAttributes($pos_product_id)
{
	return getFieldRowSQL("SELECT DISTINCT pos_product_attribute_id, attribute_name FROM pos_product_attributes INNER JOIN pos_product_options USING (pos_product_attribute_id) WHERE pos_product_options.pos_product_id = $pos_product_id");

}
function getProductSubIdOptions($pos_product_sub_id)
{
	$sql = "SELECT pos_product_sub_id_options.pos_product_option_id, option_code, option_name, 
					attribute_name, pos_product_options.pos_product_attribute_id
			FROM pos_product_sub_id_options 
			LEFT JOIN pos_product_options USING( pos_product_option_id)
			LEFT JOIN pos_product_attributes USING (pos_product_attribute_id)
			WHERE pos_product_sub_id_options.pos_product_sub_id = $pos_product_sub_id
			";
			
			
	$data = getSQL($sql);
	for($row=0;$row<sizeof($data);$row++)
	{
		$pos_product_id = getProductIdFromProductSubId($pos_product_sub_id);
		$options = getProductOptions($pos_product_id, getProductAttributeId($data[$row]['attribute_name']));
		//$options = getAvailableSubIdOptions($pos_product_id, $data[$row]['attribute_name']);
		$data[$row]['options']['names'] = $options['option_code_name'];
		$data[$row]['options']['values'] = $options['pos_product_option_id'];
		
		
	}
	
	
		return $data;
}


function createDynamicSubIdOptionTableDef($pos_product_sub_id)
{
	
	//get the options available For The Product
	$pos_product_id = getProductIdFromProductSubId($pos_product_sub_id);;
	$attributes = getProductAttributes($pos_product_id);
	
	//individual_select_options look like: data[the field named in individual select options][
	//after the attribute is selected, we need to select the option code and whalla done	
	//the option code and update dynamically
	//validate only a single option can be chosen.... 
	
		$columns = array(
					array('db_field' => 'none',
						'POST' => 'no',
						'caption' => '',
						'th_width' => '14px',
						'type' => 'checkbox',
						'element' => 'input',
						'element_type' => 'checkbox',
						'properties' => array(	'onclick' => 'function(){setSingleCheck(this);}'
												)),
					array(
					'db_field' => 'row_number',
					'caption' => 'Row',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'none',
					'properties' => array(	'onclick' => 'function(){setCurrentRow(this);}',
											'readOnly' => '"true"',
											'size' => '"3"',
											'tabIndex' => '"-1"')
						),
					array('db_field' => 'pos_product_attribute_id',
						'caption' => 'Attribute',
						'type' => 'select',
						'unique_select_options' => true,
						'select_names' => $attributes['attribute_name'],
						'select_values' => $attributes['pos_product_attribute_id'],
						'properties' => array(	'style.width' => '"15em"',
												'className' => '"nothing"',
												'onchange' => 'function(){updateOptionCodeData(this);}',
											)),

					array('caption' => 'Option Code',
						'db_field' => 'pos_product_option_id',
						'type' => 'select',
						'individual_select_options' => 'options',
						'select_names' => array(),
						'select_values' => array(),
						'properties' => array(	'style.width' => '"15em"',
												'className' => '"nothing"',
											'onchange' => 'function(){updateTableData(this);}',
												'onclick' => 'function(){setCurrentRow(this);}',
												'onblur' => 'function(){updateTableData(this);}',
												'onkeyup' => 'function(){checkValidInput(this);}',
												'onmouseup' => 'function(){updateTableData(this);}',
												))
					
					
					
				);			
						
		
		return $columns;
	
	
	
	
}
function createImageCoordinatorProductTableDef($product_table_name)
{
	$table_object_name = $product_table_name . '_object';
	$select_ids = array();
	$select_names = array();		
	//this is the select values
	$selectable_brands = getBrands();
	for($i=0;$i<sizeof($selectable_brands);$i++)
	{
		$select_ids[$i]= $selectable_brands[$i]['pos_manufacturer_brand_id'];
		$select_names[$i] = $selectable_brands[$i]['brand_name'];	
	}
	
	
	//categories
	$categories = getNoParentCategoryArray();
	$category_names = $categories['name'];
	$category_ids = $categories['pos_category_id'];
	
		$columns = array(
					array('db_field' => 'none',
						'POST' => 'no',
						'caption' => '',
						'th_width' => '14px',
						'type' => 'checkbox',
						'element' => 'input',
						'element_type' => 'checkbox',
						'properties' => array(	'onclick' => 'function(){'.$table_object_name.'.setSingleCheck(this);}'
												)),
					array(
					'db_field' => 'row_number',
					'caption' => 'Row',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'none',
					'properties' => array(	'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
											'readOnly' => '"true"',
											'size' => '"3"',
											'tabIndex' => '"-1"')
						),
				
					array(
					'db_field' => 'barcode',
					'caption' => 'Barcode',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'readOnly' => 'true',
											'className' => '"readonly"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
												'onblur' => 'function(){'.$table_object_name.'.updateTableData(this);}',
												'onkeyup' => 'function(){'.$table_object_name.'.checkValidInput(this);}',
												'onmouseup' => 'function(){'.$table_object_name.'.updateTableData(this);}',
												'onkeypress' => 'function(e){return noEnter(e);}',
											'size' => '"30"')
						),
					
	
	array('caption' => 'Title',
						'db_field' => 'big_title',
						'type' => 'input',
						'element' => 'input',
						'element_type' => 'text',
						'valid_input' => '-'.uppercase().lowercase().safesymbols().integers(), //'-ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
						'validate' => array('not_blank_or_zero_or_false_or_null' => 1, 'acceptable_values' => array('specific'=>uppercase().lowercase().safesymbols().integers())),
						'properties' => array(	'readOnly' => 'true',
											'className' => '"readonly"','size' => '"130"','onclick' => 'function(){setCurrentRow(this);}',
												'onblur' => 'function(){'.$table_object_name.'.updateTableData(this);}',
												'onkeyup' => 'function(){'.$table_object_name.'.checkValidInput(this);}',
												'onmouseup' => 'function(){'.$table_object_name.'.updateTableData(this);}',
												'onkeypress' => 'function(e){return noEnter(e);}'
												)),
					
					array(
					'db_field' => 'pos_product_sub_id',
					'type' => 'hidden'
					)
					
				);			
						
		
		return $columns;
	
	
	
}
function createImageCoordinatorProductTableDefforView($product_table_name)
{
	$table_object_name = $product_table_name . '_object';
	$select_ids = array();
	$select_names = array();		
	//this is the select values
	$selectable_brands = getBrands();
	for($i=0;$i<sizeof($selectable_brands);$i++)
	{
		$select_ids[$i]= $selectable_brands[$i]['pos_manufacturer_brand_id'];
		$select_names[$i] = $selectable_brands[$i]['brand_name'];	
	}
	
	
	//categories
	$categories = getNoParentCategoryArray();
	$category_names = $categories['name'];
	$category_ids = $categories['pos_category_id'];
	
		$columns = array(
					array('db_field' => 'none',
						'POST' => 'no',
						'caption' => '',
						'th_width' => '14px',
						'type' => 'checkbox',
						'element' => 'input',
						'element_type' => 'checkbox',
						'properties' => array(	'onclick' => 'function(){'.$table_object_name.'.setSingleCheck(this);}'
												)),
					array(
					'db_field' => 'row_number',
					'caption' => 'Row',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'none',
					'properties' => array(	'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
											'readOnly' => '"true"',
											'size' => '"3"',
											'tabIndex' => '"-1"')
						),
				
					
					array(
					'db_field' => 'pos_product_id',
					'caption' => 'System <br> Product ID',
					'type' => 'link',
					'get_id_link' => 'pos_product_id',
					'get_url_link' => POS_ENGINE_URL .'/products/ViewProduct/view_product.php',
					
					),
	
	array('caption' => 'Description',
						'db_field' => 'description',
						'type' => 'input',
						'element' => 'input',
						'element_type' => 'text',
						'valid_input' => '-'.uppercase().lowercase().safesymbols().integers(), //'-ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
						'validate' => array('not_blank_or_zero_or_false_or_null' => 1, 'acceptable_values' => array('specific'=>uppercase().lowercase().safesymbols().integers())),
						'properties' => array(	'readOnly' => 'true',
											'className' => '"readonly"','size' => '"30"','onclick' => 'function(){setCurrentRow(this);}',
												'onblur' => 'function(){'.$table_object_name.'.updateTableData(this);}',
												'onkeyup' => 'function(){'.$table_object_name.'.checkValidInput(this);}',
												'onmouseup' => 'function(){'.$table_object_name.'.updateTableData(this);}',
												'onkeypress' => 'function(e){return noEnter(e);}'
												)),
				array('caption' => 'Overview',
						'db_field' => 'overview',
						'type' => 'input',
						'element' => 'input',
						'element_type' => 'text',
						'valid_input' => '-'.uppercase().lowercase().safesymbols().integers(), //'-ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
						'validate' => array('not_blank_or_zero_or_false_or_null' => 1, 'acceptable_values' => array('specific'=>uppercase().lowercase().safesymbols().integers())),
						'properties' => array(	'readOnly' => 'true',
											'className' => '"readonly"','size' => '"30"','onclick' => 'function(){setCurrentRow(this);}',
												'onblur' => 'function(){'.$table_object_name.'.updateTableData(this);}',
												'onkeyup' => 'function(){'.$table_object_name.'.checkValidInput(this);}',
												'onmouseup' => 'function(){'.$table_object_name.'.updateTableData(this);}',
												'onkeypress' => 'function(e){return noEnter(e);}'
												)),
					
					array(
					'db_field' => 'pos_product_sub_id',
					'type' => 'hidden'
					)
					
				);			
						
		
		return $columns;
	
	
	
}
function createImageCoordinatorImageTableDef($table_name)
{
	$table_object_name = $table_name . '_object';
	$select_ids = array();
	$select_names = array();		
	//this is the select values
	$selectable_brands = getBrands();
	for($i=0;$i<sizeof($selectable_brands);$i++)
	{
		$select_ids[$i]= $selectable_brands[$i]['pos_manufacturer_brand_id'];
		$select_names[$i] = $selectable_brands[$i]['brand_name'];	
	}
	
	
	//categories
	$categories = getNoParentCategoryArray();
	$category_names = $categories['name'];
	$category_ids = $categories['pos_category_id'];
	
		$columns = array(
					array('db_field' => 'none',
						'POST' => 'no',
						'caption' => '',
						'th_width' => '14px',
						'type' => 'checkbox',
						'element' => 'input',
						'element_type' => 'checkbox',
						'properties' => array(	'onclick' => 'function(){'.$table_object_name.'.setSingleCheck(this);}'
												)),
					array(
					'db_field' => 'row_number',
					'caption' => 'Row',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'none',
					'properties' => array(	'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
											'readOnly' => '"true"',
											'size' => '"3"',
											'tabIndex' => '"-1"')
						),
				
					array('caption' => 'Image Name',
						'db_field' => 'original_image_name',
						'type' => 'input',
						'element' => 'input',
						'element_type' => 'text',
						'valid_input' => '-'.uppercase().lowercase().safesymbols().integers(), //'-ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789',
						'validate' => array('not_blank_or_zero_or_false_or_null' => 1, 'acceptable_values' => array('specific'=>uppercase().lowercase().safesymbols().integers())),
						'properties' => array(	'size' => '"30"','onclick' => 'function(){setCurrentRow(this);}',
												'onblur' => 'function(){'.$table_object_name.'.updateTableData(this);}',
												'onkeyup' => 'function(){'.$table_object_name.'.checkValidInput(this);}',
												'onmouseup' => 'function(){'.$table_object_name.'.updateTableData(this);}',
												'onkeypress' => 'function(e){return noEnter(e);}'
												)),		
					array('caption' => 'Image Order',
						'db_field' => 'image_order',
						'type' => 'input',
						'element' => 'input',
						'element_type' => 'text',
						'valid_input' => integers(), 
						'validate' => array('not_blank_or_zero_or_false_or_null' => 1, 'acceptable_values' => array('specific'=>integers())),
						'properties' => array(	'size' => '"30"','onclick' => 'function(){setCurrentRow(this);}',
												'onblur' => 'function(){'.$table_object_name.'.updateTableData(this);}',
												'onkeyup' => 'function(){'.$table_object_name.'.checkValidInput(this);}',
												'onmouseup' => 'function(){'.$table_object_name.'.updateTableData(this);}',
												'onkeypress' => 'function(e){return noEnter(e);}'
												))
					
				);			
						
		
		return $columns;
	
	
	
}
function check_product_image_exists($pos_product_image_id)
{
	//if the thumb exists display it...
	$thumbnail_path = POS_PATH . PRODUCT_IMAGE_THUMBNAIL_FOLDER . $pos_product_image_id .'.jpg';
	$linked_path = POS_PATH . PRODUCT_IMAGE_FOLDER. $pos_product_image_id .'.jpg';
	if (file_exists ( $thumbnail_path ) && file_exists ( $linked_path ))
	{
		return true;
	}
	else
	{
		return false;
	}
}
function product_image_html($pos_product_image_id)
{
		//add the image
	
	$thumbnail_url = POS_URL . PRODUCT_IMAGE_THUMBNAIL_FOLDER . $pos_product_image_id .'.jpg';
	$linked_file = POS_URL . PRODUCT_IMAGE_FOLDER. $pos_product_image_id .'.jpg';
	
	$html = '';
	
	if (check_product_image_exists($pos_product_image_id))
	{
		$html .= '<p>';
		$html .= '<a href="'.$linked_file.'" target="_blank"><img src="'.$thumbnail_url.'" /></a>'.newline();
		$html .= '</p>'.newline();
		
		
		
	} else
	{
		$html .= '<p>';
		$html .= 'No Thumbnail';
		$html .= '</p>'.newline();
	}
	return $html;
}
function createPinnacleCartCSVProductTableDef($pos_product_image_id, $table_name)
{
	//this table def replaces the .csv

	$table_object_name = $table_name . '_object';


	$columns = array(
		
				array(
					'db_field' => 'pos_product_id',
					'type' => 'hidden',
					
					),
				array(
					'db_field' => 'pos_product_sub_id',
					'type' => 'hidden',
					),


				array('db_field' => 'none',
					'POST' => 'no',
					'caption' => '',
					'th_width' => '14px',
					'type' => 'row_checkbox',
					'element' => 'input',
					'element_type' => 'checkbox',
					'properties' => array(	'onclick' => 'function(){'.$table_object_name.'.setSingleCheck(this);}'
											)),
				array(
				'db_field' => 'row_number',
				'caption' => 'Row',
				'type' => 'input',
				'element' => 'input',
				'element_type' => 'none',
				'properties' => array(	'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
										'readOnly' => '"true"',
										'size' => '"3"')
					),
				array('db_field' => 'web_product_id',
					'caption' => 'Web Product Id',
					//'td_width' => '50%',
					//'th_width' => '50%',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	
					
					
												'onblur' => 'function(){'.$table_object_name.'.updateTableData(this);}',
												'onkeyup' => 'function(){'.$table_object_name.'.checkValidInput(this);}',
												'onmouseup' => 'function(){'.$table_object_name.'.updateTableData(this);}',
												'onkeypress' => 'function(e){return noEnter(e);}',
												
												
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')
					),
				array('db_field' => 'name',
					'caption' => 'Web Title',
					//'td_width' => '50%',
					//'th_width' => '50%',
					'word_wrap' => 10,
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"150"',
					'onblur' => 'function(){'.$table_object_name.'.updateTableData(this);}',
												'onkeyup' => 'function(){'.$table_object_name.'.checkValidInput(this);}',
												'onmouseup' => 'function(){'.$table_object_name.'.updateTableData(this);}',
												'onkeypress' => 'function(e){return noEnter(e);}',
												
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')
					),
			/*	array('caption' => 'Overview',
					'db_field' => 'overview',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"40"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),
				array('caption' => 'Description',
					'db_field' => 'description',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"40"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),*/
				array('caption' => 'Categories<br>Separate Categories<br> With ||',
				'db_field' => 'catagories',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"20"',
											'className' => '"size"',
											'onblur' => 'function(){'.$table_object_name.'.updateTableData(this);}',
												'onkeyup' => 'function(){'.$table_object_name.'.checkValidInput(this);}',
												'onmouseup' => 'function(){'.$table_object_name.'.updateTableData(this);}',
												'onkeypress' => 'function(e){return noEnter(e);}',
												
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')),		
				array('caption' => 'Price',
					'db_field' => 'price',
					'type' => 'input',
					'round' => 2,
					'valid_input' => '0123456789.',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"10"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);this.select()}',
											'onkeyup' => 'function(){'.$table_object_name.'.checkValidInput(this);}',
											'onblur' => 'function(){'.$table_object_name.'.updateTableData(this);}',
												
												'onmouseup' => 'function(){'.$table_object_name.'.updateTableData(this);}',
												'onkeypress' => 'function(e){return noEnter(e);}',
												)),
			array('caption' => 'Weight',
					'db_field' => 'weight',
					'type' => 'input',
					'round' => 2,
					'valid_input' => '0123456789.',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"10"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);this.select()}',
											'onblur' => 'function(){'.$table_object_name.'.updateTableData(this);}',
												'onkeyup' => 'function(){'.$table_object_name.'.checkValidInput(this);}',
												'onmouseup' => 'function(){'.$table_object_name.'.updateTableData(this);}',
												'onkeypress' => 'function(e){return noEnter(e);}')),	
				
				array('caption' => 'Attr1 Name',
					'db_field' => 'attribute1_name',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"10"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);this.select()}',
											'onblur' => 'function(){'.$table_object_name.'.updateTableData(this);}',
												'onkeyup' => 'function(){'.$table_object_name.'.checkValidInput(this);}',
												'onmouseup' => 'function(){'.$table_object_name.'.updateTableData(this);}',
												'onkeypress' => 'function(e){return noEnter(e);}')),	
				array('caption' => 'Attr1 options<br>Separate Options <br> With ||',
					'db_field' => 'attribute1_list',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"10"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);this.select()}',
											'onblur' => 'function(){'.$table_object_name.'.updateTableData(this);}',
												'onkeyup' => 'function(){'.$table_object_name.'.checkValidInput(this);}',
												'onmouseup' => 'function(){'.$table_object_name.'.updateTableData(this);}',
												'onkeypress' => 'function(e){return noEnter(e);}')),	
				array('caption' => 'Attr2 Name',
					'db_field' => 'attribute2_name',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"10"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);this.select()}',
											'onblur' => 'function(){'.$table_object_name.'.updateTableData(this);}',
												'onkeyup' => 'function(){'.$table_object_name.'.checkValidInput(this);}',
												'onmouseup' => 'function(){'.$table_object_name.'.updateTableData(this);}',
												'onkeypress' => 'function(e){return noEnter(e);}')),	
				array('caption' => 'Attr2 options <br>Separate Options <br> With ||',
					'db_field' => 'attribute2_list',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"10"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);this.select()}',
											'onblur' => 'function(){'.$table_object_name.'.updateTableData(this);}',
												'onkeyup' => 'function(){'.$table_object_name.'.checkValidInput(this);}',
												'onmouseup' => 'function(){'.$table_object_name.'.updateTableData(this);}',
												'onkeypress' => 'function(e){return noEnter(e);}')),							
											
			);			
					
	
	return $columns;
	
	
	
}
function createPinnacleCartSecondaryImageTableDef($pos_product_image_id, $table_name)
{
	//this table def replaces the .csv

	$table_object_name = $table_name . '_object';


	$columns = array(
		
				
				array(
					'db_field' => 'pos_product_sub_id',
					'type' => 'hidden',
					),


				array('db_field' => 'none',
					'POST' => 'no',
					'caption' => '',
					'th_width' => '14px',
					'type' => 'row_checkbox',
					'element' => 'input',
					'element_type' => 'checkbox',
					'properties' => array(	'onclick' => 'function(){'.$table_object_name.'.setSingleCheck(this);}'
											)),
				array(
				'db_field' => 'row_number',
				'caption' => 'Row',
				'type' => 'input',
				'element' => 'input',
				'element_type' => 'none',
				'properties' => array(	'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
										'readOnly' => '"true"',
										'size' => '"3"')
					),
				array('db_field' => 'web_product_id',
					'caption' => 'Web Product Id',
					//'td_width' => '50%',
					//'th_width' => '50%',
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"30"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')
					),
				array('db_field' => 'name',
					'caption' => 'Web Title',
					//'td_width' => '50%',
					//'th_width' => '50%',
					'word_wrap' => 10,
					'type' => 'input',
					'element' => 'input',
					'element_type' => 'text',
					'properties' => array(	'size' => '"30"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}')
					),
								
											
			);			
					
	
	return $columns;
	
	
	
}
?>