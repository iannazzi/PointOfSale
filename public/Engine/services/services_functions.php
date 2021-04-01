<?php
/*
*	pos_database_commands.php
*	In an attempt to reduct the amount of code for interacting with the database I am going to include all mysql queries here
*	These are the functions need to write, update, insert, get, products, manufactureres...etc

*/

$page_level = 5;
$page_navigation = 'services';

require_once ('../../../Config/config.inc.php');
require_once (PHP_LIBRARY);
require_once (CHECK_LOGIN_FILE);
?>