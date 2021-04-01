<?php 

/*
	The process to create a new db entry for things like mfg, employee, etc is to define what we want in the table, then create that table....event post the table in json format for the handler to process?
	
	Craig Iannazzi 10-23-12
	
*/

$page_title = 'View Product Sub ID';
$binder_name = 'Product Sub Ids';
$access_type = 'READ';
require_once ('../product_functions.php');


$pos_product_sub_id = getPostOrGetID('pos_product_sub_id');
$pos_product_id = getProductIdFromProductSubId($pos_product_sub_id);;
$complete_location = '../ViewProduct/view_product.php?pos_product_id='.$pos_product_id;
$cancel_location = $complete_location;
$edit_location = 'add_edit_product_sub_id.php?pos_product_sub_id='.$pos_product_sub_id. '&pos_product_id='.$pos_product_id.'&type=edit';
$delete_location = 'delete_product_sub_id.form.handler.php?pos_product_id='.$pos_product_id;

$db_table = 'pos_products_sub_id';
$key_val_id['pos_product_sub_id']  = $pos_product_sub_id;
$data_table_def = createProductSUBIDTableDef('View', $pos_product_sub_id, $pos_product_id);	
$table_def_w_data = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def);


	//now the delete
	
	

$html = printGetMessage('message');
$html .= '<p>View Location</p>';
$html .= confirmDelete($delete_location);
$html .= createHTMLTableForMYSQLData($table_def_w_data);
$html .= '<p><input class = "button"  type="button" name="edit"  value="Edit (DANGER!!)" onclick="open_win(\''.$edit_location.'\')"/>';
$html .= '</p>';
$html .= '<p>';

$data =  getProductSubIdOptions($pos_product_sub_id);
$array_table_def= createDynamicSubIdOptionTableDef($pos_product_sub_id);
$html .= createStaticViewDynamicTable($array_table_def, $data, ' style = "width:100%;" ');

$html .= '<input class = "button"  type="button" name="edit"  style = "width:300px;" value="Edit Sub Id Options (DANGER!!)" onclick="open_win(\'edit_sub_id_options.php?pos_product_sub_id='.$pos_product_sub_id.'\')"/>';

//give an inventory overview table

//now the inventory
//want to see the sub id, the date of last physical count, the location, the quantity

//then we want to see how many were sold and how many were brought in
//counted 1 in the plastic bin 12-1-2000
//counted two in the sale bin 1-1-2000
//emptied the plastic bin 12-14-200 and moved one into the sale bin did not re-inventory the sale bin
//counted three in the bra bin 1-14-2000

//received one 12-12-2000
//received one 1-3-2000
//received 3 1-17-2000

//sold one 12-14-2000
//sold one 1-4-2000
//sold one 1-14-2000

// the count should be: 1 + 2 + 3 + 1 + 1 + 3 - 1 - 1 - 1 = 9 

//look like this:
//date 	store	location	sold/received/counted	quantity	type	value

//for each store
$stores = getSQL("SELECT pos_store_id, store_name FROM pos_stores");

for($s=0;$s<sizeof($stores);$s++)
{
	echo 'INVENTORY DATA......... ';
	$pos_store_id = $stores[$s]['pos_store_id'];
	preprint('store id'  . $stores[$s]['store_name']);
//by store id???
	$inventory_start_date = getSingleValueSQL("SELECT max(date(inventory_start_date)) from pos_inventory_complete_dates where pos_store_id = $pos_store_id");
	$inventory_end_date = getSingleValueSQL("SELECT max(date(inventory_end_date)) from pos_inventory_complete_dates where pos_store_id = $pos_store_id");
	
	preprint($inventory_start_date);
	preprint($inventory_end_date);
	$inventory_contents = getSQL("SELECT pos_location_id, quantity, inventory_date FROM pos_inventory_event_contents 
							LEFT JOIN pos_inventory_event  USING(pos_inventory_event_id)
							WHERE pos_product_sub_id = $pos_product_sub_id 
							AND pos_locations.pos_store_id = $pos_store_id
							AND date(inventory_date) BETWEEN '$inventory_start_date' AND '$inventory_end_date'");
	preprint($inventory_contents);
	
	/*echo 'RAW INVENTORY DATA......... ';
	$inventory_contents = getSQL("SELECT pos_location_id, inventory_date, quantity FROM pos_inventory_event_contents 
							LEFT JOIN pos_inventory_event  USING(pos_inventory_event_id)
							LEFT JOIN pos_locations USING (pos_location_id)
							WHERE pos_product_sub_id = $pos_product_sub_id 
							AND pos_locations.pos_store_id = $pos_store_id
							");
	
	preprint($inventory_contents);*/
	
	
	

	echo 'RECEIVE DATA......... ';
	$receive = getSQL("SELECT receive_date, received_quantity, cost
	FROM pos_purchase_order_receive_event 
	LEFT JOIN pos_purchase_order_receive_contents USING (pos_purchase_order_receive_event_id) 
	LEFT JOIN pos_purchase_order_contents USING (pos_purchase_order_content_id)
	WHERE pos_product_sub_id = $pos_product_sub_id
	and date(receive_date) >= 
	AND pos_store_id = $pos_store_id");
	

	preprint( $receive);
	
	echo 'SOLD DATA......... ';
	
	$sold = getSQL("SELECT invoice_date, quantity, sale_price
	FROM pos_sales_invoice
	LEFT JOIN pos_sales_invoice_contents USING(pos_sales_invoice_id)
	WHERE pos_store_id = $pos_store_id
	AND pos_product_sub_id = $pos_product_sub_id");
	
preprint( $sold);
	

}






//records table
$tmp_sql = "Create Temporary Table p
		SELECT a.pos_location_id, pos_product_sub_id, a.pos_store_id, inventory_date, location_name, 'Physical Count' as source, quantity, inventory_type, value, store_name, a.comments
		FROM pos_inventory_log a 
		LEFT JOIN pos_locations ON a.pos_location_id = pos_locations.pos_location_id
		LEFT JOIN pos_stores ON pos_locations.pos_store_id = pos_stores.pos_store_id
		INNER JOIN
		(
			SELECT MAX(inventory_date) as MaxDateTime, pos_location_id
			FROM pos_inventory_log
		) b ON a.inventory_date = b.MaxDateTime AND a.pos_location_id = b.pos_location_id
		;";	
		
$tmp_sql = "Create Temporary Table p
		SELECT a.pos_location_id, pos_product_sub_id, a.pos_store_id, max(inventory_date) as inventory_date, location_name, 'Physical Count' as source, quantity, inventory_type, value, store_name, a.comments
		FROM pos_inventory_log a 
		LEFT JOIN pos_locations ON a.pos_location_id = pos_locations.pos_location_id
		LEFT JOIN pos_stores ON pos_locations.pos_store_id = pos_stores.pos_store_id
		
		;";	
		$max_inventory_date = getSingleValueSQL("SELECT max(inventory_date) from pos_inventory_log WHERE pos_product_sub_id = $pos_product_sub_id");
$tmp_sql = "Create Temporary Table p
		SELECT a.pos_location_id, pos_product_sub_id, a.pos_store_id, inventory_date, location_name, 'Physical Count' as source, quantity, inventory_type, value, store_name, a.comments
		FROM pos_inventory_log a 
		LEFT JOIN pos_locations ON a.pos_location_id = pos_locations.pos_location_id
		LEFT JOIN pos_stores ON pos_locations.pos_store_id = pos_stores.pos_store_id
		WHERE pos_inventory_log_id IN (SELECT MAX(pos_inventory_log_id) FROM pos_inventory_log GROUP BY pos_location_id)
		AND pos_product_sub_id=$pos_product_sub_id
		UNION
		SELECT 0 as pos_location_id, pos_product_sub_id, pos_store_id, pos_purchase_orders.received_date, 'Receive Location' as location_name, 'Receive' as source, quantity_received, 'New' as inventory_type, cost as value, store_name, pos_purchase_order_contents.comments
		FROM pos_purchase_order_contents 
		LEFT JOIN pos_purchase_orders USING(pos_purchase_order_id)
		LEFT JOIN pos_stores USING (pos_store_id)
		WHERE pos_purchase_orders.received_date >= '$max_inventory_date'
		AND pos_product_sub_id=$pos_product_sub_id
		;";			
	
	$tmp_sql = "Create Temporary Table p
		SELECT a.pos_location_id, pos_product_sub_id, a.pos_store_id, inventory_date, location_name, 'Physical Count' as source, quantity, inventory_type, value, store_name, a.comments
		FROM pos_inventory_log a
		LEFT JOIN pos_locations ON a.pos_location_id = pos_locations.pos_location_id
		LEFT JOIN pos_stores ON pos_locations.pos_store_id = pos_stores.pos_store_id
		WHERE inventory_date = (SELECT MAX(inventory_date) FROM pos_inventory_log b WHERE b.pos_location_id = a.pos_location_id)
		AND pos_product_sub_id=$pos_product_sub_id
		
		UNION
		SELECT 0 as pos_location_id, pos_product_sub_id, pos_store_id, pos_purchase_orders.received_date, 'Receive Location' as location_name, 'Receive' as source, quantity_received, 'New' as inventory_type, cost as value, store_name, pos_purchase_order_contents.comments
		FROM pos_purchase_order_contents 
		LEFT JOIN pos_purchase_orders USING(pos_purchase_order_id)
		LEFT JOIN pos_stores USING (pos_store_id)
		WHERE pos_purchase_orders.received_date >= '$max_inventory_date'
		AND pos_product_sub_id=$pos_product_sub_id
		;";
		
		//echo getSQL("SELECT MAX(pos_inventory_log_id) FROM pos_inventory_log GROUP BY pos_location_id");
		/*
//when I receive should I enter it as a physical count at receive....?	
down vote
accepted
This should work for you.

 SELECT * 
 FROM [tableName] 
 WHERE id IN (SELECT MAX(id) FROM [tableName] GROUP BY code)
	
	
	
	SELECT tt.*
FROM topten tt
INNER JOIN
    (
    SELECT home, MAX(datetime) AS MaxDateTime
    FROM topten
    GROUP BY home
    ) groupedtt ON tt.home = groupedtt.home AND tt.datetime = groupedtt.MaxDateTime
		*/
		
	$tmp_select_sql = "SELECT *
		FROM p WHERE 1 ORDER BY store_name ASC, inventory_date DESC";
	//create the search form
		$table_columns = array(

		array(
			'th' => 'View<br>Location<br>Details',
			'mysql_field' => 'pos_location_id',
			'get_url_link' => "view_location.php",
			'url_caption' => 'View',
			'get_id_link' => 'pos_location_id'),
		array(
			'th' => 'Date',
			'mysql_field' => 'inventory_date',
			'sort' => 'inventory_date'),
		array(
			'th' => 'Store',
			'mysql_field' => 'store_name',
			'sort' => 'store_name'),
		array(
			'th' => 'Location Name',
			'mysql_field' => 'location_name',
			'sort' => 'location_name'),
		array(
			'th' => 'Inventory<br>Source',
			'mysql_field' => 'source',
			'sort' => 'source'),
		array(
			'th' => 'Quantity',
			'mysql_field' => 'quantity',
			'total' => 2,
			'sort' => 'quantity'),
			array(
			'th' => 'Inventory<BR>Type',
			'mysql_field' => 'inventory_type',
			'sort' => 'inventory_type'),
		array(
			'th' => 'Value',
			'mysql_field' => 'value',
			'sort' => 'value'),
		array(
			'th' => 'Comments',
			'mysql_field' => 'comments',
			'sort' => 'comments')
		
		);
	
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,$tmp_sql);
	$data = getTransactionSQL($dbc,$tmp_select_sql);
	closeDB($dbc);
	$html .= createRecordsTableWithTotals($data, $table_columns);
	

$html .= '</p>';
$html .= '<p>';
$html .= '<INPUT class = "button" type="button" style ="width:200px" value="Back to Product" onclick="window.location = \''.$complete_location.'\'" />';
$html .= '</p>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);


	




?>