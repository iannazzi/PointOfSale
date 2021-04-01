<?php
/*
	*shows a list of all registered manufacturers
*/
$page_title = 'Customer Payment Methods';
require_once ('../sales_functions.php');

$search_fields = array(				array(	'db_field' => 'pos_sales_tax_rate_id',
											'mysql_search_result' => 'pos_sales_tax_rate_id',
											'caption' => 'Rate ID',	
											'type' => 'input',
											'html' => createSearchInput('pos_sales_tax_rate_id')
										),
										

						array( 'db_field' => 'pos_sales_tax_category_id',
								'type' => 'select',
								'caption' => 'Default Sales Tax Category',
								'html' => createSalesTaxCategorySelect('pos_sales_tax_category_id', 'false')),
						array(	'db_field' => 'start_date',
											'mysql_search_result' => 'start_date',
											'caption' => 'Start Date',
											'type' => 'start_date',
											'html' => dateSelect('start_date_start_date',valueFromGetOrDefault('start_date_start_date'))
										),
								array(	'db_field' => 'start_date',
											'mysql_search_result' => 'start_date',
											'caption' => 'Start_date End',	
											'type' => 'start_date',
											'html' => dateSelect('start_date_end_date',valueFromGetOrDefault('start_date_end_date'))
										),
						array('db_field' =>  'state',
								'type' => 'input',
								'caption' => 'State',
								'html' => createSearchInput('tax_category_name')),
						array('db_field' =>  'state_tax',
								'type' => 'input',
								'caption' => 'State Rate (%)',
								'html' => createSearchInput('state_tax')),
						array('db_field' =>  'county',
								'type' => 'input',
								'caption' => 'Jurisdiction Name',
						'html' => createSearchInput('county')),
						array('db_field' =>  'zip_code',
								'type' => 'input',
								'caption' => 'Zip Code',
								'html' => createSearchInput('zip_code')),
						array('db_field' =>  'local_tax',
								'type' => 'input',
								'caption' => 'Local Rate (%)',
								'html' => createSearchInput('local_tax'))
										
										
										
										);
$table_columns = array(
		array(
			'th' => 'View',
			'mysql_field' => 'pos_customer_payment_method_id',
			'get_url_link' => "add_edit_view_customer_payment_method.php?type=view",
			'url_caption' => 'View',
			'get_id_link' => 'pos_customer_payment_method_id'),
		array(
			'th' => 'ID',
			'mysql_field' => 'pos_customer_payment_method_id',
			'sort' => 'pos_customer_payment_method_id'),	
		array(
			'th' => 'Name',
			'mysql_field' => 'payment_method_name',
			'sort' => 'payment_method_name'),
		
		
		);
$html = printGetMessage('message');	
//saved search functionality
//$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saved_tax_rates_url');

//if there is a message print it
$html = printGetMessage('message');
//here is the query that the search and table arrays are built off of.
$tmp_sql = "
CREATE TEMPORARY TABLE tmp

SELECT  
		pos_customer_payment_method_id, payment_method_name
		
		FROM pos_customer_payment_methods


;


";
$tmp_select_sql = "SELECT *
	FROM tmp WHERE 1";

//create the search form
$action = 'list_payment_methods.php';
//Create Search String (AND's after MYSQL WHERE from $_GET data)
//$search_sql = createSearchSQLStringMultipleDates($search_fields);
//$tmp_select_sql  .= $search_sql;

//Create the order sting to append to the sql statement
//$order_by = createSortSQLString($table_columns, $table_columns[1]['mysql_field'], 'DESC');
//$tmp_select_sql  .=  " ORDER BY $order_by";


//$tmp_select_sql  .=  " LIMIT 100";
$dbc = openPOSdb();
$result = runTransactionSQL($dbc,$tmp_sql);
$data = getTransactionSQL($dbc,$tmp_select_sql);
closeDB($dbc);

//create some buttons
//Add a button to add an expense
$html .= '<p>';
$html .= '<input class = "button" type="button" style="width:300px" name="add_category" value="Add Payment Method" onclick="open_win(\'add_edit_view_payment_method.php?type=Add\')"/>';


$html .= '</p>';
//now make the table
$html .= createSearchForm($search_fields,$action);
$html .= createRecordsTable($data, $table_columns);
//$html .= '<script>document.getElementsByName("zip")[0].focus();</script>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
?>
