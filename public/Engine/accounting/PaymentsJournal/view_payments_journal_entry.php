<?php

//the payment can be entered and linked to nothing
//it can be a general journal entry
//it can be a purchases journal entry.
//lets find that out first!


$binder_name = 'Payments Journal';
$access_type = 'READ';

require_once ('../accounting_functions.php');

$payments_javascript = 'payments.2014.04.01.js';
$complete_location = 'list_payments_journal.php';
$cancel_location = 'list_payments_journal.php';
$pos_payments_journal_id = getPostOrGetID('pos_payments_journal_id');
$delete_location = 'delete_payments_journal_entry.form.handler.php?pos_payments_journal_id='.$pos_payments_journal_id;

$page_title = 'Payment ' . $pos_payments_journal_id;

//now we need to link how the payment applies to journal entries...
$source_journal = getPaymentSourceJournal($pos_payments_journal_id);
$payment_table_def = array( 
					array('db_field' =>  'payment_amount',
							'caption' => 'Amount to Pay',
							'type' => 'input',
							'value' => '',
							'tags' => ' class="highlight" ',
							'validate' => 'number'),
					array( 'db_field' => 'pos_account_id',
							'type' => 'select',
							'caption' => 'Payment Method',
							'html' => createExpensePaymentSelect('credit_pos_account_id', 'false'),
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
					array('db_field' => 'applied_status',
								'caption' => 'Applied Status',
								'type' => 'select',
								'html' => appliedStatusSelect('applied_status','false'),
								'validate' => 'none'),
						array('db_field' => 'payment_status',
								'caption' => 'Payment Status',
								'type' => 'select',
								'html' => paymentStatusSelect('payment_status','false'),
								'validate' => 'none')
							
					);	
$payment_table_def = selectSingleTableDataFromID('pos_payments_journal', array('pos_payments_journal_id' => $pos_payments_journal_id), $payment_table_def);

$pos_payee_account_id = getSingleValueSQL("SELECT pos_payee_account_id FROM pos_payments_journal WHERE pos_payments_journal_id=$pos_payments_journal_id");
$pos_account_id = getSingleValueSQL("SELECT pos_account_id FROM pos_payments_journal WHERE pos_payments_journal_id=$pos_payments_journal_id");
$payee_account_link = POS_ENGINE_URL . '/accounting/Accounts/list_account_activity.php?pos_account_id='.$pos_payee_account_id;


$html = printGetMessage();
$html .= confirmDelete($delete_location);
$html .=  '<script src="'.$payments_javascript.'"></script>'.newline();

$html .= '<h2>Payment To <a href="'.$payee_account_link.'" target ="_blank">'.getAccountName($pos_payee_account_id) . ' ' . getAccountNumber($pos_payee_account_id) .' </a> </h2>';
if ($source_journal == 'PURCHASES JOURNAL')
{	
	$edit_location = 'pay_purchases_invoices.php?type=Edit&pos_payments_journal_id='.$pos_payments_journal_id;
	$edit_payment_location = 'edit_payments_journal_entry.php?type=Edit&pos_payments_journal_id='.$pos_payments_journal_id;
	
	//$html .=  '<p>Payment for Purchases Journal Entry</p>';
	
	
	$html .= createHTMLTableForMYSQLData($payment_table_def);
	$html .= '<input class = "button" type="button" name="edit" style="width:200px" value="Edit & Apply To Invoices" onclick="open_win(\''.$edit_location.'\')"/>';
	$html .= '<input class = "button" type="button" name="edit" style="width:200px"  value="Edit Payment Details" onclick="open_win(\''.$edit_payment_location.'\')"/>';
	$html .= '<input class = "button" type="button" name="edit" value="Delete" onclick="confirmDelete();"/>';
	$html .= '<p>Purchases Invoices Linked To This Payment</p>';
	$html .= createPaymentsJournalRecordTableForPurchases($pos_payments_journal_id);
	
	

	
}
else if ($source_journal == 'GENERAL JOURNAL')
{

	$edit_location = 'pay_account.php?type=Edit&pos_payments_journal_id='.$pos_payments_journal_id;
	$edit_payment_location = 'edit_payments_journal_entry.php?type=Edit&pos_payments_journal_id='.$pos_payments_journal_id;
	//$html .=  '<p>Payment for General Journal Entry</p>';
	$html .= createHTMLTableForMYSQLData($payment_table_def);
		$html .= '<input class = "button" type="button" name="edit" style="width:200px" value="Edit & Apply To Invoices" onclick="open_win(\''.$edit_location.'\')"/>';
	$html .= '<input class = "button" type="button" name="edit" style="width:200px"  value="Edit Payment Details" onclick="open_win(\''.$edit_payment_location.'\')"/>';
	$html .= '<input class = "button" type="button" name="edit" value="Delete" onclick="confirmDelete();"/>';
	$html .= '<p>General Invoices Linked To This Payment</p>';
	$html .= createJournalRecordTable($pos_payments_journal_id);

}
else
{
	trigger_error('payments view error - no journal');
}

//if this is a checking account we can print checks....


if (isCheckingAccount($pos_account_id))
{
	$html .= '<script>var pos_payments_journal_id = '.$pos_payments_journal_id. '</script>';
	//if there is a printer linked to this account then enable the following button:
	$printers = getSQL("SELECT pos_printer_id FROM pos_printers where pos_account_id = $pos_account_id");
	if (sizeof($printers)>0)
	{
		$html .='<input class = "button" style="width:200px" type="button" name="print_button" id="print_button" value="Print Check" onclick="selectPrinter()"/>';
		
		//create the modal form in case there is more than one printer
		$html .= '<script>var printers = '.sizeof($printers). '</script>';
		if(sizeof($printers)==1)
		{
			$html .= '<script>var pos_printer_id = '.$printers[0]['pos_printer_id']. '</script>';
		}
		$html .= '<div id="print-select-dialog-form" title="Select Printer">';
		$html .= createCheckingPrinterSelect('pos_printer_id', 'false', $pos_account_id);
		$html .= '</div>';
		$html .= '<div style = "text-align: center;"  id="cc_loading_image"><img src="'.POS_ENGINE_URL . '/includes/images/ajax_loader_gray.gif" style="padding-top: 10px;" height="60" width="60"/></div>';
	$html .= '</div>';
		
	}
	$html .='<input class = "button" style="width:200px" type="button" name="print_button_inline" id="print_button_inline" value="Open Check" onclick="printCheck(\'false\')"/>';
	$html.='<div id="print_alert"></div>';
}
$html .='<input class = "button" style="width:200px" type="button" name="return" value="Return" onclick="open_win(\''.$cancel_location.'\')"/>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);




	
	
	
?>