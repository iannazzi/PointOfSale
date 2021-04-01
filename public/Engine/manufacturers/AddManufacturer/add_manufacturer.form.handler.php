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
	$id = simpleInsertSQLReturnID('pos_manufacturers', $insert);
	$message = urlencode($insert['company'] . " has been added - Please now add at least one brand (brands often have the same name as the company, and companies often have many brand names)");
	header('Location: '.$_POST['complete_location'] .'?pos_manufacturer_id=' .$id.'&message=' . $message);		
}
	
?>
