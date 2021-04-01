<?php
/*
	invoice	description	DEBIT	CREDIT
	payment 1
	payment 2
	
*/
$page_title = 'Bills Due';
require_once ('../accounting_functions.php');


$html ='';
//if there is a message print it
$html = printGetMessage('message');
//here is the query that the search and table arrays are built off of.

//Want to limit the data to user level $user_level >= pos_users.level
$user_level = $_SESSION['level'];

//define the search table
$search_fields = array(array(	'db_field' => 'id',
											'mysql_search_result' => 'id',
											'caption' => 'ID',	
											'type' => 'input',
											'html' => createSearchInput('id')
										),
							array(	'db_field' => 'journal',
											'mysql_search_result' => 'journal',
											'caption' => 'Journal',
											'type' => 'input',
											'html' => createSearchInput('journal')
										),
							 array(	'db_field' => 'company',
											'mysql_search_result' => 'company',
											'caption' => 'Company',
											'type' => 'input',
											'html' => createSearchInput('company')
										),
							array(	'db_field' => 'amount_due',
											'mysql_search_result' => 'amount_due',
											'caption' => 'Amount_due',
											'type' => 'input',
											'html' => createSearchInput('supplier')
										),
							array(	'db_field' => 'minimum_amount_due',
											'mysql_search_result' => 'minimum_amount_due',
											'caption' => 'minimum_amount_due',
											'type' => 'input',
											'html' => createSearchInput('minimum_amount_due')
										),
							array(	'db_field' => 'due_date',
											'mysql_search_result' => 'due_date',
											'caption' => 'Start Date',
											'type' => 'start_date',
											'html' => dateSelect('due_date_start_date',valueFromGetOrDefault('due_date_start_date'))
										),
							array(	'db_field' => 'due_date',
											'mysql_search_result' => 'due_date',
											'caption' => 'End Date',	
											'type' => 'end_date',
											'html' => dateSelect('due_date_end_date',valueFromGetOrDefault('due_date_end_date'))
										)
						);
						
//need to know view location and pay location....different based on journal
$table_def = array(
		 array(
			'th' => 'View',
			'mysql_field' => 'id',
			'variable_get_url_link' => array(
				'row_result_lookup' => 'journal',
				"PURCHASES JOURNAL" => array(
							'url' => POS_ENGINE_URL.'/accounting/PurchaseJournal/view_purchase_invoice_to_journal.php', 
							'get_data' => array('pos_purchases_journal_id'=>'id')),				 
				"GENERAL JOURNAL" =>  array(
							'url' => POS_ENGINE_URL.'/accounting/GeneralJournal/view_general_journal_entry.php',
							'get_data' => array('pos_general_journal_id' => 'id'))),
			'url_caption' => 'View',),
		 array(
			'th' => 'Pay',
			'mysql_field' => 'id',
			'variable_get_url_link' => array(
				'row_result_lookup' => 'journal',
				"PURCHASES JOURNAL" => array(
							'url' => POS_ENGINE_URL.'/accounting/PaymentsJournal/pay_purchases_invoices.php', 
							'get_data' => array('pos_purchases_journal_id'=>'id', 'pos_account_id' => 'account_id')),				 
				"GENERAL JOURNAL" =>  array(
							'url' => POS_ENGINE_URL.'/accounting/PaymentsJournal/pay_expense_invoices.php',
							'get_data' => array('pos_general_journal_id'=>'id', 'pos_account_id' => 'account_id'))),
			'url_caption' => 'Pay',
			),
		array(
			'th' => 'Journal',
			'mysql_field' => 'journal',
			'sort' => 'journal'),
		array(
			'th' => 'Company',
			'mysql_field' => 'company',
			'mysql_table' => 'company',
			'sort' => 'company'),
		array(
			'th' => 'Due Date',
			'mysql_field' => 'due_date',
			'mysql_table' => 'due_date',
			'sort' => 'due_date'),
		array(
			'th' => 'Amount Due',
			'mysql_field' => 'amount_due',
			'sort' => 'amount_due',
			'total' => 0,
			'round' =>2),
		array(
			'th' => 'Minimum <br>Amount Due',
			'mysql_field' => 'minimum_amount_due',
			'sort' => 'minimum_amount_due',
			'total' => 0,
			'round' =>2));


$general_journal_bills_due = "
			SELECT pos_general_journal.pos_general_journal_id as id,
			'GENERAL JOURNAL' as journal,
			pos_general_journal.invoice_due_date as due_date,
			pos_general_journal.entry_amount as amount_due,
			IF(pos_general_journal.minimum_amount_due = 0,  
				pos_general_journal.entry_amount, 
				pos_general_journal.minimum_amount_due) as minimum_amount_due,
			pos_accounts.company as company,
			pos_general_journal.pos_account_id as account_id
			FROM pos_general_journal 
			LEFT JOIN pos_accounts
			on pos_general_journal.pos_account_id = pos_accounts.pos_account_id
			WHERE pos_general_journal.invoice_status = 'UNPAID' 
			";
$purchases_journal_bills_due = "
			
			SELECT pos_purchases_journal.pos_purchases_journal_id as id, 
			'PURCHASES JOURNAL' as journal,
			pos_purchases_journal.invoice_due_date as due_date,
			pos_purchases_journal.invoice_amount - pos_purchases_journal.discount_applied as amount_due,
			pos_purchases_journal.invoice_amount - pos_purchases_journal.discount_applied as minimum_amount_due,
			pos_accounts.company as company,
			pos_purchases_journal.pos_account_id as account_id
			FROM pos_purchases_journal
			LEFT JOIN pos_accounts
			on pos_purchases_journal.pos_account_id = pos_accounts.pos_account_id
			WHERE pos_purchases_journal.payment_status = 'UNPAID'

	
	";
$sql_tmp = "CREATE TEMPORARY TABLE tmp " . $general_journal_bills_due . " UNION " . $purchases_journal_bills_due .';';
$sql =  "SELECT * FROM tmp WHERE 1";
//Create Search String (AND's after MYSQL WHERE from $_GET data)
$search_sql = createSearchSQLStringMultipleDates($search_fields);
$sql  = $sql  . $search_sql;
//Create the order sting to append to the sql statement
$sql .= " ORDER BY " .createSortSQLString($table_def,'due_date', 'ASC');
//get the data
$dbc = openPOSdb();
$result = runTransactionSQL($dbc,$sql_tmp);
$data = getTransactionSQL($dbc,$sql);
closeDB($dbc);



//create the search form

$action = 'list_bills_due.php';
$html .= createSearchForm($search_fields,$action);


//now make the table
$html .= createRecordsTableWithTotals($data, $table_def);
$html .= '<script>document.getElementsByName("supplier")[0].focus();</script>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);

?>
