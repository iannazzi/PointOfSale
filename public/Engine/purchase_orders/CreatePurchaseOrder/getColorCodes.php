<?php  
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
//getColorCodes.php

/*	This page checks a database to see if
 *	$_GET['username'] has already been registered.
 *	The page will be called by JavaScript.
 *	The page returns a simple text message.
 *	No HTML is required by this script!
 */
 
 /* to test type in the url:
 	https://embrasse-moi.com/POS/purchase_orders/CreatePurchaseOrder/getColorCodes.php?pos_manufacturer_id=7&pos_manufacturer_brand_id=7&style_number=5035
 */
 
// Validate that the page received style number and manufacturer ID:

if ( (isset($_GET['pos_manufacturer_brand_id'])) && (isset($_GET['style_number'])) ) 
{
	require_once('../po_functions.php');
	
	$dbc = pos_connection();
	$style_number = scrubInput($_GET['style_number']);
	$pos_manufacturer_brand_id = scrubInput($_GET['pos_manufacturer_brand_id']);
	$pos_manufacturer_id = getManufacturerIdFromBrandId($pos_manufacturer_brand_id);
	
	//Two sets of color codes - the ones from the manufacturer UPC codes and/or the ones that we generated
	//To use manufacturer UPC codes we need to use thier color code
	
	
	//Getting the manufacturer color code:
	$manufacturer_color_code_sql = "SELECT DISTINCT color_code, color_description FROM pos_manufacturer_upc WHERE pos_manufacturer_id = '$pos_manufacturer_id' AND style_number ='$style_number'";
	$manufacturer_color_code_r = mysqli_query($dbc, $manufacturer_color_code_sql);
	if (mysqli_num_rows($manufacturer_color_code_r) > 0) 
	{
		//We found rows that match our manufacturer information - THIS IS SUPER CRITICAL
		// Put each store into the array:
		$mfg_color_codes = array();
		while ($manufacturer_color_code_row = mysqli_fetch_array($manufacturer_color_code_r, MYSQLI_ASSOC)) 
		{
			$mfg_color_codes[] = array('color_code' => strtoupper($manufacturer_color_code_row['color_code']),
			'color_description' => ucwords(strtolower($manufacturer_color_code_row['color_description'])));		
		}
		// Send the JSON data:
		

	}
	else
	{
		//There are no rows that match the manufacturer data...
	}
	
	//are there any rows that match our data?
	// get the product_id from the style numer
	$product_id_sql = "SELECT pos_product_id FROM pos_products WHERE style_number = '" . $style_number ."' AND pos_manufacturer_brand_id = '" . $pos_manufacturer_brand_id . "' LIMIT 1";
	$product_id_result = mysqli_query($dbc, $product_id_sql);
	
	
	// If there is a style number then we can get the product ID. Then We can get the color codes.
	if (mysqli_num_rows($product_id_result) != 1) 
	{
		// no result found.... no colors to load
		
	} 
	else 
	{
		// Initialize an array:
		$pos_color_names = array();
		// we can send back the color codes...
		$product_id = mysqli_fetch_array ($product_id_result, MYSQLI_ASSOC);
		//This is how you can send a single item back to the .js file
		//echo $product_id['pos_product_id'];
		$pos_product_id = $product_id['pos_product_id'];
		//select the color code from the product attributes table
		//$pos_color_names_sql = "SELECT pos_product_attribute_id, options FROM pos_products_attributes WHERE pos_product_id = '$pos_product_id' AND attribute_name = 'Color'";
		$pos_color_names_sql = "SELECT pos_product_color_id, color_name, color_code FROM pos_product_colors WHERE pos_product_id = '$pos_product_id'";
		$pos_color_names_array = getSQL($pos_color_names_sql);
		if (sizeof($pos_color_names_array) > 0) 
		{
			foreach ($pos_color_names_array as $value) 
			{
				//$color_code_and_name = parseColorCodes($value);				
				$pos_color_names[] =  array('color_code' => strtoupper($value['color_code']), 'color_description' => ucwords(strtolower($value['color_name'])));
			}	
		}
	}
	//have pos_color_name_tmp and $mfg_color_codes
	//we want to add each color in the $mfg_color_codes to the end of our codes that is not in our list
	$color_codes = array();
	if (isset($pos_color_names))
	{
		//echo '<p>POS data: ' . json_encode($pos_color_names) . "\n</p>";
		foreach($pos_color_names as $pos_code)
		{
			$color_codes[] = array('color_code' => $pos_code['color_code'], 'color_description' => $pos_code['color_description']);
		}
	}
	if (isset($mfg_color_codes))
	{
		//echo '<p>MFG DATA:' . json_encode($mfg_color_codes) . "\n</p>";
		foreach($mfg_color_codes as $mfg_code)
		{	
			$match = false;
			if (isset($pos_color_names))
			{
				foreach($pos_color_names as $code)
				{
					if ( ($mfg_code['color_code'] == $code['color_code']))
					{
						$match = true;
					}
					else
					{
					}
				}
			}				
			if (!$match) 
			{ 
				 $color_codes[] = array('color_code' => $mfg_code['color_code'], 'color_description' => $mfg_code['color_description']);
			}			
		}
	}
	if (empty($color_codes))
	{
		$color_codes = '';
	}
	
	echo json_encode($color_codes) . "\n";
	mysqli_close($dbc);
} 
else
{ // No username supplied!

	echo 'Error, no manufacturer_id AND style_number supplied';

}

function parseColorCodes($color_code_colon_name)
{
	$color_code = '';
	$color_name = '';
	$color_codes_check = explode(":", $color_code_colon_name);
	if (isset($color_codes_check[1]))
	{
		$color_code = trim($color_codes_check[0]);
		$color_name = trim($color_codes_check[1]);
	}
	elseif ($color_codes_check[0] != '')
	{
		$color_codes_check[0] = trim($color_codes_check[0]);
		$color_code = trim($color_codes_check[0]);
		$color_name = trim($color_codes_check[0]);
	}
	else
	{
		
	}
	return array('color_code' => $color_code, 'color_name' => $color_name);
}
?>
