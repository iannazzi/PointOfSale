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

$id['pos_product_option_id'] = getPostOrGetID('pos_product_option_id');
$pos_product_option_id = $id['pos_product_option_id'];
if (isset($_POST['submit'])) 
{
	$update_data = postedTableDefArraytoMysqlUpdateArray($_POST['table_def'], 'pos_product_option_id');	
	$result = simpleUpdateSQL('pos_product_options', $id, $update_data);
	
	//additionally write the secondary categories:
	$anoter_result = getAndInsertSecondaryCategories($id);
	

	header('Location: '.$_POST['complete_location']);		
}
else
{
	var_dump($_POST);
}

?>