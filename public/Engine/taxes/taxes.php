<?php
/*
	products.php
	Craig Iannazzi 2-17-2012
	
	This is the main page to access products
*/
$page_level = 5;
$page_navigation = 'taxes';
$page_title = 'Taxes';
require_once ('../../Config/config.inc.php');
require_once(PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);

//$html = '<table><tr><td>';
//$html .= '<div class="settingsSpace">';
//$html .= '<ul>';
//journals

	$html = '<div class = "verticle_top_pad_div"></div>';
	$html .= '<div class = "no_line_tight_divider">';
	$html .= '<p>Sales Tax</p>';
	$html .= '<input class = "indexButton" type="button"  name="Tax_rates"  style = "width:200px" value="Sales Tax Rates" onclick="open_win(\'sales_tax_rates/list_sales_tax_rates.php\')"/>';
	$html .= '<input class = "indexButton" type="button"  name="Tax_Categories"  style = "width:200px" value="Sales Tax Categories" onclick="open_win(\'sales_tax_categories/list_sales_tax_categories.php\')"/>';
	$html .= '<input class = "indexButton" type="button"  name="Tx_jurisdictions"  style = "width:200px" value="Sales Tax Jurisdictions" onclick="open_win(\'sales_tax_jurisdictions/list_tax_jurisdictions.php\')"/>';
	$html .= '</div>';
	
	$html .= '<div class = "tight_divider">';
	$html .= '<p>Payroll Tax</p>';

	$html .= '</div>';
	
	$html .= '<div class = "tight_divider">';
	$html .= '<p>Queries</p>';
	$html .= '</div>';
	
	$html .= '<div class = "tight_divider">';
	$html .= '<p>Reports</p>';
	$html .= '</div>';
	
		$html .= '<div class = "tight_divider">';
	$html .= '<p>Other Tax</p>';
	$html .= '</div>';
	
	
	//$html .= '</div>';
//	$html .= '</ul></div>';
	//$html .= '</td></tr></table>';
include (HEADER_FILE);
echo $html;				
include (FOOTER_FILE);
?>