<?php  
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
/*
	*get_style_numbers.php

 
  to test type in the url:
 	http://www.embrasse-moi.com/POS/purchase_orders/get_style_data.php?pos_manufacturer_id=7&pos_manufacturer_brand_id=7&style_number=5035
 */
 
// Validate that the page received style number and manufacturer ID:

if ( (isset($_GET['pos_manufacturer_brand_id'])) && (isset($_GET['style_number'])) ) 
{
$page_level = 3;
require_once ('../../../Config/config.inc.php');
require_once(PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);
require_once(MYSQL_POS_CONNECT_FILE);
require_once(PHP_LIBRARY);
	$dbc = pos_connection();
	$style_number = mysqli_real_escape_string($dbc, trim($_GET['style_number']));
	$pos_manufacturer_brand_id = mysqli_real_escape_string($dbc, trim($_GET['pos_manufacturer_brand_id']));
	$pos_manufacturer_id = getManufacturerIdFromBrandId($pos_manufacturer_brand_id);
	
	//Two sets of color codes - the ones from the manufacturer UPC codes and/or the ones that we generated
	//To use manufacturer UPC codes we need to use thier color code
	
	
	
	//first - our system
	//There are no rows that match the manufacturer data...
	//are there any rows that match our data?
	// get the product_id from the style numer
	$product_id_sql = "SELECT pos_product_id, title, cost, retail_price, pos_category_id, pos_manufacturer_brand_size_id FROM pos_products WHERE style_number = '" . $style_number ."' AND pos_manufacturer_brand_id = '" . $pos_manufacturer_brand_id . "' LIMIT 1";
	$product_id_result = mysqli_query($dbc, $product_id_sql);
	
	// If there is a style number then we can get the product ID. Then We can get the color codes.
	if (mysqli_num_rows($product_id_result) == 1) 
	{
		$json = array();
		// Put each store into the array:
		$product_row = mysqli_fetch_array($product_id_result, MYSQLI_ASSOC);
		$json[] = array('source' => 'pos_system', 'pos_product_id' => $product_row['pos_product_id'], 'pos_category_id' => $product_row['pos_category_id'],
			'title' => $product_row['title'], 'retail_price' => $product_row['retail_price'],'cost' => $product_row['cost'], 'pos_manufacturer_brand_size_id' => $product_row['pos_manufacturer_brand_size_id']);	
		
		// Send the JSON data:
		echo json_encode($json) . "\n";
	} 
	else 
	{
		//did not find the style number in our system, lets try the mfg_upc codes	
		//Getting the manufacturer color code:
		$manufacturer_style_code_sql = "SELECT style_description, cost, msrp FROM pos_manufacturer_upc WHERE pos_manufacturer_id = '$pos_manufacturer_id' AND style_number = '$style_number' LIMIT 1";
		$manufacturer_style_code_r = mysqli_query($dbc, $manufacturer_style_code_sql);
		if (mysqli_num_rows($manufacturer_style_code_r) == 1) 
		{
			//We found rows that match our manufacturer information - THIS IS SUPER CRITICAL
			$json = array();
			// Put each store into the array:
			$manufacturer_style_code_row = mysqli_fetch_array($manufacturer_style_code_r, MYSQLI_ASSOC);
			$json[] = array('source' => 'mfg_upc', 'style_description' => $manufacturer_style_code_row['style_description'],
				'cost' => $manufacturer_style_code_row['cost'], 'msrp' => $manufacturer_style_code_row['msrp']);	

			// Send the JSON data:
			echo json_encode($json) . "\n";

		}
		else
		{
			//nothing available
			echo 'null';
		}
	}	
	mysqli_close($dbc);	
}	
else
{ // No username supplied!

	echo 'Error, no manufacturer_id AND style_number supplied';

}
?>
