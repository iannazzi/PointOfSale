<?php 
/*

	Craig Iannazzi 4-23-12
	
*/
$binder_name = 'Product Categories';
$access_type = 'WRITE';
$page_title = 'Add Category';
require_once ('../product_functions.php');
require_once ('category_functions.php');
$db_table = 'pos_categories';
$complete_location = 'list_categories.php';
$cancel_location = 'list_categories.php';
$table_def = createCategoryTableDef('New', 'false');								
$big_html_table = createHTMLTableForMYSQLInsert($table_def);	
$big_html_table .= createHiddenInput('add_or_edit', 'New');
include (HEADER_FILE);
$form_handler = 'add_edit_category.form.handler.php';
$html = createFormForMYSQLInsert($table_def ,$big_html_table, $form_handler, $complete_location, $cancel_location);
echo $html;
include (FOOTER_FILE);

?>

