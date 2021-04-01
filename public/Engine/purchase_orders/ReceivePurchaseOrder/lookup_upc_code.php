<?php  
/*

 	http://www.craigiannazzi.com/POS_TEST/Engine/purchase_orders/ReceivePurchaseOrder/lookup_upc_code.php?pos_manufacturer_id=7&upc=4009706107828
 */
 
// Validate that the page received style number and manufacturer ID:
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
$page_level = 5;
$page_navigation = 'purchase_order';
$page_title = 'load style number';
require_once ('../../../Config/config.inc.php');
require_once(PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);
require_once ('../po_functions.php');
if ( (isset($_GET['pos_manufacturer_id'])) && (isset($_GET['upc'])) ) 
{
	$upc = scrubInput($_GET['upc']);
	$pos_manufacturer_id = scrubInput($_GET['pos_manufacturer_id']);
}
else
{ 
	echo 'Error, no manufacturer_id AND upc supplied';
	exit();
}
	
	$sql = "SELECT style_number, style_description, color_code, color_description, size FROM pos_manufacturer_upc WHERE pos_manufacturer_id = '$pos_manufacturer_id' AND upc_code = '$upc'";
	$data = getSQL($sql);
	if(sizeof($data) == 1)
	{
		foreach($data[0] as $key => $value)
		{
			$response[] .= $key . ': ' . $value;
		}
		echo json_encode(implode(', ', $response));
	}
	else if (sizeof($data) > 1)
	{	
		echo ("More than one data set found");
	}
	else
	{	
		echo ("no UPC data found");
	}


?>
