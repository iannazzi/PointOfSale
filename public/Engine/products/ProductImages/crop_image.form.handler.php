<?php
/*
	crop_image.form.handler.php
	craig iannazzi 2-17-2012
	
	This file will crop the images and put them in the appropriate web folders
	IMAGE SIZES

	Bra Images (these require two steps)
	crop 3:5
	products: 400x600
	thumbs: 210 x 350
	crop 1:1
	preview: 400x400
	no thumb
	
	Hosiery/Wear to work / swim/resort
	crop 3:5
	Products: 400x600
	thumbs: 210x350
	no preview
	
	Panties/accessories
	products: 400x400
	thumbs: 210 x 210
	no preview
	
*/

$page_level = 5;
$page_navigation = 'products';
$page_title = 'Crop image';
require_once ('../../../Config/config.inc.php');
require_once (PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);

//Paths:
$product_image_path = BASE_PATH . '/images/products';
//$product_image_path = 'uploads' . '/images/products';
$preview_image_path = $product_image_path . '/preview';
$thumbs_image_path = $product_image_path . '/thumbs';
$secondary_image_path = $product_image_path . '/secondary';
$secondary_thumbs_image_path = $secondary_image_path . '/thumbs';


$jpeg_quality = 100;
$src = $_POST['image_name'];
$img_r = imagecreatefromjpeg($src);

if (isset($_POST['submit'])) 
{
	


	if ($_POST['images_to_crop'] == 3)
	{

	
		
		$bra_product_id = strtolower(trim($_POST['bra_product_id']));
		$panty_product_id = strtolower(trim($_POST['panty_product_id']));
		
		// Start with the Bra/panty 
		// Product: 400x600
		// thumbs: 210 x350
		// Preview : 400x400
		
		$bra_product_image_width = 400;
		$bra_product_image_height = 667;
		$bra_thumb_image_width = 210;
		$bra_thumb_image_height = 350;
		
		$bra_preview_image_width = 400;
		$bra_preview_image_height = 400;
		
		$panty_product_image_width = 400;
		$panty_product_image_height = 400;
		$panty_thumb_image_width = 210;
		$panty_thumb_image_height = 210;
		
		//First Crop
		//Bra/panty combo  product image - note this one does not technically get used
		$bra_product_r = ImageCreateTrueColor( $bra_product_image_width, $bra_product_image_height );
		imagecopyresampled($bra_product_r,$img_r,0,0,$_POST['x1_1'],$_POST['y1_1'], $bra_product_image_width,$bra_product_image_height,$_POST['w1'],$_POST['h1']);
	
		//This is the thumbnail for the catalog view
		$bra_thumb_r = ImageCreateTrueColor( $bra_thumb_image_width, $bra_thumb_image_height );
		imagecopyresampled($bra_thumb_r,$img_r,0,0,$_POST['x1_1'],$_POST['y1_1'], $bra_thumb_image_width,$bra_thumb_image_height,$_POST['w1'],$_POST['h1']);
			
		//Second Crop	
		//This is the preview image that shows up on the product page
		$bra_preview_r = ImageCreateTrueColor( $bra_preview_image_width, $bra_preview_image_height );
		imagecopyresampled($bra_preview_r,$img_r,0,0,$_POST['x2_1'],$_POST['y2_1'], $bra_preview_image_width,$bra_preview_image_height,$_POST['w2'],$_POST['h2']);
			
		//Third Crop	
		//This is the panty product image
		$panty_product_r = ImageCreateTrueColor( $panty_product_image_width, $panty_product_image_height );
		imagecopyresampled($panty_product_r,$img_r,0,0,$_POST['x3_1'],$_POST['y3_1'], $panty_product_image_width,$panty_product_image_height,$_POST['w3'],$_POST['h3']);
		$panty_thumb_r = ImageCreateTrueColor( $panty_thumb_image_width, $panty_thumb_image_height );
		imagecopyresampled($panty_thumb_r,$img_r,0,0,$_POST['x3_1'],$_POST['y3_1'], $panty_thumb_image_width,$panty_thumb_image_height,$_POST['w3'],$_POST['h3']);

			
		$bra_product_image_file_name = $product_image_path . '/' . $bra_product_id . '.jpg'; 
		$bra_product_image_url = BASE_URL . '/images/products/' . $bra_product_id . '.jpg'; 
		$bra_preview_image_file_name = $preview_image_path. '/' .$bra_product_id . '.jpg';
		$bra_preview_image_url = BASE_URL . '/images/products/preview/' . $bra_product_id . '.jpg'; 
		$bra_thumb_image_file_name  = $thumbs_image_path . '/' . $bra_product_id . '.jpg';
		$bra_thumb_image_url = BASE_URL . '/images/products/thumbs/' . $bra_product_id . '.jpg'; 
		
		$panty_product_image_file_name = $product_image_path . '/' . $panty_product_id . '.jpg';
		$panty_product_image_url = BASE_URL . '/images/products/' . $panty_product_id . '.jpg'; 
		$panty_thumb_image_file_name = $thumbs_image_path . '/' . $panty_product_id . '.jpg';
		$panty_thumb_image_url = BASE_URL . '/images/products/thumbs/' . $panty_product_id . '.jpg'; 
		
		imagejpeg($bra_product_r,$bra_product_image_file_name,$jpeg_quality);
		imagejpeg($bra_thumb_r,$bra_thumb_image_file_name,$jpeg_quality);
		imagejpeg($bra_preview_r,$bra_preview_image_file_name,$jpeg_quality);
		imagejpeg($panty_product_r,$panty_product_image_file_name,$jpeg_quality);
		imagejpeg($panty_thumb_r,$panty_thumb_image_file_name,$jpeg_quality);
		include (HEADER_FILE);
		echo'<p>Cropping, resizing generated the following images</p>';
		echo'<p><a href="upload_image.php">Upload Another Image</a></p>';
		
		echo '<P>Image has been placed here: ' . $bra_product_image_file_name . '</p>';
		echo '<p><img src="'  . $bra_product_image_url . '" /></p>';
		echo '<P>Image has been placed here: ' . $bra_thumb_image_file_name . '</p>';
		echo '<p><img src="' . $bra_thumb_image_url . '" /></p>';
		echo '<P>Image has been placed here: ' . $bra_preview_image_file_name . '</p>';
		echo '<p><img src="' . $bra_preview_image_url . '" /></p>';
		echo '<P>Image has been placed here: ' . $panty_product_image_file_name . '</p>';
		echo '<p><img src="' . $panty_product_image_url . '" /></p>';
		echo '<P>Image has been placed here: ' . $panty_thumb_image_file_name . '</p>';
		echo '<p><img src="' . $panty_thumb_image_url . '" /></p>';
		include (FOOTER_FILE);
			
			
	
	}
	else if ($_POST['images_to_crop'] == 1)
	{	

		
		$product_id = strtolower(trim($_POST['product_id']));
		
		//Hosiery/Wear to work / swim/resort
		//crop 3:5
		//Products: 400x600
		//thumbs: 210x350
		//no preview
		
		//Panties/accessories
		//products: 400x400
		//thumbs: 210 x 210
		//no preview
		if ($_POST['w'] == $_POST['h'])
		{
			$product_targ_w = 400;
			$product_targ_h = 400;
			$thumb_targ_w = 210;
			$thumb_targ_h = 210;
		}
		else
		{
			//assume crop is 3:5
			$product_targ_w = 400;
			$product_targ_h = 667;
			$thumb_targ_w = 210;
			$thumb_targ_h = 350;
		}
			
		
		$product_r = ImageCreateTrueColor( $product_targ_w, $product_targ_h );
		imagecopyresampled($product_r,$img_r,0,0,$_POST['x1'],$_POST['y1'],
		$product_targ_w,$product_targ_h,$_POST['w'],$_POST['h']);
		
		$thumb_r = ImageCreateTrueColor( $thumb_targ_w, $thumb_targ_h );
		imagecopyresampled($thumb_r,$img_r,0,0,$_POST['x1'],$_POST['y1'],
		$thumb_targ_w,$thumb_targ_h,$_POST['w'],$_POST['h']);
		
		$product_image_file_name = $product_image_path . '/' . $product_id . '.jpg';
		$product_image_url = BASE_URL . '/images/products/' . $product_id . '.jpg'; 
		$thumb_image_file_name  = $thumbs_image_path . '/' . $product_id . '.jpg';
		$thumb_image_url = BASE_URL . '/images/products/thumbs/' . $product_id . '.jpg';
		
		//header('Content-type: image/jpeg');
		imagejpeg($product_r,$product_image_file_name,$jpeg_quality);
		imagejpeg($thumb_r,$thumb_image_file_name,$jpeg_quality);
		include (HEADER_FILE);
		echo'<p>Cropping, resizing generated the following images</p>';
		echo'<p><a href="upload_image.php">Upload Another Image</a></p>';
		
		echo '<P>Image has been placed here: ' . $product_image_file_name . '</p>';
		echo '<p><img src="' . $product_image_url . '" /></p>';
		echo '<P>Image has been placed here: ' . $thumb_image_file_name . '</p>';
		echo '<p><img src="' . $thumb_image_url . '" /></p>';
		include (FOOTER_FILE);
		
	}
}
elseif (isset($_POST['cancel']))
{
	header('Location: ' . POS_ENGINE_URL . '/products/products.php');	
}

?>
