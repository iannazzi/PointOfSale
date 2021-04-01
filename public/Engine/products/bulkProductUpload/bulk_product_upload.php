<?PHP
/*
	*bulk_product_uploader.php
	*Form to upload product data
	*Craig Iannazzi 2-3-12
*/



$page_level = 5;
$page_navigation = 'products';
$page_title = 'Bulk Upload Products';
require_once ('../includes/config.inc.php');
require_once (PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);

include (HEADER_FILE);
echo '<h2 class = "error">Only use this to initialize your data!!!</h2>';
echo '<a href="bulkUploadCsvFile.php">Download the Bulk File Uploader in CSV Format </a>';
echo '<a href="bulk_download.php">Download the current products in CSV Format </a>';
echo '

<form enctype="multipart/form-data" action="bulk_product_uploader.form.handler.php" method="post"
enctype="multipart/form-data">
<input type="hidden" name="MAX_FILE_SIZE" value="100000000" />
<label for="file">Filename:</label>
<input type="file" name="uploadedfile" id="file" />
<br />';

//Add the submit/canel buttons
echo '<p><input type="submit" name="submit" value="Submit" />';
echo '<input type="submit" name="cancel" value="Cancel" /></p>';

echo '</form>';

//Footer
include (FOOTER_FILE);


';
?>
