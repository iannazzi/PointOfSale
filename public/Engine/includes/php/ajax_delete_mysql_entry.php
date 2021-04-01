<?php  
/*
	*check_unique_data.php
	*this is used for form validation - is the value unique?
	*to find that data we will AJAX the shit out of it from validateData function
	*Return true or false?
 
  to test type in the url:
 	http://www.craigiannazzi.com/POS_TEST/includes/php/ajax_check_unique_table_field_value.php?table=pos_manufacturers&field=manufacturer_code&value=ADA
 	
 	http://www.craigiannazzi.com/POS_TEST/includes/php/ajax_check_unique_table_field_value.php?table=pos_manufacturer_brands&field=brand_code&value=LO
 	
 	http://www.craigiannazzi.com/POS_TEST/includes/php/ajax_check_unique_table_field_value.php?table=pos_manufacturers&field=company&value=LO
 	
 	http://www.craigiannazzi.com/POS_TEST/includes/php/ajax_check_unique_table_field_value.php?table=pos_manufacturers&field=company&value=ADA&id_name=pos_manufacturer_id&id=55
 	
 */
 
// Validate that the page received style number and manufacturer ID:

if ( (isset($_GET['table'])) && (isset($_GET['id_name'])) && (isset($_GET['id_value'])) ) 
{
$page_level = 3;
require_once ('../../../Config/config.inc.php');
require_once(PHP_LIBRARY);
require_once (CHECK_LOGIN_FILE);
	
	$table = scrubInput($_GET['table']);
	$id_name = scrubInput($_GET['id_name']);
	$id_value = scrubInput($_GET['id_value']);
	
	$sql = "DELETE FROM " . $table . " WHERE " .$field . "='" . $value . "'";\

	$result = runSQL($sql);

	if ($result)
	{
		echo 'deleted';
	}
	else
	{
		echo 'error';
	}
	//echo $result;

}	
else
{ // No username supplied!

	echo 'Error, table, id and value not supplied';

}
?>
