<?php 

/*
	//this will handel the payments and invoices for the purchases journal
	//I would like edit and add capability
	//pass in either payments journal id for edit or the manufacturer id for an add, along with invoice id
	
	Craig Iannazzi 1-23-12
	
*/

$binder_name = 'Payments Journal';
$access_type = 'WRITE';
$page_title = 'Transfer To Account';
require_once ('../accounting_functions.php');

$pos_payee_account_id = getPostOrGetId('pos_payee_account_id');
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
								'html' => createAccountSelect('pos_account_id', 'false'),
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



$complete_location = '../PaymentsJournal/list_payments_journal.php';
$cancel_location = $complete_location .'?message=Canceled';

$header = '<p>Transfer Funds To An Account</p>';
$big_html_table = createHTMLTableForMYSQLInsert($payment_table_def);
$big_html_table .= createHiddenInput('pos_payee_account_id', $pos_payee_account_id);

$big_html_table .= createHiddenInput('type', 'transfer');
//build the form
$form_handler = 'pay_account.form.handler.php';
$form_html = createFormForMYSQLInsert($payment_table_def, $big_html_table, $form_handler, $complete_location, $cancel_location);

//build the page
$html = $header;
$html .= $form_html;
$html .= '<script>document.getElementsByName("payment_amount")[0].focus();</script>';

//display the page
include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
?>