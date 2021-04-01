<?php
/*
	*shows a list of all registered manufacturers
*/
$page_title = 'Binders';
require_once ('../system_functions.php');
require_once(PHP_LIBRARY);
require_once (CHECK_LOGIN_FILE);

//the system needs to give me the database connection!


$search_fields = array(				array(	'db_field' => 'pos_system_id',
											'mysql_search_result' => 'pos_system_id',
											'caption' => 'ID',	
											'type' => 'input',
											'html' => createSearchInput('pos_system_id')
										),
										
										array(	'db_field' => 'account_name',
											'mysql_search_result' => 'account_name',
											'caption' => 'Account Name',	
											'type' => 'input',
											'html' => createSearchInput('account_name')
										),
										array(	'db_field' => 'user_name',
											'mysql_search_result' => 'user_name',
											'caption' => 'User Name',	
											'type' => 'input',
											'html' => createSearchInput('user_name')
										),
										array(	'db_field' => 'database_name',
											'mysql_search_result' => 'database_name',
											'caption' => 'Database Name',	
											'type' => 'input',
											'html' => createSearchInput('database_name')
										),
										array(	'db_field' => 'binder_access',
											'mysql_search_result' => 'database_name',
											'caption' => 'Binder Access',	
											'type' => 'input',
											'html' => createSearchInput('binder_access')
										),
								array(	'db_field' => 'create_date',
											'mysql_search_result' => 'create_date',
											'caption' => 'Creation Date Start',
											'type' => 'start_date',
											'html' => dateSelect('create_date_start_date',valueFromGetOrDefault('create_date_start_date'))
										),
								array(	'db_field' => 'create_date',
											'mysql_search_result' => 'create_date',
											'caption' => 'Create Date End',	
											'type' => 'end_date',
											'html' => dateSelect('create_date_end_date',valueFromGetOrDefault('create_date_end_date'))
										),
								array(	'db_field' => 'max_users',
											'mysql_search_result' => 'max_users',
											'caption' => 'Max Users',
											'type' => 'input',
											'html' => createSearchInput('max_users'))
										
										);
$table_columns = array(

		array(
			'th' => 'ID',
			'mysql_field' => 'pos_binder_id',
			'sort' => 'pos_binder_id'),	
		array(
			'th' => 'Binder Name',
			'mysql_field' => 'binder_name',
			'sort' => 'binder_name'),
		array(
			'th' => 'Enabled',
			'mysql_field' => 'enabled',
			'sort' => 'enabled'),
		/*array(
			'th' => 'Binder Collection',
			'mysql_field' => 'binder_collection',
			'sort' => 'binder_collection'),*/
		array(
			'th' => 'Binder Path',
			'mysql_field' => 'binder_path',
			'sort' => 'binder_path'),
		array(
			'th' => 'Navigation Caption',
			'mysql_field' => 'navigation_caption',
			'sort' => 'navigation_caption'),
		array(
			'th' => 'Default Rooms',
			'mysql_field' => 'default_rooms',
			'sort' => 'default_rooms'),
		
		
		);
$html = printGetMessage('message');	
//saved search functionality
$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saved_binders');

//if there is a message print it
$html = printGetMessage('message');
//here is the query that the search and table arrays are built off of.
$tmp_sql = "
CREATE TEMPORARY TABLE binders

SELECT  pos_binders.*
		FROM pos_binders
;


";
$tmp_select_sql = "SELECT * 
	FROM binders WHERE 1";
//define the search table


//$html .= '<p>';
//$html .= '<input class = "button" type="button" style="width:300px" name="add_system" value="Add A Binder" onclick="open_win(\'add_edit_view_binders.php?type=Add\')"/>';
//$html .= '</p>';

//create the search form

$action = 'list_binders.php';
//$html .= createSearchForm($search_fields,$action);
//Create Search String (AND's after MYSQL WHERE from $_GET data)
//$search_sql = createSearchSQLStringMultipleDates($search_fields);
//$tmp_select_sql  .= $search_sql;

//Create the order sting to append to the sql statement
//$order_by = createSortSQLString($table_columns, $table_columns[1]['mysql_field'], 'DESC');
//$tmp_select_sql  .=  " ORDER BY $order_by";

$dbc = openPOSdb();
$result = runTransactionSQL($dbc,$tmp_sql);
$data = getTransactionSQL($dbc,$tmp_select_sql);
closeDB($dbc);

//now make the table

$html .= createRecordsTable($data, $table_columns);
//$html .= '<script>document.getElementsByName("company")[0].focus();</script>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
?>
