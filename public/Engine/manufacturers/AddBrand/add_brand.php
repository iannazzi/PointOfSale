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
						array( 'db_field' => 'brand_name',
								'type' => 'input',
								'validate' => array('unique' => 'true', 'min_length' => 1),
								'db_table' => $db_table),
						array( 'db_field' => 'brand_code',
								'caption' => 'Brand Code: Must be Unique, Length of 3',
								'type' => 'input',
								'tags' => checkInputAto0() . ' maxlength="3" ',
								'validate' => array('unique' => 'true', 'min_length' => 3),
								'db_table' => $db_table),
						array('db_field' =>  'pos_chart_of_accounts_id',
								'type' => 'select',
								'caption' => 'Link To Received Goods to Chart Of Accounts - What Type of Inventory',
								'html' => createInventoryChartOfAccountSelect('pos_chart_of_accounts_id', 'false', 'off', ' '),
								'validate' => 	array('select_value' => 'false')),
						array('db_field' =>  'sales_rep_email',
								'caption' => 'Sales Rep Email - Use this to OVERRIDE manufacturer email',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'sales_rep_name',
						'caption' => 'Sales Rep Name - Use this to OVERRIDE manufacturer sales rep name',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'sales_rep_phone',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'active',
								'type' => 'checkbox',
								'tags' => 'checked="checked" ',
								'validate' => 'none'),
						array('db_field' => 'comments',
								'type' => 'textarea',
								'validate' => 'none'));
//Header
include (HEADER_FILE);
//set up form
//Manufacturer Table
$html = '<h2>Add A Brand To ' . getManufacturerName($id['pos_manufacturer_id']) . '</h2>';
$form_handler = 'add_brand.form.handler.php';
$html .= createTableForMYSQLInsert($data_table_def, $form_handler, $complete_location, $cancel_location);
//footer
echo $html;
include (FOOTER_FILE);

?>

