<?php
/*
	This is a listing of all sales on account
	I will set it up so that we know the flow if we wanted to put something onto a customer account.
	All customers can have an account
	We will want to utilize thier customer profile to make an account, rather than creating a whole new "account" under accounts. 
	When querying A/R
*/
$binder_name = 'Sales Journal';
$access_type = 'WRITE';
$page_title = 'Sales Journal';
require_once ('../accounting_functions.php');


$html = printGetMessage('message');


$search_fields = array(				array(	'db_field' => 'pos_purchases_journal_id',
											'mysql_search_result' => 'pos_purchases_journal_id',
											'caption' => 'System<br>id',	
											'type' => 'input',
											'html' => createSearchInput('pos_purchases_journal_id')
										),
										array(	'db_field' => 'company',
											'mysql_search_result' => 'company',
											'caption' => 'Company',	
											'type' => 'input',
											'html' => createSearchInput('company')
										),
										array(	'db_field' => 'account_name',
											'mysql_search_result' => 'account_name',
											'caption' => 'Account',	
											'type' => 'input',
											'html' => createSearchInput('account_name')
										),
										array(	'db_field' => 'invoice_number',
											'mysql_search_result' => 'invoice_number',
											'caption' => 'Invoice Number',	
											'type' => 'input',
											'html' => createSearchInput('invoice_number')
										),
										array(	'db_field' => 'invoice_status',
											'mysql_search_result' => 'invoice_status',
											'caption' => 'Invoice Status',	
											'type' => 'input',
											'html' => createSearchInput('invoice_status')
										),
										array(	'db_field' => 'invoice_type',
											'mysql_search_result' => 'invoice_type',
											'caption' => 'Invoice Type',	
											'type' => 'input',
											'html' => createSearchInput('invoice_type')
										),
										array(	'db_field' => 'purchase_order_number',
											'mysql_search_result' => 'purchase_order_numbers',
											'caption' => 'Purchase Order Number',	
											'type' => 'input',
											'html' => createSearchInput('purchase_order_numbers')
										),
								array(	'db_field' => 'invoice_date',
											'mysql_search_result' => 'invoice_date',
											'caption' => 'Invoice Date Start',
											'type' => 'start_date',
											'html' => dateSelect('invoice_date_start_date',valueFromGetOrDefault('invoice_date_start_date'))
										),
								array(	'db_field' => 'invoice_date',
											'mysql_search_result' => 'invoice_date',
											'caption' => 'Invoice Date End',	
											'type' => 'end_date',
											'html' => dateSelect('invoice_date_end_date',valueFromGetOrDefault('invoice_date_end_date'))
										),
								array(	'db_field' => 'invoice_due_date',
											'mysql_search_result' => 'invoice_due_date',
											'caption' => 'Invoice Due Date Start',
											'type' => 'start_date',
											'html' => dateSelect('invoice_due_date_start_date',valueFromGetOrDefault('invoice_due_date_start_date'))
										),
								array(	'db_field' => 'invoice_due_date',
											'mysql_search_result' => 'invoice_due_date',
											'caption' => 'Invoice Due Date End',	
											'type' => 'end_date',
											'html' => dateSelect('invoice_due_date_end_date',valueFromGetOrDefault('invoice_due_date_end_date'))
										),
										array(	'db_field' => 'invoice_amount',
											'mysql_search_result' => 'invoice_amount',
											'caption' => 'Invoice Amount',	
											'type' => 'input',
											'html' => createSearchInput('invoice_amount')
										),
										array(	'db_field' => 'discount_applied',
											'mysql_search_result' => 'discount_applied',
											'caption' => 'Discount Applied',	
											'type' => 'input',
											'html' => createSearchInput('discount_applied')
										),
										array(	'db_field' => 'shipping_amount',
											'mysql_search_result' => 'shipping_amount',
											'caption' => 'Shipping Amount',	
											'type' => 'input',
											'html' => createSearchInput('shipping_amount')
										),
								/*array(	'db_field' => 'pos_account_id',
											'mysql_search_result' => 'pos_account_id',
											'caption' => 'Account #',	
											'type' => 'select',
											'html' => createInventoryCCCheckingCashAccountSelect('pos_account_id', valueFromGetOrDefault('pos_account_id'), 'all')),*/
								array(	'db_field' => 'payment_status',
											'mysql_search_result' => 'payment_status',
											'caption' => 'Payment Status',	
											'type' => 'input',
											'html' => createSearchInput('payment_status')
										)
										);
$table_columns = array(
		array(
			'th' => 'View',
			'mysql_field' => 'pos_purchases_journal_id',
			'get_url_link' => "view_purchase_invoice_to_journal.php",
			'url_caption' => 'View',
			'get_id_link' => 'pos_purchases_journal_id'),
		array(
			'th' => 'ID',
			'mysql_field' => 'pos_purchases_journal_id',
			'sort' => 'pos_purchases_journal_id'),	
		array(
			'th' => 'Company',
			'mysql_field' => 'company',
			'sort' => 'company'),
		array(
			'th' => 'Account',
			'mysql_field' => 'account_name',
			'sort' => 'account_name'),
		array(
			'th' => 'Invoice Number',
			'mysql_field' => 'invoice_number',
			'sort' => 'invoice_number'),
		array(
			'th' => 'Invoice Status',
			'mysql_field' => 'invoice_status',
			'sort' => 'invoice_status'
			),
		array(
			'th' => 'Invoice Type',
			'mysql_field' => 'invoice_type',
			'sort' => 'invoice_type'),
		array(
			'th' => 'Purchase Order Number',
			'mysql_field' => 'purchase_order_numbers',
			'sort' => 'purchase_order_numbers'),
		array(
			'th' => 'Invoice Date',
			'mysql_field' => 'invoice_date',
			'sort' => 'invoice_date'),
		array(
			'th' => 'Due Date',
			'mysql_field' => 'invoice_due_date',
			'sort' => 'invoice_due_date'
			),
		array(
			'th' => 'Invoice Amount',
			'mysql_field' => 'invoice_amount',
			'sort' => 'invoice_amount',
			'round' => 2,
			'total' => 0
			),
		array(
			'th' => 'Shipping Amount',
			'mysql_field' => 'shipping_amount',
			'sort' => 'shipping_amount',
			'round' => 2,
			'total' => 0
			),
		array(
			'th' => 'Discount Applied',
			'mysql_field' => 'discount_applied',
			'sort' => 'discount_applied',
			'round' => 2,
			'total' => 0
			),
		array(
			'th' => 'Credits<br>Applied',
			'mysql_field' => 'credits_applied',
			'sort' => 'credits_applied',
			'round' => 2,
			'total' => 0
			),
		array(
			'th' => 'Amount<br>Applied<BR> To Purchase Orders',
			'mysql_field' => 'applied_to_po',
			'sort' => 'applied_to_po',
			'round' => 2,
			'total' => 0
			),
		array(
			'th' => 'Payments<br>Applied',
			'mysql_field' => 'payments_applied',
			'sort' => 'payments_applied',
			'round' => 2,
			'total' => 0
			),
		array(
			'th' => 'Total Due',
			'mysql_field' => 'total_due',
			'sort' => 'total_due',
			'round' => 2,
			'total' => 0
			),
		
		/*array(
			'th' => 'Payment Account Name',
			'mysql_field' => 'act_name',
			'sort' => 'act_name'
			),*/
		array(
			'th' => 'Payment Status',
			'mysql_field' => 'payment_status',
			'sort' => 'payment_status'
			)/*,
		array(
			'th' => 'Source Document',
			'mysql_field' => 'file_name',
			'sort' => 'file_name'
			)*/

		);
//saved search functionality
$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saved_sales_journal_url');

//if there is a message print it
//here is the query that the search and table arrays are built off of.
$tmp_sql = "
CREATE TEMPORARY TABLE sales_joural

;


";
$tmp_select_sql = "SELECT *, 
	IF (invoice_type ='Regular' ,invoice_amount - discount_applied - credits_applied - payments_applied,0) as total_due 
	FROM purchases_journal WHERE 1";
//define the search table


//Functions to add to the journal
if(checkWriteAccess($binder_name))
{
	$html .= '<p>';
	/*$html .= '<input class = "button" type="button" style="width:300px" name="add_purchase_invoice_on_account" value="Add Purchase Invoice On Account" onclick="open_win(\'select_manufacturer_invoice.php?invoice_type=Account\')"/>';
	$html .= '<input class = "button" type="button" style="width:300px" name="add_purchase_invoice" value="Add Purchase Invoice Plus Simple Payment" onclick="open_win(\'select_manufacturer_invoice.php?invoice_type=Payment\')"/>';
	$html .= '<input class = "button" type="button" style="width:300px" name="add_purchase_invoice" value="Add Purchase Invoice" onclick="open_win(\'select_manufacturer_invoice.php?invoice_type=Regular\')"/>';
	//$html .= '<input class = "button" type="button" style="width:300px" name="add_credit_memo" value="Add Purchase Credit Memo" onclick="open_win(\'select_manufacturer_invoice.php?invoice_type=Credit\')"/>';
	$html .= '<input class = "button" type="button" style="width:300px" name="pay_invoices" value="Pay Invoices" onclick="open_win(\'select_manufacturer_invoice.php?invoice_type=PAY\')"/>';
	$html .= '<input class = "button" type="button" style="width:300px" name="pay_invoices" value="Pay To An Account" onclick="open_win(\'select_manufacturer_invoice.php?invoice_type=PAY_TO_ACCOUNT\')"/>';
	$html .= '<input class = "button" type="button" style="width:300px" name="add_credit_memo" value="Add Credit Memo" onclick="open_win(\'select_manufacturer_invoice.php?invoice_type=credit\')"/>';
	*/
	
	//want three options:
	//add invoice
	$html .= '<input class = "button" type="button" style="width:200px" name="add_purchase_invoice_on_account" value="Add Purchase Invoice" onclick="open_win(\'select_manufacturer.php?invoice_type=invoice\')"/>';
	//pay
	$html .= '<input class = "button" type="button" style="width:200px" name="pay_invoices" value="Pay $$" onclick="open_win(\'select_account.php?invoice_type=payment\')"/>';
	//add credit memo
	$html .= '<input class = "button" type="button" style="width:200px" name="add_credit_memo" value="Add Credit Memo" onclick="open_win(\'select_manufacturer.php?invoice_type=credit\')"/>';
	
	$html .= '</p>';
}
$html.= '<div class = "tight_divider">';
$html .= createUserButton('Payments Journal');
$html .= createUserButton('Purchase Orders');

//create the search form

$action = 'list_purchase_journal.php';
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
$html .= '<script>document.getElementsByName("company")[0].focus();</script>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
?>
