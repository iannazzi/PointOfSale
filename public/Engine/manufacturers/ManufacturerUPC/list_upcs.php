<?php
/*
	*View_manufacturers.php
	*shows a list of all registered manufacturers
*/
$binder_name = 'Manufacturer UPC\'s';
$access_type = 'READ';
$page_level = 5;
$page_navigation = 'manufacturers';
require_once ('../manufacturer_functions.php');




$page_title = 'View UPCs';

include (HEADER_FILE);

$search_fields = array(		array(	'db_field' => 'upc_code',
											'mysql_search_result' => 'pos_manufacturer_upc.upc_code',
											'caption' => 'UPC',	
											'type' => 'input',
											'html' => createSearchInput('upc_code')
										),
								array(	'db_field' => 'pos_manufacturer_id',
											'mysql_search_result' => 'pos_manufacturers.pos_manufacturer_id',
											'caption' => 'Manufacturer',
											'type' => 'select',
											'html' => createManufacturerSelect('pos_manufacturer_id', valueFromGetOrDefault('pos_manufacturer_id'), 'off')
										),
								array(	'db_field' => 'style_number',
											'mysql_search_result' => 'pos_manufacturer_upc.style_number',
											'caption' => 'Style Number',
											'type' => 'input',
											'html' => createSearchInput('style_number')
										),
								array(	'db_field' => 'style_description',
											'mysql_search_result' => 'pos_manufacturer_upc.style_description',
											'caption' => 'Description',
											'type' => 'input',
											'html' => createSearchInput('style_description')
										),
								array(	'db_field' => 'color_code',
											'mysql_search_result' => 'pos_manufacturer_upc.color_code',
											'caption' => 'Color <br>Code',
											'type' => 'input',
											'html' => createSearchInput('color_code')
										),
								array(	'db_field' => 'color_description',
											'mysql_search_result' => 'pos_manufacturer_upc.color_description',
											'caption' => 'Color<br>Description',
											'type' => 'input',
											'html' => createSearchInput('color_description')
										),
								array(	'db_field' => 'size',
											'mysql_search_result' => ' pos_manufacturer_upc.size',
											'caption' => 'Size',
											'type' => 'input',
											'html' => createSearchInput('size')
										)
								);
$list_purchase_order_table_columns = array(
		 array(
			'th' => 'Sys ID',
			'mysql_field' => 'pos_manufacturer_upc_id',
			'mysql_search_result' => 'pos_manufacturer_upc_id',
			'sort' => 'pos_manufacturer_upc.pos_manufacturer_upc_id',
),
		array(
			'th' => 'UPC',
			'mysql_field' => 'upc_code',
			'mysql_search_result' => 'upc_code',
			'sort' => 'pos_manufacturer_upc.upc_code'),
		array(
			'th' => 'Company',
			'mysql_field' => 'company',
			'mysql_search_result' => 'company',
			'sort' => 'pos_manufacturers.company'),
		array(
			'th' => 'Description',
			'mysql_field' => 'style_description',
			'mysql_search_result' => 'style_description',
			'sort' => 'pos_manufacturer_upc.style_description'),
		array(
			'th' => 'Style<br>Number',
			'mysql_field' => 'style_number',
			'mysql_search_result' => 'style_number',
			'sort' => 'pos_manufacturer_upc.style_number'),
		array(
			'th' => 'Color<br>Code',
			'mysql_field' => 'color_code',
			'mysql_search_result' => 'color_code',
			'sort' => 'pos_manufacturer_upc.color_code'),
		array(
			'th' => 'Color<br>Description',
			'mysql_field' => 'color_description',
			'mysql_search_result' => 'color_description',
			'sort' => 'pos_manufacturer_upc.color_description'),
		array(
			'th' => 'Size',
			'mysql_field' => 'size',
			'mysql_search_result' => 'pos-size',
			'sort' => 'pos_manufacturer_upc.size'),
		array(
			'th' => 'MSRP',
			'mysql_field' => 'msrp',
			'mysql_search_result' => 'msrp',
			'sort' => 'pos_manufacturer_upc.msrp'),			
		array(
			'th' => 'Cost',
			'mysql_field' => 'cost',
			'mysql_search_result' => 'cost',
			'sort' => 'pos_manufacturer_upc.cost')
		);

$action = 'list_upcs.php';
$html = createSearchForm($search_fields,$action);
$purchase_order_list_sql = "

SELECT  pos_manufacturers.company, pos_manufacturers.pos_manufacturer_id, pos_manufacturer_upc.pos_manufacturer_upc_id, pos_manufacturer_upc.upc_code, pos_manufacturer_upc.style_description, pos_manufacturer_upc.style_number, pos_manufacturer_upc.color_code, pos_manufacturer_upc.color_description, pos_manufacturer_upc.size, pos_manufacturer_upc.msrp, pos_manufacturer_upc.cost 
FROM  pos_manufacturer_upc
LEFT JOIN pos_manufacturers
ON pos_manufacturer_upc.pos_manufacturer_id = pos_manufacturers.pos_manufacturer_id
WHERE pos_manufacturers.active = 1

	";

//Create Search String (AND's after MYSQL WHERE from $_GET data)
$search_sql = createSearchSQLStringMultipleDates($search_fields);
$purchase_order_list_sql  .=  $search_sql;
//Create the order sting to append to the sql statement
$order_by = createSortSQLString($list_purchase_order_table_columns, $list_purchase_order_table_columns[0]['mysql_field']);
$purchase_order_list_sql  .=  " ORDER BY $order_by";
$purchase_order_list_sql .= " LIMIT 0,1000";
//now make the table
$html .= createRecordsTable(getSQL($purchase_order_list_sql), $list_purchase_order_table_columns);
//include ('list_purchase_orders.inc.php');
$html .= '<p>Records are limited to 1000 results<p>';
echo $html;
include (FOOTER_FILE);
?>
