<?php
/*
	*shows a list of all registered manufacturers
*/
$page_title = 'Inventory Events';
$binder_name = 'Locations';
$access_type = 'READ';
require_once ('../inventory_functions.php');

$search_fields = array(				array(	'db_field' => 'pos_inventory_complete_date',
											'mysql_search_result' => 'pos_inventory_complete_date',
											'caption' => 'System ID',	
											'type' => 'input',
											'html' => createSearchInput('pos_inventory_event_id')
										),
										array(	'db_field' => 'store_name',
											'mysql_search_result' => 'store_name',
											'caption' => 'Store',	
											'type' => 'input',
											'html' => createSearchInput('pos_inventory_event_id')
										),
										array(	'db_field' => 'inventory_start_date',
											'mysql_search_result' => 'inventory_start_date',
											'caption' => 'Inventory Date Start',
											'type' => 'date',
											'html' => dateSelect('inventory_start_date',valueFromGetOrDefault('inventory_start_date'))
										),
								array(	'db_field' => 'end_date',
											'mysql_search_result' => 'inventory_end_date',
											'caption' => 'Inventory End Date',	
											'type' => 'date',
											'html' => dateSelect('inventory_end_date',valueFromGetOrDefault('inventory_end_date'))
											),

									
								
										);
$table_columns = array(
		array(
			'th' => 'View',
			'mysql_field' => 'pos_inventory_complete_date_id',
			'get_url_link' => "inventory_event.php?type=View",
			'url_caption' => 'View',
			'get_id_link' => 'pos_inventory_complete_date_id'),
		array(
			'th' => 'ID',
			'mysql_field' => 'pos_inventory_complete_date_id',
			'sort' => 'pos_inventory_complete_date_id'),	
		array(
			'th' => 'Store Name',
			'mysql_field' => 'store_name',
			'sort' => 'store_name'),
		array(
			'th' => 'Start Date',
			'mysql_field' => 'inventory_start_date',
			'date_format' => 'date',
			'sort' => 'inventory_start_date'),
		array(
			'th' => 'End Date',
			'mysql_field' => 'inventory_end_date',
			'date_format' => 'date',
			'sort' => 'inventory_end_date'),
		array(
			'th' => 'Total Amount',
			'mysql_field' => 'total_inventory_amount',
			'sort' => 'total_inventory_amount'),	

		);
$html = printGetMessage('message');	
//saved search functionality
$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saved_inventory_event');

//if there is a message print it
$html = printGetMessage('message');
//here is the query that the search and table arrays are built off of.

//date and store id from pos_inventory_complete_dates
//pos_inventory_event date and location => store
//pos_prodcut_sub_id from inventory_event_contents
//sub_id to PO? LIFO FIFO Average?

//this is wrong as we need lifo fifo cal to get the cost... that is gnarly dude...

//the best way to do this... get an array of the product subs and the quantity.
//go through those and calc the cost.


$sql = " CREATE TEMPORARY TABLE inventory

SELECT  
		
		
		pos_inventory_event.pos_inventory_event_id, pos_inventory_event.inventory_date, pos_locations.pos_location_id, location_name,
		
		(SELECT sum(quantity*cost) FROM pos_inventory_event_contents  
		LEFT JOIN pos_products_sub_id ON pos_inventory_event_contents.pos_product_sub_id = pos_products_sub_id.pos_product_sub_id
		LEFT JOIN pos_products ON pos_products.pos_product_id = pos_products_sub_id.pos_product_id
		WHERE pos_inventory_event.pos_inventory_event_id = pos_inventory_event_contents.pos_inventory_event_id) as total
		
		FROM pos_inventory_event
		LEFT JOIN pos_locations ON pos_locations.pos_location_id = pos_inventory_event.pos_location_id
		ORDER BY pos_inventory_event.pos_inventory_event_id;";
	
	
$tmp_select_sql = "SELECT *
	FROM inventory ";		
$dbc = openPOSdb();
$result = runTransactionSQL($dbc,$sql);
$inventory_data = getTransactionSQL($dbc,$tmp_select_sql);
closeDB($dbc);




$tmp_sql = "
CREATE TEMPORARY TABLE inventory

SELECT  
		pos_inventory_complete_dates.*, pos_stores.store_name,
		
		(SELECT sum(quantity*cost) FROM pos_inventory_event_contents  
		LEFT JOIN pos_products_sub_id ON pos_inventory_event_contents.pos_product_sub_id = pos_products_sub_id.pos_product_sub_id
		LEFT JOIN pos_products ON pos_products.pos_product_id = pos_products_sub_id.pos_product_id
		LEFT JOIN pos_inventory_event ON pos_inventory_event.pos_inventory_event_id = pos_inventory_event_contents.pos_inventory_event_id
		LEFT JOIN pos_locations ON pos_inventory_event.pos_location_id = pos_locations.pos_location_id
		
		WHERE
		
		 	pos_inventory_event.inventory_date  BETWEEN 
			pos_inventory_complete_dates.inventory_start_date
			AND 
			pos_inventory_complete_dates.inventory_end_date
		AND pos_locations.pos_store_id = pos_inventory_complete_dates.pos_store_id
		
		) as total_inventory_amount
		
		
		
		
		FROM pos_inventory_complete_dates
		LEFT JOIN pos_stores ON pos_stores.pos_store_id = pos_inventory_complete_dates.pos_store_id
;
";

$tmp_sql = "
CREATE TEMPORARY TABLE inventory

SELECT  
		pos_inventory_complete_dates.*, pos_stores.store_name
		FROM pos_inventory_complete_dates
		LEFT JOIN pos_stores ON pos_stores.pos_store_id = pos_inventory_complete_dates.pos_store_id
;
";




$tmp_select_sql = "SELECT *
	FROM inventory WHERE 1";

//create the search form
$action = 'list_inventory_events.php';
//Create Search String (AND's after MYSQL WHERE from $_GET data)
$search_sql = createSearchSQLStringMultipleDates($search_fields);
$tmp_select_sql  .= $search_sql;

//Create the order sting to append to the sql statement
$order_by = createSortSQLString($table_columns, $table_columns[1]['mysql_field'], 'DESC');
$tmp_select_sql  .=  " ORDER BY $order_by";


//$tmp_select_sql  .=  " LIMIT 100";
$dbc = openPOSdb();
$result = runTransactionSQL($dbc,$tmp_sql);
$event_data = getTransactionSQL($dbc,$tmp_select_sql);
closeDB($dbc);


//first we need to make an array of the max dates....


//now we have to calculate the totals....
$max_date = array();
for($ie=0;$ie<sizeof($event_data);$ie++)
{
	$total = 0;
	$start_date = new DATETIME($event_data[$ie]['inventory_start_date']);
	$end_date = new DATETIME($event_data[$ie]['inventory_end_date']);
	$date_between_array = array();
	//we have to go through all the data and limit between date ranges
	for($i=0;$i<sizeof($inventory_data);$i++)
	{
		$date = new DATETIME($inventory_data[$i]['inventory_date']);
		if($date >=$start_date AND $date <=$end_date)
		{
			$date_between_array[] = $inventory_data[$i];
		}
	}
	//then we have to go through all the data and find the last inventory
	$max_date = array();
	for($i=0;$i<sizeof($date_between_array);$i++)
	{
		$pos_location_id = $date_between_array[$i]['pos_location_id'];
		if(isset($max_date[$pos_location_id]))
		{
			$tmp_date = new datetime($max_date[$pos_location_id]['inventory_date']);
			$date = new DATETIME($date_between_array[$i]['inventory_date']);
			if($date > $tmp_date)
			{
				//$max_date[$pos_location_id] = $date_between_array[$i]['pos_location_id'];
				$max_date[$pos_location_id] = $date_between_array[$i];
			}
		}
		else
		{
			//$max_date[$pos_location_id] = $date_between_array[$i]['pos_location_id'];
			$max_date[$pos_location_id] = $date_between_array[$i];

		}
	}
	
	
	//then we can calculate totals
	
	foreach($max_date as $key => $value)
	{
		$total = $total + $value['total'];
	}
	
	
	$event_data[$ie]['total_inventory_amount'] = $total;
}




//create some buttons

$html .= '<p>';
$html .= '<input class = "button" type="button" style="width:300px" name="add_invoice" value="Create Inventory Event" onclick="open_win(\'inventory_event.php?type=Add\')"/>';

$html .= '</p>';
//now make the table
$html .= createSearchForm($search_fields,$action);
$html .= createRecordsTable($event_data, $table_columns);
//$html .= '<script>document.getElementsByName("promotion_code")[0].focus();</script>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
?>

