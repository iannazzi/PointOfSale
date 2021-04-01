<?
//direct the page to go here before doing anything else
//this way we can unlock the invoice and do some validating....

$binder_name = 'Locations';
$access_type = 'WRITE';
require_once('../inventory_functions.php');
$pos_inventory_event_id = getPostOrGetID('pos_inventory_event_id');
$db_table = 'pos_inventory_event';
$key_val_id['pos_inventory_event_id'] = $pos_inventory_event_id;
unlock_entry($db_table, $key_val_id);
$pos_location_id = getInventoryLocation($pos_inventory_event_id);
$go_url = 'inventory.php?type=view&pos_location_id='.$pos_location_id;

	header('LOCATION: '.$go_url);

?>