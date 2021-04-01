<?php 

/*

	Craig Iannazzi 4-23-12
	
*/
$binder_name = 'Accounts';
$access_type = 'READ';
$page_title = 'Accounts';
require_once ('../accounting_functions.php');

$db_table = 'pos_account_balances';
$complete_location = 'list_account_balances.php';
$cancel_location = 'list_account_balances.php';
$key_val_id['pos_account_balance_id'] = getPostOrGetID('pos_account_balance_id');
$edit_location = 'edit_account_balance.php?pos_account_balance_id='.$key_val_id['pos_account_balance_id'];

$data_table_def = createAccountBalanceTableDef('Edit', $key_val_id);
$data_table_def_with_data = selectSingleTableDataFromID($db_table, $key_val_id, $data_table_def);	
include (HEADER_FILE);
$html = printGetMessage();
$html .= '<p>Account Balance Details</p>';
$html .= createHTMLTableForMYSQLData($data_table_def_with_data);
//Add the edit button
$html .= '<p>';
$html .= '<input class = "button" type="button" name="edit" value="Edit" onclick="open_win(\''.$edit_location.'\')"/>';
$html .='<input class = "button" style="width:200px" type="button" name="return" value="Return To Accounts" onclick="open_win(\'list_account_balances.php\')"/>';
$html .= '</p>';
echo $html;
include (FOOTER_FILE);

?>

