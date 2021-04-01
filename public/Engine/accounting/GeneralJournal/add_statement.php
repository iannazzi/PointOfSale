<?php 

/*
	The process to create a new db entry for things like mfg, employee, etc is to define what we want in the table, then create that table....event post the table in json format for the handler to process?
	
	Craig Iannazzi 1-23-12
	
*/
$binder_name = 'General Journal';
$access_type = 'WRITE';
$page_title = 'Statement';
require_once ('../accounting_functions.php');

$complete_location = 'list_general_journal.php';
$cancel_location = 'list_general_journal.php?message=Canceled';
$pos_account_id = getPostOrGetID('pos_account_id');
$pos_chart_of_account_id = getDebitChartOfAccount($pos_account_id);
$db_table = 'pos_general_journal';
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
								'caption' => 'Date On Invoice',
								'type' => 'date',
								'tags' => ' ',
								'html' => dateSelect('invoice_date',''),
								'validate' => 'date'),
						array('db_field' => 'invoice_due_date',
								'caption' => 'Due Date',
								'type' => 'date',
								'tags' => ' ',
								'html' => dateSelect('invoice_due_date',''),
								'validate' => 'date'),			
						array('db_field' =>  'entry_amount',
								'caption' => 'Total Amount Due',
								'type' => 'input',
								'tags' => numbersOnly(),
								'validate' => 'number'),
						array('db_field' =>  'minimum_amount_due',
								'caption' => 'Minimum Amount Due',
								'type' => 'input',
								'tags' => numbersOnly(),
								'validate' => 'number'),
						/*array('db_field' => 'description',
								'type' => 'input',
								'caption' => 'Description or Invoice #'),*/
						array('db_field' => 'comments',
								'type' => 'input',
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
$big_html_table .= createHiddenInput('pos_account_id', $pos_account_id);
include (HEADER_FILE);
$html = '<p>Add a Statement Due</p>';
$table_array = array($data_table_def);
$form_handler = 'add_statement.form.handler.php';
$html .= createMultiPartFormForMultiMYSQLInsert($table_array, $big_html_table, $form_handler, $complete_location, $cancel_location);
$html .= '<script>document.getElementsByName("invoice_date")[0].focus();</script>';

//footer
echo $html;
include (FOOTER_FILE);

?>

