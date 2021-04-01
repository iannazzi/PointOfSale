<?php

/*
	this will store the "changes" in an autosave field.....
	
*/

$page_title = 'Documents';
$binder_name = 'Documents';
$access_type = 'WRITE';
require_once ('../office_functions.php');


//user types a bunch of stufff.... does not save it.... comes back in.... DO NOT LET AUTO SAVE ENGAGE
//upon save clear out the autosave text.... if this text has something in it we will not overwrite it....
//but then we cannot use auto save.....
//so we need a "saved" flag
//uncheck the save flag upon load
//check the save check upon save
//if the saved flag is checked


//$auto_save_text

$document_text = scrubInput($_POST['document_text']);
$pos_document_id = $_POST['pos_document_id'];

$sql = "UPDATE pos_documents SET auto_save_document_text	 = '" . $document_text . "' WHERE pos_document_id=$pos_document_id";
runSQL($sql);
echo 'STORED';


?>