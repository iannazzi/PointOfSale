<?php
/*
	crop_image.php
	Craig Iannazzi 2-16-2012
	This file will load an image and allow for cropping
	
	Features needed:
	Upload button
	resulting image
	cropping + coordinates
	Crop ration
	multiple style numbers?
	
Current Procedure:
1 - Take the photo. Put the lens cap back on. Take the card out
2 - Create a "temporary folder". I use dated name  like "2012-02-16_tmp". In the temporary folder create three sub folder: Products, preview, and thumbs
3 - "Cut" the photo off the card into a "Temporary Working Folder"
4 - Replace the card into the camera.
5 - Go to your temporary folder and rename each image to a combination of all style numbers were photographed (ex chantelle_5757_nude_bra_chantelle 5670 nude_french_knicker_panty.jpg) the name should be long to help you with the next steps
6 - Open an image for cropping. VERIFY THE IMAGE LOOKS GOOD by comparing to images on our web site. If the camera was on the wrong setting or the lights are wrong then the image will look blue, green, and generally bad. 
7 - Use the marquee tool (dotted square box tool, second from top of tools below the arrow). After clicking on the tool you can select "Fixed Ratio" on the photoshop toolbar. Use either 1:1 or 3:5 depending on what your image is. 
8 - Draw the crop box (again look at the web site to verify where you should be cropping. Bras are cropped to maximize the image of the bra).
9- Use image -> crop or shift+command+c to crop.
10 - Use image -> image size to set the size, see the sizes below.
11 File -> save as. Name the image to the identical name as the styule number, lower case only. EX ch5575nude.jpg. Put the image in the correct folder. Note you will end up with multiple images of the same name but with different sizes in different folders.
12 If you have to make mulitple sizes of the same image you can now "undo" the last image size change, then enter the new size, then again file - save as
13 FTP the images in your temporary folder to the www/images/ folder
14 move the original image to a the manufacturer's folder.


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


Secondary Images: IF NEEDED
Secondary images can be loaded into pinnacle cart. Currently this is a pain so we rarely do it, but here is what is needed to have the images correct:
Secondary image: 400x400 or 400x600
thumb: 100x100
	
	
	products: /www/images/products   size: 400x400 or 400x600
	preview: /www/images/products/preview size 400x400 (this is for the bra only)
	thumbs: /www/images/products/thumbs: 210 x 210 or 210 x 350
	secondary: /www/images/products/secondary : 400x400 or 400x600
	secondary: /www/images/products/secondary/thumbs: 100x100


*/
$page_level = 5;
$page_navigation = 'products';
$page_title = 'Upload image';
require_once ('../../../Config/config.inc.php');
require_once (PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);

//Header
include (HEADER_FILE);
include('upload_image.form.php');
//Footer
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
	
</style>

