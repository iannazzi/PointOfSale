<?php
function scpFileToBluehost($input, $output)
{
	var_dump($input);
	var_dump($output);
	$connection = ssh2_connect('embrasse-moi.com', 22);
	ssh2_auth_password($connection, 'admin', 'emb14534MOI');
	ssh2_scp_send($connection, $input, $output, 0644);

	ssh2_exec($connection, 'exit');

	unlink($input);

}
function MYSQLArrayToCSVReadyArray($mysql_array)
{
	//$mysql_array[0]['pos_product_id'] = 17 etc....
	$csv_ready_array = array();
	$counter = 0;
	foreach($mysql_array[0] as $key => $value)
	{
		$csv_ready_array[0][$counter] = $key;
		$counter++;
	}
	$counter = 0;
	for($i=0;$i<sizeof($mysql_array);$i++)
	{
		foreach($mysql_array[$i] as $value)
		{
			$csv_ready_array[$i+1][$counter] = $value;
			$counter++;
		}
	}
	return $csv_ready_array;
}
function arrayToCsv($filename, $array, $delimiter = "\t")
{
	//this will give you the popup save as box. Outputs 2-d to tab separated
	header("Content-type: text/csv");   
	header('Content-Disposition: attachment; filename="' . $filename . '"');
	$outstream = fopen("php://output",'w'); 
	foreach( $array as $row )  
	{  
	    fputcsv($outstream, $row, $delimiter);  
	} 
	fclose($outstream);
}
function arrayToTABCSV($filepath, $array, $save_keys=false)
{
    $content = '';
    reset($array);
    while(list($key, $val) = each($array))
    {
        // replace tabs in keys and values to [space]
        $key = str_replace("\t", " ", $key);
        $val = str_replace("\t", " ", $val);
 
        if ($save_keys){ $content .=  $key."\t"; }
 
        // create line:
        $content .= (is_array($val)) ? implode("\t", $val) : $val;
        $content .= "\n";
    }
 
    if ($fp = fopen($filepath, 'w+'))
    {
        fwrite($fp, $content);
        fclose($fp);
    }
    else 
    { 
   		 return false; 
    }
    return true;
}
function fileUploadHandler($posted_name, $target_path)
{
		makeDir($target_path);
		//posted name comes from the name tag of the input box
		//$ target path: UPLOAD_FILE_PATH .'/uploads' etc..
		if ($_FILES[$posted_name]["error"] > 0)
		{
			trigger_error( "Error: " . $_FILES[$file_name]["error"] . "<br />");
			exit();
		}
		else
		{
			//echo "Upload: " .  . "<br />";
			//echo "Type: " . $_FILES["image_file_name"]["type"] . "<br />";
			//echo "Size: " . ($_FILES["image_file_name"]["size"] / 1024) . " Kb<br />";
			//echo "Stored in: " . $_FILES["image_file_name"]["tmp_name"];
			
		}
		$file_name = sanitizeFileName($_FILES[$posted_name]["name"]);
		$file_name_and_path = $target_path .'/'. sanitizeFileName(basename( $_FILES[$posted_name]['name'])); 
		if(move_uploaded_file($_FILES[$posted_name]['tmp_name'], $file_name_and_path)) 
		{
			//echo "<p>The file ".  basename( $_FILES['image_file_name']['name']). " has been uploaded</p>";
			$file['name'] = sanitizeFileName($_FILES[$posted_name]["name"]);
			$file['type'] = $_FILES[$posted_name]["type"];
			$file['size'] = $_FILES[$posted_name]["size"];
			$file['path'] = $file_name_and_path;
			
		} 
		else
		{
			trigger_error( "There was an error uploading the file, please try again!");
			exit();
		}		
		return $file;

}
function getFILEPostData($post_name_for_file, $upload_folder)
{
	if (isset($_FILES[$post_name_for_file]['size']) && $_FILES[$post_name_for_file]['size'] > 0)
	{
		$file_array = fileUploadHandler($post_name_for_file, $upload_folder);
		
		//$fileName = $_FILES[$post_name_for_file]['name'];
		//$tmpName  = $_FILES[$post_name_for_file]['tmp_name'];
		//$fileSize = $_FILES[$post_name_for_file]['size'];
		//$fileType = $_FILES[$post_name_for_file]['type'];
		
		$fp = fopen($file_array['path'], 'r');
		$content = fread($fp, filesize($file_array['path']));
		$content = addslashes($content);
		fclose($fp);
		if(!get_magic_quotes_gpc())
		{
			$file_array['name'] = addslashes($file_array['name']);
		}
		$file_data['file_name'] = $file_array['name'];
		$file_data['file_type'] = $file_array['type'];
		$file_data['file_size'] = $file_array['size'];
		$file_data['binary_content'] = $content;
		return $file_data;
	}	
	else
	{
		return false;
	}
}

?>