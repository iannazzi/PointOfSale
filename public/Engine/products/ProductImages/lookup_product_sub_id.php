<?
$binder_name = 'Products';
$access_type = 'WRITE';
require_once('../product_functions.php');

$pos_manufacturer_brand_id = scrubInput(getPostOrGetValue('pos_manufacturer_brand_id'));
$style_number = scrubInput(getPostOrGetValue('style_number'));
$pos_color_option_id = scrubInput(getPostOrGetValue('pos_color_option_id'));
$pos_size_option_id = scrubInput(getPostOrGetValue('pos_size_option_id'));

$option_ids = array($pos_color_option_id,$pos_size_option_id);
/*$options_array = array(array(	'attribute_name' => 'Color',
								'option_code' => $color_code),
						array( 'attribute_name' => 'Size',
								'option_code' => $size));*/
//to get the product sub id we first need to find the option ids for all the options. then we select the sub_id that has all the options.
//$pos_product_sub_id = getProductSubIdFromAttributeNames($pos_manufacturer_brand_id, $style_number, $options_array);
$pos_product_sub_id = getProductSubIdFromOptionIds($option_ids);

//now get product details...
$sql = "SELECT  pos_products_sub_id.pos_product_id, product_subid_name, 
		 retail_price, sale_price, cost, title, style_number
		FROM pos_products_sub_id
		LEFT JOIN pos_products ON pos_products_sub_id.pos_product_id = pos_products.pos_product_id
		WHERE pos_product_sub_id = $pos_product_sub_id";


$data = getSQL($sql);

if(sizeof($data)>0)
{
	$return_data['style_number'] = $data[0]['style_number'];
	$return_data['product_subid_name'] = $data[0]['product_subid_name'];
	$return_data['pos_product_id'] = $data[0]['pos_product_id'];
	$return_data['pos_product_sub_id'] = $pos_product_sub_id;
	$return_data['quantity'] = 1;
	$return_data['retail_price'] = $data[0]['retail_price'];
	$return_data['cost'] = $data[0]['cost'];
	$return_data['value'] = $data[0]['cost'];
	$return_data['inventory_type'] = 'Available';
	$return_data['sale_price'] = $data[0]['sale_price'];
	$return_data['title'] = $data[0]['title'];
	$return_data['size'] = getProductOptionCode($pos_product_sub_id, getProductAttributeId('Size'));
	$return_data['color_code'] = getProductOptionCode($pos_product_sub_id, getProductAttributeId('Color'));
	$return_data['cup'] = getProductOptionCode($pos_product_sub_id, getProductAttributeId('Cup'));	
	$return_data['color_description'] = getProductOptionName($pos_product_sub_id, getProductAttributeId('Color'));
	$return_data['pos_manufacturer_brand_id'] = getBrandFromProductID($data[0]['pos_product_id']);
	$return_data['pos_category_id'] =  getProductCategory($data[0]['pos_product_id']);
	$return_data['brand_name'] = getBrandName(getBrandFromProductId($data[0]['pos_product_id']));
	$return_data['comments'] = '';
	echo json_encode($return_data) . "\n";
}
else
{
	echo "No Data Found For Barcode";
}




?>