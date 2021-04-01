<?php 

/*
	The process to create a new db entry for things like mfg, employee, etc is to define what we want in the table, then create that table....event post the table in json format for the handler to process?
	
	Craig Iannazzi 1-23-12
	
*/
$binder_name = 'Manufacturers';
$access_type = 'WRITE';
require_once ('../manufacturer_functions.php');

$db_table = 'pos_manufacturer_brands';
$id['pos_manufacturer_id'] = getPostOrGetID('pos_manufacturer_id');
$complete_location = '../ViewManufacturer/view_manufacturer.php?pos_manufacturer_id='.$id['pos_manufacturer_id'];
$cancel_location = '../ViewManufacturer/view_manufacturer.php?pos_manufacturer_id='.$id['pos_manufacturer_id'];

$data_table_def = array( 
						array( 'db_field' => 'pos_manufacturer_id',
								'value' => $id['pos_manufacturer_id'],
								'type' => 'input',
								'tags' => ' readonly = "readonly" '),
						array('db_field' => 'pos_manufacturer_brand_id',
									'caption' => 'Brand Name',
									'type' => 'select',
									'html' => createManufacturerBrandSelect('pos_manufacturer_brand_id', 'false', 'off', 'onchange="setPurchaseOrderNumber();needToConfirm=true;"'),
										'validate' => array('select_value' => 'false')
										));
										

//Header
include (HEADER_FILE);
//set up form
//Manufacturer Table
$html = '<h2>Move A Brand To ' . getManufacturerName($id['pos_manufacturer_id']) . '</h2>';
$form_handler = 'move_brand.form.handler.php';
$html .= createTableForMYSQLInsert($data_table_def, $form_handler, $complete_location, $cancel_location);
//footer
echo $html;
include (FOOTER_FILE);

?>

