<?
$binder_name = 'Products';
$access_type = 'WRITE';
require_once('../product_functions.php');

$pos_manufacturer_brand_id = scrubInput(getPostOrGetValue('pos_manufacturer_brand_id'));
$style_number = scrubInput(getPostOrGetValue('style_number'));
$pos_color_option_id = scrubInput(getPostOrGetValue('pos_product_option_id'));

$pos_product_id = getProductID($pos_manufacturer_brand_id, $style_number);
//$pos_color_option_id = getProductOptionId($pos_product_id, getProductAttributeId('Color'), $color_code);

//lookup the size attributes...
		
		$sql6 = "SELECT pos_product_options.pos_product_option_id, option_name 
				FROM pos_product_sub_id_options 
				LEFT JOIN pos_product_options ON pos_product_options.pos_product_option_id = pos_product_sub_id_options.pos_product_option_id
				LEFT JOIN pos_product_attributes ON pos_product_options.pos_product_attribute_id = pos_product_attributes.pos_product_attribute_id
		 WHERE pos_product_sub_id IN (SELECT pos_product_sub_id FROM pos_products_sub_id 
		 LEFT JOIN pos_product_sub_id_options USING(pos_product_sub_id)
		 WHERE pos_products_sub_id.pos_product_id = $pos_product_id
		 AND pos_product_sub_id_options.pos_product_option_id = $pos_color_option_id) 
		 AND
		 pos_product_sub_id_options.pos_product_option_id != $pos_color_option_id
		 AND pos_product_attributes.attribute_name = 'Size'
		 ORDER BY pos_product_options.sort_index ASC
		 ";
		 
		 $sql7 = 

		 
//break up the attributes
$data1 = getfieldRowSQL($sql6);
/*if(sizeof($data1)>0)
{
	$return_data[0]['size'] = explode(newline(),$data1[0]['options']);
}
else
{
	$return_data[0]['size'] = array();
}
$return_data[1] = getFieldRowSQL($sql2);*/
//echo $sql6;
echo json_encode($data1) . "\n";


?>