<?php
/*
	* add_manufacturer.form.handler.php
	* handels the additon of manufacturer information
	*called from add_manufacturer.php
	*will ne
*/
$binder_name = 'Manufacturers';
$access_type = 'WRITE';
require_once ('../manufacturer_functions.php');
require_once(PHP_LIBRARY);
$pos_manufacturer_brand_id['pos_manufacturer_brand_id'] = getPostOrGetID('pos_manufacturer_brand_id');
if (isset($_POST['submit'])) 
{
	$update_data = postedTableDefArraytoMysqlUpdateArray($_POST['table_def'], 'pos_manufacturer_brand_id');	
	$result = simpleUpdateSQL('pos_manufacturer_brands', $pos_manufacturer_brand_id, $update_data);
	$message = urlencode($update_data['brand_name'] . " has been updated");
	$pos_manufacturer_id = //getManufacturerIDFromBrandId($pos_manufacturer_brand_id['pos_manufacturer_brand_id']);
	header('Location: '.$_POST['complete_location'] .'&message=' . $message);		
}
?>
