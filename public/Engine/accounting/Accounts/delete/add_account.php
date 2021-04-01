<?php 

/*

	Craig Iannazzi 4-23-12
	
*/
$binder_name = 'Accounts';
$access_type = 'WRITE';
$page_title = 'Accounts';
require_once ('../accounting_functions.php');

$db_table = 'pos_accounts';
$complete_location = 'list_accounts.php';
$cancel_location = 'list_accounts.php';

$account_table_def = createAccountTableDef('New', 'false');
/*$multi_select=	array(	array('db_field' => 'pos_chart_of_accounts_id',
								'type' => 'select',
								'caption' => 'Select the Accounts that this account is associated with<br><br>Use Control, Shift, and/or <br>Command To Select Multiple',
								'html' => createChartOfAccountSelect('pos_chart_of_accounts_id[]', 'false', 'off', ' multiple size="15" onchange="needToConfirm=true" '),
								'validate' => array('multi_select_value' => 'false')));*/
	$multi_select=	getAccountMultiSelectTableDef('false');					
//$table_def = array($account_data_table_def, $interest_data_table_def);						
$big_html_table = createHTMLTableForMYSQLInsert($account_table_def);
$big_html_table .= createHTMLTableForMYSQLInsert($multi_select);	
//$big_html_table .=  '<div class = "mysql_table_divider">';	
//$big_html_table .= createHTMLTableForMYSQLInsert($interest_data_table_def);	
//$big_html_table .=  '</div>';								
//$table_def_for_post = convertArrayTableDefToPostTableDef($table_def);	

include (HEADER_FILE);
$form_handler = 'add_account.form.handler.php';
$html = createFormForMYSQLInsert($account_table_def ,$big_html_table, $form_handler, $complete_location, $cancel_location);
								
echo $html;
include (FOOTER_FILE);

?>

