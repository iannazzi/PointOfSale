<?

//don't want to delete a location.. just set inactive
$binder_name = 'Locations';
//on this page in particular we need to check if the user id is not the session id then the user must have access to system user binder
require_once('../inventory_functions.php');
$pos_location_id = getPostOrGetID('pos_location_id');
$key_val_id['pos_location_id'] = $pos_location_id;
$complete_location = 'list_locations.php';
$cancel_location = $complete_location;
$dbc = startTransaction();
runTransactionSQL($dbc,"UPDATE pos_locations SET pos_parent_location_id = 0 WHERE pos_parent_location_id = $pos_location_id");
$delet_q = "DELETE FROM pos_locations WHERE pos_location_id = $pos_location_id";
$results[] = runTransactionSQL($dbc, $delet_q);

//now what about inventory in this location? log the location has no inventory....
"DELETE * FROM pos_location_contents WHERE pos_location_id = $pos_location_id"
simpleCommitTransaction($dbc);

header('Location: '.$complete_location );		


?>