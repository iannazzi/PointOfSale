<?
$binder_name = 'User Account Settings';
//on this page in particular we need to check if the user id is not the session id then the user must have access to system user binder
require_once('../user_functions.php');
$pos_user_id = getPostOrGetID('pos_user_id');
checkSystemUserAccess($pos_user_id);
$key_val_id['pos_user_id'] = $pos_user_id;
$room_name = getPostOrGetValue('room_name');
$complete_location = '../UserAccountSettings/user_settings.php?type=view&pos_user_id='.$pos_user_id;
$cancel_location = $complete_location;
$dbc = startTransaction();
$delet_q = "DELETE FROM pos_room_arrangements WHERE room_name = '$room_name' AND pos_user_id = $pos_user_id";
$results[] = runTransactionSQL($dbc, $delet_q);
simpleCommitTransaction($dbc);

header('Location: '.$complete_location );		


?>