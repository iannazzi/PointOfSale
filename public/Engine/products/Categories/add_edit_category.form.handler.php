<?php
/*
Craig Iannazzi 4-23-11
*/
$binder_name = 'Product Categories';
$access_type = 'WRITE';
require_once ('../product_functions.php');
require_once(PHP_LIBRARY);

if (isset($_POST['submit'])) 
{
	$insert = postedTableDefArraytoMysqlInsertArray($_POST['table_def']);
	unset($insert['pos_category_id']);
	if($_POST['parent'] =='false')
	{
		$_POST['parent'] == 0;
	}
	$insert['name'] = strtoupper($insert['name']);
	$other_info = array(
						'is_visible' => 'Yes',
						'list_subcats' => 'No',
						'url_default' => generateCategoryURL($insert['name'], $insert['parent']),
						'key_name' => strtoupper($insert['name']),
						'category_path' => strtoupper(generateCategoryURL($insert['name'], $insert['parent'])),
						'level' => generateCategoryLevel($insert['parent']),
						'url_hash' => md5(generateCategoryURL($insert['name'], $insert['parent'])),
						'url_custom' => '',
						'category_header' => '',
					);
	$insert = array_merge($insert, $other_info);
	if($_POST['add_or_edit'] =='New')
	{
		$pos_category_id['pos_category_id'] = simpleInsertSQLReturnID('pos_categories', $insert);
	}
	elseif ($_POST['add_or_edit'] =='Edit')
	{
		$pos_category_id['pos_category_id'] = getPostOrGetID('pos_category_id');	
		$result = simpleUpdateSQL('pos_categories', $pos_category_id, $insert);
	}
	else
	{
	}
	if (WEB_STORE_ACTIVE)
	{
		//add the category to the web store
	}
	$message = urlencode($insert['name'] . " has been added");
	header('Location: '.$_POST['complete_location'] .'?message=' . $message);	
	
	
}



?>
