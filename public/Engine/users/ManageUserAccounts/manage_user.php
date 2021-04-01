<?php 
$page_title = 'Users';
//need this binder name to check if user has access to this page...
$binder_name = 'System User Accounts';

require_once ('../user_functions.php');
$page = 'manage_user.php';
$db_table = 'pos_users';
$complete_location = '../users.php';
$cancel_location = '../users.php';
$form_handler = 'manage_user.form.handler.php';
$type = getPostOrGetValue('type');
$pos_user_id = ($type == 'New') ? 'TBD' : getPostOrGetID('pos_user_id');
$key_val_id['pos_user_id'] = $pos_user_id;
$table_def = createAdminUserTableDef($type,  $pos_user_id);
$html = '<p>Manage User</p>';
if (strtoupper($type) == 'VIEW')
{
	$data_table_def_with_data = selectSingleTableDataFromID($db_table, $key_val_id, $table_def);
	//$right_table = 	selectSingleTableDataFromID($db_table, $key_val_id, createRightsTableDef($pos_user_id));
	$edit_location = $page. '?type=Edit&'. 'pos_user_id='.$pos_user_id ;
	$pswrd_location = $page. '?type=password&'. 'pos_user_id='.$pos_user_id ;
	$html .= printGetMessage('message');
	$html .= createHTMLTableForMYSQLData($data_table_def_with_data);
	$html .= '<p>';
	$html .= '<input class = "button" type="button" name="edit" value="Edit" onclick="open_win(\''.$edit_location .'\')"/>';
	$html .= '<input class = "button" type="button" name="edit" style="width:150px;" value="Change Password" onclick="open_win(\''.$pswrd_location .'\')"/>';
	$html .= '</p>';
	$html .= listUserBinderAccess($pos_user_id);
	$html .= listUserGroups($pos_user_id);
	$html.= createRoomlist($pos_user_id);
	
	$html .= createActivityTable($pos_user_id);
	
	$html .= '<input class = "button" type="button" name="edit" style="width:200px" value="Create Default Rooms" onclick="open_win(\'create_default_room_arrangement.php?pos_user_id='.$pos_user_id.'\')"/>';
	
	

}
elseif (strtoupper($type) == 'PASSWORD')
{
	$html = '<p>Update Password</p>';
	$html .= '<p>You Need minimum 8 characters, 1 special character *@#$%^&+= one upper case, on lower case, and one digit.</p>';
	$password_def = CreatePasswordTableDef();
	$big_html_table = createHTMLTableForMYSQLInsert($password_def);
	$big_html_table .= createHiddenInput('type', $type);
	$big_html_table .= createHiddenInput('pos_user_id', $pos_user_id);
	$html .= createFormForMYSQLInsert($password_def ,$big_html_table, $form_handler, $complete_location, $cancel_location);
}
else
{
	if (strtoupper($type) == 'NEW')
	{
	}
	elseif(strtoupper($type) == 'EDIT')
	{
		$table_def = selectSingleTableDataFromID($db_table, $key_val_id, $table_def);
	}
	
	$big_html_table = createHTMLTableForMYSQLInsert($table_def);
	$big_html_table .= createHiddenInput('type', $type);
	$html .= createFormForMYSQLInsert($table_def ,$big_html_table, $form_handler, $complete_location, $cancel_location);	
}
include (HEADER_FILE);		
echo $html;
include (FOOTER_FILE);

function listUserGroups($pos_user_id)
{

	$table_columns = array(
	
			array(
				'th' => 'ID',
				'mysql_field' => 'pos_user_group_id',
				'sort' => 'pos_user_group_id'),	
			array(
				'th' => 'Group Name',
				'mysql_field' => 'group_name',
				'sort' => 'group_name'),
			
			
			
			);
	
	//here is the query that the search and table arrays are built off of.
	$tmp_sql = "
	CREATE TEMPORARY TABLE user_groups
	
	SELECT  group_name, pos_user_groups.pos_user_group_id
			FROM pos_users_in_groups
			LEFT JOIN pos_user_groups USING (pos_user_group_id)
			WHERE pos_users_in_groups.pos_user_id = $pos_user_id
	;
	
	
	";
	$tmp_select_sql = "SELECT * 
		FROM user_groups WHERE 1";
	
	
	$html = '<p>';
	$html .= '<input class = "button" type="button" style="width:300px" name="add_system" value="Edit Groups" onclick="open_win(\''.POS_ENGINE_URL . '/users/ManageUserAccounts/user_groups.php?type=EDIT&pos_user_id='.$pos_user_id.'\')"/>';
	$html .= '</p>';
	
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,$tmp_sql);
	$data = getTransactionSQL($dbc,$tmp_select_sql);
	closeDB($dbc);
	
	//now make the table
	
	$html .= createRecordsTable($data, $table_columns);
	return $html;


}
function createActivityTable($pos_user_id)
{


	$table_columns = array(
			array(
				'th' => 'View Activity',
				'mysql_field' => 'date',
				'get_url_link' => "user_activity.php?type=View&pos_user_id=".$pos_user_id,
				'url_caption' => 'View',
				'get_id_link' => 'date'),
			array(
				'th' => 'Date',
				'mysql_field' => 'date',
				'sort' => 'date'),
			array(
				'th' => 'Hits',
				'mysql_field' => 'hits',
				'sort' => 'hits'),

			array(
				'th' => 'start_time',
				'mysql_field' => 'start_time',
				'sort' => 'start_time'),
			array(
				'th' => 'end_time',
				'mysql_field' => 'end_time',
				'sort' => 'end_time'),
		
			
		
			);
	$html = '<h2>User Activity Log</h2>';
	//saved search functionality

	//here is the query that the search and table arrays are built off of.
	$tmp_sql = "
	CREATE TEMPORARY TABLE tmp

	SELECT  
			DATE(time) as date, TIME(min(time)) as start_time, TIME(max(time)) as end_time, count(*) as hits
		
			FROM pos_user_log
			LEFT JOIN pos_users USING(pos_user_id)
			WHERE pos_user_log.pos_user_id = $pos_user_id
			GROUP BY DATE(time)
			ORDER BY date DESC
		

	;


	";
	$tmp_select_sql = "SELECT *
		FROM tmp WHERE 1";

	//create the search form
	//$action = 'list_active_users.php';
	//Create Search String (AND's after MYSQL WHERE from $_GET data)
	//$search_sql = createSearchSQLStringMultipleDates($search_fields);
	//$tmp_select_sql  .= $search_sql;

	//Create the order sting to append to the sql statement
	//$order_by = createSortSQLString($table_columns, $table_columns[1]['mysql_field'], 'DESC');
	//$tmp_select_sql  .=  " ORDER BY $order_by";


	//$tmp_select_sql  .=  " LIMIT 100";
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,$tmp_sql);
	$data = getTransactionSQL($dbc,$tmp_select_sql);
	closeDB($dbc);

	$html .= createRecordsTable($data, $table_columns);
	//$html .= '<script>document.getElementsByName("zip")[0].focus();</script>';

	return $html;

}
?>

