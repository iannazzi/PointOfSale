<?php
/*
	*shows a list of all registered manufacturers
*/
$page_title = 'Notes';
$binder_name = 'Notes';
$access_type = 'READ';
require_once ('../document_functions.php');

$search_fields = array(				array(	'db_field' => 'pos_note_id',
											'mysql_search_result' => 'pos_note_id',
											'caption' => 'Note ID',	
											'type' => 'input',
											'html' => createSearchInput('pos_note_id')
										),
										
										array(	'db_field' => 'note_name',
											'mysql_search_result' => 'note_name',
											'caption' => 'note Name',	
											'type' => 'input',
											'html' => createSearchInput('note_name')
										),
										array(	'db_field' => 'note_text',
											'mysql_search_result' => 'note_text',
											'caption' => 'Note Text',	
											'type' => 'input',
											'html' => createSearchInput('note_text')
										),
										array(	'db_field' => 'note_date',
											'mysql_search_result' => 'note_date',
											'caption' => 'Note Date',
											'type' => 'date',
											'html' => dateSelect('note_date',valueFromGetOrDefault('note_date'))
										),
								
								array(	'db_field' => 'last_name',
											'mysql_search_result' => 'last_name',
											'caption' => 'Last Name',	
											'type' => 'input',
											'html' => createSearchInput('last_name')
											)
									
								
										);
$table_columns = array(
		array(
			'th' => 'View',
			'mysql_field' => 'pos_discount_id',
			'get_url_link' => "discount.php?type=View",
			'url_caption' => 'View',
			'get_id_link' => 'pos_discount_id'),
		array(
			'th' => 'ID',
			'mysql_field' => 'pos_discount_id',
			'sort' => 'pos_discount_id'),	
		array(
			'th' => 'Discount Name',
			'mysql_field' => 'discount_name',
			'sort' => 'discount_name'),
		array(
			'th' => 'Start Date',
			'mysql_field' => 'start_date',
			'sort' => 'start_date'),
		array(
			'th' => 'Expiration Date',
			'mysql_field' => 'expiration_date',
			'sort' => 'expiration_date'),
		array(
			'th' => 'Discount Amount',
			'mysql_field' => 'discount_amount',
			'sort' => 'discount_amount'),
		array(
			'th' => 'Qualifying Amount',
			'mysql_field' => 'qualifying_amount',
			'sort' => 'qualifying_amount'),
		array(
			'th' => 'Post Expiration<br>Discount Amount',
			'mysql_field' => 'post_expiration_discount_amount',
			'sort' => 'post_expiration_discount_amount'),
		array(
			'th' => 'Applicable<br>To Sale Items',
			'type' =>'checkbox',
			'mysql_field' => 'check_if_can_be_applied_to_sale_items',
			'sort' => 'check_if_can_be_applied_to_sale_items'),	
		array(
			'th' => 'Active',
			'type' =>'checkbox',
			'mysql_field' => 'active',
			'sort' => 'active'),	

		);
$html = printGetMessage('message');	
//saved search functionality
$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saved_discount');

//if there is a message print it
$html = printGetMessage('message');
//here is the query that the search and table arrays are built off of.
$tmp_sql = "
CREATE TEMPORARY TABLE notes

SELECT  
		*
		FROM pos_notes
		LEFT JOIN pos_users USING (pos_user_id)
	
;


";
$tmp_select_sql = "SELECT *
	FROM notes WHERE 1";

//create the search form
$action = 'list_notes.php';
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
$html .= '<input class = "button" type="button" style="width:300px" name="add_note" value="Create Note" onclick="open_win(\'notes.php?type=Add\')"/>';

$html .= '</p>';
//now make the table
$html .= createSearchForm($search_fields,$action);
$html .= createRecordsTable($data, $table_columns);
$html .= '<script>document.getElementsByName("pos_note_id")[0].focus();</script>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
?>
