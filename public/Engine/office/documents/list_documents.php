<?php
/*
	*shows a list of all registered manufacturers
*/
$page_title = 'Documents';
$binder_name = 'Documents';
$access_type = 'READ';
require_once ('../document_functions.php');

$search_fields = array(				array(	'db_field' => 'pos_document_id',
											'mysql_search_result' => 'pos_document_id',
											'caption' => 'Note ID',	
											'type' => 'input',
											'html' => createSearchInput('pos_document_id')
										),
										
										array(	'db_field' => 'document_name',
											'mysql_search_result' => 'document_name',
											'caption' => 'Document Name',	
											'type' => 'input',
											'html' => createSearchInput('note_name')
										),
										array(	'db_field' => 'document_text',
											'mysql_search_result' => 'document_text',
											'caption' => 'Document Text',	
											'type' => 'input',
											'html' => createSearchInput('document_text')
										),
										array(	'db_field' => 'document_date',
											'mysql_search_result' => 'document_date',
											'caption' => 'Document Date',
											'type' => 'date',
											'html' => dateSelect('document_date',valueFromGetOrDefault('document_date'))
										)
									
								
										);
$table_columns = array(
		array(
			'th' => 'View',
			'mysql_field' => 'pos_document_id',
			'get_url_link' => "documents.php?type=View",
			'url_caption' => 'View',
			'get_id_link' => 'pos_document_id'),
		array(
			'th' => 'ID',
			'mysql_field' => 'pos_document_id',
			'sort' => 'pos_document_id'),	
		array(
			'th' => 'Document Name',
			'mysql_field' => 'document_name',
			'sort' => 'document_name'),
		array(
			'th' => 'Document Date',
			'mysql_field' => 'document_date',
			'sort' => 'document_date'),
		
		array(
			'th' => 'Document Overview',
			'mysql_field' => 'document_overview',
			'sort' => 'document_overview')
		);
$html = printGetMessage('message');	
//saved search functionality
$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saved_documents');

//if there is a message print it
$html = printGetMessage('message');
//here is the query that the search and table arrays are built off of.
$tmp_sql = "
CREATE TEMPORARY TABLE docuemnt

SELECT  
		*
		FROM pos_documents
		LEFT JOIN pos_users USING (pos_user_id)
	
;


";
$tmp_select_sql = "SELECT *
	FROM docuemnt WHERE 1";

//create the search form
$action = 'list_documents.php';
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
$html .= '<input class = "button" type="button" style="width:300px" name="add_note" value="Create Document" onclick="open_win(\'documents.php?type=Add\')"/>';

$html .= '</p>';
//now make the table
$html .= createSearchForm($search_fields,$action);
$html .= createRecordsTable($data, $table_columns);
$html .= '<script>document.getElementsByName("pos_document_id")[0].focus();</script>';

include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
?>
