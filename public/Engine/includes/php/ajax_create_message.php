<?php  
/*
	*check_unique_data.php
	*this is used for form validation - is the value unique?
	*to find that data we will AJAX the shit out of it from validateData function
	*Return true or false?
 
  to test type in the url:
 	http://www.craigiannazzi.com/POS_TEST/includes/php/ajax_create_message.php?table=pos_manufacturers&message=YoGabaGaba&to_pos_user_id=12&action_url=http://www.craigiannazzi.com/POS_TEST/purchase_orders/ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id=45
 	
 	
 	http://www.craigiannazzi.com/POS_TEST/includes/php/ajax_create_message.php?table=pos_manufacturers&message=YoGabaGaba&to_pos_user_id=all&action_url=http://www.craigiannazzi.com/POS_TEST/purchase_orders/ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id=45
 	
 */
 
// Validate that the page received style number and manufacturer ID:

if ( (isset($_GET['message'])) && (isset($_GET['to_pos_user_id']))&& (isset($_GET['action_url'])) ) 
{
$page_level = 1;
require_once ('../../../Config/config.inc.php');
require_once(PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);
	
	$message = scrubInput(urldecode($_GET['message'])); 
	$to_pos_user_id = scrubInput($_GET['to_pos_user_id']);
	$action_url = scrubInput(urldecode($_GET['action_url']));
	$result = insertMessage($message, $to_pos_user_id, $action_url);
	if ($result)
	{
		echo 'added';
	}
	else
	{
		echo 'error';
	}
}	
else
{ // No username supplied!

	echo 'Error, get values not correct';

}
function insertMessage($message, $to_pos_user_id, $action_url)
{
	$table = 'pos_messages';
	$from_employee_id = $_SESSION['pos_user_id'];
	if ($to_pos_user_id == 'all')
	{
		$value = array();
		$users= getActiveUsers();
		for($i=0;$i<sizeof($users);$i++)
		{
			$value[] = "('$message', '$from_employee_id', '" .$users[$i]['pos_user_id'] . "', '$action_url', NOW())";
		}
		$values = implode(', ',$value);
		$insert_q = "INSERT INTO ".$table." (message, from_pos_user_id, to_pos_user_id, action_url, message_creation_date) VALUES " . $values;
	}
	else
	{
		$insert_q = "INSERT INTO ".$table." (message, from_pos_user_id, to_pos_user_id, action_url, message_creation_date) VALUES  ('$message', '$from_employee_id', '$to_pos_user_id', '$action_url', NOW())";
	}
	return runSQL($insert_q);
}
?>
