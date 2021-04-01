<?php 

/*
	The process to create a new db entry for things like mfg, employee, etc is to define what we want in the table, then create that table....event post the table in json format for the handler to process?
	
	Craig Iannazzi 1-23-12
	
*/
$binder_name = 'Payments Journal';
$access_type = 'WRITE';
$page_title = 'Pay Expense Invoice';
require_once ('../accounting_functions.php');

$complete_location = '../GeneralJournal/list_general_journal.php';
$cancel_location = '../GeneralJournal/list_general_journal.php?message=Canceled';
$pos_account_id = getPostOrGetID('pos_account_id');
$pos_general_journal_id = getPostOrGetDataIfAvailable('pos_general_journal_id');
$statements = getUnpaidStatements($pos_account_id);
$selected_statements[0]['pos_general_journal_id'] = $pos_general_journal_id;
$auto_pay_account_id = getAutoPayAccountId($pos_account_id);
$data_table_def = array( 
						array('db_field' => 'pos_general_journal_id',
								'type' => 'select',
								'caption' => 'Statment<br>To Apply Payment To<br><br>Use Control, Shift, and/or <br>Command To Select Multiple',
								'html' => createUnpaidStatmentSelect('pos_general_journal_id[]', $pos_account_id, $selected_statements, 'off', ' multiple size="5" onclick="updateAmountDue()" onchange="needToConfirm=true" '),
								'validate' => array('multi_select_value' => 'false')),
						array('db_field' => 'pos_employee_id',
								'caption' => 'Employee',
								'type' => 'select',
								'html' => createEmployeeSelect('pos_employee_id', $_SESSION['pos_employee_id'],  'off'),
								'value' => $_SESSION['pos_employee_id'],
								'validate' => 'false'),
						array('db_field' =>  'amount_due',
								'caption' => 'Amount Due',
								'type' => 'input',
								'tags' => ' readonly ="readonly" ',
								'value' => ''),
						array('db_field' =>  'minimum_amount_due',
								'caption' => 'Minimum Amount Due',
								'type' => 'input',
								'tags' => ' readonly ="readonly" ',
								'value' => ''),
						array('db_field' =>  'payment_amount',
								'caption' => 'Amount to Pay',
								'type' => 'input',
								'value' => '',
								'tags' => numbersOnly(),
								'validate' => 'number'),
						array( 'db_field' => 'pos_account_id',
								'type' => 'select',
								'caption' => 'Payment Method',
								'html' => createExpensePaymentSelect('pos_account_id', $auto_pay_account_id),
								'validate' =>array('select_value' => 'false')
									),
						array('db_field' => 'payment_date',
								'caption' => 'Payment Date',
								'type' => 'date',
								'value' => date('Y-m-d'),
								'tags' => '',
								'html' => dateSelect('payment_date','',''),
								'validate' => 'date'),
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
$big_html_table .= createHiddenInput('statement_account_id', $pos_account_id);
include (HEADER_FILE);
$html = '<p>Pay Statement from '.getAccountName($pos_account_id).'</p>';
$html .='<script src="pay_statements.js"></script>'.newline();
$html .= '<script>var statements = ' . json_encode($statements) . ';</script>';
$form_handler = 'pay_statement.form.handler.php';
$table_array = array($data_table_def);
$html .= createMultiPartFormForMultiMYSQLInsert($table_array, $big_html_table, $form_handler, $complete_location, $cancel_location);
$html .= '<script>document.getElementsByName("amount_due")[0].focus();</script>';
$html .= '<script> window.onload= updateAmountDue();</script>';
//footer
echo $html;
include (FOOTER_FILE);

?>

