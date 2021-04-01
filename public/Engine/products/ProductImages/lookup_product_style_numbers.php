<?
$binder_name = 'Products';
$access_type = 'WRITE';
require_once('../product_functions.php');

$pos_manufacturer_brand_id = scrubInput(getPostOrGetValue('pos_manufacturer_brand_id'));
//$style_number = scrubInput(getPostOrGetDataIfAvailable('style_number'));
//$color_code = scrubInput(getPostOrGetDataIfAvailable('color_code'));


	$sql = "SELECT style_number
		
		FROM pos_products
		WHERE pos_manufacturer_brand_id = '$pos_manufacturer_brand_id'

		
		";

$data1 = getFieldRowSQL($sql);

	$sql = "SELECT product_subid_name
		
		FROM pos_products_sub_id
		LEFT JOIN pos_products USING (pos_product_id)
		WHERE pos_manufacturer_brand_id = '$pos_manufacturer_brand_id'

		
		";
$data2 = getFieldRowSQL($sql);

$return_data[0] = $data1;
$return_data[1] = $data2;

echo json_encode($return_data) . "\n";



?>