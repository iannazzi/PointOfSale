<?php
/*
	*shows a list of all registered manufacturers
*/
$binder_name = "Sales Tax Rates";
$page_title = 'Sales Tax Rates';
require_once ('../tax_functions.php');

$search_fields = array(				array(	'db_field' => 'pos_sales_tax_rate_id',
											'mysql_search_result' => 'pos_sales_tax_rate_id',
											'caption' => 'Rate ID',	
											'type' => 'input',
											'html' => createSearchInput('pos_sales_tax_rate_id')
										),
										

						array( 'db_field' => 'pos_sales_tax_category_id',
								'type' => 'select',
								'caption' => 'Default Sales Tax Category',
								'html' => createSalesTaxCategorySelect('pos_sales_tax_category_id', 'false')),
						array(	'db_field' => 'start_date',
											'mysql_search_result' => 'start_date',
											'caption' => 'Start Date',
											'type' => 'start_date',
											'html' => dateSelect('start_date_start_date',valueFromGetOrDefault('start_date_start_date'))
										),
								array(	'db_field' => 'start_date',
											'mysql_search_result' => 'start_date',
											'caption' => 'Start_date End',	
											'type' => 'start_date',
											'html' => dateSelect('start_date_end_date',valueFromGetOrDefault('start_date_end_date'))
										),
						array('db_field' =>  'state',
								'type' => 'input',
								'caption' => 'State',
								'html' => createSearchInput('tax_category_name')),
						array('db_field' =>  'state_tax',
								'type' => 'input',
								'caption' => 'State Rate (%)',
								'html' => createSearchInput('state_tax')),
						array('db_field' =>  'county',
								'type' => 'input',
								'caption' => 'Jurisdiction Name',
						'html' => createSearchInput('county')),
						array('db_field' =>  'zip_code',
								'type' => 'input',
								'caption' => 'Zip Code',
								'html' => createSearchInput('zip_code')),
						array('db_field' =>  'local_tax',
								'type' => 'input',
								'caption' => 'Local Rate (%)',
								'html' => createSearchInput('local_tax'))
										
										
										
										);
$table_columns = array(
		array(
			'th' => 'View',
			'mysql_field' => 'pos_sales_tax_rate_id',
			'get_url_link' => "add_edit_view_sales_tax_rate.php?type=view",
			'url_caption' => 'View',
			'get_id_link' => 'pos_sales_tax_rate_id'),
		array(
			'th' => 'ID',
			'mysql_field' => 'pos_sales_tax_rate_id',
			'sort' => 'pos_sales_tax_rate_id'),	
		array(
			'th' => 'Start<br>Date',
			'mysql_field' => 'start_date',
			'mysql_search_result' => 'start_date',
			'date_format' => 'date',
			'sort' => 'start_date'),
		array(
			'th' => 'Sales Tax Category Name',
			'mysql_field' => 'tax_category_name',
			'sort' => 'tax_category_name'),
		array(
			'th' => 'State',
			'mysql_field' => 'state',
			'sort' => 'state'),
		
		array(
			'th' => 'Jurisdiction Name',
			'mysql_field' => 'jurisdiction_name',
			'sort' => 'jurisdiction_name'),
		array(
			'th' => 'Local or State',
			'mysql_field' => 'local_or_state',
			'sort' => 'local_or_state'),
		array(
			'th' => 'Tax Rate',
			'mysql_field' => 'tax_rate',
			'round' => 3,
			'sort' => 'tax_rate'),
		array(
			'th' => 'Tax Type',
			'mysql_field' => 'tax_type',
			'sort' => 'tax_type'),
		array(
			'th' => 'Exemption Value',
			'mysql_field' => 'exemption_value',
			'round' => 2,
			'sort' => 'exemption_value')
			
			
			
		
		);
$html = printGetMessage('message');	
//saved search functionality
$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saved_tax_rates_url');

//if there is a message print it
$html = printGetMessage('message');
//here is the query that the search and table arrays are built off of.
$tmp_sql = "
CREATE TEMPORARY TABLE tmp

SELECT  
		pos_sales_tax_rates.*,
		if(pos_sales_tax_rates.pos_sales_tax_category_id = 0, 'All', pos_sales_tax_categories.tax_category_name) as tax_category_name,
		pos_states.name as state, 
		pos_tax_jurisdictions.local_or_state,
		pos_tax_jurisdictions.jurisdiction_name
		
		FROM pos_sales_tax_rates
		LEFT JOIN pos_sales_tax_categories USING(pos_sales_tax_category_id)
		
		LEFT JOIN pos_tax_jurisdictions ON pos_tax_jurisdictions.pos_tax_jurisdiction_id = pos_sales_tax_rates.pos_tax_jurisdiction_id
		LEFT JOIN pos_states ON pos_tax_jurisdictions.pos_state_id = pos_states.pos_state_id

;


";
$tmp_select_sql = "SELECT *
	FROM tmp WHERE 1";

//create the search form
$action = 'list_sales_tax_rates.php';
//Create Search String (AND's after MYSQL WHERE from $_GET data)
$search_sql = createSearchSQLStringMultipleDates($search_fields);
$tmp_select_sql  .= $search_sql;

//Create the order sting to append to the sql statement
$order_by = createSortSQLString($table_columns, $table_columns[1]['mysql_field'], 'DESC');
$tmp_select_sql  .=  " ORDER BY $order_by";


//$tmp_select_sql  .=  " LIMIT 100";
$dbc = openPOSdb();
$result = runTransactionSQL($dbc,$tmp_sql);
$data = getTransactionSQL($dbc,$tmp_select_sql);
closeDB($dbc);

//create some buttons
//Add a button to add an expense
$html .= '<p>';
$html .= '<input class = "button" type="button" style="width:300px" name="add_category" value="Add State Sales Tax Rate" onclick="open_win(\'add_edit_view_sales_tax_rate.php?type=Add&jurisdiction=State\')"/>';
//$html .= '<input class = "button" type="button" style="width:300px" name="add_category" value="Add Local Sales Tax Rate" onclick="open_win(\'add_edit_sales_tax_rate.php?type=Add&jurisdiction=Local\')"/>';
$html .= '<input class = "button" type="button" style="width:300px" name="add_category" value="Add Local Sales Tax Rate" onclick="open_win(\'select_state.php?type=Add&jurisdiction=Local\')"/>';
$html .= '<input class = "button" type="button" style="width:300px" name="create_rates" value="Create Default Rates" onclick="open_win(\'create_default_tax_rates.php\')"/>';
$html .= '</p>';
//now make the table
$html .= createSearchForm($search_fields,$action);
$html .= createRecordsTable($data, $table_columns);
$html .= '<script>document.getElementsByName("zip")[0].focus();</script>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
?>
