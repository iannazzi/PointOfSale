<?php
/*
	*shows a list of all registered manufacturers
*/
$page_title = 'Settings';
$binder_name = 'Settings';
$access_type = 'READ';
require_once ('../system_functions.php');

/* value could link to any databas entry and code... so how do we handle that here? => I should put that in another field? Yes
*/

$search_fields = array(				array(	'db_field' => 'name',
											'mysql_search_result' => 'name',
											'caption' => 'Name',	
											'type' => 'input',
											'html' => createSearchInput('name')
										),
										array(	'db_field' => 'group_name',
											'mysql_search_result' => 'group_name',
											'caption' => 'Group Name',	
											'type' => 'input',
											'html' => createSearchInput('group_name')
										),
										array(	'db_field' => 'value_text',
											'mysql_search_result' => 'value_text',
											'caption' => 'Value',	
											'type' => 'input',
											'html' => createSearchInput('value_text')
										)
							
									
								
										);
$table_columns = array(
		array(
			'th' => 'View',
			'mysql_field' => 'name',
			'get_url_link' => "settings.php?type=View",
			'url_caption' => 'View',
			'get_id_link' => 'name'),
		array(
			'th' => 'Name',
			'mysql_field' => 'name',
			'sort' => 'name'),
		array(
			'th' => 'Group Name',
			'mysql_field' => 'group_name',
			'sort' => 'group_name'),
		array(
			'th' => 'Value',
			'mysql_field' => 'value_text',
			'sort' => 'value_text'),	
		array(
			'th' => 'Description',
			'mysql_field' => 'description',
			'sort' => 'description'),
		

		);
$html = printGetMessage('message');	
//saved search functionality
$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saved_settings_search');

//if there is a message print it
$html = printGetMessage('message');
//here is the query that the search and table arrays are built off of.
$tmp_sql = "
CREATE TEMPORARY TABLE settings

SELECT  
		pos_settings.*
		FROM pos_settings
		



		


;


";
$tmp_select_sql = "SELECT *
	FROM settings WHERE 1";

//create the search form
$action = 'list_settings.php';
//Create Search String (AND's after MYSQL WHERE from $_GET data)
$search_sql = createSearchSQLStringMultipleDates($search_fields);
$tmp_select_sql  .= $search_sql;


//Create the order sting to append to the sql statement
$order_by = createSortSQLString($table_columns, $table_columns[2]['mysql_field'], 'DESC');
$tmp_select_sql  .=  " ORDER BY $order_by";


//$tmp_select_sql  .=  " LIMIT 100";
$dbc = openPOSdb();
$result = runTransactionSQL($dbc,$tmp_sql);
$data = getTransactionSQL($dbc,$tmp_select_sql);
closeDB($dbc);


//now make the table
$html .= createSearchForm($search_fields,$action);
$html .= createRecordsTable($data, $table_columns);
$html .= '<script>document.getElementsByName("name")[0].focus();</script>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
?>
