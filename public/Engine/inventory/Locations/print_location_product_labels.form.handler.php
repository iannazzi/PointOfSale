<?php
$binder_name = 'Locations';
$access_type = 'Write';
$page_title = 'Inventory By Location';
require_once ('../inventory_functions.php');
//pass in the $_POST['pos_product_sub_id'][] array, $_POST['quantity'], row_offest, column_offset, template, $filename

		

		
		$counter = 0;
		$product_sub_ids = array();
		$quantities = array();
		$pos_location_id = $_POST['pos_location_id'];
		for($row=0;$row<sizeof($_POST['row_number']);$row++)
		{
			//if(isset($_POST['row_checkbox_'.$row]) )
			//{
				$barcodes[$counter] = $_POST['barcode'][$row];
				$quantities[$counter] = $_POST['quantity'][$row];
				$counter++;
			//}
		}
		$row_offset = scrubInput($_POST['row_offset']);
		$column_offset = scrubInput($_POST['column_offset']);
		$filename = scrubInput($_POST['filename']);
		printLocationProductLabelsAvery5167($pos_location_id, $barcodes, $quantities, $row_offset, $column_offset,  $filename);

function printLocationProductLabelsAvery5167($pos_location_id, $barcodes, $quantities, $row_offset, $column_offset, $filename)
{

	//this is the same as the product but we are going to add the location id to the printout.... on each page
	
	require_once(TCPDF_LANG);
	require_once(TCPDF);
	
	$pdf_file_name = $filename;
	
	$subid = array();
	$poc_title = array();
	$color_price = array();

	for($i=0;$i<sizeof($barcodes);$i++)
	{
		$barcode = $barcodes[$i];

			for($qty=0;$qty<$quantities[$i];$qty++)
			{
				$subid[] = $barcode;

			}

		
		
	}

	$margin_left = 0;
	$margin_right = 0;
	$margin_top = 0;
	$margin_bottom = 0;

	$columns = 4;
	$rows = 20;
	
	$title = 'Avery 5167 template';
	$subject = 'PO # 123';
	$keywords = 'purchase order 123';
	$page_orientation = 'P';
	$page_format = 'LETTER';
	$unit = 'in';
	
	// create new PDF document
	$pdf = new TCPDF($page_orientation, $unit, $page_format, true, 'UTF-8', false);
	// set document information
	$pdf->SetCreator(getSetting('company_name'));
	$pdf->SetAuthor(getUserFullName($_SESSION['pos_user_id']));
	$pdf->SetTitle($title);
	$pdf->SetSubject($subject );
	$pdf->SetKeywords($keywords);
	$pdf->setPrintHeader(false);
	$pdf->setPrintFooter(false);
	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
	$pdf->SetMargins($margin_left, $margin_top, $margin_right);
	$pdf->SetAutoPageBreak(TRUE, $margin_bottom);
	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
	$pdf->setLanguageArray($l);
	$preferences = array('PrintScaling' => 'None');
	$pdf->setViewerPreferences($preferences);
	$pdf->SetFont('helvetica', 'R', 5);
	
	//$pdf->setCellHeightRatio(3);
	$counter = 0;
	
	//calculating the pages.... how many labels are to be printed on the sheet...
	//how many labels are going on the first sheet?
	$first_page_number_of_spots = ($rows)*($columns-($column_offset-1)) -($row_offset-1);
	$number_of_labels = sizeof($subid);
	
	//now add on the location label only on the first page...
	$number_of_labels = $number_of_labels + 1;
	
	if($number_of_labels <= $first_page_number_of_spots)
	{
		$pages = 1;
	}
	else
	{
		$lables_on_first_page = $first_page_number_of_spots;
		$labels_remaining = $number_of_labels -$lables_on_first_page;
		$number_of_spots_per_page = $rows*$columns;
		$pages = ceil($labels_remaining/($number_of_spots_per_page)) + 1;
	}
	$location_printed=false;
	for($page=0;$page<$pages;$page++)
	{
		$pdf->AddPage();
		for($col=$column_offset-1;$col<$columns;$col++)
		{
			for($row=$row_offset-1;$row<$rows;$row++)
			{
				if($location_printed)
				{
					if($counter< sizeof($subid))
					{
						$pdf = addProductLabelToPdfAvery5167($pdf, $subid[$counter], $row, $col);
					}
					$counter++;
				}
				else
				{
					$pdf = addLocationLabelToPdfAvery5167($pdf, $pos_location_id, $row, $col);	
					$location_printed=true;
				}
				
			}
			$row_offset = 1;
		}
		$column_offset = 1;
	}
//Close and output PDF document
$pdf->Output($pdf_file_name, 'D');

//============================================================+
// END OF FILE
//============================================================+
}
?>