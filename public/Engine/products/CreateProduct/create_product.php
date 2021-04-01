<?php 
/*
	*edit_product.php
	*main page used to edit existing a manufacturer to the pos system
	Craig Iannazzi 1-23-12
	
*/
$binder_name = 'Products';
$access_type = 'WRITE';
$page_title = 'Create a Product';
require_once('../product_functions.php');
$db_table = 'pos_products';

	$complete_location = '../products.php';
	$cancel_location = '../products.php';


$table_def = createNewProductTableDef($db_table);

$big_html_table = convertTableDefToHTMLForMYSQLInsert($table_def);
$big_html_table .= createSecondaryProductCategoryTable('false');
$table_def_for_post = convertArrayTableDefToPostTableDef($table_def);


include (HEADER_FILE);
$html = '<h2>Create A New Product</h2>';
$form_handler = 'create_product.form.handler.php';
$html .= createFormForMYSQLInsert($table_def_for_post, $big_html_table, $form_handler, $complete_location, $cancel_location);
echo $html;
include (FOOTER_FILE);
?>