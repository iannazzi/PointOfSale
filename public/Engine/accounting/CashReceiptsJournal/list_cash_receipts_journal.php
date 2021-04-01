<?php
/*
	*shows a list of all registered manufacturers
*/
$binder_name = 'Cash Receipts Journal';
$access_type = 'WRITE';
$page_title = 'Cash Receipts Journal';
require_once ('../accounting_functions.php');


$html = printGetMessage('message');


$search_fields = array(				array(	'db_field' => 'pos_cash_receipts_journal_id',
											'mysql_search_result' => 'pos_cash_receipts_journal_id',
											'caption' => 'System<br>id',	
											'type' => 'input',
											'html' => createSearchInput('pos_cash_receipts_journal_id')
										),
										array(	'db_field' => 'from_account_name',
											'mysql_search_result' => 'from_account_name',
											'caption' => 'From Account Name',	
											'type' => 'input',
											'html' => createSearchInput('from_account_name')
										),
										array(	'db_field' => 'deposit_account_name',
											'mysql_search_result' => 'deposit_account_name',
											'caption' => 'Deposit Account',	
											'type' => 'input',
											'html' => createSearchInput('deposit_account_name')
										),
										array(	'db_field' => 'amount',
											'mysql_search_result' => 'amount',
											'caption' => 'amount',	
											'type' => 'input',
											'html' => createSearchInput('amount')
										),
										array(	'db_field' => 'invoice_status',
											'mysql_search_result' => 'invoice_status',
											'caption' => 'Invoice Status',	
											'type' => 'input',
											'html' => createSearchInput('invoice_status')
										),
										
								array(	'db_field' => 'date',
											'mysql_search_result' => 'date',
											'caption' => 'From Date',
											'type' => 'start_date',
											'html' => dateSelect('date_start_date',valueFromGetOrDefault('date_start_date'))
										),
								array(	'db_field' => 'date',
											'mysql_search_result' => '_date',
											'caption' => 'To Date',	
											'type' => 'end_date',
											'html' => dateSelect('date_end_date',valueFromGetOrDefault('date_end_date'))
										),
								
										);
$table_columns = array(
		array(
			'th' => 'View',
			'mysql_field' => 'pos_cash_receipts_journal_id',
			'get_url_link' => "cash_receipts_journal_entry.php?type=view",
			'url_caption' => 'View',
			'get_id_link' => 'pos_cash_receipts_journal_id'),
		array(
			'th' => 'ID',
			'mysql_field' => 'pos_cash_receipts_journal_id',
			'sort' => 'pos_cash_receipts_journal_id'),	
		array(
			'th' => 'Date',
			'mysql_field' => 'date',
			'sort' => 'date'),
		array(
			'th' => 'From Account',
			'mysql_field' => 'from_account',
			'sort' => 'from_account'),
		array(
			'th' => 'Deposit Account',
			'mysql_field' => 'deposit_account',
			'sort' => 'deposit_account'),
		array(
			'th' => 'amount',
			'mysql_field' => 'amount',
			'sort' => 'amount'),
		

		
		);
//saved search functionality
$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saved_purchase_journal_url');

//if there is a message print it
//here is the query that the search and table arrays are built off of.
$tmp_sql = "
CREATE TEMPORARY TABLE pos_cash_receipts_journal

SELECT  pos_cash_receipts_journal.pos_cash_receipts_journal_id,  
		pos_cash_receipts_journal.pos_from_account_id, 
		pos_cash_receipts_journal.pos_deposit_account_id, 
		pos_cash_receipts_journal.date, 
		pos_cash_receipts_journal.comments, 
		a.company as from_account_name,
		b.company as deposit_account_name
	
		LEFT JOIN pos_accounts a
		ON pos_cash_receipts_journal.pos_from_account_id = a.pos_account_id
		LEFT JOIN pos_accounts b
		ON pos_cash_receipts_journal.pos_deposit_account_id = b.pos_account_id
;


";
$tmp_select_sql = "SELECT * 
	FROM pos_cash_receipts_journal WHERE 1";
//define the search table


//Functions to add to the journal
if(checkWriteAccess($binder_name))
{
	$html .= '<p>';
	$html .= '<input class = "button" type="button" style="width:200px" name="add_purchase_invoice_on_account" value="Add Deposit" onclick="open_win(\'cash_receipts_journal_entry.php?type=new\')"/>';
	$html .= '</p>';
}
$html.= '<div class = "tight_divider">';
//$html .= createUserButton('Payments Journal');
//$html .= createUserButton('Purchase Orders');

//create the search form

$action = 'list_cash_receipts_journal.php';
$html .= createSearchForm($search_fields,$action);
//Create Search String (AND's after MYSQL WHERE from $_GET data)
$search_sql = createSearchSQLStringMultipleDates($search_fields);
$tmp_select_sql  .= $search_sql;

//Create the order sting to append to the sql statement
$order_by = createSortSQLString($table_columns, $table_columns[1]['mysql_field'], 'DESC');
$tmp_select_sql  .=  " ORDER BY $order_by";

if (isset($_GET['search']))
{
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,$tmp_sql);
	$data = getTransactionSQL($dbc,$tmp_select_sql);
	closeDB($dbc);
	
	//now make the table
	
	$html .= createRecordsTableWithTotals($data, $table_columns);
}
$html .= '<script>document.getElementsByName("from_account_name")[0].focus();</script>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
?>
