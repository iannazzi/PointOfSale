<?php
/*
	list_accounts.php
	craig Iannazzi 4-23-12
*/
$binder_name = 'Product Categories';
$access_type = 'READ';
$page_title = 'Categories';
require_once ('../product_functions.php');

//define the search table
$search_fields = array(				array(	'db_field' => 'name',
											'mysql_search_result' => 'name',
											'caption' => 'Category Name',	
											'type' => 'input',
											'html' => createSearchInput('name')
										),
										array(	'db_field' => 'description',
											'mysql_search_result' => 'description',
											'caption' => 'Description',	
											'type' => 'input',
											'html' => createSearchInput('description')),
											array(	'db_field' => 'priority',
											'mysql_search_result' => 'priority',
											'caption' => 'Category Priority',	
											'type' => 'input',
											'html' => createSearchInput('priority'))
										);
$table_columns = array(
		array(
			'th' => 'View',
			'mysql_field' => 'pos_category_id',
			'get_url_link' => "view_category.php",
			'url_caption' => 'View',
			'get_id_link' => 'pos_category_id'),
		array(
			'th' => 'ID',
			'mysql_field' => 'pos_category_id',
			'sort' => 'pos_category_id'),
		array(
			'th' => 'Name',
			'mysql_field' => 'name',
			'sort' => 'name'),
		array(
			'th' => 'Parent',
			'mysql_field' => 'parent_name',
			'sort' => 'parent_name'),
		array(
			'th' => 'level',
			'mysql_field' => 'level',
			'sort' => 'level'),
		/*array(
			'th' => 'Priority',
			'mysql_field' => 'priority',
			'editable' => true,
			'tags' => ' style = "text-align:center", size = "7" class="lined_input" ',
			'sort' => 'priority'),
		array(
			'th' => 'Default Product Priority',
			'mysql_field' => 'default_product_priority',
			'editable' => true,
			'tags' => ' style = "text-align:center", size = "7" class="lined_input" ',
			'sort' => 'default_product_priority'),*/
		array(
			'th' => 'Default Sales Tax Category',
			'mysql_field' => 'tax_category_name',
			'sort' => 'tax_category_name'),
		array(
			'th' => 'Active',
			'mysql_field' => 'active',
			'sort' => 'active')
			
			);




//here is the query that the search and table arrays are built off of.
$tmp_sql = "
CREATE TEMPORARY TABLE tmp

SELECT  pos_categories.*, b.name as parent_name,
		pos_sales_tax_categories.tax_category_name
	FROM pos_categories
	LEFT JOIN pos_sales_tax_categories USING (pos_sales_tax_category_id)
	LEFT JOIN pos_categories as b ON b.pos_category_id = pos_categories.parent


;


";
$tmp_select_sql = "SELECT * FROM tmp WHERE 1";

//create the search form

$action = 'list_categories.php';
//Create Search String (AND's after MYSQL WHERE from $_GET data)
$search_sql = createSearchSQLStringMultipleDates($search_fields);
$tmp_select_sql  .= $search_sql;

//Create the order sting to append to the sql statement
$order_by = createSortSQLString($table_columns, $table_columns[5]['mysql_field'], 'DESC');
$tmp_select_sql  .=  " ORDER BY $order_by";


//$tmp_select_sql  .=  " LIMIT 100";
$dbc = openPOSdb();
$result = runTransactionSQL($dbc,$tmp_sql);
$data = getTransactionSQL($dbc,$tmp_select_sql);
closeDB($dbc);


$html = printGetMessage('message');

//Add a button to add an expense
$html .= '<p>';
$html .= '<input class = "button" type="button" name="add_category" value="Add Category" onclick="open_win(\'add_category.php\')"/>';
$html .= '</p>';

if (isset($_GET['edit']))
{
	$handler = 'list_categories.form.handler.php';
	$html .= '<form id = "multi_line_input" name="multi_line_input" action="'.$handler.'" method="post">';
	$html .= createEditableRecordsTable($data, $table_columns);
	$html .= '<p><input class = "button" type="submit" name="submit_multiedit" value="Submit" />';
	$html .= '<input class = "button" type="submit" name="cancel_multiedit" value="Cancel" />';
	$html .= '</form>';
}
else
{
	//echo ' regular';
	$html .= createRecordsTable($data, $table_columns);
}

$html .= '<script>document.getElementsByName("name")[0].focus();</script>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
?>
