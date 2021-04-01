<?php
$binder_name = 'Locations';
$access_type = 'Write';
$page_title = 'Print Labels';
require_once ('../inventory_functions.php');
		
		$counter = 0;
		$location_ids = array();
		$quantities = array();
		for($row=0;$row<sizeof($_POST['row_number']);$row++)
		{
			if(isset($_POST['row_checkbox'][$row]) )
			{
				$location_ids[$counter] = $_POST['pos_location_id'][$row];
				$quantities[$counter] = $_POST['quantity'][$row];
				$counter++;
			}
		}
		$row_offset = scrubInput($_POST['row_offset']);
		$column_offset = scrubInput($_POST['column_offset']);
		$filename = scrubInput($_POST['filename']);
		printLocationLabelsAvery5167V2($location_ids, $quantities, $row_offset, $column_offset,  $filename);
		
function printLocationLabelsAvery5167V2($location_ids, $quantities, $row_offset, $column_offset, $filename)
{

	require_once(TCPDF_LANG);
	require_once(TCPDF);
	
	$pdf_file_name = $filename;
	
	$id = array();
	for($i=0;$i<sizeof($location_ids);$i++)
	{
		for($qty=0;$qty<$quantities[$i];$qty++)
		{
			$id[] =  $location_ids[$i];
			
		}
	}

	$margin_left = 0;
	$margin_right = 0;
	$margin_top = 0;
	$margin_bottom = 0;
	$cell_width = 1.75;
	$cell_height = 0.5;
	$cell_spacing = 0.3;
	$columns = 4;
	$rows = 20;
	$line_spacing_adjust = 0.015;
	$barcode_spacing_adjust = 0.1;
	$barcode_height_adjust = 0.05;
	
	$title = 'Avery 5167 template';
	$subject = 'location';
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
	
	
	$counter = 0;
	
	//calculating the pages.... how many labels are to be printed on the sheet...
	//how many labels are going on the first sheet?
	$first_page_number_of_spots = ($rows)*($columns-($column_offset-1)) -($row_offset-1);
	$number_of_labels = sizeof($id);
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
	for($page=0;$page<$pages;$page++)
	{
		$pdf->AddPage();
		for($col=$column_offset-1;$col<$columns;$col++)
		{
			for($row=$row_offset-1;$row<$rows;$row++)
			{
				if($counter< sizeof($id))
				{
					$pdf = addLocationLabelToPdfAvery5167($pdf, $id[$counter], $row, $col);
				}
				$counter++;
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