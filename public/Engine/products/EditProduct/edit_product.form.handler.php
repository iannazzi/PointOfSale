<?php
/*
	* add_manufacturer.form.handler.php
	* handels the additon of manufacturer information
	*called from add_manufacturer.php
	*will ne
*/
$binder_name = 'Products';
$access_type = 'WRITE';


$page_title = 'View a Product';
require_once('../product_functions.php');
require_once(PHP_LIBRARY);

$key_val_id['pos_product_id'] = getPostOrGetID('pos_product_id');
$pos_product_id = $key_val_id['pos_product_id'];
if (isset($_POST['submit'])) 
{
	$update_data = postedTableDefArraytoMysqlUpdateArray($_POST['table_def'], 'pos_product_id');	
	$result = simpleUpdateSQL('pos_products', $key_val_id, $update_data);
	
	//additionally write the secondary categories:
	$anoter_result = getAndInsertSecondaryCategories($key_val_id);
	header('Location: '.$_POST['complete_location']);		
}


?>
