<?php
/*
	*shows a list of all registered manufacturers
*/
$page_title = 'promotions';
$binder_name = 'promotions';
$access_type = 'READ';
require_once ('../sales_functions.php');

$search_fields = array(				array(	'db_field' => 'pos_promotion_id',
											'mysql_search_result' => 'pos_promotion_id',
											'caption' => 'promotion ID',	
											'type' => 'input',
											'html' => createSearchInput('pos_promotion_id')
										),
										array(	'db_field' => 'promotion_code',
											'mysql_search_result' => 'promotion_code',
											'caption' => 'promotion Code',	
											'type' => 'input',
											'html' => createSearchInput('promotion_code')
										),
										array(	'db_field' => 'promotion_name',
											'mysql_search_result' => 'promotion_name',
											'caption' => 'Promotion Description',	
											'type' => 'input',
											'html' => createSearchInput('promotion_name')
										),
										/*array(	'db_field' => 'promotion_type',
											'mysql_search_result' => 'promotion_type',
											'caption' => 'promotion Type',	
											'type' => 'input',
											'html' => createSearchInput('promotion_type')
										),*/
										array(	'db_field' => 'start_date',
											'mysql_search_result' => 'start_date',
											'caption' => 'promotion Date Start',
											'type' => 'date',
											'html' => dateSelect('start_date',valueFromGetOrDefault('start_date'))
										),
								array(	'db_field' => 'expiration_date',
											'mysql_search_result' => 'expiration_date',
											'caption' => 'Expiration Date',	
											'type' => 'expiration_date',
											'html' => dateSelect('expiration_date',valueFromGetOrDefault('expiration_date'))
											),
								/*array(	'db_field' => 'promotion_amount',
											'mysql_search_result' => 'promotion_amount',
											'caption' => 'promotion Amount',	
											'type' => 'input',
											'html' => createSearchInput('promotion_amount')
											),
							array(	'db_field' => 'qualifying_amount',
											'mysql_search_result' => 'qualifying_amount',
											'caption' => 'Qualifying Amount',	
											'type' => 'input',
											'html' => createSearchInput('qualifying_amount')
											)*/
									
								
										);
$table_columns = array(
		array(
			'th' => 'View',
			'mysql_field' => 'pos_promotion_id',
			'get_url_link' => "promotion.php?type=View",
			'url_caption' => 'View',
			'get_id_link' => 'pos_promotion_id'),
		array(
			'th' => 'ID',
			'mysql_field' => 'pos_promotion_id',
			'sort' => 'pos_promotion_id'),	
		array(
			'th' => 'Promotion Code',
			'mysql_field' => 'promotion_code',
			'sort' => 'promotion_code'),
		array(
			'th' => 'Promotion Description',
			'mysql_field' => 'promotion_name',
			'sort' => 'promotion_name'),
		array(
			'th' => 'Start Date',
			'mysql_field' => 'start_date',
			'date_format' => 'date',
			'sort' => 'start_date'),
		array(
			'th' => 'Expiration Date',
			'mysql_field' => 'expiration_date',
			'date_format' => 'date',
			'sort' => 'expiration_date'),
		array(
			'th' => 'Item Based or Total Based',
			'mysql_field' => 'item_or_total',
			'sort' => 'item_or_total'),
		array(
			'th' => 'blanket',
			'type' =>'checkbox',
			'mysql_field' => 'blanket',
			'sort' => 'blanket'),
		array(
			'th' => 'Promotion Amount',
			'mysql_field' => 'buy_y_get_x',
			'sort' => 'buy_y_get_x'),
		array(
			'th' => 'Catgories',
			'mysql_field' => 'categories',
			'sort' => 'categories'),
		array(
			'th' => 'Brands',
			'mysql_field' => 'brands',
			'sort' => 'brands'),
		
		array(
			'th' => 'Products',
			'mysql_field' => 'products',
			'sort' => 'products'),
		array(
			'th' => 'Active',
			'type' =>'checkbox',
			'mysql_field' => 'active',
			'sort' => 'active'),	

		);
$html = printGetMessage('message');	
//saved search functionality
$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saved_promotion');

//if there is a message print it
$html = printGetMessage('message');
//here is the query that the search and table arrays are built off of.
$tmp_sql = "
CREATE TEMPORARY TABLE promotion

SELECT  
		pos_promotions.*,
		
		(SELECT group_concat(concat('Buy: ' , round(buy,1), ' Get: ', round(get,1) , ' Discount: ', concat_ws(' ', round(discount,1), d_or_p))) FROM pos_promotion_buy WHERE pos_promotion_buy.pos_promotion_id = pos_promotions.pos_promotion_id) as buy_y_get_x,
		
		(SELECT group_concat(concat(include_category, ' ' , key_name)) FROM pos_promotion_lookup LEFT JOIN pos_categories ON pos_categories.pos_category_id = pos_promotion_lookup.pos_category_id WHERE pos_promotion_lookup.pos_promotion_id = pos_promotions.pos_promotion_id) as categories,
		
		(SELECT group_concat(concat(include_brand, ' ' , brand_name)) FROM pos_promotion_lookup LEFT JOIN pos_manufacturer_brands ON pos_manufacturer_brands.pos_manufacturer_brand_id = pos_promotion_lookup.pos_manufacturer_brand_id WHERE pos_promotion_lookup.pos_promotion_id = pos_promotions.pos_promotion_id) as brands,
		
		(SELECT group_concat(concat(include_product, ' ' , title, ' ', style_number)) FROM pos_promotion_lookup LEFT JOIN pos_products ON pos_products.pos_product_id = pos_promotion_lookup.pos_product_id WHERE pos_promotion_lookup.pos_promotion_id = pos_promotions.pos_promotion_id) as products
		
		
		
		FROM pos_promotions
		
		



		


;


";
$tmp_select_sql = "SELECT *
	FROM promotion WHERE 1";

//create the search form
$action = 'list_promotions.php';
//Create Search String (AND's after MYSQL WHERE from $_GET data)
$search_sql = createSearchSQLStringMultipleDates($search_fields);
$tmp_select_sql  .= $search_sql;

//Create the order sting to append to the sql statement
$order_by = createSortSQLString($table_columns, $table_columns[1]['mysql_field'], 'DESC');
$tmp_select_sql  .=  " ORDER BY $order_by";


$tmp_select_sql  .=  " LIMIT 100";
$dbc = openPOSdb();
$result = runTransactionSQL($dbc,$tmp_sql);
$data = getTransactionSQL($dbc,$tmp_select_sql);
closeDB($dbc);

//create some buttons
$html .= '<p>Promotions are discounts that have expiration dates and are entered below the sales invoice</p>';
$html .= '<p>';
$html .= '<input class = "button" type="button" style="width:300px" name="add_invoice" value="Create Promotion" onclick="open_win(\'promotion.php?type=Add\')"/>';

$html .= '</p>';
//now make the table
$html .= createSearchForm($search_fields,$action);
$html .= createRecordsTable($data, $table_columns);
$html .= '<script>document.getElementsByName("promotion_code")[0].focus();</script>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
?>
