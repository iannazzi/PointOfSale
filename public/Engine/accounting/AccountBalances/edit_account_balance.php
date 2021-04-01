<?php 

/*

	Craig Iannazzi 4-23-12
	
*/
$binder_name = 'Accounts';
$access_type = 'WRITE';
$page_title = 'Account Balance';
require_once ('../accounting_functions.php');
$db_table = 'pos_account_balances';

$key_val_id['pos_account_balance_id'] = getPostOrGetID('pos_account_balance_id');
$complete_location = 'list_account_balances.php';
$cancel_location = 'list_account_balances.php';
$complete_location = 'view_balance.php?pos_account_balance_id='.$key_val_id['pos_account_balance_id'];
$cancel_location = 'view_balance.php?pos_account_balance_id='.$key_val_id['pos_account_balance_id'];

$account_table_def = createAccountBalanceTableDef('Edit', $key_val_id);
$data_table_def_with_data = selectSingleTableDataFromID($db_table, $key_val_id, $account_table_def);
$big_html_table = createHTMLTableForMYSQLInsert($data_table_def_with_data);
include (HEADER_FILE);
$html = printGetMessage();
$html .= '<p>Account Balance Details</p>';
$form_handler = 'edit_account_balance.form.handler.php';
$html = createFormForMYSQLInsert($account_table_def ,$big_html_table, $form_handler, $complete_location, $cancel_location);
			
echo $html;
include (FOOTER_FILE);

?>

