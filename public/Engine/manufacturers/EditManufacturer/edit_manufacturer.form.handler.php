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

$pos_manufacturer_id['pos_manufacturer_id'] = getPostOrGetID('pos_manufacturer_id');

if (isset($_POST['submit'])) 
{
	$update_data = postedTableDefArraytoMysqlUpdateArray($_POST['table_def'], 'pos_manufacturer_id');	
	$result = simpleUpdateSQL('pos_manufacturers', $pos_manufacturer_id, $update_data);
	header('Location: '.$_POST['complete_location']);		
}


?>
