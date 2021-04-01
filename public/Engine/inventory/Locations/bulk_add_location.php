<?php 

/*
	The process to create a new db entry for things like mfg, employee, etc is to define what we want in the table, then create that table....event post the table in json format for the handler to process?
	
	Craig Iannazzi 1-23-12
	
*/

$binder_name = 'Locations';
$access_type = 'Write';
$page_title = 'Bulk Add Location';
require_once ('../inventory_functions.php');		
		
$header = '<p class="error">Bulk Add Locations - NOTE THIS IS NOT CURRENTLY BEING VALIDATED - Names Must have a unique combination of store, parent, and location name. I.E. Two "SHELVES" in Store 1 cannot exist. Also Two "SHELVES" in pittsford in the basement cannot exist. Example name would be SHELVES - 01 and SHELVES - 02</p>';

$data = array();
$table_def = createbulkAddLocationTableDef();
$html_table = createDynamicTable($table_def, $data);


$html = $header;

$complete_location = 'list_locations.php';
$cancel_location = $complete_location;

$form_handler = 'bulk_add_location.form.handler.php';
$table_array = array($table_def);
$html .= createFormForMYSQLInsert($table_array, $html_table, $form_handler, $complete_location, $cancel_location);
//footer
include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);


?>

	