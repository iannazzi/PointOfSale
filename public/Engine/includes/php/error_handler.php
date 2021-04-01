<?php
function pos_error_handler ($e_number, $e_message, $e_file, $e_line, $e_vars) 
{

	
	$includes = get_included_files();
	//var_dump( $includes);
	//echo 'WTF';
	// Build the error message.
	$message = "<p>Nice Job moron: A pos error occurred in script '$e_file' on line $e_line: $e_message\n<br />";
	// Add the date and time:
	$message .= "Date/Time: " . date('n-j-Y H:i:s') . "\n<br />";
	// Append $e_vars to the $message:
	$message .= "<pre>Error Variables:<br />" . print_r ($e_vars, 1) . "</pre>\n</p>";
	// what is the url?
	$message .= "<p><pre>URL: " .getPageURL() 	. "</pre>\n</p>";
	$message .= '<pre>Session variables:<br />' . print_r($_SESSION,1). "</pre>\n</p>";
	$message .= '<pre>Bactrace:<br />' . print_r(debug_backtrace(),1). "</pre>\n</p>";
	if (!LIVE) 
	{ 
	// Send an email to the admin:

		$subject = 'TEST Site Error!';
		//mail(SUPPORT_EMAIL, $subject, $message, 'From: ' . SUPPORT_EMAIL);
		switfMailIt(SUPPORT_EMAIL, SUPPORT_EMAIL, $subject, $message);
		// Development (print the error).
		
		echo '<div class="error">' . $message . '</div><br />';
		

	}
	else 
	{
		echo '<div class="error">' . $message . '</div><br />';
		// Send an email to the admin:
		//mail(SUPPORT_EMAIL, 'Site Error!', $message, 'From: ' . SUPPORT_EMAIL);
		$subject = 'pos.craiglorious.com Site Error!';
		//mail(SUPPORT_EMAIL, $subject, $message, 'From: ' . SUPPORT_EMAIL);
		switfMailIt(SUPPORT_EMAIL, SUPPORT_EMAIL, $subject, $message);
		// Only print an error message if the error isn't a notice:
		include(HEADER_FILE);
		if ($e_number != E_NOTICE) 
		{
			echo '<div class="error">A system error occurred. We apologize for the inconvenience.</div><br />';
		}
		echo '<div class="error">An Error has occurred and has been emailed to the administrator. Please note the conditions that caused this error and send a note to the administrator</div><br />';
	}
	//include(FOOTER_FILE);
	exit();
}
function extra_debug($value=''){
    $btr=debug_backtrace();
    $line=$btr[0]['line'];
    $file=basename($btr[0]['file']);
    $msg = "<pre>$file:$line</pre>\n";
    if(is_array($value)){
        $msg .="<pre>";
        $msg .=($value);
        $msg .="</pre>\n";
    }elseif(is_object($value)){
        $value.dump();
    }else{
        $msg .=("<p>&gt;${value}&lt;</p>");
    }
    return $msg;
} 
?>