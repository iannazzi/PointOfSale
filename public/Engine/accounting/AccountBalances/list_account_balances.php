<?php
/*
	list_accounts.php
	craig Iannazzi 4-23-12
*/
$binder_name = 'Accounts';
$access_type = 'READ';
$page_title = 'Account Balances';
require_once ('../accounting_functions.php');

include (HEADER_FILE);
//if there is a message print it
$html = printGetMessage('message');
//here is the query that the search and table arrays are built off of.
$sql = "

SELECT pos_accounts.account_number, pos_accounts.company, pos_account_balances.pos_account_balance_id, pos_account_balances.pos_chart_of_accounts_id,pos_account_balances.balance_date, pos_account_balances.balance_amount, pos_account_balances.comments, pos_chart_of_accounts.account_name, pos_chart_of_accounts.account_number as act_nmb
FROM pos_account_balances
LEFT JOIN pos_accounts
ON pos_account_balances.pos_account_id = pos_accounts.pos_account_id
LEFT JOIN pos_chart_of_accounts
ON pos_account_balances.pos_chart_of_accounts_id = pos_chart_of_accounts.pos_chart_of_accounts_id
WHERE 1

";
	
//define the search table
$search_fields = array(				array(	'db_field' => 'company',
											'mysql_search_result' => 'pos_accounts.company',
											'caption' => 'Company',	
											'type' => 'input',
											'html' => createSearchInput('company')
										),
										array(	'db_field' => 'act_nmb',
											'mysql_search_result' => 'pos_chart_of_accounts.account_number',
											'caption' => 'Chart of Accounts<br>Account Number',	
											'type' => 'input',
											'html' => createSearchInput('act_nmb')),
										array(	'db_field' => 'account_name',
											'mysql_search_result' => 'pos_chart_of_accounts.account_name',
											'caption' => 'Chart Of Accounts <br>Account Name',	
											'type' => 'input',
											'html' => createSearchInput('account_name'))
										);
$table_columns = array(
		array(
			'th' => 'View',
			'mysql_field' => 'pos_account_balance_id',
			'get_url_link' => "view_balance.php",
			'url_caption' => 'View',
			'get_id_link' => 'pos_account_balance_id'),
		array(
			'th' => 'Company',
			'mysql_field' => 'company',
			'sort' => 'pos_accounts.company'),
		array(
			'th' => 'Account Number',
			'mysql_field' => 'account_number',
			'encrypted' => 1,
			'sort' => 'pos_accounts.account_number'),
		array(
			'th' => 'Chart Of Accounts Number',
			'mysql_field' => 'act_nmb',
			'sort' => 'act_nmb'),
		array(
			'th' => 'Chart Of Accounts Name',
			'mysql_field' => 'account_name',
			'sort' => 'pos_chart_of_accounts.account_name'),
		array(
			'th' => 'Balance Date',
			'mysql_field' => 'balance_date',
			'sort' => 'pos_account_balances.balance_date'),
		array(
			'th' => 'Balance Amount',
			'mysql_field' => 'balance_amount',
			'sort' => 'pos_account_balances.balance_amount'),
		array(
			'th' => 'Comments',
			'mysql_field' => 'comments',
			'sort' => 'pos_account_balances.comments')
			
			);

//Add a button to add an expense
$html .= '<p>';
$html .= '<input class = "button" style="width:200px;" type="button" name="add_account" value="Add Account Balance" onclick="open_win(\'add_account_balance.php\')"/>';

$html .= '</p>';

//create the search form

$action = 'list_account_balances.php';
//$html .= createSearchForm($search_fields,$action);
//Create Search String (AND's after MYSQL WHERE from $_GET data)
$search_sql = createSearchSQLStringMultipleDates($search_fields);
$sql  .= $search_sql;
//Create the order sting to append to the sql statement
$order_by = createSortSQLString($table_columns, $table_columns[1]['mysql_field'], 'ASC');
$sql  .=  " ORDER BY $order_by";
//now make the table
$data = getSQL($sql);
$html .= createRecordsTable($data, $table_columns);
echo $html;

include (FOOTER_FILE);
?>
