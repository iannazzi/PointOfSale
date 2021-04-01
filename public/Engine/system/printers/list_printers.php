<?php
/*
	*shows a list of all registered manufacturers
*/
$page_title = 'Printers';
$binder_name = 'Printers';
$access_type = 'READ';
require_once ('../system_functions.php');

$search_fields = array(				array(	'db_field' => 'pos_printer_id',
											'mysql_search_result' => 'pos_printer_id',
											'caption' => 'Printer ID',	
											'type' => 'input',
											'html' => createSearchInput('pos_printer_id')
										),
										array(	'db_field' => 'pos_store_id',
											'mysql_search_result' => 'pos_store_id',
											'caption' => 'Store',	
											'type' => 'select',
											'html' => createStoreSelect('pos_store_id', 'false', 'on', '')
										),
										array(	'db_field' => 'printer_name',
											'mysql_search_result' => 'printer_name',
											'caption' => 'Printer Name',	
											'type' => 'input',
											'html' => createSearchInput('printer_name')
										),
										array(	'db_field' => 'printer_description',
											'mysql_search_result' => 'printer_description',
											'caption' => 'Printer Description',	
											'type' => 'input',
											'html' => createSearchInput('printer_description')
										)
							
									
								
										);
$table_columns = array(
		array(
			'th' => 'View',
			'mysql_field' => 'pos_printer_id',
			'get_url_link' => "printers.php?type=View",
			'url_caption' => 'View',
			'get_id_link' => 'pos_printer_id'),
		array(
			'th' => 'ID',
			'mysql_field' => 'pos_printer_id',
			'sort' => 'pos_printer_id'),
		array(
			'th' => 'Store Name',
			'mysql_field' => 'store_name',
			'sort' => 'store_name'),	
		array(
			'th' => 'Printer Name',
			'mysql_field' => 'printer_name',
			'sort' => 'printer_name'),
		array(
			'th' => 'Media',
			'mysql_field' => 'media',
			'sort' => 'media'),
		array(
			'th' => 'Printer Type/Description',
			'mysql_field' => 'printer_description',
			'sort' => 'title'),
		array(
			'th' => 'Location',
			'mysql_field' => 'location',
			'sort' => 'location'),
		
		
		array(
			'th' => 'Active',
			'type' =>'checkbox',
			'mysql_field' => 'active',
			'sort' => 'active'),	

		);
$html = printGetMessage('message');	
//saved search functionality
$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saved_printers');

//if there is a message print it
$html = printGetMessage('message');
//here is the query that the search and table arrays are built off of.
$tmp_sql = "
CREATE TEMPORARY TABLE printers

SELECT  
		pos_printers.*, store_name
		FROM pos_printers
		LEFT JOIN pos_stores USING (pos_store_id)
		



		


;


";
$tmp_select_sql = "SELECT *
	FROM printers WHERE 1";

//create the search form
$action = 'list_printers.php';
//Create Search String (AND's after MYSQL WHERE from $_GET data)
$search_sql = createSearchSQLStringMultipleDates($search_fields);
$tmp_select_sql  .= $search_sql;


//Create the order sting to append to the sql statement
$order_by = createSortSQLString($table_columns, $table_columns[1]['mysql_field'], 'DESC');
$tmp_select_sql  .=  " ORDER BY $order_by";


$tmp_select_sql  .=  " LIMIT 100";
$dbc = openPOSdb();
$result = runTransactionSQL($dbc,$tmp_sql);
$data = getTransactionSQL($dbc,$tmp_select_sql);
closeDB($dbc);

//create some buttons
//Add a button to add an expense

$html .= '<p>';

$html .= '<input class = "button" type="button" style="width:300px" name="add_invoice" value="Add A Printer" onclick="open_win(\'printers.php?type=Add\')"/>';

$html .= '</p>';
//now make the table
$html .= createSearchForm($search_fields,$action);
$html .= createRecordsTable($data, $table_columns);
$html .= '<script>document.getElementsByName("pos_printer_id")[0].focus();</script>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
?>
