<?php
/*
	*shows a list of all registered manufacturers
*/
$page_title = 'Cash Drawers';
$binder_name = 'Cash Drawers';
$access_type = 'READ';
require_once ('../system_functions.php');

$search_fields = array(				array(	'db_field' => 'pos_cash_drawer_id',
											'mysql_search_result' => 'pos_cash_drawer_id',
											'caption' => 'Cash Drawer ID',	
											'type' => 'input',
											'html' => createSearchInput('pos_cash_drawer_id')
										),
										array(	'db_field' => 'pos_store_id',
											'mysql_search_result' => 'pos_store_id',
											'caption' => 'Store',	
											'type' => 'select',
											'html' => createStoreSelect('pos_store_id', 'false', 'on', '')
										),
										array(	'db_field' => 'drawer_name',
											'mysql_search_result' => 'drawer_name',
											'caption' => 'Drawer Name',	
											'type' => 'input',
											'html' => createSearchInput('drawer_name')
										)
							
									
								
										);
$table_columns = array(
		array(
			'th' => 'View',
			'mysql_field' => 'pos_cash_drawer_id',
			'get_url_link' => "cash_drawers.php?type=View",
			'url_caption' => 'View',
			'get_id_link' => 'pos_cash_drawer_id'),
		array(
			'th' => 'ID',
			'mysql_field' => 'pos_cash_drawer_id',
			'sort' => 'pos_cash_drawer_id'),
		array(
			'th' => 'Store Name',
			'mysql_field' => 'store_name',
			'sort' => 'store_name'),	
		array(
			'th' => 'Cash Drawer Name',
			'mysql_field' => 'cash_drawer_name',
			'sort' => 'cash_drawer_name'),
		array(
			'th' => 'Location',
			'mysql_field' => 'location',
			'sort' => 'location'),
		array(
			'th' => 'Cash Drawer Description',
			'mysql_field' => 'cash_drawer_description',
			'sort' => 'title'),
		
		array(
			'th' => 'Active',
			'type' =>'checkbox',
			'mysql_field' => 'active',
			'sort' => 'active'),	

		);
$html = printGetMessage('message');	
//saved search functionality
$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saved_cash_drawers');

//if there is a message print it
$html = printGetMessage('message');
//here is the query that the search and table arrays are built off of.
$tmp_sql = "
CREATE TEMPORARY TABLE cash_drawers

SELECT  
		pos_cash_drawers.*, store_name
		FROM pos_cash_drawers
		LEFT JOIN pos_stores USING (pos_store_id)
		



		


;


";
$tmp_select_sql = "SELECT *
	FROM cash_drawers WHERE 1";

//create the search form
$action = 'list_cash_drawers.php';
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

$html .= '<input class = "button" type="button" style="width:300px" name="add_invoice" value="Add A Cash Drawer" onclick="open_win(\'cash_drawers.php?type=Add\')"/>';

$html .= '</p>';
//now make the table
$html .= createSearchForm($search_fields,$action);
$html .= createRecordsTable($data, $table_columns);
$html .= '<script>document.getElementsByName("pos_cash_drawer_id")[0].focus();</script>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
?>
