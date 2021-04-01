<?php
function createSwift()
{
	require(SWIFT_MAILER);
//
//	$password = 'DOC#1264-6010';
//	$username = 'emily.norris@embrasse-moi.com';
//	$smtp = 'mail.embrasse-moi.com';
//	$port = 26;
//	$transport = Swift_SmtpTransport::newInstance($smtp, $port)
//		->setUsername($username)
//		->setPassword($password)
//	;

	$password = '666-Darkness-light';
	$username = 'accounting@embrasse-moi.com';
	$smtp = 	'server150.web-hosting.com';
	$port = 465;
	$transport = Swift_SmtpTransport::newInstance($smtp, $port, 'ssl')
			->setUsername($username)
			->setPassword($password)
		;


//	$password = 'emb14534MOI';
//	$username = 'craig.iannazzi@gmail.com';
//	$smtp = 'smtp.gmail.com';
//	$port = 465;
//
//	$transport = Swift_SmtpTransport::newInstance($smtp, $port, 'ssl')
//		->setUsername($username)
//		->setPassword($password);


	$mailer = Swift_Mailer::newInstance($transport);
	return $mailer;
}
function switfMailIt($from, $to, $subject, $msg, $cc = '')
{

	$mailer = createSwift();
	//var_dump(fixSwiftEmail($to)); exit();
	$message = Swift_Message::newInstance($subject)
		->setContentType("text/html")
		//->setFrom(fixSwiftEmail($from))
		->setFrom(array('accounting@embrasse-moi.com' => 'Embrasse-Moi'))
		->setReplyTo(fixSwiftEmail($from))
		->setTo(fixSwiftEmail($to))
		->setBody($msg)
	;
	if ($cc != '')
	{
		$message->setCc(fixSwiftEmail($cc));
	}

	$result = $mailer->send($message);
	//var_dump ($result);exit();
}
function fixSwiftEmail($email)
{
	if(strpos($email,','))
	{
		$output = [];
		$array = explode(',',$email);
		$array = array_map('trim', $array);
		foreach($array as $single_email)
		{
			if( !Swift_Validate::email($single_email) ){
				echo 'error: email ' . $single_email . ' is not valid';
				exit();
			}
			$output[$single_email] = $single_email;
		}
		return $output;
	}

	return array($email);
}
function createEmailHTMLArrayTable($table_def_array_with_data, $class, $rows_to_highlight_red = array())
{	
	if (sizeof($table_def_array_with_data) > 0) 
	{ 
		//check to see if there are totals to add on the bottom
		$totals = false;
		foreach($table_def_array_with_data[0] as $td_array)
		{
			if (isset($td_array['total']))
			{
				$totals=true;
			}
		}
		$style = emailTableClass($class);
		$html = '<div style="padding:0;margin: 0;border:0;">';
		$html = '<table '.$style['table'] . '>' .newline();
		$html .= '<thead '.$style['thead'] . '><tr>' . newline();
		foreach($table_def_array_with_data[0] as $td_array)
		{
			$html .= '<th '.$style['thead_th'] . '>'.$td_array['th'] . '</th>';
		}
		$html .= '</tr></thead>'.newline();
		
		$html .= '<tbody '.$style['tbody'] . ' >';
		$even = false;
		for ($i=0;$i<sizeof($table_def_array_with_data);$i++)
		{
			$html .= '<tr>';
			
			if (in_array($i, $rows_to_highlight_red))
			{
				$tmp_style = $style['tbody_td_error'];
			}
			elseif ($even)
  			{
    			//$html .= '<tr '. $style['even_row'] .'>';
    			$tmp_style = $style['tbody_td_even'];
  			}
 			else
 			{
    			//$html .= '<tr '. $style['odd_row'] .'>';
    			$tmp_style = $style['tbody_td_odd'];
  			}
 			$even = !$even;
 		
			foreach($table_def_array_with_data[$i] as $td_array)
			{
					//$html .= '<td '.$style['tbody_td'] . '>'.$td_array['value'] . '</td>';
					$html .= '<td '.$tmp_style . '>'.$td_array['value'] . '</td>';
			}
			$html .= '</tr>'.newline();
		}
		$html .= '</tbody>';
		// if there are totals add them here
		if($totals == true)
		{
			$html .= '<tfoot '.$style['tfoot'] . '>';
			$html .= '<tr>';
			$column_counter = 0;
			$html .= '<td>Totals</td>';
			for($t=1;$t<sizeof($table_def_array_with_data[0]);$t++)
			//foreach($table_def_array_with_data[0] as $td_array)
			{
				$column_counter++;
				
				if(isset($table_def_array_with_data[0][$t]['total']))
				{	
					$html .= '<td '.$style['lined_cell'] . '>' .number_format(calculateSQLTotalForTableDefARray($table_def_array_with_data, $t),$table_def_array_with_data[0][$t]['total']).'</td>';
					$column_counter = 0;
				}
				else
				{
					$html .= '<td></td>';
				}
			}
			$html .= '</tr></tfoot>';
		}			
		$html .= '</table>'.newline(); // Close the table.
	} 
	else 
	{ // If no records were returned.
		$html = '<p class="error">There are currently no records.</p>';
	}
	return $html;
}
function textAreaEmailHtmlTable($title, $text, $table_class = 'textareaTable')
{
	$style = textareaEmailStyle();
	$html = '<table '.$style['table'] .'>';
	if ($title != '')
	{
		$html .='<thead>';
		$html .= '<tr><th'.$style['thead_th'] .'>'.$title.'</th></tr>';
		$html .= '</thead>';
	}
	$html .= '<tbody'.$style['tbody'] .'>';
	$html .= '<tr><td'.$style['tbody_td'] .'>'.nl2br($text).'</td></tr>';
	$html .= '</tbody></table>';
	return $html;
}
function textareaEmailStyle()
{
	$style['table'] = ' style ="width: 100%;
		padding: 0px 0px 0px 0px;
		margin: 0;
		border-width: 0 0 0px 0px;
   		border-spacing: 0;
    	border-collapse: collapse;
	" ';
	$style['tbody'] = ' style ="
	   margin: 0;
	   padding: 0;
	   border-left:  1px solid black; 
	   border-right:  1px solid black; 
	   border-top:  1px solid black; 
	   border-bottom:  1px solid black; 
		text-align:center;
		" ';
	$style['tbody_td'] = ' style ="
		background: #FF9999;
		text-align:left;
		border-left:  1px solid black; 
	   border-right:  1px solid black; 
	   border-top:  1px solid black; 
	   border-bottom:  1px solid black; 
" ';
$style['thead_th'] = ' style ="
		text-align:left;
		padding: 0px 0px 0px 0px;
		border-left:  1px solid black; 
	   border-right:  1px solid black; 
	   border-top:  1px solid black; 
	   border-bottom:  1px solid black; 
	" ';
	return $style;
}
function emailTableClass($class)
{
	switch ($class) 
	{
    case 'purchase_order':
		
		$style['table'] = ' style ="
			width:100%;
			font-family:verdana,arial,helvetica,sans-serif; 
			padding: 0;
			margin: 0;
			border-collapse: collapse;" ';
		$style['thead'] = '';
		$style['thead_th'] = ' style = "	
			margin: 0;
		   padding: 0;
		   border-left:  1px solid black; 
		   border-right:  1px solid black; 
		   border-top:  1px solid black; 
		   border-bottom:  2px solid black; 
			line-height:10px;
			text-align:center;
			font-size:10px;" ';
		$style['tbody'] = 'style = "
			border-left:  1px solid black; 
		   border-right:  1px solid black; 
		   border-top:  1px solid  black; 
		   border-bottom:  1px solid black" ';
		$style['tbody_td'] =  '
		style = "border-left:  1px solid black; 
		   border-right:  1px solid black; 
		   border-top:  1px solid black; 
		   border-bottom:  1px solid black; 
	
			text-align:center;
			vertical-align: middle;
			padding: 0px;
			margin: 0px;
			font-size: 0.8em;" ';
		$style['tbody_td_even'] =  '
		style = "
			background: RGB(240,240,240);
			border-left:  1px solid black; 
		   border-right:  1px solid black; 
		   border-top:  1px solid black; 
		   border-bottom:  1px solid black; 
	
			text-align:center;
			vertical-align: middle;
			padding: 0px;
			margin: 0px;
			font-size: 0.8em;" ';
		$style['tbody_td_odd'] =  '
		style = "
			background: #FFF;
			border-left:  1px solid black; 
		   border-right:  1px solid black; 
		   border-top:  1px solid black; 
		   border-bottom:  1px solid black; 
	
			text-align:center;
			vertical-align: middle;
			padding: 0px;
			margin: 0px;
			font-size: 0.8em;" ';
		$style['tbody_td_error'] = ' style = "
			background: #FF9999;
			border-left:  1px solid black; 
		   border-right:  1px solid black; 
		   border-top:  1px solid black; 
		   border-bottom:  1px solid black; 
	
			text-align:center;
			vertical-align: middle;
			padding: 0px;
			margin: 0px;
			font-size: 0.8em;" ';
		$style['empty_cell'] = 	' style = "
			border-top: 0px none black; 
			border-left:  0px none black; 
			border-right:  0px none black; 
			border-bottom:  0px none black;" ';
		$style['lined_cell'] = 	' style = "
			text-align:center;
			border-left:  1px solid rgb(50,50,50); 
		   border-right:  1px solid rgb(50,50,50); 
		   border-top:  1px solid rgb(50,50,50); 
		   border-bottom:  1px solid rgb(50,50,50);" ';
	
		$style['tfoot'] = ' style="
		   border-left:  1px solid black; 
		   border-right:  1px solid black; 
		   border-top:  2px solid black; 
		   border-bottom:  1px solid black; 
	
			text-align:center;
			vertical-align: bottom;
			padding: 0px;
			margin: 0px;
			font-size: 0.8em;
			color: rgb(0,0,0);
			font-weight: bold;" ';
        break;
    case 'no lines':
        //echo "i equals 1";
        break;
    case 'hmm':
        //echo "i equals 2";
        break;
	}
    return $style;
}


?>