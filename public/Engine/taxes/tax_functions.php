<?php
$page_level = 3;

require_once ('../../../Config/config.inc.php');
require_once(PHP_LIBRARY);
require_once (CHECK_LOGIN_FILE);

function createDefaultTaxRatesFromJurisdictions($pos_sales_tax_category_id)
{
	$html = '<p>Checking and adding rates</p>';
	//need to select the category
	//get all the "jurisdictions"
	//for each jurisdiction create an entry on 1900-01-01
	$jurisdictions =  getSQL("SELECT * FROM pos_tax_jurisdictions");
	for($i=0;$i<sizeof($jurisdictions);$i++)
	{
		$pos_tax_jurisdiction_id = $jurisdictions[$i]['pos_tax_jurisdiction_id'];
		$start_date = '1900-01-01';
		$insert= array();
		$insert['pos_tax_jurisdiction_id'] = 	$pos_tax_jurisdiction_id;
		$insert['tax_rate'] = $jurisdictions[$i]['default_tax_rate'];
		$insert['pos_sales_tax_category_id'] = $pos_sales_tax_category_id;
		$insert['sales_tax_name'] = 'Default';
		$insert['tax_type'] = 'Regular';
		$insert['start_date'] = $start_date;
		
		//if there is already an entry for 1900-01-01 for the jurisdiction and category then igonre
		$sql = "SELECT pos_sales_tax_rate_id FROM pos_sales_tax_rates WHERE pos_sales_tax_category_id = $pos_sales_tax_category_id AND pos_tax_jurisdiction_id = $pos_tax_jurisdiction_id AND start_date = '$start_date'";
		$check = getSQL($sql);
		if(sizeof($check)==0)
		{
			//insert
			$pos_sales_tax_rate_id = simpleInsertSQLReturnID('pos_sales_tax_rates', $insert);
			$html .= '<p>Inserted Tax Rate ID: '.$pos_sales_tax_rate_id . '</p>';
		}
	}
	return $html;
}
function createTaxJurisdictionTableDef($type, $pos_tax_jurisdiction_id)
{
	$db_table = 'pos_tax_jurisdictions';
	if ($pos_tax_jurisdiction_id =='TBD')
	{
		$name_validate = array('unique' => 'jurisdiction_name', 'min_length' => 1);
		$code_validate = array('unique' => 'jurisdiction_code', 'min_length' => 1);
	}
	else
	{
		$key_val_id['pos_tax_jurisdiction_id'] = $pos_tax_jurisdiction_id;
		$name_validate = array('unique' => 'jurisdiction_name', 'id' => $key_val_id, 'min_length' => 1);
		$code_validate = array('unique' => 'jurisdiction_code', 'id' => $key_val_id, 'min_length' => 1);


	}
	return array( 
						array( 'db_field' => 'pos_tax_jurisdiction_id',
								'type' => 'input',
								'tags' => ' readonly = "readonly" ',
								'caption' => 'Sales Tax Jurisdiction ID',
								'value' => $pos_tax_jurisdiction_id,
								'validate' => 'none'
								
								),
						array('db_field' =>  'pos_state_id',
								'type' => 'select',
								'html' => createStateSelect('pos_state_id', 'false', 'off'),
								'caption' => 'State',
								'validate' => 'none'),
						array('db_field' =>  'jurisdiction_name',
								'type' => 'input',
								'caption' => 'Jurisdiction Name',
								'db_table' => $db_table,
								'validate' => $name_validate),
						array('db_field' =>  'jurisdiction_code',
								'type' => 'input',
								'caption' => 'Jurisdiction Code',
								'db_table' => $db_table,
								'validate' => $code_validate),
						array('db_field' =>  'default_tax_rate',
								'type' => 'input',
								'caption' => 'Default Tax Rate',
								'tags' => numbersOnly(),
								'validate' => 'number'),
						array('db_field' =>  'local_or_state',
								'type' => 'select',
								'html' => createEnumSelect('local_or_state','pos_tax_jurisdictions','local_or_state', 'false', 'off', ''),
								'caption' => 'Local or State',
								'validate' => 'none'),	
						array('db_field' =>  'active',
								'type' => 'checkbox',
								'caption' => 'Active',
								'validate' => 'none',
								'value' => 1)		
						);	

}
function createTaxCategoryTableDef($type, $pos_sales_tax_category_id)
{
	if ($pos_sales_tax_category_id =='TBD')
	{
		$unique_validate = array('unique' => 'tax_category_name', 'min_length' => 1);

	}
	else
	{
		$key_val_id['pos_sales_tax_category_id'] = $pos_sales_tax_category_id;
		$unique_validate = array('unique' => 'tax_category_name', 'id' => $key_val_id, 'min_length' => 1);

	}
	return array( 
						array( 'db_field' => 'pos_sales_tax_category_id',
								'type' => 'input',
								'tags' => ' readonly = "readonly" ',
								'caption' => 'Sales Tax Category ID',
								'value' => $pos_sales_tax_category_id,
								'validate' => 'none'
								
								),
						array('db_field' =>  'tax_category_name',
								'type' => 'input',
								'caption' => 'Tax Category Name',
								'db_table' => 'pos_sales_tax_categories',
								'validate' => $unique_validate),
						array('db_field' =>  'tax_exempt',
								'type' => 'checkbox',
								'caption' => 'Check For Tax Exempt',
								'validate' => 'none'),
						array('db_field' =>  'active',
								'type' => 'checkbox',
								'caption' => 'Active',
								'validate' => 'none',
								'value' => 1)
						);	

}
function createSalesTaxRateTableDef($type, $pos_sales_tax_rate_id)
{
	if ($pos_sales_tax_rate_id =='TBD')
	{
		//$unique_validate = array('unique' => 'tax_category_name', 'min_length' => 1);

	}
	else
	{
		//$key_val_id['pos_sales_tax_category_id'] = $pos_sales_tax_category_id;
		//$unique_validate = array('unique' => 'tax_category_name', 'id' => $key_val_id, 'min_length' => 1);

	}
	$pos_state_id =  getStateIDFromSalesTaxRate($pos_sales_tax_rate_id);

	$date_change = '';
	return array( 
						array( 'db_field' => 'pos_sales_tax_rate_id',
								'type' => 'input',
								'tags' => ' readonly = "readonly" ',
								'caption' => 'Sales Tax Rate ID',
								'value' => $pos_sales_tax_rate_id,
								'validate' => 'none'
								
								),
						array('db_field' =>  'pos_tax_jurisdiction_id',
								'type' => 'select',
								'html' => createLocalTaxJurisdictionSelect('pos_tax_jurisdiction_id', 'false', $pos_state_id, 'false'),
								'caption' => 'Tax Jurisdiction',
								'validate' => 'none'),
						array('db_field' => 'start_date',
								'caption' => 'Start Date',
								'type' => 'date',
								'value' => '',
								'tags' => $date_change,
								'html' => dateSelect('invoice_date','',$date_change),
								'validate' => 'date'),
						array( 'db_field' => 'pos_sales_tax_category_id',
								'type' => 'select',
								'caption' => 'Default Sales Tax Category',
								'html' => createSalesTaxCategorySelect('pos_sales_tax_category_id', 'false')),
						
						
						array('db_field' =>  'tax_rate',
								'type' => 'input',
								'caption' => 'Tax Rate (%)',
								'tags' => numbersOnly(),
								'validate' => 'number'),
						
						array( 'db_field' => 'tax_type',
								'type' => 'select',
								'caption' => 'Sales Tax Type',
								'html' => createTaxTypeSelect('tax_type', 'false')),
						array('db_field' =>  'exemption_value',
								'type' => 'input',
								'caption' => 'Maximum Value For Exemption if Exempt (otherwise leave blank)',
								'tags' => numbersOnly(),
								'validate' => 'number')
						
						
						
						);	

} 
function createStateSalesTaxRateTableDef($type, $pos_sales_tax_rate_id)
{
	if ($pos_sales_tax_rate_id =='TBD')
	{
		//$unique_validate = array('unique' => 'tax_category_name', 'min_length' => 1);

	}
	else
	{
		//$key_val_id['pos_sales_tax_category_id'] = $pos_sales_tax_category_id;
		//$unique_validate = array('unique' => 'tax_category_name', 'id' => $key_val_id, 'min_length' => 1);

	}
	$date_change = '';
	return array( 
						array( 'db_field' => 'pos_sales_tax_rate_id',
								'type' => 'input',
								'tags' => ' readonly = "readonly" ',
								'caption' => 'Sales Tax Rate ID',
								'value' => $pos_sales_tax_rate_id,
								'validate' => 'none'
								
								),
						array('db_field' =>  'pos_tax_jurisdiction_id',
								'type' => 'select',
								'html' => createStateTaxJurisdictionSelect('pos_tax_jurisdiction_id', 'false', 'off'),
								'caption' => 'Select State (Must be included in tax jurisdictions to create a tax rate)',
								'validate' => 'none'),
						array('db_field' => 'start_date',
								'caption' => 'Start Date',
								'type' => 'date',
								'value' => '',
								'tags' => $date_change,
								'html' => dateSelect('invoice_date','',$date_change),
								'validate' => 'date'),
						array( 'db_field' => 'pos_sales_tax_category_id',
								'type' => 'select',
								'caption' => 'Default Sales Tax Category',
								'html' => createSalesTaxCategorySelect('pos_sales_tax_category_id', 'false', 'on')),
						
						
						array('db_field' =>  'tax_rate',
								'type' => 'input',
								'caption' => 'State Rate (%)',
								'tags' => numbersOnly(),
								'validate' => 'number'),
						array( 'db_field' => 'tax_type',
								'type' => 'select',
								'caption' => 'Sales Tax Type',
								'html' => createTaxTypeSelect('tax_type', 'false')),
						array('db_field' =>  'exemption_value',
								'type' => 'input',
								'caption' => 'Maximum Value For Exemption if Exempt (otherwise leave blank)',
								'tags' => numbersOnly(),
								'validate' => 'number')
						
						
						
						);	

} 
function createLocalSalesTaxRateTableDef($type, $pos_sales_tax_rate_id, $pos_state_id)
{
	if ($pos_sales_tax_rate_id =='TBD')
	{
		//$unique_validate = array('unique' => 'tax_category_name', 'min_length' => 1);
		$all_counties = 'on';
	}
	else
	{
		//$key_val_id['pos_sales_tax_category_id'] = $pos_sales_tax_category_id;
		//$unique_validate = array('unique' => 'tax_category_name', 'id' => $key_val_id, 'min_length' => 1);
		$all_counties = 'off';

	}
	$date_change = '';
	return array( 
						array( 'db_field' => 'pos_sales_tax_rate_id',
								'type' => 'input',
								'tags' => ' readonly = "readonly" ',
								'caption' => 'Sales Tax Rate ID',
								'value' => $pos_sales_tax_rate_id,
								'validate' => 'none'
								
								),
								array('db_field' =>  'pos_tax_jurisdiction_id',
								'type' => 'select',
								'html' => createLocalTaxJurisdictionSelect('pos_tax_jurisdiction_id', 'false', $pos_state_id, 'false', $all_counties),
								'caption' => 'Tax Jurisdiction',
								'validate' => 'none'),
						array( 'db_field' => 'pos_sales_tax_category_id',
								'type' => 'select',
								'caption' => 'Default Sales Tax Category',
								'html' => createSalesTaxCategorySelect('pos_sales_tax_category_id', 'false', 'on')),

						array('db_field' => 'start_date',
								'caption' => 'Start Date',
								'type' => 'date',
								'value' => '',
								'tags' => $date_change,
								'html' => dateSelect('invoice_date','',$date_change),
								'validate' => 'date'),
						
						array('db_field' =>  'tax_rate',
								'type' => 'input',
								'caption' => 'Local Rate (%)',
								'tags' => numbersOnly(),
								'validate' => 'number'),
						array( 'db_field' => 'tax_type',
								'type' => 'select',
								'caption' => 'Sales Tax Type',
								'html' => createTaxTypeSelect('tax_type', 'false')),
						array('db_field' =>  'exemption_value',
								'type' => 'input',
								'caption' => 'Maximum Value For Exemption if Exempt (otherwise leave blank)',
								'tags' => numbersOnly(),
								'validate' => 'number')
						
						
						
						);	

} 
?>