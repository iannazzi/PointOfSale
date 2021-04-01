<?php 

/*
This form will allow you to select a manufacturer from a list then continue in get format with the manufacturer_id'
	
	Craig Iannazzi 4-23-12
	
*/
$binder_name = 'Purchases Journal';
$access_type = 'WRITE';
$page_title = "Select Manufacturer";
require_once ('../accounting_functions.php');

if (isset($_POST['submit'])) //process form
{
	//this is where we need to check the manufacturer to find out if it has an account.... if no account then we can't enter it...
	
	$url = $_POST['complete_location'];	
	$pos_manufacturer_id['pos_manufacturer_id'] = getPostOrGetID('pos_manufacturer_id');
	$url = addGetValue($url, 'pos_manufacturer_id', $pos_manufacturer_id['pos_manufacturer_id']);
	header_redirect($url);
	exit();
}
else // show form
{
	$invoice_type = getPostOrGetValue('invoice_type');
	//invoice, credit or payment
	if (strtoupper($invoice_type) =='INVOICE')
	{
		$complete_location = 'add_edit_purchase_invoice_to_journal.php?type=invoice';
		$select = createManufacturerSelect('pos_manufacturer_id', 'false');
	}
	elseif(strtoupper($invoice_type) =='CREDIT') //Credit
	{
		$complete_location = 'add_edit_purchase_invoice_to_journal.php?type=credit';
		$select = createManufacturerSelect('pos_manufacturer_id', 'false');
	}
	elseif(strtoupper($invoice_type) =='PAYMENT') 
	{
		$complete_location = '../PaymentsJournal/pay_purchases_invoices.php';
		//$select = createManufacturerSelect('pos_manufacturer_id', 'false');
		//$select = createInventoryAccountSelect('pos_payee_account_id', 'false');
		
	}
	else
	{
		include (HEADER_FILE);
		echo '<p class="error">Error, wrong type selected</p>';
		include (FOOTER_FILE);
	}
	$cancel_location = 'list_purchase_journal.php?message=Canceled';
	
	$db_table = 'pos_manufacturers';
	$data_table_def = array( 
							array( 'db_field' => 'pos_manufacturer_id',
									'type' => 'select',
									'caption' => 'Select Manufucaturer',
									'html' => $select,
									'validate' => array('select_value' => 'false'))
								);
	include (HEADER_FILE);
	$form_handler = 'select_manufacturer.php';
	$html = createTableForMYSQLInsert($data_table_def, $form_handler, $complete_location, $cancel_location);
	$html .= '<p style="font-size:0.8em" >If your account is not showing up make sure the inventory account is set to \'Inventory Account\' which is edited in accounts AND the manufacturer is linked to the Manufacturer Account, which is edited in manufacturers</p>';
	echo $html;
	include (FOOTER_FILE);

}

?>

