<?
$binder_name = 'Images';
$access_type = 'WRITE';
require_once('../product_functions.php');
$barcode = scrubInput(getPostOrGetValue('barcode'));

//get the product id, colors, sizes

$return_data = getProductInfoFromSubID($barcode);


if($return_data)
{

	
	echo json_encode($return_data) . "\n";
}
else
{
	echo "No Data Found For Barcode";
}

function getProductInfoFromSubId($barcode)
{
	$sql = "SELECT pos_product_sub_id, pos_products_sub_id.pos_product_id, product_subid_name, attributes_list,
		 retail_price, sale_price, title, style_number
		FROM pos_products_sub_id
		LEFT JOIN pos_products ON pos_products_sub_id.pos_product_id = pos_products.pos_product_id
		WHERE pos_product_sub_id = '$barcode'";
$data = getSQL($sql);

	if(sizeof($data)>0)
	{
		$pos_product_sub_id = $data[0]['pos_product_sub_id'];
		$pos_product_id = $data[0]['pos_product_id'];
		$barcode_data['content_type'] = 'PRODUCT';
		$barcode_data['style_number'] = $data[0]['style_number'];
		$barcode_data['barcode'] = $data[0]['pos_product_sub_id'];
		$barcode_data['pos_product_id'] = $data[0]['pos_product_id'];
		$barcode_data['pos_product_sub_id'] = $data[0]['pos_product_sub_id'];
		$barcode_data['quantity'] = 1;
		$barcode_data['retail_price'] = $data[0]['retail_price'];
		$barcode_data['sale_price'] = ($data[0]['sale_price'] == 0) ? $data[0]['retail_price'] : $data[0]['sale_price'];
		$barcode_data['title'] = $data[0]['title'];
		$barcode_data['brand_name'] = getBrandName(getBrandFromProductId($data[0]['pos_product_id']));
		$barcode_data['size'] = getProductOptionCode($pos_product_sub_id, getProductAttributeId('Size'));
		$barcode_data['color_code'] = getProductOptionCode($pos_product_sub_id, getProductAttributeId('Color'));
		$barcode_data['cup'] = getProductOptionCode($pos_product_sub_id, getProductAttributeId('Cup'));	
		$barcode_data['color_name'] = getProductOptionName($pos_product_sub_id, getProductAttributeId('Color'));
		$barcode_data['big_title'] = $barcode_data['brand_name'] . ' ' . $data[0]['title'] . ' in ' . $barcode_data['color_name'];
		$barcode_data['description'] = getProductDescription($pos_product_id);
		$pos_sales_tax_category_id = getProductSalesTaxCategoryId($data[0]['pos_product_id']);
		$barcode_data['pos_sales_tax_category_id'] = $pos_sales_tax_category_id;
		
		
	
		return $barcode_data;
	}
	else
	{
		return false;
	}
}

?>