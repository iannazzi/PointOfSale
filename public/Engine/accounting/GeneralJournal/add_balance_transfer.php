<?php
/*
	This page will add a transfer
	for example
	withdraw $200 from business checking account and place it into craig's wallet
	
	to complete this transaction we will need an account from and an account to
	
*/
$binder_name = 'General Journal';
$access_type = 'WRITE';
$page_title = 'Balance Transfer';
require_once ('../accounting_functions.php');

$complete_location = 'list_general_journal.php';
$cancel_location = 'list_general_journal.php?message=Canceled';
$pos_purchase_order_id = getPostOrGetDataIfAvailable('pos_account_id');

$data_table_def = array( 
						array( 'db_field' => 'pos_general_journal_id',
								'type' => 'input',
								'caption' => 'System PO ID',
								'value' => 'TBD',
								'tags' => ' readonly="readonly" '
									),
						array('db_field' => 'pos_store_id',
								'caption' => 'Store',
								'type' => 'select',
								'html' => createStoreSelect('pos_store_id', $_SESSION['store_id'],  'off'),
								'value' => $_SESSION['store_id'],
								'validate' => 'false'),
						array('db_field' => 'pos_employee_id',
								'caption' => 'Employee',
								'type' => 'select',
								'html' => createEmployeeSelect('pos_employee_id', $_SESSION['pos_employee_id'],  'off'),
								'value' => $_SESSION['pos_employee_id'],
								'validate' => 'false'),
						array('db_field' => 'invoice_date',
								'caption' => 'Transfer Date',
								'type' => 'date',
								'tags' => ' ',
								'html' => dateSelect('invoice_date',''),
								'validate' => 'date'),
						array('db_field' =>  'entry_amount',
								'caption' => 'Amount',
								'type' => 'input',
								'tags' => numbersOnly(),
								'validate' => 'number'),
						array('db_field' => 'credit_pos_account_id',
								'type' => 'select',
								'caption' => 'Transfer From',
								'html' => createExpensePaymentSelect('credit_pos_account_id', 'false'),
								'validate' => array('select_value' => 'false')),
						array('db_field' => 'debit_pos_account_id',
								'type' => 'select',
								'caption' => 'Transfer To',
								'html' => createAccountSelect('debit_pos_account_id', 'false'),
								'validate' => array('select_value' => 'false')),
						array('db_field' => 'description',
								'type' => 'input',
								'caption' => 'Description'),
						array('db_field' => 'comments',
								'type' => 'textarea',
								'caption' => 'Comments'),
						array('db_field' =>  '',
								'caption' => 'Source File',
								'type' => 'file_input',
								'name' => 'file_name',
								'db_table' => 'pos_general_journal',
								'db_id_name' => 'pos_general_journal_id',
								'db_id_val' => '',
								'validate' => 'none')
								
						);
					
								
$big_html_table = convertTableDefToHTMLForMYSQLInsert(array($data_table_def));
include (HEADER_FILE);
$html = '<p>Add an Expense Receipt</p>';

$form_handler = 'add_balance_transfer.form.handler.php';
$table_array = array($data_table_def);
$html .= createMultiPartFormForMultiMYSQLInsert($table_array, $big_html_table, $form_handler, $complete_location, $cancel_location);
$html .= '<script>document.getElementsByName("invoice_date")[0].focus();</script>';

//footer
echo $html;
include (FOOTER_FILE);
?>