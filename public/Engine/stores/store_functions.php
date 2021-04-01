<?php
$page_level = 3;

require_once ('../../Config/config.inc.php');
require_once(PHP_LIBRARY);
require_once (CHECK_LOGIN_FILE);


function createStoreTableDef($type, $pos_store_id)
{


	
	
	$db_table = 'pos_stores';
	if ($pos_store_id =='TBD')
	{
		$name_validate = array('unique' => 'store_name', 'min_length' => 1);
	}
	else
	{
		$key_val_id['pos_store_id'] = $pos_store_id;
		$name_validate = array('unique' => 'store_name', 'id' => $key_val_id, 'min_length' => 1);


	}
	return array( 
						array( 'db_field' => 'pos_store_id',
								'type' => 'input',
								'tags' => ' readonly = "readonly" ',
								'caption' => 'Store ID',
								'value' => $pos_store_id,
								'validate' => 'none'
								
								),
						array('db_field' =>  'store_name',
								'type' => 'input',
								'caption' => 'Store Name',
								'db_table' => $db_table,
								'validate' => $name_validate),
						array('db_field' =>  'pos_state_id',
								'type' => 'select',
								'html' => createStateSelect('pos_state_id', 'false', 'off', ' onchange="updateTaxJurisdictionList(this, \'pos_tax_jurisdiction_id\')" '),
								'caption' => 'State',
								'validate' => 'none'),
						array('db_field' =>  'pos_tax_jurisdiction_id',
								'type' => 'select',
								'html' => createLocalTaxJurisdictionSelect('pos_tax_jurisdiction_id', 'false', 'all'),
								'caption' => 'Local Tax Jurisdiction (Typically your County)'),
						
						array('db_field' =>  'company',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'shipping_address1',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'shipping_address2',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'shipping_city',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'shipping_state',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'shipping_zip',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'shipping_country',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'shipping_province',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'email',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'phone',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'fax',
								'type' => 'input',
								'validate' => 'none'),
								
						array('db_field' =>  'billing_address1',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'billing_address2',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'billing_city',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'billing_state',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'billing_zip',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'billing_country',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'billing_province',
								'type' => 'input',
								'validate' => 'none'),		
						array('db_field' =>  'comments',
								'type' => 'input',
								'caption' => 'Comments',
								'validate' => 'none'),			
								
								
								
						array('db_field' =>  'active',
								'type' => 'checkbox',
								'caption' => 'Active',
								'validate' => 'none',
								'value' => 1)		
						);	

}
 
?>