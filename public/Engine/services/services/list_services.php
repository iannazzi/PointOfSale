<?php
/*
	*shows a list of all registered manufacturers
*/
$page_title = 'Services';
$binder_name = 'Services';
$access_type = 'READ';
require_once ('../services_functions.php');

$search_fields = array(				array(	'db_field' => 'pos_service_id',
											'mysql_search_result' => 'pos_service_id',
											'caption' => 'Service ID',	
											'type' => 'input',
											'html' => createSearchInput('pos_service_id')
										),
										array(	'db_field' => 'service_code',
											'mysql_search_result' => 'service_code',
											'caption' => 'Service Code',	
											'type' => 'input',
											'html' => createSearchInput('service_code')
										),
										array(	'db_field' => 'title',
											'mysql_search_result' => 'title',
											'caption' => 'title',	
											'type' => 'input',
											'html' => createSearchInput('title')
										)
							
									
								
										);
$table_columns = array(
		array(
			'th' => 'View',
			'mysql_field' => 'pos_service_id',
			'get_url_link' => "service.php?type=View",
			'url_caption' => 'View',
			'get_id_link' => 'pos_service_id'),
		array(
			'th' => 'ID',
			'mysql_field' => 'pos_service_id',
			'sort' => 'pos_service_id'),
		array(
			'th' => 'Barcode',
			'mysql_field' => 'barcode',
			'sort' => 'barcode'),	
		array(
			'th' => 'Service Code',
			'mysql_field' => 'service_code',
			'sort' => 'service_code'),
		array(
			'th' => 'Title',
			'mysql_field' => 'title',
			'sort' => 'title'),
		array(
			'th' => 'Price',
			'mysql_field' => 'retail_price',
			'round' => 2,
			'sort' => 'retail_price'),
		
		array(
			'th' => 'Active',
			'type' =>'checkbox',
			'mysql_field' => 'active',
			'sort' => 'active'),	

		);
$html = printGetMessage('message');	
//saved search functionality
$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saved_services');

//if there is a message print it
$html = printGetMessage('message');
//here is the query that the search and table arrays are built off of.
$tmp_sql = "
CREATE TEMPORARY TABLE services

SELECT  
		*
		FROM pos_services
		



		


;


";
$tmp_select_sql = "SELECT *
	FROM services WHERE 1";

//create the search form
$action = 'list_services.php';
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

$html .= '<input class = "button" type="button" style="width:300px" name="add_invoice" value="Create Service" onclick="open_win(\'service.php?type=Add\')"/>';

$html .= '</p>';
//now make the table
$html .= createSearchForm($search_fields,$action);
$html .= createRecordsTable($data, $table_columns);
$html .= '<script>document.getElementsByName("service_code")[0].focus();</script>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
?>
