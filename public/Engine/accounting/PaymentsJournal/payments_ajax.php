<?php

$binder_name = 'Payments Journal';
$access_type = 'READ';

require_once ('../accounting_functions.php');

$ajax_request = (ISSET($_GET['ajax_request'])) ? $_GET['ajax_request'] : $_POST['ajax_request'];
if(strtoupper($ajax_request) == 'PRINT_CHECK')
{
	$pos_payments_journal_id = (ISSET($_GET['pos_payments_journal_id'])) ? $_GET['pos_payments_journal_id'] : $_POST['pos_payments_journal_id'];
	$method = (ISSET($_GET['method'])) ? $_GET['method'] : $_POST['method'];

	$filename = 'Payment_' .$pos_payments_journal_id .'.pdf';
	
	//create the check	
	
	$pos_account_id = getSingleValueSQL("Select pos_account_id FROM pos_payments_journal WHERE pos_payments_journal_id = $pos_payments_journal_id");
	
	$pdf = createPDFCheck($pos_account_id,$pos_payments_journal_id, $filename,false);

	if($method == 'INLINE')
	{
	
	
		$pdf->Output($filename, 'I');
	}
	else
	{
		//what is the printer id?
		//we would need a little help from the user
		$pos_printer_id = (ISSET($_GET['pos_printer_id'])) ? $_GET['pos_printer_id'] : $_POST['pos_printer_id'];
		//checks need to be printed to the right printer.
		//accounts need to be linked to a printer.
		// no need for a terminal check then...
		$printer_name = getPrinterName($pos_printer_id);
		$print_folder = CHECK_PRINT_FOLDER .$printer_name;
		//check and create directory
		makeDir($print_folder);
		
		
		
		$pdf->Output($filename, 'F');
		echo 'Sent to Printer';

	}
}

?>