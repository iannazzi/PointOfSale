<?
$binder_name = 'Locations';
$access_type = 'WRITE';
require_once('../inventory_functions.php');
$barcode = scrubInput(getPostOrGetValue('barcode'));

//get the product id, colors, sizes
if (ctype_digit($barcode) && sizeof(getSQL("SELECT pos_product_sub_id FROM pos_products_sub_id WHERE pos_product_sub_id = '$barcode'")) >0)
{

	//product
	$sql = "SELECT pos_product_sub_id, pos_products_sub_id.pos_product_id, product_subid_name, attributes_list,
		 retail_price, sale_price, title, cost, style_number, '0' as price_level
		FROM pos_products_sub_id
		LEFT JOIN pos_products ON pos_products_sub_id.pos_product_id = pos_products.pos_product_id
		WHERE pos_product_sub_id = '$barcode'";
	$data = getSQL($sql);

	if(sizeof($data)>0)
	{
		echo json_encode(convertProductDataForReturn($data, $barcode)) . "\n";
	}
}
else if (sizeof(getSQL("SELECT pos_product_sub_id FROM pos_products_sub_id WHERE product_subid_name = '$barcode'")) >0)
{
	

	//definately a barcode
	//get the product id, colors, sizes

	$sql = "SELECT pos_product_sub_id, pos_products_sub_id.pos_product_id, product_subid_name, attributes_list,
		 retail_price, sale_price, title, cost, style_number,  '0' as price_level
		FROM pos_products_sub_id
		LEFT JOIN pos_products ON pos_products_sub_id.pos_product_id = pos_products.pos_product_id
		WHERE product_subid_name = '$barcode'";
	$data = getSQL($sql);

	if(sizeof($data)>0)
	{
		echo json_encode(convertProductDataForReturn($data, $barcode)) . "\n";
	}
}
elseif(sizeof(getSQL("SELECT pos_product_sub_id FROM pos_product_sub_sale_price WHERE sale_barcode = '$barcode'"))>0)
{
	//barcode is a digit followd by p 1 through N
	//basically get the barcode product, then get the sale price and check if it is celarenced.
	//list($sub_id,$price_level) = explode('P', $barcode);
	$sql = "SELECT pos_products_sub_id.pos_product_sub_id, pos_products_sub_id.pos_product_id, product_subid_name, attributes_list,
		 retail_price, pos_product_sub_sale_price.title, pos_product_sub_sale_price.price as sale_price, cost, style_number, pos_product_sub_sale_price.price_level
		FROM pos_products_sub_id
		LEFT JOIN pos_product_sub_sale_price ON pos_products_sub_id.pos_product_sub_id = pos_product_sub_sale_price.pos_product_sub_id
		LEFT JOIN pos_products ON pos_products_sub_id.pos_product_id = pos_products.pos_product_id
		WHERE pos_product_sub_sale_price.sale_barcode = '$barcode'";
	
	$data = getSQL($sql);
	if(sizeof($data)>0)
	{
		echo json_encode(convertProductDataForReturn($data, $barcode)) . "\n";
	}
	
	
}
else
{
	echo "No Data Found For Barcode";
}


function convertProductDataForReturn($data, $barcode)
{
	$pos_product_sub_id = $data[0]['pos_product_sub_id'];

	$return_data['barcode'] = strtoupper($barcode);
	$return_data['price_level'] = $data[0]['price_level'];	
	/*$return_data['style_number'] = $data[0]['style_number'];

	$return_data['pos_product_id'] = $data[0]['pos_product_id'];*/
	$return_data['pos_product_sub_id'] = $data[0]['pos_product_sub_id'];
	$return_data['quantity'] = 1;
	$return_data['retail_price'] = $data[0]['retail_price'];
	$return_data['sale_price'] = $data[0]['sale_price'];
	$return_data['cost'] = $data[0]['cost'];
	$return_data['value'] = $data[0]['cost'];
	$return_data['inventory_type'] = 'Available';
	/*$return_data['sale_price'] = $data[0]['sale_price'];
	$return_data['title'] = $data[0]['title'];
	$return_data['size'] = getProductOptionCode($pos_product_sub_id, getProductAttributeId('Size'));
	$return_data['color_code'] = getProductOptionCode($pos_product_sub_id, getProductAttributeId('Color'));
	$return_data['cup'] = getProductOptionCode($pos_product_sub_id, getProductAttributeId('Cup'));	
	$return_data['color_description'] = getProductOptionName($pos_product_sub_id, getProductAttributeId('Color'));
	$return_data['pos_manufacturer_brand_id'] = getBrandFromProductID($data[0]['pos_product_id']);
	$return_data['pos_category_id'] =  getProductCategory($data[0]['pos_product_id']);
	$return_data['brand_name'] = getBrandName(getBrandFromProductId($data[0]['pos_product_id']));*/
	
	$return_data['item'] = getProductSubidBrandTitleStyleOptions($pos_product_sub_id);

	$return_data['comments'] = '';
	//now for the tax
	//is it taxable?
	
	return $return_data;

	
	
}



?>