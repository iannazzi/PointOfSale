<?php 

/*
This form will allow you to select a manufacturer from a list then continue in get format with the manufacturer_id'
	
	Craig Iannazzi 4-23-12
	
*/
$page_title = "Select Manufacturer";
require_once ('../po_functions.php');

$complete_location = 'create_purchase_return.php';
$cancel_location = '../ListPurchaseOrders/list_purchase_orders.php?message=Canceled';

$db_table = 'pos_manufacturer_brands';
$data_table_def = array( 
						array( 'db_field' => 'pos_manufacturer_brand_id',
								'type' => 'select',
								'caption' => 'Select Brand',
								'html' => createManufacturerBrandSelect('pos_manufacturer_brand_id', 'false'),
								'validate' => array('select_value' => 'false'))
							);
include (HEADER_FILE);
$form_handler = 'select_manufacturer_brand_for_return.form.handler.php';
$html = createTableForMYSQLInsert($data_table_def, $form_handler, $complete_location, $cancel_location);
echo $html;
include (FOOTER_FILE);

?>

