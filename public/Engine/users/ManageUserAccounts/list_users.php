<?php
/*
	list_accounts.php
	craig Iannazzi 4-23-12
*/
$page_title = 'Users';
$binder_name = 'System User Accounts';
require_once ('../user_functions.php');

include (HEADER_FILE);
//if there is a message print it
$html = printGetMessage('message');
//here is the query that the search and table arrays are built off of.
$sql = "

SELECT login, pos_users.pos_user_id, pos_users.first_name, pos_users.last_name, pos_users.email, pos_users.active, pos_users.admin, pos_users.rights FROM pos_users WHERE 1

";

//define the search table
$search_fields = array(				array(	'db_field' => 'first_name',
											'mysql_search_result' => 'first_name',
											'caption' => 'First Name',	
											'type' => 'input',
											'html' => createSearchInput('first_name')
										),
										array(	'db_field' => 'last_name',
											'mysql_search_result' => 'last_name',
											'caption' => 'Last Name',	
											'type' => 'input',
											'html' => createSearchInput('last_name')
										),
									array(	'db_field' => 'email',
											'mysql_search_result' => 'email',
											'caption' => 'Email',	
											'type' => 'input',
											'html' => createSearchInput('email')
										)
										
										);
$table_columns = array(
		array(
			'th' => 'View',
			'mysql_field' => 'pos_user_id',
			'get_url_link' => "manage_user.php?type=View",
			'url_caption' => 'View',
			'get_id_link' => 'pos_user_id'),
		array(
			'th' => 'Login ID',
			'mysql_field' => 'login',
			'sort' => 'login'),
		array(
			'th' => 'First Name',
			'mysql_field' => 'first_name',
			'sort' => 'first_name'),
		array(
			'th' => 'Last Name',
			'mysql_field' => 'last_name',
			'sort' => 'last_name'),
		array(
			'th' => 'Email',
			'mysql_field' => 'email',
			'sort' => 'email'),
		array(
			'th' => 'active',
			'mysql_field' => 'active',
			'sort' => 'active'),
		array(
			'th' => 'admin',
			'mysql_field' => 'admin',
			'sort' => 'admin'),
		array(
			'th' => 'rights',
			'mysql_field' => 'rights',
			'sort' => 'rights')
			);
		
//Add a button to add an expense
$html .= '<p>';
$html .= '<input class = "button" type="button" name="addUser" value="Add User" onclick="open_win(\'manage_user.php?type=New\')"/>';
$html .= '<input class = "button" type="button" name="addUser" value="List Active Users" onclick="open_win(\'../ListActiveUsers/list_active_users.php\')"/>';
$html .= '</p>';

//create the search form

$action = 'list_users.php';
$html .= createSearchForm($search_fields,$action);
//Create Search String (AND's after MYSQL WHERE from $_GET data)
$search_sql = createSearchSQLStringMultipleDates($search_fields);
$sql  .= $search_sql;
//Create the order sting to append to the sql statement
$order_by = createSortSQLString($table_columns, $table_columns[1]['mysql_field'], 'ASC');
$sql  .=  " ORDER BY $order_by";

//now make the table
$html .= createRecordsTable(getSQL($sql), $table_columns);
$html .= '<script>document.getElementsByName("last_name")[0].focus();</script>';
echo $html;

include (FOOTER_FILE);
?>
