<?php

$page_level = 5;
$page_navigation = 'products';
$page_title = 'Bulk Product Uploader';
require_once ('../includes/config.inc.php');
require_once (PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);
require_once(MYSQL_POS_CONNECT_FILE);
$dbc = pos_connection();





if (isset($_POST['submit'])) 
{

		
	include (HEADER_FILE);
	
	if ($_FILES["uploadedfile"]["error"] > 0)
	  {
	  echo "Error: " . $_FILES["uploadedfile"]["error"] . "<br />";
	  }
	else
	  {
	  echo "Upload: " . $_FILES["uploadedfile"]["name"] . "<br />";
	  echo "Type: " . $_FILES["uploadedfile"]["type"] . "<br />";
	  echo "Size: " . ($_FILES["uploadedfile"]["size"] / 1024) . " Kb<br />";
	  echo "Stored in: " . $_FILES["uploadedfile"]["tmp_name"];
	  }
	
	$mimes = array('text/csv', 'text/comma-separated-values');
	if(in_array($_FILES['uploadedfile']['type'],$mimes))
	{
	  // do something
	} else 
	{
	  die("<p>Sorry, file type not allowed</p>");
	}

	$target_path = "uploads/";
	$file_name = $_FILES["uploadedfile"]["name"];
	$target_path = $target_path . basename( $_FILES['uploadedfile']['name']); 
	
	if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
		echo "<p>The file ".  basename( $_FILES['uploadedfile']['name']). 
		" has been uploaded</p>";
	} else{
		echo "There was an error uploading the file, please try again!";
	}
	
	// this is what we are looking for....
	//Categories	Brand	Style #	Title	wholesale	resale price	sale price	Active (0 or 1)	Weight	Colos(s)	Size(s)
	
	//Retrieve all categories
	$category_q = "SELECT pos_category_id, name FROM pos_categories";		
	$category_r = @mysqli_query ($dbc, $category_q);
	$i=0;
	$category_names = array();
	$category_ids = array();
	while($category_row=mysqli_fetch_array($category_r, MYSQLI_ASSOC))
	{
		$category_names[$i] = $category_row['name'];
		$category_ids[$i] = $category_row['pos_category_id'];
		$i = $i + 1;
	}
	//Retrive all Brand Names
	$brand_q = "SELECT pos_manufacturer_brand_id, brand_name FROM pos_manufacturer_brands";		
	$brand_r = @mysqli_query ($dbc, $brand_q);
	$i=0;
	$brand_names = array();
	$brand_ids = array();
	while($brand_row=mysqli_fetch_array($brand_r, MYSQLI_ASSOC))
	{
		$brand_names[$i] = $brand_row['brand_name'];
		$brand_ids[$i] = $brand_row['pos_manufacturer_brand_id'];
		$i = $i + 1;
	}
	//Bulid MFG to brand look up
	//If I have brand anita, find the mfg anita
	$mfg_brand_q = "SELECT pos_manufacturer_id, pos_manufacturer_brand_id FROM pos_manufacturer_brands WHERE active ='1'";		
	$mfg_brand_r = @mysqli_query ($dbc, $mfg_brand_q);
	$mfg =  array();
	while($mfg_brand_row=mysqli_fetch_array($mfg_brand_r, MYSQLI_ASSOC))
	{
		$tmp_val = $mfg_brand_row['pos_manufacturer_brand_id'];
		$mfg[$tmp_val] = $mfg_brand_row['pos_manufacturer_id'];
	}
	//Retrive all Brand Names
	$brand_q = "SELECT pos_manufacturer_brand_id, brand_name, brand_code FROM pos_manufacturer_brands where active = '1'";		
	$brand_r = @mysqli_query ($dbc, $brand_q);
	$i=0;
	$brand_names = array();
	$brand_ids = array();
	$brand_codes = array();
	while($brand_row=mysqli_fetch_array($brand_r, MYSQLI_ASSOC))
	{
		$brand_names[$i] = $brand_row['brand_name'];
		$brand_ids[$i] = $brand_row['pos_manufacturer_brand_id'];
		$brand_codes[$brand_row['pos_manufacturer_brand_id']] = $brand_row['brand_code'];
		
		$i = $i + 1;
	}
	//set up the expected columns:
	$cat_col = 0;
	$brand_col = 1;
	$style_col = 2;
	$title_col = 3;
	$wholesale_col = 4;
	$retail_price_col = 5;
	$sale_price_col = 6;
	$active_col = 7;
	$weight_col = 8;
	$color_col = 9;
	$size_col = 10;
		
	$cur_row = 1;
	$row = 0;
	$fp = fopen($target_path,'r') or die("can't open file");
	$column_headers = fgetcsv($fp);
	while($csv_line = fgetcsv($fp)) 
	{
		echo '<p>'. $csv_line[$style_col] . '</p>';		
		$tmp_style = trim($csv_line[$style_col]);
		$bln_error = "FALSE";
		$errors = array(); // Initialize an error array.
		$tmp_categories = trim($csv_line[$cat_col]);
		//Check that the category exists
		$bln_cat = "FALSE";
		for ($k=0;$k<sizeof($category_names);$k++)
		{
			//echo "<p>Comparing: Category Name: " . $category_names[$k] . " To CSV File Category Name: " . $tmp_categories . "</p>";
			if ($tmp_categories==$category_names[$k])
			{
				$tmp_categories = $category_ids[$k];
				$bln_cat = "TRUE";
				break;
			}			
		}
		if ($bln_cat == "FALSE")
		{
			$errors[] = '<p>Row: ' . $cur_row . ' STYLE: ' . $tmp_style  . ' not inserted due to non existing category: ' . $tmp_categories .' </p>';
			$bln_error = "TRUE";
		}
		$tmp_brand = $csv_line[$brand_col];
		//Check that the BRAND exists
		$bln_brand = "FALSE";
		for ($k=0;$k<sizeof($brand_names);$k++)
		{
			//echo "<p>Comparing: Brand Name: " . $brand_names[$k] . " To CSV File Brand Name: " . $tmp_brand . "And the Manufacturer of " . $mfg[$brand_ids[$k]] . " </p>";	
			if ($tmp_brand==$brand_names[$k])
			{
				$tmp_brand = $brand_ids[$k];
				$bln_brand = "TRUE";
				break;
			}			
		}
		if ($bln_brand == "FALSE")
		{
			$errors[] = '<p>Row: ' . $cur_row . ' STYLE: ' . $tmp_style  . ' not inserted due to non existing brand: ' . $tmp_brand . '</p>';
			$bln_error = "TRUE";
		}
		if ($bln_error == "TRUE")
		{
			// Report the errors.
			foreach ($errors as $msg) 
			{ // Print each error.
				echo "$msg";
			}
		}
		else
		{
			//all good update the row
			$brands[$row] = mysqli_real_escape_string($dbc,$tmp_brand);
			$mfgid[$row] = mysqli_real_escape_string($dbc,$mfg[$tmp_brand]);
			$categories[$row] = mysqli_real_escape_string($dbc, $tmp_categories);
			$styles[$row] = mysqli_real_escape_string($dbc, strtoupper(trim($csv_line[$style_col])));
			$titles[$row] = mysqli_real_escape_string($dbc, trim($csv_line[$title_col]));
			$wholesale[$row] = mysqli_real_escape_string($dbc, trim($csv_line[$wholesale_col]));
			$retail[$row] = mysqli_real_escape_string($dbc, trim($csv_line[$retail_price_col]));
			$sale[$row] = mysqli_real_escape_string($dbc, trim($csv_line[$sale_price_col]));
			$active[$row] = mysqli_real_escape_string($dbc, trim($csv_line[$active_col]));
			$weight[$row] = mysqli_real_escape_string($dbc, trim($csv_line[$weight_col]));
			$colors[$row] = mysqli_real_escape_string($dbc, trim($csv_line[$color_col]));
			$sizes[$row] = mysqli_real_escape_string($dbc, trim($csv_line[$size_col]));
			$row++;
		}
		//update the current row as we are skipping bad data
		$cur_row++;
	}
	
	
	
	//Now we should have valid data
	for ($i=0;$i<sizeof($categories);$i++)
	{
		//combination of pos_manufacture_brand_id AND style_number must be unique
		$product_update_insert_sql = "INSERT INTO pos_products (pos_category_id, pos_manufacturer_id, pos_manufacturer_brand_id, style_number, title, active, cost, retail_price, sale_price, weight, added) VALUES ('$categories[$i]', '$mfgid[$i]', '$brands[$i]',  '$styles[$i]', '$titles[$i]', '$active[$i]', '$wholesale[$i]','$retail[$i]', '$sale[$i]', '$weight[$i]', NOW() )
			ON DUPLICATE KEY UPDATE
			pos_category_id='$categories[$i]', pos_manufacturer_id = '$mfgid[$i]', pos_manufacturer_brand_id = '$brands[$i]', style_number = '$styles[$i]', title  = '$titles[$i]', active = '$active[$i]', cost = '$wholesale[$i]', retail_price = '$retail[$i]', sale_price = '$sale[$i]',  weight = '$weight[$i]', added = NOW()";
			//echo '<p> Replace queue: ' . $upc_update_insert_sql . '</p>';
			$product_update_insert_r = @mysqli_query ($dbc, $product_update_insert_sql); // Run the query.
			if ($product_update_insert_r) 	
			{	
				echo '<p>Style # ' . $brand_codes[$brands[$i]] . '-' . $styles[$i] . ' has been updated</p>';
				//need to get the product ID
				$just_inserted_product_sql = "SELECT pos_product_id FROM pos_products WHERE pos_manufacturer_brand_id = '$brands[$i]' AND style_number = '$styles[$i]'";
				$just_inserted_product_r = @mysqli_query($dbc, $just_inserted_product_sql);
				if (mysqli_num_rows($just_inserted_product_r) == 1) 
				{
					$just_inserted_product_row = mysqli_fetch_array($just_inserted_product_r, MYSQL_ASSOC);
					$pos_product_id = $just_inserted_product_row['pos_product_id'];
				}
				//now we need to write the attributes
		
				//just going to dump and overwrite	
				//Color and color code should match but probably won't
				//assumne color is correct and color code is optional
				if ($colors[$i] == '')
				{
					$color_array = null;
				}
				else
				{
					$color_array = explode("||", $colors[$i]);
				}
				$color_codes = array();
				$color_names = array();
				$options_string = '';
				for ($clr = 0;$clr<sizeof($color_array);$clr++)
				{
					$color_codes_check = explode(":", $color_array[$clr]);
					if (isset($color_codes_check[1]))
					{
						$color_codes[$clr] = trim($color_codes_check[0]);
						$color_names[$clr] = trim($color_codes_check[1]);
						$options_string = $options_string . trim(strtoupper($color_codes[$clr])) . ':' . trim(strtoupper($color_names[$clr]));
					}
					elseif ($color_codes_check[0] != '')
					{
						$color_codes_check[0] = trim($color_codes_check[0]);
						//right here can we pull the color code from the mfg upc file?
						$select_upc_color_code_sql = "SELECT DISTINCT color_code FROM pos_manufacturer_upc WHERE pos_manufacturer_id = '$mfgid[$i]' AND color_description = '$color_codes_check[0]'";
						//echo '<p>' . $select_upc_color_code_sql . '</p>';
						$select_upc_color_code_r = @mysqli_query($dbc, $select_upc_color_code_sql);
						if (mysqli_num_rows($select_upc_color_code_r) == 1) 
						{
							$select_upc_color_code_row = mysqli_fetch_array($select_upc_color_code_r, MYSQL_ASSOC);
							$color_codes[$clr] = trim($select_upc_color_code_row['color_code']);
						}
						else
						{
							$color_codes[$clr] = trim($color_codes_check[0]);
						}
						
						$color_names[$clr] = trim($color_codes_check[0]);
						$options_string = $options_string . trim(strtoupper($color_codes[$clr])) . ':' . trim(strtoupper($color_names[$clr]));
					}
					if ($clr != (sizeof($color_array) - 1)) $options_string = $options_string . '\r\n';	
				}	
				// now that we have the color attribute options we need to insert or update - there is no key for this so there is no shortcut
				// we need to look if the color attribute is already there.
				$color_check_sql = "SELECT pos_product_attribute_id FROM pos_products_attributes WHERE pos_product_id = '$pos_product_id' AND attribute_name = 'Color'";
				$color_check_r = @mysqli_query($dbc, $color_check_sql);
				if (mysqli_num_rows($color_check_r) == 1) 
				{
					$color_check_row = mysqli_fetch_array($color_check_r, MYSQL_ASSOC);
					$pos_product_attribute_id = $color_check_row['pos_product_attribute_id'];
					//this color name is already there
					$update_color_sql = "UPDATE pos_products_attributes SET options = '$options_string' WHERE pos_product_attribute_id = '$pos_product_attribute_id'";
					$update_color_r = @mysqli_query ($dbc, $update_color_sql); // Run the query.
					if ($update_color_r) 
					{
						echo '<p>Style # ' . $brand_codes[$brands[$i]] . '-' . $styles[$i] .  ' COLOR ' . $options_string . ' has been updated</p>';
						
					}
					else
					{
						echo '<p class ="error">Style # ' . $brand_codes[$brands[$i]] . '-' . $styles[$i] .  ' COLOR ' . $options_string . ' has been not been updated</p>';
						echo '<p>' . $update_color_sql . '</p>';
					}
				}
				else
				{
					//we need to insert a new color
					$color_insert_sql = "INSERT INTO pos_products_attributes (pos_product_id, attribute_name, options) VALUES ('$pos_product_id', 'Color',  '$options_string')";
					$color_insert_r = @mysqli_query ($dbc, $color_insert_sql); // Run the query.
					if ($color_insert_r) 
					{
						echo '<p>Style # ' . $brand_codes[$brands[$i]] . '-' . $styles[$i] .  ' COLOR ' . $options_string . ' has been inserted</p>';
					}
					else
					{
						echo '<p class ="error">Style # ' . $brand_codes[$brands[$i]] . '-' . $styles[$i] .  ' COLOR ' . $options_string . ' has been not been inserted</p>';
						echo '<p>' . $color_insert_sql . '</p>';
					}
				}
				
				
				
				
				
				if ($sizes[$i] == '')
				{
					$size_array = null;
				}
				else
				{
					$size_array = explode("||", $sizes[$i]);
				}
				$size_options ='';
				for ($sz = 0;$sz<sizeof($size_array);$sz++)
				{
					$size_options = $size_options . strtoupper(str_replace(" ", "", $size_array[$sz]));
					if ($sz != (sizeof($size_array) - 1)) $size_options = $size_options . '\r\n';	
				
				}
				// we need to look if the size attributeis already there.
				$size_check_sql = "SELECT pos_product_attribute_id FROM pos_products_attributes WHERE pos_product_id = '$pos_product_id' AND attribute_name = 'Size'";
				//echo '</p>' . $size_check_sql . '</p>';
				$size_check_r = @mysqli_query($dbc, $size_check_sql);
				if (mysqli_num_rows($size_check_r) == 1) 
				{
					$size_check_row = mysqli_fetch_array($size_check_r, MYSQL_ASSOC);
					$pos_product_attribute_id = $size_check_row['pos_product_attribute_id'];
					//this size name is already there... might as well update it... in case the code is there
					$update_size_sql = "UPDATE pos_products_attributes SET options = '$size_options' WHERE pos_product_attribute_id = '$pos_product_attribute_id'";
					$update_size_r = @mysqli_query ($dbc, $update_size_sql); // Run the query.
					if ($update_size_r) 
					{
						echo '<p>Style # ' . $brand_codes[$brands[$i]] . '-' . $styles[$i] .  ' size ' . $size_options . ' has been updated</p>';
					}
				}
				else
				{
					//we need to insert a new size
					$size_insert_sql = "INSERT INTO pos_products_attributes (pos_product_id, attribute_name, options) VALUES ('$pos_product_id', 'Size', '$size_options')";
					$size_insert_r = @mysqli_query ($dbc, $size_insert_sql); // Run the query.
					if ($size_insert_r) 
					{
						echo '<p>Style # ' . $brand_codes[$brands[$i]] . '-' . $styles[$i] .  ' size ' . $size_options . ' has been inserted</p>';
					}
				}
			}
			else
			{
					echo '<p class = "error" >Style # ' . $brand_codes[$brands[$i]] . '-' . $styles[$i] . ' has been not been updated</p>';
			}
	}
}
elseif (isset($_POST['cancel']))
{
	header('Location: ' . POS_ENGINE_URL . '/products/products.php');	
}


fclose($fp) or die("can't close file");
?> 