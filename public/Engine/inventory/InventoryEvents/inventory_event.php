<?php

//inventory event craig iannazzi 2-19-2015 in mexico..

$page_title = 'Inventory Events';
$binder_name = 'Inventory Events';
	
require_once ('../inventory_functions.php');



$complete_location = '';
$cancel_location = 'list_inventory_events.php?message=canceled';
if(isset($_GET['type']))
{
	$type = $_GET['type'];
}
elseif(isset($_POST['type']))
{
	$type = $_POST['type'];
}
else
{
	trigger_error('missing type');
}
if (isset($_POST['submit'])) 
{
	$dbc = startTransaction();
	$time =  date("H:i:s");
	$table_def_array = deserializeTableDef($_POST['table_def']);
	$insert = tableDefArraytoMysqlInsertArray($table_def_array);	
	unset($insert['pos_inventory_complete_date_id']);

	//if it is new then insert, otherwise update.
	/*$insert['inventory_start_date'] = $insert['inventory_start_date'] . ' ' . scrubInput($_POST['inventory_start_date_time']);
	$insert['inventory_end_date'] = $insert['inventory_end_date'] .' '. scrubInput($_POST['inventory_end_date_time']);
	*/
	if($_POST['pos_inventory_complete_date_id'] == 'TBD')
	{
		
		//$insert['date_added'] = getCurrentTime();
		$pos_inventory_complete_date_id = simpleTransactionInsertSQLReturnID($dbc,'pos_inventory_complete_dates', $insert);
		$message = urlencode('pos_inventory_complete_date Id ' . $pos_inventory_complete_date_id . " has been added");
	}
	else
	{
		//this is an update
		$pos_inventory_complete_date_id = getPostOrGetID('pos_inventory_complete_date_id');
		$key_val_id['pos_inventory_complete_date_id'] = $pos_inventory_complete_date_id;
		$results[] = simpleTransactionUpdateSQL($dbc,'pos_inventory_complete_dates', $key_val_id, $insert);
		$message = urlencode('pos_inventory_complete_date ID ' . $pos_inventory_complete_date_id . " has been updated");
	}
	simpleCommitTransaction($dbc);
	header('Location: inventory_event.php?type=VIEW&pos_inventory_complete_date_id='.$pos_inventory_complete_date_id);
	
}
else if (isset($_POST['cancel']))
{

}
else if ($type == 'ajax')
{

}
elseif(strtoupper($type) == 'ADD' || strtoupper($type) == 'EDIT')
{
		
			
		if(strtoupper($type) == 'ADD')
		{	
			$pos_inventory_complete_date_id = 'TBD';
			$header = '<p>Add Inventory Event</p>';
			$page_title = 'Add Inventory Event';
			$data_table_def = createInventoryEventTableDef($type, $pos_inventory_complete_date_id);
			
		}
		else
		{
			$pos_inventory_complete_date_id = getPostOrGetID('pos_inventory_complete_date_id');
			$header = '<p>EDIT Inventory Event</p>';
			$page_title = 'Edit Inventory Event';
			
			$db_table = 'pos_inventory_complete_dates';
			$key_val_id['pos_inventory_complete_date_id'] = $pos_inventory_complete_date_id;
			$data_table_def = createInventoryEventTableDef($type, $pos_inventory_complete_date_id);
			$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def);
		}
		
		
		$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);
		$big_html_table .= createHiddenInput('type', $type);
		
		$html = $header;
		$form_handler = 'inventory_event.php';
		$html .= createFormForMYSQLInsert($data_table_def, $big_html_table, 		$form_handler, $complete_location, $cancel_location);
		
		INCLUDE(HEADER_FILE);
		echo $html;
		include(FOOTER_FILE);
		
	
}
elseif(strtoupper($type) == 'VIEW')
{
	$pos_inventory_complete_date_id = getPostOrGetID('pos_inventory_complete_date_id');
		$edit_location = 'inventory_event.php?pos_inventory_complete_date_id='.$pos_inventory_complete_date_id.'&type=edit';
		//$delete_location = 'delete_promotion.for.php?pos_promotion_id='.$pos_promotion_id;
		$db_table = 'pos_inventory_complete_dates';
		$key_val_id['pos_inventory_complete_date_id']  = $pos_inventory_complete_date_id;
		$data_table_def = createInventoryEventTableDef($type, $pos_inventory_complete_date_id);
		$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def);
		$html = printGetMessage('message');
		$html .= '<p>View promotion</p>';
		//$html .= confirmDelete($delete_location);

		$html .= createHTMLTableForMYSQLData($data_table_def);
		$html .= '<p><input class = "button"  type="button" name="edit"  value="Edit" onclick="open_win(\''.$edit_location.'\')"/>';
	// $html .= '<input class = "button" type="button" name="delete" value="Delete promotion" onclick="confirmDelete();"/>';
	
		$html .= '<p>';
		$html .= '<INPUT class = "button" type="button" style ="width:200px" value="Back to inventory events" onclick="window.location = \'list_inventory_events.php\'" />';
		$html .= '</p>';
		INCLUDE(HEADER_FILE);
		echo $html;
		include(FOOTER_FILE);
		
		
}
else
{
echo 'error';
exit();
}
function createInventoryEventTableDef($type, $pos_inventory_complete_date_id)
{
	if ($pos_inventory_complete_date_id =='TBD')
	{
	}
	else
	{
	}
	
	return array( 
						array( 'db_field' => 'pos_inventory_complete_date_id',
								'type' => 'input',
								'tags' => ' readonly = "readonly" ',
								'caption' => 'Sysyem ID',
								'value' => $pos_inventory_complete_date_id,
								'validate' => 'none'
								
								),
						array('db_field' => 'pos_store_id',
								'caption' => 'Store',
								'type' => 'select',
								'html' => createStoreSelect('pos_store_id', $_SESSION['store_id'],  'off'),
								'value' => $_SESSION['store_id'],
								'validate' => 'false'),	
						
						array('db_field' => 'inventory_start_date',
								'caption' => 'Start Date',
								'type' => 'date',
								'separate_date' => 'date',
								'tags' => ' ',
								'html' => dateSelect('inventory_start_date',''),
								'validate' => 'date'),
						/*array('db_field' => 'inventory_start_time',
								'caption' => 'Start Time 24h format hh:mm:ss',
								'type' => 'date',
								'tags' => ' ',
								'separate_date' => 'time',
								),*/
						array('db_field' => 'inventory_end_date',
								'caption' => 'End Date',
								'type' => 'date',
								'separate_date' => 'date',
								'tags' => ' ',
								'html' => dateSelect('inventory_end_date',''),
								'validate' => 'date'),
						/*array('db_field' => 'inventory_end_time',
								'caption' => 'End Time 24h format hh:mm:ss',
								'type' => 'date',
								'tags'=> ' ',
								'separate_date' => 'time',
								),*/
						
						array('db_field' =>  'comments',
								'type' => 'input',
								'caption' => 'Comments'),
						
						);	

}
