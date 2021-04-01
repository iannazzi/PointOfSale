<?php 

/*
This form will allow you to select a manufacturer from a list then continue in get format with the manufacturer_id'
	
	Craig Iannazzi 4-23-12
	
*/
$binder_name = 'Purchases Journal';
$access_type = 'WRITE';
$page_title = "Select Account For Manufacturer";
require_once ('../accounting_functions.php');

if (isset($_POST['submit'])) //process form
{
	$url = $_POST['complete_location'];	
	$pos_payee_account_id['pos_payee_account_id'] = getPostOrGetID('pos_payee_account_id');
	$url = addGetValue($url, 'pos_payee_account_id', $pos_payee_account_id['pos_payee_account_id']);
	header_redirect($url);
	exit();
}
else // show form
{
	$invoice_type = getPostOrGetValue('invoice_type');
	//invoice, credit or payment
	if(strtoupper($invoice_type) =='PAYMENT') 
	{
		$complete_location = '../PaymentsJournal/pay_purchases_invoices.php';
		$select = createInventoryAccountSelect('pos_payee_account_id', 'false');
	}
	else
	{
		include (HEADER_FILE);
		echo '<p class="error">Error, wrong type selected</p>';
		include (FOOTER_FILE);
	}
	$cancel_location = 'list_purchase_journal.php?message=Canceled';
	
	$db_table = 'pos_accounts';
	$data_table_def = array( 
							array( 'db_field' => 'pos_payee_account_id',
									'type' => 'select',
									'caption' => 'Select Manufacturer Account',
									'html' => $select,
									'validate' => array('select_value' => 'false'))
								);
	include (HEADER_FILE);
	$form_handler = 'select_account.php';
	$html = createTableForMYSQLInsert($data_table_def, $form_handler, $complete_location, $cancel_location);
	$html .= '<p style="font-size:0.8em" >If your account is not showing up make sure the inventory account is set to \'Inventory Account\' which is edited in accounts AND the manufacturer is linked to the Manufacturer Account, which is edited in manufacturers</p>';
	echo $html;
	include (FOOTER_FILE);

}

?>

