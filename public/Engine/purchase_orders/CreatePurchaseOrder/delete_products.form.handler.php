<?php
$binder_name = 'Purchase Orders';
$access_type = 'WRITE';
$page_title = 'Delete Products';
require_once('../po_functions.php');
require_once(PHP_LIBRARY);
if (isset($_POST['submit'])) 
{
	$dbc = startTransaction();
	$pos_purchase_order_id =  getPostOrGetID('pos_purchase_order_id');
	$errors = array();
	$product_sub_ids = getProductSubIdsFromPurchaseOrder($pos_purchase_order_id);	
	for($i = 0; $i<sizeof($product_sub_ids); $i++)
	{
		$pos_purchase_order_content_id = $product_sub_ids[$i]['pos_purchase_order_content_id'];
		$pos_product_sub_id = $product_sub_ids[$i]['pos_product_sub_id'];
		//check the links....
		if($pos_product_sub_id != 0)
		{
			
			$sql_po = "SELECT pos_purchase_order_id FROM pos_purchase_order_contents WHERE pos_product_sub_id = $pos_product_sub_id AND pos_purchase_order_id != $pos_purchase_order_id";
			$purchase_order_links = getSQL($sql_po);
			//that better be zero
			
			$sql_inv = "SELECT pos_inventory_event_content_id FROM pos_inventory_event_contents WHERE pos_product_sub_id = $pos_product_sub_id";
			$inventory_links = getSQL($sql_inv);
			//also better be zero
			
			//sales would go here...
			$sql_inv = "SELECT pos_sales_invoice_id FROM pos_sales_invoice_contents WHERE pos_product_sub_id = $pos_product_sub_id";
			$sales_links = getSQL($sql_inv);
			if(sizeof($purchase_order_links) == 0 AND sizeof($inventory_links) == 0 AND sizeof($sales_links) == 0)
			{
				//here is where we start deleteing....because the sub_is has never been sold, ordered else where, or counted in inventory.....
				
				
				
				//can we delete the product options - for example I want to get rid of 'size 34' and the fix the po for 34B
				//select the product options.
				$sql = "SELECT pos_product_option_id FROM pos_product_sub_id_options WHERE pos_product_sub_id = $pos_product_sub_id";
				$options = getSQL($sql);
				for($opt=0;$opt<sizeof($options);$opt++)
				{
					$pos_product_option_id = $options[$opt]['pos_product_option_id'];
					$sql = "SELECT pos_product_sub_id FROM pos_product_sub_id_options WHERE pos_product_option_id = $pos_product_option_id AND pos_product_sub_id != $pos_product_sub_id";
					if (sizeof(getSQL($sql))>0)
					{
						//can't delete
					}
					else
					{
						$sql = "DELETE FROM pos_product_options WHERE pos_product_option_id = $pos_product_option_id";
						runSQL($sql);
						//pprint($sql);
					}				
				}
				
				
				//can we delete the product
				//product_id - in case a power failure the sub id might delete with the product in it, in that case we can't delete the product from the sub
				//$pos_product_id = getProductIDFromProductSubId($pos_product_sub_id);
				
				$sql = "SELECT pos_product_id FROM pos_products_sub_id WHERE pos_product_sub_id = $pos_product_sub_id";
				$pos_product_id = getSingleValueSQL($sql);
				if($pos_product_id != false)
				{
					$sql = "SELECT pos_product_sub_id FROM pos_products_sub_id WHERE pos_product_id = $pos_product_id AND pos_product_sub_id != $pos_product_sub_id";
					if(sizeof(getSQl($sql))>0)
					{
						//no go there are other sub ids attached to this product...
					}
					else
					{
						//so there are no sub ids attached to this product...
						// what about the product options.... they should also go? => they should have gone before this
						$sql = "SELECT pos_product_option_id FROM pos_product_options WHERE pos_product_id = $pos_product_id";

						if(sizeof(getSQl($sql))>0)
						{
							//there are other product options so do not delete....
						}
						else
						{
							//ok to delete this product...
							$sql = "DELETE FROM pos_products WHERE pos_product_id = $pos_product_id";
							runSQL($sql);
						}
						
					}
				}
				else
				{
					//echo'false product';
				}
				
				
				//finally delete the sub id
				$sql = "DELETE FROM pos_products_sub_id WHERE pos_product_sub_id = $pos_product_sub_id";
				//pprint($sql);
				runSQL($sql);
				$sql = "DELETE FROM pos_product_sub_id_options WHERE pos_product_sub_id = $pos_product_sub_id";
				//pprint($sql);
				runSQL($sql);
				//set the sub_id to 0 on the POC
				$sql = "UPDATE pos_purchase_order_contents SET pos_product_sub_id = 0 WHERE pos_purchase_order_content_id = $pos_purchase_order_content_id";
				//pprint($sql);	
				runSQL($sql);			
			}
			else
			{
				$errors[] = preprint($purchase_order_links,'true');
				$errors[] = preprint($inventory_links,'true');
				$errors[] = preprint($sales_links,'true');
			}
		
			
		}
		else
		{
			//nothing to delete
		}
		
		//and check the inventory log
		
		
	}
	$sql = "UPDATE pos_purchase_orders SET purchase_order_status = 'DRAFT' WHERE pos_purchase_order_id = $pos_purchase_order_id";
				//pprint($sql);	
				runSQL($sql);
	if (sizeof($errors) == 0)
	{
		$message = "Products Deleted";
		header('Location: '.$_POST['complete_location'] . "&message=".$message);	
	}
	else
	{
		//include(HEADER_FILE);
		//preprint($errors);
		//include(FOOTER_FILE);
		//exit();
		$message = "SOME Products Deleted";
		header('Location: '.$_POST['complete_location'] . "&message=".$message);	
	}
	

}
else
{
	$message = 'Canceled Product Delete';
	header('Location: '.$_POST['cancel_location'] . "&message=".$message);	
}
?>
