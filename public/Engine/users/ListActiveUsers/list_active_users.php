<?php
/*
	*shows a list of all registered manufacturers
*/
$page_title = 'Active Users';
require_once ('../user_functions.php');

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
			'mysql_field' => 'pos_user_id',
			'get_url_link' => "../ManageUser/manage_user.php?type=View",
			'url_caption' => 'View',
			'get_id_link' => 'pos_user_id'),
		array(
			'th' => 'User Id',
			'mysql_field' => 'pos_user_id',
			'sort' => 'pos_user_id'),

		array(
			'th' => 'login',
			'mysql_field' => 'login',
			'sort' => 'login'),
		
		array(
			'th' => 'Last Accessed',
			'mysql_field' => 'last_accessed',
			'date_format' => 'date',
			'sort' => 'last_accessed'),
		array(
			'th' => 'Browser',
			'mysql_field' => 'browser',
			'sort' => 'browser'),
		
		array(
			'th' => 'IP Address',
			'mysql_field' => 'ip_address',
			'sort' => 'ip_address'),
		array(
			'th' => 'Current Page',
			'mysql_field' => 'current_page',
			'sort' => 'current_page'),

		array(
			'th' => 'Time Remaining',
			'mysql_field' => 'session_time_remaining',
			'sort' => 'session_time_remaining')
		
		);
$html = printGetMessage('message');	
//saved search functionality
$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saved_user_activity_list');

//here is the query that the search and table arrays are built off of.
$tmp_sql = "
CREATE TEMPORARY TABLE tmp

SELECT  
		pos_users_logged_in.*, CONCAT_WS(' ', first_name, last_name) as full_name, email, login
		
		FROM pos_users_logged_in
		LEFT JOIN pos_users USING(pos_user_id)
		

;


";
$tmp_select_sql = "SELECT *
	FROM tmp WHERE 1";

//create the search form
$action = 'list_active_users.php';
//Create Search String (AND's after MYSQL WHERE from $_GET data)
//$search_sql = createSearchSQLStringMultipleDates($search_fields);
//$tmp_select_sql  .= $search_sql;

//Create the order sting to append to the sql statement
$order_by = createSortSQLString($table_columns, $table_columns[1]['mysql_field'], 'DESC');
$tmp_select_sql  .=  " ORDER BY $order_by";


//$tmp_select_sql  .=  " LIMIT 100";
$dbc = openPOSdb();
$result = runTransactionSQL($dbc,$tmp_sql);
$data = getTransactionSQL($dbc,$tmp_select_sql);
closeDB($dbc);

//now make the table
//$html .= createSearchForm($search_fields,$action);
$html .= createRecordsTable($data, $table_columns);
//$html .= '<script>document.getElementsByName("zip")[0].focus();</script>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
?>
