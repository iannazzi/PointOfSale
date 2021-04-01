<?php
/*
*	pos_database_commands.php
*	In an attempt to reduct the amount of code for interacting with the database I am going to include all mysql queries here
*	These are the functions need to write, update, insert, get, products, manufactureres...etc

*/

$page_level = 5;


require_once ('../../../Config/config.inc.php');
require_once (PHP_LIBRARY);
require_once (CHECK_LOGIN_FILE);

/****************************Functions *************************************/
function loadPurchaseOrderContentsBroken($pos_purchase_order_id, $size_chart)
{
	$tbody_data = array();
	// this function will send back an array that matches the table
	$purchase_order_dbase_table = getPurchaseOrderData($pos_purchase_order_id);
	$pos_manufacturer_brand_id = $purchase_order_dbase_table['pos_manufacturer_brand_id'];
	//$brand_sizes = getBrandSizes($pos_manufacturer_brand_id);
	//get the po contents
	$purchase_order_contents_r = getPurchaseOrderContents($pos_purchase_order_id);
	while ($purchase_order_contents_row = mysqli_fetch_array($purchase_order_contents_r, MYSQLI_ASSOC))
	{
		$row = $purchase_order_contents_row['poc_row_number']; 
		$c=0;
		$tbody_data[$row][$c] = 'off';$c++;
		$tbody_data[$row][$c] = $purchase_order_contents_row['style_number'];$c++;
		$tbody_data[$row][$c] = $purchase_order_contents_row['color_code'];$c++;
		$tbody_data[$row][$c] = $purchase_order_contents_row['color_description'];$c++;
		$tbody_data[$row][$c] = $purchase_order_contents_row['title'];$c++;		
		$tbody_data[$row][$c] = $purchase_order_contents_row['pos_category_id'];$c++;
		//now we need to find out if there is a cup
		if ($size_chart['bln_cup'] == 1) 
		{
			$tbody_data[$row][$c] = $purchase_order_contents_row['cup'];$c++;
		}
		//inseam
		if ($size_chart['bln_inseam'] == 1)
		{
			 $tbody_data[$row][$c] = $purchase_order_contents_row['inseam'];$c++;
		}
		//now the size - only one in this row data, we need to find the correct column...
		//keep in mind the size chart may have changed - how should we should check for that???
		$num_sizes = $size_chart['num_sizes'];
		echo ' qty :'.$purchase_order_contents_row['quantity_ordered'].' ';
		echo 'size: ' .$purchase_order_contents_row['size'] . ' ';
		$qty = 0;
		for ($sz_col = 0;$sz_col < $num_sizes; $sz_col++)
		{
			$size='';
			for ($sz_chrt_row=0;$sz_chrt_row<sizeof($size_chart['sizes']);$sz_chrt_row++)
			{
					if ($purchase_order_contents_row['size'] == $size_chart['sizes'][$sz_chrt_row][$sz_col])
					{
						$size=$purchase_order_contents_row['quantity_ordered'];
						
						//$tbody_data[$row][$c+$sz_col] = $purchase_order_contents_row['quantity_ordered'];
					}
				
			}
			
			if (!isset($tbody_data[$row][$c]))
			{
				//need to assign any value
				$tbody_data[$row][$c] = $size;$c++;
			}
			else
			{
				// it is set, now if the current value is something other than '' then put that in
				if ($tbody_data[$row][$c] == '')
				{
					// assing a value
					$tbody_data[$row][$c] = $size;
					$c++;
				}
				else
				{
					// the data has already been assigned
					$c++;
				}
			}
		
			
		}
		//quantity
		$tbody_data[$row][$c] = $qty;$c++;
		$tbody_data[$row][$c] = round($purchase_order_contents_row['cost'],2);$c++;
		$tbody_data[$row][$c] = round($purchase_order_contents_row['retail'],2);$c++;
		$tbody_data[$row][$c] = $purchase_order_contents_row['cost']*$qty;$c++;
		$tbody_data[$row][$c] = parse_json_newlines($purchase_order_contents_row['comments']);$c++;
		$tbody_data[$row][$c] = $purchase_order_contents_row['size_row'];
	}
	return $tbody_data;
}

function createBrandSizeChartNOTUSED()
{
	//i think this was some big fancy way to do a mysql query with join, rather than an idividual query, but in
	// review I can't understand the query and it is making me nausos
	
	$size_chart_q = "SELECT pos_purchase_orders.pos_manufacturer_brand_id, pos_manufacturer_brand_sizes.pos_manufacturer_brand_size_id, pos_manufacturer_brand_sizes.cup, pos_manufacturer_brand_sizes.pos_category_id, pos_manufacturer_brand_sizes.inseam, pos_manufacturer_brand_sizes.sizes
	FROM pos_purchase_orders
	LEFT JOIN pos_manufacturer_brand_sizes 
	ON pos_purchase_orders.pos_manufacturer_brand_id = pos_manufacturer_brand_sizes.pos_manufacturer_brand_id
	WHERE pos_purchase_order_id='$pos_purchase_order_id' AND pos_manufacturer_brand_sizes.pos_manufacturer_brand_id = pos_purchase_orders.pos_manufacturer_brand_id";	
	$size_chart_r = @mysqli_query ($dbc, $size_chart_q);
	//Need to get the number of rows
	$num_size_rows = mysqli_num_rows($size_chart_r);
}
function selectNewOrStoredBrandSizeChart($pos_purchase_order_id)
{
	$pos_manufacturer_brand_id = getBrandIdFromPOId($pos_purchase_order_id);
	$stored_size_data = loadStoredSizeChart($pos_purchase_order_id);
	$brand_size_chart = getBrandSizeChart($pos_manufacturer_brand_id);
	if ($stored_size_data['num_size_rows'] == 0)
	{
		//this is a new order, load from the manufacturer side
		$brand_size_chart = getBrandSizeChart($pos_manufacturer_brand_id);
	}
	else
	{
		//this is an older order - load from the PO side
		$brand_size_chart = $stored_size_data;
	}
	return $brand_size_chart;
}
function selectNewOrStoredBrandSizeChartforPR($pos_purchase_return_id)
{
	$pos_manufacturer_brand_id = getBrandIdFromPRId($pos_purchase_return_id);
	$stored_size_data = loadStoredSizeChartfromPR($pos_purchase_return_id);
	$brand_size_chart = getBrandSizeChart($pos_manufacturer_brand_id);
	if ($stored_size_data['num_size_rows'] == 0)
	{
		//this is a new order, load from the manufacturer side
		$brand_size_chart = getBrandSizeChart($pos_manufacturer_brand_id);
	}
	else
	{
		//this is an older order - load from the PO side
		$brand_size_chart = $stored_size_data;
	}
	return $brand_size_chart;
}
/****************************CREATING *************************************/
function createPOTable($pos_purchase_order_id)
{
/*
	*purchase_order_content_overview.form.php
	*Craig Iannazzi 2-2-2012
	*This file will display the overview of the purchase order.
	
*/

	//Retrieve the purchase order information

	$pos_purchase_order_row = getPurchaseOrderOverview($pos_purchase_order_id);
	
	$po_title  = $pos_purchase_order_row[0]['po_title'];
	$delivery_date  = $pos_purchase_order_row[0]['delivery_date'];
	$cancel_date  = $pos_purchase_order_row[0]['cancel_date'];
	$purchase_order_number  = $pos_purchase_order_row[0]['purchase_order_number'];
	$manufacturer_purchase_order_number  = $pos_purchase_order_row[0]['manufacturer_purchase_order_number'];
	$pos_manufacturer_id = $pos_purchase_order_row[0]['pos_manufacturer_id'];
	$pos_manufacturer_brand_id = $pos_purchase_order_row[0]['pos_manufacturer_brand_id'];
	$pos_store_id = $pos_purchase_order_row[0]['pos_store_id'];
	$pos_user_id = $pos_purchase_order_row[0]['pos_user_id'];
	
	$html = createHiddenInput('pos_manufacturer_brand_id', $pos_manufacturer_brand_id);
	echo $html;

	// Get the manufacturer information
	$selected_manufacturer = getManufacturer($pos_manufacturer_id);
	$selected_brand = getBrand($pos_manufacturer_brand_id);
	$brand_code = $selected_brand[0]['brand_code'];
	
	// Get the store information
	$shipto_selected_store = getStore($pos_store_id);
	
	// Get the employee generating the PO information
	$selected_employee = getUser($pos_user_id);



	// re-display the info
	//************** this is the table with the manufacture, the PO details, and the store delivery details *********************
	
	echo ' <div class = "po_summary_div">';
	echo '<TABLE id = "po_summary" class ="po_summary">';
		echo '<TR  >';
			echo '<th';
			if ($po_title == "") echo ' class = "error"';
			echo '> PO Title: </TH><TD ><input name ="po_title" id="poc_title"  maxlength = "255" value ="' . $po_title .'"></input></td>';
			echo '<TH id = "poc_supplierName"> Brand: </TH> <TD id = "poc_manufacturer">' . $selected_brand[0]['brand_name'] . '</td>';
			echo '<TH id = "poc_ddate"> Delivery Date: </TH> <TD id = "poc_deliveryDate">' . $delivery_date . '</td>';
			echo '<TH id ="poc_shipto"> Ship To: </TH> <TD >' . $shipto_selected_store[0]['store_name'] . '</td>';
			echo '<TH id ="poc_ponumber" ';
			if ($po_title == "") echo ' class = "error"';
			echo'> PO Number: </TH> <TD>' . $purchase_order_number . '</td>';
		echo '</tr>';
	echo '</table></p>';
	echo '</div>';
	echo'
	<style type="text/css">
	/************************************  PO SUMMARY (OVERVIEW) Table  - Goes on top of POC  ************/
		
		.po_summary_div
		{
		padding: 10px 0px 0px 0px;
		margin: 0px;
		}
		
		#po_summary
		{
			width:100%;
			color: rgb(0,0,0);
			padding: 0px 0px 1px 0px;
			margin: 0px;
			/*border-collapse:collapse;*/
			border-top: 1px solid black; 
			border-left: 1px solid black;
			border-right: 1px solid black;
			border-bottom: 1px solid black;
			text-align: left;
		}
		#po_summary th,td
		{
			border: 0px solid black;
			padding: 0px;
			margin: 0px;
			
		}
	
		#po_summary #poc_supplierName
		{
		}
	</style>';
	
}
function createPOCtable($pos_purchase_order_id, $readonly)
{

/*
*	purchase_order_contents.poc_table.inc.php
*	This file was created to get the table out of my hair so I would stop wanting to throw up
*/
	$number_of_po_contents = getPurchaseOrderContents($pos_purchase_order_id);
	if (sizeof($number_of_po_contents) == 0)
	{
		return '<p class = "error">No Records To Display</p>';
	}
	else
	{
		$html ='';
		$pos_purchase_order_row = getPurchaseOrderOverview($pos_purchase_order_id);
		$pos_manufacturer_brand_id = $pos_purchase_order_row[0]['pos_manufacturer_brand_id'];
		//$pos_manufacturer_id = $pos_purchase_order_row[0]['pos_manufacturer_id'];
		
		//load any previously created contents
		$brand_size_chart = loadStoredSizeChart($pos_purchase_order_id);
		$tbody_data = loadPurchaseOrderContents($pos_purchase_order_id, $brand_size_chart);

		
		//preprint($tbody_data);
		//$json_tbody_data = json_encode($tbody_data);
		//$html = createHiddenInput('stored_size_chart', json_encode($brand_size_chart));
		//$html .= $html;
		$num_sizes = $brand_size_chart['num_sizes'];
		//Get the size category ids - these are used to default a  'bra' product to 'bra' sizing
		$size_category_ids = $brand_size_chart['size_categories'];
		$size_chart = $brand_size_chart['sizes'];
		$num_size_rows = $brand_size_chart['num_size_rows'];
		//Find out if there is a cup or inseam set to Yes
		$bln_cup = $brand_size_chart['bln_cup'];
		$bln_inseam = $brand_size_chart['bln_inseam'];
		$pos_manufacturer_brand_size_ids = $brand_size_chart['pos_manufacturer_brand_size_id'];
		
		if ($num_sizes == 0)
		{
			$html .= '<p class = error>This Manufacturer does not have a size chart setup, please create one.</p>';
		}
		//set up the columns - 
		$start_columns = 6;
		$footer_colspan = 6;
		if ($bln_cup == 1) 
		{
			$footer_colspan = $footer_colspan + 1;
			$start_columns = $start_columns + 1;
		}
		if ($bln_inseam == 1) 
		{
			$footer_colspan = $footer_colspan + 1;
			$start_columns = $start_columns + 1;
		}
				//preprint($start_columns);

		if(isset($brand_size_chart['attributes']) && sizeof($brand_size_chart['attributes'])>0)
		{
			for($atr=0;$atr<sizeof($brand_size_chart['attributes']);$atr++)
			{
				$footer_colspan = $footer_colspan + 1;
				$start_columns = $start_columns + 1;
			}
		}
		$html .= '<div class = "poc_table_div">';
		$html .= '<TABLE id="poc_table" class="poc_table" summary="Embrasse-Moi Purchase Order Details">
		<THEAD id="poc_thead" valign="bottom">
			<tr>
				<th rowspan="' . $num_size_rows . '" width = "14px"></th>
				<th rowspan="' . $num_size_rows . '"><b>STYLE<BR>#</B></th>
				<th rowspan="' . $num_size_rows . '"><b>COLOR<BR>OR SUB<BR>CODE</b></th>
				<th rowspan="' . $num_size_rows . '"><b>COLOR<BR>DESCRIPTION</b></th>
				<th rowspan="' . $num_size_rows . '"><b>TITLE</b></th>
				<th rowspan="' . $num_size_rows . '" ><b><a href="http://www.embrasse-moi.com/POS/add_category.php"> CATEGORY </a></B></th>';
				//<th rowspan="' . $num_size_rows . '"><b>DESCRIPTION</b></th>';
			
				if ($bln_cup == 1) $html .= '<th rowspan="' . $num_size_rows . '" width = "20px"><b>CUP<BR>SIZE</b></th>';
				if ($bln_inseam == 1) $html .= '<th rowspan="' . $num_size_rows . '" width = "20px"><b>INSEAM<BR>SIZE</b></th>';
				
			if(isset($brand_size_chart['attributes']) && sizeof($brand_size_chart['attributes'])>0 )
			{
				for($atr=0;$atr<sizeof($brand_size_chart['attributes']);$atr++)
				{
				$html .= '<th rowspan="' . $brand_size_chart['num_size_rows'] . '" width = "20px"><b>'.$brand_size_chart['attributes'][$atr].'</b></th>';
				}
			}
				
				//This is the first size row.....
				if ($num_sizes == 0)
				{
					$html .= '<th onclick="sizeSelect(this)">One<br>Size</th>';
					//Add a hidden value for the brand size id row
						$pos_manufacturer_brand_size_id = 'null';
						$html .=  '<input type="hidden" name="pos_manufacturer_brand_size_id_r0" value="' . $pos_manufacturer_brand_size_id  . '" />';
						$num_sizes=1;
				}
				else
				{
					foreach ($size_chart[0] as $value) 
					{
						$html .= '<th onclick="sizeSelect(this)">' . $value . '</th>';
						
					}
					//Add a hidden value for the brand size id row
						$pos_manufacturer_brand_size_id = $pos_manufacturer_brand_size_ids[0];
						$html .=  '<input type="hidden" name="pos_manufacturer_brand_size_id_r0" value="' . $pos_manufacturer_brand_size_id  . '" />';
				}
				//finish the table
				$html .= '
				<th rowspan="' . $num_size_rows . '" ><b>QTY</b></th>
				<th rowspan="' . $num_size_rows . '" ><b>COST</b></th>
				<th rowspan="' . $num_size_rows . '" ><b>RETAIL</b></th>
				<th rowspan="' . $num_size_rows . '" ><b>TOTAL</b></th>
				<th rowspan="' . $num_size_rows . '" ><b>COMMENTS</b></th>
			</tr>';
			$size_row_cntr = 1;
			//These are the remaining size rows.....
			for ($i=1;$i<$num_size_rows;$i++)
			{
				$pos_manufacturer_brand_size_id = $pos_manufacturer_brand_size_ids[$i];
				$html .=  '<input type="hidden" name="pos_manufacturer_brand_size_id_r' . $size_row_cntr . '" value="' . $pos_manufacturer_brand_size_id  . '" />'."\n";
				$html .= '<tr>';
				foreach ($size_chart[$i] as $value) 
				{
					$html .= '<th onclick="sizeSelect(this)">' . $value . '</th>' ."\n";
				}
				$html .= '</tr>';			
			}
		$html .= '	</thead>';
		$html .= '	<tbody id = "poc_tbody" name = "poc_tbody">';
		$categories = getCategoryAssociativeArray();
		for ($body_row = 0;$body_row < sizeof($tbody_data); $body_row++)
		{
			//blast through tbody data and shove it into readonly inputs
			$html .= '<tr>';
			$html .= '<td class ="poc_td_readonly" >' . '' . '</td>';
			$col=1;
			//style
			$html .= '<td class ="poc_td_readonly" >' . $tbody_data[$body_row][$col]. '</td>';$col++;
			//colorcode
			$html .= '<td class ="poc_td_readonly" >' .$tbody_data[$body_row][$col] . '</td>';$col++;
			//colorDescription
			$html .= '<td class ="poc_td_readonly" >' .$tbody_data[$body_row][$col] . '</td>';$col++;
			//title
			$html .= '<td class ="poc_td_readonly" >' . $tbody_data[$body_row][$col] . '</td>';$col++;
			//category
			if ($tbody_data[$body_row][$col] == '0')
			{
				$category = 'Not Selected';
			}
			else
			{
				
				$category = $categories[$tbody_data[$body_row][$col]];
			}
			$html .= '<td class ="poc_td_readonly" >' . $category . '</td>';$col++;
			//cup and inseam
			if ($bln_cup == 1)
			{
				$html .= '<td class ="poc_td_readonly" >' . $tbody_data[$body_row][$col] . '</td>';$col++;
			}
			if ($bln_inseam == 1)
			{
				$html .= '<td class ="poc_td_readonly" >' . $tbody_data[$body_row][$col] . '</td>';$col++;
			}
			if(isset($brand_size_chart['attributes']) && sizeof($brand_size_chart['attributes'])>0)
			{
				for($atr=0;$atr<sizeof($brand_size_chart['attributes']);$atr++)
				{
					$html .= '<td class ="poc_td_readonly" >' . $tbody_data[$body_row][$col] . '</td>';$col++;

				}
			}
			
			for($sz=$start_columns;$sz < $num_sizes+$start_columns;$sz++)
			{
				$html .= '<td class ="poc_td_readonly" >' . $tbody_data[$body_row][$col] . '</td>';$col++;
			}
			//qty
			$html .= '<td class ="poc_td_readonly" >' . sumPurchaseOrderQuantityRow($pos_purchase_order_id, $body_row) . '</td>';$col++;
			//cost
			$html .= '<td class ="poc_td_readonly" >' . $tbody_data[$body_row][$col] . '</td>';$col++;
			//retail
			$html .= '<td class ="poc_td_readonly" >' . $tbody_data[$body_row][$col] . '</td>';$col++;
			//total
			$html .= '<td class ="poc_td_readonly" >' . sumPurchaseOrderRow($pos_purchase_order_id, $body_row) . '</td>';$col++;
			//remarks
			$html .= '<td class ="poc_td_readonly" ><textarea readonly="readonly">' . $tbody_data[$body_row][$col]. '</textarea></td>';$col++;
			$html .= '</tr>';
		}	
		$html .= '</tbody>';
		$html .= '
			<tfoot id = "poc_tfoot">
			<tr>
				<td colspan = "' . $footer_colspan . '" id = "emptyCell"></td>
				<td colspan = "' . $num_sizes . '">Totals:</td>
				<td name =  "poc_total_qty" class = "poc_text" id = "poc_total_qty">' . sumPurchaseOrderQuantity($pos_purchase_order_id) . '</td>
				<td id = "emptyCell"></td>
				<td id = "emptyCell"></td>
				<td name = "poc_total" class = "poc_text" id = "poc_total">' . number_format(sumPurchaseOrder($pos_purchase_order_id),2) . '</td>
				<td id = "emptyCell"></td>
			</tr>
			<tr>
				<td colspan = "' . $footer_colspan . '" id = "emptyCell"></td>
				<td colspan = "' . $num_sizes . '">Discounts:</td>';
				
				//<td name =  "poc_total_qty" class = "poc_text" id = "poc_total_qty">' . sumPurchaseOrderDiscountsQuantity($pos_purchase_order_id) . '</td>
				$html.='<td id = "emptyCell"></td>
				<td id = "emptyCell"></td>
				<td id = "emptyCell"></td>
				<td name = "poc_total" class = "poc_text" id = "poc_total">' . number_format(sumPurchaseOrderDiscounts($pos_purchase_order_id),2) . '</td>
				<td id = "emptyCell"></td>
			</tr>
			<tr>
				<td colspan = "' . $footer_colspan . '" id = "emptyCell"></td>
				<td colspan = "' . $num_sizes . '">Grand Total:</td>
				<td id = "emptyCell"></td>
				<td id = "emptyCell"></td>
				<td id = "emptyCell"></td>
				<td name = "poc_total" class = "poc_text" id = "poc_total">' . number_format(sumPurchaseOrderGrandTotal($pos_purchase_order_id) - sumPurchaseOrderDiscounts($pos_purchase_order_id),2) . '</td>
				<td id = "emptyCell"></td>
			</tr>
			</tfoot>';
		$html .= '</table>';
		$html .= '</div>';
		//create some hidden input
		$html .=  '<input type="hidden" name="bln_cup" value="' . $bln_cup . '" />';
		$html .=  '<input type="hidden" name="bln_inseam" value="' . $bln_inseam . '" />';
		$html .=  '<input type="hidden" name="start_columns" value="' . $start_columns . '" />';
		$html .=  '<input type="hidden" name="num_sizes" value="' . $num_sizes . '" />';
		
		//Load up some categories for javascript to use
		$categories = getCategoryArray();
		$category_names = $categories['name'];
		$category_ids = $categories['pos_category_id'];
		//Drop the variables out for javascript
		
		$json_php_to_javascript = $brand_size_chart;
		$json_php_to_javascript['num_sizes'] = $num_sizes;
		$json_php_to_javascript['start_columns'] = $start_columns;
		$json_php_to_javascript['category_names'] = $category_names;
		$json_php_to_javascript['category_ids'] = $category_ids;
		$json_php_to_javascript['readonly'] = $readonly;
		$json_php_to_javascript['pos_manufacturer_id'] = getManufacturerIdFromBrandId($pos_manufacturer_brand_id);
		$json_php_to_javascript['pos_manufacturer_brand_id'] = $pos_manufacturer_brand_id;
		$json_php_to_javascript['tbody_data'] = $tbody_data;
	
		$html .= '<script> var json_php_to_javascript = ' . json_encode($json_php_to_javascript) . ';</script>';
		return $html;
	}

}
function createMiniPOOverview($pos_purchase_order_id, $readonly ='false')
{
	//Retrieve the purchase order information
	if ($readonly == 'true')
	{
		$readonly_tag = ' readonly = "readonly" ';
	}
	else
	{
		$readonly_tag = '';
	}
	$pos_purchase_order_row = getPurchaseOrderOverview($pos_purchase_order_id);

	$po_title  = $pos_purchase_order_row[0]['po_title'];
	$delivery_date  = $pos_purchase_order_row[0]['delivery_date'];
	$cancel_date  = $pos_purchase_order_row[0]['cancel_date'];
	$purchase_order_number  = $pos_purchase_order_row[0]['purchase_order_number'];
	$manufacturer_purchase_order_number  = $pos_purchase_order_row[0]['manufacturer_purchase_order_number'];
	$pos_manufacturer_id = $pos_purchase_order_row[0]['pos_manufacturer_id'];
	$pos_manufacturer_brand_id = $pos_purchase_order_row[0]['pos_manufacturer_brand_id'];
	$pos_store_id = $pos_purchase_order_row[0]['pos_store_id'];
	$store_name = getStoreName($pos_store_id);
	$pos_user_id = $pos_purchase_order_row[0]['pos_user_id'];

	$html = createHiddenInput('pos_manufacturer_brand_id', $pos_manufacturer_brand_id);

	// Get the manufacturer information
	$selected_manufacturer = getManufacturer($pos_manufacturer_id);
	$selected_brand = getBrand($pos_manufacturer_brand_id);
	$brand_code = $selected_brand[0]['brand_code'];
	
	// Get the store information
	$shipto_selected_store = getStore($pos_store_id);
	
	// Get the employee generating the PO information
	$selected_employee = getUser($pos_user_id);



	// re-display the info
	//************** this is the table with the manufacture, the PO details, and the store delivery details *********************
	
	$html .= ' <div class = "po_summary_div">';
	$html .= '<TABLE id = "po_summary" class ="po_summary">';
		$html .= '<TR  >';
			$html .= '<th>System Purchase Order Number'. '<td>'.url_blank_link(POS_ENGINE_URL . "/purchase_orders/ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id=".$pos_purchase_order_id,$pos_purchase_order_id) .'</td>'.newline();
			$html .= '<th';
			if ($po_title == "") $html .= ' class = "error"';
			$html .= '> PO Title: </TH><TD ><input ' .$readonly_tag.' name ="po_title" id="poc_title"  maxlength = "255" value ="' . $po_title .'"/></td>';
			$html .= '<TH id = "poc_supplierName"> Brand: </TH> <TD id = "poc_manufacturer">' . manufacturerUPCLink($selected_brand[0]['pos_manufacturer_brand_id'], $selected_brand[0]['brand_name']) . '</td>';
			$html .= '<TH id = "poc_ddate"> Delivery Date: </TH> <TD id = "poc_deliveryDate">' . $delivery_date . '</td>';
			$html .= '<TH id ="poc_shipto"> Ship To: </TH> <TD >' . $shipto_selected_store[0]['store_name'] . '</td>';
			$html .= '<TH id ="poc_ponumber" ';
			if ($po_title == "") $html .= ' class = "error"';
			$html .='>Custom PO Number: </TH> <TD>' . $purchase_order_number . '</td>';
		$html .= '</tr>';
	$html .= '</table></p>';
	$html .= '</div>';
	return $html;

}
function createMiniPROverview($pos_purchase_return_id, $readonly ='false')
{
	//Retrieve the purchase order information
	if ($readonly == 'true')
	{
		$readonly_tag = ' readonly = "readonly" ';
	}
	else
	{
		$readonly_tag = '';
	}
	$pos_purchase_order_row = getPurchaseReturnData($pos_purchase_return_id);
	
	$po_title  = $pos_purchase_order_row[0]['pr_title'];
	$purchase_order_number  = $pos_purchase_order_row[0]['purchase_return_number'];
	$return_authorization_number  = $pos_purchase_order_row[0]['return_authorization_number'];
	$pos_manufacturer_id = $pos_purchase_order_row[0]['pos_manufacturer_id'];
	$pos_manufacturer_brand_id = $pos_purchase_order_row[0]['pos_manufacturer_brand_id'];
	$pos_store_id = $pos_purchase_order_row[0]['pos_store_id'];
	$store_name = getStoreName($pos_store_id);
	$pos_user_id = $pos_purchase_order_row[0]['pos_user_id'];
	
	$html = createHiddenInput('pos_manufacturer_brand_id', $pos_manufacturer_brand_id);

	// Get the manufacturer information
	$selected_manufacturer = getManufacturer($pos_manufacturer_id);
	$selected_brand = getBrand($pos_manufacturer_brand_id);
	$brand_code = $selected_brand[0]['brand_code'];
	
	// Get the store information
	$shipto_selected_store = getStore($pos_store_id);
	
	// Get the employee generating the PO information
	$selected_employee = getUser($pos_user_id);



	// re-display the info
	//************** this is the table with the manufacture, the PO details, and the store delivery details *********************
	
	$html .= ' <div class = "po_summary_div">';
	$html .= '<TABLE id = "po_summary" class ="po_summary">';
		$html .= '<TR  >';
			$html .= '<th>System Purchase Return Number'. '<td>'.url_blank_link(POS_ENGINE_URL . "/purchase_orders/ViewPurchaseReturn/view_purchase_return.php?pos_purchase_return_id=".$pos_purchase_order_id,$pos_purchase_order_id) .'</td>'.newline();
			$html .= '<th';
			if ($po_title == "") $html .= ' class = "error"';
			$html .= '> PO Title: </TH><TD ><input ' .$readonly_tag.' name ="po_title" id="poc_title"  maxlength = "255" value ="' . $pr_title .'"/></td>';
			$html .= '<TH id = "poc_supplierName"> Brand: </TH> <TD id = "poc_manufacturer">' . $selected_brand[0]['brand_name'] . '</td>';
			$html .= '<TH id ="poc_shipto"> Ship From: </TH> <TD >' . $shipto_selected_store[0]['store_name'] . '</td>';
			$html .= '<TH id ="poc_ponumber" ';
			if ($po_title == "") $html .= ' class = "error"';
			$html .='>Custom PR Number: </TH> <TD>' . $purchase_order_number . '</td>';
		$html .= '</tr>';
	$html .= '</table></p>';
	$html .= '</div>';
	return $html;

}
function createPOCThead($brand_size_chart,$pos_manufacturer_brand_id)
{
	$html = '<THEAD id="poc_thead" valign="bottom">
		<tr>
			<th rowspan="' . $brand_size_chart['num_size_rows'] . '" width = "14px"></th>
			<th rowspan="' . $brand_size_chart['num_size_rows'] . '"><b>'.manufacturerUPCLink($pos_manufacturer_brand_id,'STYLE<BR>#').'</B></th>
			<th rowspan="' . $brand_size_chart['num_size_rows'] . '"><b>COLOR<BR>OR SUB<BR>CODE</b></th>
			<th rowspan="' . $brand_size_chart['num_size_rows'] . '"><b>COLOR<BR>DESCRIPTION</b></th>
			<th rowspan="' . $brand_size_chart['num_size_rows'] . '"><b>TITLE</b></th>
			<th rowspan="' . $brand_size_chart['num_size_rows'] . '" ><b><a href="http://www.embrasse-moi.com/POS/add_category.php"> CATEGORY </a></B></th>';
		
			if ($brand_size_chart['bln_cup'] == 1) $html .= '<th rowspan="' . $brand_size_chart['num_size_rows'] . '" width = "20px"><b>CUP<BR>SIZE</b></th>';
			if ($brand_size_chart['bln_inseam'] == 1) $html .= '<th rowspan="' . $brand_size_chart['num_size_rows'] . '" width = "20px"><b>INSEAM<BR>SIZE</b></th>';
			if(isset($brand_size_chart['attributes']) && sizeof($brand_size_chart['attributes'])>0)
			{
				for($atr=0;$atr<sizeof($brand_size_chart['attributes']);$atr++)
				{
				$html .= '<th rowspan="' . $brand_size_chart['num_size_rows'] . '" width = "20px"><b>'.$brand_size_chart['attributes'][$atr].'</b></th>';
				}
			}
			//This is the first size row.....
			if ($brand_size_chart['num_sizes'] == 0)
			{
				$html .= '<th onclick="sizeSelect(this)">One<br>Size</th>';
				//Add a hidden value for the brand size id row
					$pos_manufacturer_brand_size_id = 'null';
					$html .=  '<input type="hidden" name="pos_manufacturer_brand_size_id_r0" value="' . $pos_manufacturer_brand_size_id  . '" />';
					$brand_size_chart['num_sizes']=1;
			}
			else
			{
				foreach ($brand_size_chart['sizes'][0] as $value) 
				{
					$html .= '<th onclick="sizeSelect(this)">' . $value . '</th>';
					
				}
				//Add a hidden value for the brand size id row
					$pos_manufacturer_brand_size_id = $brand_size_chart['pos_manufacturer_brand_size_id'][0];
					$html .=  '<input type="hidden" name="pos_manufacturer_brand_size_id_r0" value="' . $pos_manufacturer_brand_size_id  . '" />';
			}
			//finish the table
			$html .= '
			<th rowspan="' . $brand_size_chart['num_size_rows'] . '" ><b>QTY</b></th>
			<th rowspan="' . $brand_size_chart['num_size_rows'] . '" ><b>COST</b></th>
			<th rowspan="' . $brand_size_chart['num_size_rows'] . '" ><b>RETAIL</b></th>
			<th rowspan="' . $brand_size_chart['num_size_rows'] . '" ><b>TOTAL</b></th>
			<th rowspan="' . $brand_size_chart['num_size_rows'] . '" ><b>COMMENTS</b></th>
		</tr>';
		$size_row_cntr = 1;
		//These are the remaining size rows.....
		for ($i=1;$i<$brand_size_chart['num_size_rows'];$i++)
		{
			$pos_manufacturer_brand_size_id = $brand_size_chart['pos_manufacturer_brand_size_id'][$i];
			$html .=  '<input type="hidden" name="pos_manufacturer_brand_size_id_r' . $size_row_cntr . '" value="' . $pos_manufacturer_brand_size_id  . '" />'."\n";
			$html .= '<tr>';
			foreach ($brand_size_chart['sizes'][$i] as $value) 
			{
				$html .= '<th onclick="sizeSelect(this)">' . $value . '</th>' ."\n";
			}
			$html .= '</tr>';			
		}
	$html .= '	</thead>';
	return $html;
}
function createPRCThead($brand_size_chart)
{
	$html = '<THEAD id="poc_thead" valign="bottom">
		<tr>
			<th rowspan="' . $brand_size_chart['num_size_rows'] . '" width = "14px"></th>
			<th rowspan="' . $brand_size_chart['num_size_rows'] . '"><b>STYLE<BR>#</B></th>
			<th rowspan="' . $brand_size_chart['num_size_rows'] . '"><b>COLOR<BR>OR SUB<BR>CODE</b></th>
			<th rowspan="' . $brand_size_chart['num_size_rows'] . '"><b>COLOR<BR>DESCRIPTION</b></th>';
		
			if ($brand_size_chart['bln_cup'] == 1) $html .= '<th rowspan="' . $brand_size_chart['num_size_rows'] . '" width = "20px"><b>CUP<BR>SIZE</b></th>';
			if ($brand_size_chart['bln_inseam'] == 1) $html .= '<th rowspan="' . $brand_size_chart['num_size_rows'] . '" width = "20px"><b>INSEAM<BR>SIZE</b></th>';
			
			//This is the first size row.....
			if ($brand_size_chart['num_sizes'] == 0)
			{
				$html .= '<th onclick="sizeSelect(this)">One<br>Size</th>';
				//Add a hidden value for the brand size id row
					$pos_manufacturer_brand_size_id = 'null';
					$html .=  '<input type="hidden" name="pos_manufacturer_brand_size_id_r0" value="' . $pos_manufacturer_brand_size_id  . '" />';
					$brand_size_chart['num_sizes']=1;
			}
			else
			{
				foreach ($brand_size_chart['sizes'][0] as $value) 
				{
					$html .= '<th onclick="sizeSelect(this)">' . $value . '</th>';
					
				}
				//Add a hidden value for the brand size id row
					$pos_manufacturer_brand_size_id = $brand_size_chart['pos_manufacturer_brand_size_id'][0];
					$html .=  '<input type="hidden" name="pos_manufacturer_brand_size_id_r0" value="' . $pos_manufacturer_brand_size_id  . '" />';
			}
			//finish the table
			$html .= '
			<th rowspan="' . $brand_size_chart['num_size_rows'] . '" ><b>QTY</b></th>
			<th rowspan="' . $brand_size_chart['num_size_rows'] . '" ><b>COST</b></th>
			<th rowspan="' . $brand_size_chart['num_size_rows'] . '" ><b>TOTAL</b></th>
			<th rowspan="' . $brand_size_chart['num_size_rows'] . '" ><b>COMMENTS</b></th>
		</tr>';
		$size_row_cntr = 1;
		//These are the remaining size rows.....
		for ($i=1;$i<$brand_size_chart['num_size_rows'];$i++)
		{
			$pos_manufacturer_brand_size_id = $brand_size_chart['pos_manufacturer_brand_size_id'][$i];
			$html .=  '<input type="hidden" name="pos_manufacturer_brand_size_id_r' . $size_row_cntr . '" value="' . $pos_manufacturer_brand_size_id  . '" />'."\n";
			$html .= '<tr>';
			foreach ($brand_size_chart['sizes'][$i] as $value) 
			{
				$html .= '<th onclick="sizeSelect(this)">' . $value . '</th>' ."\n";
			}
			$html .= '</tr>';			
		}
	$html .= '	</thead>';
	return $html;
}

function getPurchaseReturnData($pos_purchase_return)
{
	$sql="SELECT * FROM pos_purchase_returns WHERE pos_purchase_return_id=$pos_purchase_return_id";
	return getSQL($sql);
}
function createPRForm($pos_purchase_return_id)
{
	$complete_location = "../ViewPurchaseReturn/view_purchase_return.php?pos_purchase_return_id=".$pos_purchase_return_id;
	$pos_purchase_return = getPurchaseReturnData($pos_purchase_return_id);
	$pos_manufacturer_id = $pos_purchase_return[0]['pos_manufacturer_id'];
	$pos_manufacturer_brand_id = $pos_purchase_return[0]['pos_manufacturer_brand_id'];
	// Get the manufacturer information
	$selected_manufacturer = getManufacturer($pos_manufacturer_id);
	$selected_brand = getBrand($pos_manufacturer_brand_id);
	$brand_code = $selected_brand[0]['brand_code'];
	$brand_size_chart = selectNewOrStoredBrandSizeChartforPR($pos_purchase_return_id);

	if ($brand_size_chart['num_sizes'] == 0)
	{
		$message=urlencode('This Manufacturer does not have a size chart setup, please create one.');
		$referring_page =POS_ENGINE_URL.'/purchase_orders/CreatePurchaseReturn/purchase_return_contents.php?pos_purchase_return_id='. $pos_purchase_order_id;
		$pos_url ='/manufacturers/EditBrandSizeChart/edit_brand_size_chart.php?pos_manufacturer_brand_id='. $pos_manufacturer_brand_id.'&message='.$message.'&referring_page='.$referring_page;
		//pos_redirect($pos_url);
		header('Location: '.POS_ENGINE_URL . $pos_url);
	}
	
	//load any previously created contents
	$tbody_data = loadPurchaseReturnContents($pos_purchase_return_id, $brand_size_chart);

	//set up the columns - 
	$start_columns = 4;
	$footer_colspan = 4;
	if ($brand_size_chart['bln_cup'] == 1) 
	{
		$footer_colspan = $footer_colspan + 1;
		$start_columns = $start_columns + 1;
	}
	if ($brand_size_chart['bln_inseam'] == 1) 
	{
		$footer_colspan = $footer_colspan + 1;
		$start_columns = $start_columns + 1;
	}

	$html = '<link type="text/css" href="../poStyles.css" rel="Stylesheet"/>'.newline();
	$html .=  '<script src="purchase_return_contents.form.js"></script>'.newline();
	
	$form_id = "poc_form";
	$html .=  '<form id = "' . $form_id . '" action="process_purchase_return_contents.php" method="post" onsubmit="return validatePOCForm()">';
	//this is the overview table
	
	$html .= createMiniPROverview($pos_purchase_return_id);
	
	$html .=  '<div class = "poc_table_div">';
	$html .=  '<TABLE id="poc_table" summary="Embrasse-Moi Purchase Order Details">';
	$html .=	createPRCThead($brand_size_chart);
	//this is the body which is created by javascript	
	$html .=  '	<tbody id = "poc_tbody" name = "poc_tbody"></tbody>';
	//And the footer
	$html .=  '
			<tfoot id = "poc_tfoot">
			<tr>
				<td colspan = "' . $footer_colspan . '" id = "emptyCell"></td>
				<td colspan = "' . $brand_size_chart['num_sizes'] . '">Totals:</td>
				<td name =  "poc_total_qty" id = "poc_total_qty">0</td>
				<td id = "emptyCell"></td>
				<td name = "poc_total" id = "poc_total">0</td>
				<td id = "emptyCell"></td>
			</tr>
			</tfoot>';
		$html .=  '</table>';
		$html .=  '</div>';

	$html .=  '<INPUT class = "button" type="button" style="width:60px;" value="Add Row" onclick="addRow(\'poc_tbody\')" />';
	$html .=  '<INPUT class = "button" type="button" style="width:80px;" value="Copy Row(s)" onclick="copyRow(\'poc_tbody\')" />';
	$html .=  '<INPUT class = "button" type="button" value="Move Row(s) Up" onclick="moveRowUp(\'poc_tbody\')" />';
	$html .=  '<INPUT class = "button" type="button" style="width:120px;" value="Move Row(s) Down" onclick="moveRowDown(\'poc_tbody\')" />';
	$html .=  '<INPUT class = "button" type="button" style="width:80px;" value="Delete Row(s)" onclick="deleteRow(\'poc_tbody\')" />';
	
	$html .=  '<INPUT class = "button" type="button"  style="margin: 2px 4px 6px 30px;"value="Save Draft Return" onclick="saveDraft(\'poc_tbody\')" />'.newline();
	$html .=  '<INPUT class = "button" type="button" style="width:120px" value="Exit (Finish Later)" onclick="exit()" />'.newline();
	$html .=  '<INPUT class = "button" type="button" style="width:120px" value="Continue To Return" onclick="saveDraftAndContinue()" />'.newline();

	
	$html .=  '<input class = "rightButton" type="button" name="cancel" style="width:180px;" value="Cancel Changes Since Last Save" onclick="cancelPO()"/>'.newline();
	$html .=  '<INPUT class = "rightButton" type="button" style="width:80px;" value="Destroy PR" onclick="deletePurchaseReturn(\''.$pos_purchase_return_id.'\')" />'.newline();

	//create some hidden input
	$html .= createHiddenSerializedInput('stored_size_chart', $brand_size_chart).newline();
	
	$html .=   '<input type="hidden" name="bln_cup" value="' . $brand_size_chart['bln_cup'] . '" />'.newline();
	$html .=   '<input type="hidden" name="bln_inseam" value="' . $brand_size_chart['bln_inseam'] . '" />'.newline();
	$html .=   '<input type="hidden" name="start_columns" value="' . $start_columns . '" />'.newline();
	$html .=   '<input type="hidden" name="num_sizes" value="' . $brand_size_chart['num_sizes'] . '" />'.newline();
	$html .=   '<input type="hidden" name="pos_purchase_order_id" value="' . $pos_purchase_order_id . '" />'.newline();
	$html .=   '<input type="hidden" name="brand_code" value="' . $brand_code . '" />'.newline();
	
	$html .= '<script>var json_tbody_data = '.  json_encode($tbody_data) .';</script>'.newline();
	$rows_with_system_styles =  json_encode(checkForSystemStyles($tbody_data, $pos_manufacturer_brand_id));
	$html .= '<script> var rows_with_system_styles = '. $rows_with_system_styles .';</script>'.newline();
	$html .= '<script> var size_category_ids = ["' .join("\", \"", $brand_size_chart['size_categories']). '"];</script>'.newline();
	
	$html .= '<script> var pos_manufacturer_id = "'.  $pos_manufacturer_id .'";</script>'.newline();
	$html .= '<script> var pos_manufacturer_brand_id = "'.   $pos_manufacturer_brand_id . '";</script>'.newline();
	$html .= '<script> var formID = "'.   $form_id .'";</script>'.newline();
	$html .= '<script> var num_sizes = "'.   $brand_size_chart['num_sizes'] .'";</script>'.newline();
	$html .= '<script> var bln_cup= "'.   $brand_size_chart['bln_cup'] . '";</script>'.newline();
	$html .= '<script> var bln_inseam= "'.   $brand_size_chart['bln_inseam'] . '";</script>'.newline();
	$html .= '<script> var num_size_rows= "'.   $brand_size_chart['num_size_rows'] . '";</script>'.newline();
	$html .= '<script> var start_columns= "'.   $start_columns . '";</script>'.newline();
	$html .= '<script> var complete_location = "'.$complete_location.'";</script>'.newline();
	$html .=  '</form>';
	
	return $html;
}
function createHTMLPO($pos_purchase_order_id)
{
/*
	*purchase_order_content_overview.form.php
	*Craig Iannazzi 2-2-2012
	*This file will display the overview of the purchase order.
	
*/

	//Retrieve the purchase order information

	$pos_purchase_order_row = getPurchaseOrderOverview($pos_purchase_order_id);
	
	$po_title  = $pos_purchase_order_row[0]['po_title'];
	$delivery_date  = $pos_purchase_order_row[0]['delivery_date'];
	$cancel_date  = $pos_purchase_order_row[0]['cancel_date'];
	$purchase_order_number  = $pos_purchase_order_row[0]['purchase_order_number'];
	$manufacturer_purchase_order_number  = $pos_purchase_order_row[0]['manufacturer_purchase_order_number'];
	$pos_manufacturer_id = $pos_purchase_order_row[0]['pos_manufacturer_id'];
	$pos_manufacturer_brand_id = $pos_purchase_order_row[0]['pos_manufacturer_brand_id'];
	$pos_store_id = $pos_purchase_order_row[0]['pos_store_id'];
	$pos_user_id = $pos_purchase_order_row[0]['pos_user_id'];
	

	// Get the manufacturer information
	$selected_manufacturer = getManufacturer($pos_manufacturer_id);
	$selected_brand = getBrand($pos_manufacturer_brand_id);
	$brand_code = $selected_brand[0]['brand_code'];
	
	// Get the store information
	$shipto_selected_store = getStore($pos_store_id);
	
	// Get the employee generating the PO information
	$selected_employee = getUser($pos_user_id);



	// re-display the info
	//************** this is the table with the manufacture, the PO details, and the store delivery details *********************
	
	$posummary_style = ' style ="width:100%;
			color: rgb(0,0,0);
			padding: 0px 0px 1px 0px;
			margin: 0px;
			border-top: 1px solid black; 
			border-left: 1px solid black;
			border-right: 1px solid black;
			border-bottom: 1px solid black;
			text-align: left;" ';
			
	$html  = '';
	$html = $html . ' <div class = "po_summary_div" style="margin: 0px;padding: 10px 0px 0px 0px;">';
	$html = $html . '<TABLE id = "po_summary" class ="po_summary" ' . $posummary_style . '>';
		$html = $html . '<TR  >';
			$html = $html . '<th style = "border: 0px solid black;
			padding: 0px;
			margin: 0px;"';
			if ($po_title == "") $html = $html . ' class = "error"';
			$html = $html . '> PO Title: </TH><TD style = "border: 0px solid black;
			padding: 0px;
			margin: 0px;">' . $po_title .'</td>';
			$html = $html . '<TH style = "border: 0px solid black;
			padding: 0px;
			margin: 0px;"id = "poc_supplierName"> Brand: </TH> <TD style = "border: 0px solid black;
			padding: 0px;
			margin: 0px;"id = "poc_manufacturer">' . $selected_brand[0]['brand_name'] . '</td>';
			$html = $html . '<TH style = "border: 0px solid black;
			padding: 0px;
			margin: 0px;"id = "poc_ddate"> Delivery Date: </TH> <TD style = "border: 0px solid black;
			padding: 0px;
			margin: 0px;"id = "poc_deliveryDate">' . $delivery_date . '</td>';
			$html = $html . '<TH style = "border: 0px solid black;
			padding: 0px;
			margin: 0px;"id ="poc_shipto"> Ship To: </TH> <TD style = "border: 0px solid black;
			padding: 0px;
			margin: 0px;" >' . $shipto_selected_store[0]['store_name'] . '</td>';
			$html = $html . '<TH style = "border: 0px solid black;
			padding: 0px;
			margin: 0px;" id ="poc_ponumber" ';
			if ($po_title == "") $html = $html . ' class = "error"';
			$html = $html .'> PO Number: </TH> <TD style = "border: 0px solid black;
			padding: 0px;
			margin: 0px;">' . $purchase_order_number . '</td>';
		$html = $html . '</tr>';
	$html = $html . '</table></p>';
	$html = $html . '</div>';
	return $html;
}
function createHTMLBIGPO($pos_purchase_order_id)
{
	$po = getPurchaseOrderData($pos_purchase_order_id);
	
	$brand = getBrand($po['pos_manufacturer_brand_id']);
	$mfg = getManufacturer(getManufacturerIDFromBrandId($po['pos_manufacturer_brand_id']));
	$employee = getUser($po['pos_user_id']);
	$store	 = getStore($po['pos_store_id']);
	

	$po_overview_div_style =' style = "
	padding: 0px;
	margin: 0px;
	" ';
	$po_title_style =' style = "
		border: 1px solid black;
		margin-bottom: 1px;
		font-size: 1.4em;
		padding: 0px 4px 0px 4px;
	" ';
	$po_overview_table_style = ' style = "
		width:100%;
		color: rgb(0,0,0);
		padding: 0px;
		margin: 0px;
		border-top: 1px solid black; 
		border-left: 1px solid black;
		border-right: 1px solid black;
		border-bottom: 1px solid black;
		text-align: left;
	" ';
	$po_overview_table_th_td_style =' style = "
		padding: 0px;
		margin: 0px;
		border-left: 0px solid black;
		border-right: 0px solid black;
	" ';
	$po_overview_mfg_table_and_details_style =' style = "
		width:100%;
		color: rgb(0,0,0);
		padding: 0px;
		margin: 0px;
		border: 0;
		text-align: left;
	" ';
	$po_overview_mfg_detail_th_td=' style = "
		border: 0px solid black;
		padding: 0px;
		margin: 0px;
	" ';

	$html =  '<p><span '. $po_title_style . '>' .$store[0]['company'] . ' Purchase Order Number: <b> ' .$pos_purchase_order_id . ' ' . $po['purchase_order_number'] . '</b> </span></p>';
	$html .=  '<TABLE id = "po_overview_table"' . $po_overview_table_style . '>
  	<TR valign ="top">
   		<TD >
    	<TABLE id = "po_overview_mfg_table">
     		<TR><TH> Supplier Name: </TH>
     		<TD>' . $mfg[0]['company'] .
			'</TD></tr>
     		<TR><TH>Brand Name:</TH>
     		<TD>' . $brand[0]['brand_name'] .'
			</TD></tr>
			  <TR><TH rowspan = "4" valign ="top"> Manufacturer Address: </TH> 
			  <TD> ' . $mfg[0]['address1']. '</TD>
			  <TR><TD>'. $mfg[0]['address2'] . '</TD></TR>
			  <TR><TD>' . $mfg[0]['city'] . '</td></tr>
			  <TR><TD>' .$mfg[0]['country'] . '</td/tr>
			  <TR><TH align = "left"> Phone </TH><TD>' .$mfg[0]['phone'] . '</td/tr>
			  <TR><TH align = "left"> FAX </TH><TD>' .$mfg[0]['fax'] . '</td/tr>
			  <TR><TH align = "left"> Sales Rep. </TH> <TD>' .getSalesRepNameFromPO($pos_purchase_order_id) . '</td></tr>
			  <TR><TH align = "left"> Email </TH> <TD>' .getSalesRepEmailFromPO($pos_purchase_order_id) . '</td></tr>
			  <TR><TH valign ="top" align = "left">Manufacturer Purchase Order Number</TH><TD> '. $po['manufacturer_purchase_order_number'].'</TD></TR>
			  </TD>
			</TR>
      </TABLE>
   </TD>';
   
 
//******************************* this is the purchase order table with PO details -->
$html .=  '<TD>
    		<TABLE id="po_overview_details_table"  >
     			 <TR><TH align = "left">Delivery Date</TH><TD>' . $po['delivery_date'] . '
   				<TR><TH align = "left">Cancel Date</TH><TD>'. $po['cancel_date'] . '
   				<TR><TH valign ="top" align = "left">Purchase Order System ID</TH><TD>' . $pos_purchase_order_id . '</TD></TR>
     			<TR><TH valign ="top" align = "left">Purchase Order Number</TH><TD>' . $po['purchase_order_number'] . '</TD></TR>
   		 </TABLE>
   		</TD>';
   
//**************************** This is the company to ship to (us) ***********************************

	$html .=  
   	'<TD >
    <TABLE BORDER="0">
    	<TR><TH align = "left"><b> SHIP TO </b></TH></TR>
    	<TR><TD>' .$store[0]['company']. '</TD></TR>
		<TR><TD><b> SHIPPING ADDRESSS</b></td></tr>
		<TR><TD> ' . $store[0]['shipping_address1'] . '</TD></TR>
		<TR><TD> ' . $store[0]['shipping_address2'] . ' </TD></TR>
		<TR><TD> ' . $store[0]['shipping_city'] . ', ' . $store[0]['shipping_state'] . ' ' . $store[0]['shipping_zip'] . ' </TD></TR>

		<TR><TD><b> BILLING ADDRESSS</b></td></tr>
		<TR><TD> ' . $store[0]['billing_address1'] . '</TD></TR>
		<TR><TD> ' . $store[0]['billing_address2'] . ' </TD></TR>
		<TR><TD> ' . $store[0]['billing_city'] . ', ' . $store[0]['billing_state'] . ' ' . $store[0]['billing_zip'] . ' </TD></TR>
		<TR><TD> Phone: ' . $store[0]['phone'] . ' </TD></TR>
		<TR><TD> FAX: ' . $store[0]['fax'] . ' </TD></TR>';

//*************************create the ordered by table - this is the person logged into the system *******************
      $html .=  '<TR><TH align = "left"> ORDERED BY </TH></TR>
   				<TR><TD>' .  $employee[0]['first_name'] . ' ' .  $employee[0]['last_name'] .'</TD></TR>
      			<TR><TD>' . $employee[0]['email'] . '</TD></TR>
   			 </TABLE>
  		 </TD>
  	</TR>
	</TABLE>';

	return $html;
}
function createHTMLEmailPOCStatus($pos_purchase_order_id)
{
	//need to create the table with inline styles.
	$table_def_array = createEmailPOUpdateTableArrayDef($pos_purchase_order_id);
	$html = '';
	if(checkForProductSubIds($pos_purchase_order_id))
	{
		$purchase_order_products = getPurchaseOrderContentsAndProductSubids($pos_purchase_order_id);
		$table_def_array_with_data = loadMYSQLArrayIntoTableArray($table_def_array, $purchase_order_products);
		$class = "purchase_order";
		//$html_table = createMYSQLArrayHTMLTable($table_def_array_with_data, $class, 'receive_table');
		$rows_to_highlight_red = checkPOforRecevieErrors($table_def_array_with_data);
		$html .= createEmailHTMLArrayTable($table_def_array_with_data, $class, $rows_to_highlight_red);
	}
	if (getPOCStatusComments($pos_purchase_order_id) != '')
	{
		$title = 'Received Events And Status';
		$title = '';
		$received_notes = getPOCStatusComments($pos_purchase_order_id);
		$html .= 'The following errors have occured during receive event:';
		$html .= textAreaEmailHtmlTable($title, $received_notes);
	}
	return $html;

}
function checkPOforRecevieErrors($table_def_array_with_data)
{
	$ordered_column = getTableArrayColumn($table_def_array_with_data, 'quantity_ordered');
	$received_column = getTableArrayColumn($table_def_array_with_data, 'received_quantity');
	$canceled_column = getTableArrayColumn($table_def_array_with_data, 'quantity_canceled');
	$error_rows = array();
	for($i=0;$i<sizeof($table_def_array_with_data);$i++)
	{
		if ($table_def_array_with_data[$i][$ordered_column]['value'] != $table_def_array_with_data[$i][$received_column]['value'])
		{
			$error_rows[] = $i;
		}
		if ($table_def_array_with_data[$i][$canceled_column]['value'] != 0)
		{
			$error_rows[] = $i;
		}
	}
	return $error_rows;
}
function getTableArrayColumn($table_def_array_with_data, $mysql_result_field)
{
	for($i=0;$i<sizeof($table_def_array_with_data[0]);$i++)
	{
		if ($table_def_array_with_data[0][$i]['mysql_result_field'] == $mysql_result_field)
		{
			return $i;
		}
	}
}
function getPORARequest($pos_purchase_order_id)
{
	$sql = "SELECT ra_required FROM pos_purchase_orders WHERE pos_purchase_order_id = $pos_purchase_order_id";
	return getSingleValueSQL($sql);
}
function getRANumber($pos_purchase_order_id)
{
	$sql = "SELECT ra_number FROM pos_purchase_orders WHERE pos_purchase_order_id = $pos_purchase_order_id";
	return getSingleValueSQL($sql);
}
function getPOCreditMemoRequired($pos_purchase_order_id)
{
	$sql = "SELECT credit_memo_required FROM pos_purchase_orders WHERE pos_purchase_order_id = $pos_purchase_order_id";
	return getSingleValueSQL($sql);
}
function getCreditMemoNumber($pos_purchase_order_id)
{
	$sql = "SELECT credit_memo_invoice_number FROM pos_purchase_orders WHERE pos_purchase_order_id = $pos_purchase_order_id";
	return getSingleValueSQL($sql);
}


function textAreaHtmlTable($title, $text, $table_class = 'textareaTable')
{
	$html = '<table class="'.$table_class.'">';
	if ($title != '')
	{
		$html .='<thead>';
		$html .= '<tr><th>'.$title.'</th></tr>';
		$html .= '</thead>';
	}
	$html .= '<tbody>';
	$html .= '<tr><td>'.nl2br($text).'</td></tr>';
	$html .= '</tbody></table>';
	return $html;
}
function getPOCStatusComments($pos_purchase_order_id)
{
	$sql = "SELECT wrong_items_comments FROM pos_purchase_orders WHERE pos_purchase_order_id = $pos_purchase_order_id";
	return getSingleValueSQL($sql);
}

function createEmailPOUpdateTableArrayDef()
{
		$array_table_def= array(	
					array(
							'th' => 'Manufacturer UPC',
							'mysql_result_field' => 'product_upc',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(
							'th' => 'Style Number',
							'mysql_result_field' => 'style_number',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(
							'th' => 'Color Code',
							'mysql_result_field' => 'color_code',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(
							'th' => 'Color Description',
							'mysql_result_field' => 'color_description',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(	'th' => 'Size',
							'mysql_result_field' => 'size',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(	'th' => 'Quantity Ordered',
							'mysql_result_field' => 'quantity_ordered',
							'type' => 'td',
							'total' => 0,
							'mysql_post_field' => ''),
					array(	'th' => 'Quantity Received',
							'mysql_result_field' => 'received_quantity',
							'type' => 'td',
							'total' => 0,
							'mysql_post_field' => ''),
					array(	'th' => 'Quantity Canceled',
							'mysql_result_field' => 'quantity_canceled',
							'type' => 'td',
							'total' => 0,
							'mysql_post_field' => '')
					/*array(	'th' => 'Quantity Damaged',
							'mysql_result_field' => 'quantity_damaged',
							'type' => 'td',
							'total' => 0,
							'mysql_post_field' => '')*/
					
					);
	return $array_table_def;

}

function createHTMLEmailPOC($pos_purchase_order_id)
{
	/*
*	purchase_order_contents.poc_table.inc.php
*	This file was created to get the table out of my hair so I would stop wanting to throw up
*/
	$html = '';
	$pos_purchase_order_row = getPurchaseOrderOverview($pos_purchase_order_id);
	$pos_manufacturer_brand_id = $pos_purchase_order_row[0]['pos_manufacturer_brand_id'];
	$pos_manufacturer_id = $pos_purchase_order_row[0]['pos_manufacturer_id'];
	
	//load any previously created contents
	$stored_size_data = loadStoredSizeChart($pos_purchase_order_id);
	$tbody_data = loadPurchaseOrderContents($pos_purchase_order_id, $stored_size_data);
	//$json_tbody_data = json_encode($tbody_data);
	
	$brand_size_chart = selectNewOrStoredBrandSizeChart($pos_purchase_order_id);
	$num_sizes = $brand_size_chart['num_sizes'];
	//Get the size category ids - these are used to default a  'bra' product to 'bra' sizing
	$size_category_ids = $brand_size_chart['size_categories'];
	$size_chart = $brand_size_chart['sizes'];
	$num_size_rows = $brand_size_chart['num_size_rows'];
	//Find out if there is a cup or inseam set to Yes
	$bln_cup = $brand_size_chart['bln_cup'];
	$bln_inseam = $brand_size_chart['bln_inseam'];
	$pos_manufacturer_brand_size_ids = $brand_size_chart['pos_manufacturer_brand_size_id'];
	
	if ($num_sizes == 0)
	{
		$html = $html . '<p class = error>This Manufacturer does not have a size chart setup, please create one.</p>';
	}
	//set up the columns - 
	$start_columns = 4;
	$footer_colspan = 4;
	if ($bln_cup == 1) 
	{
		$footer_colspan = $footer_colspan + 1;
		$start_columns = $start_columns + 1;
	}
	if ($bln_inseam == 1) 
	{
		$footer_colspan = $footer_colspan + 1;
		$start_columns = $start_columns + 1;
	}
	if(isset($brand_size_chart['attributes']) && sizeof($brand_size_chart['attributes'])>0)
	{
		for($atr=0;$atr<sizeof($brand_size_chart['attributes']);$atr++)
		{
			$footer_colspan = $footer_colspan + 1;
			$start_columns = $start_columns + 1;
		}
	}
	
	$table_style = ' style ="
		width:100%;
		font-family:verdana,arial,helvetica,sans-serif; 
		padding: 0;
		margin: 0;
    	border-collapse: collapse;" ';

    $thead_style = '';
	$thead_th_style = ' style = "	
		margin: 0;
	   padding: 0;
	   border-left:  1px solid black; 
	   border-right:  1px solid black; 
	   border-top:  1px solid black; 
	   border-bottom:  1px solid black; 
		line-height:10px;
		text-align:center;
		font-size:10px;" ';
	$tbody_style = 'style = "
		border-left:  1px solid black; 
	   border-right:  1px solid black; 
	   border-top:  1px solid  black; 
	   border-bottom:  1px solid black" ';
	$td_style =  '
	style = "border-left:  1px solid black; 
	   border-right:  1px solid black; 
	   border-top:  1px solid black; 
	   border-bottom:  1px solid black; 

		text-align:center;
		vertical-align: middle;
		padding: 0px;
		margin: 0px;
		font-size: 0.8em;" ';
	
	$empty_cell = 	' style = "
		border-top: 0px none black; 
		border-left:  0px none black; 
		border-right:  0px none black; 
		border-bottom:  0px none black;" ';
	$footer_style = ' style="
	   border-left:  1px solid black; 
	   border-right:  1px solid black; 
	   border-top:  1px solid black; 
	   border-bottom:  1px solid black; 

		text-align:center;
		vertical-align: bottom;
		padding: 0px;
		margin: 0px;
		font-size: 0.8em;
		color: rgb(0,0,0);
		font-weight: bold;" ';
		
	$html = $html . '<div class = "poc_table_div" style="	padding: 0;
		margin: 0;
		border :0;">';
	$html = $html . '<TABLE id="poc_table" summary="Embrasse-Moi Purchase Order Details" '.$table_style.' >
	<THEAD id="poc_thead" valign="bottom">
		<tr>
			<th ' . $thead_th_style . ' rowspan="' . $num_size_rows . '"><b>STYLE<BR>#</B></th>
			<th ' . $thead_th_style . '  rowspan="' . $num_size_rows . '"><b>COLOR<BR>OR SUB<BR>CODE</b></th>
			<th ' . $thead_th_style . '  rowspan="' . $num_size_rows . '"><b>COLOR<BR>DESCRIPTION</b></th>
			<th ' . $thead_th_style . '  rowspan="' . $num_size_rows . '"><b>TITLE</b></th>';
		
			if ($bln_cup == 1) $html = $html . '<th ' . $thead_th_style . '  rowspan="' . $num_size_rows . '" width = "20px"><b>CUP<BR>SIZE</b></th>';
			if ($bln_inseam == 1) $html = $html . '<th ' . $thead_th_style . '   rowspan="' . $num_size_rows . '" width = "20px"><b>INSEAM<BR>SIZE</b></th>';
			if(isset($brand_size_chart['attributes']) && sizeof($brand_size_chart['attributes'])>0)
			{
			for($atr=0;$atr<sizeof($brand_size_chart['attributes']);$atr++)
	{
		 $html = $html . '<th ' . $thead_th_style . '   rowspan="' . $num_size_rows . '" width = "20px"><b>'.$brand_size_chart['attributes'][$atr] .'</b></th>';
	}
	}
			//This is the first size row.....
			if ($num_sizes == 0)
			{
				$html = $html . '<th ' . $thead_th_style . '  >One<br>Size</th>';
				//Add a hidden value for the brand size id row
					$pos_manufacturer_brand_size_id = 'null';
					$num_sizes=1;
			}
			else
			{
				foreach ($size_chart[0] as $value) 
				{
					$html = $html . '<th ' . $thead_th_style . '  >' . $value . '</th>';
					
				}
				//Add a hidden value for the brand size id row
					$pos_manufacturer_brand_size_id = $pos_manufacturer_brand_size_ids[0];
			}
			//finish the table
			$html = $html . '
			<th ' . $thead_th_style . '  rowspan="' . $num_size_rows . '" ><b>QTY</b></th>
			<th ' . $thead_th_style . '  rowspan="' . $num_size_rows . '" ><b>COST</b></th>
			<th ' . $thead_th_style . '  rowspan="' . $num_size_rows . '" ><b>TOTAL</b></th>';
			//<th ' . $thead_th_style . '   rowspan="' . $num_size_rows . '" ><b>COMMENTS</b></th>
		$html .= '</tr>';
		$size_row_cntr = 1;
		//These are the remaining size rows.....
		for ($i=1;$i<$num_size_rows;$i++)
		{
			$pos_manufacturer_brand_size_id = $pos_manufacturer_brand_size_ids[$i];
			$html = $html . '<tr>';
			foreach ($size_chart[$i] as $value) 
			{
				$html = $html . '<th ' . $thead_th_style . '  >' . $value . '</th>' ."\n";
			}
			$html = $html . '</tr>'.newline();			
		}
	$html = $html . '	</thead>'.newline();

	$html = $html . '	<tbody id = "poc_tbody" name = "poc_tbody"  ' .$tbody_style. '>'.newline();
	$categories = getCategoryAssociativeArray();

		
	for ($body_row = 0;$body_row < sizeof($tbody_data); $body_row++)
	{
		//blast through tbody data and shove it into readonly inputs
		$html = $html . '<tr>'.newline();
		
		$col=1;
		//style
		$html = $html . '<td ' .$td_style. ' >' . $tbody_data[$body_row][$col]. '</td>'.newline();$col++;
		//colorcode
		$html = $html . '<td ' .$td_style. ' >' .$tbody_data[$body_row][$col] . '</td>'.newline();$col++;
		//colorDescription
		$html = $html . '<td ' .$td_style. ' >' .$tbody_data[$body_row][$col] . '</td>'.newline();$col++;
		//title
		$html = $html . '<td ' .$td_style. ' >' . $tbody_data[$body_row][$col] . '</td>'.newline();$col++;
		//category
		$col++;
		//cup and inseam
		if ($bln_cup == 1)
		{
			$html = $html . '<td ' .$td_style. ' >' . $tbody_data[$body_row][$col] . '</td>'.newline();$col++;
		}
		if ($bln_inseam == 1)
		{
			$html = $html . '<td ' .$td_style. ' >' . $tbody_data[$body_row][$col] . '</td>'.newline();$col++;
		}
		if(isset($brand_size_chart['attributes']) && sizeof($brand_size_chart['attributes'])>0)
		{
			for($atr=0;$atr<sizeof($brand_size_chart['attributes']);$atr++)
			{
				$html = $html . '<td ' .$td_style. ' >' . $tbody_data[$body_row][$col] . '</td>'.newline();$col++;
			}
		}
		for($sz=$start_columns;$sz < $num_sizes+$start_columns;$sz++)
		{
			$html = $html . '<td ' .$td_style. ' >' . $tbody_data[$body_row][$col] . '</td>'.newline();$col++;
		}
		//qty
		$html = $html . '<td ' .$td_style. ' >' . sumPurchaseOrderQuantityRow($pos_purchase_order_id, $body_row) . '</td>'.newline();$col++;
		//cost
		$html = $html . '<td ' .$td_style. ' >' . $tbody_data[$body_row][$col] . '</td>'."\n";$col++;
		//retail
		$col++;
		//total
		$html = $html . '<td ' .$td_style. ' >' . sumPurchaseOrderRow($pos_purchase_order_id, $body_row) . '</td>'."\n";$col++;
		//remarks
		//$html = $html . '<td ' .$td_style. ' >' . str_replace("\\\\\\\\n", "<br>", $tbody_data[$body_row][$col]). '</td>';$col++;
		$col++;
		$html = $html . '</tr>';	
	}
	
	
	$html = $html . '			</tbody>';

	
		
	$html = $html . '
		<tfoot id = "poc_tfoot">';
		$html .= '<tr>
			<td colspan = "' . $footer_colspan . '" id = "emptyCell" '.$empty_cell.'></td>
			<td colspan = "' . $num_sizes . '" '.$footer_style.'>Totals:</td>
			<td name =  "poc_total_qty" '.$footer_style.' id = "poc_total_qty">' . sumPurchaseOrderQuantity($pos_purchase_order_id) . '</td>
			<td id = "emptyCell" '.$empty_cell.'></td>
			<td name = "poc_total" '.$footer_style.' id = "poc_total">' . sumPurchaseOrder($pos_purchase_order_id) . '</td>';
			//<td id = "emptyCell" '.$empty_cell.'></td>
		$html .= '</tr>
		</tfoot>';
	$html = $html . '</table>';
	$html = $html . '</div>';



	return $html;
	
}
function getPOInvoiceStatusOptions()
{
	$sql = "SELECT COLUMN_TYPE
			FROM information_schema.columns
WHERE TABLE_NAME = 'pos_purchase_orders'
AND COLUMN_NAME = 'invoice_status'
LIMIT 1
";
	$r_array = getSQL($sql);
	$result = str_replace(array("enum('", "')", "''"), array('', '', "'"), $r_array[0]['COLUMN_TYPE']);
	$arr = explode("','", $result);
	return $arr;
}
function createPOInvoiceStatusSelect($name, $option, $option_all ='off')
{
	
	$status_options = getPOInvoiceStatusOptions();

	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= ' onchange="needToConfirm=true" ';
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Account Type</option>';
	//add an option for all accounts
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($option == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Status</option>';
	}
	for($i = 0;$i < sizeof($status_options); $i++)
	{
		$html .= '<option value="' . $status_options[$i] . '"';
		if ( ($status_options[$i] == $option) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $status_options[$i] . '</option>';
	}
	$html .= '</select>';
	return $html;
}
/************************** SAVING *****************************************/
function saveDraftOrder($pos_purchase_order_id)
{
	$post_data = loadPOCPostData();
	//preprint($post_data);
	updatePO($pos_purchase_order_id, $post_data);
	$poc_to_write = prepare_purchase_order_contents_for_write($pos_purchase_order_id, $post_data['stored_size_chart'], $post_data['tbody_data']);
	$html = write_purchase_order_contents($pos_purchase_order_id, $poc_to_write);
	if(getPurchaseOrderStatus($pos_purchase_order_id)=='INIT')
	{
		setPOStatus($pos_purchase_order_id, 'DRAFT');
	}
	return $html;
}
function updatePO($pos_purchase_order_id, $post_data)
{
	$contents  = array('po_title' =>  $post_data['po_title'],
						'stored_size_chart' => json_encode($post_data['stored_size_chart']));
	
	$dbc = openPOSDatabase();
	$db_fields = array_keys($contents);
	$str_fields = implode(',', $db_fields);
	$key_value_array = array();
	foreach($db_fields as $field)
	{
		$key_value_array[] = $field . " = '" .$contents[$field] ."'";
    }
    $sql_set_string  =  implode(',',$key_value_array);
    
	$po_update_q = "UPDATE pos_purchase_orders SET " . $sql_set_string . " WHERE pos_purchase_order_id=$pos_purchase_order_id LIMIT 1";
	$debug_level2[]  =  '<p>' . $po_update_q . '</p>';
	$po_update_r = @mysqli_query ($dbc, $po_update_q); // Run the query.
	if (!$po_update_r) 
	 //PO Overview did not update
	{ 
		// Debugging message:
		trigger_error(  '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $po_update_q . '</p>');
	}	
	mysqli_close($dbc);
}
function prepare_purchase_order_contents_for_write($pos_purchase_order_id,$stored_size_chart, $tbody_data)
{
	$col = 0;
	$checkbox_col = $col;$col ++;
	$style_col = $col;$col ++;
	$color_col = $col;$col ++;
	$color_description_col = $col;$col ++;
	$title_col = $col;$col ++;
	$category_col = $col;$col ++;
	//Is there a cup or inseam col?
	if ($stored_size_chart['bln_cup'] == 1)
	{
		$cup_col = $col;$col ++;
	}
	if ($stored_size_chart['bln_inseam'] == 1)
	{
		$inseam_col = $col;$col ++;
	}
	if(isset($stored_size_chart['attributes']) && sizeof($stored_size_chart['attributes'])>0)
	{
		$atr_col = array();
		for($atr=0;$atr<sizeof($stored_size_chart['attributes']);$atr++)
		{
			$atr_col[] = $col;$col ++;
		}
	}
	$size_col = array();
	for($sz=0;$sz<$stored_size_chart['num_sizes'];$sz++)
	{
		$size_col[$sz] = $col;
		$col++;
	}
	$qty_col = $col;
	$col++;
	$cost_col = $col;
	$col++;
	$retail_col = $col;
	$col++;
	$total_col = $col;
	$col++;
	$comments_col = $col;$col++;
	$size_row_col =$col;
	


	//Write the order to POC and the Products AND the Attributes - wrong... want the posted data...
	$poc_to_write = array();
	//Go through line by line the purchase order contents
	$poc_counter = 0;
	for($i=0;$i<sizeof($tbody_data);$i++)
	{				
		$style_number =  $tbody_data[$i][$style_col];
		$title = str_replace("\\","", $tbody_data[$i][$title_col]);
		//echo $title;
		$category = $tbody_data[$i][$category_col];
		$cost = $tbody_data[$i][$cost_col];
		$retail = $tbody_data[$i][$retail_col];
		$color_code = $tbody_data[$i][$color_col];
		$color_description = $tbody_data[$i][$color_description_col];
		$comments = $tbody_data[$i][$comments_col];
		// Now go through each size quantity
		$bln_qty_entered = false;
		//$attributes = array();
		if(isset($stored_size_chart['attributes']) && sizeof($stored_size_chart['attributes'])>0)
		{
			for($atr=0;$atr<sizeof($atr_col);$atr++)
			{
				$attributes[] = $tbody_data[$i][$atr_col[$atr]];
			}
			$attributes = implode('::',$attributes);
		}
		if (!isset($attributes)) $attributes = '';
		for($sz=0;$sz<$stored_size_chart['num_sizes'];$sz++)
		{
			$cup_size_append = '';
			$inseam_size_append = '';
			
			//if there is no qty entered then do not add the line item to the purchase order contents.
			if ($tbody_data[$i][$size_col[$sz]] != '')
			{
				$bln_qty_entered = true;
				//Get the cup and inseam values
				if ($stored_size_chart['bln_cup'] == 1)
				{
					$cup_size_append = $tbody_data[$i][$cup_col];
				}
				if ($stored_size_chart['bln_inseam'] == 1)
				{
					$inseam_size_append = $tbody_data[$i][$inseam_col];
				}
				//here is the size
				//$size = trim($stored_size_chart['sizes'][$size_data[$i]][$sz]) . trim($cup_size_append) . trim($inseam_size_append);
				if ($tbody_data[$i][$size_row_col] == 'undefined' || $tbody_data[$i][$size_row_col] == '')
				{
					//size is undefined, so just use the top line.
					
					$size = trim($stored_size_chart['sizes'][0][$sz]);
					//or
					//$size = 'undefined'.$tbody_data[$i][$size_row_col];
				}
				else
				{
					
					
					$size = trim($stored_size_chart['sizes'][$tbody_data[$i][$size_row_col]][$sz]);
				}
				
				// and the quantity
				$quantity_ordered = $tbody_data[$i][$size_col[$sz]];
				$poc_to_write[$poc_counter] = array(
							'pos_purchase_order_id' => $pos_purchase_order_id,
							'poc_row_number' => $i,
							'size_column' => $sz,
							'style_number' => $tbody_data[$i][$style_col],
							'color_code' => $tbody_data[$i][$color_col],
							'color_description' => $tbody_data[$i][$color_description_col],
							'title' => scrubInput(str_replace("\\","", $tbody_data[$i][$title_col])),
							'pos_category_id' => $tbody_data[$i][$category_col],				
							'cup' => $cup_size_append,
							'inseam' => $inseam_size_append,
							'attributes' => $attributes,
							'size' =>	$size,
							'quantity_ordered' => $tbody_data[$i][$size_col[$sz]],
							'cost' => $tbody_data[$i][$cost_col],
							'retail' => $tbody_data[$i][$retail_col],
							'comments' => $tbody_data[$i][$comments_col],
							'size_row' => $tbody_data[$i][$size_row_col]);
				$poc_counter++;
				
			}//this is the end of checking if a size has been entered	
		}//This is the end of the sizes	
		//in case no qty's are ordered we need to write the line details...
		if ($bln_qty_entered == false)
		{
			$poc_to_write[$poc_counter] = array(
							'pos_purchase_order_id' => $pos_purchase_order_id,
							'poc_row_number' => $i,
							'size_column' => '',
							'style_number' => $tbody_data[$i][$style_col],
							'color_code' => $tbody_data[$i][$color_col],
							'color_description' => $tbody_data[$i][$color_description_col],
							'title' => scrubInput(str_replace("\\","", $tbody_data[$i][$title_col])),
							'cup' => '',
							'inseam' => '',
							'size' => '',
							'attributes' => '',
							'quantity_ordered' => '',
							'pos_category_id' => $tbody_data[$i][$category_col],
							'cost' => $tbody_data[$i][$cost_col],
							'retail' => $tbody_data[$i][$retail_col],
							'comments' => $tbody_data[$i][$comments_col],
							'size_row' => $tbody_data[$i][$size_row_col]);
			$poc_counter++;
		}
	}//End of $i each POC row
	return $poc_to_write;

}
function write_purchase_order_contents_preserving_values($pos_purchase_order_id, $contents)
{
	//get the contents
	$current_contents = getPurchaseOrderContentsInArray($pos_purchase_order_id);
	preprint($contents);
	preprint($current_contents);
	$db_fields = array_keys($current_contents[0]);
	//delete the contents
	//$poc_delet_q = "DELETE FROM pos_purchase_order_contents WHERE pos_purchase_order_id = '$pos_purchase_order_id'";
	//$result = runSQL($poc_delet_q);
	//update new contents array with some old values if they are not there
	for($j=0;$j<sizeof($contents);$j++)
	{
		foreach($db_fields as $field)
		{
			if (!isset($contents[$field]) && $field != 'pos_purchase_order_content_id')
			{
				$contents[$j][$field] = '';
			}	
    	}
		for($i=0;$i<sizeof($current_contents);$i++)
		{
			if($contents[$j]['size'] == $current_contents[$i]['size'] && $contents[$j]['style_number'] == $current_contents[$i]['style_number'] && $contents[$j]['style_number'] == $current_contents[$i]['style_number'])
			{
				//its a match, so copy it....
				foreach($db_fields as $field)
				{
					if (!isset($contents[$field]) && $field != 'pos_purchase_order_id')
					{
						$contents[$j][$field] = $current_contents[$i][$field];	
					}
    			}
			}
		}
	}
	//finally re-check that all fields are there, some may be missed if the contents are new.
	for($j=0;$j<sizeof($contents);$j++)
	{
		foreach($db_fields as $field)
		{
			if (!isset($contents[$field]) && $field != 'pos_purchase_order_content_id')
			{
				$contents[$j][$field] = '';
			}	
    	}
    }
    $html = write_purchase_order_contents($pos_purchase_order_id, $contents);
    return $html;
    	
}
function write_purchase_order_contents($pos_purchase_order_id, $contents)
{
	$dbc = openPOSDatabase();
	//Need to clean out the contents for the purchase order
	$poc_delet_q = "DELETE FROM pos_purchase_order_contents WHERE pos_purchase_order_id = '$pos_purchase_order_id'";
	$poc_delet_r = @mysqli_query ($dbc, $poc_delet_q);
	// contents are a key->value pair array that should precisely match the db.
	// when writing purchase order contents, i believe all of the contents for a purchase order id need to be first removed. Then add the new id's. There is no other way to create a unique index to allow updating or inserting the data
	//get the keys
	$sql = array(); 
	$db_fields = array_keys($contents[0]);
	$str_fields = implode(',', $db_fields);
	foreach( $contents as $row ) 
	{	
		$row_array = array();
		foreach($db_fields as $field)
		{
			$row_array[] = "'" . $row[$field] . "'";	
    	}
    	$row_string =  implode(',',$row_array);
    	$sql[] = '(' . $row_string .')';
	}

	
	//for a new purchase order this is an insert.
	//for an edit we need to first delete the original contents, then insert the new contents.
	
	$poc_insert_q = "INSERT INTO pos_purchase_order_contents (" . $str_fields . ") VALUES  " . implode(',', $sql);
	$poc_insert_r = @mysqli_query ($dbc, $poc_insert_q);
	if ($poc_insert_r)
	{
		return  'STORED';	
		
	}
	else
	{
		trigger_error(  '<p>' . mysqli_error($dbc) . '<br /><br />Query: ' . $poc_insert_q . '</p>');
	}
	mysqli_close($dbc);
}
function loadPOCPostData()
{

	$tbody_name = "poc_tbody";
	//preprint($_POST);
	//loop through POST DATA to grab all data from the table body id defined via javascript
	foreach($_POST as $key => $value)
	{
		//looking for $tbody_name
		
		$pos = strpos($key,$tbody_name);
		if ($pos !== false)
		{
			$rc = substr($key,strlen($tbody_name),strlen($key));
			$rpos = stripos($rc,'r');
			$cpos = stripos($rc,'c');
			$r = substr($rc,$rpos+1,$cpos-1);
			$c = substr($rc,$cpos+1,strlen($rc));
		}
	}	
	$num_rows = 0;
	if (isset($r))
	{
		//There are table rows so get the data into an array
		$num_rows = $r + 1;
		$num_cols = $c + 1;
		$tBody_data = array();
		for($r=0;$r<$num_rows;$r++)
		{
			for($c=0;$c<$num_cols;$c++)
			{
				//an unchecked checkbox will not post, so if there is a null value, it is the stupos_product_id checkbox
				if (isset($_POST[$tbody_name . 'r' . $r . 'c' . $c]) ) 
				{
					$tBody_data[$r][$c] = scrubInput($_POST[$tbody_name . 'r' . $r . 'c' . $c]);
				}
				else
				{
					$tBody_data[$r][$c] = 'off';
				}
			}
			//lets get the sizing information here - I dont think there should be any issues with the data "missing" - yes there are issues.....
			if (isset($_POST['style_size_chart_r' . $r]))
			{
				$size_data[$r] = $_POST['style_size_chart_r' . $r];
				$tBody_data[$r][$c] = $_POST['style_size_chart_r' . $r];
			}
			else
			{
				//the size data is missing... we shouldn't care as this is saving a draft. javascript needs to make sure there are no missing size charts for proper processing
				//set the size chart to the first row (0)
				//$size_data[$r] = 'undefined';
				//$tBody_data[$r][$c] = $_POST['style_size_chart_r' . $r]
			}

		}	
	}
	
	$post_data['size_data'] =$size_data;
	//Set up the column information so we can process the post data correctly
	//We can access the correct infomation then like this:  $tBody_data[$row][$style_col]
	$post_data['start_columns'] = $_POST['start_columns'];
	$post_data['num_sizes'] = $_POST['num_sizes'];
	$post_data['bln_cup'] = $_POST['bln_cup'];
	$post_data['bln_inseam'] = $_POST['bln_inseam'];
	$post_data['brand_code'] = $_POST['brand_code'];
	$post_data['pos_manufacturer_brand_id'] = $_POST['pos_manufacturer_brand_id'];
	
	$post_data['tbody_data'] = $tBody_data;
$post_data['stored_size_chart'] = unserialize(stripslashes(htmlspecialchars_decode($_POST['stored_size_chart'])));
	
	$post_data['po_grand_total'] = scrubInput($_POST['po_grand_total']);
	$post_data['po_title'] = scrubInput($_POST['po_title']);
	
	return $post_data;
}
/************************** Products ***************************************/
function writePurchaseOrderContentValue($purchase_order_content_id, $db_field, $value)
{
	
	$update_data = array($db_field => $value);
	//var_dump( $update_data);
	$id['pos_purchase_order_content_id']=$purchase_order_content_id;
	$result = simpleUpdateSQL('pos_purchase_order_contents',  $id, $update_data);
	return $result;
}
function createPOTableDef()
{

if (checkIfUserIsAdmin($_SESSION['pos_user_id']))
{
	//enable certain select boxes
	$mfg_select = createManufacturerBrandSelect('pos_manufacturer_brand_id', 'false', 'off', 'onchange="setPurchaseOrderNumber();needToConfirm=true;"  ');
}
else
{
	//disable certain select boxes
	$mfg_select = createManufacturerBrandSelect('pos_manufacturer_brand_id', 'false', 'off', 'onchange="setPurchaseOrderNumber();needToConfirm=true;" disabled="disabled" ');
}
		$po_date_change_events = ' onchange="changeDate(\'delivery_date\', \'cancel_date\', \'30\');setPurchaseOrderNumber();needToConfirm=true;"';
	$po_creation_basics = array( 
							array( 'db_field' => 'pos_purchase_order_id',
								'type' => 'input',
								'caption' => 'System ID',
								'tags' => ' readonly="readonly" '
									),
							array('db_field' => 'pos_manufacturer_brand_id',
									'caption' => 'Brand Name',
									'type' => 'select',
									'html' => $mfg_select,
									'validate' => array('select_value' => 'false')),
							/*array( 'db_field' => 'pos_manufacturer_brand_id',
								'type' => 'input',
								'caption' => 'Brand Name',
								'tags' => ' readonly="readonly" '
									),		*/
							array( 'db_field' => 'po_title',
									'type' => 'input',
									'caption' => 'Purchase Order Title',
									),
					
							array('db_field' => 'purchase_order_status',
									'type' => 'select',
									'html' => disableSelect(createPOStatusSelect('purchase_order_status', 'INIT', 'off', 'onchange="needToConfirm=true" '))
									),
							array('db_field' => 'ordered_status',
									'type' => 'select',
									'html' => disableSelect(createPOOrderedStatusSelect('ordered_status', 'NOT SUBMITTED', 'off', 'onchange="needToConfirm=true" ')),
									'value' => 'NOT SUBMITTED',
									'tags' => 'readonly="readonly"'),
							array('db_field' => 'received_status',
									'type' => 'input',
									'tags' => 'readonly="readonly"'),
							array( 'db_field' => 'pos_store_id',
									'type' => 'select',
									'caption' => 'Ship To',
									'html' => createShipToStoreSelect('pos_store_id', 'false'),
									'value' => $_SESSION['store_id'],
									'validate' => array('select_value' => 'false')),		
							array( 'db_field' => 'pos_category_id',
									'type' => 'select',
									'caption' => 'Primary Category',
									'html' => createCategorySelect('pos_category_id', 'false'),
									'validate' => array('select_value' => 'false')),
							array('db_field' =>  'delivery_date',
									'type' => 'date',
									'tags' => $po_date_change_events,
									'value' => '',
									'validate' => 'date'),
							array('db_field' =>  'cancel_date',
									'type' => 'date',
									'tags' => 'onchange="needToConfirm=true;"',
									'value' => '',
									'validate' => 'date'),
							array('db_field' => 'purchase_order_number',
									'type' => 'input',
									'validate' => 'none'),
							array('db_field' => 'comments',
									'caption' => 'Comments',
									'type' => 'input',
									'validate' => 'none'),
							array('db_field' => 'invoice_status',
									'caption' => 'Invoice Status',
									'type' => 'select',
									'html' => createPOInvoiceStatusSelect('invoice_status', 'false'),
									'validate' => 'none')
							);
							
	
	//$table_def = $po_creation_basics;
	$table_def = array(array($po_creation_basics));
	return $table_def;
}

function generatePOPreparedEnableTag($pos_purchase_order_id)
{
	if(getPurchaseOrderStatus($pos_purchase_order_id) == 'PREPARED')
	{
		$tags = ' disabled = "disabled" ';
	}
	else
	{
		$tags = '';
	}
	return $tags;
}
function generatePOOpenEnableTag($pos_purchase_order_id)
{
	if(!checkForProductSubIds($pos_purchase_order_id) && getPurchaseOrderStatus($pos_purchase_order_id) != 'PREPARED')
	{
		$tags = ' disabled = "disabled" ';
	}
	else
	{
		$tags = '';
	}
	return $tags;
}

function generateManualSendEnableTags($pos_purchase_order_id)
{
	//send is enabled if the po is prepared
	if(getPurchaseOrderStatus($pos_purchase_order_id) == 'PREPARED')
	{
		$tags = '';
	}
	else
	{
		
		$tags = ' disabled = "disabled" ';
	}
	return $tags;
	
}
function generateEmailSendEnableTags($pos_purchase_order_id)
{
	//send is enabled if the po is prepared
	if(getPurchaseOrderStatus($pos_purchase_order_id) == 'PREPARED' || getPurchaseOrderStatus($pos_purchase_order_id) == 'OPEN')
	{
		$tags = '';
	}
	else
	{
		
		$tags = ' disabled = "disabled" ';
	}
	return $tags;
	
}
function generatePOCopyEnableTags($pos_purchase_order_id)
{
	//send is enabled if the po is prepared
	if(getPurchaseOrderStatus($pos_purchase_order_id) != 'INIT' && getPurchaseOrderStatus($pos_purchase_order_id) != 'DRAFT')
	{
		$tags = '';
	}
	else
	{
		
		$tags = ' disabled = "disabled" ';
	}
	return $tags;
	
}
function generateReceiveEnableTag($pos_purchase_order_id)
{
	$tags = '';

	if(getPurchaseOrderStatus($pos_purchase_order_id) != 'OPEN' || getPurchaseOrderReceivedStatus($pos_purchase_order_id) == 'COMPLETE')
	{
		$tags = ' disabled = "disabled" ';
	}
	return $tags;
}

function generatePOCPre($pos_purchase_order_id)
{
	if (getPurchaseOrderStatus($pos_purchase_order_id) == 'INIT' || getPurchaseOrderStatus($pos_purchase_order_id) == 'DRAFT' /*|| getPurchaseOrderStatus($pos_purchase_order_id) == 'PREPARED'*/)
	{
		$tags = '';
	}
	else
	{
		$tags = ' disabled = "disabled" ';
	}
	return $tags;
}
function getWrongItems($pos_purchase_order_id)
{
	$sql = "SELECT wrong_items_qty FROM pos_purchase_orders WHERE pos_purchase_order_id=$pos_purchase_order_id";
	return getSingleValueSQL($sql);
}
function getWrongItemsComments($pos_purchase_order_id)
{
	$sql = "SELECT wrong_items_comments FROM pos_purchase_orders WHERE pos_purchase_order_id=$pos_purchase_order_id";
	return getSingleValueSQL($sql);
}
function createRecievedStatus($dbc, $pos_purchase_order_id)
{
	/* if the qty ordered = qty recevied + qty canceled for all items then received status is complete
		if qty_damaged + qty received + qty canceled = qty ordered then received status in damaged
	*/
	//Assume fully complete - change if not...
	$incomplete_status = '';
	$extra_items_status='';
	$damaged_status = '';
	$canceled_status = '';
	$status_array = array();
	$sql = "SELECT pos_product_sub_id, quantity_ordered, (SELECT sum(received_quantity) FROM pos_purchase_order_receive_contents WHERE pos_purchase_order_receive_contents.pos_purchase_order_content_id = pos_purchase_order_contents.pos_purchase_order_content_id) as received_quantity, quantity_damaged, quantity_canceled FROM pos_purchase_order_contents WHERE pos_purchase_order_id = '$pos_purchase_order_id'";
	$poc_data = getTransactionSQL($dbc, $sql);
	for ($i=0;$i<sizeof($poc_data);$i++)
	{
		if ($poc_data[$i]['quantity_ordered'] - $poc_data[$i]['quantity_canceled'] > $poc_data[$i]['received_quantity'])
		{
			$status[$i] = 'INCOMPLETE';
			$incomplete_status = 'INCOMPLETE';
		}
		if ($poc_data[$i]['quantity_ordered'] - $poc_data[$i]['quantity_canceled'] < $poc_data[$i]['received_quantity'])
		{
			$status[$i] = 'EXTRA ITEMS';
			$extra_items_status = 'EXTRA ITEMS';
		}
		if ($poc_data[$i]['quantity_damaged'] > 0 && $poc_data[$i]['received_quantity'] + $poc_data[$i]['quantity_canceled'] + getPurchaseReturnOrderReturnedItems($dbc, $pos_purchase_order_id, $poc_data[$i]['pos_product_sub_id']) < $poc_data[$i]['quantity_ordered'])
		{
			$status[$i] = 'DAMAGED ITEMS';
			$damaged_status = 'DAMAGED ITEMS';
		}
		if ( $poc_data[$i]['quantity_canceled'] > 0)
		{
			$canceled_status = 'CANCELED ITEMS';
		}
	}
	$sql = "SELECT wrong_items_qty FROM pos_purchase_orders WHERE pos_purchase_order_id=$pos_purchase_order_id";
	$wiq_data = getTransactionSQL($dbc, $sql);
	if($wiq_data[0]['wrong_items_qty']>0)
	{
		$wrong_items = 'WRONG ITEMS';
	}
	else
	{
		$wrong_items = '';
	}
	if($incomplete_status !='') $status_array[] = 'INCOMPLETE';
	if($extra_items_status !='') $status_array[] = 'EXTRA ITEMS';
	if($damaged_status !='') $status_array[] = 'DAMAGED ITEMS';
	if($wrong_items !='') $status_array[] = 'WRONG ITEMS';
	
	$status = implode(', ', $status_array);
	
	
	
	if ($status == '')
	{
		$received_status = 'COMPLETE';

	}
	else
	{
		if($canceled_status !='') $status_array[] = 'CANCELED ITEMS';
		$status = implode(', ', $status_array);
		$received_status = $status;

	}
	$result = updatePOReceivedStatus($dbc, $pos_purchase_order_id, $received_status);
	return $result;
}
function createPurchaseOrderRecordTable($pos_purchase_order_id)
{
	$tmp_sql = "
	
			CREATE TEMPORARY TABLE invoices

			SELECT pos_purchases_journal.pos_purchases_journal_id, pos_purchases_invoice_to_po.applied_amount, 
			invoice_number, invoice_status, payment_status, invoice_amount, invoice_type
			FROM pos_purchases_journal
			LEFT JOIN pos_purchases_invoice_to_po USING (pos_purchases_journal_id)
			WHERE pos_purchases_invoice_to_po.pos_purchase_order_id = $pos_purchase_order_id
			UNION
			SELECT pos_purchases_journal.pos_purchases_journal_id, pos_purchases_credit_memo_to_po.applied_amount, 
			invoice_number, invoice_status, payment_status, -invoice_amount, invoice_type
			FROM pos_purchases_journal
			LEFT JOIN pos_purchases_credit_memo_to_po USING (pos_purchases_journal_id)
			WHERE pos_purchases_credit_memo_to_po.pos_purchase_order_id = $pos_purchase_order_id
			
			;";
				
	$tmp_select_sql = "SELECT * FROM invoices WHERE 1";			
		$table_columns = array(
		array(
			'th' => '',
			'mysql_field' => 'pos_purchases_journal_id',
			'get_url_link' => POS_ENGINE_URL . "/accounting/PurchaseJournal/view_purchase_invoice_to_journal.php",
			'url_caption' => 'view',
			'get_id_link' => 'pos_purchases_journal_id'),
		array(
			'th' => 'System ID',
			'mysql_field' => 'pos_purchases_journal_id'),
		array(
			'th' => 'Invoice Number',
			'mysql_field' => 'invoice_number',
			'sort' => 'invoice_number'),
		array(
			'th' => 'Invoice Type',
			'mysql_field' => 'invoice_type',
			'sort' => 'invoice_type'),
		array(
			'th' => 'Invoice Status',
			'mysql_field' => 'invoice_status',
			'sort' => 'invoice_status'),	
		array(
			'th' => 'Payment Status',
			'mysql_field' => 'payment_status',
			'sort' => 'payment_status'),
		array(
			'th' => 'Amount Applied',
			'mysql_field' => 'applied_amount',
			'sort' => 'applied_amount',
			'round' => 2,
			'total' => 2),
		array(
			'th' => 'Invoice Amount',
			'mysql_field' => 'invoice_amount',
			'sort' => 'invoice_amount',
			'round' => 2,
			'total' => 2)
		
		);
		
		
	$dbc = openPOSdb();
	$result = runTransactionSQL($dbc,$tmp_sql);
	$data = getTransactionSQL($dbc,$tmp_select_sql);
	closeDB($dbc);
	$html = createRecordsTableWithTotals($data, $table_columns);
	return $html;
}
function updatePOCCategories($pos_purchase_order_id)
{
	$poc = getPurchaseOrderContentsInArray($pos_purchase_order_id);
	$update_data = array();
	$update_data[0]['db_field'] = 'pos_category_id';
	$update_data[0]['id'] = 'pos_purchase_order_content_id';
	for($i=0;$i<sizeof($poc);$i++)
	{
		$update_data[0]['data_array'][$poc[$i]['pos_purchase_order_content_id']] = getProductCategory($poc[$i]['pos_product_id']);
	}
	return arrayUpdateSQL('pos_purchase_order_contents', $update_data);
}
function updatePOCTitles($pos_purchase_order_id)
{
	$poc = getPurchaseOrderContentsInArray($pos_purchase_order_id);
	$update_data = array();
	$update_data[0]['db_field'] = 'title';
	$update_data[0]['id'] = 'pos_purchase_order_content_id';
	for($i=0;$i<sizeof($poc);$i++)
	{
		$update_data[0]['data_array'][$poc[$i]['pos_purchase_order_content_id']] = scrubInput(getProductTitle($poc[$i]['pos_product_id']));
	}
	return arrayUpdateSQL('pos_purchase_order_contents', $update_data);
}
function updatePOCStyleNumbers($pos_purchase_order_id)
{
	$poc = getPurchaseOrderContentsInArray($pos_purchase_order_id);
	$update_data = array();
	$update_data[0]['db_field'] = 'style_number';
	$update_data[0]['id'] = 'pos_purchase_order_content_id';
	for($i=0;$i<sizeof($poc);$i++)
	{
		$update_data[0]['data_array'][$poc[$i]['pos_purchase_order_content_id']] = scrubInput(getProductStyleNumber($poc[$i]['pos_product_id']));
	}
	return arrayUpdateSQL('pos_purchase_order_contents', $update_data);
}
function updatePOCColorCodes($pos_purchase_order_id)
{
	$poc = getPurchaseOrderContentsInArray($pos_purchase_order_id);
	$update_data = array();
	$update_data[0]['db_field'] = 'color_code';
	$update_data[0]['id'] = 'pos_purchase_order_content_id';
	for($i=0;$i<sizeof($poc);$i++)
	{
		$update_data[0]['data_array'][$poc[$i]['pos_purchase_order_content_id']] = scrubInput(getProductOptionCode($poc[$i]['pos_product_sub_id'], getProductAttributeId('Color')));
	}
	return arrayUpdateSQL('pos_purchase_order_contents', $update_data);
}
function updatePOCColorDescriptions($pos_purchase_order_id)
{
	$poc = getPurchaseOrderContentsInArray($pos_purchase_order_id);
	$update_data = array();
	$update_data[0]['db_field'] = 'color_description';
	$update_data[0]['id'] = 'pos_purchase_order_content_id';
	for($i=0;$i<sizeof($poc);$i++)
	{
		$update_data[0]['data_array'][$poc[$i]['pos_purchase_order_content_id']] = scrubInput(getProductOptionName($poc[$i]['pos_product_sub_id'], getProductAttributeId('Color')));
	}
	return arrayUpdateSQL('pos_purchase_order_contents', $update_data);
}
function updatePOCRetailPrice($pos_purchase_order_id)
{
	$poc = getPurchaseOrderContentsInArray($pos_purchase_order_id);
	$update_data = array();
	$update_data[0]['db_field'] = 'retail';
	$update_data[0]['id'] = 'pos_purchase_order_content_id';
	//$update_data[1]['db_field'] = 'cost';
	//$update_data[1]['id'] = 'pos_purchase_order_content_id';

	
	for($i=0;$i<sizeof($poc);$i++)
	{
		/*
		$mysql_data_array[0]['db_field'] = 'cost';
		$mysql_data_array[0]['id'] = 'pos_purchase_order_content_id';
		$mysql_data_array[0]['data_array']['3789'] = 30.75;
		$mysql_data_array[0]['data_array']['3790'] = 40.75;
		$mysql_data_array[1]['db_field'] = 'retail';
		$mysql_data_array[1]['id'] = 'pos_purchase_order_content_id;
		$mysql_data_array[1]['data_array']['3789'] = 60.75;
		$mysql_data_array[1]['data_array']['3790'] = 80.75;
		*/
		
		$update_data[0]['data_array'][$poc[$i]['pos_purchase_order_content_id']] = getProductRetail($poc[$i]['pos_product_id']);
		//$update_data[1]['data_array'][$poc[$i]['pos_purchase_order_content_id']] = getProductRetail($poc[$i]['pos_product_id']);
		
	}
	return arrayUpdateSQL('pos_purchase_order_contents', $update_data);
	
}	

function createManualRecieveCompleteTableARrayDef($pos_purchase_order_id)
{
	$array_table_def= array(	
					array(	'th' => 'POC ID',
			 				'type' => 'hidden_input',
							'mysql_result_field' => 'pos_purchase_order_content_id',
							'mysql_post_field' => 'pos_purchase_order_content_id'),
					array(
							'th' => 'Manufacturer_id',
							'mysql_result_field' => 'product_upc',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(
							'th' => 'Style Number',
							'mysql_result_field' => 'style_number',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(
							'th' => 'Color Code',
							'mysql_result_field' => 'color_code',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(
							'th' => 'Color Description',
							'mysql_result_field' => 'color_description',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(	'th' => 'Size',
							'mysql_result_field' => 'size',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(	'th' => 'Product SubId',
							'mysql_result_field' => 'product_subid_name',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(	'th' => 'Quantity Ordered',
							'mysql_result_field' => 'quantity_ordered',
							'type' => 'td',
							'total' => 0,
							'mysql_post_field' => ''),
					array(	'th' => 'Quantity Received',
							'mysql_result_field' => 'received_quantity',
							'type' => 'td',
							'total' => 0,
							'mysql_post_field' => ''),
					array(	'th' => 'Quantity<br> Checking In <br> In New Condition',
							'mysql_result_field' => 'quantity_left_to_receive',
							'type' => 'input',
							'total' => 0,
							'tags' => ' class="highlight" ',
							'mysql_post_field' => 'received_quantity'),
			array(	'th' => 'Comments',
							'mysql_result_field' => 'comments',
							'type' => 'td',
							'mysql_post_field' => ''),
					array(	'th' => 'Receive Comments',
							'mysql_result_field' => 'receive_comments',
							'type' => 'input',
							'mysql_post_field' => 'receive_comments')
					
					);
	return $array_table_def;
}


function getPOLOG($pos_purchase_order_id)
{
	$sql = "SELECT log  FROM pos_purchase_orders WHERE pos_purchase_order_id = '$pos_purchase_order_id'";
	$data = getSingleValueSQL($sql);
	return $data;
}
function updatePOLog($pos_purchase_order_id, $unscrubbed_log)
{
	$user = getUserFullName($_SESSION['pos_user_id']);
	$date = getDateTime();
	$data = getPOLOG($pos_purchase_order_id);
	$new_log = scrubInput($data . "<p>" . 'Date: ' .$date . ' User:' . $user . ' ' . $unscrubbed_log .'</p>');
	$update_sql = "UPDATE pos_purchase_orders SET log = '$new_log' WHERE pos_purchase_order_id=$pos_purchase_order_id";
	$result = runSQL($update_sql);
	$new_sql  = "SELECT log  FROM pos_purchase_orders WHERE pos_purchase_order_id = '$pos_purchase_order_id'";
	return getSingleValueSQL($new_sql);
}
function checkIfPOCanCreateProducts($pos_purchase_order_id)
{
	//we need the po to have style numbers, color codes, sizes, and size rows defined
	$errors=array();
	$poc_array = getPurchaseOrderContentsInArray($pos_purchase_order_id);
	for($i=0;$i<sizeof($poc_array);$i++)
	{
		if($poc_array[$i]['size_row'] == 'undefined')
		{
			$errors[] = 'Size Row is Undefined For ID: ' .$poc_array[$i]['pos_purchase_order_content_id'];
		}
		if($poc_array[$i]['style_number'] == '')
		{
			$errors[] = 'Style Number is blank For ID: ' .$poc_array[$i]['pos_purchase_order_content_id'];
		}
		if($poc_array[$i]['size'] == '')
		{
			$errors[] = 'Size is blank For ID: ' .$poc_array[$i]['pos_purchase_order_content_id'];
		}
		if($poc_array[$i]['cost'] == '')
		{
			$errors[] = 'Cost is blank For ID: ' .$poc_array[$i]['pos_purchase_order_content_id'];
		}
		if($poc_array[$i]['color_code'] == '')
		{
			$errors[] = 'Color Code is blank For ID: ' .$poc_array[$i]['pos_purchase_order_content_id'];
		}
		if($poc_array[$i]['color_description'] == '')
		{
			$errors[] = 'Color Description is blank For ID: ' .$poc_array[$i]['pos_purchase_order_content_id'];
		}
		/*if($poc_array[$i]['pos_category_id'] == '0')
		{
			$errors[] = 'No Category For ID: ' .$poc_array[$i]['pos_purchase_order_content_id'];
		}*/
		
		
		
		//now check size against the acceptable sizes....
		//ufix
		
		//this will prevent a user from ordering 34 with no cup....
		
		
	}
	
	
	
	
	return $errors;

}
function checkForProductSubIds($pos_purchase_order_id)
{
	$purchase_order_products = getPurchaseOrderContentsAndProductSubids($pos_purchase_order_id);
	$bln_ok = true;
	for($i=0;$i<sizeof($purchase_order_products);$i++)
	{
		if($purchase_order_products[$i]['product_subid_name'] == '')
		{
			$bln_ok = false;
		}
	}
	return $bln_ok;
}
function getPurchaseOrderContentsAndProductSubids($pos_purchase_order_id)
{
	$purchase_order_contents_sql = "
		SELECT pos_purchase_order_contents.pos_purchase_order_content_id,
		 pos_purchase_order_contents.pos_purchase_order_id,
		 pos_products_sub_id.pos_product_id, 
		 pos_purchase_order_contents.style_number, 
		 pos_purchase_order_contents.color_description, 
		 pos_purchase_order_contents.color_code,
		pos_purchase_order_contents.pos_product_sub_id,
		pos_purchase_order_contents.quantity_ordered, 

		(pos_purchase_order_contents.quantity_ordered - pos_purchase_order_contents.quantity_canceled)*pos_purchase_order_contents.cost as expected_cost, 
		pos_purchase_order_contents.quantity_damaged,
		pos_purchase_order_contents.quantity_canceled,
		pos_purchase_order_contents.cost, 
		pos_purchase_order_contents.discount, 
		pos_purchase_order_contents.cost - pos_purchase_order_contents.discount as cost_minus_discount,
		
		
		pos_purchase_order_contents.discount_quantity, 
		pos_purchase_order_contents.comments, 
		
		pos_products_sub_id.product_subid_name, 
		pos_products_sub_id.product_upc, 
		pos_products.title,
		pos_purchase_order_contents.quantity_returning,
		
		
		
		 (SELECT coalesce(sum(pos_purchase_order_receive_contents.received_quantity),0) FROM pos_purchase_order_receive_contents
			WHERE pos_purchase_order_receive_contents.pos_purchase_order_content_id = pos_purchase_order_contents.pos_purchase_order_content_id) 
			
			 as received_quantity,
		
		
		 (SELECT coalesce(sum(pos_purchase_order_receive_contents.received_quantity),0) FROM pos_purchase_order_receive_contents
			WHERE pos_purchase_order_receive_contents.pos_purchase_order_content_id = pos_purchase_order_contents.pos_purchase_order_content_id) -quantity_ordered-quantity_canceled
			
			 as quantity_left_to_receive,
	
		
		(SELECT concat(
		
			(SELECT group_concat(concat(attribute_name,':Code:',option_code,' Desc:',option_name) SEPARATOR '<br>') 
			FROM pos_product_sub_id_options 
			LEFT JOIN pos_product_options ON pos_product_sub_id_options.pos_product_option_id = pos_product_options.pos_product_option_id 
			LEFT JOIN pos_product_attributes ON pos_product_options.pos_product_attribute_id = pos_product_attributes.pos_product_attribute_id
			WHERE pos_products_sub_id.pos_product_sub_id = pos_product_sub_id_options.pos_product_sub_id )
		
			)
		
		FROM pos_products_sub_id 
		LEFT JOIN pos_products ON pos_products_sub_id.pos_product_id = pos_products.pos_product_id
		LEFT JOIN pos_manufacturer_brands ON pos_products.pos_manufacturer_brand_id = pos_manufacturer_brands.pos_manufacturer_brand_id
		WHERE pos_products_sub_id.pos_product_sub_id = pos_purchase_order_contents.pos_product_sub_id) as item
		
		
		
		
		FROM pos_purchase_order_contents 
		LEFT JOIN pos_products_sub_id
		ON pos_products_sub_id.pos_product_sub_id = pos_purchase_order_contents.pos_product_sub_id
		LEFT JOIN pos_products ON pos_products_sub_id.pos_product_id = pos_products.pos_product_id
		WHERE pos_purchase_order_id = '$pos_purchase_order_id' 
		ORDER BY pos_purchase_order_contents.pos_purchase_order_content_id ASC";
	$contents=  getSQL($purchase_order_contents_sql);
	for($i=0;$i<sizeof($contents);$i++)
	{
		$contents[$i]['size'] = getPOCSize($contents[$i]['pos_purchase_order_content_id']);
	}
	return $contents;
	
}
function getSalesRepEmailFromPO($pos_purchase_order_id)
{
	$po = getPurchaseOrderData($pos_purchase_order_id);
	$pos_manufacturer_brand_id = $po['pos_manufacturer_brand_id'];
	$brand_level_rep = getSQL("SELECT sales_rep_email, sales_rep_name FROM pos_manufacturer_brands WHERE pos_manufacturer_brand_id = $pos_manufacturer_brand_id");
	
	if ($brand_level_rep[0]['sales_rep_email']!='')
	{
		$rep_email = $brand_level_rep[0]['sales_rep_email'];
	}
	else
	{
		$pos_manufacturer_id = getManufacturerIdFromBrandId($po['pos_manufacturer_brand_id']);
	
		$rep_email = getSalesRepEmail($pos_manufacturer_id);
	}
	return $rep_email;
}
function getSalesRepNameFromPO($pos_purchase_order_id)
{
	$po = getPurchaseOrderData($pos_purchase_order_id);
	$pos_manufacturer_brand_id = $po['pos_manufacturer_brand_id'];
	$brand_level_rep = getSQL("SELECT sales_rep_email, sales_rep_name FROM pos_manufacturer_brands WHERE pos_manufacturer_brand_id = $pos_manufacturer_brand_id");
	
	if ($brand_level_rep[0]['sales_rep_email']!='')
	{
		$rep_name = $brand_level_rep[0]['sales_rep_name'];
	}
	else
	{

		$pos_manufacturer_id = getManufacturerIdFromBrandId($po['pos_manufacturer_brand_id']);
		$rep_sql = getSQL("SELECT sales_rep FROM pos_manufacturers WHERE pos_manufacturer_id = $pos_manufacturer_id");
		$rep_name = $rep_sql[0]['sales_rep'];
	}
	return $rep_name;
}

?>