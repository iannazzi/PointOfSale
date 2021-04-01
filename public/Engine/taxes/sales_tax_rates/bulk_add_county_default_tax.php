<?php
require_once ('../tax_functions.php');

$pos_state_id = 32;
$states = getSQL("SELECT * FROM pos_tax_jurisdictions WHERE pos_state_id =$pos_state_id AND local_or_state = 'State'");
$insert = array();
for($i=0;$i<sizeof($states);$i++)
{
	$insert['pos_state_id'] = $states[$i]['pos_state_id'];
	$insert['state_tax'] = $states[$i]['default_tax_rate'];
	$insert['pos_sales_tax_category_id'] = 0;
	$insert['start_date'] = '1900-00-00 00:00:00';
	$insert['sales_tax_name'] = 'Default State Tax';
	$insert['tax_type'] = 'Regular';
	$id = simpleInsertSQLReturnID('pos_sales_tax_rates', $insert);
}
$insert = array();
for($i=0;$i<sizeof($states);$i++)
{
	$insert['pos_state_id'] = $states[$i]['pos_state_id'];
	$insert['state_tax'] = $states[$i]['default_tax_rate'];
	$insert['pos_sales_tax_category_id'] = 4;
	$insert['start_date'] = '1900-00-00 00:00:00';
	$insert['sales_tax_name'] = 'State Tax Exempt';
	$insert['tax_type'] = 'Regular';
	$id = simpleInsertSQLReturnID('pos_sales_tax_rates', $insert);
}
$insert = array();
$counties = getSQL("SELECT * FROM pos_tax_jurisdictions WHERE pos_state_id =$pos_state_id AND local_or_state = 'Local'");
for($i=0;$i<sizeof($counties);$i++)
{
	$insert['pos_tax_jurisdiction_id'] = $counties[$i]['pos_tax_jurisdiction_id'];
	$insert['local_tax'] = $counties[$i]['default_tax_rate'];
	$insert['pos_sales_tax_category_id'] = 0;
	$insert['start_date'] = '1900-00-00 00:00:00';
	$insert['sales_tax_name'] = 'Default Local Tax Rate';
	$insert['tax_type'] = 'Regular';
	$id = simpleInsertSQLReturnID('pos_sales_tax_rates', $insert);
}
$insert = array();
for($i=0;$i<sizeof($counties);$i++)
{
	$insert['pos_tax_jurisdiction_id'] = $counties[$i]['pos_tax_jurisdiction_id'];
	$insert['local_tax'] = 0;
	$insert['pos_sales_tax_category_id'] = 4;
	$insert['start_date'] = '1900-00-00 00:00:00';
	$insert['sales_tax_name'] = 'Tax Exempt';
	$insert['tax_type'] = 'Regular';
	$id = simpleInsertSQLReturnID('pos_sales_tax_rates', $insert);
}

?>