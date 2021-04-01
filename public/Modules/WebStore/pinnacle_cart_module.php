<?php
/*
	This is the web store plug in module. I assume if I define all the function names and pass them the correct arguments, then the functions themselves would be able to be modified to provide the link to the web store
	
	What we expect to need to interact with:
	
	PRODUCTS - We will need to upload / download product information. I can pass in an array of all our product information for example
	SIMILAR PRODUCTS
	INVENTORY - We will need to update quantities of inventory
	IMAGES
	CATEGORIES
	CUSTOMERS
	ORDERS
	TAXES
	DISCOUNTS
	PROMOTIONS
	PAYMENT METHODS
	GIFT CARDS
	STORE CREDITS
	
	
*/
//this is the main controller, once here we can redirect
function pageController()
{
	if (isset($_GET['p']))
	{
	SWITCH($_GET['p'])
	{
		case 'product_export':
			$html = exportProducts();
			break;
		default:
			$html ='';
			break;
	}
	echo $html;
}
	else
	{
		echo 'Error: page not found';
	}
}
function webStoreJavascript()
{
	$html .= '<script type="text/javascript" src="' . POS_URL . 'Modules/WebStore/pinnacle_cart_javascript_module.js"></script>';
	return $html;
}
function getWebStoreSQL($sql)
{
	$dbc = openWebStoreDatabase();
	$result = @mysqli_query($dbc, $sql);
	$result_array = convert_mysql_result_to_array($result);
	mysqli_close($dbc);
	return $result_array;
}
function createGoogleBaseFeedArray()
{	
	$sql = 	"
	SELECT lower(pi_products.product_id) AS product_id, pi_products.overview, pi_products.title, pi_products.weight, pi_products.url_default,  pi_products.price, MID(lower(pi_products.product_id), 3, length(pi_products.product_id)) AS mpn, pi_catalog.name AS category_name, pi_manufacturers.manufacturer_name
	FROM pi_products
	LEFT JOIN pi_catalog
	ON pi_products.cid = pi_catalog.cid
	LEFT JOIN pi_manufacturers
	ON pi_products.manufacturer_id = pi_manufacturers.manufacturer_id
			";
	$mysql_array = getWebStoreSQL($sql);
	
	$now = date('Y-m-d');
	$expiration_date = date('Y-m-d', strtotime(" +20 days", strtotime($now)));
	
	$feed_format = array(
					array(	'header' => 'id',
							'mysql_result_field' => 'product_id'),
					array(	'header' => 'title',
							'mysql_result_field' => 'title'),
					array(	'header' => 'description',
							'mysql_result_field' => 'overview'),
					array(	'header' => 'google_product_category',
							'data' => 'Apparel & Accessories'),
					array(	'header' => 'product_type',
							'mysql_result_field' => 'category_name'),
					array(	'header' => 'link',
							'mysql_result_field' => 'url_default'),
					array(	'header' => 'image_link',
							'mysql_result_field' =>  'url_default'),
					array(	'header' => 'condition',
							'data' => 'new'),
					array(	'header' => 'availability',
							'data' => 'in stock'),
					array(	'header' => 'price',
							'mysql_result_field' => 'price'),
					array(	'header' => 'brand',
							'mysql_result_field' => 'manufacturer_name'),
					array(	'header' => 'mpn',
							'mysql_result_field' => 'mpn'),
					array(	'header' => 'gender',
							'data' => 'Female'),
					array(	'header' => 'age_group',
							'data' => 'Adult'),
					array(	'header' => 'color',
							'data' => 'all'),
					array(	'header' => 'size',
							'data' => 'all'),
					array(	'header' => 'location',
							'data' => '1 N Main Street, Pittsford, NY 14534'),
					array(	'header' => 'expiration_date',
							'data' => $expiration_date),	
					array(	'header' => 'weight',
							'mysql_result_field' => 'weight'));
	// need to take the mysql result and mege it with the table format to create a final data array
	$feed_array = array();
	//Step 1 create the headers:
	for($i=0; $i<sizeof($feed_format);$i++)
	{
		$feed_array[0][$i] = $feed_format[$i]['header'];
	}
	for($i=0;$i<sizeof($mysql_array);$i++)
	{
		for($j=0; $j<sizeof($feed_format);$j++)
		{
			if (isset($feed_format[$j]['data']))
			{
				$feed_array[$i+1][$j] = $feed_format[$j]['data'];
			}
			else
			{
				foreach($mysql_array[$i] as $key=>$value)
				{
					if ($key == $feed_format[$j]['mysql_result_field'])
					{
						if ($feed_format[$j]['header'] == 'link')
						{
							$feed_array[$i+1][$j] = UNSECURE_URL . '/'. $mysql_array[$i][$key];
						}
						elseif ($feed_format[$j]['header'] =='image_link')
						{
							$feed_array[$i+1][$j] = UNSECURE_URL  . '/images/products/'  . $mysql_array[$i]['product_id'].'.jpg';
						}
						
						else $feed_array[$i+1][$j] = $mysql_array[$i][$key];
					}
				}
			}
		}
	}
	return $feed_array;
}
function getProductCategoriesNames($pos_product_id)
{
	$sql = "SELECT CONCAT_WS('||',pos_categories.name,
(SELECT GROUP_CONCAT(pos_categories.name SEPARATOR '||') 
		FROM pos_product_secondary_categories
		LEFT JOIN pos_categories
		ON pos_product_secondary_categories.pos_category_id = pos_categories.pos_category_id
		WHERE pos_products.pos_product_id = pos_product_secondary_categories.pos_product_id )) as categories
FROM pos_products
LEFT JOIN pos_categories
ON pos_products.pos_category_id = pos_categories.pos_category_id
WHERE pos_products.pos_product_id = $pos_product_id
";
	return getSingleValueSQL($sql);
}
function getPcartCombinedWebProductColors($pos_product_id)
{
	/*return getSingleValueSQL("SELECT GROUP_CONCAT(color_name SEPARATOR '||')
			FROM pos_product_colors
			WHERE pos_product_id = '$pos_product_id' AND unique_web_product = 0");*/
			
	return getSingleValueSQL("SELECT GROUP_CONCAT(option_name SEPARATOR '||')
			FROM pos_product_options
			LEFT JOIN pos_product_attributes USING (pos_product_attribute_id)
			WHERE pos_product_id = '$pos_product_id' AND unique_web_product = 0 AND attribute_name = 'Color'");
			
}
function getPinnacleCartSizes($pos_product_id)
{
		//return implode('||',getProductSizes($pos_product_id));
		$sizes = getProductSizesAsArray($pos_product_id);
		return implode('||', $sizes['option_name']);
		
}
function exportProductsForWebStore($product_array)
{
	
	$web_store_products = array();
	$counter = 0;
	for($i=0;$i<sizeof($product_array);$i++)
	{
		//each color is a unique product?
		$unique_web_colors = getUniqueWebProductColors($product_array[$i]['pos_product_id']);
		for($cl=0;$cl<sizeof($unique_web_colors);$cl++)
		{
			$web_store_products[$counter] = createPcartToPOSProductLookup($product_array[$i]);
			$web_store_products[$counter]['Product ID'] = strtolower(getBrandCode($product_array[$i]['pos_manufacturer_brand_id']).'-'.$product_array[$i]['style_number']. '-' .$unique_web_colors[$cl]['option_code']);
			$web_store_products[$counter]['Attribute options 1'] = $unique_web_colors[$cl]['option_name'];
			$web_store_products[$counter]['Overview'] = ucfirst(strtolower(getBrandName($product_array[$i]['pos_manufacturer_brand_id']) .  ' ' . $product_array[$i]['title'] . ' in Color ' . $unique_web_colors[$cl]['option_name']));
			$web_store_products[$counter]['Name'] = $web_store_products[$counter]['Name'] . ' in ' . ucwords(strtolower($unique_web_colors[$cl]['option_name']));
			$counter++;
			
		}
		$combined_colors = getPcartCombinedWebProductColors($product_array[$i]['pos_product_id']);
		if($combined_colors!='')
		{
			$web_store_products[$counter] = createPcartToPOSProductLookup($product_array[$i]);
			$web_store_products[$counter]['Product ID'] = strtolower(getBrandCode($product_array[$i]['pos_manufacturer_brand_id']).'-'.$product_array[$i]['style_number']);
			$web_store_products[$counter]['Attribute options 1'] = $combined_colors;
			$web_store_products[$counter]['Overview'] = ucfirst(strtolower(getBrandName($product_array[$i]['pos_manufacturer_brand_id'])  . ' ' . $product_array[$i]['title']));
			$counter++;
		}
		
	}	
	return $web_store_products;


//this is stuff from pcart
/*case "products" : 
					{
						$ch = isset($_POST["ch"]) ? $_POST["ch"] : array();
						$ch = is_array($ch) ? $ch : array();

						$header = "Product ID".$separator."Name".$separator."Price";
						
						if (isset($ch["product_sku"])) $header.=$separator.$product_fields["product_sku"];
						if (isset($ch["product_upc"])) $header.=$separator.$product_fields["product_upc"];
						if (isset($ch["category"])) $header.=$separator.$product_fields["category"];
						if (isset($ch["manufacturer_name"])) $header.=$separator.$product_fields["manufacturer_name"];
						if (isset($ch["manufacturer_code"])) $header.=$separator.$product_fields["manufacturer_code"];
						if (isset($ch["location_name"])) $header.=$separator.$product_fields["location_name"];
						if (isset($ch["location_code"])) $header.=$separator.$product_fields["location_code"];
						if (isset($ch["meta_keywords"])) $header.=$separator.$product_fields["meta_keywords"];
						if (isset($ch["meta_title"])) $header.=$separator.$product_fields["meta_title"];
						if (isset($ch["meta_description"])) $header.=$separator.$product_fields["meta_description"];
						if (isset($ch["priority"])) $header.=$separator.$product_fields["priority"];
						if (isset($ch["call_for_price"])) $header.=$separator.$product_fields["call_for_price"];
						if (isset($ch["is_taxable"])) $header.=$separator.$product_fields["is_taxable"];
						if (isset($ch["tax_class_name"])) $header.=$separator.$product_fields["tax_class_name"];
						if (isset($ch["tax_class_id"])) $header.=$separator.$product_fields["tax_class_id"];
						if (isset($ch["tax_rate"])) $header.=$separator.$product_fields["tax_rate"];
						if (isset($ch["sale_price"])) $header.=$separator.$product_fields["sale_price"];
						if (isset($ch["price_level_1"])) $header.=$separator.$product_fields["price_level_1"];
						if (isset($ch["price_level_2"])) $header.=$separator.$product_fields["price_level_2"];
						if (isset($ch["price_level_3"])) $header.=$separator.$product_fields["price_level_3"];
						$db->query("SELECT * FROM ".DB_PREFIX."shipping_selected WHERE carrier_id='custom' AND method_id='product_level'");
						while (($shipping_selected = $db->moveNext()) != false)
						{
							if(isset($ch["shipping_price_".$shipping_selected["ssid"]])) $header.=$separator.$product_fields["shipping_price_".$shipping_selected["ssid"]];
						}
						if (isset($ch["free_shipping"])) $header.=$separator.$product_fields["free_shipping"];
						if (isset($ch["weight"])) $header.=$separator.$product_fields["weight"];
						if (isset($ch["inter_pack"])) $header.=$separator.$product_fields["inter_pack"];
						if (isset($ch["case_pack"])) $header.=$separator.$product_fields["case_pack"];
						if (isset($ch["min_order"])) $header.=$separator.$product_fields["min_order"];
						if (isset($ch["max_order"])) $header.=$separator.$product_fields["max_order"];
						if (isset($ch["is_visible"])) $header.=$separator.$product_fields["is_visible"];
						if (isset($ch["is_home"])) $header.=$separator.$product_fields["is_home"];
						if (isset($ch["is_hotdeal"])) $header.=$separator.$product_fields["is_hotdeal"];
						if (isset($ch["digital_product"])) $header.=$separator.$product_fields["digital_product"];
						if (isset($ch["digital_product_file"])) $header.=$separator.$product_fields["digital_product_file"];
						if (_ACCESS_ALL || _ACCESS_INVENTORY)
						{
							if (isset($ch["inventory_control"])) $header.=$separator.$product_fields["inventory_control"];
							if (isset($ch["stock"])) $header.=$separator.$product_fields["stock"];
							if (isset($ch["stock_warning"])) $header.=$separator.$product_fields["stock_warning"];
							if (isset($ch["inventory_rule"])) $header.=$separator.$product_fields["inventory_rule"];
						}
						if (isset($ch["overview"])) $header.=$separator.$product_fields["overview"];
						if (isset($ch["description"])) $header.=$separator.$product_fields["description"];
						if (isset($ch["image_url"])) $header.=$separator.$product_fields["image_url"];
						if (isset($ch["zoom_option"])) $header.=$separator.$product_fields["zoom_option"];
						if (isset($ch["image_alt_text"])) $header.=$separator.$product_fields["image_alt_text"];
						if (isset($ch["attributes"]))
						{
							$db->query("SELECT MAX(attributes_count) AS m FROM ".DB_PREFIX."products");
							if ($db->moveNext())
							{
								$m = $db->col["m"];
								for ($i=1; $i<=$m; $i++)
								{
									$header.=$separator."Attribute type ".$i;
									$header.=$separator."Attribute name ".$i;
									$header.=$separator."Attribute caption ".$i;
									$header.=$separator."Attribute options ".$i;
								}
							}
						}
						if (isset($ch["quantity_discounts"])) $header.=$separator.$product_fields["quantity_discounts"];
						
						echo $header."\n";
						
						if ($from == "products-search")
						{
							$where = isset($_SESSION["products-search-where"]) ? $_SESSION["products-search-where"] : "";
						}
						else
						{
							$where = isset($by_dates)? " WHERE added BETWEEN '".$mysql_from."' AND '".$mysql_to."'" : "";
						}
						
						
						echo $where;
						
						$products = $db->query("
							SELECT 
								".DB_PREFIX."products.*, 
								".DB_PREFIX."catalog.name AS cat_name, 
								".DB_PREFIX."catalog.key_name, 
								".DB_PREFIX."manufacturers.manufacturer_code,
								".DB_PREFIX."manufacturers.manufacturer_name,
								".DB_PREFIX."tax_classes.class_name AS tax_class_name,
								".DB_PREFIX."tax_classes.key_name AS tax_class_code,
								".DB_PREFIX."products_locations.name AS location_name,
								".DB_PREFIX."products_locations.code AS location_code,
								".DB_PREFIX."products_shipping_price.price as pl_shipping_price
							FROM ".DB_PREFIX."products 
							INNER JOIN ".DB_PREFIX."catalog ON ".DB_PREFIX."products.cid = ".DB_PREFIX."catalog.cid 
							LEFT JOIN ".DB_PREFIX."manufacturers ON ".DB_PREFIX."products.manufacturer_id = ".DB_PREFIX."manufacturers.manufacturer_id
							LEFT JOIN ".DB_PREFIX."tax_classes ON ".DB_PREFIX."products.tax_class_id = ".DB_PREFIX."tax_classes.class_id
							LEFT JOIN ".DB_PREFIX."products_locations ON ".DB_PREFIX."products.products_location_id = ".DB_PREFIX."products_locations.products_location_id
							LEFT JOIN ".DB_PREFIX."products_shipping_price ON ".DB_PREFIX."products_shipping_price.pid = ".DB_PREFIX."products.pid AND ".DB_PREFIX."products_shipping_price.is_price = 'Yes'
							".($where != "" ? $where : "")." 
							ORDER BY product_id
						");
						
						while ($product = $db->moveNext($products))
						{
							//first 3 fields
							echo 
								str_csv($product["product_id"]).$separator.
								str_csv($product["title"]).$separator.
								str_csv(isset($ch["sale_price"]) && $product["price2"] > 0 ? $product["price2"] : $product["price"]);
							if (isset($ch["product_sku"])) echo $separator.str_csv($product["product_sku"]);
							if (isset($ch["product_upc"])) echo $separator.str_csv($product["product_upc"]);
							if (isset($ch["category"])) 
							{
								$db->query("
									SELECT * FROM ".DB_PREFIX."products_categories 
									INNER JOIN ".DB_PREFIX."catalog ON ".DB_PREFIX."products_categories.cid = ".DB_PREFIX."catalog.cid 
									WHERE ".DB_PREFIX."products_categories.pid = '".$product["pid"]."' 
									ORDER BY ".DB_PREFIX."products_categories.is_primary DESC, ".DB_PREFIX."products_categories.cid
								");
								$categories = "";
								while ($category = $db->moveNext())
								{
									$categories = ($categories == "" ? "" : "||").$category["key_name"];
								}
								echo $separator.str_csv($categories);
							}
							if (isset($ch["manufacturer_name"])) echo $separator.str_csv($product["manufacturer_name"]);
							if (isset($ch["manufacturer_code"])) echo $separator.str_csv($product["manufacturer_code"]);
							if (isset($ch["location_name"])) echo $separator.str_csv($product["location_name"]);
							if (isset($ch["location_code"])) echo $separator.str_csv($product["location_code"]);
							if (isset($ch["meta_keywords"])) echo $separator.str_csv($product["meta_keywords"]);
							if (isset($ch["meta_title"])) echo $separator.str_csv($product["meta_title"]);
							if (isset($ch["meta_description"])) echo $separator.str_csv($product["meta_description"]);
							if (isset($ch["priority"])) echo $separator.str_csv($product["priority"]);
							if (isset($ch["call_for_price"])) echo $separator.str_csv($product["call_for_price"]);
							if (isset($ch["is_taxable"])) echo $separator.str_csv($product["is_taxable"]);
							if (isset($ch["tax_class_name"])) echo $separator.str_csv($product["tax_class_name"]);
							if (isset($ch["tax_class_id"])) echo $separator.str_csv($product["tax_class_code"]);
							if (isset($ch["tax_rate"])) echo $separator.str_csv($product["tax_rate"]);
							if (isset($ch["sale_price"])) echo $separator.str_csv($product["price"]);
							if (isset($ch["price_level_1"])) echo $separator.str_csv($product["price_level_1"]);
							if (isset($ch["price_level_2"])) echo $separator.str_csv($product["price_level_2"]);
							if (isset($ch["price_level_3"])) echo $separator.str_csv($product["price_level_3"]);
							
							$db->query("
								SELECT 
									IF(
										(".DB_PREFIX."products_shipping_price.price IS NULL) OR (".DB_PREFIX."products_shipping_price.is_price = 'No'), 
										0.00, ".DB_PREFIX."products_shipping_price.price
									) AS shipping_price,
									".DB_PREFIX."shipping_selected.ssid
								FROM ".DB_PREFIX."shipping_selected 
								LEFT JOIN 
									".DB_PREFIX."products_shipping_price 
									ON 
										".DB_PREFIX."products_shipping_price.ssid = ".DB_PREFIX."shipping_selected.ssid 
										AND 
										".DB_PREFIX."products_shipping_price.pid = ".$product["pid"]."
								WHERE 
									".DB_PREFIX."shipping_selected.carrier_id='custom' 
									AND 
									".DB_PREFIX."shipping_selected.method_id='product_level'
							");
							while (($shipping_selected = $db->moveNext()) != false)
							{
								if(isset($ch["shipping_price_".$shipping_selected["ssid"]])) 
									echo $separator.str_csv($product["shipping_price"]);
							}
							
							if (isset($ch["free_shipping"])) echo $separator.str_csv($product["free_shipping"]);
							if (isset($ch["weight"])) echo $separator.str_csv($product["weight"]);
							if (isset($ch["inter_pack"])) echo $separator.str_csv($product["inter_pack"]);
							if (isset($ch["case_pack"])) echo $separator.str_csv($product["case_pack"]);
							if (isset($ch["min_order"])) echo $separator.str_csv($product["min_order"]);
							if (isset($ch["max_order"])) echo $separator.str_csv($product["max_order"]);
							if (isset($ch["is_visible"])) echo $separator.str_csv($product["is_visible"]);
							if (isset($ch["is_home"])) echo $separator.str_csv($product["is_home"]);
							if (isset($ch["is_hotdeal"])) echo $separator.str_csv($product["is_hotdeal"]);
							if (isset($ch["digital_product"])) echo $separator.str_csv($product["digital_product"]);
							if (isset($ch["digital_product_file"])) echo $separator.str_csv($product["digital_product_file"]);
							if (_ACCESS_ALL || _ACCESS_INVENTORY)
							{
								if (isset($ch["inventory_control"])) echo $separator.str_csv($product["inventory_control"]);
								if (isset($ch["stock"])) echo $separator.str_csv($product["stock"]);
								if (isset($ch["stock_warning"])) echo $separator.str_csv($product["stock_warning"]);
								if (isset($ch["inventory_rule"])) echo $separator.str_csv($product["inventory_rule"]);
							}
							if (isset($ch["overview"])) echo $separator.str_csv($product["overview"]);
							if (isset($ch["description"])) echo $separator.str_csv($product["description"]);
							if (isset($ch["image_url"])) echo $separator.str_csv($product["image_url"]);
							if (isset($ch["zoom_option"])) echo $separator.str_csv($product["zoom_option"]);
							if (isset($ch["image_alt_text"])) echo $separator.str_csv($product["image_alt_text"]);
							if (isset($ch["attributes"]) && $product["attributes_count"] > 0)
							{
								$db->query("SELECT * FROM ".DB_PREFIX."products_attributes WHERE pid= '".$product["pid"]."' ORDER BY pid");
								while($attribute = $db->movenext())
								{
									echo $separator.str_csv($attribute["attribute_type"]);
									echo $separator.str_csv($attribute["name"]);
									echo $separator.str_csv($attribute["caption"]);
									if (in_array($attribute["attribute_type"], array("select", "radio")))
									{
										echo $separator.str_csv(str_replace("\n", "||", $attribute["options"]));
									}
									else
									{
										echo $separator.str_csv($attribute["text_length"]);
									}			
								}	
							}						
							echo "\n";
						}
						die();
						break;
*/
}
function createPcartToPOSProductLookup($product)
{
	$pinnacle_product['Product ID'] = 'TBD';
	$pinnacle_product['Name'] = getBrandName($product['pos_manufacturer_brand_id']) . ' ' . ucwords(strtolower(($product['title'])));
	$pinnacle_product['Price'] = $product['retail_price'];
	$pinnacle_product['Available'] = convert1_0ToYesNo($product['active']);
	$pinnacle_product['Product SKU'] = '';
	$pinnacle_product['UPC code'] = '';
	$pinnacle_product['Categories'] = getProductCategoriesNames($product['pos_product_id']);
	$pinnacle_product['Manufacturer name'] = getBrandName($product['pos_manufacturer_brand_id']);
	$pinnacle_product['Manufacturer code'] = getBrandName($product['pos_manufacturer_brand_id']);
	$pinnacle_product['Location name'] = '';
	$pinnacle_product['Location code'] = '';
	$pinnacle_product['Lock product'] = 'No';
	$pinnacle_product['Lock fields'] = '';
	$pinnacle_product['Meta keywords'] = '';
	$pinnacle_product['Meta title'] = '';
	$pinnacle_product['Meta description'] = '';
	$pinnacle_product['Priority'] = $product['priority'];
	$pinnacle_product['Rating'] = '';
	$pinnacle_product['Reviews Count'] = '';
	$pinnacle_product['Date added'] = $product['added'];
	$pinnacle_product['Call for price'] = '';
	$pinnacle_product['Is taxable'] = $product['is_taxable'];
	$pinnacle_product['Tax class name'] = ($product['retail_price']>110) ? 'Regular' : 'Exempt';
	$pinnacle_product['Tax class ID'] = ($product['retail_price']>110) ? 'Regular' : 'Exempt';
	$pinnacle_product['Tax rate on product level'] = '';
	$pinnacle_product['Cost'] = $product['retail_price'];
	$pinnacle_product['Sale price'] = $product['sale_price'];
	$pinnacle_product['Price level 1'] = 0;
	$pinnacle_product['Price level 2'] = 0;
	$pinnacle_product['Price level 3'] = 0;
	$pinnacle_product['Free shipping'] = 'No';
	$pinnacle_product['Weight'] = $product['weight'];
	$pinnacle_product['Inter pack'] = 0;
	$pinnacle_product['Case pack'] = 0;
	$pinnacle_product['Min order'] = 1;
	$pinnacle_product['Max order'] = 0;
	$pinnacle_product['On home page'] = 'No';
	$pinnacle_product['Is hotdeal'] = 'No';
	$pinnacle_product['Dollar Days Product'] = 'No';
	$pinnacle_product['Doba Product'] = 'No';
	$pinnacle_product['Default Product URL'] = '';
	$pinnacle_product['Custom Product URL'] = '';
	$pinnacle_product['Is digital'] = 'No';
	$pinnacle_product['Digital product file'] = '';
	$pinnacle_product['Track inventory'] = 'No';
	$pinnacle_product['Stock'] = 0;
	$pinnacle_product['Stock warning'] = 0;
	$pinnacle_product['Inventory rule'] = 'OutOfStock';
	$pinnacle_product['Overview'] = 'TBD';
	$pinnacle_product['Description'] = $product['description'];
	$pinnacle_product['Image URL'] = '';
	$pinnacle_product['Image Location'] = 'Local';
	$pinnacle_product['Image zooming option'] = 'global';
	$pinnacle_product['Image alt text'] = '';
	$pinnacle_product['Attribute type 1'] = 'select';
	$pinnacle_product['Attribute name 1'] = 'Color';
	$pinnacle_product['Attribute caption 1'] = 'Color';
	$pinnacle_product['Attribute options 1'] = 'TBD';
	$pinnacle_product['Attribute priority 1'] = 1;
	$pinnacle_product['Attribute type 2'] = 'select';
	$pinnacle_product['Attribute name 2'] = 'Size';
	$pinnacle_product['Attribute caption 2'] = 'Size';
	$pinnacle_product['Attribute options 2'] = getPinnacleCartSizes($product['pos_product_id']);
	$pinnacle_product['Attribute priority 2'] = 1;
	$pinnacle_product['Attribute type 3'] = '';
	$pinnacle_product['Attribute name 3'] = '';
	$pinnacle_product['Attribute caption 3'] = '';
	$pinnacle_product['Attribute options 3'] = '';
	$pinnacle_product['Attribute priority 3'] = '';
	return $pinnacle_product;
}
function updateWebStoreProducts()
{
}
function updateWebStoreCategory($pos_category_id)
{
}
function updateWebStoreManufacturer($pos_manufacturer_brand_id)
{
}
function updateWebStoreTaxes()
{
}
function updateWebStoreDiscounts()
{
}
function updateWebStorePromoCodes()
{
}
function updateWebStoreGiftCard()
{
}
function updateWebStoreUser()
{
}
function updateWebStoreAdmin()
{
}
?>