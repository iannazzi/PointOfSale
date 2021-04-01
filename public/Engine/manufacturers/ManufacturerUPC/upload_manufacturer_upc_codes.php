<?php
/*
	*upload_manufacturer_upcs.php
	*Craig Iannazzi 2-14-12
	
	*page for loading manufacturer upcs
	*Will include an upload format and instructions
	
*/
$binder_name = 'Manufacturer UPC\'s';
$access_type = 'WRITE';
ini_set('upload_max_filesize', '200M');
ini_set('post_max_size', '200M');
ini_set('max_input_time', 3000);
ini_set('max_execution_time', 3000);
ini_set('memory_limit', '512M');
require_once ('../manufacturer_functions.php');
$page_title = 'Upload UPC\'s';


$pos_manufacturer_id = getPostOrGetID('pos_manufacturer_id');
if (checkForValidIdinPOS($pos_manufacturer_id, 'pos_manufacturers', 'pos_manufacturer_id'))
{
	include (HEADER_FILE);
	
	echo '<p></p>';
	echo '<h2>Upload UPC\'s For ' . getManufacturerName($pos_manufacturer_id) . '</h2>';
	echo '<p></p>';
	
	echo '
	<form enctype="multipart/form-data" action="upload_manufacturer_upc_codes.form.handler.php" method="post"
	enctype="multipart/form-data">';
	
	//<input type="hidden" name="MAX_FILE_SIZE" value="1000000000" />
	echo '<p class ="error" >File must be .CSV format. You will be able to choose delimiters and header rows in the next step.</p>
	<label for="file">Filename:</label>
	<input type="file" name="upc_code_file" id="upc_code_file" />
	<a href="manufacturer_upc_codes_template.csv">Download the .csv File Template</a>
	<br />';
	//Add the submit/canel buttons
	echo '<p><input type="submit" class = "button" name="upload_file" value="Submit" onclick="needToConfirm=false;"/>';
	echo '<input type="submit" class = "button" name="cancel" value="Cancel" onclick="needToConfirm=false;"/></p>';
	echo '<input type="hidden" name="pos_manufacturer_id" value="' . $pos_manufacturer_id . '" />';
	echo '</form>';
	include (FOOTER_FILE);
}
else
{
	//no valid manufacturer id
	//Header
	include (HEADER_FILE);
	echo 'error - no valid mfg ID';
	include (FOOTER_FILE);
}


?>
