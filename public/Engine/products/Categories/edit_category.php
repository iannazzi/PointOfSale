<?php 

/*

	Craig Iannazzi 4-23-12
	
*/
$binder_name = 'Product Categories';
$access_type = 'WRITE';
$page_title = 'Add Category';
require_once ('../product_functions.php');
require_once ('category_functions.php');
$key_val_id['pos_category_id'] = getPostOrGetID('pos_category_id');
$db_table = 'pos_categories';
$complete_location = 'list_categories.php';
$cancel_location = 'list_categories.php';
$table_def = createCategoryTableDef('Edit', $key_val_id);	
$data_table_def_with_data = selectSingleTableDataFromID($db_table, $key_val_id, $table_def);

$big_html_table = createHTMLTableForMYSQLInsert($data_table_def_with_data);
$big_html_table .= createHiddenInput('add_or_edit', 'Edit');
include (HEADER_FILE);
$html = printGetMessage();
$html .= '<p>Edit Category</p>';
$form_handler = 'add_edit_category.form.handler.php';
$html = createFormForMYSQLInsert($table_def ,$big_html_table, $form_handler, $complete_location, $cancel_location);		
echo $html;
include (FOOTER_FILE);

?>

