<?php 

/*
	Rooms contain binders. Users can set up rooms and add binders to the rooms. Binders contain the 'pages' - probably the "list" pages, we will see
	
	Craig Iannazzi 11-15-12
	
*/

require_once ('../user_functions.php');


$complete_location = POS_ENGINE_URL.'/users/CustomBinders/list_custom_binders.php';
$cancel_location = $complete_location;
$type = getPostOrGetValue('type');
if (strtoupper($type) =='EDIT')
{
	$pos_custom_binder_id = getPostOrGetID('pos_custom_binder_id');
	$table_type = 'Edit';
	$header = '<p>EDIT Binder</p>';
	$page_title = 'Edit Binder ';

	$data_table_def_no_data = customBinderTableDef($type, $pos_binder_id);

	$db_table = 'pos_custom_binders';
	$key_val_id['pos_custom_binder_id'] = $pos_custom_binder_id;
	$dbc = openPOSdb();
	$data_table_def = selectSingleTableDataFromID($dbc,$db_table, $key_val_id,  $data_table_def_no_data);
	closeDB($dbc);
}
else if(strtoupper($type) == 'ADD')
{
	$table_type = 'New';
	$pos_custom_binder_id = 'TBD';
	$header = '<p>ADD A Binder</p>';
	$page_title = 'Add A Binder';

	$data_table_def = customBinderTableDef($type, $pos_custom_binder_id);
	
}
else if(strtoupper($type) == 'VIEW')
{
	$pos_custom_binder_id = getPostOrGetID('pos_custom_binder_id');
	$edit_location = 'add_edit_view_binders.php?pos_custom_binder_id='.$pos_custom_binder_id.'&type=edit';
	$primary_id_name = 'pos_custom_binder_id';
	$primary_id_value = $pos_custom_binder_id;
	$delete_message = urlencode('Confirm Delete this Custom Binder?');
	$db_table = 'pos_custom_binders';
	$header = 'View Custom Binders';
	//$delete_location =  POS_ENGINE_URL . '/includes/php/delete_mysql_entry.php?db_table='.$db_table.'&primary_id_name='.$primary_id_name.'&primary_id_value='.$primary_id_value.'&delete_message='.$delete_message.'&complete_location='.$complete_location.'&cancel_location='.$cancel_location;
	
	$page_title = 'Binders';
	$key_val_id['pos_custom_binder_id']  = $pos_custom_binder_id;
	$data_table_def = customBinderTableDef('View', $pos_custom_binder_id);
	$dbc = openPOSdb();
	$table_def_w_data = selectSingleTableDataFromID($dbc,$db_table, $key_val_id,  $data_table_def);
	closeDB($dbc);
}
if(strtoupper($type) == 'VIEW')
{
	$html = printGetMessage('message');
	$html .= '<p>View Binders</p>';
	
	$html .= createHTMLTableForMYSQLData($table_def_w_data);
	$html .= '<p><input class = "button"  type="button" name="edit"  value="Edit" onclick="open_win(\''.$edit_location.'\')"/>';
	//$html .= '<input class = "button"  type="button" name="edit"  value="Delete" onclick="open_win(\''.$delete_location.'\')"/>';
	$html .= '</p>';
	$html .= '<p>';
	//rights

	$html .= '<INPUT class = "button" type="button" style ="width:200px" value="Back to User" onclick="window.location = \''.$complete_location.'\'" />';
	$html .= '</p>';
}
else
{
	$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);
	$big_html_table .= createHiddenInput('type', $type);
	$big_html_table .= createHiddenInput('pos_custom_binder_id', $pos_custom_binder_id);
	$html = $header;
	$form_handler = 'add_edit_custom_binder.form.handler.php';
	$html .= createFormForMYSQLInsert($data_table_def, $big_html_table, $form_handler, $complete_location, $cancel_location);
	//$html .= '<script>document.getElementsByName("binder_name")[0].focus();</script>';
}

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);

?>

	