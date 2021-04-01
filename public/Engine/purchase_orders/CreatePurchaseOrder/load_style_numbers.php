<?php  
/*
	*load_style_numbers.php
	*to be used with ajax to grab style numbers from the POS system and manufacturer UPC system

 
  to test type in the url:
 	http://www.embrasse-moi.com/POS/purchase_orders/load_style_numbers.php?pos_manufacturer_id=7&pos_manufacturer_brand_id=7&style_number=50
 */
 
// Validate that the page received style number and manufacturer ID:
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
require_once ('../../../Config/config.inc.php');
require_once(PHP_LIBRARY);

	require_once (CHECK_LOGIN_FILE);
	require_once(MYSQL_POS_CONNECT_FILE);
	require_once(PHP_LIBRARY);
	$dbc = pos_connection();
if ( (isset($_GET['pos_manufacturer_brand_id'])) && (isset($_GET['style_number'])) ) 
{
	$style_number = mysqli_real_escape_string($dbc, trim($_GET['style_number']));
	$pos_manufacturer_brand_id = mysqli_real_escape_string($dbc, trim($_GET['pos_manufacturer_brand_id']));
	$pos_manufacturer_id = getManufacturerIdFromBrandId($pos_manufacturer_brand_id);
}
elseif ( (isset($_POST['pos_manufacturer_brand_id'])) && (isset($_POST['style_number']))  ) 
{
	$style_number = mysqli_real_escape_string($dbc, trim($_POST['style_number']));
	$pos_manufacturer_brand_id = mysqli_real_escape_string($dbc, trim($_POST['pos_manufacturer_brand_id']));
	$pos_manufacturer_id = getManufacturerIdFromBrandId($pos_manufacturer_brand_id);
}
else
{ // No username supplied!

	echo 'Error, no manufacturer_id AND style_number supplied';
	exit();
}


	
	$style_number = mysqli_real_escape_string($dbc, trim($_GET['style_number']));
	$pos_manufacturer_brand_id = mysqli_real_escape_string($dbc, trim($_GET['pos_manufacturer_brand_id']));
	$pos_manufacturer_id = getManufacturerIdFromBrandId($pos_manufacturer_brand_id);
	
	//Two sets of color codes - the ones from the manufacturer UPC codes and/or the ones that we generated
	//To use manufacturer UPC codes we need to use thier color code
	
	$mfg_styles = array();
	$pos_styles = array();
	
	//first - our system
	//There are no rows that match the manufacturer data...
	//are there any rows that match our data?
	// get the product_id from the style numer
	$product_id_sql = "SELECT style_number FROM pos_products WHERE style_number LIKE '" . $style_number ."%' AND pos_manufacturer_brand_id = '" . $pos_manufacturer_brand_id . "'";
	$product_id_result = mysqli_query($dbc, $product_id_sql);
	
	// If there is a style number then we can get the product ID. Then We can get the color codes.
	if (mysqli_num_rows($product_id_result) > 0) 
	{
		// Put each style into the array:
		while ($product_row = mysqli_fetch_array($product_id_result, MYSQLI_ASSOC))
		{
			$pos_styles[] = array('source' => 'pos_system', 'style_number' => $product_row['style_number']);	
		}
		// Send the JSON data:
		//echo json_encode($pos_styles) . "\n";
	}
	
	//if the lenght of the style_numer coming in is greater than 2 (i.e. 3)
	
	if (strlen($style_number) > 2)
	{
		//Getting the manufacturer style_numbers:
		$manufacturer_style_code_sql = "SELECT DISTINCT style_number FROM pos_manufacturer_upc WHERE pos_manufacturer_id = '$pos_manufacturer_id' AND style_number LIKE '$style_number%'";
		$manufacturer_style_code_r = mysqli_query($dbc, $manufacturer_style_code_sql);
		if (mysqli_num_rows($manufacturer_style_code_r) > 0) 
		{
			//We found rows that match our manufacturer information 
			
			// Put each store into the array:
			while($manufacturer_style_code_row = mysqli_fetch_array($manufacturer_style_code_r, MYSQLI_ASSOC))
			{
				$mfg_styles[] = array('source' => 'mfg_upc', 'style_number' => $manufacturer_style_code_row['style_number']);	
			}
			// Send the JSON data:
			//echo json_encode($mfg_styles) . "\n";
		}
		else
		{
			//nothing available
			//echo 'null';
		}
	}	
	mysqli_close($dbc);	
	// now combine everything
	$styles = combine_styles($pos_styles, $mfg_styles);
	echo json_encode($styles) . "\n";
	


function combine_styles($array1, $array2)
{
	
	$styles=array();
	foreach($array1 as $pos_styles)
	{
		$styles[] = array('source' => 'pos_system', 'style_number' => $pos_styles['style_number']);
	}
	foreach($array2 as $mfg_styles)
	{	
		$match = false;
		foreach($styles as $pos_styles)
		{
			if ($mfg_styles['style_number'] == $pos_styles['style_number'])
			{
				$match = true;
			}
			else
			{
			}
		}
		if (!$match) 
		{ 
			 $styles[] = array('source' => 'mfg_upc', 'style_number' => $mfg_styles['style_number']);
		}			
	}
	if (empty($styles)) $styles = '';
	return $styles;
}

?>
