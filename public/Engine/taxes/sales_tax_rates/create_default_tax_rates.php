<?php 
/*
	Craig Iannazzi 10-28-12	
*/
$binder_name = "Sales Tax Rates";
$page_title = "Default Sales Tax Rates";
require_once ('../tax_functions.php');
$complete_location = 'list_sales_tax_rates.php';
$cancel_location = 'list_sales_tax_rates.php?message=Canceled';

//first we need to show the tax categories
if (ISSET($_POST['pos_sales_tax_category_id']))
{
	$pos_sales_tax_category_id = $_POST['pos_sales_tax_category_id'];
	$html = createDefaultTaxRatesFromJurisdictions($pos_sales_tax_category_id);
	include (HEADER_FILE);
	echo $html;
	include (FOOTER_FILE);
}
else
{
	//$html = createSalesTaxCategorySelect('pos_sales_tax_category_id', 'false'));
	
	
	$data_table_def = array( 
							array( 'db_field' => 'pos_sales_tax_category_id',
									'type' => 'select',
									'caption' => 'Select Sales Tax Category',
									'html' => createSalesTaxCategorySelect('pos_sales_tax_category_id', 'false'),
									'validate' => array('select_value' => 'false'))
								);
	$form_handler = 'create_default_tax_rates.php';
	$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);
	$html ='<p>This will create a default tax rate starting on 1900-01-01 using the default rate for all tax jurisdictions. It will not overwrite existing rates at 1900-01-01 for a tax category and jurisdiction. Changes in jurisdiction are still a bit tricky.<p>';
	$html .= createFormForMYSQLInsert($data_table_def ,$big_html_table, $form_handler, $complete_location, $cancel_location);
	$html .= '<script>document.getElementById(\'pos_sales_tax_category_id\').focus()</script>';
	include (HEADER_FILE);
	echo $html;
	include (FOOTER_FILE);
	
}

?>