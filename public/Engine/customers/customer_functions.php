<?php
$page_level = 3;
$page_navigation = 'customers';

require_once ('../../Config/config.inc.php');
require_once(PHP_LIBRARY);
require_once (CHECK_LOGIN_FILE);


function createCustomerTableDef($type, $pos_customer_id)
{
	if ($pos_customer_id =='TBD')
	{
	}
	else
	{
	}
	$date_change ='';
	return array( 
						array( 'db_field' => 'pos_customer_id',
								'type' => 'input',
								'tags' => ' readonly = "readonly" ',
								'caption' => 'Customer ID',
								'value' => $pos_customer_id,
								'validate' => 'none'
								),
						array('db_field' =>  'first_name',
								'type' => 'input',
								'caption' => 'First Name',
								'validate' => 'none'),
						array( 'db_field' => 'last_name',
								'caption' => 'Last Name',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'email1',
								'type' => 'input',
								'validate' => 'none'),
						
						array('db_field' =>  'phone',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'active',
								'type' => 'checkbox',
								'tags' => 'checked="checked" ',
								'validate' => 'none'),
						/*array('db_field' => 'date_added',
								'caption' => 'Date Added',
								'type' => 'date',
								'value' => '',
								'tags' => $date_change,
								'html' => dateSelect('date_added','',$date_change),
								'validate' => 'date'),*/
						array('db_field' => 'comments',
								'type' => 'textarea',
								'tags' => ' class="big_textarea" ',
								'validate' => 'none'));	

}


?>