<?php
/*
	list_accounts.php
	craig Iannazzi 4-23-12
*/
$binder_name = 'Chart Of Accounts';
$access_type = 'READ';
$page_title = 'Chart of Accounts';
require_once ('../accounting_functions.php');



//if there is a message print it
$html = printGetMessage('message');
//here is the query that the search and table arrays are built off of.
$tmp_sql = "
CREATE TEMPORARY TABLE COA
SELECT pos_chart_of_accounts.account_sub_type, pos_chart_of_accounts.pos_chart_of_accounts_id, pos_chart_of_accounts.account_name, pos_chart_of_accounts.account_number, pos_chart_of_accounts.active, pos_chart_of_accounts.comments, pos_chart_of_account_types.account_type_name, pos_chart_of_account_types.pos_chart_of_account_type_id, pos_chart_of_accounts_required.required_account_name
FROM pos_chart_of_accounts
LEFT JOIN pos_chart_of_account_types
ON pos_chart_of_accounts.pos_chart_of_account_type_id = pos_chart_of_account_types.pos_chart_of_account_type_id
LEFT JOIN pos_chart_of_accounts_required
ON pos_chart_of_accounts.pos_chart_of_accounts_required_id = pos_chart_of_accounts_required.pos_chart_of_accounts_required_id
;
";
$tmp_select_sql = "SELECT * FROM COA WHERE 1";
//define the search table
$search_fields = array(				array(	'db_field' => 'account_name',
											'mysql_search_result' => 'account_name',
											'caption' => 'Account Name',	
											'type' => 'input',
											'html' => createSearchInput('account_name')
										),
										array(	'db_field' => 'account_number',
											'mysql_search_result' => 'account_number',
											'caption' => 'Account Number',	
											'type' => 'input',
											'html' => createSearchInput('account_number')),
											array(	'db_field' => 'pos_chart_of_account_type_id',
											'mysql_search_result' => 'pos_chart_of_account_type_id',
											'caption' => 'Chart Of Account Type',	
											'type' => 'select',
											'html' => createChartOfAccountTypeSelect('pos_chart_of_account_type_id', valueFromGetOrDefault('pos_chart_of_account_type_id'), 'all'))
										
										);
$table_columns = array(
		/*array(
			'th' => 'View',
			'mysql_field' => 'pos_chart_of_accounts_id',
			'get_url_link' => "list_chart_of_account_activity.php",
			'url_caption' => 'View',
			'get_id_link' => 'pos_chart_of_accounts_id'),*/
		/*array(
			'th' => 'General Ledger<br>Posts',
			'mysql_field' => 'pos_chart_of_accounts_id',
			'get_url_link' => "list_general_ledger_chart_of_account_activity.php",
			'url_caption' => 'View GL',
			'get_id_link' => 'pos_chart_of_accounts_id'),*/
		array(
			'th' => 'View',
			'mysql_field' => 'pos_chart_of_accounts_id',
			'get_url_link' => "chart_of_accounts.php?type=view",
			'url_caption' => 'View',
			'get_id_link' => 'pos_chart_of_accounts_id'),
		/*array(
			'th' => 'System <br>ID',
			'mysql_field' => 'pos_chart_of_accounts_id',
			'sort' => 'pos_chart_of_accounts_id'),*/
		array(
			'th' => 'Account Name',
			'mysql_field' => 'account_name',
			'sort' => 'account_name'),
		array(
			'th' => 'Account Number',
			'mysql_field' => 'account_number',
			'sort' => 'account_number'),
		array('th' => 'Chart Of Account Type',
		'mysql_field' =>  'account_type_name',
				'sort' => 'account_type_name'	),
		array('th' => 'Chart Of Account Sub-Type',
		'mysql_field' =>  'account_sub_type',
				'sort' => 'account_sub_type'	),
		/*array('th' => 'Required Account Name',
		'mysql_field' =>  'required_account_name',
				'sort' => 'required_account_name'	),*/
		array('th' => 'Active',
				'mysql_field' =>  'active',
				'sort' => 'active'				),
		array('th' => 'Comments',
		'mysql_field' => 'comments')
		);

$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saved_coa');


//Add a button to add an account
$html .= '<p>';
$html .= '<input class = "button" type="button" name="add_account" style="width:200px" value="Add Account To Chart Of Accounts" onclick="open_win(\'chart_of_accounts.php?type=add\')"/>';
//$html .= '<input class = "button" type="button" name="bulk_upload_accounts" style="width:300px" value="Bulk Upload Accounts To Chart Of Accounts" onclick="open_win(\'bulk_chart_of_account.php\')"/>';
$html .= '</p>';

//create the search form

$action = 'list_chart_of_accounts.php';
$html .= createSearchForm($search_fields,$action);
//Create Search String (AND's after MYSQL WHERE from $_GET data)
$search_sql = createSearchSQLStringMultipleDates($search_fields);
$tmp_select_sql  .= $search_sql;
//Create the order sting to append to the sql statement
$order_by = createSortSQLString($table_columns, $table_columns[2]['mysql_field'], 'ASC');
$tmp_select_sql  .=  " ORDER BY $order_by";

if (isset($_GET['search']))
{
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,$tmp_sql);
	$data = getTransactionSQL($dbc,$tmp_select_sql);
	closeDB($dbc);
	//now make the table
	$html .= createRecordsTable($data, $table_columns);
}



include (HEADER_FILE);

echo $html;

include (FOOTER_FILE);
?>
