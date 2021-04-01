<?php 

/*

	Craig Iannazzi 4-23-12
	
*/
$binder_name = 'Accounts';
$access_type = 'WRITE';
$page_title = 'Accounts';
require_once ('../accounting_functions.php');
$db_table = 'pos_accounts';

$key_val_id['pos_account_id'] = getPostOrGetID('pos_account_id');
$complete_location = 'list_accounts.php';
$cancel_location = 'list_accounts.php';
$complete_location = 'view_account.php?pos_account_id='.$key_val_id['pos_account_id'];
$cancel_location = 'view_account.php?pos_account_id='.$key_val_id['pos_account_id'];

/*$data_table_def = array(
						array( 'db_field' => 'pos_account_id',
								'type' => 'input',
								'tags' => 'readonly="readonly"'),
						array( 'db_field' => 'company',
								'type' => 'input',
								'validate' => 'none'),
						array( 'db_field' => 'account_number',
								'type' => 'input',
								'validate' => array('unique_group' => array('account_number', 'company'), 'min_length' => 1, 'id' => $id),
								'db_table' => $db_table),
						array('db_field' =>  'pos_account_type_id',
								'type' => 'select',
								'caption' => 'Type of Account',
								'html' => createAccountTypeSelect('pos_account_type_id', 'false'),
								'validate' => array('select_value' => 'false')),
						array('db_field' =>  'pos_chart_of_accounts_id',
								'type' => 'select',
								'caption' => "Default Chart of Account",
								'html' => createChartOfAccountSelect('pos_chart_of_accounts_id', 'false'),
								'validate' => 'none'),
						
						array('db_field' => 'primary_contact',
								'caption' => 'Primary Representative(s) (commas to separate names)',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'email',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' => 'phone',
								'type' => 'input',
								'tags' => 'maxlength = "40" ',
								'validate' => 'none'),
						array('db_field' =>  'fax',
								'type' => 'input',
								'tags' => 'maxlength = "40" ',
								'validate' => 'none'),
						array('db_field' => 'address1',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' => 'address2',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' => 'city',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' => 'state',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' => 'zip',
								'type' => 'input',
								'tags' => 'maxlength = "20" ',
								'validate' => 'none'),
						array('db_field' => 'country',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'autopay',
								'type' => 'checkbox',
								'validate' => 'none'),
						array('db_field' =>  'autopay_account_id',
								'type' => 'select',
								'html' => createAccountSelect('autopay_account_id', 'false'),
								'validate' => 'none'),
						array('db_field' =>  'terms',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'days',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'discount',
								'type' => 'input',
								'tags' => numbersOnly(),
								'validate' => 'none'),
						array( 'db_field' => 'interest_rate',
								'tags' => numbersOnly(),
								'type' => 'input',
								'validate' => 'number'),
						array('db_field' =>  'active',
								'type' => 'checkbox',
								'tags' => 'checked="checked" ',
								'validate' => 'none'),
						array('db_field' => 'comments',
								'type' => 'textarea',
								'validate' => 'none'));	*/
$account_table_def = createAccountTableDef('Edit', $key_val_id);
$data_table_def_with_data = selectSingleTableDataFromID($db_table, $key_val_id, $account_table_def);
$multi_select_table_def=	getAccountMultiSelectTableDef($key_val_id['pos_account_id']);

$big_html_table = createHTMLTableForMYSQLInsert($data_table_def_with_data);
$big_html_table .= createHTMLTableForMYSQLInsert($multi_select_table_def);
include (HEADER_FILE);
$html = printGetMessage();
$html .= '<p>Account Details</p>';
$form_handler = 'edit_account.form.handler.php';
//$html .= createTableForMYSQLInsert($data_table_def_with_data, $form_handler, $complete_location, $cancel_location);
$html = createFormForMYSQLInsert($account_table_def ,$big_html_table, $form_handler, $complete_location, $cancel_location);
			
echo $html;
include (FOOTER_FILE);

?>

