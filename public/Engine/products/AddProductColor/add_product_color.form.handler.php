<?php
/*
	* add_manufacturer.form.handler.php
	* handels the additon of manufacturer information
	*called from add_manufacturer.php
	*will ne
*/

$binder_name = 'Products';
$access_type = 'WRITE';

$page_title = 'Product';
require_once('../product_functions.php');
require_once(PHP_LIBRARY);

if (isset($_POST['submit'])) 
{	
	$insert_data = postedTableDefArraytoMysqlInsertArray($_POST['table_def']);
	$id['pos_product_color_id'] = simpleInsertSQLReturnID('pos_product_colors', $insert_data);
	
	//additionally write the secondary categories:
	$anoter_result = getAndInsertSecondaryCategories($id);
	header('Location: '.$_POST['complete_location']);		
}


?>
