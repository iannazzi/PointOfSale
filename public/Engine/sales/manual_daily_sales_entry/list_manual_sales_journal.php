<?php
/*
	*shows a list of all registered manufacturers
*/
$page_title = 'Manual Sales Journal';
require_once ('../accounting_functions.php');

$search_fields = array(				array(	'db_field' => 'company',
											'mysql_search_result' => 'company',
											'caption' => 'Company',	
											'type' => 'input',
											'html' => createSearchInput('company')
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
								array(	'db_field' => 'pos_account_id',
											'mysql_search_result' => 'pos_account_id',
											'caption' => 'Account #',	
											'type' => 'select',
											'html' => createInventoryCCCheckingCashAccountSelect('pos_account_id', valueFromGetOrDefault('pos_account_id'), 'all')),
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
		
		array(
			'th' => 'Payment Account Name',
			'mysql_field' => 'act_name',
			'sort' => 'act_name'
			),
		array(
			'th' => 'Payment Status',
			'mysql_field' => 'payment_status',
			'sort' => 'payment_status'
			),
		array(
			'th' => 'Source Document',
			'mysql_field' => 'file_name',
			'sort' => 'file_name'
			)

		);
		
//saved search functionality
$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saved_purchase_journal_url', 'invoice_date_start_date');

//if there is a message print it
$html = printGetMessage('message');
//here is the query that the search and table arrays are built off of.
$tmp_sql = "
CREATE TEMPORARY TABLE purchases_journal

SELECT  pos_purchases_journal.pos_purchases_journal_id,  
		pos_purchases_journal.file_name, 
		pos_accounts.company as act_name, 
		pos_purchases_journal.pos_account_id, 
		pos_purchases_journal.invoice_date, 
		pos_purchases_journal.invoice_due_date, 
		pos_purchases_journal.invoice_type, 
		pos_purchases_journal.invoice_received_date, 
		pos_purchases_journal.payment_status, 
		pos_purchases_journal.invoice_status, 
		pos_purchases_journal.discount_applied, 
		pos_purchases_journal.discount_available, 
		pos_purchases_journal.discount_lost, 
		pos_purchases_journal.invoice_number, 
		if(pos_purchases_journal.invoice_type = 'Regular', pos_purchases_journal.invoice_amount, -pos_purchases_journal.invoice_amount) as invoice_amount,
		pos_purchases_journal.shipping_amount, 
		pos_manufacturers.company,
		(SELECT GROUP_CONCAT(pos_purchase_orders.purchase_order_number SEPARATOR ', ') 
		FROM pos_purchase_orders
		LEFT JOIN pos_purchases_invoice_to_po
		ON pos_purchases_invoice_to_po.pos_purchase_order_id = pos_purchase_orders.pos_purchase_order_id
		WHERE pos_purchases_invoice_to_po.pos_purchases_journal_id = pos_purchases_journal.pos_purchases_journal_id) as purchase_order_numbers,
		
		(SELECT COALESCE(sum(applied_amount),0) FROM pos_purchases_invoice_to_po WHERE pos_purchases_journal_id = pos_purchases_journal.pos_purchases_journal_id) as applied_to_po,
		(SELECT COALESCE(sum(applied_amount),0) FROM pos_invoice_to_payment WHERE pos_journal_id = pos_purchases_journal.pos_purchases_journal_id AND source_journal = 'PURCHASES JOURNAL') as payments_applied,
		IF(pos_purchases_journal.invoice_type = 'Regular', 
			(SELECT COALESCE(sum(applied_amount),0) 
				FROM pos_invoice_to_credit_memo 
				WHERE pos_purchases_journal_invoice_id = pos_purchases_journal.pos_purchases_journal_id ), 
			0)	
		as credits_applied

		FROM pos_purchases_journal
		LEFT JOIN pos_manufacturers
		ON pos_manufacturers.pos_manufacturer_id = pos_purchases_journal.pos_manufacturer_id
		LEFT JOIN pos_accounts
		ON pos_purchases_journal.pos_account_id = pos_accounts.pos_account_id

;


";
$tmp_select_sql = "SELECT *, 
	IF (invoice_type ='Regular' ,invoice_amount - discount_applied - credits_applied - payments_applied,0) as total_due 
	FROM purchases_journal WHERE 1";
//define the search table


//Add a button to add an expense
$html .= '<p>';
$html .= '<input class = "button" type="button" style="width:300px" name="add_purchase_invoice_on_account" value="Add Purchase Invoice On Account" onclick="open_win(\'select_manufacturer_invoice.php?invoice_type=Account\')"/>';
$html .= '<input class = "button" type="button" style="width:300px" name="add_purchase_invoice" value="Add Purchase Invoice Plus Simple Payment" onclick="open_win(\'select_manufacturer_invoice.php?invoice_type=Payment\')"/>';
$html .= '<input class = "button" type="button" style="width:300px" name="add_purchase_invoice" value="Add Purchase Invoice" onclick="open_win(\'select_manufacturer_invoice.php?invoice_type=Regular\')"/>';
$html .= '<input class = "button" type="button" style="width:300px" name="add_credit_memo" value="Add Purchase Credit Memo" onclick="open_win(\'select_manufacturer_invoice.php?invoice_type=Credit\')"/>';
$html .= '<input class = "button" type="button" style="width:300px" name="pay_invoices" value="Pay Invoices" onclick="open_win(\'select_manufacturer_invoice.php?invoice_type=PAY\')"/>';

$html .= '</p>';

//create the search form

$action = 'list_purchase_journal.php';
$html .= createSearchForm($search_fields,$action);
//Create Search String (AND's after MYSQL WHERE from $_GET data)
$search_sql = createSearchSQLStringMultipleDates($search_fields);
$tmp_select_sql  .= $search_sql;

//Create the order sting to append to the sql statement
$order_by = createSortSQLString($table_columns, $table_columns[1]['mysql_field'], 'DESC');
$tmp_select_sql  .=  " ORDER BY $order_by";

$dbc = openPOSdb();
$result = runTransactionSQL($dbc,$tmp_sql);
$data = getTransactionSQL($dbc,$tmp_select_sql);
closeDB($dbc);

//now make the table

$html .= createRecordsTableWithTotals($data, $table_columns);
$html .= '<script>document.getElementsByName("company")[0].focus();</script>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
?>
