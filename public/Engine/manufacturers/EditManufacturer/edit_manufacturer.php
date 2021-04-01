<?php 
/*
	*edit_manufacturer.php
	*main page used to edit existing a manufacturer to the pos system
	Craig Iannazzi 1-23-12
	
*/
$binder_name = 'Manufacturers';
$access_type = 'WRITE';
require_once ('../manufacturer_functions.php');
$db_table = 'pos_manufacturers';
$id['pos_manufacturer_id'] = getPostOrGetID('pos_manufacturer_id');
$complete_location = '../ViewManufacturer/view_manufacturer.php?pos_manufacturer_id='.$id['pos_manufacturer_id'];
$cancel_location = '../ViewManufacturer/view_manufacturer.php?pos_manufacturer_id='.$id['pos_manufacturer_id'];
$data_table_def = array( 
						array( 'db_field' => 'pos_manufacturer_id',
								'type' => 'input',
								'tags' => 'readonly="readonly"'),
						array( 'db_field' => 'company',
								'type' => 'input',
								'validate' => array('unique' => 'true', 'min_length' => 1, 'id' => $id),
								'db_table' => $db_table),
						/*array( 'db_field' => 'pos_account_id',
								'type' => 'select',
								'caption' => 'Account ID',
								'html' => createInventoryAccountSelect('pos_account_id', 'false'),
								'validate' => 'none'),*/
						array('db_field' => 'sales_rep',
								'caption' => 'Sales Representative(s) (commas to separate names)',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'email',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' => 'phone',
								'type' => 'input',
								'tags' => 'maxlength = "40" ',
								'validate' => 'none'),
						array('db_field' =>  'fax',
								'type' => 'input',
								'tags' => 'maxlength = "40" ',
								'validate' => 'none'),
						array('db_field' => 'address1',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' => 'address2',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' => 'city',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' => 'state',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' => 'zip',
								'type' => 'input',
								'tags' => 'maxlength = "20" ',
								'validate' => 'none'),
						array('db_field' => 'country',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'active',
								'type' => 'checkbox',
								'validate' => 'none'),
						array('db_field' => 'comments',
								'type' => 'textarea',
								'validate' => 'none'));
$data_table_def_with_data = selectSingleTableDataFromID($db_table, $id, $data_table_def);
//Header
include (HEADER_FILE);
//set up form
//Manufacturer Table
$html = '<h2>Edit A Manufacturer/Supplier</h2>';
$form_handler = 'edit_manufacturer.form.handler.php';
$html .= createTableForMYSQLInsert($data_table_def_with_data, $form_handler, $complete_location, $cancel_location);
//footer
echo $html;
include (FOOTER_FILE);
?>


