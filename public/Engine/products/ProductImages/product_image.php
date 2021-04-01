<?php
/*
	this is where we can view a product image, set properties, link it to products, etc....
	
	the product image properties is a regular table
	the link to products is a dynamic table....
*/
$binder_name = 'Images';
$access_type = 'Write';
$page_title = 'Product Images';
require_once ('../product_functions.php');	
$type = getFormType();	
$access_type = (strtoupper($type)=='VIEW') ? 'READ' : 'WRITE';

$complete_location = 'list_product_images.php';
$cancel_location = 'list_product_images.php?message=Canceled';



if(strtoupper($type) == 'ADD')
{
	$pos_product_image_id = 'TBD';
	$header = '<p>Add Product Image</p>';
	$page_title = 'Add Product Image';
	$data_table_def = createProductImageTableDef($type, $pos_product_image_id);
	
	//Products
	$linked_products = array();
}
else if (strtoupper($type) == 'REPLACE')
{
	$pos_product_image_id = getPostOrGetID('pos_product_image_id');
	$original_file_name = getSingleValueSql("SELECT original_image_name FROM pos_product_images WHERE pos_product_image_id = $pos_product_image_id");
	$sql = "UPDATE pos_product_images SET pos_path = '' WHERE pos_product_image_id = $pos_product_image_id";
	runSQL($sql);
	$file = POS_PATH .PRODUCT_IMAGE_FOLDER . $pos_product_image_id .'.jpg';
	$thumb = POS_PATH.PRODUCT_IMAGE_THUMBNAIL_FOLDER. $pos_product_image_id .'.jpg';
	delete_file($file);
	delete_file($thumb);
	header('Location: ' . $complete_location . '?message='.$pos_product_image_id . ' Has Been De-Referenced - Upload a New Image with the name of ' . $original_file_name. ' To Replace The Current Image');
	exit();
}
else if (strtoupper($type) == 'DELETE')
{
		$pos_product_image_id = getPostOrGetID('pos_product_image_id');

	
	
	$sql1 = "DELETE FROM pos_product_images WHERE pos_product_image_id = $pos_product_image_id";
	$sql2 = "DELETE FROM pos_product_image_lookup WHERE pos_product_image_id = $pos_product_image_id";
	runSQL($sql1);
	runSQL($sql2);
	$file = POS_PATH .PRODUCT_IMAGE_FOLDER . $pos_product_image_id .'.jpg';
	$thumb = POS_PATH.PRODUCT_IMAGE_THUMBNAIL_FOLDER. $pos_product_image_id .'.jpg';
	delete_file($file);
	delete_file($thumb);
	header('Location: ' . $complete_location . '?message='.$pos_product_image_id . ' Has Been Deleted');
	exit();

	
}
elseif (strtoupper($type) == 'EDIT')
{
	$pos_product_image_id = getPostOrGetID('pos_product_image_id');
	$header = '<p>EDIT Location Group</p>';
	$page_title = 'Edit Image';
	$data_table_def_no_data = createProductImageTableDef($type, $pos_product_image_id);	
	$db_table = 'pos_product_images';
	$key_val_id['pos_product_image_id'] = $pos_product_image_id;
	$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def_no_data);
	
	$linked_products = getProductsLinkedToImage($pos_product_image_id);
	
	
	
}
elseif (strtoupper($type) == 'VIEW')
{
	$pos_product_image_id = getPostOrGetID('pos_product_image_id');
	$edit_location = 'product_image.php?pos_product_image_id='.$pos_product_image_id.'&type=edit';
	//$delete_location = 'delete_discount.form.handler.php?pos_product_image_id='.$pos_product_image_id;
	$db_table = 'pos_product_images';
	$key_val_id['pos_product_image_id']  = $pos_product_image_id;
	$data_table_def = createProductImageTableDef($type, $pos_product_image_id);
	$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def);
	$linked_products = getProductsLinkedToImage($pos_product_image_id);
}
else
{
	trigger_error('No Type Defined');
}

//build the html page
if (strtoupper($type) == 'VIEW')
{
	$html = printGetMessage('message');
	$html .= '<p>View Image</p>';
	//$html .= confirmDelete($delete_location);
	$html .= createHTMLTableForMYSQLData($data_table_def);
	$product_table_name = 'product_table';
	$html .=  '<script src="product_image.js"></script>'.newline();

		$html .= '<h3>Linked Products Shown In The Image</h3>';

	$product_contents_table_def = createImageCoordinatorProductTableDefforView($product_table_name);
	$html .= createStaticViewDynamicTable($product_contents_table_def, $linked_products);
	$html .= '<p><input class = "button"  style="width:200px" type="button" name="edit"  value="Edit Image Details" onclick="open_win(\''.$edit_location.'\')"/>';
		
	$html .= product_image_html($pos_product_image_id);
	$html .= '<p><input class = "button"  style="width:300px" type="button" name="Delete"  value="Delete This Image and Photo Entry" onclick="deleteImage(\'product_image.php?type=DELETE&pos_product_image_id='.$pos_product_image_id.'\')"/>';
	
	//replace is not easily working....
	$html .= '<input class = "button"  style="width:300px" type="button" name="Replace"  value="Remove Image (replace by uploading a new image)" onclick="replaceImage(\'product_image.php?type=REPLACE&pos_product_image_id='.$pos_product_image_id.'\')"/>';
	
	if (check_product_image_exists($pos_product_image_id) && WEB_STORE_ACTIVE)
	{
		$html .= '<p><input class = "button"  style="width:300px" type="button" name="edit"  value="Create a Web Product With This Image as Primary" onclick="open_win(\'load_web_image.php?type=PRIMARY&pos_product_image_id='.$pos_product_image_id.'\')"/>';
		$html .= '<input class = "button"  style="width:300px" type="button" name="edit"  value="Add This Image as a Secondary Product Image" onclick="open_win(\'load_web_image.php?type=SECONDARY&pos_product_image_id='.$pos_product_image_id.'\')"/>';
		$html .= '<input class = "button"  style="width:350px" type="button" name="edit"  value="Replace A  Web Product Primary Image With This Image" onclick="open_win(\'load_web_image.php?type=REPLACE&pos_product_image_id='.$pos_product_image_id.'\')"/>';
	}

// $html .= '<input class = "button" type="button" name="delete" value="Delete Discount" onclick="confirmDelete();"/>';
	
	$html .= '<p>';
	$html .= '<INPUT class = "button" type="button" style ="width:200px" value="Back to Images" onclick="window.location = \''.$complete_location.'\'" />';
	$html .= '</p>';
}

else
{
	$form_handler = 'product_image.fh.php';
	$form_id = 'form_id';
	$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);
	$big_html_table .= createHiddenInput('type', $type);
	
	$big_html_table .= '<h3>Link The Products Shown In The Image</h3>';
	$big_html_table .=  '<script src="product_image_coordinator.js"></script>'.newline();
	$big_html_table .=  '<script src="'.AJAX_PRODUCT_SUB_ID.'"></script>'.newline();
	
	$big_html_table .= productLookUpTable();

	$product_table_name = 'product_table';
	$product_contents_table_def = createImageCoordinatorProductTableDef($product_table_name);
	$big_html_table .= createDynamicTableReuse($product_table_name, $product_contents_table_def, $linked_products, $form_id, ' class="dynamic_contents_table"  ');
	
	$html = $header;
	
	$html .= createFormForMYSQLInsert($data_table_def, $big_html_table, $form_handler, $complete_location, $cancel_location);
	$html .= '<script>document.getElementsByName("original_image_name")[0].focus();</script>';
}


include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);





	
function createProductImageTableDef($type, $pos_product_image_id)
{
	if ($pos_product_image_id =='TBD')
	{
		$unique_validate = array('unique' => 'pos_path', 'min_length' => 1);
	}
	else
	{
		$key_val_id['pos_product_image_id'] = $pos_product_image_id;
		$unique_validate = array('unique' => 'pos_path', 'min_length' => 1, 'id' => $key_val_id);
	}
	
	return array( 
						array( 'db_field' => 'pos_product_image_id',
								'type' => 'input',
								'tags' => ' readonly = "readonly" onkeypress="return noEnter(event)" ',
								'caption' => 'Discount ID',
								'value' => $pos_product_image_id,
								'validate' => 'none'
								
								
								),
							
						array('db_field' =>  'original_image_name',
								'type' => 'input',
								'tags' => ' onkeypress="return noEnter(event)" ',
								'caption' => 'Image Name',
								'validate' => 'none'),
						array('db_field' =>  'pos_path',
								'type' => 'input',
								'db_table' => 'pos_product_images',
								'caption' => 'Path',
								//'validate' => $unique_validate
								),	
						array('db_field' =>  'comments',
								'type' => 'input',
								'tags' => ' onkeypress="return noEnter(event)" ',
								'caption' => 'Comments'),
						array('db_field' =>  'active',
								'type' => 'checkbox',
								'caption' => 'Active',
								'value' => '1')
						);	

}



	
?>