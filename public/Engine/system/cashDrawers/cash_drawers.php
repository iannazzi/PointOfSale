<?php
/*
	Ahh the terminal... shove a cookie down it to get it an id......
*/
$type = $_GET['type'];
$page_title = 'Cash Drawers';
$binder_name = 'Cash Drawers';
$access_type = (strtoupper($type)=='VIEW') ? 'READ' : 'WRITE';
require_once ('../system_functions.php');

$complete_location = 'list_cash_drawers.php';
$cancel_location = 'list_cash_drawers.php?message=Canceled';



if(strtoupper($type) == 'ADD')
{
	$pos_cash_drawer_id = 'TBD';
	$header = '<p>Add Cash Drawer</p>';
	$page_title = 'Add Cash Drawer';
	$data_table_def = createCashDrawerTableDef($type, $pos_cash_drawer_id);
}
elseif (strtoupper($type) == 'EDIT')
{
	$pos_cash_drawer_id = getPostOrGetID('pos_cash_drawer_id');
	$header = '<p>EDIT Cash Drawer</p>';
	$page_title = 'Edit Cash Drawer';
	$data_table_def_no_data = createCashDrawerTableDef($type, $pos_cash_drawer_id);	
	$db_table = 'pos_cash_drawers';
	$key_val_id['pos_cash_drawer_id'] = $pos_cash_drawer_id;
	$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def_no_data);
}
elseif (strtoupper($type) == 'VIEW')
{
	$pos_cash_drawer_id = getPostOrGetID('pos_cash_drawer_id');
	$edit_location = 'cash_drawers.php?pos_cash_drawer_id='.$pos_cash_drawer_id.'&type=edit';
	//$delete_location = 'delete_discount.form.handler.php?pos_discount_id='.$pos_discount_id;
	$db_table = 'pos_cash_drawers';
	$key_val_id['pos_cash_drawer_id']  = $pos_cash_drawer_id;
	$data_table_def = createCashDrawerTableDef($type, $pos_cash_drawer_id);
	$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def);
}
else
{
}

//build the html page
if (strtoupper($type) == 'VIEW')
{
	$html = printGetMessage('message');
	$html .= '<p>View Cash Drawer</p>';
	//$html .= confirmDelete($delete_location);
	$html .= createHTMLTableForMYSQLData($data_table_def);
	$html .= '<p><input class = "button"  type="button" name="edit"  value="Edit" onclick="open_win(\''.$edit_location.'\')"/>';
// $html .= '<input class = "button" type="button" name="delete" value="Delete Discount" onclick="confirmDelete();"/>';
	
	$html .= '<p>';
	$html .= '<INPUT class = "button" type="button" style ="width:200px" value="Back To Cash Drawers" onclick="window.location = \''.$complete_location.'\'" />';
	$html .= '</p>';
}
else
{
	$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);
	$big_html_table .= createHiddenInput('type', $type);
	
	$html = $header;
	$form_handler = 'cash_drawers.fh.php';
	$html .= createFormForMYSQLInsert($data_table_def, $big_html_table, $form_handler, $complete_location, $cancel_location);
	$html .= '<script>document.getElementsByName("printer_name")[0].focus();</script>';
}


include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);

function createCashDrawerTableDef($type, $pos_cash_drawer_id)
{
	if ($pos_cash_drawer_id =='TBD')
	{
		$unique_validate = array('unique_group' => array('pos_store_id', 'cash_drawer_name'), 'min_length' => 1);
	}
	else
	{
		$key_val_id['pos_cash_drawer_id'] = $pos_cash_drawer_id;
		$unique_validate = array('unique_group' => array('pos_store_id', 'cash_drawer_name'), 'min_length' => 1, 'id' => $key_val_id);
	}
	
	return array( 
						array( 'db_field' => 'pos_cash_drawer_id',
								'type' => 'input',
								'tags' => ' readonly = "readonly" ',
								'caption' => 'Cash Drawer ID',
								'value' => $pos_cash_drawer_id,
								'validate' => 'none'
								),
						array('db_field' =>  'cash_drawer_name',
								'type' => 'input',
								'caption' => 'Drawer Name',
								'db_table' => 'pos_cash_drawers',
								'validate' => $unique_validate,
								),	
						array('db_field' => 'pos_store_id',
								'caption' => 'Store',
								'type' => 'select',
								'html' => createStoreSelect('pos_store_id', $_SESSION['store_id'],  'off'),
								'value' => $_SESSION['store_id'],
								'validate' => 'false'),
						array('db_field' =>  'location',
								'type' => 'input',
								'caption' => 'location'),
					array('db_field' =>  'cash_drawer_description',
								'type' => 'input',
								'caption' => 'Cash Drawer Description'),
						array('db_field' =>  'active',
								'type' => 'checkbox',
								'caption' => 'Active',
								'value' => '1')
						);	

}
?>