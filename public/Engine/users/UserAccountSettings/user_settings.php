<?php 
$page_title = 'Users';
$binder_name = 'User Account Settings';
require_once ('../user_functions.php');
$page = 'user_settings.php';
$db_table = 'pos_users';
$primary_key = 'pos_user_id';
$complete_location = 'user_settings.php?type=View';
$cancel_location = $complete_location;
$form_handler = 'user_settings.form.handler.php';
//why?
if(!isset($_GET['type']))
{
	$type = 'view';
}
else
{
	$type = getPostOrGetValue('type');
}
$pos_user_id = (isset($_POST['pos_user_id']) || isset($_GET['pos_user_id'])) ? getPostOrGetID('pos_user_id') :  $_SESSION['pos_user_id'];
$key_val_id['pos_user_id'] = $pos_user_id;
$table_def = createUserSettingTableDef($type, $pos_user_id);
$html = '<p>Manage User</p>';
if (strtoupper($type) == 'VIEW')
{
	$data_table_def_with_data = selectSingleTableDataFromID($db_table, $key_val_id, $table_def);	
	$edit_location = $page. '?type=Edit&'. $primary_key.'='.$pos_user_id;
	$pswrd_location = $page. '?type=password&'. $primary_key.'='.$pos_user_id;
	$html .= printGetMessage('message');
	$html .= createHTMLTableForMYSQLData($data_table_def_with_data);
	$html .= '<p>';
	$html .= '<input class = "button" type="button" name="edit" value="Edit" onclick="open_win(\''.$edit_location .'\')"/>';
	$html .= '<input class = "button" type="button" name="edit" style="width:150px;" value="Change Password" onclick="open_win(\''.$pswrd_location .'\')"/>';
	$html .= '</p>';
	//now display the rooms with the option to modify...
	
	
	$html.= createRoomlist($pos_user_id);
	//$html.= createBinderCollectionlist($pos_user_id);
	
}
elseif (strtoupper($type) == 'EDIT')
{
	$table_def = selectSingleTableDataFromID($db_table, $key_val_id, $table_def);
	$big_html_table = createHTMLTableForMYSQLInsert($table_def);
	$big_html_table .= createHiddenInput('type', $type);
	$html .= createFormForMYSQLInsert($table_def ,$big_html_table, $form_handler, $complete_location, $cancel_location);	
}
elseif (strtoupper($type)=='PASSWORD')
{
	$html = '<p>Update Password</p>';
	$password_def = CreatePasswordTableDef();
	$big_html_table = createHTMLTableForMYSQLInsert($password_def);
	$big_html_table .= createHiddenInput('type', $type);
	$big_html_table .= createHiddenInput('pos_user_id', $key_val_id[$primary_key]);
	$html .= createFormForMYSQLInsert($table_def ,$big_html_table, $form_handler, $complete_location, $cancel_location);
}
else
{
	$html = 'missing type';
}
include (HEADER_FILE);		
echo $html;
include (FOOTER_FILE);
?>

