<?php
/*
	*shows a list of all registered manufacturers
*/
$page_title = 'Attributes';
$binder_name = 'Product Attributes';
$access_type = 'READ';
require_once ('../product_functions.php');

if(isset($_POST['print_labels']))
{
	/*$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,urldecode($_POST['tmp_sql']));
	$data = getTransactionSQL($dbc,urldecode($_POST['tmp_select_sql']));
	closeDB($dbc);
	$filename = 'locations.pdf';
	$html = printLocationLabelsForm($data, $filename);*/
}
else if(isset($_POST['bulk_edit']))
{
	/*$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,urldecode($_POST['tmp_sql']));
	$data = getTransactionSQL($dbc,urldecode($_POST['tmp_select_sql']));
	closeDB($dbc);
	
	$header = '<p class="error">Bulk EDIT Locations - NOTE THIS IS NOT CURRENTLY BEING VALIDATED - Names Must have a unique combination of store, parent, and location name. I.E. Two "SHELVES" in Store 1 cannot exist. Also Two "SHELVES" in pittsford in the basement cannot exist. Example name would be SHELVES - 01 and SHELVES - 02</p>';
	
	$table_def = createbulkEditLocationTableDef();
	//take off the checkbox.... 
	$html_table = createStaticArrayHTMLTable($table_def, $data);
	
	
	$html = $header;
	
	$complete_location = 'list_locations.php';
	$cancel_location = $complete_location;
	
	$form_handler = 'bulk_edit_location.form.handler.php';
	$table_array = array($table_def);
	$html .= createFormForMYSQLInsert($table_array, $html_table, $form_handler, $complete_location, $cancel_location);*/
	
	
	

}
else
{
	$search_fields = array(				array(	'db_field' => 'pos_product_attribute_id',
											'mysql_search_result' => 'pos_product_attribute_id',
											'caption' => 'Attribute ID',	
											'type' => 'input',
											'html' => createSearchInput('pos_product_attribute_id')
										),
										
										array(	'db_field' => 'attribute_name',
											'mysql_search_result' => 'attribute_name',
											'caption' => 'Attribute Name',	
											'type' => 'input',
											'html' => createSearchInput('attribute_name')
										),
										array(	'db_field' => 'priority',
											'mysql_search_result' => 'priority',
											'caption' => 'Priority',	
											'type' => 'input',
											'html' => createSearchInput('priority')
										),
										array(	'db_field' => 'active',
											'mysql_search_result' => 'active',
											'caption' => 'Active',	
											'type' => 'input',
											'html' => createSearchInput('active')
										),
										array(	'db_field' => 'comments',
											'mysql_search_result' => 'comments',
											'caption' => 'Comments',	
											'type' => 'input',
											'html' => createSearchInput('comments')
										)
										
										
										
										);
	$table_columns = array(

		array(
			'th' => 'View<br>Attribute',
			'mysql_field' => 'pos_product_attribute_id',
			'get_url_link' => "view_attribute.php",
			'url_caption' => 'View',
			'get_id_link' => 'pos_product_attribute_id'),
		array(
			'th' => 'ID',
			'mysql_field' => 'pos_product_attribute_id',
			'sort' => 'pos_product_attribute_id'),

		array(
			'th' => 'Attribute Name',
			'mysql_field' => 'attribute_name',
			'sort' => 'attribute_name'),
		array(
			'th' => 'Priority',
			'mysql_field' => 'priority',
			'sort' => 'priority'),
		array(
			'th' => 'Active',
			'mysql_field' => 'active',
			'sort' => 'active'),
		array(
			'th' => 'Comments',
			'mysql_field' => 'comments',
			'sort' => 'comments'),
			
		
		);
	//saved search functionality
	$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saved_attributes');
	
	//if there is a message print it
	//here is the query that the search and table arrays are built off of.
	
	//to get the current inventory value what do we do?
	// can't really do this as there is no actual link between sub id's and locationi id's
	$tmp_sql = "
	CREATE TEMPORARY TABLE tmp
	SELECT  
			pos_product_attribute_id, attribute_name, comments, priority, active FROM pos_product_attributes
	;
	";
	$tmp_select_sql = "SELECT *
		FROM tmp WHERE 1";
	//create the search form
	$action = 'list_attributes.php';
	//Create Search String (AND's after MYSQL WHERE from $_GET data)
	$search_sql = createSearchSQLStringMultipleDates($search_fields);
	$tmp_select_sql  .= $search_sql;
	//Create the order sting to append to the sql statement
	$order_by = createSortSQLString($table_columns, $table_columns[1]['mysql_field'], 'ASC');
	$tmp_select_sql  .=  " ORDER BY $order_by";
	
	
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,$tmp_sql);
	$data = getTransactionSQL($dbc,$tmp_select_sql);
	closeDB($dbc);
	
	//Build search the page
	$html = printGetMessage('message');
	$html .= '<p>';
	$html .= '<input class = "button" type="button" style="width:300px" name="add_location" value="Add Attribute" onclick="open_win(\'add_edit_attribute.php?type=Add\')"/>';

	

	$html .= '</p>';
	//now make the table
	$html .= createSearchForm($search_fields,$action);
	$html .= createRecordsTableWithTotals($data, $table_columns);
	$html .= '<script>document.getElementsByName("attribute_name")[0].focus();</script>';
		
	//create a table that displays this data....
	$form_handler = "list_attributes.php";
	$html .= '<form action="' . $form_handler.'" method="post">';
	$html .= createHiddenInput('tmp_sql', urlencode($tmp_sql));
	$html .= createHiddenInput('tmp_select_sql', urlencode($tmp_select_sql));
	$html .= createHiddenInput('search_sql', urlencode($search_sql));
	//$html .= '<input class = "button" style="width:150px" type="submit" name="print_labels" value="Print Labels"/>';
	//$html .= '<input class = "button" style="width:250px" type="submit" name="bulk_edit" value="Bulk Edit These Locations"/>';
	$html .= '</form>';
					
}


include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
?>
