<?php
/*
	list_accounts.php
	craig Iannazzi 4-23-12
*/
$binder_name = 'Products';
$access_type = 'READ';
$page_title = 'Product Sub Ids';
require_once ('../product_functions.php');

//define the search table
$search_fields = array(				array(	'db_field' => 'pos_product_sub_id',
											'mysql_search_result' => 'pos_product_sub_id',
											'caption' => 'System Id',	
											'type' => 'input',
											'html' => createSearchInput('pos_product_sub_id')
										),
										array(	'db_field' => 'product_subid_name',
											'mysql_search_result' => 'product_subid_name',
											'caption' => 'Sub id Name <br>(barcode)',	
											'type' => 'input',
											'html' => createSearchInput('product_subid_name')),
											array(	'db_field' => 'brand_name',
											'mysql_search_result' => 'brand_name',
											'caption' => 'Brand Name',	
											'type' => 'input',
											'html' => createSearchInput('brand_name')),
											array(	'db_field' => 'style_number',
											'mysql_search_result' => 'style_number',
											'caption' => 'Style Number',	
											'type' => 'input',
											'html' => createSearchInput('style_number')),
											array(	'db_field' => 'color_code',
											'mysql_search_result' => 'color_code',
											'caption' => 'Color Code',	
											'type' => 'input',
											'html' => createSearchInput('color_code')),
											array(	'db_field' => 'color_description',
											'mysql_search_result' => 'color_description',
											'caption' => 'Color Description',	
											'type' => 'input',
											'html' => createSearchInput('color_description')),
											array(	'db_field' => 'size',
											'mysql_search_result' => 'size',
											'caption' => 'Size',	
											'type' => 'input',
											'html' => createSearchInput('size'))
										);
$table_columns = array(

		array(
			'th' => 'View',
			'mysql_field' => 'pos_product_sub_id',
			'get_url_link' => "view_pos_product_sub_id.php",
			'url_caption' => 'View',
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
			'th' => 'Brand Name',
			'mysql_field' => 'brand_name',
			'sort' => 'brand_name'),
		array(
			'th' => 'Title',
			'mysql_field' => 'title',
			'sort' => 'title'),
		array(
			'th' => 'Style Number',
			'mysql_field' => 'style_number',
			'sort' => 'style_number'),
		
		array(
			'th' => 'Color Description',
			'mysql_field' => 'color_description',
			'sort' => 'color_description'),
	
			/*array(
			'th' => 'Attribute Name',
			'mysql_field' => 'attribute_name',
			'sort' => 'attribute_name'),*/

			array(
			'th' => 'Color Code',
			'mysql_field' => 'color_code',
			'sort' => 'color_code'),


			array(
			'th' => 'Size',
			'mysql_field' => 'size',
			'sort' => 'size'),
			array(
			'th' => 'Cup',
			'mysql_field' => 'cup',
			'sort' => 'cup'),
array(
			'th' => 'pos',
			'mysql_field' => 'pos',
			'sort' => 'pos'),

			/*array(
			'th' => 'size_difference',
			'mysql_field' => 'size_difference',
			'sort' => 'size_difference'),*/
			
			
			);

//substr is string, start, length



//here is the query that the search and table arrays are built off of.
//this is a super dangerous search as the size is not delimited at the end....
/*
(SELECT option_name FROM pos_product_sub_id_options LEFT JOIN pos_product_options ON pos_product_options.pos_product_option_id = pos_product_sub_id_options.pos_product_option_id WHERE pos_product_sub_id_options.pos_product_sub_id = pos_products_sub_id.pos_product_sub_id) as option_name,

(SELECT option_name FROM pos_product_sub_id_options LEFT JOIN pos_product_options ON pos_product_options.pos_product_option_id = pos_product_sub_id_options.pos_product_option_id WHERE pos_product_sub_id_options.pos_product_sub_id = pos_products_sub_id.pos_product_sub_id) as option_code,

(SELECT attribute_name FROM pos_product_sub_id_options LEFT JOIN pos_product_options ON pos_product_options.pos_product_option_id = pos_product_sub_id_options.pos_product_option_id LEFT JOIN pos_product_attributes ON pos_product_options.pos_product_attribute_id = pos_product_attributes.pos_product_attribute_id WHERE pos_product_sub_id_options.pos_product_sub_id = pos_products_sub_id.pos_product_sub_id) as attribute_name,
*/
$tmp_sql = "
CREATE TEMPORARY TABLE tmp

SELECT  pos_product_sub_id, product_subid_name, pos_product_id, style_number, brand_name,  brand_code, attributes_list, title,

(SELECT option_code FROM pos_product_options LEFT JOIN pos_product_sub_id_options ON pos_product_options.pos_product_option_id = pos_product_sub_id_options.pos_product_option_id LEFT JOIN pos_product_attributes ON pos_product_attributes.pos_product_attribute_id = pos_product_options.pos_product_attribute_id WHERE attribute_name = 'Color' AND pos_product_sub_id_options.pos_product_sub_id = pos_products_sub_id.pos_product_sub_id) as color_code2,

(SELECT option_name FROM pos_product_options LEFT JOIN pos_product_sub_id_options ON pos_product_options.pos_product_option_id = pos_product_sub_id_options.pos_product_option_id LEFT JOIN pos_product_attributes ON pos_product_attributes.pos_product_attribute_id = pos_product_options.pos_product_attribute_id WHERE attribute_name = 'Color' AND pos_product_sub_id_options.pos_product_sub_id = pos_products_sub_id.pos_product_sub_id) as color_description2,

(SELECT option_code FROM pos_product_options LEFT JOIN pos_product_sub_id_options ON pos_product_options.pos_product_option_id = pos_product_sub_id_options.pos_product_option_id LEFT JOIN pos_product_attributes ON pos_product_attributes.pos_product_attribute_id = pos_product_options.pos_product_attribute_id WHERE attribute_name = 'Size' AND pos_product_sub_id_options.pos_product_sub_id = pos_products_sub_id.pos_product_sub_id) as size2,

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


;


";
$tmp_select_sql = "SELECT *, concat(brand_code,'::',style_number,'::', color_code2, '::', size2) as bcode2, if(STRCMP(size, size2), 'DIFFERENT', '') as size_compare,  if(STRCMP(color_code, color_code2), 'DIFFERENT', '') as color_code_compare, if(STRCMP(color_description, color_description2), 'DIFFERENT', '') as color_description_compare,if(STRCMP(product_subid_name,concat(brand_code,'::',style_number,'::', color_code2, '::', size2)), 'DIFFERENT', '') as barcode_compare  FROM tmp WHERE 1" ;

$tmp_sql = "
CREATE TEMPORARY TABLE tmp

SELECT  pos_product_sub_id, product_subid_name, pos_product_id, pos_products.style_number, brand_name,  brand_code, attributes_list, pos_products.title,

(SELECT option_code FROM pos_product_options LEFT JOIN pos_product_sub_id_options ON pos_product_options.pos_product_option_id = pos_product_sub_id_options.pos_product_option_id LEFT JOIN pos_product_attributes ON pos_product_attributes.pos_product_attribute_id = pos_product_options.pos_product_attribute_id WHERE attribute_name = 'Color' AND pos_product_sub_id_options.pos_product_sub_id = pos_products_sub_id.pos_product_sub_id) as color_code,

(SELECT option_name FROM pos_product_options LEFT JOIN pos_product_sub_id_options ON pos_product_options.pos_product_option_id = pos_product_sub_id_options.pos_product_option_id LEFT JOIN pos_product_attributes ON pos_product_attributes.pos_product_attribute_id = pos_product_options.pos_product_attribute_id WHERE attribute_name = 'Color' AND pos_product_sub_id_options.pos_product_sub_id = pos_products_sub_id.pos_product_sub_id) as color_description,

(SELECT option_code FROM pos_product_options LEFT JOIN pos_product_sub_id_options ON pos_product_options.pos_product_option_id = pos_product_sub_id_options.pos_product_option_id LEFT JOIN pos_product_attributes ON pos_product_attributes.pos_product_attribute_id = pos_product_options.pos_product_attribute_id WHERE attribute_name = 'Size' AND pos_product_sub_id_options.pos_product_sub_id = pos_products_sub_id.pos_product_sub_id) as size,

(SELECT option_code FROM pos_product_options LEFT JOIN pos_product_sub_id_options ON pos_product_options.pos_product_option_id = pos_product_sub_id_options.pos_product_option_id LEFT JOIN pos_product_attributes ON pos_product_attributes.pos_product_attribute_id = pos_product_options.pos_product_attribute_id WHERE attribute_name = 'Cup' AND pos_product_sub_id_options.pos_product_sub_id = pos_products_sub_id.pos_product_sub_id) as cup,


(SELECT GROUP_CONCAT(pos_purchase_order_id  SEPARATOR ', ' ) 
				FROM pos_purchase_orders LEFT JOIN pos_purchase_order_contents USING (pos_purchase_order_id) 
 WHERE  pos_purchase_order_contents.pos_product_sub_id = pos_products_sub_id.pos_product_sub_id ) as pos

FROM pos_products_sub_id
LEFT JOIN pos_products USING (pos_product_id)
LEFT join pos_manufacturer_brands USING (pos_manufacturer_brand_id)
LEFT JOIN pos_purchase_order_contents USING (pos_product_sub_id)
LEFT JOIN pos_purchase_orders USING (pos_purchase_order_id)
WHERE purchase_order_status = 'OPEN' OR purchase_order_status = 'PREPARED' OR purchase_order_status = 'CLOSED'


;


";
$tmp_select_sql = "SELECT * FROM tmp WHERE 1" ;




//create the search form
//search form will not work for this example => because I can't split out the size from the attributes
//so we will need to take the data and the search parameters and limit it here?
//shit....

$action = 'list_product_sub_ids2.php';
//Create Search String (AND's after MYSQL WHERE from $_GET data)
$search_sql = createSearchSQLStringMultipleDates($search_fields);
$tmp_select_sql  .= $search_sql;

//Create the order sting to append to the sql statement
$order_by = createSortSQLString($table_columns, $table_columns[5]['mysql_field'], 'DESC');
$tmp_select_sql  .=  " ORDER BY $order_by";



$html = printGetMessage('message');
$html .= createSearchForm($search_fields,$action);


if (isset($_GET['search']))
{
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,$tmp_sql);
	$data = getTransactionSQL($dbc,$tmp_select_sql);
	closeDB($dbc);
	//now we need to process the data to add in some stuff
	/* this will kill search functionality
	for($row=0;$row<sizeof($data);$row++)
	{
		$pos_product_sub_id = $data[$row]['pos_product_sub_id'];
		$data[$row]['size'] = getProductSubIdSize($pos_product_sub_id);
		$data[$row]['color_code'] = getProductSubIdColorCode($pos_product_sub_id);
		$data[$row]['color_description'] = getProductSubIdColorDescription($pos_product_sub_id);
	}*/
	//now the form... this is something I think I need to break out?
	$form_handler = '../PrintLabels/print_subid_search_labels.php';
	$html .= '<form action="' . $form_handler.'" method="post">';
	//$html .= createSelectAll();
	$html .= createRecordsTable($data, $table_columns);
	$html .= createHiddenInput('tmp_sql', urlencode($tmp_sql));
	$html .= createHiddenInput('tmp_select_sql', urlencode($tmp_select_sql));
	$html .= createHiddenInput('search_sql', urlencode($search_sql));
	$html .= '<input class = "button" style="width:150px" type="submit" name="print_labels" value="Print Labels"/>';
		$html .= '</form>';
}
$html .= '<script>document.getElementsByName("pos_product_sub_id")[0].focus();</script>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
function createSelectAll()
{
	$html = '<p><table><tbody><tr><td><input  onclick="selectDeselectAll()" checked="checked" type = "checkbox" id="selectAll" name="selectAll"  /></td><td>Select/Desect All</td></tr></tbody></table></p>';
	$html .= '<script>var select = true;
					function selectDeselectAll()
					{
						if (select == true)
						{
							document.getElementById(\'checkbox\').checked=true;
							select = false;
						}
						else
						{
							document.getElementById(\'checkbox\').checked=false;
							select = true;
						}
					}
				</script>';
					
	return $html;
}
?>
