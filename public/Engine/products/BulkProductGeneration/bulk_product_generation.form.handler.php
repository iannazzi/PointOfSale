<?php
/* 
 A physical count of inventory was done at this location. if the product does not have a barcode that means it was created
 
 
 Distressed inventory should create a new product sub-id.... ex aub::5456::blu::32B::distressed
 Damaged simialrly
 aub::5456::blu::32B::11234543 and a unique stock keeping unit => this sub ID then has a unique value, cost, retail price
 
 
 Distressed inventory has a zero value?

*/
$binder_name = 'Products';
$access_type = 'Write';
$page_title = 'Bulk Product Generation';
require_once ('../product_functions.php');	

//$table_data_object = json_decode(stripslashes($_POST['table_data_object']) , true);
//$table_data_array = json_decode(stripslashes($_POST['table_data_array']) , true);
if (isset($_POST['submit'])) 
{
	$date_added = getDateTime();

	//this is also a bulk product loader, although I am feeling that needs to be a bit different....
	$html = '';
	$labels_needed = array();
	if(isset($_POST['row_number']))
	{
		$counter = 0;
		for($row=0;$row<sizeof($_POST['row_number']);$row++)
		{
			$comments =scrubInput($_POST['comments'][$row]);

			//if there is no barcode then we need to create the product => and sticker!
			
			$product_array['pos_manufacturer_brand_id'] = $_POST['pos_manufacturer_brand_id'][$row];
			$product_array['pos_category_id'] = $_POST['pos_category_id'][$row];
			$product_array['style_number']= scrubInput($_POST['style_number'][$row]);
			$product_array['title'] = scrubInput($_POST['title'][$row]);
			$product_array['cost'] = scrubInput($_POST['cost'][$row]);
			$product_array['retail_price'] =scrubInput($_POST['retail_price'][$row]);
			$product_array['added'] =$date_added;				

			$size =scrubInput($_POST['size'][$row]);
			$color_code = scrubInput($_POST['color_code'][$row]);
			$color_description =scrubInput($_POST['color_description'][$row]);
			
			$options_array = array('Color' => array('option_code' => $color_code, 'option_name' => $color_description),
							'Size' => array('option_code' => $size, 'option_name' => $size));
							
							
			$pos_product_sub_id = fashionClothingProductCreator($product_array, $options_array);
			$product_subid_name = getProductSubIDName($pos_product_sub_id);
			
			$labels_needed[$counter]['pos_product_sub_id'] = $pos_product_sub_id;
			$labels_needed[$counter]['product_subid_name'] = $product_subid_name;
			$labels_needed[$counter]['quantity'] = 1;
			$labels_needed[$counter]['row_checkbox'] = '1';		
			$counter++;	
		}
	}
	//now we need the labels
	if(sizeof($labels_needed)>0)
	{	
		$file_name =  $date_added . '_products.pdf';
		$html = printProductLabelsForm($labels_needed, $file_name);	
		include (HEADER_FILE);
		echo $html;
		include (FOOTER_FILE);
	}
	else
	{
		//where to go to?
		header('Location: '.$_POST['complete_location']);

	}
}
else
{
	trigger_error( 'not submitted');
}								
