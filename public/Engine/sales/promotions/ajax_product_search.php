<?php

$binder_name = 'Promotions';
$access_type = 'WRITE';
require_once ('../sales_functions.php');

/* OK ojk ok we want to search a product to find a sub id
	we need to search the brand name
	the title
	the options list
	
	and link each entry to a sub id
	
	we will pass in a string that should be split via spaces
*/
$product_search_terms = urldecode(getPostOrGetValue('product_search_terms'));

$search_array = explode(' ', $product_search_terms);

//create a list of sub_ids -> from there turn that to an array


//create a list of all the products then use a tmp table to select those...
$titles_sql = "CREATE TEMPORARY TABLE products
SELECT pos_products_sub_id.pos_product_sub_id, concat(pos_manufacturer_brands.brand_name,':',pos_products.title,':',pos_products.style_number,':',
		
			(SELECT group_concat(concat(option_code,'-',option_name) SEPARATOR ' ') 
			FROM pos_product_sub_id_options 
			LEFT JOIN pos_product_options ON pos_product_sub_id_options.pos_product_option_id = pos_product_options.pos_product_option_id 
			LEFT JOIN pos_product_attributes ON pos_product_options.pos_product_attribute_id = pos_product_attributes.pos_product_attribute_id
			WHERE pos_products_sub_id.pos_product_sub_id = pos_product_sub_id_options.pos_product_sub_id )
		
			) as long_name
		
		FROM pos_products_sub_id 
		LEFT JOIN pos_products ON pos_products_sub_id.pos_product_id = pos_products.pos_product_id
		LEFT JOIN pos_manufacturer_brands ON pos_products.pos_manufacturer_brand_id = pos_manufacturer_brands.pos_manufacturer_brand_id
		;
		";
	
$search_array_sql = '';
for($si=0;$si<sizeof($search_array);$si++)
{
	$search_array_sql .= " AND long_name LIKE '%". scrubInput($search_array[$si]) ."%' ";
}
$select_sql = "SELECT * FROM products WHERE 1" .$search_array_sql .' LIMIT 10';

$dbc = openPOSdb();
$result = runTransactionSQL($dbc,'SET SESSION group_concat_max_len=1000');
$result = runTransactionSQL($dbc,$titles_sql);
$data = getTransactionFieldRowSql($dbc,$select_sql);
closeDB($dbc);

echo json_encode($data);
/*function getProductSubidBrandTitleStyleOptions($pos_product_sub_id)
{
	$sql = "SELECT pos_products_sub_id.pos_product_sub_id, concat(pos_manufacturer_brands.brand_name,':',pos_products.title,':',pos_products.style_number,':',
		
			(SELECT group_concat(concat(option_code,'-',option_name) SEPARATOR ' ') 
			FROM pos_product_sub_id_options 
			LEFT JOIN pos_product_options ON pos_product_sub_id_options.pos_product_option_id = pos_product_options.pos_product_option_id 
			LEFT JOIN pos_product_attributes ON pos_product_options.pos_product_attribute_id = pos_product_attributes.pos_product_attribute_id
			WHERE pos_products_sub_id.pos_product_sub_id = pos_product_sub_id_options.pos_product_sub_id )
		
			) as long_name
		
		FROM pos_products_sub_id 
		LEFT JOIN pos_products ON pos_products_sub_id.pos_product_id = pos_products.pos_product_id
		LEFT JOIN pos_manufacturer_brands ON pos_products.pos_manufacturer_brand_id = pos_manufacturer_brands.pos_manufacturer_brand_id
		WHER pos_products_sub_id.pos_product_sub_id = $pos_product_sub_id";
		
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,'SET SESSION group_concat_max_len=1000');
	$data = getTransactionSingleValueSql($dbc,$sql);
	closeDB($dbc);
	return $data;
		
}*/
?>