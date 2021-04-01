<?php 

/*
	The process to create a new db entry for things like mfg, employee, etc is to define what we want in the table, then create that table....event post the table in json format for the handler to process?
	
	Craig Iannazzi 1-23-12
	
*/
$binder_name = 'Purchases Journal';
$access_type = 'WRITE';
$page_title = 'Purchase Journal';
require_once ('../accounting_functions.php');

$complete_location = 'list_purchase_journal.php';
$cancel_location = 'list_purchase_journal.php?message=Canceled';
$pos_manufacturer_id = getPostOrGetID('pos_manufacturer_id');
$pos_purchase_order_id = getPostOrGetDataIfAvailable('pos_purchase_order_id');
$db_table = 'pos_purchases_journal';

$select_events = ' onchange="loadPO()" ';


$data_table_def =  createCreditMemoTableDef($pos_manufacturer_id);
$multi_select =	createCreditMemoPOSelect($pos_manufacturer_id, $pos_purchase_order_id);		
								
$big_html_table = convertTableDefToHTMLForMYSQLInsert(array($data_table_def));
$big_html_table .= convertTableDefToHTMLForMYSQLInsert(array($multi_select));
$big_html_table .= createHiddenInput('pos_manufacturer_id', $pos_manufacturer_id);
include (HEADER_FILE);
$html = '<p>Add a Credit Memo to the Purchase Journal For ' . getManufacturerName($pos_manufacturer_id) . '</p>';

$form_handler = 'add_purchase_credit_memo_to_journal.form.handler.php';
$table_array = array($data_table_def,$multi_select);
$html .= createMultiPartFormForMultiMYSQLInsert(array($data_table_def), $big_html_table, $form_handler, $complete_location, $cancel_location);
$html .= '<script>document.getElementsByName("invoice_number")[0].focus();</script>';

//footer
echo $html;
include (FOOTER_FILE);

?>

