<?php

function getCsvFileInArray($file_path_name, $delimiter = ',')
{
	$file_data = array();
	$file = fopen($file_path_name, 'r') or die("can't open file");
	$line_length = 0;
	$enclosure = '"';
	$escape ='\\';
	while($csv_line = fgetcsv($file, $line_length, $delimiter, $enclosure)) 
	{
		$file_data[] = $csv_line;
	}
	return $file_data;
}
function createCSVDelimeterSelect($delimiter)
{	

	$delimiters = array(';', ',');
	$name = 'delimiter';
	$html = '<select id = "'.$name.'" name="'.$name.'" ';

	$html .= '>';
	//Add an option for not selected
	for($i = 0;$i < sizeof($delimiters); $i++)
	{
		$html .= '<option value="' . $delimiters[$i] . '"';
		
		if ( ($delimiters[$i] == $delimiter) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $delimiters[$i] . '</option>';
	}
	$html .= '</select>';
	return $html;
}


function scrubTextUPCInput($input)
{
	$charachters_to_replace = array();
	$clean_input = fixOddCharacters($input);
	$clean_input = str_replace($charachters_to_replace, "", $clean_input);
	$clean_input =  scrubInput($clean_input);
	return $clean_input;
}
function scrubNumericUPCInput($input)
{
	$charachters_to_replace = array("'", "$", "\"");
	$clean_input =  str_replace($charachters_to_replace, "", $input);
	$clean_input = str_replace(',', '.',$clean_input);
	$clean_input = fixOddCharacters($clean_input);
	$clean_input =  scrubInput($clean_input);
	
	return $clean_input;
}
function fixOddCharacters($string)
{
/*$crap = array(
            'Á'=>'A', 'À'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Å'=>'A', 'Ä'=>'A', 'Æ'=>'AE', 'Ç'=>'C',
            'É'=>'E', 'È'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Í'=>'I', 'Ì'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ð'=>'Eth',
            'Ñ'=>'N', 'Ó'=>'O', 'Ò'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O',
            'Ú'=>'U', 'Ù'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y',
   
            'á'=>'a', 'à'=>'a', 'â'=>'a', 'ã'=>'a', 'å'=>'a', 'ä'=>'a', 'æ'=>'ae', 'ç'=>'c',
            'é'=>'e', 'è'=>'e', 'ê'=>'e', 'ë'=>'e', 'í'=>'i', 'ì'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'eth',
            'ñ'=>'n', 'ó'=>'o', 'ò'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o',
            'ú'=>'u', 'ù'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y',
           
            'ß'=>'sz', 'þ'=>'thorn', 'ÿ'=>'y'
        ); 
        //return strtr($string, $crap);
        return strtr($string,
 "\xe1\xc1\xe0\xc0\xe2	\xe4\xc4\xe3\xc3\xe5\xc5".
 "\xaa\xe7\xc7\xe9\xc9\xe8\xc8\xea\xca\xeb\xcb\xed".
 "\xcd\xec\xcc\xee\xce\xef\xcf\xf1\xd1\xf3\xd3\xf2".
 "\xd2\xf4\xd4\xf6\xd6\xf5\xd5\x8\xd8\xba\xf0\xfa\xda".
 "\xf9\xd9\xfb\xdb\xfc\xdc\xfd\xdd\xff\xe6\xc6\xdf\xf8",
 "aAaAaAaAaAaAacCeEeEeEeEiIiIiIiInNo".
 "OoOoOoOoOoOoouUuUuUuUyYyaAso");
 	*/
 	//return preg_replace("/[^\x9\xA\xD\x20-\x7F]/", "", $string);
 	return htmlentities(transcribe($string));
 	//return normaliza($string);
 	//return transcribe($string);
 //return  iconv('UTF-8', 'ISO-8859-1//IGNORE', $string);
  //return strtr($string,"äåéöúûü•µ¿¡¬√ƒ≈∆«»… ÀÃÕŒœ–—“”‘’÷ÿŸ⁄€‹›ﬂ‡·‚„‰ÂÊÁËÈÍÎÏÌÓÔÒÚÛÙıˆ¯˘˙˚¸˝ˇ","SOZsozYYuAAAAAAACEEEEIIIIDNOOOOOOUUUUYsaaaaaaaceeeeiiiionoooooouuuuyy");


}
function transcribe($string) {
    $string = strtr($string,
       "\xA1\xAA\xBA\xBF\xC0\xC1\xC2\xC3\xC5\xC7
        \xC8\xC9\xCA\xCB\xCC\xCD\xCE\xCF\xD0\xD1
        \xD2\xD3\xD4\xD5\xD8\xD9\xDA\xDB\xDD\xE0
        \xE1\xE2\xE3\xE5\xE7\xE8\xE9\xEA\xEB\xEC
        \xED\xEE\xEF\xF0\xF1\xF2\xF3\xF4\xF5\xF8
        \xF9\xFA\xFB\xFD\xFF",
        "!ao?AAAAAC
        EEEEIIIIDN
        OOOOOUUUYa
        aaaaceeeei
        iiidnooooo
        uuuyy");  
    $string = strtr($string, array("\xC4"=>"Ae", "\xC6"=>"AE", "\xD6"=>"Oe", "\xDC"=>"Ue", "\xDE"=>"TH", "\xDF"=>"ss", "\xE4"=>"ae", "\xE6"=>"ae", "\xF6"=>"oe", "\xFC"=>"ue", "\xFE"=>"th"));
    return($string);
}
function oldImport($file_array)
{		
		//set up the expected columns:
		$style_col = 0;
		$description_col = 1;
		$color_code_col = 2;
		$color_description_col = 3;
		$size_col = 4;
		$upc_col = 5;
		$cost_col = 6;
		$MRSP_col = 7;

		$file = fopen($file_array['path'], 'r') or die("can't open file");
		$column_headers = fgetcsv($file);
		while($csv_line = fgetcsv($file)) 
		{
			 $style_number = mysqli_real_escape_string($dbc, trim($csv_line[$style_col]));
			 $style_description = ucwords(strtolower(mysqli_real_escape_string($dbc, trim($csv_line[$description_col]))));
			 $color_code = strtoupper(mysqli_real_escape_string($dbc, trim($csv_line[$color_code_col])));
			 $color_description = ucwords(strtolower(mysqli_real_escape_string($dbc, trim($csv_line[$color_description_col]))));
			 $size = mysqli_real_escape_string($dbc, trim($csv_line[$size_col]));
			 $upc = mysqli_real_escape_string($dbc, trim($csv_line[$upc_col]));
			 $msrp = mysqli_real_escape_string($dbc, trim($csv_line[$MRSP_col]));
			 $cost = mysqli_real_escape_string($dbc, trim($csv_line[$cost_col]));
			 //$colors = mysqli_real_escape_string($dbc, trim($csv_line[$colors_col]));
			 //$colors_code_col = mysqli_real_escape_string($dbc, trim($csv_line[$color_codes_col]));
			 
			 //trim off the '$ from cost and retail then convert to a number
			 $charachters_to_replace = array("'", "$", "\"");
			 $cost = str_replace($charachters_to_replace, "", $cost);
			 $msrp = str_replace($charachters_to_replace, "", $msrp);
			 $upc = str_replace($charachters_to_replace, "", $upc);
			 $style_number = str_replace($charachters_to_replace, "", $style_number);
			 $color_code = str_replace($charachters_to_replace, "", $color_code);
			 $size = str_replace($charachters_to_replace, "", $size);
			 
			 //If the style number + brand combo exists then we need to update, other wise we need to insert
			$upc_replace_sql = "REPLACE INTO pos_manufacturer_upc ( upc_code, pos_manufacturer_id, date_added, style_number, style_description, color_code, color_description, size, msrp, cost, comments) VALUES ('$upc', '$pos_manufacturer_id', NOW(), '$style_number', '$style_description', '$color_code', '$color_description', '$size', '$msrp', '$cost', '')" ; 
			$upc_update_insert_sql = "INSERT INTO pos_manufacturer_upc (upc_code, pos_manufacturer_id, date_added, style_number, style_description, color_code, color_description, size, msrp, cost, comments) 
			VALUES ('$upc', '$pos_manufacturer_id', NOW(), '$style_number', '$style_description', '$color_code', '$color_description', '$size', '$msrp', '$cost', '') 
			ON DUPLICATE KEY UPDATE
			date_added = NOW(), 
			style_number = '$style_number', 
			style_description = '$style_description', 
			color_code = '$color_code', 
			color_description = '$color_description', 
			size = '$size', msrp = '$msrp', 
			cost = '$cost', 
			comments = ''";
			//echo '<p> Replace queue: ' . $upc_update_insert_sql . '</p>';
			$upc_update_insert_r = @mysqli_query ($dbc, $upc_update_insert_sql); // Run the query.
			if ($upc_update_insert_r) 	
			{	
					echo '<p>UPC # ' . $upc . ' has been inserted/updated</p>';
			}
			else
			{
					echo '<p class = "error" >Style # ' . $upc .  ' has been not been updated</p>';
			}
		}
		//All Done... redirect
}
?>