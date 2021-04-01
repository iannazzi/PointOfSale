<?php
function makeDir($directory)
{
	/*mod settings: file: 0644
					folder: 0755*/
	if (!is_dir($directory ))
	{	
		if (!mkdir($directory, 0755, true))
		{
    		die('Failed to create folders...');
		}
	}
}
function copy_file($file, $newfile)
{
	if (!copy($file, $newfile)) 
	{
    	return "failed to copy $file...\n";
	}
}
function move_file($file, $newfile)
{
	//Attempts to rename oldname to newname, moving it between directories if necessary. If newname exists, it will be overwritten.
	rename($file, $newfile);
}
function delete_file($file)
{
	if(file_exists ( $file )	)
	{
		unlink($file);
	}
}
function get_files()
{
}
?>