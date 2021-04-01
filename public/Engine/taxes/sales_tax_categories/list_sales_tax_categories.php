<?php
/*
	*shows a list of all registered manufacturers
*/
$page_title = 'Sales Tax';
require_once ('../tax_functions.php');

$search_fields = array(				array(	'db_field' => 'pos_sales_tax_category_id',
											'mysql_search_result' => 'pos_sales_tax_category_id',
											'caption' => 'Category ID',	
											'type' => 'input',
											'html' => createSearchInput('pos_sales_tax_category_id')
										),
										
										array(	'db_field' => 'tax_category_name',
											'mysql_search_result' => 'tax_category_name',
											'caption' => 'Tax Category Name',	
											'type' => 'input',
											'html' => createSearchInput('tax_category_name')
										)
										
										);
$table_columns = array(
		array(
			'th' => 'View',
			'mysql_field' => 'pos_sales_tax_category_id',
			'get_url_link' => "view_sales_tax_category.php",
			'url_caption' => 'View',
			'get_id_link' => 'pos_sales_tax_category_id'),
		array(
			'th' => 'ID',
			'mysql_field' => 'pos_sales_tax_category_id',
			'sort' => 'pos_sales_tax_category_id'),	
		array(
			'th' => 'Sales Tax Category Name',
			'mysql_field' => 'tax_category_name',
			'sort' => 'tax_category_name')
		
		);
$html = printGetMessage('message');	
//saved search functionality
$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saved_tax_categories_url');

//if there is a message print it
$html = printGetMessage('message');
//here is the query that the search and table arrays are built off of.
$tmp_sql = "
CREATE TEMPORARY TABLE tmp

SELECT  
		pos_sales_tax_categories.pos_sales_tax_category_id,
		pos_sales_tax_categories.tax_category_name
		
		FROM pos_sales_tax_categories


;


";
$tmp_select_sql = "SELECT *
	FROM tmp WHERE 1";

//create the search form
$action = 'list_sales_tax_categories.php';
//Create Search String (AND's after MYSQL WHERE from $_GET data)
$search_sql = createSearchSQLStringMultipleDates($search_fields);
$tmp_select_sql  .= $search_sql;

//Create the order sting to append to the sql statement
$order_by = createSortSQLString($table_columns, $table_columns[1]['mysql_field'], 'DESC');
$tmp_select_sql  .=  " ORDER BY $order_by";


$tmp_select_sql  .=  " LIMIT 100";
$dbc = openPOSdb();
$result = runTransactionSQL($dbc,$tmp_sql);
$data = getTransactionSQL($dbc,$tmp_select_sql);
closeDB($dbc);

//create some buttons
//Add a button to add an expense
$html .= '<p>';
$html .= '<input class = "button" type="button" style="width:300px" name="add_category" value="Add Sales Tax Category" onclick="open_win(\'add_edit_sales_tax_category.php?type=Add\')"/>';
$html .= '</p>';
//now make the table
$html .= createSearchForm($search_fields,$action);
$html .= createRecordsTableWithTotals($data, $table_columns);
$html .= '<script>document.getElementsByName("tax_category_name")[0].focus();</script>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
?>
