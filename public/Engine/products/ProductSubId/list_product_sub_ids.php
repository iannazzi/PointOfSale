<?php
/*
	list_accounts.php
	craig Iannazzi 4-23-12
*/
$binder_name = 'Product Sub Ids';
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
										array(	'db_field' => 'item',
											'mysql_search_result' => 'item',
											'caption' => 'Item',	
											'type' => 'input',
											'html' => createSearchInput('item')),
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
											array(	'db_field' => 'title',
											'mysql_search_result' => 'title',
											'caption' => 'Title',	
											'type' => 'input',
											'html' => createSearchInput('title')),
									
											array(	'db_field' => 'group_options',
											'mysql_search_result' => 'group_options',
											'caption' => 'Options',	
											'type' => 'input',
											'html' => createSearchInput('group_options')),
											
										);
$table_columns = array(

		array(
			'th' => 'View',
			'mysql_field' => 'pos_product_sub_id',
			'get_url_link' => "view_product_sub_id.php",
			'url_caption' => 'View',
			'get_id_link' => 'pos_product_sub_id'),
			
		array(
			'th' => 'System ID',
			'mysql_field' => 'pos_product_sub_id',
			'sort' => 'pos_product_sub_id'),
		array(
			'th' => 'Product ID',
			'mysql_field' => 'pos_product_id',
			'sort' => 'pos_product_id'),
		array(
			'th' => 'Item',
			'mysql_field' => 'item',
			'sort' => 'item'),
		array(
			'th' => 'Sub id Name<br>(barcode)',
			'mysql_field' => 'product_subid_name',
			'sort' => 'product_subid_name'),
			/*array(
			'th' => 'bcode2',
			'mysql_field' => 'bcode2',
			'sort' => 'bcode2'),*/
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
			'th' => 'Options',
			'mysql_field' => 'group_options',
			'sort' => 'group_options'),
		/*array(
			'th' => 'Color Description',
			'mysql_field' => 'color_description',
			'sort' => 'color_description'),
	
			
			array(
			'th' => 'color_description2',
			'mysql_field' => 'color_description2',
			'sort' => 'color_description2'),
			array(
			'th' => 'Color Code',
			'mysql_field' => 'color_code',
			'sort' => 'color_code'),
			array(
			'th' => 'color_code2',
			'mysql_field' => 'color_code2',
			'sort' => 'color_code2'),
			array(
			'th' => 'Size',
			'mysql_field' => 'size',
			'sort' => 'size'),
			array(
			'th' => 'size2',
			'mysql_field' => 'size2',
			'sort' => 'size2'),*/
			/*array(
			'th' => 'size_difference',
			'mysql_field' => 'size_difference',
			'sort' => 'size_difference'),*/
			
			
			);

//substr is string, start, length

	$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saved_product_subid_search');


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

concat(pos_manufacturer_brands.brand_name,':',pos_products.title,':',pos_products.style_number,':',
		
			(SELECT group_concat(concat(attribute_name,':',option_code,'-',option_name) SEPARATOR ' ') 
			FROM pos_product_sub_id_options 
			LEFT JOIN pos_product_options ON pos_product_sub_id_options.pos_product_option_id = pos_product_options.pos_product_option_id 
			LEFT JOIN pos_product_attributes ON pos_product_options.pos_product_attribute_id = pos_product_attributes.pos_product_attribute_id
			WHERE pos_products_sub_id.pos_product_sub_id = pos_product_sub_id_options.pos_product_sub_id )
		
			) as item,
		
	

(SELECT GROUP_CONCAT(if(pos_product_options.option_code=pos_product_options.option_name, CONCAT(attribute_name,':',pos_product_options.option_code),CONCAT(attribute_name,': CODE ',pos_product_options.option_code, ' DESC ',pos_product_options.option_name)) ORDER BY pos_product_attributes.priority DESC SEPARATOR ', ' ) 
				FROM pos_product_options LEFT JOIN pos_product_attributes USING (pos_product_attribute_id) 
LEFT JOIN pos_product_sub_id_options ON pos_product_sub_id_options.pos_product_option_id = pos_product_options.pos_product_option_id WHERE  pos_product_sub_id_options.pos_product_sub_id = pos_products_sub_id.pos_product_sub_id ) as group_options


FROM pos_products_sub_id
LEFT JOIN pos_products USING (pos_product_id)
LEFT join pos_manufacturer_brands USING (pos_manufacturer_brand_id)


;


";
$tmp_select_sql = "SELECT * FROM tmp WHERE 1" ;

//create the search form
//search form will not work for this example => because I can't split out the size from the attributes
//so we will need to take the data and the search parameters and limit it here?
//shit....

$action = 'list_product_sub_ids.php';
//Create Search String (AND's after MYSQL WHERE from $_GET data)
$search_sql = createSearchSQLStringMultipleDates($search_fields);
$tmp_select_sql  .= $search_sql;

//Create the order sting to append to the sql statement
$order_by = createSortSQLString($table_columns, $table_columns[1]['mysql_field'], 'DESC');
$tmp_select_sql  .=  " ORDER BY $order_by";
$tmp_select_sql .= ' LIMIT 1000';



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
	$html .= '<p> results are limited to 1000 </p>';
	$html .= createHiddenInput('tmp_sql', urlencode($tmp_sql));
	$html .= createHiddenInput('tmp_select_sql', urlencode($tmp_select_sql));
	$html .= createHiddenInput('search_sql', urlencode($search_sql));
	$html .= '<input class = "button" style="width:150px" type="submit" name="print_labels" value="Print Labels"/>';
		$html .= '</form>';
		//$html.='<p>Limited to 100 results</p>';
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
