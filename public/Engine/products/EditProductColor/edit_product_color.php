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
$db_table = 'pos_product_options';
$id['pos_product_option_id'] = getPostOrGetID('pos_product_option_id');
$pos_product_option_id = $id['pos_product_option_id'];
$pos_product_id = getProductIDFromProductOptionId($pos_product_option_id);
$complete_location = '../ViewProduct/view_product.php?pos_product_id='.$pos_product_id;
$cancel_location = '../ViewProduct/view_product.php?pos_product_id='.$pos_product_id;

$table_def = createProductColorTableDef($db_table, $pos_product_option_id);


$db_table = 'pos_product_options';
$key_val_id['pos_product_option_id'] = $pos_product_option_id;
$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $table_def);
$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);

$form_handler = 'edit_product_color.form.handler.php';
//$big_html_table .= createSecondaryProductColorCategoryTable($pos_product_option_id);


$html = '<h2>Edit A Product Color</h2>';
$html .= createFormForMYSQLInsert($data_table_def, $big_html_table, $form_handler, $complete_location, $cancel_location);

//$html .= '<div class = "mysql_table_divider">';
	//$html .= '<p>Product is sold as a set with the following products</p>';
	//$html .= createProductSelect();
	//$html .= '</div>';
include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
?>