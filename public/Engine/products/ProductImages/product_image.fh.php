<?php
/* 
	This will process 1 image and the multiple products linked to it.....
*/
$binder_name = 'Images';
$access_type = 'Write';
$page_title = 'Images';
require_once ('../product_functions.php');	


if (isset($_POST['submit'])) 
{
	$date_added = getDateTime();
	$product_table_data_object = json_decode(stripslashes($_POST['product_table_data_object']) , true);
	
	$pos_product_image_id = $_POST['pos_product_image_id'];
	$image_name =scrubInput($_POST['original_image_name']);
	$comments = scrubInput($_POST['comments']);
	$pos_path = scrubInput($_POST['pos_path']);
	//what is image order?
	$image_order =1;
	$image_insert_array = array( 	
								'original_image_name' => $image_name,
								'date_added' => $date_added,
								'comments' => $comments,
								'pos_path' => $pos_path
							);
	if ($pos_product_image_id == 'TBD')
	{
		$pos_product_image_id  = simpleInsertSQLReturnID('pos_product_images', $image_insert_array);
	}
	else
	{
		$key_val_id['pos_product_image_id'] = $pos_product_image_id;
		$results[] = simpleUpdateSQL('pos_product_images', $key_val_id, $image_insert_array);
	}
	
	//now for each product build the lookup
	//first clear the lookup
	$sql = "DELETE FROM pos_product_image_lookup WHERE pos_product_image_id = $pos_product_image_id";
	runSQL($sql);
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