<?php  
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
/*
	*get_style_numbers.php

 
  to test type in the url:
 	http://www.embrasse-moi.com/POS/purchase_orders/get_brand.php?pos_manufacturer_id=7
 */
 
// Validate that the page received style number and manufacturer ID:

if ( (isset($_GET['pos_manufacturer_id'])) ) 
{
$page_level = 3;
require_once ('../../../Config/config.inc.php');
require_once(PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);
	$pos_manufacturer_id = scrubInput($_GET['pos_manufacturer_id']);
	$brand_sql = "SELECT pos_manufacturer_brand_id, brand_name FROM pos_manufacturer_brands WHERE pos_manufacturer_id = '$pos_manufacturer_id' AND active = '1'";
	$brands  = getSQL($brand_sql);
	echo json_encode($brands);
}	
else
{ // No username supplied!

	echo 'Error, no manufacturer_id AND style_number supplied';

}
?>
