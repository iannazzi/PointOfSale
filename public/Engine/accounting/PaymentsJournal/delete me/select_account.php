<?php 

/*
This form will allow you to select a manufacturer from a list then continue in get format with the manufacturer_id'
	
	Craig Iannazzi 4-23-12
	
*/
$page_title = "Select Account";
require_once ('../accounting_functions.php');
$action = getPostOrGetValue('action');
if ($action =='pay_account')
{
	$complete_location = '../PaymentsJournal/pay_account.php';
}
$cancel_location = 'list_payments_journal.php?message=Canceled';
$data_table_def = array( 
						array( 'db_field' => 'pos_account_id',
								'type' => 'select',
								'caption' => 'Select Expense Account',
								'html' => createAccountSelect('pos_account_id', 'false'),
								'validate' => array('select_value' => 'false'))
							);
include (HEADER_FILE);
$form_handler = 'select_account.form.handler.php';
$html = createTableForMYSQLInsert($data_table_def, $form_handler, $complete_location, $cancel_location);
echo $html;
include (FOOTER_FILE);

?>

