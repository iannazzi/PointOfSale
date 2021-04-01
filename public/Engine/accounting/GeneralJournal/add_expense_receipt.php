<?php 
$page_title = 'General Journal';
$binder_name = 'General Journal';
$access_type = 'WRITE';
require_once ('../accounting_functions.php');

$complete_location = 'list_general_journal.php';
$cancel_location = 'list_general_journal.php?message=Canceled';
$form_handler = 'add_expense_receipt.form.handler.php';

$data_table_def = createGeneralJournalExpenseEntryTableDef('New');			
//$big_html_table = convertTableDefToHTMLForMYSQLInsert(array($data_table_def));


$pj_table_def = createPaymentEntryTableDef('New');

$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);
$big_html_table .= '<p>Payment</p>';
$big_html_table .= createHTMLTableForMYSQLInsert($pj_table_def);	

$validator = array(array_merge($data_table_def,$pj_table_def));

$html = '<p>Add an Expense Receipt</p>';
$html .= createMultiPartFormForMultiMYSQLInsert($validator, $big_html_table, $form_handler, $complete_location, $cancel_location);
$html .= '<script>document.getElementsByName("pos_employee_id")[0].focus();</script>';
include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
?>

