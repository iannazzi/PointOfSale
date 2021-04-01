<?php  
/*
	*provide an sql statement to execute then display the results.
  to test type in the url:
 	http://www.craigiannazzi.com/POS_TEST/includes/php/ajax_simple_select.php?sql=SELECT%20pos_purchase_order_id,%20purchase_order_number%20FROM%20pos_purchase_orders%20WHERE%20purchase_order_status='OPEN'%20AND%20pos_manufacturer_id=7
 	
 	
 */
 
// Validate that the page received style number and manufacturer ID:
if ( isset($_POST['sql']) )
{
$page_level = 3;
require_once ('../../../Config/config.inc.php');
require_once(PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);
	
	//scrubbing this seems to mess it up?
	$sql = stripslashes($_POST['sql']);
	$result = runSQL($sql);
	if ($result)
	{
		echo json_encode($result);
	}
	else
	{	
		echo "error in sql";
	}
}	
else
{ 
	echo 'Error, sql not supplied';
}
?>
