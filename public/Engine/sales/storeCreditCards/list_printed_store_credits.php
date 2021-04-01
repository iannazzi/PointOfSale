<?php
/*
	craig iannazzi 11-26-2014 with a stiff neck one day before thanksgiving.
	
	This is just a listing of printed or aquired store credit card numbers, primarily for debug
*/
$page_title = 'Store Credits';
$binder_name = 'Store Credits';
$access_type = 'READ';
require_once ('../sales_functions.php');

$search_fields = array(				array(	'db_field' => 'pos_store_credit_card_number_id',
											'mysql_search_result' => 'pos_store_credit_card_number_id',
											'caption' => 'Store Credit Card Number ID',	
											'type' => 'input',
											'html' => createSearchInput('pos_store_credit_card_number_id')
										),
										
										array(	'db_field' => 'card_number',
											'mysql_search_result' => 'card_number',
											'caption' => 'Card Number',	
											'type' => 'input',
											'html' => createSearchInput('card_number')
										),
										array(	'db_field' => 'date_created',
											'mysql_search_result' => 'date_created',
											'caption' => 'Date Created',	
											'type' => 'input',
											'html' => createSearchInput('date_created')
										)
									
								
										);
$table_columns = array(
		array(
			'th' => 'ID',
			'mysql_field' => 'pos_store_credit_card_number_id',
			/*'get_url_link' => "store_credits.php?type=View",
			'url_caption' => 'View',
			'get_id_link' => 'pos_store_credit_id',*/
			'sort' => 'pos_store_credit_card_number_id'),
		
		array(
			'th' => 'Card Number',
			'mysql_field' => 'card_number',
			'sort' => 'card_number'),
		array(
			'th' => 'date_created',
			'mysql_field' => 'date_created',
			'sort' => 'date_created'),



		);
$html = printGetMessage('message');	
//saved search functionality
$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saved_printed_store_credit');

//if there is a message print it
$html = printGetMessage('message');
//here is the query that the search and table arrays are built off of.
$tmp_sql = "
CREATE TEMPORARY TABLE store_credit


SELECT pos_store_credit_card_number_id, card_number, date_created
	
FROM pos_store_credit_card_numbers


";
$tmp_select_sql = "SELECT *
	FROM store_credit WHERE 1";

//create the search form
$action = 'list_printed_store_credits.php';
//Create Search String (AND's after MYSQL WHERE from $_GET data)
$search_sql = createSearchSQLStringMultipleDates($search_fields);
$tmp_select_sql  .= $search_sql;

//Create the order sting to append to the sql statement
$order_by = createSortSQLString($table_columns, $table_columns[0]['mysql_field'], 'DESC');
$tmp_select_sql  .=  " ORDER BY $order_by";


$tmp_select_sql  .=  " LIMIT 100";
$dbc = openPOSdb();
$result = runTransactionSQL($dbc,$tmp_sql);
$data = getTransactionSQL($dbc,$tmp_select_sql);
closeDB($dbc);

//create some buttons
//Add a button to add an expense
$html .= '<p>';
//$html .= '<input class = "button" type="button" style="width:300px" name="add_invoice" value="Batch Test" onclick="open_win(\'store_credits.php?type=batch_test\')"/>';


$html .= '<input class = "button" type="button" style="width:300px" name="add_invoice" value="List Store Credit Cards" onclick="open_win(\'list_store_credits.php\')"/>';

$html .= '</p>';
//now make the table
$html .= createSearchForm($search_fields,$action);
$html .= createRecordsTable($data, $table_columns);
$html .= '<script>document.getElementsByName("card_number")[0].focus();</script>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
?>
