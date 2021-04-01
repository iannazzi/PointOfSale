<?php

/* 
	* Level break down
	* 0 - nothing
	* 1 basic sales floor - add an invoice, add a customer, enter starting and ending cash drawer
	* 2 advanced sales floor - products
	* 3 sales floor manager - enter recipits
	* 4
	* 5 Back end - create PO's, add manufacturers and brands
	* 6 - 
	* 7 - General manager , stores
	* 8 - Business Owner. 
	* 9 - Pre admin account
	* 10 - Execute test scripts, delete scripts. Admin account only - not even I should log in a s a 10
	
*/

$page_level = 0;
require_once ($_SERVER['DOCUMENT_ROOT'] . '/POS/login/check_login.php');
include ($_SERVER['DOCUMENT_ROOT'] . '/POS/includes/header.html');
echo '<p class="error">You do not have clearance for this functionality</p>';
include ($_SERVER['DOCUMENT_ROOT'] . '/POS/includes/footer.html');


?>

