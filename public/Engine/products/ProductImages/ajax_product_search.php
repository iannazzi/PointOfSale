<?php

$binder_name = 'Sales Invoices';
$access_type = 'WRITE';
$page_title = 'Sales Invoice';
require_once('../product_functions.php');

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
/*$subids = array();
for($si=0;$si<sizeof($search_array);$si++)
{
	$search_term = $search_array[$si];
	//1 search the brand name
	
	$brand_name_sql = "
	SELECT pos_product_sub_id FROM pos_products_sub_id
	LEFT JOIN pos_products USING (pos_product_id)
	LEFT JOIN pos_manufacturer_brands USING (pos_manufacturer_brand_id)
	WHERE brand_name LIKE '%$search_term%'";
	$brand_name_results = getFieldRowSql($brand_name_sql);
	if(sizeof($brand_name_results)>0)
	{
		$subids = array_merge($subids,$brand_name_results['pos_product_sub_id']);
	}
	
	//2 search the title
	$title_sql = "
	SELECT pos_product_sub_id FROM pos_products_sub_id
	LEFT JOIN pos_products USING (pos_product_id)
	WHERE title LIKE '%$search_term%'";
	$title_results = getFieldRowSql($title_sql);
	if(sizeof($title_results)>0)
	{
		$subids = array_merge($subids,$title_results['pos_product_sub_id']);
	}
	//3 search the style number
		$style_sql = "
	SELECT pos_product_sub_id FROM pos_products_sub_id
	LEFT JOIN pos_products USING (pos_product_id)
	WHERE style_number LIKE '%$search_term%'";
	$style_results = getFieldRowSql($style_sql);
	if(sizeof($style_results)>0)
	{
		$subids = array_merge($subids,$style_results['pos_product_sub_id']);
	}
	
	//4 search the options list
	
	$option_sql="	SELECT pos_product_sub_id 
			FROM pos_product_sub_id_options 
			LEFT JOIN pos_product_options ON pos_product_sub_id_options.pos_product_option_id = pos_product_options.pos_product_option_id 
			WHERE option_name LIKE '%$search_term' OR option_code LIKE '%$search_term'";
	$option_results = getFieldRowSql($option_sql);
	if(sizeof($option_results)>0)
	{
		$subids = array_merge($subids,$option_results['pos_product_sub_id']);
	}
	
	
	
	
	//5 search the services list
	
	//6 search the promotions
	
	

}*/

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

/*//now convert the subids into a full blown listing
//first limit the subids to 12
$imploded_subids = implode(',', $subids);

$titles_sql = "SELECT concat(pos_manufacturer_brands.brand_name,':',pos_products.title,':',pos_products.style_number,':',
		
			(SELECT group_concat(concat(option_name) SEPARATOR ' ') 
			FROM pos_product_sub_id_options 
			LEFT JOIN pos_product_options ON pos_product_sub_id_options.pos_product_option_id = pos_product_options.pos_product_option_id 
			LEFT JOIN pos_product_attributes ON pos_product_options.pos_product_attribute_id = pos_product_attributes.pos_product_attribute_id
			WHERE pos_products_sub_id.pos_product_sub_id = pos_product_sub_id_options.pos_product_sub_id )
		
			) as long_name
		
		FROM pos_products_sub_id 
		LEFT JOIN pos_products ON pos_products_sub_id.pos_product_id = pos_products.pos_product_id
		LEFT JOIN pos_manufacturer_brands ON pos_products.pos_manufacturer_brand_id = pos_manufacturer_brands.pos_manufacturer_brand_id
		WHERE pos_products_sub_id.pos_product_sub_id IN (" . $imploded_subids .")
";
$return_titles = getFieldRowSQL($titles_sql);*/

echo json_encode($data);

?>