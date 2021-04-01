<?php 

/*
	A manufacturer needs to have an account to enter invoices
	The account can of course change, so the manufacturer needs to link to accounts
	
*/
$binder_name = 'Manufacturers';
$access_type = 'READ';
require_once('../manufacturer_functions.php');
$page_title = 'Link an Account To a Manufacturer';

$pos_manufacturer_id = getPostOrGetID('pos_manufacturer_id');
$complete_location = '../ViewManufacturer/view_manufacturer.php?pos_manufacturer_id='.$pos_manufacturer_id;
$cancel_location = $complete_location . '&message=canceled';
//now we want to add a link, remove a link, and see the links...

//lets do a dynamic table!!

$form_id = 'linkAccount';
$form_handler = 'link_accounts.form.handler.php';
		$account_table_name = 'account_table';
		$account_table_def = createMfgAccountTableDef($account_table_name);
		$account_data = getSQL("SELECT pos_manufacturer_accounts.pos_account_id, pos_accounts.company, pos_manufacturer_accounts.default_account FROM pos_accounts
								LEFT JOIN pos_manufacturer_accounts USING(pos_account_id)
								WHERE pos_manufacturer_accounts.pos_manufacturer_id = $pos_manufacturer_id");
		$html_table = createDynamicTableReuse($account_table_name, $account_table_def, $account_data,$form_id);
		$html_table .= createhiddenInput('pos_manufacturer_id', $pos_manufacturer_id);
		
$html = '<h3>Select Accounts Here.</h3>';
$html .=  '<form id = "' . $form_id . '" action="'.$form_handler.'" method="post" onsubmit="return prepareDynamicTableForPost()">';
	//invoice header
$html .= $html_table;
$html .= '<p><input class ="button" type="submit"  id = "submit" name="submit" value="Submit" onclick="account_table_object.copyHTMLTableDataToObject();needToConfirm=false;"/>' .newline();
	$html .= '<input class = "button" type="submit" name="cancel" value="Cancel" "/>';
$html .= '</form>';

//$html .= createFormForDynamicTableMYSQLInsert($account_table_def, $html_table, $form_handler, $complete_location, $cancel_location);
//$html .= '<script>document.getElementsByName("barcode")[0].focus();</script>';

//$html .= createManufacturerAccountsRecordTable($pos_manufacturer_id);





include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
function createMfgAccountTableDef($table_name)
{
	$table_object_name = $table_name . '_object';
	
	$accounts = getInventoryAccountsFieldRow();

	$columns = array(
		

				

				array('db_field' => 'none',
					'POST' => 'no',
					'caption' => '',
					'th_width' => '14px',
					'type' => 'checkbox',
					'element' => 'input',
					'element_type' => 'checkbox',
					'properties' => array(	'onclick' => 'function(){'.$table_object_name.'.setSingleCheck(this);}'
											)),
				array(
				'db_field' => 'row_number',
				'caption' => 'Row',
				'type' => 'input',
				'element' => 'input',
				'element_type' => 'none',
				'properties' => array(	'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
										
										'size' => '"3"')
					),
				
					array('caption' => 'Account<br>Name',
					'db_field' => 'pos_account_id',
					'unique_select_options' => true,
					'type' => 'select',
					'select_names' => $accounts['company'],
					'select_values' => $accounts['pos_account_id'],
					'properties' => array(	'style.width' => '"7em"',
											'className' => '"nothing"',
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);}',
											'onblur' => 'function(){}',
											'onkeyup' => 'function(){}',
											'onmouseup' => 'function(){}')),

				array('caption' => 'Default<br>Account',
				'db_field' => 'default_account',
					'type' => 'checkbox',
					'element' => 'input',
					'element_type' => 'checkbox',
					'properties' => array(	'size' => '"3"',
											
											'onclick' => 'function(){'.$table_object_name.'.setCurrentRow(this);'.$table_object_name.'.setSingleCheck(this);}')),
		
				
			);			
					
	
	return $columns;
	
	
	
}
?>