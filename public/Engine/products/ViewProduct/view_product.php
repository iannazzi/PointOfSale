<?php 

/*
	*view_manufacturer.php
	Creates a html table with an edit button
	Craig Iannazzi 1-23-12
	
*/
$binder_name = 'Products';
$access_type = 'READ';
$page_title = 'View a Product';
require_once('../product_functions.php');

 $pos_product_id = getPostOrGetID('pos_product_id');
if (checkForValidIDinPOS($pos_product_id, 'pos_products', 'pos_product_id'))
{
	$html = printGetMessage();
	
	$html .= createProductOverviewTable($pos_product_id);

	$html .= '<div class = "mysql_table_divider">';
	$html .= '<p>Promotions, Discounts</p>';
	$html .= '</div>';
	
	$html .= '<div class = "mysql_table_divider">';
	$html .= '<p>SKU\'s + child/parent/master packs</p>';
	$html .= '</div>';


	$html .= '<h3>Product Options</h3>';
	//turn this into a records table
	$html .= '<div class = "mysql_table_divider">';
	$html .= '<p>Colors, Images, Web Properties, Recommended Products</p>'; //priority, overview, meta keywords, meta title, meta description, url 
	$html .= createProductColorHTMLTable( $pos_product_id);
	$html .= '<p>'.createOpenWinButton('Add A Color' , '../AddProductColor/add_product_color.php?pos_product_id='.$pos_product_id, $width = '300') .'</p>';
	$html .= '</div>';
	

	//sizes => if there are any then display them
	
	
	//turn this into a records table
	$html .= '<div class = "mysql_table_divider">';
	$html .= '<p>Sizes</p>';
	//$html .= createProductSizesHTMLTable($pos_product_id);
	$html .= createStaticViewDynamicTable(createSizeTable(),getProductSizes($pos_product_id), ' style = "width:100%;" ');
	$html .= '<p>'.createOpenWinButton('Edit Sizes' , '../EditSizes/edit_sizes.php?pos_product_id='.$pos_product_id, $width = '300') ;
	//$html .= createOpenWinButton('Sort Sizes' , '../EditSizes/sort_sizes.php?pos_product_id='.$pos_product_id, $width = '300') .'</p>';
	$html .= '</div>';
	

	
	
	//now how many more attributes are there?
	$html .= '<p>'.createOpenWinButton('Add Option' , '../Options/add_product_option.php?pos_product_id='.$pos_product_id, $width = '300') ;
	
	//turn this into a records table
	$html .= '<div class = "mysql_table_divider">';
	$html .= '<p>Sub IDs</p>';
	
	$html .= createProductSubIDTable2($pos_product_id);
	
	
	$html .= '</div>';
	
	
	
	
	
	include (HEADER_FILE);
	echo $html;
	include (FOOTER_FILE);
}
else
{
	include(HEADER_FILE);
	echo '<p class="error">Error - id does not exist</p>';
	include(FOOTER_FILE);
}

function createProductOverviewTable($pos_product_id)
{
	$edit_product_location = '../EditProduct/edit_product.php?pos_product_id='.$pos_product_id;
	$db_table = 'pos_products';
	$key_val_id['pos_product_id'] = $pos_product_id;
	$table_def = createProductTableDef($db_table, $key_val_id);
	$table_def_w_data = loadDataToTableDef($table_def, $db_table, $key_val_id);
	$html = '<div class = "mysql_table_divider_no_line">';
	$html .= '<p>Product Details</p>';
	$html .= convertTableDefToHTMLForView($table_def_w_data);
	$html .= disableSelect(createSecondaryProductCategoryTable($pos_product_id));
	$html .= '<p>'.createOpenWinButton('Edit Product Details' ,  $edit_product_location, $width = '300') .'</p>';
	$html .= '</div>';
	return $html;
}

function createProductSubIDTable2($pos_product_id)
{


	/*
		printProductLabelsForm($data, $filename);	
		include (HEADER_FILE);
		echo $html;
		include (FOOTER_FILE);
	*/
		
	$table_columns = array(

		array(
			'th' => 'View',
			'mysql_field' => 'pos_product_sub_id',
			'get_url_link' => "../ProductSubId/view_product_sub_id.php?pos_product_id=".$pos_product_id,
			'url_caption' => 'View',
			'get_id_link' => 'pos_product_sub_id'),
		array(
			'th' => 'Inventory',
			'mysql_field' => 'pos_product_sub_id',
			'get_url_link' => POS_ENGINE_URL . "/inventory/MerchandiseInventory/inventory_by_product.php",
			'url_caption' => 'Inventory',
			'get_id_link' => 'pos_product_sub_id'),
		array(
			'th' => 'ID',
			'mysql_field' => 'pos_product_sub_id',
			'sort' => 'pos_product_sub_id'),
		array(
			'th' => 'Sub id Name<br>(barcode)',
			'mysql_field' => 'product_subid_name',
			'sort' => 'product_subid_name'),
		array(
			'th' => 'Brand Code',
			'mysql_field' => 'brand_code',
			'sort' => 'brand_code'),
		array(
			'th' => 'Style Number',
			'mysql_field' => 'style_number',
			'sort' => 'style_number'),
		array(
			'th' => 'Color Code',
			'mysql_field' => 'color_code',
			'sort' => 'color_code'),
		array(
			'th' => 'Color Description',
			'mysql_field' => 'color_description',
			'sort' => 'color_description'),
		array(
			'th' => 'Size',
			'mysql_field' => 'size',
			'sort' => 'size'),
		array(
			'th' => 'Group Options',
			'mysql_field' => 'group_options',
			'sort' => 'group_options')
			
			);



	$tmp_sql = "
CREATE TEMPORARY TABLE tmp

SELECT  pos_product_sub_id, product_subid_name, pos_product_id, style_number, brand_name,  attributes_list, 


LOCATE('color_code::', attributes_list) + LENGTH('color_code::') as start,
LOCATE('\r\n', attributes_list, LOCATE('color_code::', attributes_list)) as end,
LOCATE('\r\n', attributes_list, LOCATE('color_code::', attributes_list)) - ((LOCATE('color_code::', attributes_list) + LENGTH('color_code::')) )as length,

SUBSTRING(attributes_list, 
LOCATE('color_code::', attributes_list) + LENGTH('color_code::'), 
LOCATE('\r\n', attributes_list, LOCATE('color_code::', attributes_list)) - (LOCATE('color_code::', attributes_list) + LENGTH('color_code::'))
) as color_code,

SUBSTRING(attributes_list, 
LOCATE('color_description::', attributes_list) + LENGTH('color_description::'), 
LOCATE('\r\n', attributes_list, LOCATE('color_description::', attributes_list)) - (LOCATE('color_description::', attributes_list) + LENGTH('color_description::'))
) as color_description,

SUBSTRING(attributes_list, LOCATE('size::', attributes_list) + LENGTH('size::'), 1+ LENGTH(attributes_list) - (LOCATE('size::', attributes_list) + LENGTH('size::'))) as size

FROM pos_products_sub_id
LEFT JOIN pos_products USING (pos_product_id)
LEFT join pos_manufacturer_brands USING (pos_manufacturer_brand_id)
WHERE pos_product_id = $pos_product_id


;


";

	$tmp_sql = "
CREATE TEMPORARY TABLE tmp
SELECT DISTINCT pos_products_sub_id.pos_product_sub_id, product_subid_name, pos_products.pos_product_id, style_number, brand_code,  

(SELECT option_code FROM pos_product_options LEFT JOIN pos_product_attributes USING (pos_product_attribute_id) 
LEFT JOIN pos_product_sub_id_options ON pos_product_sub_id_options.pos_product_option_id = pos_product_options.pos_product_option_id WHERE attribute_name = 'Color' and pos_product_sub_id_options.pos_product_sub_id = pos_products_sub_id.pos_product_sub_id) as color_code,
(SELECT option_name FROM pos_product_options LEFT JOIN pos_product_attributes USING (pos_product_attribute_id) 
LEFT JOIN pos_product_sub_id_options ON pos_product_sub_id_options.pos_product_option_id = pos_product_options.pos_product_option_id WHERE attribute_name = 'Color' and pos_product_sub_id_options.pos_product_sub_id = pos_products_sub_id.pos_product_sub_id) as color_description,
(SELECT option_code FROM pos_product_options LEFT JOIN pos_product_attributes USING (pos_product_attribute_id) 
LEFT JOIN pos_product_sub_id_options ON pos_product_sub_id_options.pos_product_option_id = pos_product_options.pos_product_option_id WHERE attribute_name = 'Size' and pos_product_sub_id_options.pos_product_sub_id = pos_products_sub_id.pos_product_sub_id) as size,

(SELECT GROUP_CONCAT(CONCAT(attribute_name,':',pos_product_options.option_code) ORDER BY pos_product_attributes.priority DESC SEPARATOR ', ' ) 
				FROM pos_product_options LEFT JOIN pos_product_attributes USING (pos_product_attribute_id) 
LEFT JOIN pos_product_sub_id_options ON pos_product_sub_id_options.pos_product_option_id = pos_product_options.pos_product_option_id WHERE  pos_product_sub_id_options.pos_product_sub_id = pos_products_sub_id.pos_product_sub_id ) as group_options

FROM pos_products_sub_id

INNER JOIN pos_products USING (pos_product_id)
INNER JOIN pos_product_sub_id_options ON pos_products_sub_id.pos_product_sub_id = pos_product_sub_id_options.pos_product_sub_id
INNER JOIN pos_product_options  ON pos_product_sub_id_options.pos_product_option_id = pos_product_options.pos_product_option_id
INNER JOIN pos_product_attributes ON pos_product_options.pos_product_attribute_id = pos_product_attributes.pos_product_attribute_id

INNER join pos_manufacturer_brands USING (pos_manufacturer_brand_id)
WHERE pos_products.pos_product_id = $pos_product_id
;";





	$tmp_select_sql = "SELECT * FROM tmp WHERE 1 ORDER BY color_code ASC";

	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,$tmp_sql);
	$data = getTransactionSQL($dbc,$tmp_select_sql);
	closeDB($dbc);

	//now the form... this is something I think I need to break out?
	$form_handler = POS_ENGINE_URL .'/products/PrintLabels/print_labels.form.handler.php';
	//$html = '<form action="' . $form_handler.'" method="post">';
	//$html .= '<input class = "button" type="button" style="width:300px" name="add_location" value="Add A Product Sub Id (DANGEROUS)" onclick="open_win(\'../ProductSubId/add_edit_product_sub_id.php?pos_product_id='.$pos_product_id . '&type=Add\')"/>';
	$html = createRecordsTable($data, $table_columns);
	//$html .= createHiddenInput('tmp_sql', urlencode($tmp_sql));
	//$html .= createHiddenInput('tmp_select_sql', urlencode($tmp_select_sql));
	//$html .= '<input class = "button" style="width:150px" type="submit" name="print_labels" value="Print Labels"/>';
	//$html .= '</form>';
	$html .= '<p>'.createOpenWinButton('Print labels' , POS_ENGINE_URL .'/products/PrintLabels/print_product_labels.php?pos_product_id='.$pos_product_id, $width = '300') .'</p>';


	return $html;


}
?>


