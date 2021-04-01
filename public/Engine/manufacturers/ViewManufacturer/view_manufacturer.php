<?php 

/*
	*view_manufacturer.php
	Creates a html table with an edit button
	Craig Iannazzi 1-23-12
	
*/
$binder_name = 'Manufacturers';
$access_type = 'READ';
require_once('../manufacturer_functions.php');
$page_title = 'View a Manufacturer';

$db_table = 'pos_manufacturers';
$id['pos_manufacturer_id'] = getPostOrGetID('pos_manufacturer_id');
$pos_manufacturer_id = $id['pos_manufacturer_id'];
$edit_location = '../EditManufacturer/edit_manufacturer.php?pos_manufacturer_id='.$id['pos_manufacturer_id'];

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
$html = printGetMessage();
$html .= '<p>Manufacturer Details</p>';
$html .= createHTMLTableForMYSQLData($data_table_def_with_data);
//Add the edit button
$html .= '<p><input class = "button" type="button" name="edit" value="Edit" onclick="open_win(\''.$edit_location.'\')"/></p>';

//accounts linked to this manufacturer
$html.= '<h3>Accounts Linked To this manufacturer. To enter an Invoice the manufacturer needs to be linked to at least one account.</h3>';
$html .= '<p><input style="width:200px" class = "button" type="button" name="account" value="Link To Account(s)" onclick="open_win(\'../Account/link_accounts.php?pos_manufacturer_id=' . $id['pos_manufacturer_id'] .'\')"/>';
$html .= createManufacturerAccountsRecordTable($pos_manufacturer_id);

//Brands
$html.= '<h3>Manufacturer Brands</h3>';
$html .= listBrands($id);
$html .= '<p><input class = "button" type="button" name="add_brand" value="Add A Brand" onclick="open_win(\'../AddBrand/add_brand.php?pos_manufacturer_id=' . $id['pos_manufacturer_id'] .'\')"/>';
$html .= '<input class = "button" style="width:200px;" type="button" name="move_brand" value="Move A Brand to this Manufacturer" onclick="open_win(\'../MoveBrand/move_brand.php?pos_manufacturer_id=' . $id['pos_manufacturer_id'] .'\')"/></p>';
//Upc Uploads

$html .= '<p><input class = "button" type="button" name="upcs" value="Upload UPC\'s" onclick="open_win(\'../ManufacturerUPC/upload_manufacturer_upc_codes.php?pos_manufacturer_id=' . $id['pos_manufacturer_id'] .'\')"/>';



	
echo $html;
include (FOOTER_FILE);
?>


