<?php
/*
	invoice	description	DEBIT	CREDIT
	payment 1
	payment 2
	
*/
$binder_name = 'General Journal';
$access_type = 'READ';
$page_title = 'General Journal';
require_once ('../accounting_functions.php');

$html ='';
//if there is a message print it
$html = printGetMessage('message');
//here is the query that the search and table arrays are built off of.

//Want to limit the data to user level $user_level >= pos_users.level
$user_level = $_SESSION['level'];

//here is the query that the search and table arrays are built off of.

$tmp_sql = "
CREATE TEMPORARY TABLE general_journal
SELECT  pos_general_journal.pos_general_journal_id, 
		pos_general_journal.invoice_type, 
		pos_general_journal.entry_type,
		pos_general_journal.pos_user_id, 
		pos_general_journal.entry_amount, 
		pos_general_journal.pos_store_id,  
		pos_general_journal.invoice_date, 
		pos_general_journal.invoice_status, 
		pos_general_journal.payment_status, 
		pos_general_journal.invoice_number, 
		pos_general_journal.invoice_due_date, 
		pos_general_journal.description,  
		pos_general_journal.supplier, 
		pos_general_journal.pos_chart_of_accounts_id, 
		pos_general_journal.pos_account_id,   
		pos_general_journal.comments, 
		pos_general_journal.file_name,  
		pos_general_journal.payments_applied, 
		pos_employees.last_name, 
		pos_stores.store_name,
		pos_users.level,

		(SELECT pos_chart_of_accounts.account_name  
		FROM pos_chart_of_accounts 
		WHERE pos_chart_of_accounts.pos_chart_of_accounts_id = pos_general_journal.pos_chart_of_accounts_id) 
		AS debit_account_name, 
		
		(SELECT pos_chart_of_accounts.account_number 
		FROM pos_chart_of_accounts 
		WHERE pos_chart_of_accounts.pos_chart_of_accounts_id = pos_general_journal.pos_chart_of_accounts_id) 
		AS debit_account_number, 

	
		'tmp' AS credit_account_number,

(SELECT pos_accounts.company FROM pos_accounts WHERE pos_accounts.pos_account_id = pos_general_journal.pos_account_id) AS debit_subsidiary_account,

(SELECT GROUP_CONCAT(pos_payments_journal.pos_payments_journal_id) FROM pos_payments_journal
			LEFT JOIN pos_invoice_to_payment
			ON pos_payments_journal.pos_payments_journal_id = pos_invoice_to_payment.pos_payments_journal_id
			WHERE pos_invoice_to_payment.pos_journal_id = pos_general_journal.pos_general_journal_id
			AND pos_invoice_to_payment.source_journal = 'GENERAL JOURNAL') as pos_payments_journal_ids,
(SELECT GROUP_CONCAT(pos_payments_journal.pos_account_id) FROM pos_payments_journal
			LEFT JOIN pos_invoice_to_payment
			ON pos_payments_journal.pos_payments_journal_id = pos_invoice_to_payment.pos_payments_journal_id
			WHERE pos_invoice_to_payment.pos_journal_id = pos_general_journal.pos_general_journal_id
			AND pos_invoice_to_payment.source_journal = 'GENERAL JOURNAL') as payment_account_ids,		

(SELECT  GROUP_CONCAT(pos_chart_of_accounts.account_name) FROM pos_payments_journal
			LEFT JOIN pos_accounts
			ON pos_payments_journal.pos_account_id = pos_accounts.pos_account_id
			LEFT JOIN pos_chart_of_accounts
			ON pos_accounts.parent_pos_chart_of_accounts_id = pos_chart_of_accounts.pos_chart_of_accounts_id
			LEFT JOIN pos_invoice_to_payment
			ON pos_payments_journal.pos_payments_journal_id = pos_invoice_to_payment.pos_payments_journal_id
			WHERE pos_invoice_to_payment.pos_journal_id = pos_general_journal.pos_general_journal_id
			AND pos_invoice_to_payment.source_journal = 'GENERAL JOURNAL') as credit_account_name,	
			
(SELECT  GROUP_CONCAT(pos_accounts.company) FROM pos_payments_journal
			LEFT JOIN pos_accounts
			ON pos_payments_journal.pos_account_id = pos_accounts.pos_account_id
			LEFT JOIN pos_invoice_to_payment
			ON pos_payments_journal.pos_payments_journal_id = pos_invoice_to_payment.pos_payments_journal_id
			WHERE pos_invoice_to_payment.pos_journal_id = pos_general_journal.pos_general_journal_id
			AND pos_invoice_to_payment.source_journal = 'GENERAL JOURNAL') as credit_subsidiary_account,	
			


'tmp' as total_payments

FROM pos_general_journal
LEFT JOIN pos_employees
ON pos_general_journal.pos_employee_id = pos_employees.pos_employee_id
LEFT JOIN pos_stores
ON pos_general_journal.pos_store_id = pos_stores.pos_store_id
LEFT JOIN pos_users
ON pos_general_journal.pos_user_id = pos_users.pos_user_id	

WHERE pos_users.level <= '$user_level'  
	
	;";
$tmp_select_sql = "SELECT * FROM general_journal WHERE 1";
//define the search table
$search_fields = array(/*array(	'db_field' => 'pos_store_id',
											'mysql_search_result' => 'pos_store_id',
											'caption' => 'Store Name',	
											'type' => 'select',
											'html' => createStoreSelect('pos_store_id', getOrSessionStoreId(), 'on')
										),*/
							
							array(	'db_field' => 'pos_general_journal_id',
											'mysql_search_result' => 'pos_general_journal_id',
											'caption' => 'System<br>ID',
											'type' => 'input',
											'html' => createSearchInput('pos_general_journal_id')
										),
							array(	'db_field' => 'entry_type',
											'mysql_search_result' => 'entry_type',
											'caption' => 'Entry Type',
											'type' => 'input',
											'html' => createSearchInput('entry_type')
										),
							/* array(	'db_field' => 'last_name',
											'mysql_search_result' => 'last_name',
											'caption' => 'Last Name',
											'type' => 'input',
											'html' => createSearchInput('last_name')
										),*/
							
							
							array(	'db_field' => 'supplier',
											'mysql_search_result' => 'supplier',
											'caption' => 'Supplier',
											'type' => 'input',
											'html' => createSearchInput('supplier')
										),
							array(	'db_field' => 'description',
											'mysql_search_result' => 'description',
											'caption' => 'Description',
											'type' => 'input',
											'html' => createSearchInput('description')
										),
							array(	'db_field' => 'invoice_date',
											'mysql_search_result' => 'invoice_date',
											'caption' => 'Start Date',
											'type' => 'start_date',
											'html' => dateSelect('invoice_date_start_date',valueFromGetOrDefault('invoice_date_start_date'))
										),
							array(	'db_field' => 'invoice_date',
											'mysql_search_result' => 'invoice_date',
											'caption' => 'End Date',	
											'type' => 'end_date',
											'html' => dateSelect('invoice_date_end_date',valueFromGetOrDefault('invoice_date_end_date'))
										),
							array(	'db_field' => 'pos_chart_of_accounts_id',
											'mysql_search_result' => 'pos_chart_of_accounts_id',
											'caption' => 'Chart Of Account',	
											'type' => 'select',
											'html' => createChartOfAccountSelect('pos_chart_of_accounts_id', valueFromGetOrDefault('pos_chart_of_accounts_id'), 'all')),
							array(	'db_field' => 'entry_amount',
											'mysql_search_result' => 'entry_amount',
											'caption' => 'Amount',	
											'type' => 'input',
											'html' => createSearchInput('entry_amount')
										));
$view_expense_table_columns = array(
		 array(
			'th' => 'View',
			'mysql_field' => 'pos_general_journal_id',
			'get_url_link' => "view_general_journal_entry.php",
			'url_caption' => 'View',
			'get_id_link' => 'pos_general_journal_id'),
		array(
			'th' => 'Journal<br>ID',
			'mysql_field' => 'pos_general_journal_id',
			'mysql_table' => 'pos_general_journal_id',
			'sort' => 'pos_general_journal_id'),
		/*array(
			'th' => 'Store Name',
			'mysql_field' => 'store_name',
			'mysql_table' => 'pos_stores',
			'sort' => 'store_name'),
		array(
			'th' => 'Last Name',
			'mysql_field' => 'last_name',
			'mysql_table' => 'pos_employees',
			'sort' => 'last_name'),*/
		array(
			'th' => 'Entry<br>Type',
			'mysql_field' => 'entry_type',
			'mysql_table' => 'pos_general_journal',
			'sort' => 'entry_type'),
		array(
			'th' => 'Status',
			'mysql_field' => 'invoice_status',
			'sort' => 'invoice_status'),
	array(
			'th' => 'Invoice Number',
			'mysql_field' => 'invoice_number',
			'sort' => 'invoice_number'),
		array(
			'th' => 'Invoice<br>Date',
			'mysql_field' => 'invoice_date',
			'mysql_table' => 'pos_general_journal',
			'sort' => 'invoice_date'),
		
		array(
			'th' => 'Due <br>Date',
			'mysql_field' => 'invoice_due_date',
			'mysql_table' => 'pos_general_journal',
			'sort' => 'invoice_due_date'),
		array(
			'th' => 'Supplier',
			'mysql_field' => 'supplier',
			'mysql_table' => 'pos_general_journal',
			'sort' => 'supplier'),
		array(
			'th' => 'Description',
			'mysql_field' => 'description',
			'mysql_table' => 'pos_general_journal',
			'sort' => 'description'),
		/*array(
			'th' => 'Debit <br> (Should Have)<br>(Left)<br> Chart of Accounts Number',
			'mysql_field' => 'debit_account_number',
			'sort' => 'debit_account_number'),*/
		array(
			'th' => 'Debit <br> (Should Have)<br>(Left)<br>Chart of Accounts Name',
			'mysql_field' => 'debit_account_name',
			'sort' => 'debit_account_name'),	
		array(
			'th' => 'Debit <br> (Should Have)<br>(Left)<br>Subsidiary Account Name',
			'mysql_field' => 'debit_subsidiary_account',
			'sort' => 'debit_subsidiary_account'),	
		/*array(
			'th' => 'Credit <br> (Should Give)<br>(Right)<br>Chart of Accounts Number',
			'mysql_field' => 'credit_account_number',
			'sort' => 'credit_account_number'),*/
		array(
			'th' => 'Credit <br> (Should Give)<br>(Right)<br> Chart of Accounts Name',
			'mysql_field' => 'credit_account_name',
			'sort' => 'credit_account_name'),	
		array(
			'th' => 'Credit <br> (Should Give)<br>(Right)<br>Subsidiary Account Name',
			'mysql_field' => 'credit_subsidiary_account',
			'sort' => 'credit_subsidiary_account'),
		array(
			'th' => 'Entry Amount',
			'mysql_field' => 'entry_amount',
			'mysql_table' => 'pos_general_journal',
			'sort' => 'pos_general_journal.entry_amount',
			'round' => '2',
			'total' => 0),
		array(
			'th' => 'Payment Status',
			'mysql_field' => 'payment_status',
			'sort' => 'payment_status'),
		/*array(
			'th' => 'Payment<br>ID',
			'mysql_field' => 'pos_payments_journal_ids',
			)*/
			 array(
			'th' => 'Payment id(s)',
			'mysql_field' => 'pos_payments_journal_ids',
			'get_url_link' => POS_ENGINE_URL . "/accounting/PaymentsJournal/view_payments_journal_entry.php",
			/*'url_caption' => 'View',*/
			'get_id_link' => 'pos_payments_journal_id'),
			
		/*array(
			'th' => 'Payment <br>Date',
			'mysql_field' => 'payment_date',
			'mysql_table' => 'pos_payments_journal',
			'sort' => 'pos_payments_journal.payment_date'),
	
		array(
			'th' => 'Comments',
			'mysql_table' => 'pos_general_journal',
			'mysql_field' => 'comments')*/
			
			);

$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saved_general_journal_url', 'invoice_date_start_date');

//Add a button to add an expense
$html .= '<p>';

//change this to 	ADD => receipt, invoice, transfer, statement
//					PAY => 
$html .= '<input class = "button" type="button" style = "width:250px" name="add_general_journal_entry" value="Add Simple Expense Receipt (non-account)" onclick="open_win(\'add_expense_receipt.php\')"/>';
$html .= '<input class = "button" type="button" style = "width:200px" name="add_expense_invoice" value="Add Expense Invoice (on account)" onclick="open_win(\'select_expense_account.php?action=add\')"/>';
$html .= '<input class = "button" type="button" style = "width:250px" name="add_expense_invoice" value="Add Expense Invoice And Payment" onclick="open_win(\'select_expense_account.php?action=add_plus\')"/>';
$html .= '<input class = "button" type="button" style = "width:200px" name="pay_account" value="Pay Account or Transfer $$" onclick="open_win(\'../PaymentsJournal/select_account.php?action=pay_account\')"/>';


//$html .= '<input class = "button" type="button" style = "width:200px" name="pay_expense_invoices" value="Pay Expense Invoice(s)" onclick="open_win(\'../GeneralJournal/select_expense_account.php?action=pay\')"/>';
//$html .= '<input class = "button" type="button" style = "width:200px" name="add_account_balance" value="Transfer Funds" onclick="open_win(\'add_balance_transfer.php\')"/>';
//$html .= '<input class = "button" type="button" style = "width:200px" name="add_statement" value="Add Statement Due" onclick="open_win(\'../GeneralJournal/select_expense_account.php?action=statement\')"/>';
//$html .= '<input class = "button" type="button" style = "width:200px" name="add_statement" value="Pay Statement" onclick="open_win(\'../GeneralJournal/select_expense_account.php?action=pay_statement\')"/>';
$html .='</p>';
$html .= createUserButton('Payments Journal');


//create the search form

$action = 'list_general_journal.php';
$html .= createSearchForm($search_fields,$action);

//Create Search String (AND's after MYSQL WHERE from $_GET data)
$search_sql = createSearchSQLStringMultipleDates($search_fields);
$tmp_select_sql  .= $search_sql;
//Create the order sting to append to the sql statement
$order_by = createSortSQLString($view_expense_table_columns, $view_expense_table_columns[0]['mysql_field']);
$tmp_select_sql .= " ORDER BY $order_by";
//get the data
if (isset($_GET['search']))
{
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,$tmp_sql);
	$data = getTransactionSQL($dbc,$tmp_select_sql);
	closeDB($dbc);
	//now make the table
	$html .= createRecordsTableWithTotals($data, $view_expense_table_columns);
}
$html .= '<script>document.getElementsByName("supplier")[0].focus();</script>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
//older sql gives two entries for double payment
$sql = "

SELECT  pos_general_journal.pos_general_journal_id, pos_general_journal.invoice_type, pos_general_journal.entry_type,pos_general_journal.pos_user_id, pos_general_journal.entry_amount, pos_general_journal.pos_store_id, pos_general_journal.pos_user_id,  pos_general_journal.invoice_number, pos_general_journal.invoice_date, pos_general_journal.invoice_status, pos_general_journal.invoice_due_date, pos_general_journal.description,  pos_general_journal.supplier, pos_general_journal.pos_chart_of_accounts_id, pos_general_journal.pos_account_id,   pos_general_journal.comments, pos_general_journal.file_name,  pos_employees.last_name, pos_stores.store_name,pos_payments_journal.payment_date,pos_payments_journal.pos_payments_journal_id,
(SELECT pos_chart_of_accounts.account_name  FROM pos_chart_of_accounts WHERE pos_chart_of_accounts.pos_chart_of_accounts_id = pos_general_journal.pos_chart_of_accounts_id) as debit_account_name, 
(SELECT pos_chart_of_accounts.account_number FROM pos_chart_of_accounts WHERE pos_chart_of_accounts.pos_chart_of_accounts_id = pos_general_journal.pos_chart_of_accounts_id) AS debit_account_number, 



(SELECT pos_chart_of_accounts.account_name FROM pos_chart_of_accounts WHERE pos_chart_of_accounts.pos_chart_of_accounts_id = (SELECT pos_accounts.parent_pos_chart_of_accounts_id FROM pos_accounts WHERE pos_accounts.pos_account_id = pos_payments_journal.pos_account_id) ) AS credit_account_name,

(SELECT pos_chart_of_accounts.account_number FROM pos_chart_of_accounts WHERE pos_chart_of_accounts.pos_chart_of_accounts_id = (SELECT pos_accounts.parent_pos_chart_of_accounts_id FROM pos_accounts WHERE pos_accounts.pos_account_id = pos_payments_journal.pos_account_id) ) AS credit_account_number,


(SELECT pos_accounts.company FROM pos_accounts WHERE pos_accounts.pos_account_id = pos_payments_journal.pos_account_id) AS credit_subsidiary_account,
(SELECT pos_accounts.company FROM pos_accounts WHERE pos_accounts.pos_account_id = pos_general_journal.pos_account_id) AS debit_subsidiary_account,
pos_users.level,
pos_payments_journal.pos_account_id, 

(SELECT sum(pos_payments_journal.payment_amount) FROM pos_payments_journal 
LEFT JOIN pos_invoice_to_payment
ON pos_invoice_to_payment.pos_payments_journal_id = pos_payments_journal.pos_payments_journal_id

WHERE pos_invoice_to_payment.pos_journal_id = pos_general_journal.pos_general_journal_id) as total_payments

FROM pos_general_journal
LEFT JOIN pos_employees
ON pos_general_journal.pos_employee_id = pos_employees.pos_employee_id
LEFT JOIN pos_stores
ON pos_general_journal.pos_store_id = pos_stores.pos_store_id
LEFT JOIN pos_users
ON pos_general_journal.pos_user_id = pos_users.pos_user_id	
LEFT JOIN pos_invoice_to_payment
ON pos_invoice_to_payment.pos_journal_id = pos_general_journal.pos_general_journal_id
LEFT JOIN pos_payments_journal
ON pos_invoice_to_payment.pos_payments_journal_id = pos_payments_journal.pos_payments_journal_id
WHERE pos_users.level <= '$user_level'  
	
	";
?>
