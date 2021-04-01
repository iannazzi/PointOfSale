<?php
/*
	products.php
	Craig Iannazzi 2-17-2012
	
	This is the main page to access products
*/
$page_level = 5;
$page_navigation = 'accounting';
$page_title = 'Accounting';

require_once ('../../Config/config.inc.php');
require_once(PHP_LIBRARY);
require_once (CHECK_LOGIN_FILE);

//$html = '<table><tr><td>';
//$html .= '<div class="settingsSpace">';
//$html .= '<ul>';
//journals

	$html = '<div class = "verticle_top_pad_div"></div>';
	$html .= '<div class = "no_line_tight_divider">';
	$html .= '<p>Journals</p>';
	$html .= '<input class = "indexButton" type="button"  name="Purchases Journal"  style = "width:200px" value="Purchases Journal" onclick="open_win(\'PurchaseJournal/list_purchase_journal.php\')"/>';
	$html .= '<input class = "indexButton" type="button"  name="General Journal" style = "width:200px" value="General Journal" onclick="open_win(\'GeneralJournal/list_general_journal.php\')"/>';
	$html .= '<input class = "indexButton" type="button"  name="Payments Journal" style = "width:200px" value="Payments Journal" onclick="open_win(\'PaymentsJournal/list_payments_journal.php\')"/>';
	$html .= '</div>';
	
	$html .= '<div class = "tight_divider">';
	$html .= '<p>Accounts</p>';
	$html .= '<input class = "indexButton" type="button"  name="Accounts" style = "width:200px" value="Accounts" onclick="open_win(\'Accounts/list_accounts.php\')"/>';

	$html .= '<input class = "indexButton" type="button"  name="Account Balances" style = "width:200px" value="Account Balances" onclick="open_win(\'AccountBalances/list_account_balances.php\')"/>';
	$html .= '</div>';
	
	$html .= '<div class = "tight_divider">';
	$html .= '<p>Queries</p>';
	$html .= '<input class = "indexButton" type="button"  name="Bills_Due" style = "width:200px" value="Bills Due" onclick="open_win(\'BillsDue/list_bills_due.php\')"/>';
	$html .= '</div>';
	
	$html .= '<div class = "tight_divider">';
	$html .= '<p>Reports</p>';
	$html .= '<input class = "indexButton" type="button"  style = "width:200px" name="Operating Expenses" value="Operating Expenses" onclick="open_win(\'operating_expenses/operating_expenses.php\')"/>';
	$html .= '</div>';
	
		$html .= '<div class = "tight_divider">';
	$html .= '<p>Accounting Setup</p>';
	$html .= '<input class = "indexButton" type="button"  name="Chart Of Accounts" style = "width:200px" value="Chart Of Accounts" onclick="open_win(\'ChartOfAccounts/list_chart_of_accounts.php\')"/>';
	$html .= '</div>';
	
	
	//$html .= '</div>';
//	$html .= '</ul></div>';
	//$html .= '</td></tr></table>';
include (HEADER_FILE);
echo $html;				
include (FOOTER_FILE);
?>