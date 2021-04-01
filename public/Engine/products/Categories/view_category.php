<?php 

/*

	Craig Iannazzi 4-23-12
	
*/
$binder_name = 'Product Categories';
$access_type = 'READ';
$page_title = 'Add Category';
require_once ('../product_functions.php');
require_once ('category_functions.php');
$key_val_id['pos_category_id'] = getPostOrGetID('pos_category_id');
$db_table = 'pos_categories';
$complete_location = 'list_categories.php';
$cancel_location = 'list_categories.php';

$edit_location = 'edit_category.php?pos_category_id='.$key_val_id['pos_category_id'];

$table_def = createCategoryTableDef('Edit', $key_val_id);	
$data_table_def_with_data = selectSingleTableDataFromID($db_table, $key_val_id, $table_def);
include (HEADER_FILE);
$html = printGetMessage();
$html .= '<p>Edit Category</p>';
$html .= createHTMLTableForMYSQLData($data_table_def_with_data);
//Add the edit button
$html .= '<p>';
$html .= '<input class = "button" type="button" name="edit" value="Edit" onclick="open_win(\''.$edit_location.'\')"/>';
$html .='<input class = "button" style="width:200px" type="button" name="return" value="Return To Categories" onclick="open_win(\'list_categories.php\')"/>';
$html .= '</p>';
echo $html;
include (FOOTER_FILE);

?>

