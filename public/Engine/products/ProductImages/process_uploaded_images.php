<?php
/*

//this script is failing cause it takes too long... what needs to happen it it gets called from something elese

*/

$binder_name = 'Images';
$access_type = 'Write';
$page_title = 'Product Image Bulk Uploader';
require_once ('../product_functions.php');	
$disable_check_login = true;
$thumb_size = 100;
$scale_dimension = 'height';
$upload_path = IMAGE_UPLOAD_PATH;
$images_folder =  PRODUCT_IMAGE_FOLDER;
$thumbnails_folder =  PRODUCT_IMAGE_THUMBNAIL_FOLDER;
//check and create product image directory
makeDir(POS_PATH .$images_folder);
makeDir(POS_PATH.$thumbnails_folder);

$html = '';
$html = '<p>Unfortunatley I can only figure out how to process one at a time without the script shit-bombing. So sit here and keep pressing this button until no more messages show up</p>';
$html .= '<input class = "button" type="button" style="width:400px;" name="add_product" value="Process Uploaded Files" onclick="open_win(\'process_uploaded_images.php\')"/>';
$html .= '<p>' . createUserButton('Images') .'</p>';
//get a list of files in the directory
$files = glob($upload_path.'/*.{jpg,JPG}', GLOB_BRACE);
foreach($files as $file) 
{
	$image_name = strtolower(basename($file));
	$parts=pathinfo($image_name);
	$image_name  = scrubInput(str_replace('.' . $parts['extension'], '', $image_name));
  //for each file see if you can get a system id

  //now there is no gurantee that the image exists more than once
  $sql = "SELECT pos_product_image_id FROM pos_product_images WHERE original_image_name = '$image_name' AND pos_path = ''";
 $image_data = getSQL($sql);
  if (sizeof($image_data)==1)
  {
  	$pos_product_image_id = $image_data[0]['pos_product_image_id'];
  	$new_file_name = $pos_product_image_id .'.jpg';
  	
  	$pos_path = $images_folder . $new_file_name;
  	$new_file_path = POS_PATH .$images_folder .$new_file_name;
  	$thumb_file_path = POS_PATH . $thumbnails_folder . $new_file_name;
  	
  	//move the file to the system id name in the product image directory
	$sql = "UPDATE pos_product_images SET pos_path = '$pos_path' WHERE pos_product_image_id = $pos_product_image_id";
	$html .= '<p>' . $sql . '</p>';
	runSQL($sql);
	
	$html .= '<p>Moving ' . $file .' To '  . $new_file_path . '</p>';
	move_file($file, $new_file_path);
	
	//delete the thumb
	$thumb_file = $upload_path . '/thumbnail/' . basename($file);
	$html .= '<p>Deleting ' . $thumb_file  . '</p>';
	delete_file($thumb_file);
	
	//make a thumbnail and put it in thumbs
	make_thumbnail($new_file_path, $thumb_file_path, $thumb_size, $scale_dimension);
	
	//tag the photo meta data
	//I had to remove this for the nikon camera.....
	//tag_image($new_file_path);
	//tag_image($thumb_file_path);
	 break;
  }
  else if (sizeof($image_data)>1)
  {	
  	$html .= 'Can not correlate the uploaded image to the data  for file: ' .$file;
  	$html .= preprint($image_data, true);
  	
  }
  else
  {
  	//nada
  	 $html .= 'No Database Entry for file: ' .$file;
		$html .= preprint($sql, true);
  }
 
}

include(HEADER_FILE);
echo $html;
include(FOOTER_FILE);


?>