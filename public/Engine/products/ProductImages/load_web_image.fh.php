<?
/*
	The image coming in has a list of products on it. 
	Some of those products will already be made. Some will not.
	
	We will need to either overwrite the products with the listed products, or skip the product generation
	
	After submitting we will then need to check for what products exist.
	If products exist:
	come back with a list of existing products with a check box to overwrite OR add image as a secondary, or add image as primary
	
	For example:
	fantasie bikini top (new product)
	fantasie bikini bottom (new product)
	tru jewelry (new product)
	pur la victio jackyn wedge (existing product)
	
	I want to add the top three products
	I might want to add the image to the pour la victio as primary -or- secondary
	
	IN all cases I want to add the recommended products....
	If there is an image on a page, then the recommended products should show
	
	So if I push the image to pur la victoir product page, I need to add the recommended products to that page.....
	
	
*/

$binder_name = 'Images';
$access_type = 'Write';
$page_title = 'Images';
require_once ('../product_functions.php');	
$html = '';

$type = getPostOrGetValue('type');
$pos_product_image_id = getPostOrGetID('pos_product_image_id');

$web_path = '/var/www/embrasse-moi.com';
$web_url = 'http://embrasse-moi.com';




$image_file_path = POS_PATH . PRODUCT_IMAGE_FOLDER  . $_POST['pos_product_image_id'] .'.jpg';

	$tmp_path = BASE_PATH  . '/tmp';
	if (!file_exists($tmp_path)) {
		mkdir($tmp_path, 0777, true);
	}
	$tmp_image = $tmp_path . '/tmp_image.jpg';
	$product_image_path = $web_path . '/images/products';
	$preview_image_path = $product_image_path . '/preview';
	$thumbs_image_path = $product_image_path . '/thumbs';
	$secondary_image_path = $product_image_path . '/secondary';
	$secondary_thumbs_image_path = $secondary_image_path . '/thumbs';

$main_width = 1024;
$thumb_width = 210;
$preview_width = 600;
				
if (isset($_POST['submit'])) 
{
	if (strtoupper($type) == 'PRIMARY')
	{

		$pcart_dbc = openWebStoreDatabase();
		//trigger_error('openend...');
		$date_added = getDateTime();
		if (isset($_POST['overwrite_check'])) 
		{
			//error checking is complete/verified, however re-grab the posted tdo
			$product_table_data_object = deserializeTableDef($_POST['product_table_data_object']);
			//preprint($product_table_data_object);	
		}
		else
		{
			//need to check existing_ids
			$existing_ids = array();
			$product_table_data_object = json_decode(stripslashes($_POST['product_table_data_object']) , true);		
			

			//error checking - catagaries and existing products
			for($row=0;$row<sizeof($product_table_data_object['row_number']);$row++)
			{
				$pos_product_sub_id = $product_table_data_object['pos_product_sub_id'][$row];
				$pos_product_id = $product_table_data_object['pos_product_id'][$row];
				//if the category is not there error
				$categories = explode('||', $product_table_data_object['catagories'][$row]);
				for($cat=0;$cat<sizeof($categories);$cat++)
				{
					//get the product category id
					$category_name = trim($categories[$cat]);
					$pcat_sql = "SELECT cid FROM pi_catalog WHERE name = '$category_name'";
					$cid = getTransactionSingleValueSQL($pcart_dbc, $pcat_sql);
					if ($cid == false)
					{
						//the category does not exist
						INCLUDE(HEADER_FILE);
						echo 'Category ' . $category_name . ' Does Not exist on the web. Go to Pinnacle cart, add the category, then refresh this page';
						INCLUDE(FOOTER_FILE);
						exit();
					}
					
				}
				
				//existing products
				//first check if it is there is a matching id
				$product_id = $product_table_data_object['web_product_id'][$row];
				$check_sql = "SELECT pid FROM pi_products WHERE product_id ='$product_id'";
				$existing_product = getTransactionSingleValueSQL($pcart_dbc,$check_sql);
				if($existing_product)
				{
					$existing_ids[] = $product_id;
				}
				else
				{
				}
			}
			if(sizeof($existing_ids)>0)
			{
				
				$html = '';
				$html .= product_image_html($pos_product_image_id);

				$html .= '<h3>The following products were found on the web store, What would you like to do?</h3>';
				$form_id = "load_web_image";
				$form_action = 'load_web_image.fh.php';
				$html .=  '<form id = "' . $form_id . '" action="'.$form_action.'" method="post">';
				/*for($err=0;$err<sizeof($errors);$err++)
				{
					$html.= pprint($errors[$err], true);
				}*/
				$array_table_def= array(	
			

					array(
							'th' => 'Row',
							'db_field' => 'row_number2',
							'type' => 'row_number'),
					array(
							'th' => 'Web Product Id',
							'db_field' => 'existing_product_id',
							'type' => 'td_hidden_input'),
					array(
							'th' => 'Update <br>Product<br> Data',
							'db_field' => 'product_overwrite',
							'type' => 'checkbox',
							'value' => 1),
					array(
							'th' => 'Update<br> Image',
							'db_field' => 'image_overwrite',
							'type' => 'checkbox',
							'value' => 1),
					/*array(
							'th' => 'Leave Product Data<br>And Add Secondary Image',
							'db_field' => 'create_secondary',
							'type' => 'checkbox'),*/
					array(
							'th' => 'Create Product <br> Family Grouping',
							'db_field' => 'create_family',
							'type' => 'checkbox',
							'value' => 1),
					

					array(	'th' => 'Existing Product Link',
							'db_field' => 'web_link',
							'type' => 'html_link',
							'round' => 0,
							'tags' => ' style="background-color:yellow" ')
					);
				for($eid=0;$eid<sizeof($existing_ids);$eid++)
				{
					$data[$eid]['existing_product_id'] = $existing_ids[$eid];
					$product_id = $existing_ids[$eid];
					$url = 'http://embrasse-moi.com'; //UNSECURE_URL
					$data[$eid]['web_link'] =  $url  . '/' . getTransactionSingleValueSQL($pcart_dbc, "SELECT url_default FROM pi_products WHERE product_id ='$product_id'");
					//$data[$i]['row_number'] = $i+1;
				}
				$html .= createStaticArrayHTMLTablev2($array_table_def, $data);
				$html .= '<p><input class ="button" type="submit"  id = "submit" name="submit" value="Submit" onclick="needToConfirm=false;"/>' .newline();
				$html .= '<input class = "button" type="submit" name="cancel" value="Cancel"/>';
				$html .= createHiddenInput('type', $type);
				$html .= createHiddenInput('overwrite_check', 'overwrite_check');
				$html .= createHiddenSerializedInput('product_table_data_object', $product_table_data_object);
				$html .= createHiddenInput('pos_product_image_id', $pos_product_image_id);
				$html .= '</form>';
				
				/*//redisplay the original form
				$html .= '<h2>Here is the submitted data</h2>';
					$product_table_name = 'product_table';

				$products_table_def = createPinnacleCartCSVProductTableDef($pos_product_image_id, $product_table_name);
				
				$html .= createStaticArrayHTMLTable($products_table_def, $product_table_data_object);*/
				include (HEADER_FILE);
				echo $html;
				include (FOOTER_FILE);
				exit();

			}
			else
			{
				//everything is OK
			}
		}
		//now check which products we are 'overwriting
		$product_overwrite= array();
		//ok we came back with a possible array of matching id's
		//we need to know if which ID is associated with each existing id
		$counter = 0;
		for($row=0;$row<sizeof($product_table_data_object['row_number']);$row++)
		{
			if(isset($_POST['row_number2'][$counter]) && $product_table_data_object['web_product_id'][$row] == $_POST['existing_product_id'][$counter])
			{				
				if(isset($_POST['image_overwrite'.'_'.$counter]) )
				{
					$product_overwrite[$row]['image_overwrite'] = true;
				}
				else
				{
					$product_overwrite[$row]['image_overwrite'] = false;
				}
				if(isset($_POST['product_overwrite'.'_'.$counter]) )
				{
					$product_overwrite[$row]['product_overwrite'] = true;
				}
				else
				{
					$product_overwrite[$row]['product_overwrite'] = false;
				}
				if(isset($_POST['create_family'.'_'.$counter]) )
				{
					$product_overwrite[$row]['create_family'] = true;
				}
				else
				{
					$product_overwrite[$row]['create_family'] = false;
				}	
				$counter++;
				
			}
			else
			{
				$product_overwrite[$row]['image_overwrite'] = true;
				$product_overwrite[$row]['product_overwrite'] = true;
				$product_overwrite[$row]['create_family'] = true;
			}
		}

		//errors are dealt with... now lets create the products... first copy the images
					//copy the image, thumbs, preview to pinnacle cart
		for($row=0;$row<sizeof($product_table_data_object['row_number']);$row++)
		{	
			if($product_overwrite[$row]['image_overwrite'])
			{
				$product_id = $product_table_data_object['web_product_id'][$row];
				
			
			

				$product_image_file_name = $product_image_path . '/' . $product_id . '.jpg';
				$product_image_url = $web_url . '/images/products/' . $product_id . '.jpg';
				$product_preview_image_file_name = $preview_image_path . '/' . $product_id . '.jpg';
				$product_preview_image_url = $web_url . '/images/products/preview/' . $product_id . '.jpg';
				$thumb_image_file_name  = $thumbs_image_path . '/' . $product_id . '.jpg';
				$thumb_image_url = $web_url . '/images/products/thumbs/' . $product_id . '.jpg';
	
	
	
	
				//main image
				//copy_file($image_file_path, $product_image_file_name);
				make_thumbnail($image_file_path, $tmp_image, $main_width, 'width');
				scpFileToBluehost($tmp_image, $product_image_file_name);
				//preview
				make_thumbnail($image_file_path, $tmp_image, $preview_width, 'width');
				scpFileToBluehost($tmp_image, $product_preview_image_file_name);
				//thumbnail
				make_thumbnail($image_file_path, $tmp_image, $thumb_width, 'width');
				scpFileToBluehost($tmp_image, $thumb_image_file_name);
			
			
				/*$html .= '<P>Image has been placed here: ' . $product_image_file_name . '</p>';
				$html .= '<p><img src="' . $product_image_url . '" /></p>';
				$html .= '<P>Image has been placed here: ' . $product_preview_image_file_name . '</p>';
				$html .= '<p><img src="' . $product_preview_image_url . '" /></p>';
				$html .= '<P>Image has been placed here: ' . $thumb_image_file_name . '</p>';
				$html .= '<p><img src="' . $thumb_image_url . '" /></p>';*/
			}
		}

		//next the products
		for($row=0;$row<sizeof($product_table_data_object['row_number']);$row++)
		{
			if($product_overwrite[$row]['product_overwrite'])
			{
				$pos_product_sub_id = $product_table_data_object['pos_product_sub_id'][$row];
				$pos_product_id = $product_table_data_object['pos_product_id'][$row];
				$product_id = $product_table_data_object['web_product_id'][$row];
				//ok get all the 'crappy' default product stuff.....
				$product_insert_array = getPinnacleCartValues($pos_product_sub_id);
			
				//if the brand is not there add it
				$brand_name = $product_insert_array['Manufacturer name'];
				$mfg_sql = "SELECT manufacturer_id FROM pi_manufacturers WHERE manufacturer_name = '$brand_name'";
				$manufacturer_id = getTransactionSingleValueSQL($pcart_dbc, $mfg_sql);
				if ($manufacturer_id == false)
				{
					$mfg_insert_sql['is_visible']  = 1;
					$mfg_insert_sql['manufacturer_code']  = $brand_name;
					$mfg_insert_sql['manufacturer_name']  = $brand_name;
					$manufacturer_id = simpleTransactionInsertSQLReturnID($pcart_dbc,'pi_manufacturers', $mfg_insert_sql);
				}

				$categories = explode('||', $product_table_data_object['catagories'][$row]);
				$primary_category = $categories[0];
				$cid = getTransactionSingleValueSQL($pcart_dbc, "SELECT cid FROM pi_catalog WHERE name = '$primary_category'");
			
				//get the tax class id
				$tax_name = $product_insert_array['Tax class name'];
				$tax_class_id = getTransactionSingleValueSQL($pcart_dbc, "SELECT class_id FROM pi_tax_classes WHERE class_name = '$tax_name'");
			
		
		
		
			//	count the attributes
			$attibute_count = 0;
				if ($product_table_data_object['attribute1_name'][$row] != '')
				{
					$attribute_count = $attibute_count + 1;
				}
				if ($product_table_data_object['attribute2_name'][$row] != '')
				{
					$attribute_count = $attibute_count + 1;
				}

				//set up the product
				$product['cid'] = $cid;
				$product['manufacturer_id'] = $manufacturer_id;
				$product['tax_class_id'] = $tax_class_id;
				$product['weight'] = $product_table_data_object['weight'][$row];
				$product['price'] = $product_table_data_object['price'][$row];
				$product['product_id'] = $product_id;
				$product['priority'] = getProductPriority($pos_product_id);
				$product['added'] = $date_added;
			
				$product['attributes_count'] = $attribute_count;
			
				$ud = getTransactionSingleValueSQL($pcart_dbc,"SELECT url_default FROM pi_catalog WHERE cid = $cid");
				$title = scrubInput($product_table_data_object['name'][$row]);
				$url_default = $ud  . slug($title)  . '/';
				$product['url_hash'] = md5($url_default);
				$product['url_default'] = $url_default;
				$product['title'] = $title;
				$product['meta_keywords'] = '';
				$product['meta_title'] = '';
				$product['meta_description'] = '';
				$product['image_alt_text'] = $title;
				
				//$product['overview'] = scrubInput($product_table_data_object['overview'][$row]);
				//$product['description'] = scrubInput($product_table_data_object['description'][$row]);
				
				$product['overview'] = scrubInput(getProductOverview($pos_product_id));
				$product['description'] = scrubInput(getProductDescription($pos_product_id));
			
				//the bullshit
				$product['is_taxable'] = 'Yes';
				$product['zoom_option'] = 'imagelayover';//'magicthumb'; magic thumb is more visually consistent but seems to open another page
			
			
			
			
			
				//update or insert
		
				//first check if it is there is a matching id
				$product_id = $product_table_data_object['web_product_id'][$row];
				$check_sql = "SELECT pid FROM pi_products WHERE product_id ='$product_id'";
				$existing_product = getTransactionSQL($pcart_dbc,$check_sql);	
				if(sizeof($existing_product)>0)
				{
					//there can be an error caught here...
					$pid = $existing_product[0]['pid'];
					simpleUpdateTransactionSQL($pcart_dbc, 'pi_products', array('pid' => $pid), $product);
			
				}
				else
				{
					$pid = simpleTransactionInsertSQLReturnID($pcart_dbc,'pi_products', $product);
				}
		
			
		
				$pids[$row] = $pid;
		
				$delete_sql = "DELETE FROM pi_products_attributes WHERE pid = $pid";
				runTransactionSQL($pcart_dbc,$delete_sql);
		
				//insert the attributes: 1
				if ($product_table_data_object['attribute1_name'][$row] != '')
				{		
					$pi_product_attributes['attribute_type'] = 'select';
					$pi_product_attributes['pid'] = $pid;
					$pi_product_attributes['is_modifier'] = 'No';
					$pi_product_attributes['is_active'] = 'Yes';
					$pi_product_attributes['priority'] = '1';
					$pi_product_attributes['track_inventory'] = '1';
					$pi_product_attributes['name'] = scrubInput($product_table_data_object['attribute1_name'][$row]);
					$pi_product_attributes['caption'] = scrubInput($product_table_data_object['attribute1_name'][$row]);
					$pi_product_attributes['text_length'] = '0';
					$pi_product_attributes['options'] = str_replace('||', '\r\n', scrubInput($product_table_data_object['attribute1_list'][$row]));
					simpleTransactionInsertSQL($pcart_dbc,'pi_products_attributes', $pi_product_attributes);
					//preprint(simpleInsertSQLString('pi_products_attributes', $pi_product_attributes));
				}
				//insert the attributes: 2
				if ($product_table_data_object['attribute2_name'][$row] != '')
				{		
					$pi_product_attributes['attribute_type'] = 'select';
					$pi_product_attributes['pid'] = $pid;
					$pi_product_attributes['is_modifier'] = 'No';
					$pi_product_attributes['is_active'] = 'Yes';
					$pi_product_attributes['priority'] = '2';
					$pi_product_attributes['track_inventory'] = '1';
					$pi_product_attributes['name'] = scrubInput($product_table_data_object['attribute2_name'][$row]);
					$pi_product_attributes['caption'] = scrubInput($product_table_data_object['attribute2_name'][$row]);
					$pi_product_attributes['text_length'] = '0';
					$pi_product_attributes['options'] = str_replace('||', "\r\n", scrubInput($product_table_data_object['attribute2_list'][$row]));
					simpleTransactionInsertSQL($pcart_dbc,'pi_products_attributes', $pi_product_attributes);
					
				}
		
				$delete_sql = "DELETE FROM pi_products_categories WHERE pid = $pid";
				runTransactionSQL($pcart_dbc,$delete_sql);
			
				for($cat=0;$cat<sizeof($categories);$cat++)
				{
					//categories  the first one is primary
				
					//get the product category id
					$category_name = $categories[$cat];
					$pcat_sql = "SELECT cid FROM pi_catalog WHERE name = '$category_name'";
					$cid = getTransactionSingleValueSQL($pcart_dbc, $pcat_sql);
					$pi_product_categories['cid'] = $cid;
					$pi_product_categories['pid'] = $pid;
					$pi_product_categories['is_primary'] = ($cat == 0) ? 1 : 0;
					simpleTransactionInsertSQL($pcart_dbc,'pi_products_categories', $pi_product_categories);
				}
				$html .= '<P>Product Created: <a href="' . $web_url . '/' .$url_default .'" target="_blank">'.$product_id . '</a></p>';
			}
		}
	
	
		//Now that the products are created... create the family groupings.. only if we wanted to update the products...

			//WO18563 i created shizz for.... pid276 pfid 21
//might need to limit family creation if there is only one item...
for($row=0;$row<sizeof($product_table_data_object['row_number']);$row++)
		{
			//we may have skipped product generation
			if($product_overwrite[$row]['create_family'] && sizeof($product_overwrite)>1)
			{
				$product_id = $product_table_data_object['web_product_id'][$row];
				
				$pid = getTransactionSingleValueSQL($pcart_dbc,"SELECT pid FROM pi_products WHERE product_id ='$product_id'");
				$delete_sql = "DELETE FROM pi_products_families WHERE name = '$product_id'";
				runTransactionSQL($pcart_dbc,$delete_sql);
				$delete_sql = "DELETE FROM pi_products_families_content WHERE pid = $pid";
				runTransactionSQL($pcart_dbc,$delete_sql);
				$delete_sql = "DELETE FROM pi_products_families_dependencies WHERE pid = $pid";
				runTransactionSQL($pcart_dbc,$delete_sql);
			}
		}
		for($row=0;$row<sizeof($product_table_data_object['row_number']);$row++)
		{
			//we may have skipped product generation
			if($product_overwrite[$row]['create_family'] && sizeof($product_overwrite)>1)
			{
				
				//create a famil named the product id
				//get rid of any other families with that name
				//get rid of any contents with the pid linked to the name
				//get rid of any links between the pid and the name
				//for each product in the list, link the product
				$product_id = $product_table_data_object['web_product_id'][$row];
				
				$pid = getTransactionSingleValueSQL($pcart_dbc,"SELECT pid FROM pi_products WHERE product_id ='$product_id'");
				//preprint( 'Creating family for ' . $product_id . 'pid ' . $pid);
				
		
				$pi_products_families['name'] = $product_id;
				$pf_id = simpleTransactionInsertSQLReturnID($pcart_dbc,'pi_products_families', $pi_products_families);
				//link to the 
				$pi_products_families_dependencies['pid'] = $pid;
				$pi_products_families_dependencies['pf_id'] =$pf_id;
				simpleTransactionInsertSQL($pcart_dbc,'pi_products_families_dependencies', $pi_products_families_dependencies);
				
				$pi_products_families_contents['pf_id'] = $pf_id;
				for($row2=0;$row2<sizeof($product_table_data_object['row_number']);$row2++)
				{
					if($row2!=$row)
					{
						$family_product_id = $product_table_data_object['web_product_id'][$row2];
						
						$family_pid = getTransactionSingleValueSQL($pcart_dbc,"SELECT pid FROM pi_products WHERE product_id ='$family_product_id'");
						//preprint('adding ' . $family_product_id .' to ' . $product_id . 'pid ' . $family_pid);
						$pi_products_families_contents['pid'] = $family_pid;
						simpleTransactionInsertSQL($pcart_dbc,'pi_products_families_content', $pi_products_families_contents);
						//preprint(simpleInsertSQLString('pi_products_families_content', $pi_products_families_contents));
					}
				}
				
		
		
				
			}
		}
	

		mysqli_close($pcart_dbc);
		
		//where to go to?
		//$message = 'message=' . urlencode('OK NOW LETS UPLOAD - FTP THE IMAGES TO '. POS_URL. '/DataFiles/image_upload');
		//header('Location: list_product_images.php?'. $message);
		include(HEADER_FILE);
		echo '<h3> Temp page - do not reload or refresh!!</h3>';
		echo $html;
		include(FOOTER_FILE);
		exit();
	}
	else if (strtoupper($type) == 'SECONDARY')
	{
		$pcart_dbc = openWebStoreDatabase();
		$product_table_data_object = json_decode(stripslashes($_POST['product_table_data_object']) , true);		
		//here all we need to do is put images in the correct locations.......
		for($row=0;$row<sizeof($product_table_data_object['row_number']);$row++)
		{	
			$product_id = $product_table_data_object['web_product_id'][$row];
			//get the pid....for feedback
			$pid = getTransactionSingleValueSQL($pcart_dbc,"SELECT pid FROM pi_products WHERE product_id ='$product_id'");	
			if($pid != false)
			{
				//how many secondary images are there?
				$secondary_images_count = getTransactionSingleValueSQL($pcart_dbc, "SELECT COUNT(iid) FROM pi_products_images WHERE pid= $pid");
				
				$next_image = $secondary_images_count + 1;
				$file_name =  $product_id . '-' .$next_image. '.jpg';
				$secondary_image_file_name = $secondary_image_path . '/' .$file_name;
				$secondary_thumb_image_file_name  = $secondary_thumbs_image_path . '/' .$file_name;
			

				
				
								//main image too big
				//copy_file($image_file_path, $secondary_image_file_name);
				//preview => not sure if this needs to be resized?
				$return_image_data = make_thumbnail($image_file_path, $tmp_image , 1024, 'width');
				scpFileToBluehost($tmp_image, $secondary_image_file_name);

				//thumbnail
				make_thumbnail($image_file_path, $secondary_thumb_image_file_name, 210, 'width');
				scpFileToBluehost($tmp_image, $secondary_image_file_name);

			
				//we need to insert this:
				$pi_products_images['pid'] = $pid;
				$pi_products_images['is_visible'] = 'Yes';
				$pi_products_images['image_priority'] = 1;
				$pi_products_images['width'] = $return_image_data['width'];
				$pi_products_images['height'] = $return_image_data['height'];
				$pi_products_images['filename'] = $product_id . '-' .$next_image;
				$pi_products_images['type'] = 'jpg';
				$pi_products_images['caption'] = '';
				$pi_products_images['alt_text'] = '';
				
				
				simpleTransactionInsertSQL($pcart_dbc,'pi_products_images', $pi_products_images);


				$url_default = getTransactionSingleValueSQL($pcart_dbc,"SELECT url_default FROM pi_products WHERE product_id ='$product_id'");	
				$html .= '<P>Product updated With Secondary Image: <a href="' . $web_url . '/' .$url_default .'" target="_blank">'.$product_id . '</a></p>';
			}
			else
			{
				$html .= '<P>Error: Web Product Does Not Exist: '.$product_id . '</p>';
			}
				
		}
		mysqli_close($pcart_dbc);
		include(HEADER_FILE);
		echo '<h3> Temp page - do not reload or refresh!!</h3>';
		echo $html;
		include(FOOTER_FILE);
		exit();
	
	}
	else if (strtoupper($type) == 'REPLACE')
	{
		$pcart_dbc = openWebStoreDatabase();
		$product_table_data_object = json_decode(stripslashes($_POST['product_table_data_object']) , true);		
		//here all we need to do is put images in the correct locations.......
		for($row=0;$row<sizeof($product_table_data_object['row_number']);$row++)
		{	
			$product_id = $product_table_data_object['web_product_id'][$row];
			//get the pid....for feedback
			$pid = getTransactionSingleValueSQL($pcart_dbc,"SELECT pid FROM pi_products WHERE product_id ='$product_id'");	
			if($pid != false)
			{
	
				$product_image_file_name = $product_image_path . '/' . $product_id . '.jpg';
				$product_preview_image_file_name = $preview_image_path . '/' . $product_id . '.jpg';
				$thumb_image_file_name  = $thumbs_image_path . '/' . $product_id . '.jpg';
				
				//copy_file($image_file_path, $product_image_file_name);
				make_thumbnail($image_file_path, $tmp_image, $main_width, 'width');
				scpFileToBluehost($tmp_image, $product_image_file_name);

				//preview
				make_thumbnail($image_file_path, $tmp_image, $preview_width, 'width');
				scpFileToBluehost($tmp_image, $product_preview_image_file_name);

				//thumbnail
				make_thumbnail($image_file_path, $tmp_image, $thumb_width, 'width');
				scpFileToBluehost($tmp_image, $thumb_image_file_name);

				$url_default = getTransactionSingleValueSQL($pcart_dbc,"SELECT url_default FROM pi_products WHERE product_id ='$product_id'");
				$html .= '<P>Product Image Updated: <a href="' . $web_url . '/' .$url_default .'" target="_blank">'.$product_id . '</a></p>';
			}
			else
			{
				$html .= '<P>Error: Web Product Does Not Exist: '.$product_id . '</p>';
			}
				
		}
		mysqli_close($pcart_dbc);
		include(HEADER_FILE);
		echo '<h3> Temp page - do not reload or refresh!!</h3>';
		echo $html;
		include(FOOTER_FILE);
		exit();
	}
	else
	{
	}
					
}							
else
{
		$message = 'message=CANCELED' ;
		header('Location: list_product_images.php?'. $message);
		exit();
}	

?>