<?php
/* the system library is for the overall system functions, features, configurations...

	configurations we might need:
	version of code
	admin user name and password
	billing information
	company name
	Those are all listed in 'god'
	
	what about....
	logo and font
	an image file
	discalimer text on the invoice
	return policy
	those should be "hard files"?
	
	etc......
	however this is not 'really' a database function as there is no index?
	we do need to know what the 'system_id' is from a 'god' perspecitve....
*/

function createPrinterSelect($name, $pos_printer_id, $tags = ' onchange="needToConfirm=true" ')
{
	// use the default_store_id set on login to load a store. The company name should be selectable, then the address
	//option_all is used to add an 'all' option for the stores... this should be the default....
    //get the company info for the default store id
    $printers = getSQL("SELECT pos_printer_id, printer_name, location, store_name, printer_description FROM pos_printers LEFT JOIN pos_stores USING (pos_store_id) ORDER BY store_name, printer_name ASC");
    

	$html = '<select   name="' . $name . '" id="' . $name .'" ';
	$html .= $tags;
	$html .= '>';
	$html .= '<option value="false">No Printer Selected</option>';
	
	for($i = 0;$i < sizeof($printers); $i++)
	{
		$html .= '<option value="' . $printers[$i]['pos_printer_id'] . '"';
		//set the store to the default value or the selected value
		if ($printers[$i]['pos_printer_id'] == $printers) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $printers[$i]['store_name'] . ' - ' . $printers[$i]['printer_name'] . ' - ' . $printers[$i]['printer_description'] . ' - ' .  $printers[$i]['location'] .  '</option>';
	}
	$html .= '</select>';
	return $html;
}
function createCheckingPrinterSelect($name, $pos_printer_id, $pos_account_id, $tags = ' onchange="needToConfirm=true" ')
{
	// use the default_store_id set on login to load a store. The company name should be selectable, then the address
	//option_all is used to add an 'all' option for the stores... this should be the default....
    //get the company info for the default store id
    $printers = getSQL("SELECT pos_printer_id, printer_name, location, store_name, printer_description FROM pos_printers LEFT JOIN pos_stores USING (pos_store_id) WHERE pos_account_id = $pos_account_id ORDER BY store_name, printer_name ASC");
    

	$html = '<select   name="' . $name . '" id="' . $name .'" ';
	$html .= $tags;
	$html .= '>';
	$html .= '<option value="false">No Printer Selected</option>';
	
	for($i = 0;$i < sizeof($printers); $i++)
	{
		$html .= '<option value="' . $printers[$i]['pos_printer_id'] . '"';
		//set the store to the default value or the selected value
		if ($printers[$i]['pos_printer_id'] == $printers) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $printers[$i]['store_name'] . ' - ' . $printers[$i]['printer_name'] . ' - ' . $printers[$i]['printer_description'] . ' - ' .  $printers[$i]['location'] .  '</option>';
	}
	$html .= '</select>';
	return $html;
}

function getSetting($name)
{
	return getSingleValueSQL("SELECT value FROM pos_settings WHERE name='$name'");
}
function getTerminalCookie()
{
	if(isset($_COOKIE['pos_terminal_name']))
	{
		return $_COOKIE['pos_terminal_name'];
	}
	else
	{
		return false;
		//return 'NOT A REGISTERED TERMINAL';
	}
}
function getTerminalID($terminal_name)
{
	if($terminal_name)
	{
		$pos_terminal_id = getSingleValueSQL("SELECT pos_terminal_id FROM pos_terminals WHERE terminal_name = '$terminal_name'");
		return $pos_terminal_id;
	}
	else
	{
		return 0;
	}

}
function getDefaultTerminalPrinter($pos_terminal_id)
{
	$pos_printer_id = getSingleValueSQL("SELECT pos_printer_id FROM pos_terminals WHERE pos_terminal_id = $pos_terminal_id");
	return $pos_printer_id;
}
function getPrinterName($pos_printer_id)
{
	$name = getSingleValueSQL("SELECT printer_name FROM pos_printers WHERE pos_printer_id = $pos_printer_id");
	return $name;
}
function getPrinterFullName($pos_printer_id)
{
	$name = getSingleValueSQL("SELECT CONCAT(store_name,' ', printer_description, ' ', location, ' ', printer_name) FROM pos_printers LEFT JOIN pos_stores USING(pos_store_id)  WHERE pos_printer_id = $pos_printer_id");
	return $name;
}
function getPrinterLocation($pos_printer_id)
{
	$name = getSingleValueSQL("SELECT location FROM pos_printers  WHERE pos_printer_id = $pos_printer_id");
	return $name;
}
function terminalCheck()
{
//terminal check
	if (!getTerminalCookie())
	{
		include(HEADER_FILE);
		echo '<p class="error">ERROR - POS Terminal is not set-up. Contact System Admin to add this system as a terminal. A system consists of both a computer and a web-browser. Safari, Firefox, and Chrome all need to be independantly set up on a computer.</p>';
		include(FOOTER_FILE);
		exit();
	}
	else
	{	
		$pos_terminal_id = getTerminalID(getTerminalCookie());
		if(getDefaultTerminalPrinter($pos_terminal_id)	== 0)
		{
			include(HEADER_FILE);
			echo '<p class="error">ERROR - POS Terminal Printer is not set-up. Contact System Admin to add a printer to this system. </p>';
			include(FOOTER_FILE);
			exit();
		}
	}
	return $pos_terminal_id;
}
function getTerminalPrinters($pos_terminal_id)
{
	$pos_printer_ids = getSQL("SELECT pos_printer_id FROM pos_terminals_printers WHERE pos_terminal_id = $pos_terminal_id");
	return $pos_printer_ids;
}
function checkTerminal()
{
	if  (getTerminalCookie())
	{
		if(getTerminalID(getTerminalCookie()) != 0)
		{
			return getTerminalID(getTerminalCookie());
		}
		else
		{
			return false;
		}
	}
	else
	{
		return false;
	}
}
function getTerminalStoreId($pos_terminal_id)
{
	return getSingleValueSQL("SELECT pos_store_id FROM pos_terminals WHERE pos_terminal_id = $pos_terminal_id");
}
function getTerminalName($pos_terminal_id)
{
	if($pos_terminal_id == 0)
	{
		return 'NOT A REGISTERED TERMINAL';
	}
	else
	{
		return getSingleValueSQL("SELECT terminal_name FROM pos_terminals WHERE pos_terminal_id = $pos_terminal_id");
	}
}
function generateUniqueName($length, $charset = '0123456789')
{


	$key = '';
	for($i=0; $i<$length; $i++)
	{
	 $key .= $charset[(mt_rand(0,(strlen($charset)-1)))]; 
	}
	return $key;

}
function getSafeCharset()
{
			$charset = "ABCDEFGHJKMNPRSTWXY345678";
			return $charset;

}
?>