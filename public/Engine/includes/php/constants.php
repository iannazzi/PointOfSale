<?php
/*
	this is where we should update the javascript library versions
	we need to version the javascript files to force browser to reload them
*/


define ('PRODUCT_IMAGE_FOLDER', '/DataFiles/ProductImages/');
define ('PRODUCT_IMAGE_THUMBNAIL_FOLDER', '/DataFiles/ProductImages/thumbs/');
define ('IMAGE_UPLOAD_PATH', POS_PATH . '/DataFiles/image_uploads/files');
define ('INVOICE_PRINT_FOLDER', POS_PATH . '/PrintQueue/invoices/');
define ('CHECK_PRINT_FOLDER', POS_PATH . '/PrintQueue/checks/');
define ('CUSTOM_IMAGES_FOLDER', POS_PATH . '/SystemFiles/CustomImages/');

define ('SUCCESS_BEEP_FILENAME', POS_ENGINE_URL . '/includes/sounds/success_beep');
define ('ERROR_BEEP_FILENAME', POS_ENGINE_URL . '/includes/sounds/error_beep');
define ('SUCCESS_BEEP_FILE', POS_ENGINE_URL . '/includes/sounds/success_beep.wav');
define ('ERROR_BEEP_FILE', POS_ENGINE_URL . '/includes/sounds/error_beep.wav');
define ('SUCCESS_BEEP_FILE_OGG', POS_ENGINE_URL . '/includes/sounds/success_beep.ogg ');
define ('ERROR_BEEP_FILE_OGG', POS_ENGINE_URL . '/includes/sounds/error_beep.ogg');
define ('SUCCESS_BEEP_FILE_MP3', POS_ENGINE_URL . '/includes/sounds/success_beep.mp3');
define ('ERROR_BEEP_FILE_MP3', POS_ENGINE_URL . '/includes/sounds/error_beep.mp3');

?>