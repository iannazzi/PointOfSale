<?php
//Every file calls this config file first.... this will get us to the database and the library of files
//This will also allow us to turn on/off the system and set test system on/off
//Every 'constant' that we need that is system independent should be in here
//every variable that is system dependedant should be in the database

//most importatnt is the database connection. This is stored outside the web tree directory
//define the connection to the database

$path = '/var/www/private';
$connection_file = 'pos_database_connection.php';
$web_store_conneciton_file = 'web_store_database_connection.php';


define('ADMIN_EMAIL', 'admin@embrasse-moi.com');
define('LIVE', TRUE);
/****************************** DATABASE Connection ************************************/
define ('MYSQL_POS_CONNECT_FILE', $path . '/' . $connection_file);


define('COMPANY_NAME', 'Embrasse-Moi');
define('COMPANY_LEGAL_NAME', 'Embrasse-Moi, LLC');
define('COMPANY_LOGO', 'E M B R A S S E - M O I');
define('LOGO_FONT', 'Times New Roman');
define('SUPPORT_EMAIL', 'admin@embrasse-moi.com');
define('OFFICE_EMAIL', 'office@embrasse-moi.com');
//define('PURCHASE_ORDERS_CC_EMAIL', 'purchase_orders@embrasse-moi.com');
define ('BASE_URL', 'http://localhost:89');
define ('UNSECURE_URL', 'http://localhost:89');
// ************ THESE SETTING SHOULDN't NEED TO BE CHANGED ************ //
define ('POS_URL', BASE_URL . '');
define ('POS_ENGINE_URL', POS_URL . '/Engine');
define('BASE_PATH', $_SERVER['DOCUMENT_ROOT']);
define('POS_PATH', BASE_PATH . '');
define('POS_ENGINE_PATH', POS_PATH . '/Engine');
require_once (POS_ENGINE_PATH . '/includes/php/file_paths.php');

/****************************** WEBSTORE Connection ************************************/
define ('WEB_STORE_URL', 'http://embrasse-moi.com');
define ('WEB_STORE_PATH', $_SERVER['DOCUMENT_ROOT']);
define('WEB_STORE_ACTIVE', true);
define('WEB_STORE_NAME', 'Pinnacle Cart 3.7.8');
define ('WEBSTORE_MYSQL_CONNECT_FILE',  $path . '/' . $web_store_conneciton_file);
define('WEB_STORE_MODULE', POS_PATH . '/Modules/WebStore/pinnacle_cart_module.php');

date_default_timezone_set ('UTC');
// ************ ERROR MANAGEMENT ************ //
require_once (POS_ENGINE_PATH . '/includes/php/error_handler.php');
set_error_handler ('pos_error_handler');


?>