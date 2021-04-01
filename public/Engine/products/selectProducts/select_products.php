<?php
/*
	*View_manufacturers.php
	*shows a list of all registered manufacturers
*/
$binder_name = 'Products';
$access_type = 'READ';
$page_title = 'Products';
require_once ('../product_functions.php');

$html ='';



$search_fields = array(		array(	'db_field' => 'pos_category_id',
											'mysql_search_result' => 'pos_products.pos_category_id',
											'caption' => 'Primary Category',	
											'type' => 'select',
											'html' => createCategorySelect('pos_category_id', valueFromGetOrDefault('pos_category_id'), 'all')),
							/*array(	'db_field' => 'secondary_category',
											'mysql_search_result' => '(SELECT GROUP_CONCAT(pos_categories.pos_category_id) 
		FROM pos_product_categories
		LEFT JOIN pos_categories
		ON pos_product_categories.pos_category_id = pos_categories.pos_category_id
		WHERE pos_products.pos_product_id = pos_product_categories.pos_product_id AND is_primary = 0 )',
											'caption' => 'Secondary Categories',	
											'type' => 'select',
											'html' => createSecondaryCategorySelect('secondary_category[]', valueFromGetOrDefault('secondary_category	'), 'all')),*/
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
								array(	'db_field' => 'cost',
											'mysql_search_result' => 'ROUND(cost,2)',
											'caption' => 'Cost',
											'type' => 'input',
											'html' => createSearchInput('cost')),
								array(	'db_field' => 'retail_price',
											'mysql_search_result' => 'ROUND(retail_price,2)',
											'caption' => 'Retail Price',
											'type' => 'input',
											'html' => createSearchInput('retail_price')),
								array(	'db_field' => 'sale_price',
											'mysql_search_result' => 'ROUND(sale_price,2)',
											'caption' => 'Sale Price',
											'type' => 'input',
											'html' => createSearchInput('sale_price')),
								array(	'db_field' => 'added',
											'mysql_search_result' => 'added',
											'caption' => 'From Date Added',
											'type' => 'start_date',
											'html' => dateSelect('added_start_date',valueFromGetOrDefault('added_start_date'))
										),
							array(	'db_field' => 'added',
											'mysql_search_result' => 'added',
											'caption' => 'To Date Added',	
											'type' => 'end_date',
											'html' => dateSelect('added_end_date',valueFromGetOrDefault('added_end_date'))
										),
							array(	'db_field' => 'purchase_orders',
											'mysql_search_result' => 'purchase_orders',
											'caption' => 'Purchase Order',	
											'type' => 'input',
											'html' => createSearchInput('purchase_orders')
										)
								
											
						);								
$list_table_columns = array(
		 array(
			'th' => 'View',
			'mysql_field' => 'pos_product_id',
			'get_url_link' => "../ViewProduct/view_product.php",
			'url_caption' => 'View',
			'get_id_link' => 'pos_product_id'),
		 array(
			'th' => 'Edit',
			'mysql_field' => 'pos_product_id',
			'get_url_link' => "../EditProduct/edit_product.php",
			'url_caption' => 'Edit',
			'get_id_link' => 'pos_product_id'),
		array(
			'th' => 'System<br>ID',
			'mysql_field' => 'pos_product_id',
			'mysql_search_result' => 'pos_product_id',
			'sort' => 'pos_product_id'),
		array(
			'th' => 'Primary <BR> Category',
			'mysql_field' => 'name',
			'mysql_search_result' => 'name',
			'sort' => 'name'),
		array(
			'th' => 'Secondary Categories',
			'mysql_field' => 'secondary_categories',
			'mysql_search_result' => 'secondary_categories',
			'sort' => 'secondary_categories'),
		array(
			'th' => 'Brand Name',
			'mysql_field' => 'brand_name',
			'mysql_search_result' => 'brand_name',
			'sort' => 'brand_name'),
		array(
			'th' => 'Style Number',
			'mysql_field' => 'style_number',
			'mysql_search_result' => 'style_number',
			'sort' => 'style_number'),
		array(
			'th' => 'Title',
			'mysql_field' => 'title',
			'mysql_search_result' => 'title',
			'sort' => 'title'),
		array(
			'th' => 'Cost',
			'mysql_field' => 'cost',
			'mysql_search_result' => 'cost',
			'sort' => 'cost'),
		array(
			'th' => 'Retail Price',
			'mysql_field' => 'retail_price',
			'mysql_search_result' => 'retail_price',
			'sort' => 'retail_price'),
		array(
			'th' => 'Sale Price',
			'mysql_field' => 'sale_price',
			'mysql_search_result' => 'sale_price',
			'sort' => 'sale_price'),
		array(
			'th' => 'Date Added',
			'mysql_field' => 'added',
			'mysql_search_result' => 'added',
			'sort' => 'added')
		);

$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saved_products_url');
$tmp_sql = "
CREATE TEMPORARY TABLE products

SELECT 	pos_products.pos_product_id,  
		pos_products.active,
		pos_products.pos_manufacturer_brand_id,
		pos_products.is_taxable,
		pos_products.tax_class_id,
		pos_products.tax_rate,
		pos_products.priority,
		pos_products.style_number,
		pos_products.description,
		pos_products.overview,
		pos_products.weight, 
		pos_products.title, 
		ROUND(pos_products.cost,2) as cost, 
		ROUND(pos_products.retail_price,2) AS retail_price, 
		ROUND(pos_products.sale_price,2) AS sale_price, 
		ROUND(pos_products.employee_price,2) as employee_price, 
		pos_manufacturer_brands.brand_name, 
		pos_categories.name,pos_products.added,
		(SELECT GROUP_CONCAT(pos_categories.name) 
			FROM pos_product_secondary_categories
			LEFT JOIN pos_categories
			ON pos_product_secondary_categories.pos_category_id = pos_categories.pos_category_id
			WHERE pos_products.pos_product_id = pos_product_secondary_categories.pos_product_id) as secondary_categories,
		(SELECT GROUP_CONCAT( DISTINCT CONVERT( pos_purchase_orders.pos_purchase_order_id, char( 10 ) ) ) 
			FROM pos_purchase_orders
			LEFT JOIN pos_purchase_order_contents 
			ON pos_purchase_orders.pos_purchase_order_id = pos_purchase_order_contents.pos_purchase_order_id
			WHERE pos_products.pos_product_id = pos_purchase_order_contents.pos_product_id) as purchase_orders

FROM pos_products
LEFT JOIN pos_manufacturer_brands
ON pos_products.pos_manufacturer_brand_id = pos_manufacturer_brands.pos_manufacturer_brand_id
LEFT JOIN pos_categories
ON pos_products.pos_category_id = pos_categories.pos_category_id


;

";
$tmp_select_sql = "SELECT * FROM products WHERE 1";

//Create Search String (AND's after MYSQL WHERE from $_GET data)
$search_sql = createSearchSQLStringMultipleDates($search_fields);
//Create the order sting to append to the sql statement
$order_by = createSortSQLString($list_table_columns, $list_table_columns[0]['mysql_field']);
$order_sql  =  " ORDER BY $order_by";

$dbc = openPOSdb();
$result = runTransactionSQL($dbc,$tmp_sql);
$data = getTransactionSQL($dbc,$tmp_select_sql.$search_sql.$order_sql);
closeDB($dbc);

//if there is a message print it
$html .= printGetMessage();
$html .= '<p>';


if(checkWriteAccess($binder_name))
{
	$html .= '<p>';
	$html .= '<input class = "button" type="button" style="width:180px;" name="add_product" value="Create A Product" onclick="open_win(\'../CreateProduct/create_product.php\')"/>';
	$html .= '<input class = "button" type="button" style="width:180px;" name="add_product" value="Image Cropper Tool" onclick="open_win(\'../ProductImages/upload_image.php\')"/>';
	
	$html .= '</p>';
}


$html .= createUserButton('Inventory');
$html .= createUserButton('Product Categories');
$html .= '</p>';
$action = 'list_products.php';
$html .= createSearchForm($search_fields,$action);
//now make the table
$html .= createRecordsTable($data, $list_table_columns);
$html .= '<script>document.getElementsByName("brand_name")[0].focus();</script>';
$html .= '<p>';
$html .= '<form  name="frmPosExport" action="../ExportProducts/export_products.php" target="_blank" method="post">';
//$html .= '<input class = "button" type="submit" style="width:180px;" name="pos_export" value="Export Results To .CSV"/>';

if (WEB_STORE_ACTIVE)
{
	//$html .= '<input class = "button" type="button" style="width:200px;" name="export_webstore" value="Export Results for '. WEB_STORE_NAME .'" onclick="open_win(\''.WEB_STORE_MODULE . '?p=product_export&sql='.urlencode($list_sql) .'\')"/>';
	$html .= '<input class = "button" type="submit" style="width:220px;" name="webstore_export" value="Export Results for '. WEB_STORE_NAME .'" />';
 }
$html .= createHiddenInput('tmp_sql', urlencode($tmp_sql));
$html .= createHiddenInput('tmp_select_sql', urlencode($tmp_select_sql));
$html .= createHiddenInput('order_sql', urlencode($order_sql));
$html .= createHiddenInput('search_sql', urlencode($search_sql));
$html .= '</form>';
$html .= '</p>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
?>
