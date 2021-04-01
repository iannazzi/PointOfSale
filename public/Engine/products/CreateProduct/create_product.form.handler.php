<?php
/*
	* add_manufacturer.form.handler.php
	* handels the additon of manufacturer information
	*called from add_manufacturer.php
	*will ne
*/
$binder_name = 'Products';
$access_type = 'WRITE';


$page_title = 'Create a Product';
require_once('../product_functions.php');
require_once(PHP_LIBRARY);


if (isset($_POST['submit'])) 
{
	$data = postedTableDefArraytoMysqlInsertArray($_POST['table_def']);	
	$pos_product_id['pos_product_id'] = simpleInsertSQLReturnID('pos_products', $data);
	
	$complete_location = '../ViewProduct/view_product.php?pos_product_id='.$pos_product_id['pos_product_id'];
	$cancel_location = '../ViewProduct/view_product.php?pos_product_id='.$pos_product_id['pos_product_id'];

	//additionally write the secondary categories:
	
	$anoter_result = getAndInsertSecondaryCategories($pos_product_id	);
	header('Location: '.$complete_location);		
}


?>
