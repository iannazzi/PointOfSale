<?php
/*
	*shows a list of all registered manufacturers
*/
$page_title = 'Shipping Options';
$binder_name = 'Shipping Methods';
$access_type = 'READ';
require_once ('../services_functions.php');

$search_fields = array(				array(	'db_field' => 'pos_shipping_option_id',
											'mysql_search_result' => 'pos_shipping_option_id',
											'caption' => 'Shipping Option ID',	
											'type' => 'input',
											'html' => createSearchInput('pos_shipping_option_id')
										),
										array(	'db_field' => 'carrier_name',
											'mysql_search_result' => 'carrier_name',
											'caption' => 'Carrier Name',	
											'type' => 'input',
											'html' => createSearchInput('carrier_name')
										),
										array(	'db_field' => 'method_name',
											'mysql_search_result' => 'method_name',
											'caption' => 'Shipping Method',	
											'type' => 'input',
											'html' => createSearchInput('method_name')
										)
							
									
								
										);
$table_columns = array(
		array(
			'th' => 'View',
			'mysql_field' => 'pos_shipping_option_id',
			'get_url_link' => "shipping_options.php?type=View",
			'url_caption' => 'View',
			'get_id_link' => 'pos_shipping_option_id'),
		array(
			'th' => 'ID',
			'mysql_field' => 'pos_shipping_option_id',
			'sort' => 'pos_shipping_option_id'),	
		array(
			'th' => 'Barcode',
			'mysql_field' => 'barcode',
			'sort' => 'barcode'),
		array(
			'th' => 'Carrier Name',
			'mysql_field' => 'carrier_name',
			'sort' => 'carrier_name'),
		array(
			'th' => 'Shipping Method',
			'mysql_field' => 'method_name',
			'sort' => 'method_name'),
		array(
			'th' => 'Active',
			'type' =>'checkbox',
			'mysql_field' => 'active',
			'sort' => 'active'),	

		);
$html = printGetMessage('message');	
//saved search functionality
$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saved_shipping_options');

//if there is a message print it
$html = printGetMessage('message');
//here is the query that the search and table arrays are built off of.
$tmp_sql = "
CREATE TEMPORARY TABLE shipping

SELECT  
		*
		FROM pos_shipping_options
		



		


;


";
$tmp_select_sql = "SELECT *
	FROM shipping WHERE 1";

//create the search form
$action = 'list_shipping_options.php';
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

$html .= '<input class = "button" type="button" style="width:300px" name="add_invoice" value="Create Shipping Option" onclick="open_win(\'shipping_options.php?type=Add\')"/>';

$html .= '</p>';
//now make the table
$html .= createSearchForm($search_fields,$action);
$html .= createRecordsTable($data, $table_columns);
$html .= '<script>document.getElementsByName("carrier_name")[0].focus();</script>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
?>
