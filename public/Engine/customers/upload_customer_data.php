<?php
/*
	*upload_customers.php
	*Craig Iannazzi 10-23-12
	
*/
$binder_name = 'Customers';
$access_type = 'WRITE';
ini_set('upload_max_filesize', '200M');
ini_set('post_max_size', '200M');
ini_set('max_input_time', 3000);
ini_set('max_execution_time', 3000);
ini_set('memory_limit', '512M');
require_once ('customer_functions.php');
$page_title = 'Upload Customers';

include (HEADER_FILE);

echo '<p></p>';
echo '<h2>Upload Customers</h2>';
echo '<p></p>';
echo '
<form enctype="multipart/form-data" action="upload_customers.form.handler.php" method="post"
enctype="multipart/form-data">';

//<input type="hidden" name="MAX_FILE_SIZE" value="1000000000" />
echo '<p class ="error" >File must be .CSV format. You will be able to choose delimiters and header rows in the next step.</p>
<label for="file">Filename:</label>
<input type="file" name="customer_file" id="customer_file" />';
//Add the submit/canel buttons
echo '<p><input type="submit" class = "button" name="upload_file" value="Submit" onclick="needToConfirm=false;"/>';
echo '<input type="submit" class = "button" name="cancel" value="Cancel" onclick="needToConfirm=false;"/></p>';
echo '</form>';
include (FOOTER_FILE);



?>
