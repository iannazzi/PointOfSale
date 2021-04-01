<?php
/*
	Ahh the terminal... shove a cookie down it to get it an id......
	to register a terminal.... we need to have a list of terminals.
	There should be a number on the terminal. Select that number.
	select the terminal from the list and hit go....
	this will simply push a cookie into the web browser.
	
	
*/
$page_title = 'Terminals';
$binder_name = 'Terminals';
$access_type = 'WRITE';
require_once ('../system_functions.php');

$complete_location = 'list_terminals.php';
$cancel_location = 'list_terminals.php?message=Canceled';

if(isset($_POST['submit']))
{
	$pos_terminal_id = getPostOrGetID('pos_terminal_id');
	$cookie = getSingleValueSQL("SELECT cookie_name from pos_terminals WHERE pos_terminal_id=$pos_terminal_id");
	//i want / to be /POS which is pos url?
	setcookie ('pos_terminal_name',$cookie , time()+(10 * 365 * 24 * 60 * 60), '/', '', 0, 0);
	//go to the list with a message
	$message = 'message='.urlencode($cookie . ' Has Been Registered');
	header('Location: ' .addGetToURL($complete_location, $message));
	exit();
}
else if (isset($_POST['cancel']))
{
	$message = 'message=Canceled';
	header('Location: ' .addGetToURL($complete_location, $message));
	exit();
}
else
{
	//show the select....	
	$form_handler = 'register_terminal.php';
	$html = '<form action="' . $form_handler.'" id="form_id" method="post" ">';
	$html .= 'Select Terminal';
	$html .= createTerminalSelect('pos_terminal_id', 'false' , '');
	$html .= '<script>document.getElementById(\'pos_terminal_id\').focus()</script>';
	$html .= '<p><input class ="button" type="submit"   id = "submit" name="submit" value="Submit" onclick="needToConfirm=false;"/>' .newline();
	$html .= '<input class = "button" type="submit" id = "cancel" name="cancel"  value="Cancel" />';
	
	include (HEADER_FILE);
	echo $html;
	include (FOOTER_FILE);
}
function createTerminalSelect($name, $pos_terminal_id, $tags = ' onchange="needToConfirm=true" ')
{
	// use the default_store_id set on login to load a store. The company name should be selectable, then the address
	//option_all is used to add an 'all' option for the stores... this should be the default....
    //get the company info for the default store id
    $terminals = getSQL("SELECT pos_terminal_id, terminal_name, store_name, terminal_description FROM pos_terminals LEFT JOIN pos_stores USING (pos_store_id) ORDER BY store_name, terminal_name ASC");
    

	$html = '<select   name="' . $name . '" id="' . $name .'" ';
	$html .= $tags;
	$html .= '>';
	$html .= '<option value="false">No Terminal Selected</option>';
	
	for($i = 0;$i < sizeof($terminals); $i++)
	{
		$html .= '<option value="' . $terminals[$i]['pos_terminal_id'] . '"';
		//set the store to the default value or the selected value
		if ($terminals[$i]['pos_terminal_id'] == $pos_terminal_id) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $terminals[$i]['store_name'] . ' - ' . $terminals[$i]['terminal_name'] . ' - ' . $terminals[$i]['terminal_description'] . '</option>';
	}
	$html .= '</select>';
	return $html;
}
?>