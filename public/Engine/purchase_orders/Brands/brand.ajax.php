<?
$access_type = 'WRITE';
require_once('../po_functions.php');
require_once('brand.inc.php');

$ajax_request = (ISSET($_GET['ajax_request'])) ? $_GET['ajax_request'] : $_POST['ajax_request'];

if($ajax_request == 'submit_mfg_add_edt')
{
	//form processor for the javascript table.....
	
	//the name of the elements is the table_name_db_field
	//this keeps javascript collecting and operating on the correct table...
	
	// I could post the table def but why.....it is right here....
	$table_def = createManufacturerTableDef();
	$table_name = $_POST['table_name'];
	$post_data = SCRUBmySQLTablePOST($table_def,$table_name);	
	unset($post_data['pos_manufacturer_id']);
	$pos_manufacturer_id = scrubInput($_POST[$table_name  .'_' . 'pos_manufacturer_id'] );
	//echo json_encode($_POST);
	
	
	//we need to check for errors here.. the database will fail on matching compnay name... js should catch this first... second php, third the database.... 
	$company = $post_data['company'];
	$existing_company = getSQL("SELECT pos_manufacturer_id FROM pos_manufacturers WHERE company = '$company'");
	if($pos_manufacturer_id == 'TBD')
	{
		if(sizeof($existing_company) >0)
		{
			//problem, existing company
		}
	}
	else
	{
		if(sizeof($existing_company) == 1)
		{
			if($existing_company[0]['pos_manufacturer_id'] != $pos_manufacturer_id)
			{
				//problem - the company name changed to an existing company...
			}
		}
	}
	
	$dbc = startTransaction();
	if($pos_manufacturer_id == 'TBD')
	{
		//this is an insert
		$pos_manufacturer_id = simpleTransactionInsertSQLReturnID($dbc,'pos_manufacturers', $post_data);
		
	}
	else
	{
		
		$key_val_id['pos_manufacturer_id'] = $pos_manufacturer_id;
		$results[] = simpleTransactionUpdateSQL($dbc,'pos_manufacturers', $key_val_id, $post_data);
	}
	
	simpleCommitTransaction($dbc);
	
	//finally re-get the data and post it...
	//return the array
	
	$mfg_data = getmySQLTableData('pos_manufacturers', array('pos_manufacturer_id' => $pos_manufacturer_id), $table_def);
	$return['mysql_return_data'] = $mfg_data;
	$return['success'] = true;
	echo json_encode($return);
}
elseif($ajax_request == 'auto_complete_company')
{
	$search = scrubInput($_GET['search_terms']);
	$limit = scrubInput($_GET['maxRows']);
	$return_data = getFieldRowSQL("SELECT company FROM pos_manufacturers WHERE company LIKE '%$search%' LIMIT $limit");
	if(isset($return_data['company']))
	{
		echo json_encode($return_data['company']);
	}
	else
	{
		echo '';
	}
	

}

elseif($ajax_request == 'auto_complete_brand')
{
	$search = scrubInput($_GET['search_terms']);
	$limit = scrubInput($_GET['maxRows']);
	$return_data = getFieldRowSQL("SELECT brand_name FROM pos_manufacturer_brands WHERE brand_name LIKE '%$search%' LIMIT $limit");
	if(isset($return_data['company']))
	{
		echo json_encode($return_data['brand_name']);
	}
	else
	{
		echo '';
	}
	

}
elseif($ajax_request == 'select_company')
{
	$company = scrubInput($_POST['company']);
	$table_def = createManufacturerTableDef();
	$pos_manufacturer_id = getSingleValueSQL("SELECT pos_manufacturer_id FROM pos_manufacturers WHERE company = '$company'");
	if($pos_manufacturer_id)
	{
		$mfg_data = getmySQLTableData('pos_manufacturers', array('pos_manufacturer_id' => $pos_manufacturer_id), $table_def);
		$return['mysql_return_data'] = $mfg_data;
		$return['success'] = true;
		//also need the account link and brand data...
		$account_link = getMfgAccountLink($pos_manufacturer_id);
		$brand_list_data = getBrandList($pos_manufacturer_id);
		$return['account_link'] = $account_link;
		$return['brand_list_data'] = $brand_list_data;
		
	}
	else
	{	
		 $return['success'] = false;
	}
	echo json_encode($return);
}

?>