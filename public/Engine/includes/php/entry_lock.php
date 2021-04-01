<?php
$page_level = 5;
$page_navigation = 'purchase_orders';
$page_title = 'entry_lock';
require_once ('../../../Config/config.inc.php');
require_once (PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);
//this is the form handler for the lock functionality
//it needs to either set the lock value to unlocked, then go to the continue location, or go to a cancel location

if (isset($_POST['submit']))
{
	$table = getPostOrGetValue('table');
	$primary_key = getPostOrGetValue('primary_key_name');
	$primary_key_value = getPostOrGetValue('primary_key_value');
	$complete_location = getPostOrGetValue('complete_location');
	
	//unlock
	$key_val_id[$primary_key] = $primary_key_value;
	$result = unlock_entry($table, $key_val_id);
	header('Location: '.$complete_location);
}
else
{
	$cancel_location = getPostOrGetValue('cancel_location');
	header('Location: '.$cancel_location);
}






?>