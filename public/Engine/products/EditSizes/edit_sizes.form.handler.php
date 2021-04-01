<?php
$binder_name = 'Products';
$access_type = 'WRITE';
require_once ('../product_functions.php');
require_once(PHP_LIBRARY);
$pos_product_id = getPostOrGetID('pos_product_id');
if (isset($_POST['submit'])) 
{
	
	$unsorted_sizes = getProductSizes($pos_product_id);
	//if any of these are missing we need to set them to "not active" or we can delete them if they are bogus....
	if(isset($_POST['row_number']))
	{
		$table_data_object = json_decode(stripslashes($_POST['table_data_object']) , true);
	//preprint($table_data_object);
		for($row=0;$row<sizeof($_POST['row_number']);$row++)
		{

			//if there is no barcode then we need to create the product => and sticker!
			
			$size = scrubInput($_POST['size_code'][$row]);
			//$option_name = scrubInput($_POST['option_name'][$row]);
			$sort_update['sort_index']= $_POST['row_number'][$row];
		
			$size_sort = array();
			//now we insert or we update....
			//lets get the id first, then we will update the sort
			if($size != '')
			{
				$pos_product_size_option_id = checkAndCreateProductSizeOption($pos_product_id, 'Size', $size, $size, $size_sort);
				//now update
				$key_val_id['pos_product_option_id'] = $pos_product_size_option_id;
				simpleUpdateSQL('pos_product_options', $key_val_id, $sort_update);
			}
		}
	}
	
	
	//check the original sizes
	$posted_sizes = (isset($_POST['pos_product_option_id']))? $_POST['pos_product_option_id']: array();
	for($i=0;$i<sizeof($unsorted_sizes);$i++)
	{
		if(!in_array($unsorted_sizes[$i]['pos_product_option_id'], $posted_sizes))
		{
			//ok the original size is not there.. meaning it sucked and got deleted....
			// this should be no problem to get rid of it, or set it to inactive
			$pos_product_sub_id =$unsorted_sizes[$i]['pos_product_option_id'];
			$update_array['active'] = 0;
			$key_val_id['pos_product_option_id'] = $unsorted_sizes[$i]['pos_product_option_id'];
			simpleUpdateSQL('pos_product_options', $key_val_id, $update_array);
			
			
			
			
			$sql = "SELECT pos_product_sub_id FROM pos_product_sub_id_options where pos_product_option_id = " . $pos_product_sub_id ;
			
			if (checkProductSubIdCanBeDeleted($pos_product_sub_id)==true)
			{
				$dbc=startTransaction();
				//alternatively delete it:
				$sql = "DELETE FROM pos_product_options WHERE pos_product_option_id = " .  $pos_product_sub_id ;
				runTransactionSQL($dbc, $sql);
				$sql = "DELETE FROM pos_products_sub_id WHERE pos_product_sub_id = " .  $pos_product_sub_id;
				runTransactionSQL($dbc, $sql);
				$sql = "DELETE FROM pos_product_sub_id_options WHERE pos_product_sub_id = " .  $pos_product_sub_id;
				runTransactionSQL($dbc, $sql);
				simpleCommitTransaction($dbc);

			}
			 
			//what if this option was linked to a sub id? => well now that will suck casu it is gone....
			//should the subid be deleted?
			
			//is the subid on a purchase order?
			//is the subid on a sale?
			//has the subid ever been inventoried?
			
		}
		else
		{
			//all good...
		}
	}
	
	
	
	header('Location: '.$_POST['complete_location']);
		
}
?>
