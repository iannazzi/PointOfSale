<?php
/*
	Ahh the terminal... shove a cookie down it to get it anexecutable*/
$type = $_GET['type'];
$page_title = 'Printers';
$binder_name = 'Printers';
$access_type = (strtoupper($type)=='VIEW') ? 'READ' : 'WRITE';
require_once ('../system_functions.php');

$complete_location = 'list_printers.php';
$cancel_location = 'list_printers.php?message=Canceled';



if(strtoupper($type) == 'ADD')
{
	$pos_printer_id = 'TBD';
	$header = '<p>Add Printer</p>';
	$page_title = 'Add Printer';
	$data_table_def = createPrinterTableDef($type, $pos_printer_id);
}
elseif (strtoupper($type) == 'EDIT')
{
	$pos_printer_id = getPostOrGetID('pos_printer_id');
	$header = '<p>EDIT Printer</p>';
	$page_title = 'Edit Printer';
	$data_table_def_no_data = createPrinterTableDef($type, $pos_printer_id);	
	$db_table = 'pos_printers';
	$key_val_id['pos_printer_id'] = $pos_printer_id;
	$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def_no_data);
}
elseif (strtoupper($type) == 'VIEW')
{
	$pos_printer_id = getPostOrGetID('pos_printer_id');
	$edit_location = 'printers.php?pos_printer_id='.$pos_printer_id.'&type=edit';
	//$delete_location = 'delete_discount.form.handler.php?pos_discount_id='.$pos_discount_id;
	$db_table = 'pos_printers';
	$key_val_id['pos_printer_id']  = $pos_printer_id;
	$data_table_def = createPrinterTableDef($type, $pos_printer_id);
	$data_table_def = selectSingleTableDataFromID($db_table, $key_val_id,  $data_table_def);
}
elseif (strtoupper($type) == 'MAKE_SHELL_SCRIPT')
{
	//https://www.embrasse-moi.com/POS_TEST/Engine/system/printers/printers.php?type=MAKE_SHELL_SCRIPT&print_queue=cefr&ACCOUNT_KEY=1234
	
	$pos_printer_id = getPostOrGetID('pos_printer_id');
	$print_queue = getPrinterName($pos_printer_id);
	$account_key = '1234'; // this is a unique id for each system account $_GET['ACCOUNT_KEY'];
	$root_path = 'script';
	$shell_script_name = $root_path ."/pos_printer.command";
	$automator_app = $root_path."/run_craigs_shell_script.app";
	$output_file_name =  "pos_printer.command";

	//$shell_script = file_get_contents($shell_script_name);
	$shell_script = '';
	$shell_script_file = fopen($shell_script_name, "r") or exit("Unable to open file!");
	while(!feof($shell_script_file))
	{
   		 $line = fgets($shell_script_file);
   		$line = str_replace("\r", '', $line);
   		$shell_script .= $line;
   		//$line = str_replace("\n", '', $line);
   		 //$shell_script .= $line  .newline();// . PHP_EOL;//"\n";
   		//$shell_script .= $line . PHP_EOL;//"\n";
   		
	}
	fclose($shell_script_file);
	//LIST_URL='embrasse-moi.com/POS_TEST/Engine/system/printers/printer_queue.php?type=LIST&print_queue='$PRINTER_QUEUE'&ACCOUNT_KEY='$ACCOUNT_KEY
	$list_url = POS_ENGINE_URL . '/system/printers/printer_queue.php?type=LIST&print_queue='.$print_queue.'&ACCOUNT_KEY='.$account_key;
//DOWNLOAD_URL='http://www.embrasse-moi.com/POS_TEST/PrintQueue/invoices/'$PRINTER_QUEUE
	//need to modify with ACCOUNT_KEY
	$download_url = POS_URL . '/PrintQueue/invoices/' . $print_queue;
//DELETE_URL='embrasse-moi.com/POS_TEST/Engine/system/printers/printer_queue.php?type=DELETE&print_queue='$PRINTER_QUEUE'&ACCOUNT_KEY='$ACCOUNT_KEY

	$delete_url = POS_ENGINE_URL . '/system/printers/printer_queue.php?type=DELETE&print_queue='.$print_queue.'&ACCOUNT_KEY='.$account_key;
	
	$delimeter = "\n";
	$output =  "#!/bin/bash" . $delimeter;
	$output .= "LOCAL_PRINTER_NAME='".$print_queue ."'" .$delimeter;
	$output .= "ACCOUNT_KEY='".$account_key ."'" .$delimeter;
	$output .= "PRINTER_QUEUE='".$print_queue ."'" .$delimeter;
	$output .= "LIST_URL='".$list_url ."'" .$delimeter;
	$output .= "DOWNLOAD_URL='".$download_url ."'" .$delimeter;
	$output .= "DELETE_URL='".$delete_url ."'" .$delimeter;
	$output .= $shell_script;

header("Content-Disposition: attachment; filename=\"" . $output_file_name . "\"");
//header("Content-Type: application/force-download");
header('Content-type: text/plain');
header("Content-Length: " . strlen($output));
echo $output;

exit();

//zipping would not work for the automator app. So that needs to be different?
	if (!file_exists($account_key))
	{
		mkdir($account_key);
	}
	if (!file_exists($account_key.'/'.$print_queue))
	{
		mkdir($account_key.'/'.$print_queue);
	}
	
	
	file_put_contents($account_key.'/'.$print_queue.'/'.$output_file_name, $output);

	// Create recursive directory iterator
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root_path),
    RecursiveIteratorIterator::LEAVES_ONLY
);


	
	$zipname = $account_key.'/'.$print_queue.'/craigs_print_script.zip';
	$zip = new ZipArchive;
	$zip->open($zipname, ZipArchive::CREATE);
	foreach ($files as $name => $file) 
	{
		// Get real path for current file
		$filePath = $file->getRealPath();
		echo $filePath .newline();
		// Add current file to archive
		$zip->addFile($filePath);
	}
	$zip->addFile($account_key.'/'.$print_queue.'/'.$shell_script_name);
	$zip->close();
	
	header('Content-Type: application/zip');
	header('Content-disposition: attachment; filename='.$zipname);
	header('Content-Length: ' . filesize($zipname));
	readfile($zipname);


	//unlink($zipname);
	//unlink($output_file_name);
	//rmdir($account_key);
	
	
	
	

	//fwrite($open, $text); 
	//fclose($open);


}
else
{
}

//build the html page
if (strtoupper($type) == 'VIEW')
{
	$html = printGetMessage('message');
	$html .= '<p>View Printer</p>';
	//$html .= confirmDelete($delete_location);
	$html .= createHTMLTableForMYSQLData($data_table_def);
	$html .= '<p><input class = "button"  type="button" name="edit"  value="Edit" onclick="open_win(\''.$edit_location.'\')"/>';
// $html .= '<input class = "button" type="button" name="delete" value="Delete Discount" onclick="confirmDelete();"/>';
	

	
	$html.= '<p>Below is a shell script unique to printer ' . getPrinterName($pos_printer_id). '. It will download the printer files off the server and to a local computer. It will then send the file to the printer named ' . getPrinterName($pos_printer_id) .'. To use it on a mac, 
	
	<li>Download the shell script to a folder of your choice. ';
	$html.= '<a href = "'.$_SERVER['PHP_SELF'] .'?pos_printer_id='.$pos_printer_id.'&type=MAKE_SHELL_SCRIPT">Download Shell Script</a>';
	$html.= '</li>';
	$html.= '<li>right click on the script and select edit in text edit or similar. </li>';
	$html.= '<li>Change LOCAL_PRINTER_NAME to match a printer name in your local system printers </li>';
	$html.= '<li>on the mac open terminal (command+space bar then type terminal)</li>';
	$html.= '<li>cd to the directory where the script is located (cd /)</li>';
	$html.= '<li>change permissions to executable: (chmod +x pos_printer.command)</li>';
	$html.= '<li>Double click to execute - you will probably get an error opening the script</li>';
	$html.= '<li>Go to system preferences Security and Privacy -> General and click opoen anyway</li>';
	$html.= '<li>Bonus: add the command file to user login items</li>';

	$html .= '<li> Delete any printer with the name of ' .  getPrinterName($pos_printer_id) . '.</li>';
	$html .= '<li> Set up a new printer with the name of ' .  getPrinterName($pos_printer_id) . '.</li>';

	$html .= '<p>';
	$html .= '<INPUT class = "button" type="button" style ="width:200px" value="Back To Printers" onclick="window.location = \''.$complete_location.'\'" />';
	$html .= '</p>';
}
else
{
	$big_html_table = createHTMLTableForMYSQLInsert($data_table_def);
	$big_html_table .= createHiddenInput('type', $type);
	
	$html = $header;
	$form_handler = 'printers.fh.php';
	$html .= createFormForMYSQLInsert($data_table_def, $big_html_table, $form_handler, $complete_location, $cancel_location);
	$html .= '<script>document.getElementsByName("printer_name")[0].focus();</script>';
}


include (HEADER_FILE);
echo $html;
include (FOOTER_FILE);

function createPrinterTableDef($type, $pos_printer_id)
{
	if ($pos_printer_id =='TBD')
	{
		$unique_validate = array('unique_group' => array('pos_store_id', 'printer_name'), 'min_length' => 1);
	}
	else
	{
		$key_val_id['pos_printer_id'] = $pos_printer_id;
		$unique_validate = array('unique_group' => array('pos_store_id', 'printer_name'), 'min_length' => 1, 'id' => $key_val_id);
	}
	
	return array( 
						array( 'db_field' => 'pos_printer_id',
								'type' => 'input',
								'tags' => ' readonly = "readonly" ',
								'caption' => 'Printer ID',
								'value' => $pos_printer_id,
								'validate' => 'none'
								),
						array('db_field' =>  'media',
								'type' => 'select',
								'caption' => 'Media Type',
								'html' => createEnumSelect('media','pos_printers', 'media', 'false',  'off')),
						array('db_field' => 'pos_account_id',
								'type' => 'select',
								'caption' => 'Checking Account (Used For Check Printer Only)',
								'html' => createCheckingAccountSelect('pos_account_id', 'false'),
								'validate' => 'none'),
						array('db_field' =>  'printer_name',
								'type' => 'input',
								'tags' => ' readonly = "readonly" ',
								'caption' => 'Printer Name',
								'db_table' => 'pos_printers',
								'value' => 'AUTOMATICALLY NAMED',
								'validate' => 'none',
								),	
						array('db_field' => 'pos_store_id',
								'caption' => 'Store',
								'type' => 'select',
								'html' => createStoreSelect('pos_store_id', $_SESSION['store_id'],  'off'),
								'value' => $_SESSION['store_id'],
								'validate' => 'false'),
						array('db_field' =>  'printer_description',
								'type' => 'input',
								'caption' => 'Printer Description'),
						array('db_field' =>  'location',
								'type' => 'input',
								'caption' => 'location'),
				
						array('db_field' =>  'active',
								'type' => 'checkbox',
								'caption' => 'Active',
								'value' => '1')
						);	

}
?>