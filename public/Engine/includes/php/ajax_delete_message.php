<?php  
/*
	*check_unique_data.php
	*this is used for form validation - is the value unique?
	*to find that data we will AJAX the shit out of it from validateData function
	*Return true or false?
 
  to test type in the url:
 	http://www.craigiannazzi.com/POS_TEST/includes/php/ajax_create_message.php?table=pos_manufacturers&message=YoGabaGaba&to_pos_user_id=12&action_url=http://www.craigiannazzi.com/POS_TEST/purchase_orders/ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id=45
 	
 	
 */
 
// Validate that the page received style number and manufacturer ID:

if ( (isset($_GET['pos_message_id']))) 
{
$page_level = 1;
require_once ('../../../Config/config.inc.php');
require_once(PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);
require_once(MYSQL_DELETE_FUNCTIONS);
	
	$pos_message_id = scrubInput($_GET['pos_message_id']);
	$result = deleteMessage($pos_message_id);
	if ($result)
	{
		echo 'deleted';
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
?>
