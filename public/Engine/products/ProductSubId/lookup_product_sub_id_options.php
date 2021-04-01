<?
$binder_name = 'Products';
$access_type = 'WRITE';
require_once('../product_functions.php');

$pos_product_sub_id = getPostOrGetValue('pos_product_sub_id');
$pos_product_attribute_id = getPostOrGetValue('pos_product_attribute_id');
$pos_product_id = getProductIDFromProductSubId($pos_product_sub_id);
$options = getProductOptions($pos_product_id, $pos_product_attribute_id);

//when updating the individual select items just pass in name and value...
//the first column tells us the column to update
//'individual_select_options' => 'options',\
$return_array['pos_product_option_id']='Does not matter, but I need this db_filed';
$return_array['options']['names'] = $options['option_code_name'];
$return_array['options']['values'] = $options['pos_product_option_id'];

echo json_encode($return_array) . "\n";


?>