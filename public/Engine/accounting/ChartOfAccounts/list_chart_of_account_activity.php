<?php
/*
	list_accounts.php
	craig Iannazzi 4-23-12
*/
$binder_name = 'Chart Of Accounts';
$access_type = 'READ';
$page_title = 'Account Activity';
require_once ('../accounting_functions.php');
$key_val_id['pos_chart_of_accounts_id'] = getPostOrGetID('pos_chart_of_accounts_id');
$pos_chart_of_accounts_id = $key_val_id['pos_chart_of_accounts_id'];
include (HEADER_FILE);
//if there is a message print it
$html = printGetMessage('message');
//here is the query that the search and table arrays are built off of.
$sql = "


SELECT pos_purchases_journal.pos_purchases_journal_id as cur_id,pos_purchases_journal.invoice_date as cdate, pos_purchases_journal.pos_account_id, pos_accounts.company, pos_accounts.account_number,  'PURCHASES JOURNAL' as source,
(SELECT pos_purchases_journal.invoice_amount  FROM pos_purchases_journal WHERE pos_purchases_journal.invoice_type = 'Credit Memo' AND pos_purchases_journal.pos_purchases_journal_id = cur_id) as debit, 
(SELECT pos_purchases_journal.invoice_amount  FROM pos_purchases_journal WHERE pos_purchases_journal.invoice_type = 'Regular' AND pos_purchases_journal.pos_purchases_journal_id = cur_id) as credit,
NULL as balance
FROM pos_purchases_journal 
LEFT JOIN pos_accounts
ON pos_accounts.pos_account_id = pos_purchases_journal.pos_account_id
LEFT JOIN pos_chart_of_accounts
ON pos_chart_of_accounts.pos_chart_of_accounts_id = pos_accounts.parent_pos_chart_of_accounts_id
WHERE pos_chart_of_accounts_id = $pos_chart_of_accounts_id   

";




//define the search table
$search_fields = array(				array(	'db_field' => 'cdate',
											'mysql_search_result' => 'pos_purchases_journal.invoice_date',
											'caption' => 'Date',	
											'type' => 'input',
											'html' => createSearchInput('cdate')
										),
										array(	'db_field' => 'company',
											'mysql_search_result' => 'pos_accounts.company',
											'caption' => 'Account Name',	
											'type' => 'input',
											'html' => createSearchInput('company')),
										array(	'db_field' => 'account_number',
											'mysql_search_result' => 'pos_accounts.account_number',
											'caption' => 'Account Number',	
											'type' => 'input',
											'html' => createSearchInput('account_number'))
										/* not sure how to search 'source'
										array(	'db_field' => 'source',
											'mysql_search_result' => 'source',
											'caption' => 'Source',	
											'type' => 'input',
											'html' => createSearchInput('source')),
										array(	'db_field' => 'debit',
											'mysql_search_result' => "(SELECT pos_purchases_journal.invoice_amount  FROM pos_purchases_journal WHERE pos_purchases_journal.invoice_type = 'Credit Memo' AND pos_purchases_journal.pos_purchases_journal_id = pos_purchases_journal.pos_purchases_journal_id)",
											'caption' => 'Debit',	
											'type' => 'input',
											'html' => createSearchInput('debit')),
										array(	'db_field' => 'credit',
											'mysql_search_result' => "(SELECT pos_purchases_journal.invoice_amount  FROM pos_purchases_journal WHERE pos_purchases_journal.invoice_type = 'Regular' AND pos_purchases_journal.pos_purchases_journal_id = pos_purchases_journal.pos_purchases_journal_id)",
											'caption' => 'Credit',	
											'type' => 'input',
											'html' => createSearchInput('credit'))
										array(	'db_field' => 'balance',
											'mysql_search_result' => 'balance',
											'caption' => 'Balance',	
											'type' => 'input',
											'html' => createSearchInput('balance'))*/
										);
$table_columns = array(
		array(
			'th' => 'Date',
			'mysql_field' => 'cdate',
			'sort' => 'cdate'),
		array(
			'th' => 'Account Name',
			'mysql_field' => 'company',
			'sort' => 'company'),
		array(
			'th' => 'Account Number',
			'mysql_field' => 'account_number',
			'sort' => 'account_number'),
		array('th' => 'Source',
		'mysql_field' =>  'source',
				'sort' => 'source'	),
		array('th' => 'Source ID',
		'mysql_field' =>  'cur_id',
				'sort' => 'cur_id'	),
		array('th' => 'Debit',
				'mysql_field' =>  'debit',
				'total' =>0,
				'round' =>2,
				'sort' => 'debit'),
		array('th' => 'Credit',
				'mysql_field' =>  'credit',
				'total' =>0,
				'round' =>2,
				'sort' => 'credit'),
		array('th' => 'Balance',
				'mysql_field' =>  'balance',
				'sort' => 'balance')
		);

//Add a button to add an account
$html .= '<p>';
$html .= 'Chart Of Accounts ' . getChartOfAccountNumber($pos_chart_of_accounts_id) . ' ' . getChartOfAccountName($pos_chart_of_accounts_id) .' Activity';
$html .= '</p>';

//create the search form
$action = 'list_chart_of_account_activity.php';
$html .= createSearchFormWithID($search_fields,$action, $key_val_id);
//Create Search String (AND's after MYSQL WHERE from $_GET data)
$search_sql = createSearchSQLStringMultipleDates($search_fields);
$sql  .= $search_sql;
//Create the order sting to append to the sql statement
$order_by = createSortSQLString($table_columns, $table_columns[3]['mysql_field'], 'ASC');
$sql  .=  " ORDER BY $order_by";

//now make the table
$html .= createRecordsTableWithTotals(getSQL($sql), $table_columns);
echo $html;

include (FOOTER_FILE);
?>
