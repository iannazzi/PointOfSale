<?php
function createDynamicTable($table_def, $data)
{
	$javascript = POS_ENGINE_URL . '/includes/javascript/dynamic_table.js';
	
	$html =  '<script src="'.$javascript.'"></script>'.newline();
	$html .=  '<TABLE  class="linedTable" ><tr><td>';
	
	$html .= createDynamicHtmlTable($table_def,$data);
	$html .=  createDynamicTableActionButtons();
	$html .=  '</td></tr></TABLE >';

	return $html;
}
function createDynamicTableReuse($table_name, $table_def, $data, $form_id, $table_tags = 'class="dynamic_contents_table"')
{
	$javascript = DYNAMIC_TABLE_OBJECT;//POS_ENGINE_URL . '/includes/javascript/dynamic_table_object.js';
	$table_functions = JAVASCRIPT_TABLE_FUNCTIONS;
	$thead_name = $table_name . '_thead';
	$tbody_name = $table_name . '_tbody';
	$tfoot_name = $table_name . '_tfoot';
	$table_object_name = $table_name . '_object';
	
	$html =  '<script src="'.$javascript.'"></script>'.newline();
	$html .=  '<script src="'.$table_functions.'"></script>'.newline();
	//$html =  '<div class = "dynamic_table">';
	$html .=  '<TABLE  class="linedTable" '. $table_tags .'><tr><td>';
	
	
	$html .=  '<TABLE id="'.$table_name.'" name="'.$table_name.'"  '.$table_tags.' >';

	$html .= '<thead id="'.$thead_name.'"  name="'.$thead_name.'">' .newline();
	$html .= '<tr>'.newline();
	for($i=0;$i<sizeof($table_def);$i++)
	{
		$html .= createTHFromTD_def($table_def[$i]);
	}
	$html .= '</tr>'.newline();

	$html .= '</thead>'.newline();
	//this is the body which is created by javascript	
	$html .=  '	<tbody id = "'.$tbody_name.'" name = "'.$tbody_name.'"  >';

	//javascript loads the contents...
	
	
	
	$html.= '</tbody>';
	//And the footer
	$html .=  '<tfoot id = "'.$tfoot_name.'" name = "'.$tfoot_name.'" >';
	//now we need to figure out which columns need a footer, and what are they?
	$html .= createTFootFromTD_def($table_def);

	$html .= '</tfoot>';
	$html .=  '</table>';
	

	$html .= '<script>var tbody_def = ' . prepareTableDefForJavascriptTableGeneration($table_def) . ';</script>';

	$tbody_def = prepareTableDefForJavascriptTableGeneration($table_def);
	$json_invoice_contents = json_encode($data);
	//this creates the table
	$html .= '<script>var '.$table_object_name.' = new dynamic_table_object("'.$table_name.'", '.$tbody_def.', "'.$form_id. '",'. $json_invoice_contents.')</script>';
	
	
	$html .=  '<INPUT class = "thin_button" type="button" style="width:60px;" value="Add Row" onclick="'.$table_object_name.'.addRow()" />';
	$html .=  '<INPUT class = "thin_button" type="button" style="width:80px;" value="Copy Row" onclick="'.$table_object_name.'.copyRow()" />';
	$html .=  '<INPUT class = "thin_button" type="button" value="Move Row Up" onclick="'.$table_object_name.'.moveRowUp()" />';
	$html .=  '<INPUT class = "thin_button" type="button" style="width:120px;" value="Move Row Down" onclick="'.$table_object_name.'.moveRowDown()" />';
	$html .=  '<INPUT class = "thin_button" type="button" style="width:80px;" value="Delete Row" onclick="'.$table_object_name.'.deleteRow()" />';
	$html .=  '</td></tr></TABLE >';
//this needs to be called after the table is "rendered" which I think means the form needs to be put in...
$html .= '<script>'.$table_name.'_object.init()</script>';
	return $html;
}
function createDynamicTableReuseV2($table_name, $table_def, $data, $form_id, $table_tags = 'class="dynamic_contents_table"', $buttons = array())
{
	//version 2 has some button functions
	$javascript = DYNAMIC_TABLE_OBJECT;//POS_ENGINE_URL . '/includes/javascript/dynamic_table_object.js';
	$table_functions = JAVASCRIPT_TABLE_FUNCTIONS;
	$thead_name = $table_name . '_thead';
	$tbody_name = $table_name . '_tbody';
	$tfoot_name = $table_name . '_tfoot';
	$table_object_name = $table_name . '_object';
	
	$html =  '<script src="'.$javascript.'"></script>'.newline();
	$html .=  '<script src="'.$table_functions.'"></script>'.newline();
	//$html =  '<div class = "dynamic_table">';
	$html .=  '<TABLE  '. $table_tags .'><tr><td>';
	
	
	$html .=  '<TABLE id="'.$table_name.'" name="'.$table_name.'"  '.$table_tags.' >';

	$html .= '<thead id="'.$thead_name.'"  name="'.$thead_name.'">' .newline();
	$html .= '<tr>'.newline();
	for($i=0;$i<sizeof($table_def);$i++)
	{
		$html .= createTHFromTD_def($table_def[$i]);
	}
	$html .= '</tr>'.newline();

	$html .= '</thead>'.newline();
	//this is the body which is created by javascript	
	$html .=  '	<tbody id = "'.$tbody_name.'" name = "'.$tbody_name.'"  >';

	//javascript loads the contents...
	
	
	
	$html.= '</tbody>';
	//And the footer
	$html .=  '<tfoot id = "'.$tfoot_name.'" name = "'.$tfoot_name.'" >';
	//now we need to figure out which columns need a footer, and what are they?
	$html .= createTFootFromTD_def($table_def);

	$html .= '</tfoot>';
	$html .=  '</table>';
	

	//$html .= '<script>var '. tbody_name.'_tbody_def = ' . prepareTableDefForJavascriptTableGeneration($table_def) . ';</script>';

	$tbody_def = prepareTableDefForJavascriptTableGeneration($table_def);
	$json_invoice_contents = json_encode($data);
	//this creates the table
	$html .= '<script>var '.$table_object_name.' = new dynamic_table_object("'.$table_name.'", '.$tbody_def.', "'.$form_id. '",'. $json_invoice_contents.')</script>';
	
	for($btn = 0; $btn<sizeof($buttons);$btn++)
	{
		$html .=  '<INPUT ';
		foreach($buttons[$btn] as $prop => $value)
		{
			$html .= ' ' . $prop . '="'.$value .'" ';
			
		}
		$html .= ' />'.newline();
		
	}
	
	$html .=  '</td></tr></TABLE >';
//this needs to be called after the table is "rendered" which I think means the form needs to be put in...
$html .= '<script>'.$table_name.'_object.init()</script>';
	return $html;
}
function createDynamicTableReuseV3($table_id, $table_def, $data, $table_tags = 'class="dynamic_contents_table"', $buttons = array())
{
	//version 2 has some button functions
	$javascript = DYNAMIC_TABLE_OBJECT_V3;//POS_ENGINE_URL . '/includes/javascript/dynamic_table_object.js';
	$table_functions = JAVASCRIPT_TABLE_FUNCTIONS;
	$thead_name = $table_id . '_thead';
	$tbody_name = $table_id . '_tbody';
	$tfoot_name = $table_id . '_tfoot';
	//$table_object_name = $table_name . '_object';
	
	$html =  '<script src="'.$javascript.'"></script>'.newline();
	$html .=  '<script src="'.$table_functions.'"></script>'.newline();
	//$html =  '<div class = "dynamic_table">';
	
	if(sizeof($buttons)>0)
	{
		$html .=  '<TABLE  class="plainTable"><tr><td>';
	}
	
	$html .=  '<TABLE id="'.$table_id.'" name="'.$table_id.'"  '.$table_tags.' >';

	$html .= '<thead id="'.$thead_name.'"  name="'.$thead_name.'">' .newline();
	$html .= '<tr>'.newline();
	for($i=0;$i<sizeof($table_def);$i++)
	{
		$html .= createTHFromTD_def($table_def[$i]);
	}
	$html .= '</tr>'.newline();

	$html .= '</thead>'.newline();
	//this is the body which is created by javascript	
	$html .=  '	<tbody id = "'.$tbody_name.'" name = "'.$tbody_name.'"  >';

	//javascript loads the contents...
	
	
	
	$html.= '</tbody>';
	//And the footer
	$html .=  '<tfoot id = "'.$tfoot_name.'" name = "'.$tfoot_name.'" >';
	//now we need to figure out which columns need a footer, and what are they?
	$html .= createTFootFromTD_def($table_def);

	$html .= '</tfoot>';
	$html .=  '</table>';
	
	if(sizeof($buttons)>0)
	{
		for($btn = 0; $btn<sizeof($buttons);$btn++)
		{
			$html .=  '<INPUT ';
			foreach($buttons[$btn] as $prop => $value)
			{
				$html .= ' ' . $prop . '="'.$value .'" ';
			
			}
			$html .= ' />'.newline();
		
		}
		$html .=  '</td></tr></TABLE >';
	}
	//$html .= '<script>var '. tbody_name.'_tbody_def = ' . prepareTableDefForJavascriptTableGeneration($table_def) . ';</script>';

	$tbody_def = json_encode($table_def);
	$json_invoice_contents = json_encode($data);
	//this creates the table, although in javascript we will need to re-create the table...
	$html .= '<script>var '.$table_id.' = new dynamic_table_object_v3("'.$table_id.'", '.$tbody_def.','. $json_invoice_contents.')</script>';
	


//this needs to be called after the table is "rendered" which I think means the form needs to be put in...
$html .= '<script>'.$table_id.'.init()</script>';
	return $html;
}
function createDynamicHtmlTableReuse($table_name, $table_def, $init_table_contents)
{

	return $html;
}
function createDynamicHtmlTable($table_def, $init_table_contents)
{
	//$html =  '<div class = "dynamic_table">';
	$html =  '<TABLE id="dynamic_contents_table" name="dynamic_contents_table" class="dynamic_contents_table" >';
	$html .= '<thead id="dynamic_contents_thead" class="dynamic_contents_thead" name="dynamic_contents_thead">' .newline();
	$html .= '<tr>'.newline();
	for($i=0;$i<sizeof($table_def);$i++)
	{
		$html .= createTHFromTD_def($table_def[$i]);
	}
	$html .= '</tr>'.newline();

	$html .= '</thead>'.newline();
	//this is the body which is created by javascript	
	$html .=  '	<tbody id = "dynamic_contents_tbody" name = "dynamic_contents_tbody" class = "dynamic_contents_tbody" ></tbody>';
	//And the footer
	$html .=  '<tfoot id = "dynamic_contents_tfoot" name = "dynamic_contents_tfoot" class = "dynamic_contents_tfoot">';
	//now we need to figure out which columns need a footer, and what are they?
	$html .= createTFootFromTD_def($table_def);

	$html .= '</tfoot>';
	$html .=  '</table>';
	

	$html .= '<script>var tbody_def = ' . prepareTableDefForJavascriptTableGeneration($table_def) . ';</script>';
	
	$html .=  '	<script>var contents_table = "dynamic_contents_table";</script>';
	//I harde coded tbody_id into the javascript - so use it!
	$html .=  '	<script>var tbody_id = "dynamic_contents_tbody";</script>';
	$html .=  '	<script>var contents_thead = "dynamic_contents_thead";</script>';
	$html .=  '	<script>var contents_tfoot = "dynamic_contents_tfoot";</script>';
	
	$html .= '<script> var json_table_contents = ' . json_encode($init_table_contents) . ';</script>';	
	return $html;
}
function createDynamicTableActionButtons()
{

	$html =  '<INPUT tabindex="-1" class = "button" type="button" style="width:60px;" value="Add Row" onclick="addRow()" />';
	$html .=  '<INPUT tabindex="-1" class = "button" type="button" style="width:80px;" value="Copy Row" onclick="copyRow()" />';
	$html .=  '<INPUT  tabindex="-1" class = "button" type="button" value="Move Row Up" onclick="moveRowUp()" />';
	$html .=  '<INPUT tabindex="-1" class = "button" type="button" style="width:120px;" value="Move Row Down" onclick="moveRowDown()" />';
	$html .=  '<INPUT tabindex="-1" class = "button" type="button" style="width:80px;" value="Delete Row" onclick="deleteRow()" />';
	return $html;
	
}
function createStaticViewDynamicTable($table_def, $data, $table_tags = '')
{
	$html =  '<TABLE id="static_contents_table" name="static_contents_table" class="static_contents_table" '.$table_tags.'>';
	$html .= '<thead id="static_contents_thead" class="static_contents_thead" name="static_contents_thead">' .newline();
	$html .= '<tr>'.newline();
	for($i=0;$i<sizeof($table_def);$i++)
	{
		if ($table_def[$i]['type'] != 'row_checkbox')
		{
			$html .= createTHFromTD_def($table_def[$i]);
		}
	}
	$html .= '</tr>'.newline();

	$html .= '</thead>'.newline();
	//this is the body which is created by javascript	
	$html .=  '	<tbody id = "static_contents_tbody" name = "static_contents_tbody" class = "static_contents_tbody" >';

	for($row = 0;$row<sizeof($data);$row++)
	{
		$html .= '<tr>'.newline();
		for($i=0;$i<sizeof($table_def);$i++)
		{
			
			if(isset($table_def[$i]['type']) && $table_def[$i]['type'] != 'hidden' && $table_def[$i]['type']!='row_checkbox')
			{
				if ($table_def[$i]['db_field'] == 'none' )
				{
					$html .= '<td>'.newline();
					$html .= '</td>'.newline();

				}
				elseif ($table_def[$i]['type'] == 'checkbox')
				{
					$html .= '<td>'.newline();
					if(isset($data[$row][$table_def[$i]['db_field']]))
					{
						$html .= '<input type="checkbox" ';
						if ($data[$row][$table_def[$i]['db_field']] == 1)
						{
							$html.= 'checked ';
						}
						$html .= '/>';
					}
					$html .= '</td>'.newline();
				}
				elseif ($table_def[$i]['type'] == 'link')
				{
					
					if(isset($data[$row][$table_def[$i]['db_field']]) && $data[$row][$table_def[$i]['db_field']] != 0)
					{
						$html .= '<td>';
						if(isset($table_def[$i]['get_id_data']))
						{
							$get = $table_def[$i]['get_id_link'] . '=' .$data[$row][$table_def[$i]['get_id_data']];
						}
						else
						{
							$get = $table_def[$i]['get_id_link'] . '=' .$data[$row][$table_def[$i]['db_field']];
						}
						$caption = (isset($table_def[$i]['url_caption'])) ? $table_def[$i]['url_caption'] : $data[$row][$table_def[$i]['db_field']];
						$link = addGetToUrl($table_def[$i]['get_url_link'],$get);
						 $html .= url_blank_link($link, $caption);
						 $html .= '</td>';
					}
					else
					{
						$html .= '<td>';
						 $html .= '</td>';
					}
				}
				elseif ($table_def[$i]['db_field'] == 'row_number')
				{
					$html .= '<td>'.newline();
					$html .= $row +1;
					$html .= '</td>'.newline();
				}
				else
				{
					$html .= '<td>'.newline();
					if(isset($data[$row][$table_def[$i]['db_field']]))
					{
						//might want to stick the html in here....
						if(isset($table_def[$i]['round']))
						{
							$html .= number_format($data[$row][$table_def[$i]['db_field']],$table_def[$i]['round']);
						}
						else
						{
							$html .= $data[$row][$table_def[$i]['db_field']];
						}
					}
					$html .= '</td>'.newline();
				}
			}
		}
		$html .= '</tr>'.newline();
	}
	$html .= '</tbody>';
	//And the footer
	$html .=  '<tfoot id = "static_contents_tfoot" name = "static_contents_tfoot" class = "static_contents_tfoot">';
	//now we need to figure out which columns need a footer, and what are they?
	$html .= createTFootForView($table_def, $data);

	$html .= '</tfoot>';
	$html .=  '</table>';
	

	$html .= '<script>var tbody_def = ' . prepareTableDefForJavascriptTableGeneration($table_def) . ';</script>';
	
	$html .=  '	<script>var contents_table = "dynamic_contents_table";</script>';
	//I harde coded tbody_id into the javascript - so use it!
	$html .=  '	<script>var tbody_id = "dynamic_contents_tbody";</script>';
	$html .=  '	<script>var contents_thead = "dynamic_contents_thead";</script>';
	$html .=  '	<script>var contents_tfoot = "dynamic_contents_tfoot";</script>';
	
	$html .= '<script> var json_table_contents = ' . json_encode($data) . ';</script>';	
	return $html;
}
function createStaticViewDynamicTablev2($table_name, $table_def, $data, $table_tags = '')
{
	
	$thead_name = $table_name . '_thead';
	$tbody_name = $table_name . '_tbody';
	$tfoot_name = $table_name . '_tfoot';
	$html = newline();
	$html .=  '<TABLE id="'.$table_name.'" name="'.$table_name.'"  '.$table_tags.'>'.newline();
	$html .= '<thead id="'.$thead_name.'" name="'.$thead_name.'" >' .newline();
	$html .= '<tr>'.newline();
	for($i=0;$i<sizeof($table_def);$i++)
	{
		if ($table_def[$i]['type'] != 'row_checkbox' )
		{
			$html .= createTHFromTD_def($table_def[$i]);
		}
	}
	$html .= '</tr>'.newline();

	$html .= '</thead>'.newline();
	//this is the body which is created by javascript	
	$html .=  '	<tbody id="'.$tbody_name.'" name="'.$tbody_name.'"  >';

	for($row = 0;$row<sizeof($data);$row++)
	{
		$html .= '<tr>'.newline();
		for($i=0;$i<sizeof($table_def);$i++)
		{
			if(isset($table_def[$i]['td_width']))
			{
				$td_width = 'style="width:'. $table_def[$i]['td_width'] .'"';
			}
			else
			{
				$td_width = '';
			}
			if(isset($table_def[$i]['type']) && $table_def[$i]['type'] != 'hidden' && $table_def[$i]['type']!='row_checkbox')
			{

				if ($table_def[$i]['db_field'] == 'none')
				{
					$html .= '<td ' . $td_width. '>'.newline();
					$html .= '</td>'.newline();
				}
				elseif( $table_def[$i]['type'] == 'row_checkbox')
				{
				}
				elseif( $table_def[$i]['type'] == 'checkbox')
				{
					$html .= '<td '. $td_width. '><input  type = "checkbox"  disabled = "disabled" ';
					if($data[$row][$table_def[$i]['db_field']] == '1')
					{
						$html .=  ' checked = "checked" ';
						}
					$html .= ' />'.newline();
				}
				elseif( $table_def[$i]['type'] == 'select_checkbox')
				{
					$html .= '<td '. $td_width. '><input  type = "checkbox"   ';
					if(isset($data[$row][$table_def[$i]['db_field']]) && $data[$row][$table_def[$i]['db_field']] == '1')
					{
						$html .=  ' checked = "checked" ';
					}
					$html .= ' />'.newline();
				}
				elseif ($table_def[$i]['type'] == 'link')
				{
					
					if(isset($data[$row][$table_def[$i]['db_field']]) && $data[$row][$table_def[$i]['db_field']] != 0)
					{
						$html .= '<td '. $td_width. '>';
						$get = $table_def[$i]['get_id_link'] . '=' .$data[$row][$table_def[$i]['db_field']];
						$caption = (isset($table_def[$i]['url_caption'])) ? $table_def[$i]['url_caption'] : $data[$row][$table_def[$i]['db_field']];
						$link = addGetToUrl($table_def[$i]['get_url_link'],$get);
						 $html .= url_blank_link($link, $caption);
						 $html .= '</td>';
					}
					else
					{
						$html .= '<td '. $td_width. ' >';
						 $html .= '</td>';
					}
				}
				elseif ($table_def[$i]['db_field'] == 'row_number')
				{
					$html .= '<td '. $td_width. ' >'.newline();
					$html .= $row +1;
					$html .= '</td>'.newline();
				}
				elseif($table_def[$i]['type'] == 'select' || $table_def[$i]['type'] == 'tree_select')
				{
					$html .= '<td '. $td_width. ' >'.newline();
					//$html .= addvalueToSelect(disableSelect($table_def[$i]['html']),$data[$row][$table_def[$i]['db_field']]);
					//get the value from select names using the index from select_values
					//value is $data[$row][$table_def[$i]['db_field']]
					
					if (in_array($data[$row][$table_def[$i]['db_field']], $table_def[$i]['select_values']))
					{
					$html .= $table_def[$i]['select_names'][array_search($data[$row][$table_def[$i]['db_field']],$table_def[$i]['select_values'])];
					$html .= '</td>'.newline();
					}
					else
					{
						$html .= '--';
					}
					
					
					
				}
				elseif(isset($table_def[$i]['variable_get_url_link']))
				{
					//we have a little tricky linker here....
					if(isset($data[$row][$table_def[$i]['db_field']]) && $data[$row][$table_def[$i]['db_field']] != 0
							&& isset($table_def[$i]['variable_get_url_link'][ $data[$row][$table_def[$i]['variable_get_url_link']['row_result_lookup']]]['url']))
					{
					
						$html .= '<td '. $td_width. '>';
							//echo 	$table_def[$i]['variable_get_url_link'][ $data[$row][$table_def[$i]['variable_get_url_link']['row_result_lookup']]]['url'];
							//echo $data[$row][$table_def[$i]['variable_get_url_link']['row_result_lookup']];
						$url = $table_def[$i]['variable_get_url_link'][ $data[$row][$table_def[$i]['variable_get_url_link']['row_result_lookup']]]['url'];
						$get_array = array();	
						//preprint ($table_def[$i]['variable_get_url_link']['PRODUCT']['get_data']);
		foreach($table_def[$i]['variable_get_url_link'][$data[$row][$table_def[$i]['variable_get_url_link']['row_result_lookup']]]['get_data'] as $key=>$value)
		{
			$get_array[] = $key . '=' .$data[$row][$value];
		}
		
		$gets = implode('&', $get_array);

						$caption = (isset($table_def[$i]['url_caption'])) ? $table_def[$i]['url_caption'] : $data[$row][$table_def[$i]['db_field']];
						$link = addGetToUrl($url,$gets);
						 $html .= url_blank_link($link, $caption);
						 $html .= '</td>';
					}
					else
					{
						$html.= staticViewDynamicTableTD($data, $table_def,$i, $row, $td_width);
					}
					
					
					
					
					
				}
				else
				{
					$html.= staticViewDynamicTableTD($data, $table_def,$i, $row, $td_width);

				}
			}
		}
		$html .= '</tr>'.newline();
	}
	$html .= '</tbody>';
	
	
	//************************************ FOOOTER *************************************************
	$html .=  '<tfoot id="'.$tfoot_name.'" name="'.$tfoot_name.'" >';
	//now we need to figure out which columns need a footer, and what are they?
	//look for total
	$total_line = false;
	$footer = false;

	
	for($i=0;$i<sizeof($table_def);$i++)
	{
		if(isset($table_def[$i]['total']))
		{
			$total_line = true;
		}
		if(isset($table_def[$i]['footer']))
		{
			$footer = true;
		}
	}
	$num_columns = 0;
	for($i=0;$i<sizeof($table_def);$i++)
	{
		if($table_def[$i]['type'] == 'hidden' || $table_def[$i]['type'] == 'row_checkbox' )
		{
		}
		else
		{
			$num_columns ++;
		}
	}
	if ($total_line)
	{
		$col_span_counter = 0;
		$html .= '<tr>';
		for($i=0;$i<sizeof($table_def);$i++)
		{
			if(isset($table_def[$i]['total']))
			{
				//get the total
				$total = 0;
				for($tot=0;$tot<sizeof($data);$tot++)
				{
					$total = $total + $data[$tot][$table_def[$i]['db_field']];
				}
				$html .= '<td>';
				$html .= number_format($total,$table_def[$i]['total']);
				$html .='</td>';
			}
			else
			{
				if($table_def[$i]['type'] != 'hidden' && $table_def[$i]['type'] != 'row_checkbox')
				{
					$html .= '<td class="emptyCell"></td>';
				}
			}
		}
		$html .= '</tr>'.newline();
	}

	if($footer)
	{
		$col_span_counter = 0;
		for($i=0;$i<sizeof($table_def);$i++)
		{
			
			if(isset($table_def[$i]['footer']))
			{
				for($j=0;$j<sizeof($table_def[$i]['footer']);$j++)
				{
					$html .= '<tr>';
					$html .= '<th colspan = "'. ($col_span_counter) . '" class = "emptyCell">';
					$html .= $table_def[$i]['footer'][$j]['caption'];
					$html .= '</th>';
					$html .= '<td>TBD</td>';
					//$html .= createTDFromTD_def($table_def[$i]['footer'][$j]);
					
					/*$html .='<td>';
					$html .= '<input class="footerCell" id = "'.$column_defintion[$i]['footer'][$j]['db_field'] .'" name="'.$column_defintion[$i]['footer'][$j]['db_field'] .'" />';
					$html .= '</td>';*/
					$html .= '<th colspan = "'. ($num_columns - $col_span_counter -1) .'" class = "emptyCell"></th>';
					$html .= '</tr>'.newline();
				}
			}
			else
			{
				if($table_def[$i]['type'] != 'hidden' && $table_def[$i]['type'] != 'row_checkbox')
				{
					$col_span_counter = $col_span_counter+1;
				}
			}
		}
	}

	$html .= '</tfoot>';
	$html .=  '</table>';
	


	return $html;
}
function staticViewDynamicTableTD($data, $table_def,$i, $row, $td_width)
{
		$html = '';
		$html .= '<td '. $td_width. ' >'.newline();
		if(isset($data[$row][$table_def[$i]['db_field']]))
		{
			//might want to stick the html in here....
			if(isset($table_def[$i]['round']))
			{
				$html .= number_format($data[$row][$table_def[$i]['db_field']],$table_def[$i]['round']);
			}
			else
			{
				if(isset($table_def[$i]['word_wrap']))
				{
					
					$html .= 
					wordwrap($data[$row][$table_def[$i]['db_field']], $table_def[$i]['word_wrap'], "<br />\n", true);
					
				}
				else
				{
					  $html .= $data[$row][$table_def[$i]['db_field']];
				}
			}
		}
		$html .= '</td>'.newline();
		return $html;
}
function convertTableDataArrayToArray($poorly_posted_json_array)
{
	$data = explode('[',$poorly_posted_json_array);
	$counter = 0;
	for($i=2;$i<sizeof($data);$i++)
	{
		$data2[$counter] = explode(',',str_replace('\"','',str_replace(']]','',str_replace('],','',$data[$i]))));
		$counter++;
	}
	return $data2;
}
function createStaticArrayHTMLTable($table_def, $table_contents)
{
	//this function creates a table for array style data that does not need to add/change/move/delete rows
	
	//$html =  '<div class = "dynamic_table">';
	$html =  '<TABLE id="static_contents_table" name="static_contents_table" class="static_contents_table" >';
	$html .= '<thead id="static_contents_thead" class="static_contents_thead" name="static_contents_thead">' .newline();
	$html .= '<tr>'.newline();
	for($i=0;$i<sizeof($table_def);$i++)
	{
		$html .= createTHFromTD_def($table_def[$i]);
	}
	$html .= '</tr>'.newline();

	$html .= '</thead>'.newline();
	//this is the body
	$html .=  '	<tbody id = "static_contents_tbody" name = "static_contents_tbody" class = "static_contents_tbody" ></tbody>';
	for($row=0;$row<sizeof($table_contents);$row++)
	{
		$html .= '<tr>'.newline();
		for($col=0;$col<sizeof($table_def);$col++)
		{
			//find the value here and pass it in?
			$table_def[$col]['value'] = (isset($table_contents[$row][$table_def[$col]['db_field']])) ? $table_contents[$row][$table_def[$col]['db_field']] : '';
			if ($table_def[$col]['type'] == 'row_number') $table_def[$col]['value'] = $row +1;
			if ($table_def[$col]['type'] == 'row_checkbox') $table_def[$col]['checkbox_index'] = $row;
			$html .= createArrayTDFromTD_def($table_def[$col]);
		}
		$html .= '</tr>'.newline();
	}
	//And the footer
	$html .=  '<tfoot id = "static_contents_tfoot" name = "static_contents_tfoot" class = "static_contents_tfoot">';
	//now we need to figure out which columns need a footer, and what are they?
	$html .= createTFootFromTD_def($table_def);

	$html .= '</tfoot>';
	$html .=  '</table>';
	

	$html .= '<script>var tbody_def = ' . prepareTableDefForJavascriptTableGeneration($table_def) . ';</script>';
	
	$html .=  '	<script>var contents_table = "static_contents_table";</script>';
	//I harde coded tbody_id into the javascript - so use it!
	$html .=  '	<script>var tbody_id = "static_contents_tbody";</script>';
	$html .=  '	<script>var contents_thead = "static_contents_thead";</script>';
	$html .=  '	<script>var contents_tfoot = "static_contents_tfoot";</script>';
	
	//prevent enter....
	$html .= '<script>$(document).ready(function() {
  $(window).keydown(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });
});
</script>';
	//$html .= '<script> var json_table_contents = ' . json_encode($table_contents) . ';</script>';	
	return $html;

}
function createStaticArrayHTMLTablev2($table_def, $table_contents, $table_tags = 'class="static_contents_table"')
{
	//this function creates a table for array style data that does not need to add/change/move/delete rows
	//this version fixes the checkbox = it has to be name_row in order to check, and not an 'array'
	
	//$html =  '<div class = "dynamic_table">';
	$html =  '<TABLE id="static_contents_table" name="static_contents_table" '.$table_tags.' >';
	$html .= '<thead id="static_contents_thead" class="static_contents_thead" name="static_contents_thead">' .newline();
	$html .= '<tr>'.newline();
	for($i=0;$i<sizeof($table_def);$i++)
	{
		$html .= createTHFromTD_def($table_def[$i]);
	}
	$html .= '</tr>'.newline();

	$html .= '</thead>'.newline();
	//this is the body
	$html .=  '	<tbody id = "static_contents_tbody" name = "static_contents_tbody" class = "static_contents_tbody" ></tbody>';
	for($row=0;$row<sizeof($table_contents);$row++)
	{
		$html .= '<tr>'.newline();
		for($col=0;$col<sizeof($table_def);$col++)
		{
			//find the value here and pass it in?
			$table_def[$col]['value'] = (isset($table_contents[$row][$table_def[$col]['db_field']])) ? $table_contents[$row][$table_def[$col]['db_field']] : '';
			if ($table_def[$col]['type'] == 'row_number') $table_def[$col]['value'] = $row +1;
			if ($table_def[$col]['type'] == 'row_checkbox') $table_def[$col]['checkbox_index'] = $row;
			if ($table_def[$col]['type'] == 'checkbox')
			{
				$post_name = $table_def[$col]['db_field'] .'_' . $row;
				$html .= '<td><input  onchange="needToConfirm=true" type = "checkbox" id="'.$post_name .'" name="'.$post_name .'" ';
				if(isset($table_def[$col]['tags'])) $html .= $table_def[$col]['tags'];
				if(isset($table_def[$col]['value']))
				{
					if ($table_def[$col]['value'] == '1' || strtolower($table_def[$col]['value']) == 'true' || strtolower($table_def[$col]['value']) == 'checked' || strtolower($table_def[$col]['value']) == 'yes')
					{
						$html .=  ' checked = "checked" ';
					}
				}
				$html .= '/></td>' .newline();
			}
			elseif($table_def[$col]['type'] == 'hidden')
			{
			}
			else
			{
				$html .= createArrayTDFromTD_def($table_def[$col]);
			}
		}
		$html .= '</tr>'.newline();
	}
	//And the footer
	$html .=  '<tfoot id = "static_contents_tfoot" name = "static_contents_tfoot" class = "static_contents_tfoot">';
	//now we need to figure out which columns need a footer, and what are they?
	$html .= createTFootFromTD_def($table_def);

	$html .= '</tfoot>';
	$html .=  '</table>';
	

	$html .= '<script>var tbody_def = ' . prepareTableDefForJavascriptTableGeneration($table_def) . ';</script>';
	
	$html .=  '	<script>var contents_table = "static_contents_table";</script>';
	//I harde coded tbody_id into the javascript - so use it!
	$html .=  '	<script>var tbody_id = "static_contents_tbody";</script>';
	$html .=  '	<script>var contents_thead = "static_contents_thead";</script>';
	$html .=  '	<script>var contents_tfoot = "static_contents_tfoot";</script>';
	
	//prevent enter....
	$html .= '<script>$(document).ready(function() {
  $(window).keydown(function(event){
    if(event.keyCode == 13) {
      event.preventDefault();
      return false;
    }
  });
});
</script>';
	//$html .= '<script> var json_table_contents = ' . json_encode($table_contents) . ';</script>';	
	return $html;

}
function createFormForDynamicTableMYSQLInsert($table_def, $table_html, $form_handler, $complete_location, $cancel_location)
{
	$html = confirmNavigation();
	//$html .= includeJavascriptLibrary();
	$html .= '<form action="' . $form_handler.'" name = "form_id" id="form_id" method="post" onsubmit="return prepareDynamicTableForPost()">';
	$html.= $table_html;
	$html .= '<p><input class ="button" type="submit"  id = "submit" name="submit" value="Submit" onclick="needToConfirm=false;"/>' .newline();
	$html .= '<input class = "button" type="button" name="cancel" value="Cancel" onclick="cancelForm()"/>';
	$html .= createHiddenSerializedInput('table_def', prepareTableDefForPost($table_def));	
	$html .= createHiddenInput('complete_location', $complete_location);
	$html .= createHiddenInput('cancel_location', $cancel_location);
	//close the form
	$html .= '</form>' .newline();
	//drop the array out for form validation
	//$json_table_def = json_encode($table_def);
	$html .= '<script>var json_table_def = ' . prepareTableDefArrayForJavascriptVerification(array($table_def)) . ';</script>';
	//$html .= '<script>document.getElementsByName("' . $table_def[findElementToFocus($table_def)]['db_field'] .'")[0].focus();</script>';
	$html .= '<script>var complete_location = "' . $complete_location . '";</script>';
	$html .= '<script>var cancel_location = "' . $cancel_location . '";</script>';
		$html .= '<script>var formId = "form_id";</script>';

	return $html;
	
}
?>