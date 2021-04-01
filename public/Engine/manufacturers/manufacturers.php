<?PHP
/*
	*manufacturers.php
	*Craig Iannazzi
*/


require_once ('../../Config/config.inc.php');
require_once(PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);

header('Location: ' . POS_ENGINE_URL . '/manufacturers/ListManufacturers/list_manufacturers.php');	

?>
