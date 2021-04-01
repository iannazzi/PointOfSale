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

if (isset($_POST['submit'])) 
{
		$update_array['pos_manufacturer_id'] = $_POST['pos_manufacturer_id'];
		$key_val_id['pos_manufacturer_brand_id'] = $_POST['pos_manufacturer_brand_id'];
		$result = simpleUpdateSQL('pos_manufacturer_brands', $key_val_id, $update_array);

	$message = urlencode(getBrandName($_POST['pos_manufacturer_brand_id']) . " has been moved");
	header('Location: '.$_POST['complete_location'] .'&message=' . $message);		
}
	
?>
