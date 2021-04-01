<?php

$binder_name = 'Products';
$access_type = 'Write';
require_once ('../product_functions.php');	
$page_title = 'Product Sub Id';

if (isset($_POST['submit'])) 
{
	//preprint($_POST);
	$table_data_object = json_decode(stripslashes($_POST['table_data_object']) , true);
	$table_data_array = json_decode(stripslashes($_POST['table_data_array']) , true);
	//preprint($table_data_object);
	//preprint($table_data_array);
	$date_added = getDateTime();
	$pos_product_sub_id = $_POST['pos_product_sub_id'];
	$pos_product_id = getProductIdFromProductSubId($pos_product_sub_id);

	if(isset($_POST['row_number']))
	{
		$pos_product_options_ids = $_POST['pos_product_option_id'];

		$option_array = '(' . implode(',',$pos_product_options_ids) . ')';

		
		$sql = "SELECT pos_product_sub_id_options.pos_product_sub_id  FROM pos_product_sub_id_options
			LEFT JOIN pos_products_sub_id ON pos_product_sub_id_options.pos_product_sub_id = pos_products_sub_id.pos_product_sub_id WHERE pos_product_option_id IN ".$option_array . " GROUP BY pos_product_sub_id_options.pos_product_sub_id HAVING COUNT(*) = " . sizeof($pos_product_options_ids);
		
		$subids = getSQL($sql);
		if(sizeof($subids) == 0)
		{
			// we can insert
			//first clear out
			$dbc = startTransaction();
			$sql = "DELETE FROM pos_product_sub_id_options WHERE pos_product_sub_id = $pos_product_sub_id";
			runTransactionSQl($dbc,$sql);
			for($row=0;$row<sizeof($_POST['row_number']);$row++)
			{
				$pos_product_option_id = $_POST['pos_product_option_id'][$row];
				$sql2 = "INSERT INTO pos_product_sub_id_options (pos_product_sub_id, pos_product_option_id) VALUES ($pos_product_sub_id, $pos_product_option_id)"; 
				runTransactionSQl($dbc,$sql2);
				
			
			
			}
			simpleCommitTransaction($dbc);
			
			//go 
			$message = "message=" . urlencode("SubId Updated");
			header('Location: '.addGetToURL($_POST['complete_location'], $message) );
		}
		else
		{
			 'hello?' ;
			//we have an error.. display the error,then exit
			include(HEADER_FILE);
			pprint( 'Error: Subid already Exists -- And I told you you shouldn\'t be here anyway....');
			preprint($subids);
			include(FOOTER_FILE);
			exit();
		}
			
		
		
	}
}


?>