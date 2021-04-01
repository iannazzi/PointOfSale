<?php 

/*

	Craig Iannazzi 4-23-12
	
*/
$binder_name = 'Accounts';
$access_type = 'READ';
$page_title = 'Accounts';
require_once ('../accounting_functions.php');

$db_table = 'pos_accounts';
$complete_location = 'list_accounts.php';
$cancel_location = 'list_accounts.php';
$key_val_id['pos_account_id'] = getPostOrGetID('pos_account_id');
$pos_account_id = $key_val_id['pos_account_id'];
$edit_location = 'edit_account.php?pos_account_id='.$key_val_id['pos_account_id'];

$data_table_def = createAccountTableDef('Edit', $key_val_id);
$data_table_def_with_data = selectSingleTableDataFromID($db_table, $key_val_id, $data_table_def);	
$multi_select_table_def=	getAccountMultiSelectTableDef($key_val_id['pos_account_id']);
include (HEADER_FILE);
$html = printGetMessage();
$html .= '<p>Account Details</p>';
$html .= createHTMLTableForMYSQLData($data_table_def_with_data);
$html .= createHTMLTableForMYSQLData($multi_select_table_def);
//Add the edit button
$html .= '<p>';
$html .= '<input class = "button" type="button" name="edit" value="Edit" onclick="open_win(\''.$edit_location.'\')"/>';
$html .='<input class = "button" style="width:200px" type="button" name="return" value="Return To Accounts" onclick="open_win(\'list_accounts.php\')"/>';
$html .= '</p>';






echo $html;
include (FOOTER_FILE);

?>

