<?


function createManufacturerTableDef()
{
	$data_table_def = array( 
						array( 'db_field' => 'pos_manufacturer_id',
								'type' => 'td',
								'caption' => 'System ID',
								'validate' => 'none',
								'default_value' => 'TBD',
								
								),
							
						array( 'db_field' => 'company',
								'type' => 'input',
								'autoComplete' => array(
													'url' => 'brand.ajax.php',
													'ajax_request' => 'auto_complete_company',
													'minLength' => 3,
													
													),
								'validate' => array('unique' => 'true', 'min_length' => 1),
								'db_table' => 'pos_manufacturers',
								'tags' => 'onclick="console.log(\'test\')"'), //need predictive text here....
						/*array( 'db_field' => 'pos_account_id',
								'type' => 'select',
								'caption' => 'Account ID',
								'html' => createInventoryAccountSelect('pos_account_id', 'false'),
								'validate' => 'none'),*/
						array('db_field' => 'sales_rep',
								'caption' => 'Sales Representative(s) (commas to separate names)',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'email',
								'caption' => 'email (use commas to separate Emails)',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' => 'phone',
								'type' => 'input',
								'tags' => 'maxlength = "40" ',
								'validate' => 'none'),
						array('db_field' =>  'fax',
								'type' => 'input',
								'tags' => 'maxlength = "40" ',
								'validate' => 'none'),
						array('db_field' => 'address1',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' => 'address2',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' => 'city',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' => 'state',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' => 'zip',
								'type' => 'input',
								'tags' => 'maxlength = "20" ',
								'validate' => 'none'),
						array('db_field' => 'country',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'active',
								'type' => 'checkbox',
								'default_value' => 1,
								'validate' => 'none'),
						array('db_field' => 'comments',
								'type' => 'textarea',
								'validate' => 'none'));
								
		return $data_table_def;
}
function createBrandTableDEf()
{
		$data_table_def = array( 
		array( 'db_field' => 'pos_manufacturer_id',
				'type' => 'td',
				),
		array( 'db_field' => 'brand_name',
				'type' => 'input',
				'autoComplete' => array(
													'url' => 'brand.ajax.php',
													'ajax_request' => 'auto_complete_brand',
													
													),
				'validate' => array('unique' => 'true', 'min_length' => 1),
				'db_table' => 'pos_manufacturer_brands'),
		/*array('db_field' =>  'pos_chart_of_accounts_id',
				'type' => 'select',
				'caption' => 'Link To Received Goods to Chart Of Accounts - What Type of Inventory',
				'html' => createInventoryChartOfAccountSelect('pos_chart_of_accounts_id', 'false', 'off', ' '),
				'validate' => 	array('select_value' => 'false')),*/
		/*array('db_field' =>  'sales_rep_email',
				'caption' => 'Sales Rep Email - Use this to OVERRIDE manufacturer email',
				'type' => 'input',
				'validate' => 'none'),
		array('db_field' =>  'sales_rep_name',
		'caption' => 'Sales Rep Name - Use this to OVERRIDE manufacturer sales rep name',
				'type' => 'input',
				'validate' => 'none'),
		array('db_field' =>  'sales_rep_phone',
				'type' => 'input',
				'validate' => 'none'),*/
		/*array('db_field' =>  'size_chart',
				'type' => '2Darray',
				'tags' => 'checked="checked" ',
				'validate' => 'none'),*/
		array('db_field' =>  'active',
				'type' => 'checkbox',
				'tags' => 'checked="checked" ',
				'validate' => 'none'),
		array('db_field' => 'comments',
				'type' => 'textarea',
				'validate' => 'none'));
		return $data_table_def;
}
function listBrands($pos_manufacturer_id)
{

	$html = createRecordsTable(getSQL($sql), $table_columns, 'linedTable');
	
}
function getBrandList($pos_manufacturer_id)
{
	return getSQL( "
	
	SELECT pos_manufacturer_brand_id, active, brand_name, brand_code, 
	(SELECT REPLACE(GROUP_CONCAT( REPLACE( sizes,'\\r\\n', ' ' ) ) , ',', '\\r\\n'	 )
	FROM `pos_manufacturer_brand_sizes`
	WHERE pos_manufacturer_brand_sizes.pos_manufacturer_brand_id = pos_manufacturer_brands.pos_manufacturer_brand_id) 
	as concatsizes, comments FROM pos_manufacturer_brands WHERE pos_manufacturer_id = " . $pos_manufacturer_id);
	
}
function createBrandListTableDef()
{
	return array(
			array(
				'th' => 'Edit',
				'mysql_field' => 'pos_manufacturer_brand_id',
				'get_url_link' => "../EditBrand/edit_brand.php",
				'url_caption' => 'Edit',
				'get_id_link' => 'pos_manufacturer_brand_id'),
			array(
				'th' => 'System Id <br> ',
				'mysql_field' => 'pos_manufacturer_brand_id'),
			array(
				'th' => 'Active',
				'mysql_field' => 'active'),
			array(
				'th' => 'Brand Name',
				'mysql_field' => 'brand_name'),
			array(
				'th' => 'Brand Code',
				'mysql_field' => 'brand_code'),
			array(
				'th' => 'Size Chart',
				'mysql_field' => 'concatsizes'),
			array(
				'th' => 'Edit Sizes',
				'mysql_field' => 'pos_manufacturer_brand_id',
				'get_url_link' => "../EditBrandSizeChart/edit_brand_size_chart.php",
				'url_caption' => 'Edit Sizes',
				'get_id_link' => 'pos_manufacturer_brand_id'),
			array(
				'th' => 'Comments',
				'mysql_field' => 'comments'));
}
function createAccountTableDef()
{
		$account_type = "Inventory Account";
		$pos_account_type_id = getSingleValueSQL("SELECT pos_account_type.pos_account_type_id FROM pos_account_type WHERE account_type_name = '$account_type'");
		$default_chart_of_account_id = getSingleValueSQL("SELECT default_chart_of_account_id FROM pos_account_type WHERE pos_account_type_id=$pos_account_type_id");
				// is this invetory which is an asset or an expense?
				
		$coa = getFieldRowSQL("SELECT pos_chart_of_accounts_id, account_number, account_name FROM pos_chart_of_accounts WHERE active = 1 ORDER BY account_name ASC");
		
		
		$inventory_accounts = getFieldRowSQL("SELECT * FROM pos_chart_of_accounts
	LEFT JOIN pos_chart_of_account_types USING (pos_chart_of_account_type_id)
	WHERE account_sub_type = 'Inventory' AND account_type_name = 'Current Assets'
			");
		
		return  array(
						array( 'db_field' => 'pos_account_id',
								'caption' => 'System ID',
								'type' => 'td',
								'validate' => 'none'),
						array( 'db_field' => 'company',
								'caption' => 'Easy to type nick name for quick drop down selection',
								'type' => 'input',
								'validate' => 'none'),
						array( 'db_field' => 'account_number',
								'type' => 'input',
								'encrypted' => 1,
								),
								
						array('db_field' =>  'parent_pos_chart_of_accounts_id',
								'type' => 'select',
								'caption' => 'Liability Account',
								'select_names' =>$coa['account_name'],
								'select_values' => $coa['pos_chart_of_accounts_id'],
								'default_value' => $default_chart_of_account_id,
								'validate' => array('select_value' => 'false')						
								),
				
					array('db_field' =>  'default_payment_pos_chart_of_accounts_id',
								'type' => 'select',
								'caption' => 'Default type of Inventory (This drop down will list all assets, Pick one of the inventory accounts, the option will be the default when entering invoices)',

								'select_names' =>$inventory_accounts['account_name'],
								'select_values' => $inventory_accounts['pos_chart_of_accounts_id'],
								'validate' => 'none'),
						
						array('db_field' => 'primary_contact',
								'caption' => 'Primary Representative(s) (commas to separate names)',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'email',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'legal_name',
								'caption' => 'Name to print on checks',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'address1',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'address2',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'city',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'state',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'zip',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'phone',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'fax',
								'type' => 'input',
								'validate' => 'none'),
						
						array('db_field' =>  'terms',
								'type' => 'input',
								'validate' => 'none'),
						array('db_field' =>  'days',
								'type' => 'input',
								'tags' => numbersOnly(),
								'validate' => 'none'),
						array('db_field' =>  'discount',
								'caption' => 'Discount Rate (%)',
								'type' => 'input',
								'tags' => numbersOnly(),
								'validate' => 'none'),

					
						array('db_field' => 'comments',
								'type' => 'textarea',
								'tags' => ' class="regular_textarea" ',
								'validate' => 'none'));	

}
function createAccountSelectTAbleDef()
{
	$accounts = getInventoryAccountsFieldRow();


	$columns = array(
		
				
					array('caption' => 'Account<br>Name',
					'db_field' => 'pos_account_id',
					'th_width' => '200px',
					'unique_select_options' => true,
					'type' => 'select',
					'select_names' => $accounts['company_account'],
					'select_values' => $accounts['pos_account_id'],
					)

			
			);	
		return $columns;
}
function getMfgAccountLink($pos_manufacturer_id)
{
	$account_link = getSQL("SELECT pos_account_id, company, account_number FROM pos_accounts
		LEFT JOIN pos_manufacturer_accounts USING (pos_account_id)
		WHERE pos_manufacturer_accounts.pos_manufacturer_id = $pos_manufacturer_id");
	return $account_link;
}
	
?>