<?php 
/*
	*edit_product.php
	*main page used to edit existing a manufacturer to the pos system
	Craig Iannazzi 1-23-12
	
*/
$binder_name = 'Products';
$access_type = 'WRITE';
$page_title = 'View a Product';
require_once('../product_functions.php');
$db_table = 'pos_products';
$id['pos_product_id'] = getPostOrGetID('pos_product_id');
$pos_product_id = $id['pos_product_id'];
$complete_location = '../ViewProduct/view_product.php?pos_product_id='.$id['pos_product_id'];
$cancel_location = '../ViewProduct/view_product.php?pos_product_id='.$id['pos_product_id'];

$table_def = createProductTableDef($db_table, $id);
$table_def_w_data = loadDataToTableDef($table_def, $db_table, $id);
$big_html_table = convertTableDefToHTMLForMYSQLInsert($table_def_w_data);
$big_html_table .= createSecondaryProductCategoryTable($pos_product_id);
$table_def_for_post = convertArrayTableDefToPostTableDef($table_def);


include (HEADER_FILE);
$html = '<h2>Edit A Product</h2>';
$html .= tinymce_editor();
$form_handler = 'edit_product.form.handler.php';
$html .= createFormForMYSQLInsert($table_def_for_post, $big_html_table, $form_handler, $complete_location, $cancel_location);
echo $html;
include (FOOTER_FILE);
?>