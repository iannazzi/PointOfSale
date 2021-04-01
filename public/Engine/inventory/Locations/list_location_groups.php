<?php
/*
	*shows a list of all registered manufacturers
*/
$page_title = 'Locations';
$binder_name = 'Locations';
$access_type = 'READ';
require_once ('../inventory_functions.php');

if(isset($_POST['print_labels']))
{
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,urldecode($_POST['tmp_sql']));
	$data = getTransactionSQL($dbc,urldecode($_POST['tmp_select_sql']));
	closeDB($dbc);
	$filename = 'location_groups.pdf';
	$html = printLocationLabelsForm($data, $filename);
}
else
{
	$search_fields = array(				array(	'db_field' => 'pos_location_group_id',
											'mysql_search_result' => 'pos_location_group_id',
											'caption' => 'Location Group ID',	
											'type' => 'input',
											'html' => createSearchInput('pos_location_group_id')
										),
										array(	'db_field' => 'location_group_name',
											'mysql_search_result' => 'location_group_name',
											'caption' => 'Location Group Name',	
											'type' => 'input',
											'html' => createSearchInput('location_group_name')
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
			'th' => 'View<br>Location<br>Details',
			'mysql_field' => 'pos_location_group_id',
			'get_url_link' => "view_location_group.php",
			'url_caption' => 'View',
			'get_id_link' => 'pos_location_group_id'),
		array(
			'th' => 'ID',
			'mysql_field' => 'pos_location_group_id',
			'sort' => 'pos_location_group_id'),
		array(
			'th' => 'Location Group Name',
			'mysql_field' => 'location_group_name',
			'sort' => 'location_group_name'),
		array(
			'th' => 'Active',
			'mysql_field' => 'active',
			'sort' => 'active'),
		array(
			'th' => 'Comments',
			'mysql_field' => 'comments',
			'sort' => 'comments')
		
		);
	//saved search functionality
	$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saved_group_locations');
	
	//if there is a message print it
	//here is the query that the search and table arrays are built off of.
	
	//to get the current inventory value what do we do?
	// can't really do this as there is no actual link between sub id's and locationi id's
	$tmp_sql = "
	CREATE TEMPORARY TABLE tmp
	SELECT  
			pos_location_group_id, location_group_name, comments, active
			FROM pos_location_groups
	;
	";
	$tmp_select_sql = "SELECT *
		FROM tmp WHERE 1";
	//create the search form
	$action = 'list_locations_groups.php';
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
	
	//Build search the page
	$html = printGetMessage('message');
	$html .= '<p>';
	$html .= '<input class = "button" type="button" style="width:300px" name="add_location_group" value="Add Location Group" onclick="open_win(\'add_edit_location_group.php?type=Add\')"/>';
	//$html .= '<input class = "button" type="button" style="width:300px" name="bulk_add_location_groups" value="Bulk Add Location Groups" onclick="open_win(\'bulk_add_location_groups.php\')"/>';

	$html .= '</p>';
	//now make the table
	$html .= createSearchForm($search_fields,$action);
	$html .= createRecordsTableWithTotals($data, $table_columns);
	$html .= '<script>document.getElementsByName("location_group_name")[0].focus();</script>';
		
	/*
	$form_handler = "list_location_groups.php";
	$html .= '<form action="' . $form_handler.'" method="post">';
	$html .= createHiddenInput('tmp_sql', urlencode($tmp_sql));
	$html .= createHiddenInput('tmp_select_sql', urlencode($tmp_select_sql));
	$html .= createHiddenInput('search_sql', urlencode($search_sql));
	$html .= '<input class = "button" style="width:150px" type="submit" name="print_labels" value="Print Labels"/>';
	$html .= '</form>';
	*/
					
}


include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
?>
