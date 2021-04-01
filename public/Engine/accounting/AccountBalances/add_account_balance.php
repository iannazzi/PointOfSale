<?php 

/*

	Craig Iannazzi 4-23-12
	
*/
$binder_name = 'Accounts';
$access_type = 'WRITE';
$page_title = 'Accounts';
require_once ('../accounting_functions.php');

$db_table = 'pos_account_balances';
$complete_location = 'list_account_balances.php';
$cancel_location = 'list_account_balances.php';

$account_balance_table_def = createAccountBalanceTableDef('New', 'false');
$big_html_table = createHTMLTableForMYSQLInsert($account_balance_table_def);
	
//$big_html_table .=  '<div class = "mysql_table_divider">';	
//$big_html_table .= createHTMLTableForMYSQLInsert($interest_data_table_def);	
//$big_html_table .=  '</div>';								
//$table_def_for_post = convertArrayTableDefToPostTableDef($table_def);	


$form_handler = 'add_account_balance.form.handler.php';
$html = createFormForMYSQLInsert($account_balance_table_def ,$big_html_table, $form_handler, $complete_location, $cancel_location);
include (HEADER_FILE);								
echo $html;
include (FOOTER_FILE);

?>

