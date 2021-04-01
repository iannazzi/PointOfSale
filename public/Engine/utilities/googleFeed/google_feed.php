<?php
/*
	google feed .csv file creator
	This file creates the google feed .csv file.
	This file will have to be modified a million times
	
	id	title	description	google_product_category	product_type	link	image_link	condition	availability	price	brand	mpn	gender	age_group	color	size	location	expiration_date	weight

*/
$page_title = 'Google Feed';
$page_level = 3;
$page_navigation = 'utilities';
require_once ('../../../Config/config.inc.php');
require_once(PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);

$filename = BASE_PATH . '/google_feed.txt'; 
$file_url = BASE_URL . '/google_feed.txt';
$feed_array = createGoogleBaseFeedArray();
arrayToTABCSV($filename, $feed_array, $save_keys=false);

include(HEADER_FILE);
echo '<p>Number of products: ' . (sizeof($feed_array) - 1) . '</p>' . newline();
echo '<p> The following products were exported to <a href="' . $file_url .'">'.$file_url.'</a>- Google feed will automatically pick up the file</P>';
$html =  createDataTableFromArray($feed_array);
echo $html;
include(FOOTER_FILE);


?>