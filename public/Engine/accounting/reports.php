<?php
/*
	products.php
	Craig Iannazzi 2-17-2012
	
	This is the main page to access products
*/

$page_level = 5;
$page_navigation = 'accounting';
$page_title = 'Business Reports';
require_once ($_SERVER['DOCUMENT_ROOT'] . '/POS/login/check_login.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/POS/includes/config.inc.php');

//Header
include ($_SERVER['DOCUMENT_ROOT'] . '/POS/includes/header.html');

echo'
<div class="settingsSpace">
		<li>
			<p><a href="operating_expenses/operating_expenses.php">Operating Expenses</a></p>
			<div class="settingsItemComment">
				View Profit Statement			
			</div>
		</li>
		<li>
			<a href="cash_flow/cash_flow.php">Cash Flow Analysis</a>
			<div class="settingsItemComment">
				View Cash Flow Analysis		
			</div>
		</li>
		<li>
			<a href="balance_sheet/balance_sheet.php">Balance Sheet</a>
			<div class="settingsItemComment">
				Still in Development, Balance Sheet Analysis.	
			</div>
		</li>
</div>';


//Footer
include ($_SERVER['DOCUMENT_ROOT'] . '/POS/includes/footer.html');
?>