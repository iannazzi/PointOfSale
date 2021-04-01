<?php
/*
	upload_image.form
	Craig Iannazzi 2-17-2012
*/
//load an image
	echo '<div class="indent">';
	echo '<p class="error">This Form Typically Only works in MOZILLA FIREFOX i.e. Not SAFARI</p>';
	echo 
	'<form enctype="multipart/form-data" method="post" id="upload_image_form" 
				  action="upload_image.form.handler.php" ">
	<input type="hidden" name="MAX_FILE_SIZE" value="100000000" />
	<p> 1: Select the Prodcut you are uploading: </p> 
	<p><select name="product_type" >
		<option value="false">Select a Category</option>
		<option value="Bra">Bra/Panty Set</option>
		<option value="Swim">Swim/Hosiery/WearToWork</option>
		<option value="Accessories">Shoes/Bets/Accessories</option>
	</select></p>
	<p>2: Select an Image: </p>
	<p><input type="file" name="image_file_name" id="image_file_id" size ="100"/></p>
	<p>3: Set this value to the maximum width needed  - lower values will reduce upload/processing times and reduce quality (leave it at 1024 if you are unsure)</p>
	<p><input type="text" name="max_width" id="max_width" value = "1024"/></p>
	<p>To convert image sizes quickly before uploading:</p>
	<ol>
		<li>
	<a href="resize_1536w_mac.app.zip">Download the resize_1536w_mac App</a></li>
	</ol>
	';
//Add the submit/canel buttons
echo '<p><input class = "button" type="submit" name="submit" value="Submit" />';
echo '<input class = "button" type="submit" name="cancel" value="Cancel" /></p>';
echo '</form>';
echo '</div>';

?>
