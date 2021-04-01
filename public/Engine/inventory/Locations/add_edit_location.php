<?php 

/*
	The process to create a new db entry for things like mfg, employee, etc is to define what we want in the table, then create that table....event post the table in json format for the handler to process?
	
	Craig Iannazzi 1-23-12
	
*/

$page_title = 'View Location';
$binder_name = 'Locations';
$access_type = 'WRITE';
require_once ('../inventory_functions.php');


$complete_location = 'list_locations.php';
$cancel_location = 'list_locations.php?message=Canceled';
$type = getPostOrGetValue('type');
if (strtoupper($type) =='EDIT')
{
	//when editing we are only editing the invoice, not payment info
	$pos_location_id = getPostOrGetID('pos_location_id');
	$table_type = 'Edit';
	$header = '<p>EDIT Category</p>';
	$page_title = 'Edit Location';
	$data_table_def_no_data = createLocationTableDef($table_type, $pos_location_id);	
	$db_table = 'pos_locations';
	$key_val_id['pos_location_id'] = $pos_location_id;
	$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def_no_data);
}
else
{
	$table_type = 'New';
	$pos_location_id = 'TBD';
	if(ISSET($_GET['pos_parent_location_id']))
	{
		$pos_parent_location_id = $_GET['pos_parent_location_id'];
	}
	else
	{
		$pos_parent_location_id = 'false';
	}
	$header = '<p>Add location</p>';
	$page_title = 'Add Location';
	$data_table_def = createLocationTableDef($table_type, $pos_location_id,$pos_parent_location_id);
	
}

$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);
$big_html_table .= createHiddenInput('type', $type);

$html = $header;
$form_handler = 'add_edit_location.form.handler.php';
$html .= createFormForMYSQLInsert($data_table_def, $big_html_table, $form_handler, $complete_location, $cancel_location);
$html .= '<script>document.getElementsByName("location_name")[0].focus();</script>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);

?>

	