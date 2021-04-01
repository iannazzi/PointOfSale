<?php
/*
	* add_manufacturer.form.handler.php
	* handels the additon of manufacturer information
	*called from add_manufacturer.php
	*will ne
*/
$binder_name = 'Manufacturers';
$access_type = 'WRITE';
require_once('../manufacturer_functions.php');
require_once(PHP_LIBRARY);
$pos_manufacturer_brand_id['pos_manufacturer_brand_id'] = getPostOrGetID('pos_manufacturer_brand_id');
if (isset($_POST['submit'])) 
{
	//get the table tbody data into a 2-d array
	$tbody_data=getTbodyData($_POST['tbody_name'], $_POST['table_def'],$_POST['number_of_rows'], $_POST['number_of_columns']);
	//Delete all sizes, then create new sizes
	$size_delete_q = "DELETE FROM pos_manufacturer_brand_sizes WHERE pos_manufacturer_brand_id =".$pos_manufacturer_brand_id['pos_manufacturer_brand_id'];
	runSQL($size_delete_q);
	//convert the posted data into an sql statement by comparing it to the table definition
	$sql_insert_update = createBrandChartSQLStatement($pos_manufacturer_brand_id['pos_manufacturer_brand_id'], $tbody_data, $_POST['table_def']);
	$message = urlencode(getBrandName($pos_manufacturer_brand_id['pos_manufacturer_brand_id']) . " has been updated");
	$pos_manufacturer_id = getManufacturerIDFromBrandId($pos_manufacturer_brand_id['pos_manufacturer_brand_id']);
	header('Location: '.$_POST['complete_location'] .'&message=' . $message);		
}

?>
