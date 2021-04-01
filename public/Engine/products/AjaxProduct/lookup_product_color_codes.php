<?
$binder_name = 'Products';
$access_type = 'WRITE';
require_once('../product_functions.php');

$pos_manufacturer_brand_id = scrubInput(getPostOrGetValue('pos_manufacturer_brand_id'));
$style_number = scrubInput(getPostOrGetValue('style_number'));
//$color_code = scrubInput(getPostOrGetDataIfAvailable('color_code'));

	$sql1 = "SELECT color_code
		
		FROM pos_product_colors
		LEFT JOIN pos_products ON pos_product_colors.pos_product_id = pos_products.pos_product_id
		WHERE pos_products.pos_manufacturer_brand_id = '$pos_manufacturer_brand_id'
		AND pos_products.style_number = '$style_number'

		
		";
		$sql1 = "SELECT pos_product_option_id, concat(option_code,'::',option_name) as code_name
		
		FROM pos_product_options
		LEFT JOIN pos_products ON pos_product_options.pos_product_id = pos_products.pos_product_id
		LEFT JOIN pos_product_attributes USING (pos_product_attribute_id)
		WHERE pos_products.pos_manufacturer_brand_id = '$pos_manufacturer_brand_id'
		AND pos_products.style_number = '$style_number' AND attribute_name = 'Color'

		
		";
	$sql2 = "SELECT product_subid_name
		
		FROM pos_products_sub_id
		LEFT JOIN pos_products USING (pos_product_id)		
		WHERE pos_products.pos_manufacturer_brand_id = '$pos_manufacturer_brand_id'
		AND pos_products.style_number = '$style_number'

		
		";
$return_data[0] = getFieldRowSQL($sql1);
$return_data[1] = getFieldRowSQL($sql2);
echo json_encode($return_data) . "\n";


?>