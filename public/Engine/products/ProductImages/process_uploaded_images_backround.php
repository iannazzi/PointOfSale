<?
$binder_name = 'Images';
$access_type = 'Write';
$page_title = 'Product Image Bulk Uploader';
require_once ('../product_functions.php');	

exec(echo \'/usr/bin/php -q process_uploaded_images.php\' | at now');


$html = '';
$html = '<p>Processesing Images In The Background</p>';
$html .= '<p>' . createUserButton('Images') .'</p>';
include(HEADER_FILE);
echo $html;
include(FOOTER_FILE);

?>