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
	$filename = 'locations.pdf';
	$html = printLocationLabelsForm($data, $filename);
}
else if(isset($_POST['bulk_edit']))
{
	$dbc = openPOSdb();
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
	$html .= createFormForMYSQLInsert($table_array, $html_table, $form_handler, $complete_location, $cancel_location);
	
	
	

}
else
{
	$search_fields = array(				array(	'db_field' => 'pos_location_id',
											'mysql_search_result' => 'pos_location_id',
											'caption' => 'Location ID',	
											'type' => 'input',
											'html' => createSearchInput('pos_location_id')
										),
									/*	array(	'db_field' => 'barcode',
											'mysql_search_result' => 'barcode',
											'caption' => 'barcode',	
											'type' => 'input',
											'html' => createSearchInput('barcode')
										),
										array(	'db_field' => 'location_name',
											'mysql_search_result' => 'location_name',
											'caption' => 'Location Name',	
											'type' => 'input',
											'html' => createSearchInput('location_name')
										),*/
										array(	'db_field' => 'location_group_name',
											'mysql_search_result' => 'location_group_name',
											'caption' => 'Location Group Name',	
											'type' => 'input',
											'html' => createSearchInput('location_group_name')
										),
										array(	'db_field' => 'store_name',
											'mysql_search_result' => 'store_name',
											'caption' => 'Site Name',	
											'type' => 'input',
											'html' => createSearchInput('store_name')
										),
										/*array(	'db_field' => 'parent_name',
											'mysql_search_result' => 'parent_name',
											'caption' => 'Parent Location Name',	
											'type' => 'input',
											'html' => createSearchInput('parent_name')
										),*/
										array(	'db_field' => 'parent_child',
											'mysql_search_result' => 'parent_child',
											'caption' => 'Full<br>Location Name',	
											'type' => 'input',
											'html' => createSearchInput('parent_child')
										),
										array(	'db_field' => 'last_inventory_date',
											'mysql_search_result' => 'last_inventory_date',
											'caption' => 'Most Recent <br>Inventory Date Start',
											'type' => 'start_date',
											'html' => dateSelect('last_inventory_date_start_date',valueFromGetOrDefault('last_inventory_date_start_date'))
										),
									array(	'db_field' => 'last_inventory_date',
											'mysql_search_result' => 'last_inventory_date',
											'caption' => 'Most Recent <br> Inventory Date End',	
											'type' => 'end_date',
											'html' => dateSelect('last_inventory_date_end_date',valueFromGetOrDefault('last_inventory_date_end_date'))
											),
										
										array(	'db_field' => 'comments',
											'mysql_search_result' => 'comments',
											'caption' => 'Comments',	
											'type' => 'input',
											'html' => createSearchInput('comments')
										),
										array(	'db_field' => 'inventory_contents',
											'mysql_search_result' => 'inventory_contents',
											'caption' => 'Inventory<br>Contents',	
											'type' => 'input',
											'html' => createSearchInput('inventory_contents')
										)
										
										);
	$table_columns = array(

		array(
			'th' => 'View<br>Location<br>Details',
			'mysql_field' => 'pos_location_id',
			'get_url_link' => "inventory.php?type=view",
			'url_caption' => 'View',
			'get_id_link' => 'pos_location_id'),
		array(
			'th' => 'ID',
			'mysql_field' => 'pos_location_id',
			'sort' => 'pos_location_id'),
		/*array(
			'th' => 'barcode',
			'mysql_field' => 'barcode',
			'sort' => 'barcode'),*/
		/*array(
			'th' => 'Location Name',
			'mysql_field' => 'location_name',
			'sort' => 'location_name'),*/
		array(
			'th' => 'Site Name',
			'mysql_field' => 'store_name',
			'sort' => 'store_name'),
		array(
			'th' => 'Location <br>Group',
			'mysql_field' => 'location_group_name',
			'sort' => 'location_group_name'),
		
		/*array(
			'th' => 'Parent Location Name',
			'mysql_field' => 'parent_name',
			'sort' => 'parent_name'),*/
			array(
			'th' => 'Full Location Name',
			'mysql_field' => 'parent_child',
			'sort' => 'parent_child'),
		
				
		array(
			'th' => 'Most Recent <br> Inventory Date',
			'mysql_field' => 'last_inventory_date',
			'sort' => 'last_inventory_date'),
		array(
			'th' => 'Last Inventory Cost',
			'mysql_field' => 'last_inventory_cost',
			'sort' => 'last_inventory_cost',
			'total' => 0,
			'round' => 2),
		array(
			'th' => 'Last Inventory Value',
			'mysql_field' => 'last_inventory_value',
			'sort' => 'last_inventory_value',
			'total' => 0,
			'round' => 2),
		/*array(
			'th' => 'Current Inventory Value<BR>(ESTIMATED)',
			'mysql_field' => 'last_inventory_value',
			'sort' => 'last_inventory_value',
			'total' => 2,
			'round' => 2),*/
		array(
			'th' => 'Comments',
			'mysql_field' => 'comments',
			'sort' => 'comments'),
		
		
		);
	//saved search functionality
	$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saved_locations');
	
	//if there is a message print it
	//here is the query that the search and table arrays are built off of.
	

	//preprint(getSQL($test_sql));
	
	
	$tmp_sql = "
	CREATE TEMPORARY TABLE tmp
	SELECT  
			pos_locations.pos_location_id, 
			pos_locations.pos_store_id, 
			if(pos_locations.pos_parent_location_id = 0, pos_locations.location_name, concat(x.location_name,'-',pos_locations.location_name)) as parent_child, 
			pos_locations.pos_parent_location_id, 
			concat('LOCATION::' ,pos_locations.pos_location_id) as barcode, 
			x.location_name as parent_name, 
			(SELECT sum(quantity*value) FROM pos_inventory_event_contents
			LEFT JOIN pos_inventory_event USING (pos_inventory_event_id)
			WHERE pos_location_id=pos_locations.pos_location_id 
			AND inventory_date = (SELECT max(inventory_date) 
			FROM pos_inventory_event WHERE pos_location_id = pos_locations.pos_location_id)) as last_inventory_value,
			(SELECT max(inventory_date) FROM pos_inventory_event 
WHERE pos_location_id = pos_locations.pos_location_id) as last_inventory_date,
			(SELECT sum(quantity*cost) FROM pos_inventory_event_contents
			LEFT JOIN pos_inventory_event USING (pos_inventory_event_id)
			LEFT JOIN pos_products_sub_id USING(pos_product_sub_id)
			LEFT JOIN pos_products USING (pos_product_id)
			WHERE pos_location_id=pos_locations.pos_location_id 
			AND inventory_date = (SELECT max(inventory_date) 
			FROM pos_inventory_event WHERE pos_location_id = pos_locations.pos_location_id)) as last_inventory_cost,
			(SELECT GROUP_CONCAT(pos_product_sub_id) FROM pos_inventory_event_contents
			LEFT JOIN pos_inventory_event USING (pos_inventory_event_id)
			WHERE pos_location_id=pos_locations.pos_location_id 
			AND inventory_date = (SELECT max(inventory_date) 
			FROM pos_inventory_event WHERE pos_location_id = pos_locations.pos_location_id)) as inventory_contents,
			
			pos_locations.location_name, 
			pos_stores.store_name, 
			pos_locations.comments,
			pos_location_groups.location_group_name, 
			pos_location_groups.pos_location_group_id
						
			FROM pos_locations
			LEFT JOIN pos_stores ON pos_locations.pos_store_id = pos_stores.pos_store_id
			LEFT JOIN pos_locations as x ON pos_locations.pos_parent_location_id = x.pos_location_id
			LEFT JOIN pos_location_groups ON pos_locations.pos_location_group_id = pos_location_groups.pos_location_group_id
			

	;
	";
		
	$tmp_select_sql = "SELECT *
		FROM tmp WHERE 1";
	//create the search form
	$action = 'list_locations.php';
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
	
	$html .= '<h2>Inventory Procedure</h2>

<h3>** Because we need to count inventory by location and adjust location inventory for items you have sold during inventory counting it is important to count as quickly as possible with minimum interruptions to counted locations. **</h3>

For an accurate and timely counting of inventory perform the following steps. 
<ol style = "margin-left:10px">
<li> Create locations. Locations allow for dividing of inventory counting into smaller piles of work.</li>
<li> Create an inventory event. An inventory event is a record of the start date and the end date when counting inventory. Put the correct start date in, and put an anticipated end date.</li>
<li> Count each location and add a physical tag to the location to mark it counted.</li>
<li> If you sell an item from a location it has to removed from the location inventory.</li> 
<li> If you receive items you must inventory them to a location. Its your choice to receive then count it in inventory or hold the new packages until completion of inventory.</li>
<li> Due to the speed required for accurate inventory count it may not be worth counting items that are discounted, sale, clearance, damaged, etc. Place these items into a dingy dark corner with hazard tape around them before starting inventory.</li>
<li> The locations show the inventory at the most recent date. Sorting by date may show location never counted and locations that may have been counted years ago. It may be worthwhile to clean up old location inventory by either deactivating them or by re-counting them, possibly zeroing out the contents.</li>
<li> At the end of the inventory counting adjust the inventory end date and time. Sales after the set date and time will now pull from inventory and receive events after the inventory date time will put items into inventory.</li>
</ol>
The inventory quantities are now set to the items counted between the inventory start and inventory end dates.';

	$html .= '</p>';
	$html .='<p>';
	
	//$html .=createUserButton('Merchandise Inventory');
	$html .= '<input class = "button" type="button" style="width:300px" name="add_location" value="Add Location" onclick="open_win(\'add_edit_location.php?type=Add\')"/>';
	$html .= '<input class = "button" type="button" style="width:300px" name="bulk_add_location" value="Bulk Add Locations" onclick="open_win(\'bulk_add_location.php\')"/>';
	$html .= '<input class = "button" type="button" style="width:300px" name="location_groups" value="Location Groups" onclick="open_win(\'list_location_groups.php\')"/>';
	

	$html .= '</p>';
	//now make the table
	$html .= createSearchForm($search_fields,$action);
	$html .= createRecordsTableWithTotals($data, $table_columns);
	$html .= '<script>document.getElementsByName("barcode")[0].focus();document.getElementsByName("barcode")[0].select();</script>';
		
	//create a table that displays this data....
	$form_handler = "list_locations.php";
	$html .= '<form action="' . $form_handler.'" method="post">';
	$html .= createHiddenInput('tmp_sql', urlencode($tmp_sql));
	$html .= createHiddenInput('tmp_select_sql', urlencode($tmp_select_sql));
	$html .= createHiddenInput('search_sql', urlencode($search_sql));
	$html .= '<input class = "button" style="width:150px" type="submit" name="print_labels" value="Print Labels"/>';
	$html .= '<input class = "button" style="width:250px" type="submit" name="bulk_edit" value="Bulk Edit These Locations"/>';
	$html .= '</form>';
					
}


include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
?>
