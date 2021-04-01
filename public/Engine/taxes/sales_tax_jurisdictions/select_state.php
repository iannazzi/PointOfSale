<?php 

/*
This form will allow you to select a manufacturer from a list then continue in get format with the manufacturer_id'
	
	Craig Iannazzi 4-23-12
	
*/
$page_title = "Select State";
require_once ('../tax_functions.php');


if (isset($_POST['submit'])) 
{
	$url = $_POST['complete_location'];
	$pos_state_id = getPostOrGetID('pos_state_id');
	$url = addGetValue($url, 'pos_state_id', $pos_state_id);
	header_redirect($url);
}


$type = getPostOrGetValue('type');
$jurisdiction = getPostOrGetValue('jurisdiction');
$complete_location = 'add_edit_view_tax_jurisdiction.php?type='.$type.'&jurisdiction='.$jurisdiction;
$cancel_location = 'list_tax_jurisdictions.php?message=Canceled';

$db_table = 'pos_states';
$data_table_def = array( 
						array( 'db_field' => 'pos_state_id',
								'type' => 'select',
								'caption' => 'Select State',
								'html' => createStateSelect('pos_state_id', 'false','off'),
								'validate' => array('select_value' => 'false'))
							);
include (HEADER_FILE);
$form_handler = 'select_state.php';
$html = createTableForMYSQLInsert($data_table_def, $form_handler, $complete_location, $cancel_location);
echo $html;
include (FOOTER_FILE);

?>

