<?php  
/*
	*check_unique_data.php
	*this is used for form validation - is the value unique?
	*to find that data we will AJAX the shit out of it from validateData function
	*Return true or false?
 
  to test type in the url:
 	https://embrasse-moi.com/POS_TEST/Engine/includes/php/ajax_check_unique_sql.php?sql=SELECT%20*%20%20FROM%20pos_products%20WHERE%20style_number='5695'%20AND%20pos_manufacturer_brand_id='7'%20AND%20pos_product_id!='27'
 	
http://www.craigiannazzi.com/POS_TEST/Engine/includes/php/ajax_check_unique_sql.php?sql= 	SELECT%20*%20%20FROM%20pos_accounts%20WHERE%20account_number='xx7960'%20AND%20company='Capitol%20One'
 	
 */
 $page_level = 3;
require_once ('../../../Config/config.inc.php');
require_once(PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);

// Validate that the page received style number and manufacturer ID:
if ( isset($_GET['sql']) )
{

	$sql = stripSlashes(urldecode($_GET['sql']));
	//echo $sql;
	$result = checkSQLIfExists($sql);
	if ($result)
	{
		echo 'exists';
	}
	else
	{
		echo 'does not exist';
	}
	//echo $result;

}	
else
{ // No username supplied!

	echo 'Error get SQL Not supplied';

}
?>
