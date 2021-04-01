<?php

$binder_name = 'Locations';
$access_type = 'Write';
$page_title = 'Bulk add Locations';
require_once ('../inventory_functions.php');	


if (isset($_POST['submit'])) 
{
	$date_added = getDateTime();
	if(isset($_POST['row_number']))
	{
		for($row=0;$row<sizeof($_POST['row_number']);$row++)
		{
			$pos_location_id = $_POST['pos_location_id'][$row];
			$insert['pos_store_id'] = $_POST['pos_store_id'][$row];
			$insert['location_name'] = scrubInput($_POST['location_name'][$row]);
			$insert['pos_parent_location_id'] = scrubInput($_POST['pos_parent_location_id'][$row]);
			$insert['pos_location_group_id'] = scrubInput($_POST['pos_location_group_id'][$row]);
			$insert['active'] = 1;
			$insert['comments'] = scrubInput($_POST['comments'][$row]);
			$key_val_id['pos_location_id'] = $pos_location_id;
			$pos_location_id = simpleUpdateSQL('pos_locations', $key_val_id, $insert);

		}
	}
	header('Location: '.$_POST['complete_location']);
				
}							
else
{
	trigger_error( 'not submitted');
}								


?>