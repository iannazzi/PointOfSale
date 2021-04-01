<?php 
/*
	*edit_product_color.php
	*main page used to edit existing a manufacturer to the pos system
	Craig Iannazzi 4-15-2012
*/
$binder_name = 'Products';
$access_type = 'WRITE';
$page_title = 'Product Color';
require_once('../product_functions.php');
$db_table = 'pos_product_colors';
$id['pos_product_id'] = getPostOrGetID('pos_product_id');
$pos_product_id = $id['pos_product_id'];
$complete_location = '../ViewProduct/view_product.php?pos_product_id='.$pos_product_id;
$cancel_location = '../ViewProduct/view_product.php?pos_product_id='.$pos_product_id;

$table_def = createProductColorTableDef($db_table, $pos_product_id);

$big_html_table = convertTableDefToHTMLForMYSQLInsert($table_def);
$big_html_table .= createSecondaryProductColorCategoryTable('false');
$table_def_for_post = convertArrayTableDefToPostTableDef($table_def);


include (HEADER_FILE);
$html = '<h2>Add A Product Color</h2>';
$form_handler = 'add_product_color.form.handler.php';
$html .= createFormForMYSQLInsert($table_def_for_post, $big_html_table, $form_handler, $complete_location, $cancel_location);
echo $html;
include (FOOTER_FILE);
?>