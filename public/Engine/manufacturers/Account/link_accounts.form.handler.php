<?php

$binder_name = 'Manufacturers';
$access_type = 'WRITE';
require_once('../manufacturer_functions.php');
$page_title = 'Link an Account To a Manufacturer';

$pos_manufacturer_id = getPostOrGetID('pos_manufacturer_id');
$complete_location = '../ViewManufacturer/view_manufacturer.php?pos_manufacturer_id='.$pos_manufacturer_id;
$cancel_location = $complete_location . '&message=canceled';

if (isset($_POST['submit'])) 
{

	//this is also a bulk product loader, although I am feeling that needs to be a bit different....
	$html = '';
	if(isset($_POST['row_number']))
	{
		$counter = 0;
		$table_data_object = json_decode(stripslashes($_POST['account_table_data_object']) , true);
		//delete
		$delete_q = "DELETE FROM pos_manufacturer_accounts WHERE pos_manufacturer_id = $pos_manufacturer_id";
		runSQL($delete_q);
		for($row=0;$row<sizeof($_POST['row_number']);$row++)
		{
			$pos_account_id = scrubInput($table_data_object['pos_account_id'][$row]);
			//handle the checkmark
			$default_account = ($table_data_object['default_account'][$row])?1:0;

			
			
			$insert_array = array( 	'pos_manufacturer_id' => $pos_manufacturer_id,
									'pos_account_id' => $pos_account_id,
									'default_account' => $default_account
									
								);
			$result = simpleInsertSQL('pos_manufacturer_accounts', $insert_array);
		
		}
	}
	//where to go to?
	$message = urlencode('Accounts Updated');
	header('Location: '.addgetToURL($complete_location, 'message=' .$message));


	
}
else
{
	header('Location: '.$cancel_location);
}
?>