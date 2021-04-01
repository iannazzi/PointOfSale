<?php
/*
	*purchase_order_contents.php
	*Craig Iannazzi 2-2-2012
	*This file will set up for display of the purchase order
*/
$page_title = 'Purchase Return';
require_once ('../po_functions.php');
$pos_purchase_return_id =  getPostOrGetID('pos_purchase_return_id');


include (HEADER_FILE);
//echo $html;
include (FOOTER_FILE);
function createReturnFROMPOTableArrayDef($pos_purchase_order_id)
{
	$array_table_def= array(	
					array(	'th' => 'POC ID',
			 				'type' => 'hidden_input',
							'mysql_result_field' => 'pos_purchase_order_content_id',
							'mysql_post_field' => 'pos_purchase_order_content_id'),
					array(	'th' => 'Product SubId',
							'mysql_result_field' => 'product_subid_name',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(
							'th' => 'Style Number',
							'mysql_result_field' => 'style_number',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(
							'th' => 'Color Code',
							'mysql_result_field' => 'color_code',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(
							'th' => 'Color Description',
							'mysql_result_field' => 'color_description',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(	'th' => 'Size',
							'mysql_result_field' => 'size',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(	'th' => 'Quantity<br>Returning',
							'mysql_result_field' => '',
							'type' => 'input',
							'tags' => ' class="highlight" ',
							'value' => 0,
							'mysql_post_field' => 'quantity_returning'),
					array(	'th' => 'Comments',
							'mysql_result_field' => 'comments',
							'type' => 'input',
							'mysql_post_field' => 'comments')
					);
	return $array_table_def;
}
function createReturnTableArrayDef($pos_purchase_order_id)
{
	$style_numbers = removeKey(getStyleNumbersFromBrandId($pos_manufacturer_brand_id));
	$array_table_def= array(	
					array(	'th' => 'Row',
			 				'type' => 'hidden_input',
							'mysql_result_field' => '',
							'mysql_post_field' => ''),
					array(	'th' => 'Style Number',
							'html' => createGenericSelect('style_number', $style_numbers, $style_numbers, 'false')
							'mysql_result_field' => 'product_subid_name',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(
							'th' => 'Style Number',
							'mysql_result_field' => 'style_number',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(
							'th' => 'Color Code',
							'mysql_result_field' => 'color_code',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(
							'th' => 'Color Description',
							'mysql_result_field' => 'color_description',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(	'th' => 'Size',
							'mysql_result_field' => 'size',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(	'th' => 'Quantity<br>Returning',
							'mysql_result_field' => '',
							'type' => 'input',
							'tags' => ' class="highlight" ',
							'value' => 0,
							'mysql_post_field' => 'quantity_returning'),
					array(	'th' => 'Comments',
							'mysql_result_field' => 'comments',
							'type' => 'input',
							'mysql_post_field' => 'comments')
					);
	return $array_table_def;
}
?>
