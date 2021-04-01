<?php 

/*
This form will allow you to select a manufacturer from a list then continue in get format with the manufacturer_id'
	
	Craig Iannazzi 4-23-12
	
*/
$page_title = "Select Account";
require_once ('../accounting_functions.php');

if (isset($_POST['submit'])) //process form
{
	$action = getPostOrGetValue('action');

	$url = $_POST['complete_location'];	
	$pos_payee_account_id['pos_payee_account_id'] = getPostOrGetID('pos_payee_account_id');
	$url = addGetValue($url, 'pos_payee_account_id', $pos_payee_account_id['pos_payee_account_id']);
	header_redirect($url);
	exit();
}
else
{
	$action = getPostOrGetValue('action');

	if ($action =='pay_account')
	{
		$complete_location = '../PaymentsJournal/pay_account.php';
	}
	else if (strtoupper($action) =='TRANSFER')
	{
		$complete_location = '../PaymentsJournal/transfer_funds.php';
	}
	$cancel_location = 'list_payments_journal.php?message=Canceled';
	$data_table_def = array( 
							array( 'db_field' => 'pos_payee_account_id',
									'type' => 'select',
									'caption' => 'Select Account To Pay To or Transfer To',
									'html' => createAccountSelect('pos_payee_account_id', 'false'),
									'validate' => array('select_value' => 'false'))
								);
	$form_handler = 'select_account.php';
	$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);
	$big_html_table .= createHiddenInput('action',$action);
	$html = createFormForMYSQLInsert($data_table_def ,$big_html_table, $form_handler, $complete_location, $cancel_location);
	$html .= '<script>document.getElementById(\'pos_payee_account_id\').focus()</script>';
	//$html = createTableForMYSQLInsert($data_table_def, $form_handler, $complete_location, $cancel_location);
	include (HEADER_FILE);
	echo $html;
	include (FOOTER_FILE);
}
?>

