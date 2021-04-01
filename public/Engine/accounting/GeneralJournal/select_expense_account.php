<?php 

/*
This form will allow you to select a manufacturer from a list then continue in get format with the manufacturer_id'
	
	Craig Iannazzi 4-23-12
	
*/
$binder_name = 'General Journal';
$access_type = 'WRITE';
$page_title = "Select Account";
require_once ('../accounting_functions.php');
$action = getPostOrGetValue('action');
if ($action =='add')
{
	$complete_location = 'add_expense_invoice_to_general_journal.php';
	$accounts = createExpenseAccountSelect('pos_account_id', 'false');

}
elseif ($action =='add_plus')
{
	$complete_location = 'add_expense_invoice_plus_payment_to_general_journal.php';
	$accounts = createExpenseAccountSelect('pos_account_id', 'false');
}
elseif ($action =='pay')
{
	$complete_location = '../PaymentsJournal/pay_expense_invoices.php';
	$accounts = createExpenseAccountSelect('pos_account_id', 'false');
}
elseif ($action == 'statement')
{
	$complete_location = '../GeneralJournal/add_statement.php';
	$accounts = createCreditCardAccountSelect('pos_account_id', 'false');
}
elseif($action == 'pay_statement')
{
	$complete_location = '../PaymentsJournal/pay_statement.php';
	$accounts = createCreditCardAccountSelect('pos_account_id', 'false');
}
$cancel_location = 'list_general_journal.php?message=Canceled';
$data_table_def = array( 
						array( 'db_field' => 'pos_account_id',
								'type' => 'select',
								'caption' => 'Select Expense Account',
								'html' => $accounts,
								'validate' => array('select_value' => 'false'))
							);
include (HEADER_FILE);
$form_handler = 'select_expense_account.form.handler.php';
$html = createTableForMYSQLInsert($data_table_def, $form_handler, $complete_location, $cancel_location);
echo $html;
include (FOOTER_FILE);

?>

