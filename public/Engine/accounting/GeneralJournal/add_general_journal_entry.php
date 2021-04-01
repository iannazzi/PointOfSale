<?php 
$binder_name = 'General Journal';
$access_type = 'WRITE';
/*
	The process to create a new db entry for things like mfg, employee, etc is to define what we want in the table, then create that table....event post the table in json format for the handler to process?
	
	Craig Iannazzi 1-23-12
	
*/
$page_title = 'General Journal';
require_once ('../accounting_functions.php');

$complete_location = 'list_general_journal.php';
$cancel_location = 'list_general_journal.php?message=Canceled';
$pos_purchase_order_id = getPostOrGetDataIfAvailable('pos_account_id');
$db_table = 'pos_general_journal';


$data_table_def = array( 
						array( 'db_field' => 'pos_general_journal_id',
								'type' => 'input',
								'caption' => 'System PO ID',
								'value' => 'TBD',
								'tags' => ' readonly="readonly" '
									),
						array('db_field' => 'receipt_date',
								'caption' => 'Receipt Date',
								'type' => 'date',
								'tags' => ' ',
								'html' => dateSelect('receipt_date',''),
								'validate' => 'date'),
						array('db_field' =>  'receipt_amount',
								'caption' => 'Amount',
								'type' => 'input',
								'tags' => numbersOnly(),
								'validate' => 'number'),
						array('db_field' => 'expense_chart_of_account_id',
								'type' => 'select',
								'caption' => 'Expense Account',
								'html' => createChartOfAccountsExpenseCategorySelect('expense_chart_of_account_id', 'false'), 
								'validate' => array('select_value' => 'false')),
						array('db_field' => 'payment_chart_of_account_id',
								'type' => 'select',
								'caption' => 'Payment Method',
								'html' => createExpensePaymentSelect('payment_chart_of_account_id', 'false'),
								'validate' => array('select_value' => 'false')),
						array('db_field' => 'description',
								'type' => 'input',
								'caption' => 'Description'),
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

$form_handler = 'add_general_journal_entry.form.handler.php';
$table_array = array($data_table_def);
$html .= createMultiPartFormForMultiMYSQLInsert($table_array, $big_html_table, $form_handler, $complete_location, $cancel_location);
$html .= '<script>document.getElementsByName("receipt_date")[0].focus();</script>';

//footer
echo $html;
include (FOOTER_FILE);

?>

