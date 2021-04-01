<?php
/*
This is a list of our images
*/
$binder_name = 'Images';
$access_type = 'READ';
$page_title = 'Images';
require_once ('../product_functions.php');

$html ='';



$search_fields = array(		array(	'db_field' => 'pos_product_image_id',
											'mysql_search_result' => 'pos_product_image_id',
											'caption' => 'System Id ',	
											'type' => 'input',
											'html' => createSearchInput('pos_product_image_id')),
							array(	'db_field' => 'original_image_name',
											'mysql_search_result' => 'original_image_name',
											'caption' => 'Image Name',	
											'type' => 'input',
											'html' => createSearchInput('original_image_name')),
							array(	'db_field' => 'style_numbers',
											'mysql_search_result' => 'style_numbers',
											'caption' => 'Style Number',	
											'type' => 'input',
											'html' => createSearchInput('style_numbers')),
							array(	'db_field' => 'titles',
											'mysql_search_result' => 'titles',
											'caption' => 'Title',	
											'type' => 'input',
											'html' => createSearchInput('titles')),
							array(	'db_field' => 'brands',
											'mysql_search_result' => 'brands',
											'caption' => 'Brand',	
											'type' => 'input',
											'html' => createSearchInput('brands')),
							array(	'db_field' => 'comments',
											'mysql_search_result' => 'comments',
											'caption' => 'Comments',	
											'type' => 'input',
											'html' => createSearchInput('comments'))
								
											
						);								
$list_table_columns = array(
		 array(
			'th' => 'View',
			'mysql_field' => 'pos_product_image_id',
			'get_url_link' => "product_image.php?type=View",
			'url_caption' => 'View',
			'get_id_link' => 'pos_product_image_id'),

		array(
			'th' => 'System<br>ID',
			'mysql_field' => 'pos_product_image_id',
			'mysql_search_result' => 'pos_product_image_id',
			'sort' => 'pos_product_image_id'),
		array(
			'th' => 'Thumbnail',
			'thumbnail_path' =>  PRODUCT_IMAGE_THUMBNAIL_FOLDER,
			'thumbnail_prefix' => '.jpg',
			'thumbnail_link' => PRODUCT_IMAGE_FOLDER,
			'thumbnail_name' => 'pos_product_image_id'
			),
		array(
			'th' => 'Original <BR> Image Name',
			'mysql_field' => 'original_image_name',
			'mysql_search_result' => 'original_image_name',
			'sort' => 'image_name'),
		array(
			'th' => 'Style Numbers',
			'mysql_field' => 'style_numbers',
			'mysql_search_result' => 'style_numbers',
			'sort' => 'style_numbers'),
		array(
			'th' => 'Titles',
			'mysql_field' => 'titles',
			'mysql_search_result' => 'titles',
			'sort' => 'titles'),
		array(
			'th' => 'Brands',
			'mysql_field' => 'brands',
			'mysql_search_result' => 'brands',
			'sort' => 'brands'),
		array(
			'th' => 'Date Added',
			'mysql_field' => 'date_added',
			'mysql_search_result' => 'date_added',
			'sort' => 'date_added'),
		array(
			'th' => 'Comments',
			'mysql_field' => 'comments',
			'mysql_search_result' => 'comments',
			'sort' => 'comments')
		);

$search_set = saveAndRedirectSearchFormUrl($search_fields, 'saved_images_url');
$tmp_sql = "
CREATE TEMPORARY TABLE image

SELECT 	pos_product_images.pos_product_image_id,  
		pos_product_images.active,		pos_product_images.comments,

		pos_product_images.original_image_name,
		pos_product_images.date_added,
		(SELECT GROUP_CONCAT(pos_products.style_number) 
			FROM pos_products
			LEFT JOIN pos_product_image_lookup
			ON pos_products.pos_product_id = pos_product_image_lookup.pos_product_id
			WHERE pos_product_image_lookup.pos_product_image_id = pos_product_images.pos_product_image_id) as style_numbers,
		(SELECT GROUP_CONCAT(pos_products.title) 
			FROM pos_products
			LEFT JOIN pos_product_image_lookup
			ON pos_products.pos_product_id = pos_product_image_lookup.pos_product_id
			WHERE pos_product_image_lookup.pos_product_image_id = pos_product_images.pos_product_image_id) as titles,
		(SELECT  GROUP_CONCAT(DISTINCT pos_manufacturer_brands.brand_name) 
			FROM pos_products
			LEFT JOIN pos_product_image_lookup
			ON pos_products.pos_product_id = pos_product_image_lookup.pos_product_id
			LEFT JOIN pos_manufacturer_brands
			ON pos_products.pos_manufacturer_brand_id = pos_manufacturer_brands.pos_manufacturer_brand_id
			WHERE pos_product_image_lookup.pos_product_image_id = pos_product_images.pos_product_image_id) as brands

FROM pos_product_images



;

";
$tmp_select_sql = "SELECT * FROM image WHERE 1";

//Create Search String (AND's after MYSQL WHERE from $_GET data)
$search_sql = createSearchSQLStringMultipleDates($search_fields);
//Create the order sting to append to the sql statement
$order_by = createSortSQLString($list_table_columns, $list_table_columns[0]['mysql_field']);
$order_sql  =  " ORDER BY $order_by";



//if there is a message print it
$html .= printGetMessage();
$html .= '<p>';


if(checkWriteAccess($binder_name))
{
	$html .= '<p>';
	//$html .= '<input class = "button" type="button" style="width:180px;" name="add_product" value="Add Single Image" onclick="open_win(\'product_image.php?type=add\')"/>';
 $html .= '<input class = "button" type="button" style="width:180px;" name="image_coord" value="Take Photos" onclick="open_win(\'../ProductImages/product_image_coordinator.php\')"/>';
 //$html .= '<input class = "button" type="button" style="width:180px;" name="crop_image" value="Image Cropper Tool" onclick="open_win(\'../ProductImages/upload_image.php\')"/>';
  $html .= '<input class = "button" type="button" style="width:180px;" name="crop_image" value="Bulk Image Uploader" onclick="open_win(\'../ProductImages/bulk_image_uploader.php\')"/>';
 
 $html .= '</p>';
}
//$html .= createUserButton('Products');
$html .= '</p>';
$action = 'list_product_images.php';
$html .= createSearchForm($search_fields,$action);
//now make the table
if (isset($_GET['search']))
{
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,$tmp_sql);
	$data = getTransactionSQL($dbc,$tmp_select_sql.$search_sql.$order_sql);
	closeDB($dbc);
	$html .= createRecordsTable($data, $list_table_columns);
}
$html .= '<script>document.getElementsByName("original_image_name")[0].focus();</script>';
$html .= '<p>';


include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);
?>
