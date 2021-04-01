<?php
/*
	*shows a list of all registered manufacturers
*/
$page_title = 'Discounts';
$binder_name = 'Discounts';
$access_type = 'READ';
require_once ('../sales_functions.php');

$search_fields = array(				array(	'db_field' => 'pos_discount_id',
											'mysql_search_result' => 'pos_discount_id',
											'caption' => 'Discount ID',	
											'type' => 'input',
											'html' => createSearchInput('pos_discount_id')
										),
										array(	'db_field' => 'discount_code',
											'mysql_search_result' => 'discount_code',
											'caption' => 'Discount Code',	
											'type' => 'input',
											'html' => createSearchInput('discount_code')
										),
										array(	'db_field' => 'discount_name',
											'mysql_search_result' => 'discount_name',
											'caption' => 'Discount Name',	
											'type' => 'input',
											'html' => createSearchInput('discount_name')
										),
	
								array(	'db_field' => 'discount_amount',
											'mysql_search_result' => 'discount_amount',
											'caption' => 'Discount Amount',	
											'type' => 'input',
											'html' => createSearchInput('discount_amount')
											),
							
									
								
										);
$table_columns = array(
		array(
			'th' => 'View',
			'mysql_field' => 'pos_discount_id',
			'get_url_link' => "discount.php?type=View",
			'url_caption' => 'View',
			'get_id_link' => 'pos_discount_id'),
		array(
			'th' => 'ID',
			'mysql_field' => 'pos_discount_id',
			'sort' => 'pos_discount_id'),	
		array(
			'th' => 'Discount Code',
			'mysql_field' => 'discount_code',
			'sort' => 'discount_code'),
		array(
			'th' => 'Discount Name',
			'mysql_field' => 'discount_name',
			'sort' => 'discount_name'),
		array(
			'th' => 'Discount Amount',
			'mysql_field' => 'discount_amount',
			'round' => 2,
			'sort' => 'discount_amount'),
		array(
			'th' => 'Calculation',
			'mysql_field' => 'percent_or_dollars',
			'sort' => 'percent_or_dollars'),
		
		array(
			'th' => 'Active',
			'type' =>'checkbox',
			'mysql_field' => 'active',
			'sort' => 'active'),	

		);
$html = printGetMessage('message');	
//saved search functionality
$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saved_discount');

//if there is a message print it
$html = printGetMessage('message');
//here is the query that the search and table arrays are built off of.
$tmp_sql = "
CREATE TEMPORARY TABLE discount

SELECT  
		*
		FROM pos_discounts
		
		



		


;


";
$tmp_select_sql = "SELECT *
	FROM discount WHERE 1";

//create the search form
$action = 'list_discounts.php';
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
//Add a button to add an expense
$html .= '<p>';
$html .= '<p>Discounts codes designate why an item is discounted when an item is given a discount. Discount codes are selected on the same line as the item on an invoice being discounted. There is no expiration dates associated with discount codes. Promotions on the other hand are more like coupons, with an expiration date. </p>';
$html .= '<input class = "button" type="button" style="width:300px" name="add_invoice" value="Create Discount" onclick="open_win(\'discount.php?type=Add\')"/>';

$html .= '</p>';
//now make the table
$html .= createSearchForm($search_fields,$action);
$html .= createRecordsTable($data, $table_columns);
$html .= '<script>document.getElementsByName("discount_code")[0].focus();</script>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
?>
