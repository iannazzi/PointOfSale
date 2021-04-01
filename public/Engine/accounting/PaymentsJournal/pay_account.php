<?php 

/*
	//this will handel the payments and invoices for the purchases journal
	//I would like edit and add capability
	//pass in either payments journal id for edit or the manufacturer id for an add, along with invoice id
	
	Craig Iannazzi 1-23-12
	
*/

$binder_name = 'Payments Journal';
$access_type = 'WRITE';
$page_title = 'Pay To Account';
require_once ('../accounting_functions.php');

$type = getPostOrGetDataIfAvailable('type');
	

if (strtoupper($type) == 'EDIT')
{
	$pos_payments_journal_id = getPostOrGetID('pos_payments_journal_id');
	//$pos_manufacturer_id = getManufacturerIdFromPaymentId($pos_payments_journal_id);
		
	$pos_payee_account_id = getPayeeAccountIdFromPaymentId($pos_payments_journal_id);
	$header = '<p>Edit Payment '.$pos_payments_journal_id.' to '.getAccountName($pos_payee_account_id).'</p>';
	$selected_invoices = getGeneralInvoicesLinkedToPayment($pos_payments_journal_id);
	//$payment_table_def = createPaymentsJournalRecordTableForPurchases($pos_payments_journal_id);
	$payment_table_def = selectSingleTableDataFromID('pos_payments_journal', array('pos_payments_journal_id' => $pos_payments_journal_id), $payment_table_def);
}
else //new
{
	$type = 'New';
	$pos_payments_journal_id = 'TBD';
		$pos_payee_account_id = getPostOrGetID('pos_payee_account_id');

	$header = '<p>New Payment to '.getAccountName($pos_payee_account_id).'</p>';
	//get the invoices associated with the payment for loading
	$pos_general_journal_id = getPostOrGetDataIfAvailable('pos_general_journal_id');
	$selected_invoices = getGeneralInvoiceForNewPayment($pos_general_journal_id);
}


$payment_table_def = array( 
						array('db_field' =>  'payment_amount',
								'caption' => 'Amount to Pay',
								'type' => 'input',
								'value' => '',
								'tags' => ' style="background-color:yellow" ',
								'validate' => 'number'),
						array( 'db_field' => 'pos_account_id',
								'type' => 'select',
								'caption' => 'Payment Method',
								//'html' => createExpensePaymentSelect('pos_account_id', 'false'),
								'html' => createAccountSelect('pos_account_id', getAutoPayAccountId($pos_payee_account_id)),
								'validate' =>array('select_value' => 'false')
									),
						array('db_field' => 'payment_date',
								'caption' => 'Payment Date',
								'type' => 'date',
								'value' => date('Y-m-d'),
								'tags' => '',
								'html' => dateSelect('invoice_date','',''),
								'validate' => 'date'),
						array('db_field' => 'comments',
								'type' => 'textarea',
								'caption' => 'Comments'),
								
						);		


$complete_location = '../GeneralJournal/list_general_journal.php';
$cancel_location = $complete_location .'?message=Canceled';

//create the dynamic invoice table
$unselected_unpaid_invoices = getUnpaidGeneralInvoicesNotIncludedInPayment($pos_payments_journal_id,$pos_payee_account_id);
//$selectable_invoices = getSelectablePurchaseInvoices($init_table_contents, $pos_manufacturer_id);
$selectable_invoices = array_merge($selected_invoices,$unselected_unpaid_invoices);
$payment_invoice_table_def = createPayExpenseInvoiceDynamicTableDef($selectable_invoices);
$invoice_table = createDynamicTable($payment_invoice_table_def, $selected_invoices);
$invoice_table .= '<script>var selectable_invoices = ' . json_encode($selectable_invoices) . ';</script>';
$invoice_table .='<script src="add_general_payment_to_journal.js"></script>'.newline();

		
//create the flow / html tables
		
$big_html_table = '<p>Choose Invoices To Pay. You can also Just Add A payment without Selecting Invoices</p>';
$big_html_table .= $invoice_table;	
$big_html_table .= '<p>Add The Payment</p>';
$big_html_table .= createHTMLTableForMYSQLInsert($payment_table_def);
//$big_html_table .= createHiddenInput('pos_manufacturer_id', $pos_manufacturer_id);
$big_html_table .= createHiddenInput('pos_payee_account_id', $pos_payee_account_id);

$big_html_table .= createHiddenInput('type', $type);
$big_html_table .= createHiddenInput('pos_payments_journal_id', $pos_payments_journal_id);
//build the form
$form_handler = 'pay_account.form.handler.php';
$form_html = createMultiPartFormForMultiMYSQLInsert(array($payment_table_def,$payment_invoice_table_def), $big_html_table, $form_handler, $complete_location, $cancel_location);

//build the page
$html = $header;
$html .= $form_html;
$html .= '<script>document.getElementById("payment_amount").focus();</script>';

//display the page
include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
?>