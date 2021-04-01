<?php
/*
	*View_manufacturers.php
	*shows a list of all registered manufacturers
*/
$binder_name = 'Manufacturers';
$access_type = 'READ';
require_once ('../manufacturer_functions.php');


$html = '';//includeJavascriptLibrary();

include (HEADER_FILE);
//if there is a message print it
$html .= printGetMessage('message');
//here is the query that the search and table arrays are built off of.
$sql = "

SELECT pos_manufacturers.pos_manufacturer_id, pos_manufacturers.company,
		(SELECT GROUP_CONCAT( brand_name) 
		FROM pos_manufacturer_brands
		WHERE pos_manufacturer_brands.pos_manufacturer_id = pos_manufacturers.pos_manufacturer_id AND active = 1) as brand_names,
		(SELECT GROUP_CONCAT( brand_code) 
		FROM pos_manufacturer_brands
		WHERE pos_manufacturer_brands.pos_manufacturer_id = pos_manufacturers.pos_manufacturer_id AND active = 1) as brand_codes,
	pos_manufacturers.sales_rep, pos_manufacturers.email, pos_manufacturers.phone, pos_manufacturers.fax
	FROM pos_manufacturers
	WHERE pos_manufacturers.active = 1 

";
	
//define the search table
$search_fields = array(				array(	'db_field' => 'company',
											'mysql_search_result' => 'pos_manufacturers.company',
											'caption' => 'Company',	
											'type' => 'input',
											'html' => createSearchInput('company')
										),
										array(	'db_field' => 'brand_name',
											'mysql_search_result' => '(SELECT GROUP_CONCAT( brand_name) 
		FROM pos_manufacturer_brands
		WHERE pos_manufacturer_brands.pos_manufacturer_id = pos_manufacturers.pos_manufacturer_id AND active = 1)',
											'caption' => 'Brand Name',	
											'type' => 'input',
											'html' => createSearchInput('brand_name')
										));
$table_columns = array(
		array(
			'th' => 'View',
			'mysql_field' => 'pos_manufacturer_id',
			'get_url_link' => "../ViewManufacturer/view_manufacturer.php",
			'url_caption' => 'View',
			'get_id_link' => 'pos_manufacturer_id'),
		array(
			'th' => 'Company',
			'mysql_field' => 'company',
			'sort' => 'pos_manufacturers.company'),
		array(
			'th' => 'Brand Name',
			'mysql_field' => 'brand_names',
			'sort' => 'brand_names'),
		array(
			'th' => 'Brand Code',
			'mysql_field' => 'brand_codes',
			'sort' => 'brand_codes'),
		array(
			'th' => 'Sales Rep',
			'mysql_field' => 'sales_rep',
			'sort' => 'pos_manufacturers.sales_rep'),
		array(
			'th' => 'Sales Email',
			'mysql_field' => 'email',
			'sort' => 'pos_manufacturers.email'),
		array(
			'th' => 'Phone',
			'mysql_field' => 'phone',
			'sort' => 'pos_manufacturers.phone'),
		array(
			'th' => 'Fax',
			'mysql_field' => 'fax',
			'sort' => 'pos_manufacturers.fax')			
		);

//Add a button to add an expense
$html .= '<p>';
$html .= '<input class = "button" type="button" name="add_manufacturer" value="Add Manufacturer" onclick="open_win(\'../AddManufacturer/add_manufacturer.php\')"/>';
$html .= '<input class = "button" type="button" style="width:200px" name="manufacturer_emails" value="Get All Manufacturer Emails" onclick="open_win(\'../ManufacturerEmails/manufacturer_emails.php\')"/>';
$html .= '<input class = "button" type="button" style="width:200px" name="upcs" value="View Manufacturer UPC\'s" onclick="open_win(\'../ManufacturerUPC/list_upcs.php\')"/>';
$html .= '</p>';

//create the search form

$action = 'list_manufacturers.php';
$html .= createSearchForm($search_fields,$action);
//Create Search String (AND's after MYSQL WHERE from $_GET data)
$search_sql = createSearchSQLStringMultipleDates($search_fields);
$sql  .= $search_sql;
//Create the order sting to append to the sql statement
$order_by = createSortSQLString($table_columns, $table_columns[1]['mysql_field'], 'ASC');
$sql  .=  " ORDER BY $order_by";

//now make the table
$html .= createRecordsTable(getSQL($sql), $table_columns);
$html .= '<script>document.getElementsByName("company")[0].focus();</script>';
echo $html;

include (FOOTER_FILE);
?>
