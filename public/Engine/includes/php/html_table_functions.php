<?php
/* these are functions that create html tables
*/

function createHorizontalInputHTMLTable($table_name, $table_def, $data, $table_tags = '')
{
/* table_def looks like this:
return array(array('caption' => 'Product Subtotal',
					'db_field' => 'pre_discount_subtotal',
					'type' => 'input',
					'tags' =>  ' class="footerCell" readonly = "readonly" onblur="calculateTotals(this)" ',

					'element' => 'input',
					'element_type' => 'text',
					'properties' => array()),
				array('caption' => 'In Store Promotions',
					'db_field' => 'pre_tax_promotion_amount',
					'type' => 'input',
					'tags' =>  ' class="footerCell" readonly = "readonly" onblur ="calculateTotals(this)"  onkeyup="checkInput2(this,\'0123456789.\');calculateTotals(this)" ',
					'value' => getPreTaxDiscountAmountFromSalesInvoice($pos_sales_invoice_id),
					'round' => 2,

					'element' => 'input',
					'element_type' => 'text',
					'properties' => array()),
				array('caption' => 'Pre Tax Subtotal',
					'db_field' => 'pre_tax_subtotal',
					'type' => 'input',
					'tags' =>  ' class="footerCell" readonly = "readonly" onblur="calculateTotals(this)" ',

					'element' => 'input',
					'element_type' => 'text',
					),
				array('caption' => 'Tax',
					'db_field' => 'invoice_tax_total',
					'type' => 'input',
					'tags' =>  ' class="footerCell" readonly = "readonly" onblur="calculateTotals(this)" ',

					'element' => 'input',
					'element_type' => 'text',
					),
				array('caption' => 'Manufacturer Promotions',
					'db_field' => 'post_tax_promotion_amount',
					'type' => 'input',
					'tags' =>  ' class="footerCell" readonly = "readonly" onkeyup="checkInput2(this,\'0123456789.\');calculateTotals(this)" onblur="calculateTotals(this)" ',
					'value' => getPostTaxDiscountAmountFromSalesInvoice($pos_sales_invoice_id),
					'round' => 2,
					'element' => 'input',
					'element_type' => 'text',
					),
				array('caption' => 'Number Of Items',
					'db_field' => 'total_quantity',
					'type' => 'input',
					'tags' =>  ' class="footerCell" readonly="readonly" ',

					'element' => 'input',
					'element_type' => 'text',
					'properties' => array()),
				array('caption' => 'Le Grande Total',
					'db_field' => 'le_grande_total',
					'type' => 'input',
					'tags' =>  ' class="footerCell" readonly="readonly" ',

					'element' => 'input',
					'element_type' => 'text',
					'properties' => array()),
				array('caption' => 'You Save',
					'db_field' => 'you_save',
					'type' => 'input',
					'tags' =>  ' class="footerCell" readonly="readonly" ',

					'element' => 'input',
					'element_type' => 'text',
					'properties' => array())
				);
	*/
	/* data would look lie this: $data['db_field'] = value; */
	$thead_name = $table_name . '_thead';
	$tbody_name = $table_name . '_tbody';
	$tfoot_name = $table_name . '_tfoot';
	
	
	$html =  '<TABLE id="'.$table_name.'" name="'.$table_name.'"  '.$table_tags.'>';	
	$html .= '<thead id="'.$thead_name.'" name="'.$thead_name.'" >' .newline();
	$html .= '<tr>'.newline();
	for($i=0;$i<sizeof($table_def);$i++)
	{
		$html .= createTHFromTD_def($table_def[$i]);
	}
	$html .= '</tr>'.newline();
	$html .= '</thead>'.newline();
	//this is the body
	$html .=  '	<tbody id="'.$tbody_name.'" name="'.$tbody_name.'"  >';
	$html .= '<tr>'.newline();
	for($col=0;$col<sizeof($table_def);$col++)
	{
		$table_def[$col]['value'] = (isset($data[$table_def[$col]['db_field']])) ? $data[$table_def[$col]['db_field']] : '';
		$html .= createTDFromTD_def($table_def[$col]);
	}
	$html .= '</tr>'.newline();
	$html .=  '</tbody>';
	$html .=  '</table>';
	return $html;	
}
function createHorizontalViewHTMLTable($table_name, $table_def, $data, $table_tags = '')
{
	//same as createHorizontalInputHTMLTable but switched to plain data
	$thead_name = $table_name . '_thead';
	$tbody_name = $table_name . '_tbody';
	$tfoot_name = $table_name . '_tfoot';
	
	
	$html =  '<TABLE id="'.$table_name.'" name="'.$table_name.'"  '.$table_tags.'>';	
	$html .= '<thead id="'.$thead_name.'" name="'.$thead_name.'" >' .newline();
	$html .= '<tr>'.newline();
	for($i=0;$i<sizeof($table_def);$i++)
	{
		$html .= createTHFromTD_def($table_def[$i]);
	}
	$html .= '</tr>'.newline();
	$html .= '</thead>'.newline();
	//this is the body
	$html .=  '	<tbody id="'.$tbody_name.'" name="'.$tbody_name.'"  >';
	$html .= '<tr>'.newline();
	for($col=0;$col<sizeof($table_def);$col++)
	{
		//$html .= createTDFromTD_def($table_def[$col]);
		if(isset($table_def[$col]['tags']))
		{
			$html.= '<td ' . $table_def[$col]['tags']. ' >'.$data[$table_def[$col]['db_field']] . '</td>';
		}
		else
		{
			$html.= '<td>'.$data[$table_def[$col]['db_field']] . '</td>';
		}
	}
	$html .= '</tr>'.newline();
	$html .=  '</table>';
	return $html;
	
	
}

?>