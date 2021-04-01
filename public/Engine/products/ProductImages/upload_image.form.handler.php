<?php
/*
	upload_image.handler.php
	craig iannazzi 2-17-12
	
	this file will process the upload then call the crop image with the file name in the get
	
*/

$page_level = 5;
$page_navigation = 'products';
$page_title = 'Upload an Image';
require_once ('../../../Config/config.inc.php');
require_once (PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);

if (isset($_POST['submit'])) 
{
	//Lets start by looking at post data.
	//There will be hidden values for images that have been/or are being resized
	$errors = array();
	if ($_POST['product_type'] == 'false')
	{
		$errors[] = "You forgot to select a category";
	}
	if (empty($errors)) 
	{
		//File handling
		if ($_FILES["image_file_name"]["error"] > 0)
		{
			echo "Error: " . $_FILES["image_file_name"]["error"] . "<br />";
		}
		else
		{
			//echo "Upload: " . $_FILES["image_file_name"]["name"] . "<br />";
			//echo "Type: " . $_FILES["image_file_name"]["type"] . "<br />";
			//echo "Size: " . ($_FILES["image_file_name"]["size"] / 1024) . " Kb<br />";
			//echo "Stored in: " . $_FILES["image_file_name"]["tmp_name"];
		}
		$target_path = "uploads/";
		$file_name = $_FILES["image_file_name"]["name"];
		$image_file_name = $target_path . basename( $_FILES['image_file_name']['name']); 
		if(move_uploaded_file($_FILES['image_file_name']['tmp_name'], $image_file_name)) 
		{
			//echo "<p>The file ".  basename( $_FILES['image_file_name']['name']). " has been uploaded</p>";
		} else
		{
			echo "There was an error uploading the file, please try again!";
		}		
		//let's see if we can crop the image to a maximum width
		include("resize_class.php"); 
		$max_width = $_POST['max_width'];
		//ResizeToDimension(400, $target_path, "jpg", $target_path);

		list($width, $height) = getimagesize($image_file_name);
		//echo '<p>Width: ' . $width . ' height: ' . $height .'<p>';

		
		//echo '<p>Max Width For Display: ' . $max_width . '</p>';

		//OK we need to look at the camera data to determine the rotation
		/*
		These are the values that can be present in the orientation tag. For a technical background, please refer to the excellent description on jpegclub.org .

	Value 	0th Row 	0th Column
	1 	top 	left side
	2 	top 	right side
	3 	bottom 	right side
	4 	bottom 	left side
	5 	left side 	top
	6 	right side 	top
	7 	right side 	bottom
	8 	left side 	bottom
	
	Here is another description given by Adam M. Costello:
	
	For convenience, here is what the letter F would look like if it were tagged correctly and displayed by a program that ignores the orientation tag:
	
	
		  1        2       3      4         5            6           7          8
	
		888888  888888      88  88      8888888888  88                  88  8888888888
		88          88      88  88      88  88      88  88          88  88      88  88
		8888      8888    8888  8888    88          8888888888  8888888888          88
		88          88      88  88
		88          88  888888  888888
*/
		//we want IFD0.Orientation: 8
		
		//fix photos taken on cameras that have incorrect
		//dimensions
		$exif = exif_read_data($image_file_name);
		//get the orientation
		$ort = $exif['Orientation'];
		//determine what oreientation the image was taken at
		
		if ($ort < 5)
		{
			if ($width>$max_width)
			{
				ResizeToDimension($max_width, $image_file_name, "jpg", $image_file_name);
			}
			else
			{
				//the image is actually good to go
			}
		}
		elseif ($ort >4)
		{
			//echo '<p>Width: ' . $width . ' height: ' . $height .'<p>';

			//calulate the new $max_width
			//reverse widt and height
			$correct_width = $height;
			$correct_height = $width;
			//echo '<p>Corrected Width: ' . $correct_width . ' height: ' . $correct_height .'<p>';

			if ($correct_width>$max_width)
			{
				//resize based on scaling the max_width to a corresponding max_height
				$max_height = ($max_width/$correct_width) * $correct_height;
				//echo '<p>max_Width: ' . $max_width . ' max_height: ' . $max_height .'<p>';

				ResizeToDimension($max_height, $image_file_name, "jpg", $image_file_name);
			}
			else
			{
				//resize to the full height
				ResizeToDimension($correct_height, $image_file_name, "jpg", $image_file_name);
			}
		}
	    
		/*$exif = exif_read_data($target_path, 0, true);
		//echo '<p>' . $exif['IFD0']['Orientation'] . '</p>';
		$orientation = $exif['IFD0']['Orientation'];
		if ($orientation > 4)
		{
			//Select the rotation and re-write the image.
			switch($orientation)
			{
				case 8:
					//rotate the image 90 degrees ccw
					
					break;
				case 6:
					//rotate the image 90 degrees cw
					
					break;
			
			
			}*/
		/*foreach ($exif as $key => $section) {
    		foreach ($section as $name => $val) {
	        echo "$key.$name: $val<br />\n";
    		}
			}*/
		/*
		if ($width>$max_width)
		{
			//echo '<p>RESIZING FOR DISPLAY</p>';
			$max_height = intval((intval($max_width)/intval($width))*(intval($height)));
			//echo '<p>Width: ' . $width . ' max height: ' . $max_height . '<p>';
			$resizeObj = new resize($image_file_name);  
			//$resizeObj -> resizeImage(150, 100, 'crop');
			$resizeObj -> resizeImage($max_width, $max_height, 'exact');
			$resizeObj -> saveImage($image_file_name, 100);
		}
		else
		{
		
		}*/
		$image_file_name = urlencode($image_file_name);
		header('Location: crop_image.form.php?image_file_name=' . $image_file_name . '&product_type=' . urlencode($_POST['product_type']));	
	}
	else 
	{ 
		include (HEADER_FILE);
		// Report the errors.
		echo '<h1>Error!</h1>
		<p class="error">The following error(s) occurred:<br />';
		foreach ($errors as $msg) { // Print each error.
			echo " - $msg<br />\n";
		}
		echo '</p><p>Please try again.</p><p><br /></p>';
		include('upload_image.form.php');
		include (FOOTER_FILE);
		
	} // End of if (empty($errors)) IF.

}
elseif (isset($_POST['cancel']))
{
	header('Location: ' . POS_ENGINE_URL . '/products/products.php');	
}




?>