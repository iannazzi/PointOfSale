<?php 

/*
	The process to create a new db entry for things like mfg, employee, etc is to define what we want in the table, then create that table....event post the table in json format for the handler to process?
	
	Craig Iannazzi 10-23-12
	
*/

$page_title = 'View Location';
$binder_name = 'Locations';
$access_type = 'READ';
require_once ('../inventory_functions.php');

$complete_location = 'list_location_groups.php';
$cancel_location = 'list_location_groups.php?message=Canceled';
$pos_location_group_id = getPostOrGetID('pos_location_group_id');
$edit_location = 'add_edit_location_group	.php?pos_location_group_id='.$pos_location_group_id.'&type=edit';
$delete_location = 'delete_location.form.handler.php?pos_location_id='.$pos_location_group_id;

$db_table = 'pos_location_groups';
$key_val_id['pos_location_group_id']  = $pos_location_group_id;
$data_table_def = createLocationGroupTableDef('View', $pos_location_group_id);
$table_def_w_data = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def);


	//now the delete
	
	

$html = printGetMessage('message');
$html .= '<p>View Location Group</p>';
$html .= confirmDelete($delete_location);
$html .= createHTMLTableForMYSQLData($table_def_w_data);
$html .= '<p><input class = "button"  type="button" name="edit"  value="Edit" onclick="open_win(\''.$edit_location.'\')"/>';
	// $html .= '<input class = "button" type="button" name="delete" value="Delete Room" onclick="confirmDelete();"/>';

$html .= '<p>';
$html .= '<INPUT class = "button" type="button" style ="width:200px" value="Back to Location Groups" onclick="window.location = \''.$complete_location.'\'" />';
$html .= '</p>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);






?>