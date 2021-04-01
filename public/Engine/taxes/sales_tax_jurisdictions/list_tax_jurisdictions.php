<?php
/*
	*shows a list of all registered manufacturers
*/
$page_title = 'Sales Tax Jurisdictions';
require_once ('../tax_functions.php');

$search_fields = array(		
						array(	'db_field' => 'pos_tax_jurisdiction_id',
											'mysql_search_result' => 'pos_tax_jurisdiction_id',
											'caption' => 'Jurisdiction ID',	
											'type' => 'input',
											'html' => createSearchInput('pos_tax_jurisdiction_id')
										),
						array( 'db_field' => 'state_name',
								'type' => 'input',
								'caption' => 'State',
								'html' => createSearchInput('state_name')),
						
						array('db_field' =>  'jurisdiction_name',
								'type' => 'input',
								'caption' => 'Jurisdiction Name',
								'html' => createSearchInput('jurisdiction_name')),
						array('db_field' =>  'jurisdiction_code',
								'type' => 'input',
								'caption' => 'Jurisdiction Code',
								'html' => createSearchInput('jurisdiction_code')),
						
						array('db_field' =>  'local_or_state',
								'type' => 'input',
								'caption' => 'Jurisdiction Scope',
								'html' => createSearchInput('local_or_state')),
						array('db_field' =>  'default_tax_rate',
								'type' => 'input',
								'caption' => 'Default Tax Rate',
						'html' => createSearchInput('default_tax_rate'))
										);
$table_columns = array(
		array(
			'th' => 'View',
			'mysql_field' => 'pos_tax_jurisdiction_id',
			'get_url_link' => "add_edit_view_tax_jurisdiction.php?type=view",
			'url_caption' => 'View',
			'get_id_link' => 'pos_tax_jurisdiction_id'),
		array(
			'th' => 'ID',
			'mysql_field' => 'pos_tax_jurisdiction_id',
			'sort' => 'pos_tax_jurisdiction_id'),	
			array(
			'th' => 'State',
			'mysql_field' => 'state_name',
			'sort' => 'state_name'),
		array(
			'th' => 'Jurisdiction Name',
			'mysql_field' => 'jurisdiction_name',
			'mysql_search_result' => 'jurisdiction_name',
			'sort' => 'jurisdiction_name'),
		
		array(
			'th' => 'Jurisdiction Code',
			'mysql_field' => 'jurisdiction_code',
			'sort' => 'jurisdiction_code'),
		
		array(
			'th' => 'Jurisdiction Scope',
			'mysql_field' => 'local_or_state',
			'sort' => 'local_or_state'),
		array(
			'th' => 'Default Tax Rate',
			'mysql_field' => 'default_tax_rate',
			'sort' => 'default_tax_rate',
			'round' => 3),
			
			
		
		);
$html = printGetMessage('message');	
//saved search functionality
$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saved_tax_jurisdictions');

//if there is a message print it
$html = printGetMessage('message');
//here is the query that the search and table arrays are built off of.
$tmp_sql = "
CREATE TEMPORARY TABLE tmp

SELECT  
		pos_tax_jurisdictions.*, pos_states.short_name as state_name

		FROM pos_tax_jurisdictions
		LEFT JOIN pos_states ON pos_tax_jurisdictions.pos_state_id = pos_states.pos_state_id


;


";
$tmp_select_sql = "SELECT *
	FROM tmp WHERE active=1";

//create the search form
$action = 'list_sales_tax_juridictions.php';
//Create Search String (AND's after MYSQL WHERE from $_GET data)
$search_sql = createSearchSQLStringMultipleDates($search_fields);
$tmp_select_sql  .= $search_sql;

//Create the order sting to append to the sql statement
$order_by = createSortSQLString($table_columns, $table_columns[1]['mysql_field'], 'ASC');
$tmp_select_sql  .=  " ORDER BY $order_by";


//$tmp_select_sql  .=  " LIMIT 100";
$dbc = openPOSdb();
$result = runTransactionSQL($dbc,$tmp_sql);
$data = getTransactionSQL($dbc,$tmp_select_sql);
closeDB($dbc);

//create some buttons
//Add a button to add an expense
$html .= '<p>';
$html .= '<input class = "button" type="button" style="width:300px" name="add_category" value="Add Sales Tax Jurisdiction" onclick="open_win(\'add_edit_view_tax_jurisdiction.php?type=add\')"/>';
$html .= '</p>';
//now make the table
$html .= createSearchForm($search_fields,$action);
$html .= createRecordsTable($data, $table_columns);
$html .= '<script>document.getElementsByName("state_name")[0].focus();</script>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
?>
