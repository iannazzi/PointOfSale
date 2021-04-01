<?PHP
$page_level = 5;
$page_navigation = 'purchase_order';
$page_title = 'print_labels';
require_once ('../../../Config/config.inc.php');
require_once(PHP_LIBRARY);

require_once (CHECK_LOGIN_FILE);
require_once ('../po_functions.php');
$pos_purchase_order_id =  getPostOrGetID('pos_purchase_order_id');
$complete_location = '../ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id='.$pos_purchase_order_id;
if (isset($_POST['submit']))
{
	
	$row_offset = $_POST['row_offset'];
	$column_offset = $_POST['column_offset'];
	if(isset($_POST['print_comments']))
	{
		$print_comments = true;
	}
	else
	{
		$print_comments = false;
	}
	if(isset($_POST['print_labels']))
	{
		$print_labels = true;
	}
	else
	{
		$print_labels = false;
	}


	tcpdf_multicell_testV2($pos_purchase_order_id, $row_offset, $column_offset, $_POST['receive_date_select'], $print_labels, $print_comments);
	updatePOLog($pos_purchase_order_id, "Labels Printed");
}
else
{
	header('Location: '.$complete_location);
}

function tcpdf_multicell_testV2($pos_purchase_order_id, $row_offset, $column_offset, $ordered_allReceived__or_single_receive, $print_labels, $print_comments)
{

	require_once(TCPDF_LANG);
	require_once(TCPDF);
	$brand_code = getBrandCode(getBrandIdFrompoid($pos_purchase_order_id));
	
	
	if($ordered_allReceived__or_single_receive == 'all_ordered')
	{
		$select_column = 'quantity_ordered';
		$sql = "SELECT pos_products_sub_id.pos_product_sub_id, size, color_code, color_description, pos_purchase_order_contents.style_number, pos_products.title, quantity_ordered,  product_subid_name , pos_products.retail_price, pos_purchase_order_contents.comments
			FROM pos_purchase_order_contents 
			LEFT JOIN pos_products_sub_id USING (pos_product_sub_id)
			LEFT JOIN pos_products ON pos_products.pos_product_id = pos_products_sub_id.pos_product_id
			WHERE pos_purchase_order_id = $pos_purchase_order_id AND pos_purchase_order_contents.quantity_ordered > 0";
	}
	elseif($ordered_allReceived__or_single_receive == 'all_received')
	{
		//need to print all received items
		$select_column = 'received_quantity';
		$sql = "SELECT pos_products.title, pos_purchase_order_contents.pos_product_sub_id, pos_purchase_order_contents.comments,
			
			(SELECT sum(pos_purchase_order_receive_contents.received_quantity) FROM  pos_purchase_order_receive_contents WHERE pos_purchase_order_receive_contents.pos_purchase_order_content_id = pos_purchase_order_contents.pos_purchase_order_content_id ) 
		as received_quantity
			
			FROM pos_purchase_order_contents 
			LEFT JOIN pos_purchase_orders ON pos_purchase_orders.pos_purchase_order_id = pos_purchase_order_contents.pos_purchase_order_id
			LEFT JOIN pos_products_sub_id USING (pos_product_sub_id)
			LEFT JOIN pos_products USING (pos_product_id)
			
			WHERE pos_purchase_order_contents.pos_purchase_order_id = $pos_purchase_order_id AND pos_purchase_order_contents.quantity_ordered > 0";
	}
	else
	{
		//a received_event_id was passed in.....
		$pos_purchase_order_receive_event_id = $ordered_allReceived__or_single_receive;
		$select_column = 'received_quantity';
		$sql = "SELECT pos_products.title,
		pos_purchase_order_contents.pos_product_sub_id,pos_purchase_order_contents.comments,
			
			pos_purchase_order_receive_contents.received_quantity
			
			FROM pos_purchase_order_receive_contents
			 
			LEFT JOIN  pos_purchase_order_receive_event
			ON pos_purchase_order_receive_event.pos_purchase_order_receive_event_id = pos_purchase_order_receive_contents.pos_purchase_order_receive_event_id
			LEFT JOIN pos_purchase_order_contents USING (pos_purchase_order_content_id)
			LEFT JOIN pos_products_sub_id USING (pos_product_sub_id)
			LEFT JOIN pos_products USING (pos_product_id)
			WHERE pos_purchase_order_receive_event.pos_purchase_order_id = $pos_purchase_order_id AND pos_purchase_order_contents.quantity_ordered > 0 AND pos_purchase_order_receive_event.pos_purchase_order_receive_event_id = $pos_purchase_order_receive_event_id";
			
	}
	

	$poc_data = getSQL($sql);

	$subid = array();
	$poc_title = array();
	$color_price = array();
	$label_type = array();
	
	// add start PO# label
	
	$subid[] = '';
	$poc_title[] = 'Start PO#' .$pos_purchase_order_id;
	$color_price[] = '';//take care of this later....
	$label_type[] = 'PRODUCT';
	 
	for($i=0;$i<sizeof($poc_data);$i++)
	{
		for($qty=0;$qty<$poc_data[$i][$select_column];$qty++)
		{
			if($print_labels)
			{
				$subid[] = $poc_data[$i]['pos_product_sub_id'];
				$poc_title[] = '';//take care of this later....
				$color_price[] = '';//take care of this later....
				$label_type[] = 'PRODUCT';
			}
			if ($print_comments && $poc_data[$i]['comments'] !='')
			{
				$pos_product_sub_id = $poc_data[$i]['pos_product_sub_id'];
				$max_chars = 45;
				$subid[] = $poc_data[$i]['comments'];
				$poc_title[] = substr(ucwords(strtolower($poc_data[$i]['title'])),0,$max_chars);
				
				$options = getProductSubIdOptionsList($pos_product_sub_id);
				
				if(strlen($options)>$max_chars)
				{
					$options = getProductSubIdOptionsListNoDescription($pos_product_sub_id);
					if(strlen($options)>$max_chars)
					{
						$options = getProductSubIdOptionsCodeListNoDescription($pos_product_sub_id);
					}
		
				}
				$options =substr($options,0,$max_chars);
				
				
				$color_price[] = $options;
				$label_type[] = 'COMMENT';
			}
			
		}
	}
	
	// add end PO# label
	$subid[] = '';
	$poc_title[] = 'Received   Not Received';
	$color_price[] = '';//take care of this later....
	$label_type[] = 'PRODUCT';

	

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
	
	//barcode: 128a?
	// define barcode style
	$barcode_style = array(
    'position' => '',
    'align' => 'C',
    'stretch' => false,
    'fitwidth' => false,
    'cellfitalign' => '',
    'border' => false,
    'hpadding' => '0',
    'vpadding' => '0',
    'fgcolor' => array(0,0,0),
    'bgcolor' => false, //array(255,255,255),
    'text' => false,
    'font' => 'helvetica',
    'fontsize' => 4,
    'stretchtext' => 0
);
	

	// set border width
	$pdf->SetLineWidth(0.01);
	$pdf->SetDrawColor(0,0,0);
	//$pdf->setCellHeightRatio(3);
	$counter = 0;
	
	//calculating the pages.... how many labels are to be printed on the sheet...
	//how many labels are going on the first sheet?
	$first_page_number_of_spots = ($rows)*($columns-($column_offset-1)) -($row_offset-1);
	$number_of_labels = sizeof($subid);
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
				if($counter< sizeof($subid))
				{
					if ($counter == 0 || $counter == sizeof($subid) -1)
					{
						//add the po start or end label
						$x_spot = $cell_spacing + $col*$cell_width + $col*$cell_spacing;
						$y_spot = $cell_height + $row*$cell_height;
						$coords = 'X:'.$x_spot . ' Y:' .$y_spot;
						$border = 0;
						$border2 = 0;
						//this is the cell that will allow allignment to sticker checking
						$pdf->SetFont('helvetica', 'B', 10);
						$pdf->SetXY($x_spot, $y_spot);
						$pdf->Cell($cell_width, $cell_height,  '', $border, 0, 'C', 0, '', 0, false, 'T', 'M');
				

				
						//the remaining 3 lines have to fit in 1/2 the sticker size
						//$y_offset = $cell_height/2;
						$pdf->SetXY($x_spot, $y_spot - 0*$line_spacing_adjust);
						$pdf->Cell($cell_width, $cell_height,  $poc_title[$counter], $border2, 0, 'C', 0, '', 0, false, 'T', 'C');
						
					}
					else if($label_type[$counter] == 'PRODUCT')
					{
						$pdf = addProductLabelToPdfAvery5167($pdf, $subid[$counter], $row, $col);
					}
					//barcodes must be cap
					$line1 = strtoupper($subid[$counter]);
					$line2 = $poc_title[$counter];
					$line3 = $color_price[$counter];
				}
				else
				{
					$line1 = '';
					$line2 = '';
					$line3 = '';
				}
				
				if($counter< sizeof($subid) && $label_type[$counter] == 'COMMENT')
				{
					
					$x_spot = $cell_spacing + $col*$cell_width + $col*$cell_spacing;
					$y_spot = $cell_height + $row*$cell_height;
					$coords = 'X:'.$x_spot . ' Y:' .$y_spot;
					$border = 0;
					$border2 = 0;
					//this is the cell that will allow allignment to sticker checking
					$pdf->SetXY($x_spot, $y_spot);
					$pdf->Cell($cell_width, $cell_height,  '', $border, 0, 'C', 0, '', 0, false, 'T', 'M');
				

				
					//the remaining 3 lines have to fit in 1/2 the sticker size
					//$y_offset = $cell_height/2;
					$pdf->SetXY($x_spot, $y_spot - 0*$line_spacing_adjust + 3/6*$cell_height);
					$pdf->Cell($cell_width, $cell_height/6,  $line1, $border2, 0, 'C', 0, '', 0, false, 'T', 'C');
					$pdf->SetXY($x_spot, $y_spot - 1*$line_spacing_adjust + 4/6*$cell_height);
					$pdf->Cell($cell_width, $cell_height/6,  $line2, $border2, 0, 'C', 0, '', 0, false, 'T', 'C');
					$pdf->SetXY($x_spot, $y_spot -2*$line_spacing_adjust + 5/6*$cell_height);
					$pdf->Cell($cell_width, $cell_height/6,  $line3, $border2, 0, 'C', 0, '', 0, false, 'T', 'C');
				
				
				}
				$counter++;
			}
			$row_offset = 1;
		}
		$column_offset = 1;
	}
//Close and output PDF document
$pdf->Output($pos_purchase_order_id . '.pdf', 'D');

//============================================================+
// END OF FILE
//============================================================+
}

?>