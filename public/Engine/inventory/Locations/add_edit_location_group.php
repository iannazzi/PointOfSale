<?php 

/*
	The process to create a new db entry for things like mfg, employee, etc is to define what we want in the table, then create that table....event post the table in json format for the handler to process?
	
	Craig Iannazzi 1-23-12
	
*/

$page_title = 'Location Group';
$binder_name = 'Locations';
$access_type = 'WRITE';
require_once ('../inventory_functions.php');


$complete_location = 'list_location_groups.php';
$cancel_location = 'list_location_groups.php?message=Canceled';
$type = getPostOrGetValue('type');
if (strtoupper($type) =='EDIT')
{
	$pos_location_group_id = getPostOrGetID('pos_location_group_id');
	$table_type = 'Edit';
	$header = '<p>EDIT Location Group</p>';
	$page_title = 'Edit Location Group';
	$data_table_def_no_data = createLocationGroupTableDef($table_type, $pos_location_group_id);	
	$db_table = 'pos_location_groups';
	$key_val_id['pos_location_group_id'] = $pos_location_group_id;
	$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def_no_data);
}
else
{
	$table_type = 'New';
	$pos_location_group_id = 'TBD';
	$header = '<p>Add location Group</p>';
	$page_title = 'Add Location';
	$data_table_def = createLocationGroupTableDef($table_type, $pos_location_group_id);
}

$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);
$big_html_table .= createHiddenInput('type', $type);

$html = $header;
$form_handler = 'add_edit_location_group.form.handler.php';
$html .= createFormForMYSQLInsert($data_table_def, $big_html_table, $form_handler, $complete_location, $cancel_location);
$html .= '<script>document.getElementsByName("location__group_name")[0].focus();</script>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);

?>

	