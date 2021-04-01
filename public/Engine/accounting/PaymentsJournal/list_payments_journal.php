<?php
$binder_name = 'Payments Journal';
$access_type = 'WRITE';
/*
	*shows a list of all registered manufacturers
*/
$page_title = 'Payments Journal';

require_once ('../accounting_functions.php');


//if there is a message print it
$html = printGetMessage('message');

//here is the query that the search and table arrays are built off of.
	
	$tmp_sql = "
CREATE TEMPORARY TABLE payments_journal
SELECT DISTINCT pos_payments_journal.pos_payments_journal_id, pos_payments_journal.pos_manufacturer_id, pos_manufacturers.company,pos_payments_journal.source_journal, pos_payments_journal.pos_employee_id, pos_accounts.company as act_name, pos_payments_journal.pos_account_id, pos_payments_journal.pos_payee_account_id, pos_payee_accounts.company as payee_name, pos_payments_journal.payment_date, pos_payments_journal.payment_amount, pos_payments_journal.payment_status, pos_payments_journal.applied_status,pos_payments_journal.reference_id, pos_payments_journal.file_name,
(SELECT
CASE pos_payments_journal.source_journal 
WHEN 'PURCHASES JOURNAL' THEN (SELECT pos_manufacturers.company FROM pos_manufacturers WHERE pos_manufacturers.pos_manufacturer_id = pos_payments_journal.pos_manufacturer_id)
WHEN 'GENERAL JOURNAL' THEN (SELECT pos_general_journal.supplier FROM pos_general_journal WHERE pos_general_journal.pos_general_journal_id = pos_invoice_to_payment.pos_journal_id)
ELSE '-' END) as supplier,

(select sum(applied_amount) from pos_invoice_to_payment WHERE pos_invoice_to_payment.pos_payments_journal_id = pos_payments_journal.pos_payments_journal_id) as applied_amount

FROM pos_payments_journal
LEFT JOIN pos_accounts
ON pos_payments_journal.pos_account_id = pos_accounts.pos_account_id
LEFT JOIN pos_accounts as pos_payee_accounts
ON pos_payments_journal.pos_payee_account_id = pos_payee_accounts.pos_account_id
LEFT JOIN pos_invoice_to_payment
ON pos_payments_journal.pos_payments_journal_id = pos_invoice_to_payment.pos_payments_journal_id
LEFT JOIN pos_general_journal
ON pos_general_journal.pos_general_journal_id = pos_invoice_to_payment.pos_journal_id
LEFT JOIN pos_purchases_journal
ON pos_purchases_journal.pos_purchases_journal_id = pos_invoice_to_payment.pos_journal_id
LEFT JOIN pos_manufacturers
ON pos_payments_journal.pos_manufacturer_id = pos_manufacturers.pos_manufacturer_id

;

";
$tmp_sql = "
CREATE TEMPORARY TABLE payments_journal
SELECT DISTINCT pos_payments_journal.pos_payments_journal_id,pos_payments_journal.source_journal, pos_payments_journal.pos_employee_id, pos_accounts.company as act_name, pos_payments_journal.pos_account_id, pos_payments_journal.pos_payee_account_id, pos_payee_accounts.company as payee_name, pos_payments_journal.payment_date, pos_payments_journal.payment_amount, pos_payments_journal.payment_status, pos_payments_journal.applied_status,pos_payments_journal.reference_id, pos_payments_journal.file_name,


(SELECT CASE pos_payments_journal.source_journal 

WHEN 'PURCHASES JOURNAL' THEN (SELECT GROUP_CONCAT(DISTINCT pos_manufacturers.company SEPARATOR ', ') FROM pos_purchases_journal LEFT JOIN pos_manufacturers ON pos_purchases_journal.pos_manufacturer_id = pos_manufacturers.pos_manufacturer_id LEFT JOIN pos_invoice_to_payment ON pos_purchases_journal.pos_purchases_journal_id = pos_invoice_to_payment.pos_journal_id WHERE pos_payments_journal.pos_payments_journal_id = pos_invoice_to_payment.pos_payments_journal_id)

WHEN 'GENERAL JOURNAL' THEN (SELECT pos_general_journal.supplier FROM pos_general_journal WHERE pos_general_journal.pos_general_journal_id = pos_invoice_to_payment.pos_journal_id)
ELSE '-' END) 

as supplier,

(select sum(applied_amount) from pos_invoice_to_payment WHERE pos_invoice_to_payment.pos_payments_journal_id = pos_payments_journal.pos_payments_journal_id) as applied_amount

FROM pos_payments_journal
LEFT JOIN pos_accounts
ON pos_payments_journal.pos_account_id = pos_accounts.pos_account_id
LEFT JOIN pos_accounts as pos_payee_accounts
ON pos_payments_journal.pos_payee_account_id = pos_payee_accounts.pos_account_id
LEFT JOIN pos_invoice_to_payment
ON pos_payments_journal.pos_payments_journal_id = pos_invoice_to_payment.pos_payments_journal_id
LEFT JOIN pos_general_journal
ON pos_general_journal.pos_general_journal_id = pos_invoice_to_payment.pos_journal_id
LEFT JOIN pos_purchases_journal
ON pos_purchases_journal.pos_purchases_journal_id = pos_invoice_to_payment.pos_journal_id


;

";


//define the search table
$search_fields = array(				
									array(	'db_field' => 'pos_payments_journal_id',
											'mysql_search_result' => 'pos_payments_journal_id',
											'caption' => 'Journal Id',	
											'type' => 'input',
											'html' => createSearchInput('pos_payments_journal_id')
										),
							array(	'db_field' => 'source_journal',
											'mysql_search_result' => 'source_journal',
											'caption' => 'Source Journal',	
											'type' => 'input',
											'html' => createSearchInput('source_journal')
										),
									array(	'db_field' => 'supplier',
											'mysql_search_result' => 'supplier',
											'caption' => 'Supplier',	
											'type' => 'input',
											'html' => createSearchInput('supplier')
										),
								array(	'db_field' => 'payee_name',
											'mysql_search_result' => 'payee_name',
											'caption' => 'Payee Account Name',	
											'type' => 'input',
											'html' => createSearchInput('payee_name')
									),
										array(	'db_field' => 'act_name',
											'mysql_search_result' => 'act_name',
											'caption' => 'Payment Account Name',	
											'type' => 'input',
											'html' => createSearchInput('act_name')
									),
								
								array(	'db_field' => 'payment_status',
											'mysql_search_result' => 'payment_status',
											'caption' => 'Payment Status',	
											'type' => 'input',
											'html' => createSearchInput('payment_status')
										),
								array(	'db_field' => 'payment_date',
											'mysql_search_result' => 'payment_date',
											'caption' => 'Payment Date Start',
											'type' => 'start_date',
											'html' => dateSelect('payment_date_start_date',valueFromGetOrDefault('payment_date_start_date'))
										),
								array(	'db_field' => 'payment_date',
											'mysql_search_result' => 'payment_date',
											'caption' => 'Payment Date End',	
											'type' => 'end_date',
											'html' => dateSelect('payment_date_end_date',valueFromGetOrDefault('payment_date_end_date'))
										),
										array(	'db_field' => 'payment_amount',
											'mysql_search_result' => 'payment_amount',
											'caption' => 'Payment Amount',	
											'type' => 'input',
											'html' => createSearchInput('payment_amount')
										),
										array(	'db_field' => 'applied_status',
											'mysql_search_result' => 'applied_status',
											'caption' => 'Applied Status',	
											'type' => 'input',
											'html' => createSearchInput('applied_status')
										)
										);
$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saved_payments_journal_url');

$table_columns = array(
		array(
			'th' => 'View',
			'mysql_field' => 'pos_payments_journal_id',
			'get_url_link' => "view_payments_journal_entry.php",
			'url_caption' => 'View',
			'get_id_link' => 'pos_payments_journal_id'),
		array(
			'th' => 'ID',
			'mysql_field' => 'pos_payments_journal_id',
			'sort' => 'pos_payments_journal_id'),	
		array(
			'th' => 'Source Journal',
			'mysql_field' => 'source_journal',
			'sort' => 'source_journal'),
		array(
			'th' => 'Supplier',
			'mysql_field' => 'supplier',
			'sort' => 'supplier'
			),
		array(
			'th' => 'Payment Account Name',
			'mysql_field' => 'act_name',
			'sort' => 'act_name'
			),
		array(
			'th' => 'Payee Account Name',
			'mysql_field' => 'payee_name',
			'sort' => 'payee_name'
			),
		


		array(
			'th' => 'Payment Date',
			'mysql_field' => 'payment_date',
			'sort' => 'payment_date'),
		array(
			'th' => 'Payment Amount',
			'mysql_field' => 'payment_amount',
			'sort' => 'payment_amount',
			'round' => 2,
			'total' => 2
			),
		array(
			'th' => 'Applied Amount',
			'mysql_field' => 'applied_amount',
			'sort' => 'applied_amount',
			'round' => 2,
			'total' => 2
			),
		array(
			'th' => 'Applied Status',
			'mysql_field' => 'applied_status',
			'sort' => 'applied_status'
			)
			

		);


//$html .= '<input class = "button" type="button" style="width:200px" name="transfer" value="Transfer $$" onclick="open_win(\'select_account.php?action=transfer\')"/>';
$html .= '<p>';
$html .= createUserButton('General Journal');
$html .= createUserButton('Purchases Journal');
$html .= '</p>';


$tmp_select_sql = "SELECT *
	FROM payments_journal WHERE 1";

//create the search form
$action = 'list_payments_journal.php';
$html .= createSearchForm($search_fields,$action);
//Create Search String (AND's after MYSQL WHERE from $_GET data)
$search_sql = createSearchSQLStringMultipleDates($search_fields);
$tmp_select_sql  .= $search_sql;

//Create the order sting to append to the sql statement
$order_by = createSortSQLString($table_columns, $table_columns[1]['mysql_field'], 'DESC');
$tmp_select_sql  .=  " ORDER BY $order_by";
//$tmp_select_sql  .=  " LIMIT 100";
if (isset($_GET['search']))
{
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,$tmp_sql);
	$data = getTransactionSQL($dbc,$tmp_select_sql);
	closeDB($dbc);
	
	//now make the table
	$html .= createRecordsTableWithTotals($data, $table_columns);
	//$html .= '<p>Results are limited to 100</p>';
}
$html .= '<script>document.getElementsByName("supplier")[0].focus();</script>';

include (HEADER_FILE);
echo $html;

include (FOOTER_FILE);
?>
