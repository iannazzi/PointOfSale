<?php
/*
	*shows a list of all registered manufacturers
*/
$page_title = 'Stores';
require_once ('store_functions.php');

$search_fields = array(		
						array(	'db_field' => 'pos_store_id',
											'mysql_search_result' => 'pos_store_id',
											'caption' => 'Store ID',	
											'type' => 'input',
											'html' => createSearchInput('pos_store_id')
										),
						array( 'db_field' => 'store_name',
								'type' => 'input',
								'caption' => 'Store Name',
								'html' => createSearchInput('store_name')),
						array( 'db_field' => 'state',
								'type' => 'input',
								'caption' => 'State',
								'html' => createSearchInput('state')),
						array('db_field' =>  'jurisdiction_name',
								'type' => 'input',
								'caption' => 'Jurisdiction Name',
								'html' => createSearchInput('jurisdiction_name')),
						
										);
$table_columns = array(
		array(
			'th' => 'View',
			'mysql_field' => 'pos_store_id',
			'get_url_link' => "add_edit_view_store.php?type=view",
			'url_caption' => 'View',
			'get_id_link' => 'pos_store_id'),
		array(
			'th' => 'ID',
			'mysql_field' => 'pos_store_id',
			'sort' => 'pos_store_id'),	
		array(
			'th' => 'Store Name',
			'mysql_field' => 'store_name',
			'sort' => 'store_name'),
			array(
			'th' => 'State',
			'mysql_field' => 'state_name',
			'sort' => 'state_name'),
		array(
			'th' => 'Tax Jurisdiction Name',
			'mysql_field' => 'jurisdiction_name',
			'mysql_search_result' => 'jurisdiction_name',
			'sort' => 'jurisdiction_name'),
		
		
			
			
		
		);
$html = printGetMessage('message');	
//saved search functionality
$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saved_stores');

//if there is a message print it
$html = printGetMessage('message');
//here is the query that the search and table arrays are built off of.
$tmp_sql = "
CREATE TEMPORARY TABLE tmp

SELECT  
		pos_stores.*, pos_states.short_name as state_name, jurisdiction_name

		FROM pos_stores
		LEFT JOIN pos_tax_jurisdictions USING (pos_tax_jurisdiction_id)
		LEFT JOIN pos_states ON pos_stores.pos_state_id = pos_states.pos_state_id


;


";
$tmp_select_sql = "SELECT *
	FROM tmp WHERE active=1";

//create the search form
$action = 'list_stores.php';
//Create Search String (AND's after MYSQL WHERE from $_GET data)
$search_sql = createSearchSQLStringMultipleDates($search_fields);
$tmp_select_sql  .= $search_sql;

//Create the order sting to append to the sql statement
$order_by = createSortSQLString($table_columns, $table_columns[1]['mysql_field'], 'ASC');
$tmp_select_sql  .=  " ORDER BY $order_by";


//$tmp_select_sql  .=  " LIMIT 100";
$dbc = openPOSdb();
$result = runTransactionSQL($dbc,$tmp_sql);
$data = getTransactionSQL($dbc,$tmp_select_sql);
closeDB($dbc);

//create some buttons
//Add a button to add an expense
$html .= '<p>';
$html .= '<input class = "button" type="button" style="width:300px" name="add_store" value="Add Store" onclick="open_win(\'add_edit_view_store.php?type=add\')"/>';
$html .= '</p>';
//now make the table
$html .= createSearchForm($search_fields,$action);
$html .= createRecordsTable($data, $table_columns);
$html .= '<script>document.getElementsByName("store_name")[0].focus();</script>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
?>
