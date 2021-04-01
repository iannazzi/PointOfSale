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
	$insert = postedTableDefArraytoMysqlInsertArray($_POST['table_def']);	
	$id = simpleInsertSQLReturnID('pos_manufacturer_brands', $insert);
	$message = urlencode($insert['brand_name'] . " has been added");
	header('Location: '.$_POST['complete_location'] .'&message=' . $message);		
}
	
?>
