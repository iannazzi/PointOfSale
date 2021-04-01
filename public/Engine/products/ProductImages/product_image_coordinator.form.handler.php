<?php
/* 
coordinate images and product
images can have multiple products on it
products can have multiple images
therefore a lookup db is used

*/
$binder_name = 'Images';
$access_type = 'Write';
$page_title = 'Images';
require_once ('../product_functions.php');	


if (isset($_POST['submit'])) 
{
	$date_added = getDateTime();
	$image_table_data_object = json_decode(stripslashes($_POST['image_table_data_object']) , true);
	$product_table_data_object = json_decode(stripslashes($_POST['product_table_data_object']) , true);
	//go through each image
	
		$counter = 0;

		for($row=0;$row<sizeof($image_table_data_object['row_number']);$row++)
		{
			$image_name =scrubInput($image_table_data_object['original_image_name'][$row]);
			//strip off the .jpg or .JPG
			$parts=pathinfo($image_name);
			if(isset($parts['extension']))
			{
				$image_name  = str_replace('.' . $parts['extension'], '', $image_name);
			}
			$image_order =scrubInput($image_table_data_object['image_order'][$row]);
			$image_insert_array = array( 	
										'original_image_name' => $image_name,
										'date_added' => $date_added,
									);
			$pos_product_image_id  = simpleInsertSQLReturnID('pos_product_images', $image_insert_array);
			
			//now for each product build the lookup
			for($prow=0;$prow<sizeof($product_table_data_object['row_number']);$prow++)
			{
				$product_subid_name = $product_table_data_object['barcode'][$prow];
				//$pos_product_sub_id = $product_table_data_object['pos_product_sub_id'][$prow];
				$pos_product_sub_id = $product_table_data_object['pos_product_sub_id'][$prow];
				$pos_product_id = getProductIdFromProductSubId($pos_product_sub_id);
				//$pos_product_sub_id = getProductSubID($product_subid_name);
			
				//for the image we want to link it to an option id....specifically the color....
				//get the sub id color id....
				$pos_product_color_id = getProductOptionIdFromProductSubId($pos_product_sub_id, 'Color');
			
				$lookup_insert_array = array( 	'pos_product_id' => $pos_product_id,
										'pos_product_sub_id' => $pos_product_sub_id,
											'pos_product_image_id' => $pos_product_image_id,
											'image_order' => $image_order,
											//'comments' => $comments,
										);
				simpleInsertSQL('pos_product_image_lookup', $lookup_insert_array);
			}

		}
		

		//where to go to?
		$message = 'message=' . urlencode('OK NOW LETS UPLOAD - FTP THE IMAGES TO '. POS_URL. '/DataFiles/image_upload');
		header('Location: list_product_images.php?'. $message);
		exit();
					
}							
else
{
	$message = 'message=CANCELED' ;
		header('Location: list_product_images.php?'. $message);
		exit();
}								


?>