<?php
/*
	*shows a list of all registered manufacturers
*/
$page_title = 'User Groups';
$binder_name = 'User Groups';
$access_type = 'READ';
require_once ('../user_functions.php');

$search_fields = array(				array(	'db_field' => 'pos_user_group_id',
											'mysql_search_result' => 'pos_user_group_id',
											'caption' => 'User Group ID',	
											'type' => 'input',
											'html' => createSearchInput('pos_user_group_id')
										),
										array(	'db_field' => 'group_name',
											'mysql_search_result' => 'group_name',
											'caption' => 'Group Name',	
											'type' => 'input',
											'html' => createSearchInput('group_name')
										)
							
									
								
										);
$table_columns = array(
		array(
			'th' => 'View',
			'mysql_field' => 'pos_user_group_id',
			'get_url_link' => "user_groups.php?type=View",
			'url_caption' => 'View',
			'get_id_link' => 'pos_user_group_id'),
		array(
			'th' => 'ID',
			'mysql_field' => 'pos_user_group_id',
			'sort' => 'pos_user_group_id'),
		array(
			'th' => 'Group Name',
			'mysql_field' => 'group_name',
			'sort' => 'group_name'),	
		
		array(
			'th' => 'Active',
			'type' =>'checkbox',
			'mysql_field' => 'active',
			'sort' => 'active'),	

		);
$html = printGetMessage('message');	
//saved search functionality
$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saved_user_groups');

//if there is a message print it
$html = printGetMessage('message');
//here is the query that the search and table arrays are built off of.
$tmp_sql = "
CREATE TEMPORARY TABLE services

SELECT  
		*
		FROM pos_user_groups
		



		


;


";
$tmp_select_sql = "SELECT *
	FROM services WHERE 1";

//create the search form
$action = 'list_user_groups.php';
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

$html .= '<input class = "button" type="button" style="width:300px" name="add_invoice" value="Create User Group" onclick="open_win(\'user_groups.php?type=Add\')"/>';

$html .= '</p>';
//now make the table
$html .= createSearchForm($search_fields,$action);
$html .= createRecordsTable($data, $table_columns);
$html .= '<script>document.getElementsByName("service_code")[0].focus();</script>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
?>
