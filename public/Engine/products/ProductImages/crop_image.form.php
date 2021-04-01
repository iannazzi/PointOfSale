<?php
/*
	crop_image.form
	
*/


$page_level = 5;
$page_navigation = 'products';
$page_title = 'Crop image';
require_once ('../../../Config/config.inc.php');
require_once (PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);

// Check for a valid file
if ( isset($_GET['image_file_name'])) 
{ 
	$image_file = urldecode($_GET['image_file_name']);
} 
else 
{ // No valid file, kill the script.
	echo '<p class="error">This page has been accessed in error.</p>';
	exit();
}


		
		
		include (HEADER_FILE);
		echo '<script type="text/javascript" src = "' . POS_URL . '/3rdParty/jQuery/jquery-1.7.1.min.js"></script>';
		echo '<script type="text/javascript" src = "' . POS_URL . '/3rdParty/Jcrop/js/jquery.Jcrop.js"></script>';
		echo'<link rel="stylesheet" href="' . POS_URL . '/3rdParty/Jcrop/css/jquery.Jcrop.css" type="text/css" />';
		echo '<script type="text/javascript" src = "crop_image.form.js"></script>';
		$product_id = basename(strtolower($image_file), ".jpg");
		$product_type = urldecode($_GET['product_type']);
		
		//ok look up the $image_file_name to find the resulting product id.... there might be more than one... so we will need a select
		
		/*$sql = "SELECT pos_manufacturer_brands.brand_code, pos_products.style_number, pos_product_options.option_code 
				FROM pos_product_options
				LEFT JOIN pos_products ON pos_product.pos_product_id = pos_product_options.pos_product_id
				LEFT JOIN pos_manufacturer_brands ON pos_products.pos_manufacturer_brand_id = pos_manufacturer_brands.pos_manufacturer_brand_id
				LEFT JOIN pos_product_images ON pos_product_option.pos_product_option_id = pos_product_images.pos_product_option_id
				WHERE pos_product_images.image_name = '$image_file'";
		
		$linked_products = getSQL($sql);
		echo $linked_products;*/
		?><script>
		var product_type= "<?php echo $product_type; ?>";
		</script><?php
		if ($product_type == 'Bra')
		{
			//Need three images: One for Catalog view, one for Preview, One for panty
			echo '<form method="post" id="coords" class="coords"
				  action="crop_image.form.handler.php" onsubmit="return validateForm(\'bra_product_id\',\'panty_product_id\')">';
			echo '<p>Enter the Product Id For The Bra: <input class = "highlight_input" type="text" size="20" id="bra_product_id" name="bra_product_id" value = "' . $product_id . '"/></p>';
			echo '<p>This is for the Catalog View</p>';
			echo '<p> Select your size ratio: ';
			echo '<select name="crop_ratio_select" onChange="change_crop_ratio(this)">';
						echo '<option value="1" selected= "selected">3:5</option>';
						echo '</select></p>';
						
		
			
			
			echo'<table><tr>';
			echo '<td><img src="' . $image_file .'" id="target"  /></td>';
			echo '<td><img src="reference_images/fel150061nublkCatalog.jpg"   />';
			echo '<p align= "center">This image is your reference image.</p></td>';
			echo '</tr></table>';
			
			
			
			echo'		<p></p>
				  <div>
					<input type="hidden"  id="x1_1" name="x1_1" />
					<input type="hidden"  id="y1_1" name="y1_1" />
					<input type="hidden"  id="x1_2" name="x1_2" />
					<input type="hidden"  id="y1_2" name="y1_2" />
					<input type="hidden"  id="w1" name="w1" />
					<input type="hidden"  id="h1" name="h1" />
				  </div>';
			  
			echo '<p>This is for the Bra Product page - a square bra image</p>';

			echo '<p> Select your size ratio: ';
			echo '<select name="crop_ratio_select" onChange="change_crop_ratio(this)">';
			echo '<option value="0" selected="selected">1:1</option>';
			echo '</select></p>';

			
			echo'<table><tr>';
			echo '<td><img src="' . $image_file .'" id="target2"  /></td>';
			echo '<td><img src="reference_images/fel150061nublkPreview.jpg"   />';
			echo '<p align= "center">This image is your reference image.</p></td>';
			echo '</tr></table>';
			
			echo '
					<p></p>
				  <div>
						<input type="hidden"  id="x2_1" name="x2_1" />
						<input type="hidden"  id="y2_1" name="y2_1" />
						<input type="hidden"  id="x2_2" name="x2_2" />
						<input type="hidden"  id="y2_2" name="y2_2" />
						<input type="hidden"  id="w2" name="w2" />
						<input type="hidden"  id="h2" name="h2" />
				  </div>';
		
			echo '<p>Enter the Product Id For The Panty: <input class = "highlight_input" type="text" size="20" id="panty_product_id" name="panty_product_id" /></p>';
			echo '<p>This is for the Panty Product page - a square panty image</p>';
			
			echo '<p> Select your size ratio: ';
			echo '<select name="crop_ratio_select" onChange="change_crop_ratio(this)">';
			echo '<option value="0" selected="selected">1:1</option>';
			echo '</select></p>'; 	
			echo'<table><tr>';
			echo '<td><img src="' . $image_file .'" id="target3"  /></td>';
			echo '<td><img src="reference_images/fel730061nublkPanty.jpg"   />';
			echo '<p align= "center">This image is your reference image.</p></td>';
			echo '</tr></table>';
			echo '
					<p></p>
				  <div>
						<input type="hidden"  id="x3_1" name="x3_1" />
						<input type="hidden"  id="y3_1" name="y3_1" />
						<input type="hidden"  id="x3_2" name="x3_2" />
						<input type="hidden"  id="y3_2" name="y3_2" />
						<input type="hidden"  id="w3" name="w3" />
						 <input type="hidden"  id="h3" name="h3" />
				  </div>';
			//Add hidden value
			echo  '<input type="hidden" name="images_to_crop" value="3" />';
			echo  '<input type="hidden" name="image_name" value="' . $image_file .'" />';
			
			//Add the submit/canel buttons
			echo '<p><input class = "button" type="submit" name="submit" value="Crop Images And Load to Web" style= "width:200px"/>';
			echo '<input class = "button" type="submit" name="cancel" value="Cancel" /></p>';
			echo '</form>';
		}
		else
		{
		echo '<form id="coords" class="coords" method="post"
			  action="crop_image.form.handler.php" onsubmit="return validateForm(\'product_id\')">';
		echo '<p>Enter the Product Id: <input class="highlight_input" type="text" size="20" id="product_id" name="product_id" value = "' . $product_id . '"/></p>';			
		echo '<img src="' . $image_file .'" id="target"  />';
		
		echo '		<p></p>
			  <div>
					<input type="hidden" size="4" id="x1" name="x1" />
					<input type="hidden" size="4" id="y1" name="y1" />
					<input type="hidden" size="4" id="x2" name="x2" />
					<input type="hidden" size="4" id="y2" name="y2" />
					<input type="hidden" size="4" id="w" name="w" />
					<input type="hidden" size="4" id="h" name="h" />
			  </div>';
			
			//Add hidden value
			echo  '<input type="hidden" name="images_to_crop" value="1" />';
			echo  '<input type="hidden" name="image_name" value="' . $image_file .'" />';
			
			//Add the submit/canel buttons
			echo '<p><input class = "button" type="submit" name="submit" value="Crop Image And Load to Web" style= "width:200px" />';
			echo '<input class = "button" type="submit" name="cancel" value="Cancel" /></p>';
			echo '</form>';
		}


		
	include (FOOTER_FILE);
	
?>
<style type="text/css">

	ul
	{
	padding: 0px 0px 0px 20px;
	}
	
	input
	{
		border: 1px solid black;
		padding: 0px;
		margin: 0px
	}
	.highlight_input
	{
		background-color: yellow;
	}
	
</style>
	