<?php
/*
	This file tells us where most of the files are
*/
define('SESSION_PATH', POS_ENGINE_PATH . '/login/sessions');
define ('CHECK_LOGIN_FILE', POS_ENGINE_PATH . '/login/check_login.php');
define ('LOGIN_FILE', POS_ENGINE_PATH . '/login/login.php');
define ('LOGOUT_URL', POS_ENGINE_URL . '/login/logout.php');
define ('HEADER_FILE', POS_ENGINE_PATH . '/includes/html/header.html');
define ('FOOTER_FILE', POS_ENGINE_PATH . '/includes/html/footer.html');
define ('STYLE_SHEET', POS_ENGINE_URL . '/includes/css/style.css');
define ('PHP_LIBRARY', POS_ENGINE_PATH . '/includes/php/php_library.php');
define ('DEBUG_FUNCTIONS', POS_ENGINE_PATH . '/includes/php/debug_functions.php');
define ('FORM_ELEMENTS', POS_ENGINE_PATH . '/includes/php/form_elements.php');
define ('MYSQL_COMMON_FUNCTIONS', POS_ENGINE_PATH . '/includes/php/mysql_common_functions.php');
define ('MYSQL_INSERT_FUNCTIONS', POS_ENGINE_PATH . '/includes/php/mysql_insert_functions.php');
define ('MYSQL_SELECT_FUNCTIONS', POS_ENGINE_PATH . '/includes/php/mysql_select_functions.php');
define ('MYSQL_TRANSACTION_FUNCTIONS', POS_ENGINE_PATH . '/includes/php/mysql_transaction_functions.php');
define ('MYSQL_DELETE_FUNCTIONS', POS_ENGINE_PATH . '/includes/php/mysql_delete_functions.php');
define ('UPLOAD_FILE_PATH', POS_PATH . '/DataFiles');
define ('DOWNLOADER_FILE', POS_ENGINE_PATH . '/includes/php/mysql_blob_downloader.php');
define ('DOWNLOADER_URL', POS_ENGINE_URL . '/includes/php/mysql_blob_downloader.php');
define ('AUTHORIZE_NET_LIBRARY', POS_PATH . '/3rdParty/AuthorizeNet/anet_php_sdk/AuthorizeNet.php');


define ('BACKUP_PATH', POS_PATH .'/DataFiles/Backup/Database');

//TCPDF
define('TCPDF_LANG', POS_PATH . '/3rdParty/tcpdf/config/lang/eng.php');
define('TCPDF',POS_PATH . '/3rdParty/tcpdf/tcpdf.php');

//phpMailer
define('PHP_MAILER', POS_PATH . '/3rdParty/PHPMailer-master/PHPMailerAutoload.php');
define('SWIFT_MAILER', POS_PATH . '/3rdParty/Swift-5.0.3/lib/swift_required.php');



?>
