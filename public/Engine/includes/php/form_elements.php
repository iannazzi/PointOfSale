<?php	


/************************************FORM HELPERS ****************************************/
function backButton()
{
	$html = '<input class = "button" type="button" value="Back" onclick="window.history.back()">';
	return $html;
}
function checkPostErrors()
{
	//call this function when checking post data
	//this is fail safe stuff in case javascript form validation fails....
	checkForDoubleSubmit();
	postTimer();
	checkForCorrectInput();
	
}
function printErrors($errors)
{
	$html = '';
	for ($i=0;$i<sizeof($errors);$i++)
	{
		$html .= '<p class="error">' . $errors[$i] .'</p>'.newline();
	}
	return $html;
}
function checkForCorrectInput()
{
	//this will use the same code as the javascript form validator.... 
	//basically find the table def, blast through it and make sure the numbers are numbers, dates are dates, etc..
}
function checkForDoubleSubmit()
{
	 
	//this won't work correctly if the user posts from one page, while leaving the other page open...
	/*if (isset($_SESSION['double_submit_token']))
	{
		if (isset($_POST['double_submit_token']))
		{
			if ($_POST['double_submit_token'] != $_SESSION['double_submit_token'])
			{
				// double submit
				trigger_error('double submit caught and defeated by post token');
			}
			else
			{
				$_SESSION['double_submit_token'] = md5(session_id() . time());
			}
		}
	}*/
}
function postTimer()
{
    if (isset($_SESSION['posttimer']))
    {
        if ( (time() - $_SESSION['posttimer']) <= 2)
        {
            // less then 2 seconds since last post
            trigger_error('double post caught by post timer');
        }
        else
        {
            // more than 2 seconds since last post do nothing
        }
    }
    $_SESSION['posttimer'] = time();
}
function url_blank_link($link, $text)
{
	$html = '<a href="'.$link.'" target="_blank">'.$text.'</a>';
	return $html;
}
function createGeneralJournalLink($pos_general_journal_id)
{
	return url_blank_link(POS_ENGINE_URL . '/accounting/GeneralJournal/view_general_journal_entry.php?pos_general_journal_id=' .$pos_general_journal_id, $pos_general_journal_id);
}
function createPJLink($pos_purchase_journal_id)
{
	return url_blank_link(POS_ENGINE_URL . '/accounting/PurchaseJournal/view_purchase_invoice_to_journal.php?pos_purchase_journal_id=' .$pos_purchase_journal_id, $pos_purchase_journal_id);
}
function createPaymentJournalLink($pos_payments_journal_id)
{
	return url_blank_link(POS_ENGINE_URL . '/accounting/PaymentsJournal/view_payments_journal_entry.php?pos_payments_journal_id=' .$pos_payments_journal_id, $pos_payments_journal_id);
}
function createPOLink($pos_purchase_order_id)
{
	return url_blank_link(POS_ENGINE_URL . '/purchase_orders/ViewPurchaseOrder/view_purchase_order.php?pos_purchase_order_id=' .$pos_purchase_order_id, $pos_purchase_order_id);
}
function accountURLLink($pos_account_id)
{
	return url_blank_link(POS_ENGINE_URL . '/accounting/Accounts/view_account.php?pos_account_id=' .$pos_account_id, 'System ID: ' .$pos_account_id);
}
function manufacturerUPCLink($pos_manufacturer_brand_id, $text)
{
	$pos_manufacturer_id = getManufacturerIdFromBrandId($pos_manufacturer_brand_id);
	$pos_manufacturer_brand_name = getBrandName($pos_manufacturer_brand_id);
	return url_blank_link(POS_ENGINE_URL . '/manufacturers/ManufacturerUPC/list_upcs.php?pos_manufacturer_id='.$pos_manufacturer_id, $text);
}
function convert1_0ToYesNo($active)
{
	if($active == 1)
	{
		return 'Yes';
	}
	else
	{
		return 'No';
	}
}
function confirmDelete($delete_location)
{
	$html = '<script>

		function confirmDelete()
		{
			if(confirm("Certain about that delete?"))
			{
				needToConfirm = false;
				open_win(\''.$delete_location.'\');
			}
		}
		</script>';
	return $html;

}
function confirmJournalDelete($delete_location)
{
	$html = '<script>

		function confirmJournalDelete()
		{
			if(confirm("This will delete all Payments as well. Certain about that delete?"))
			{
				needToConfirm = false;
				open_win(\''.$delete_location.'\');
			}
		}
		</script>';
	return $html;

}
function getCurrentTime()
{
	return date("Y-m-d H:i:s");
}
function browser_os_detect()
{
	/* ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ */
	/* Script written by Adam Khoury at www.developphp.com */
	/* VIDEO GUIDE - http://www.developphp.com/view.php?tid=1057 */
	/* See video guide above for full explanation if you want full understanding */
	/* ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ */
	// Obtain user agent which is a long string not meant for human reading
	$agent = $_SERVER['HTTP_USER_AGENT']; 
	// Get the user Browser now -----------------------------------------------------
	// Create the Associative Array for the browsers we want to sniff out
	$browserArray = array(
			'Windows Mobile' => 'IEMobile',
		'Android Mobile' => 'Android',
		'iPhone Mobile' => 'iPhone',
		'Firefox' => 'Firefox',
			'Google Chrome' => 'Chrome',
			'Internet Explorer' => 'MSIE',
			'Opera' => 'Opera',
			'Safari' => 'Safari'
	); 
	foreach ($browserArray as $k => $v) {
	
		if (preg_match("/$v/", $agent)) {
			 break;
		}	else {
		 $k = "Browser Unknown";
		}
	} 
	$browser = $k;
	// -----------------------------------------------------------------------------------------
	// Get the user OS now ------------------------------------------------------------
	// Create the Associative Array for the Operating Systems to sniff out
	$osArray = array(
			'Windows 98' => '(Win98)|(Windows 98)',
			'Windows 2000' => '(Windows 2000)|(Windows NT 5.0)',
		'Windows ME' => 'Windows ME',
			'Windows XP' => '(Windows XP)|(Windows NT 5.1)',
			'Windows Vista' => 'Windows NT 6.0',
			'Windows 7' => '(Windows NT 6.1)|(Windows NT 7.0)',
			'Windows NT 4.0' => '(WinNT)|(Windows NT 4.0)|(WinNT4.0)|(Windows NT)',
		'Linux' => '(X11)|(Linux)',
		'Mac OS' => '(Mac_PowerPC)|(Macintosh)|(Mac OS)'
	); 
	foreach ($osArray as $k => $v) {
	
		if (preg_match("/$v/", $agent)) {
			 break;
		}	else {
		 $k = "Unknown OS";
		}
	} 
	$os = $k;
	// At this point you can do what you wish with both the OS and browser acquired
	
	return array('browser' => $browser, 'os' => $os);

}
function addBeepV3()
{
	 $html = '<div id="sound_file"></div>';
	 $html .= '<script> var SUCCESS_BEEP_FILENAME = "'.SUCCESS_BEEP_FILENAME.'";</script>';
	 $html .= '<script> var ERROR_BEEP_FILENAME = "'.ERROR_BEEP_FILENAME.'";</script>';
	 return $html;
}

function addBeepV2()
{

		$html = '<audio id="success_beep"  preload="auto">';
		$html.=  '<source src="'.SUCCESS_BEEP_FILE_MP3.'" type="audio/mpeg">';
		$html.=  '<source src="'.SUCCESS_BEEP_FILE_OGG.'" type="audio/ogg">';
		$html .= '<embed src="'.SUCCESS_BEEP_FILE.'" autostart="false" width="0" height="0" id="success_beep"
enablejavascript="true">';
		$html .= '</audio>';
		$html .= '<audio id="error_beep"  preload="auto">';
		$html.=  '<source src="'.ERROR_BEEP_FILE_MP3.'" type="audio/mpeg">';
		$html.=  '<source src="'.ERROR_BEEP_FILE_OGG.'" type="audio/ogg">';
		$html .= '<embed src="'.ERROR_BEEP_FILE.'" autostart="false" width="0" height="0" id="error_beep"
enablejavascript="true">';
		$html .= '</audio>';
		

	return $html;
}
function addBeep()
{
	//here we need to detect the browser, posibly the os, then set-up based on that....
	$browser = browser_os_detect();
	//preprint($browser);
	if($browser['browser'] == 'Firefox')
	{
		$html = '<embed src="'.SUCCESS_BEEP_FILE.'" autostart="false" width="0" height="0" id="success_beep"
enablejavascript="true">' .newline();
		$html .= '<embed src="'.ERROR_BEEP_FILE.'" autostart="false" width="0" height="0" id="error_beep"
enablejavascript="true">'.newline();
	}
	else if($browser['browser'] == 'Safari')
	{
		$html = '<embed src="'.SUCCESS_BEEP_FILE.'" autostart="false" width="0" height="0" id="success_beep"
enablejavascript="true">' .newline();
		$html .= '<embed src="'.ERROR_BEEP_FILE.'" autostart="false" width="0" height="0" id="error_beep"
enablejavascript="true">'.newline();
	}
	else if($browser['browser'] == 'Google Chrome')
	{
		$html = '<audio id="success_beep" src="'.SUCCESS_BEEP_FILE.'" preload="auto"></audio>';
		$html .= '<audio id="error_beep" src="'.ERROR_BEEP_FILE.'" preload="auto"></audio>';
		
  				
				
	}
	else
	{
		$html = '<embed src="'.SUCCESS_BEEP_FILE.'" autostart="false" width="0" height="0" id="success_beep"
enablejavascript="true">' .newline();
		$html .= '<embed src="'.ERROR_BEEP_FILE.'" autostart="false" width="0" height="0" id="error_beep"
enablejavascript="true">'.newline();
	}
	
	$html .= '<script> var browser = "'.$browser['browser'].'" </script>';
	
//$html .= '<script> var SUCCESS_BEEP_FILE = "'. SUCCESS_BEEP_FILE .'"; var ERROR_BEEP_FILE = "'.ERROR_BEEP_FILE.'";</script>';
	return $html;
}
function checkInputAto0()
{
	return ' onkeyup="checkInput(this,\'0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ\')" ';
}
function checkInputAlphaNumeric()
{
	return ' onkeyup="checkInput2(this,\'0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz\')" ';
}
function uppercase()
{
	return 'ABCEDFGHIJKLMNOPQRSTUVWXYZ';
}
function lowercase()
{
	return strtolower(uppercase());
}
function integers()
{
	return '0123456789';
}
function safesymbols()
{
	return ';:|-_=+-*,\'"()&%$#@!<>.?';
}
function numbersOnly()
{
	return ' onkeyup="checkInput(this,\'+-.0123456789\')" ';
}
function positiveNumbersOnly()
{
	return ' onkeyup="checkInput(this,\'.0123456789\')" ';
}
function integersOnly()
{
	return ' onkeyup="checkInput(this,\'-0123456789\')" ';
}
function getPageURL() 
{
	$isHTTPS = (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on");
	$port = (isset($_SERVER["SERVER_PORT"]) && ((!$isHTTPS && $_SERVER["SERVER_PORT"] != "80") || ($isHTTPS && $_SERVER["SERVER_PORT"] != "443")));
	$port = ($port) ? ':'.$_SERVER["SERVER_PORT"] : '';
	$url = ($isHTTPS ? 'https://' : 'http://').$_SERVER["SERVER_NAME"].$port.$_SERVER["REQUEST_URI"];
	return $url;
}
function removeGetValue($url,$key)
{ 
	$remove_str = '?';
	$rmv_pos = strpos($url, $remove_str);
	if ($rmv_pos !== false) 
	{
		$parts = parse_url($url);
		$queryParams = array();
		if(isset($parts['query']))
		{
			parse_str($parts['query'], $queryParams);
			unset($queryParams[$key]);
		}
		$queryString = http_build_query($queryParams);
		
		$lengthRemove = strlen($remove_str);
		$lengthUrl=strlen($url);
		$inputStart=substr($url,0,$rmv_pos);
		$main_url =  $inputStart; 
		$url = ($queryString != '') ? $main_url . '?' . $queryString : $main_url;
	} 
	else
	{
	}

	return $url;
	
}
function convertUrlQuery($query) {
    $queryParts = explode('&', $query);
   
    $params = array();
    foreach ($queryParts as $param) {
        $item = explode('=', $param);
        $params[$item[0]] = $item[1];
    }
   
    return $params;
} 
function getRef()
{
	return urldecode(getPostOrGetValue('ref'));
}
function createRefGet()
{
	$ref = 'ref=' . urlencode(removeGetValue(getPageURL(),'ref'));
	return $ref;
}
function createRef($ref)
{
	return $ref = 'ref=' . urlencode($ref);
}
function getOrderSortUrl()
{
	$url = getPageUrl();
	//remove order;
	$url = removeGetValue($url, 'order');
	$url = removeGetValue($url, 'sort');
	
	$separator = "?";
	if (strpos($url,"?")!=false)
	{
  			$separator = "&";
  	}
	$newUrl = $url . $separator; 
	return $newUrl;
}
function getPageURLwithGETS()
{
	$url = getPageUrl();
	$separator = "?";
	if (strpos($url,"?")!=false)
	{
  			$separator = "&";
  	}
	$newUrl = $url . $separator; 
	return $newUrl;
}
function saveAndRedirectSearchFormUrl($search_fields, $session_variable_name, $date_field = '')
{
	$search_set = false;
	//this will tell us if the url has a search set on it
	foreach($_GET as $key => $value)
	{
		foreach($search_fields as $field)
		{
			if ($key == $field['db_field'] && $key != $date_field)
			{
				$search_set = true;
			}
		}
		if ($key == 'order' OR $key == 'sort')
		{
			$search_set = true;
		}
	}
	//ok we might be incoming with a message. If that is the case rebuild the string with the message....
	
	if(isset($_GET['message']))
	{
		if($search_set)
		{
			//ok we have a saved search and a message
			//this is good to pass through
		}
		else
		{
			//there is a message, but no information on a search.
			if(isset($_SESSION[$session_variable_name]) )
			{
				//there is a saved search that needs to be activated
				
				//instead of re-direct why not set the $_GET variable...
				
				
				$message = 'message='. urlencode($_GET['message']);
				$new_url = addGetToUrl($_SESSION[$session_variable_name], $message);
				/*$parts = parse_url($new_url);
				$query = convertUrlQuery($parts['query']);
				//var_dump($query);
				//echo('hello');
				foreach($query as $key => $value)
						{
							$_GET[$key] = $value;
						}*/
				header('Location: '.$new_url);
				exit();
				
			}
			else
			{
				//there is no saved search, so do nothing, let the message pass
			}
		}
		
	}
	else
	{
	
		//there is no message. 
		// now we can check on if the form is to be reset
		
		$reset_form = (isset($_GET['reset_form'])) ? 'true' : 'false';
		if($reset_form == 'true')
		{
			unset($_SESSION[$session_variable_name]);
		}
		else
		{
			//ok this is not a reset. Now check if there is a search in the url
			if ($search_set == false)
			{
				
				if(isset($_SESSION[$session_variable_name]) )
				{
				//we have a stored search, but a blank url, so we will re-direct to the last stored search
						 $new_url = $_SESSION[$session_variable_name];
						 foreach($_GET as $key => $value)
						{		
							$found = false;
							foreach($search_fields as $field)
							{
								if ($key == $field['db_field'] )
								{
									$found = true;
								}
							}
							if($key == 'order' OR $key == 'sort')
							{
								$found = true;
							}
							if (!$found)
							{
								if ($key != 'message')
								{
									$get = $key . '=' . $value;
									$new_url = addGetToUrl($new_url, $get);
								}
							}
							
						}
						unset($_SESSION[$session_variable_name]);
						/*$parts = parse_url($new_url);
						$query = convertUrlQuery($parts['query']);
						//var_dump($query);
						
						foreach($query as $key => $value)
						{
							$_GET[$key] = $value;
						}*/

						 header('Location: '.$new_url);
						 exit();
				}
				else
				{
					if($date_field !='')
					{
						if (isset($_GET[$date_field]))
						{
							//do nothing
						}
						else
						{
							//re-direct with date
							$get = $date_field . '=' . getDefaultDate();
							$new_url = addGetToUrl(getPageUrl(), $get);
							//trigger_error('check date');
							//header('Location: '.$new_url);
							//exit();
						}
					}
				}
			}
			else
			{
				//a search is set, so go ahead and save it.
				$_SESSION[$session_variable_name] = getPageUrl();	
			}
		}
	}
	return $search_set;
}
function saveAndRedirectSearchFormUrlV2($search_fields,$table_columns,  $session_variable_name)
{
	
	//reset was pushed kill the session name and return false
	$reset_form = (isset($_GET['reset_form'])) ? 'true' : 'false';
	if($reset_form == 'true')
	{
		unset($_SESSION[$session_variable_name]);
		//strip off reset_form
		$new_url = removeGetFromUrl(getPageUrl(), array('reset_form'));
		header('Location: '.$new_url);	
		//done
		exit();
	}
	//Now Are there any searched parameters in the url
	$search_set = false;
	foreach($_GET as $key => $value)
	{
		foreach($search_fields as $field)
		{
			if ($key == $field['db_field'])
			{
				$search_set = true;
			}
		}
		if ($key == 'order' OR $key == 'sort')
		{
			$search_set = true;
		}
	}
	
	//if the search is set save it and continue otherwise we need to check for a saved search

	if ($search_set == true)
	{
		//a search is set, so go ahead and save it.
		$_SESSION[$session_variable_name] = getPageUrl();	
	}
	else
	{
		if(isset($_SESSION[$session_variable_name]) )
		{
			//we have a stored search and the url has no search values in it... it could have other info
			 $new_url = $_SESSION[$session_variable_name];
			 foreach($_GET as $key => $value)
			{		
				$found = false;
				foreach($search_fields as $field)
				{
					if ($key == $field['db_field'] )
					{
						$found = true;
					}
				}
				if($key == 'order' OR $key == 'sort')
				{
					$found = true;
				}
				if (!$found)
				{
					if ($key != 'message')
					{
						$get = $key . '=' . $value;
						$new_url = addGetToUrl($new_url, $get);
					}
				}
				
			}
			unset($_SESSION[$session_variable_name]);
			 header('Location: '.$new_url);
			 exit();
		}
		else //do nothing
		{
		
		}
	}
			

	return $search_set;
}
function getDefaultDate()
{
	$date = getDateTime();
	$days = getDefaultDaysForView($_SESSION['pos_user_id']);
	$newdate = strtotime ( '-' .$days .' day' , strtotime ( $date ) ) ;
	$newdate = date ( 'Y-m-d' , $newdate );
	return $newdate;
}
function addGetToUrl($url, $get)
{
	$separator = "?";
	if (strpos($url,"?")!=false)
	{
  			$separator = "&";
  	}
	$newUrl = $url . $separator . $get; 
	return $newUrl;
}
function removeGetFromUrl($uri, $kill_params) 
{
    //$kill_params is an array of get parameters
    


    $uri_array = parse_url($uri);
    if (isset($uri_array['query'])) {
        // Do the chopping.
        $params = array();
        foreach (explode('&', $uri_array['query']) as $param) {
          $item = explode('=', $param);
          if (!in_array($item[0], $kill_params)) {
            $params[$item[0]] = isset($item[1]) ? $item[1] : '';
          }
        }
        // Sort the parameter array to maximize cache hits.
        ksort($params);
        // Build new URL (no hosts, domains, or fragments involved).
        $new_uri = '';
        if ($uri_array['path']) {
          $new_uri = $uri_array['path'];
        }
        if (count($params) > 0) {
          // Wish there was a more elegant option.
          $new_uri .= '?' . urldecode(http_build_query($params));
        }
        return $new_uri;
    }
    return $uri;
}
function valueFromGetOrDefault($id)
{
	if (isset($_GET[$id]))
	{
		$return_id = $_GET[$id];
	}
	else
	{
		$return_id = '';
	}
	return $return_id;
}
function dateValueFromGetOrSession($id)
{
	if (isset($_GET[$id]))
	{
		$return_id = $_GET[$id];
	}
	else if (isset($_SESSION['default_date_range']))
	{
		$return_id = $_SESSION['default_date_range'];
	}
	else
	{
		$return_id = '';
	}
	return $return_id;
}
function newline()
{
	return "\r\n";
}
function javascript($file)
{
	return '<script src="' . $file .'"></script>';
}
function includeJavascriptLibrary($disable_login_check)
{
	
	
	
	$html = '<script>POS_ENGINE_URL = "'.POS_ENGINE_URL.'"</script>'.newline();
	$html .= '<script type="text/javascript" src="' . JQUERY_VERSION .'"></script>'.newline();
	$html .= '<link type="text/css" href="' . JQUERY_UI_CSS_VERSION.'" rel="Stylesheet" />	' .newline();
	$html .='<script type="text/javascript" src="' . JQUERY_UI_VERSION.'"></script>'.newline();
	$html .= '<script src="' . JAVASCRIPT_LIBRARY .'"></script>'.newline();
	$html .= '<script src="' . JAVASCRIPT_TABLE_FUNCTIONS .'"></script>'.newline();
	if(!$disable_login_check)
	{
		$html .= '<script src="' . JAVASCRIPT_LOGIN .'"></script>'.newline();
	}
	return $html;
}
function confirmNavigation()
{
	//confirm navagation before leaving
	return '<script src="' . CONFIRM_NAVIGATION .'"></script>';
}
function createOpenWinButton($caption, $location, $width = '200px', $class ='button')
{
	return '<input class = "'.$class.'" style="width:'.$width.'px;" type="button" name="button" value="'.$caption.'" onclick="open_win(\''.$location.'\')"/>';
}
function addValueToInput($html, $value)
{
	$lengthHTML=strlen($html);
	$inputStart=substr($html,0,$lengthHTML-2);
	$inputClose =substr($html,$lengthHTML-2,$lengthHTML);
	$valueHTML = ' value="'.$value.'" ';
	return $inputStart.$valueHTML.$inputClose; 
}

function addTagToInput($html, $tag)
{
	$closeTag = '/>';
	$lengthHTML=strlen($html);
	$inputStart=substr($html,0,$lengthHTML-strlen($closeTag));
	$inputClose =substr($html,$lengthHTML-strlen($closeTag),$lengthHTML);
	return $inputStart.$tag.$inputClose; 
}
function disableSelect($html)
{
	$readonly_html = 'disabled="disabled" ';
	//simply replace select with select disabled="disabled
	return str_replace('<select', '<select disabled="disabled"', $html);
	
}
function addTagToSelect($html, $tag)
{
	return str_replace('<select', '<select '.$tag. ' ', $html);
}
function removeTagFromElement($html, $tag)
{
	$selectHTML = $html;
	$remove_str = $tag;
	$rmv_pos = strpos($selectHTML, $remove_str);
	if ($rmv_pos !== false) 
	{
		$lengthRemove = strlen($remove_str);
		$lengthHTML=strlen($selectHTML);
		$inputStart=substr($selectHTML,0,$rmv_pos);
		$inputClose =substr($selectHTML,$rmv_pos+$lengthRemove,$lengthHTML);
		$selectHTML =  $inputStart.$inputClose; 
	} 
	return $selectHTML;
}
function getUrlLinkHTML($url, $caption)
{
	return '<a href="'.$url.'">'.$caption.'</a>';
}
function addValueToSelect($selectHTML, $value)
{
	//first remove the select
	$remove_str = 'selected="selected"';
	$rmv_pos = strpos($selectHTML, $remove_str);
	if ($rmv_pos !== false) 
	{
		$lengthRemove = strlen($remove_str);
		$lengthHTML=strlen($selectHTML);
		$inputStart=substr($selectHTML,0,$rmv_pos);
		$inputClose =substr($selectHTML,$rmv_pos+$lengthRemove,$lengthHTML);
		$selectHTML =  $inputStart.$inputClose; 
	} 
	//now add the select to the value
	$valueToSelect = 'value="'.$value.'"';
	$str_pos = strpos($selectHTML, $valueToSelect);
	if ($str_pos !== false) 
	{
		$lengthValue = strlen($valueToSelect);
		$lengthHTML=strlen($selectHTML);
		$inputStart=substr($selectHTML,0,$str_pos+$lengthValue);
		$inputClose =substr($selectHTML,$str_pos+$lengthValue,$lengthHTML);
		$selectHTML =  $inputStart.' selected="selected" ' .$inputClose; 
	} 
    return $selectHTML;  
}
function addValueToTextArea($html, $value)
{
	//add value before </textarea>
	$closeTag = '</textarea>';
	$lengthHTML=strlen($html);
	$inputStart=substr($html,0,$lengthHTML-strlen($closeTag));
	$inputClose =substr($html,$lengthHTML-strlen($closeTag),$lengthHTML);
	return $inputStart.$value.$inputClose; 
}
function addValueToCheckBox($html, $value)
{
	//value is 0 or 1. 1 is checked....
	if ($value == 1 || strtolower($value) == 'yes' )
	{
		$html = addTagToInput($html, ' checked="checked" ');
	}
	else
	{
		$html =removeTagFromElement($html, 'checked="checked"');
	}
	
	return $html;
}
function getHTMLtype($html)
{
	$types = array('<input', '<textarea', '<div', '<select');
	foreach($types as $type)
	{
		if (strpos($html, $type) !== false)
		{
			return substr($type, 1, strlen($type));
		}
	}
	
}
function addNameToElement($html, $name)
{
	$type = getHTMLtype($html);
	$str_pos=strpos($html, $type);
	$name_string = ' name="' . $name .'" ';
	return substr($html, 0, $str_pos+strlen($type)).$name_string.substr($html,$str_pos+strlen($type), strlen($html));
}
function createBlankSelect($name, $tags)
{
    
	$html = '<select  style="width:100%;" name="' . $name . '" id="' . $name .'" ';
	$html .= $tags;
	$html .= '>';
	$html .= '</select>';
	return $html;
}
function  changeElementName($html, $cell_name)
{
	//find name
	$name = 'name';
	//keep the first part of the string
	$name_pos = strpos($html, $name);
	if ($name_pos !== false) 
	{
		$start_html = substr($html, 0, $name_pos);
		$end_html = substr($html , $name_pos+strlen($name), strlen($html));
		//now cut out the name
		$delimeter = '"';
		$delim_pos = strpos($end_html, $delimeter,2);
		$end_html =substr($end_html, $delim_pos+strlen($delimeter), strlen($end_html));
		return $start_html.' name="'.$cell_name.'" '.$end_html;
	}
	else
	{
		return addNameToElement($html, $cell_name);
	}
}
function createFormInput($args)
{
	$html = '<input onchange="needToConfirm=true" ';
	foreach($args as $key=> $value)
	{
		$html .= ' '.$key.'="' .$value. '" ';
	}
	 $html .= '/>';
	 return $html;
}
function createFormTextInput($args)
{
	$html= '<input type = "text" onchange="needToConfirm=true" ';
	foreach($args as $key=> $value)
	{
		$html .= ' '.$key.'="' .$value. '" ';
	}
	$html.= '/>';
	return $html;
}
function createFormTextArea($args, $optional_value='')
{
	$html= '<textarea  onchange="needToConfirm=true" ';
	foreach($args as $key=> $value)
	{
		$html .= ' '.$key.'="' .$value. '" ';
	}
	$html.='>';
	$html .= $optional_value;
	$html .= '</textarea>';
	return $html;
}
		
function createHiddenInput($name, $value)
{
	return  '<input type=\'hidden\' id =\''.$name .'\' name=\''. $name . '\' value=\'' . $value . '\' />';
}
function createHiddenJSONInput($name, $value)
{
	return  '<input type=\'hidden\' name=\''. $name . '\' value=\'' . $value . '\' />';
}
function createHiddenSerializedInput($name, $value)
{
	$value = htmlspecialchars(serialize($value));
	return "<input type='hidden' name='$name' value=\"$value\">";
}
function getPostOrGetID($str_incoming_id)
{
	// Check for a valid product ID, through GET or POST: We should have come from view_manufacturers.php
	if ( (isset($_GET[$str_incoming_id])) && (is_numeric($_GET[$str_incoming_id])) ) 
	{ // From view_products.php
		$id = $_GET[$str_incoming_id];
	} elseif ( (isset($_POST[$str_incoming_id])) && (is_numeric($_POST[$str_incoming_id])) ) 
	{ // Form submission.
		$id = $_POST[$str_incoming_id];
	} else 
	{ // No valid ID, kill the script.
		echo '<p class="error">This page has been accessed in error - ' .$str_incoming_id.'</p>';
		exit();
	}
	return $id;

}
function getPostOrGetValue($value)
{
	// Check for a valid product ID, through GET or POST: 
	if ( (isset($_GET[$value])) ) 
	{ // From view_products.php
		$id = $_GET[$value];
	} elseif ( isset($_POST[$value])) 
	{ // Form submission.
		$id = $_POST[$value];
	} else 
	{ // No valid value, kill the script.
		echo '<p class="error">This page has been accessed in error: '.$value .'</p>';
		exit();
	}
	return $id;

}
function getPostOrGetDataIfAvailable($str_incoming_id)
{
	$data = 'false';
	// Check for a valid product ID, through GET or POST: We should have come from view_manufacturers.php
	if ( isset($_GET[$str_incoming_id]) ) 
	{ // From view_products.php
		$data = $_GET[$str_incoming_id];
	}
	if ( (isset($_POST[$str_incoming_id])) && (is_numeric($_POST[$str_incoming_id])) ) 
	{ // Form submission.
		$data = $_POST[$str_incoming_id];
	}  
	return $data;

}
function createCheckbox($name, $tags)
{
	return '<input type="checkbox" name = "'.$name.'" '.$tags.' />';
}
function readOnlyInput($value, $size, $class)
{
	//return '<input value = "'.$value.'" size = "'.$size.'" class = "'.$class.'" readonly="readonly" />';
	return $value;

}
function createFileUploadInput($name, $tags)
{
	$html = '<input type="hidden" name="MAX_FILE_SIZE" value="2000000">';
	$html .= '<input name="'.$name.'" id="'.$name.'" type="file">';
	return $html;
}
function parse_json_newlines($text)
{
    // Damn pesky carriage returns...
    $text = str_replace("\r\n", "\n", $text);
    $text = str_replace("\r", "\n", $text);

    // JSON requires new line characters be escaped
    $text = str_replace("\n", "\\n", $text);
    return $text;
}
function cleanPostCookies()
{
	foreach($_POST as $key => $value)
	{
		if (isset($_COOKIE[$key]))
		{
			setcookie($key, '', time()-3600, '/', '', 0, 0); 
		}
	}
}
function printGetMessage($message='message')
{
	$html = '';
	if (isset($_GET[$message]))
	{
		$html = '<h2 class = "return_message">' . stripslashes(urldecode($_GET[$message])) .'</h2>';
	}
	return $html;
	
}
/******************************DATES*****************************************************/
function dateSelect($date_name, $date, $select_events = ' onchange="needToConfirm=true" ')
{
	//$date = getDateFromDatetime($date);
	//usage: use  date('Y-m-d') for now
	$html = '<script>
	$(function() 
	{
		$( "#' . $date_name . '" ).datepicker({dateFormat: \'yy-mm-dd\',onSelect: function () {
                document.all ? $(this).get(0).fireEvent("onchange") : $(this).change();
                this.focus();
            },
            onClose: function (dateText, inst) {
                if (!document.all)
                    this.select();
            }});
	});</script>';
	
	$html .= '<input size = "10" name="' . $date_name .'" id="' . $date_name .'" type="text" ';
	$html .= ' value="'.$date.'" ';
	$html .= $select_events;
	$html .= '>';
	return $html;
}
function timeSelect($time_name, $date_time, $select_events = ' onchange="needToConfirm=true" ')
{
	
	$time = getTimeFromDateTime($date_time);
	//usage: use  date('Y-m-d') for now
	//need to strip the time off the date_time
	
	
	$html = '<input size = "10" name="' . $time_name .'" id="' . $time_name .'" type="text" ';
	$html .= ' value="'.$time.'" ';
	$html .= $select_events;
	$html .= '>';
	return $html;
}

/*********************STORES****************************************/
function getOrSessionStoreId()
{
	$store_id = '';
	$store_id = $_SESSION['store_id'];
	if (isset($_GET['pos_store_id']))
	{
		$store_id = $_GET['pos_store_id'];
	}
	return $store_id;
}

function createShipToStoreSelect($name, $store_id, $option_all = 'off')
{
	// use the default_store_id set on login to load a store. The company name should be selectable, then the address
	//option_all is used to add an 'all' option for the stores... this should be the default....
    //get the company info for the default store id
    $stores = getStoresAndCompanies();
    
	if ($store_id == 'all')
    {
    	$option_all = "on";
    }
	$html = '<select  style="width:100%;" name="' . $name . '" id="' . $name .'" class = "store_select"';
	$html .= ' onchange="needToConfirm=true" ';
	$html .= '>';
	$html .= '<option value="false">Select Store</option>';
	if ($option_all == 'on')
	{
		$html .= '<option value ="all"';
		if ($store_id == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Stores</option>';
	}
	for($i = 0;$i < sizeof($stores); $i++)
	{
		$html .= '<option value="' . $stores[$i]['pos_store_id'] . '"';
		//set the store to the default value or the selected value
		if ($stores[$i]['pos_store_id'] == $store_id) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $stores[$i]['company'] . ' ' . $stores[$i]['shipping_address1'] . '</option>';
	}
	$html .= '</select>';
	return $html;
}

function createStoreSelect($name, $store_id, $option_all='off', $tags = ' onchange="needToConfirm=true" ')
{
	// use the default_store_id set on login to load a store. The company name should be selectable, then the address
	//option_all is used to add an 'all' option for the stores... this should be the default....
    //get the company info for the default store id
    $stores = getStores(getSetting('company_name'));
    
	$default_store_id = $store_id;
	if ($store_id == 'all')
    {
    	$option_all = "on";
    }
	$html = '<select  style="width:100%;" name="' . $name . '" id="' . $name .'" class = "store_select"';
	$html .= $tags;
	$html .= '>';
	$html .= '<option value="false">No Store Selected</option>';
	if ($option_all == 'on')
	{
		$html .= '<option value ="all"';
		if ($store_id == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Stores</option>';
	}
	for($i = 0;$i < sizeof($stores); $i++)
	{
		$html .= '<option value="' . $stores[$i]['pos_store_id'] . '"';
		//set the store to the default value or the selected value
		if ($stores[$i]['pos_store_id'] == $default_store_id) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $stores[$i]['store_name'] . '</option>';
	}
	$html .= '</select>';
	return $html;
}

/*********************EMPLOYEEEEEEEEEEEEEEE****************************************/





function createSecondaryProductColorCategoryTable($pos_product_option_id, $class ='mysqlTable')
{
		$sql = "SELECT pos_category_id FROM pos_product_secondary_categories WHERE pos_product_option_id = '$pos_product_option_id' AND pos_product_option_id != 0";
		$categories = getSQL($sql);
	$html = '<table style="width:400px" class="'.$class.'"><thead><th>Product Secondary 	Categories <BR> (hold control or command to multi-select)</th></thead><tbody><tr><td>'.createSecondaryCategorySelect('pos_product_secondary_categories[]', $categories).'</td></tr></tbody></table>';
	return $html;
}
function createSecondaryProductCategoryTable($pos_product_id, $class ='mysqlTable')
{
	return '<table style="width:400px" class="'.$class.'"><thead><tr><th>Product Secondary 	Categories <BR> (hold control or command to multi-select)</th></tr></thead><tbody><tr><td>'.createSecondaryCategorySelectWithValuesFromProduct($pos_product_id).'</td></tr></tbody></table>';
}
function  createSecondaryCategorySelectWithValuesFromProduct($pos_product_id)
{
	if ($pos_product_id =='false')
	{
		return createSecondaryCategorySelect('pos_product_secondary_categories[]', array());
	}
	else
	{
		$sql = "SELECT pos_category_id FROM pos_product_secondary_categories WHERE pos_product_id = '$pos_product_id'";
		$categories = getSQL($sql);
		return createSecondaryCategorySelect('pos_product_secondary_categories[]', $categories);
	}
}
function createSecondaryCategorySelect($name, $pos_category_id_array, $option_all = 'off', $select_events ='')
{
	$categories = getCategories();
	$html = '<select style="width:100%;" multiple id = "'.$name .'" name="'.$name.'" ';
	$html .= $select_events;
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">None Selected</option>';
	//add an option for all product categories
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_category_id  == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Secondary Categories</option>';
	}
	for($i = 0;$i < sizeof($categories); $i++)
	{
		$html .= '<option value="' . $categories[$i]['pos_category_id'] . '"';
		for($j=0;$j<sizeof($pos_category_id_array);$j++)
		{
			if ( $categories[$i]['pos_category_id'] == $pos_category_id_array[$j]['pos_category_id'] ) 
			{
				$html .= ' selected="selected"';
			}
		}				
		$html .= '>' . $categories[$i]['name'] .'</option>';
	}
	$html .= '</select>';
	return $html;
}
function createMultiSelect($name, $display_array, $selected_array, $select_events ='')
{
	//display_array[0]['value'] = the value
	//diaplay_array[0]['name'] = the name
	//selected_array[0]['value'] = the selected value
	//
	$html = '<select style="width:100%;" multiple id = "'.$name .'" name="'.$name.'" ';
	$html .= $select_events;
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">None Selected</option>';
	for($i = 0;$i < sizeof($display_array); $i++)
	{
		$html .= '<option value="' . $display_array[$i]['value'] . '"';
		for($j=0;$j<sizeof($selected_array);$j++)
		{
			if ( $display_array[$i]['value'] == $selected_array[$j]['value'] ) 
			{
				$html .= ' selected="selected"';
			}
		}				
		$html .= '>' . $display_array[$i]['name'] .'</option>';
	}
	$html .= '</select>';
	return $html;
}
function createEmployeeSelect($name, $employee_id, $option_all='off')
{
	$employees = getActiveEmployees($employee_id);
	//The employe id will often be a not-active employee - meaing we want all active employees plus
	//the one with $employee_id
	$html = '<select style="width:100%" name="' . $name . '" id="' . $name . '" ';
	$html .= ' onchange="needToConfirm=true" ';
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Employee</option>';
	//add an option for all employees
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($employee_id == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Employees</option>';
	}
	for($i = 0;$i < sizeof($employees); $i++)
	{
		$html .= '<option value="' . $employees[$i]['pos_employee_id'] . '"';
		
		if ( ($employees[$i]['pos_employee_id'] == $employee_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $employees[$i]['first_name'] . ' ' . $employees[$i]['last_name'] .'</option>';
	}
	$html .= '</select>';
	return $html;
}
function createUserGroupSelect($name, $pos_user_group_id, $option_all='off')
{
	$groups = getSQL("SELECT * FROM pos_user_groups WHERE active = 1");

	$html = '<select style="width:100%" name="' . $name . '" id="' . $name . '" ';
	$html .= ' onchange="needToConfirm=true" ';
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Group</option>';
	//add an option for all employees
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_user_group_id == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Groups</option>';
	}
	for($i = 0;$i < sizeof($groups); $i++)
	{
		$html .= '<option value="' . $groups[$i]['pos_user_group_id'] . '"';
		
		if ( ($groups[$i]['pos_user_group_id'] == $pos_user_group_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $groups[$i]['group_name'] .'</option>';
	}
	$html .= '</select>';
	return $html;
}
function getOpenMFGPurchaseOrders($pos_manufacturer_id)
{

	$sql = 		"
			SELECT pos_purchase_orders.pos_purchase_order_id,pos_purchase_orders.purchase_order_number FROM pos_purchase_orders 
			LEFT JOIN pos_manufacturer_brands 
			ON pos_manufacturer_brands.pos_manufacturer_brand_id = pos_purchase_orders.pos_manufacturer_brand_id
			LEFT JOIN pos_manufacturers
			ON pos_manufacturers.pos_manufacturer_id = pos_manufacturer_brands.pos_manufacturer_id
			WHERE pos_manufacturers.pos_manufacturer_id = '$pos_manufacturer_id' AND pos_purchase_orders.purchase_order_status = 'OPEN'
			";
	return getSQL($sql);
}
function getDays($pos_manufacturer_id)
{
	//is the mfg on a credit account?
	$sql = "SELECT pos_account_id FROM pos_manufacturers WHERE pos_manufacturer_id = '$pos_manufacturer_id'";
	$account = getSQL($sql);
	if($account[0]['pos_account_id'] == 0)
	{
		//no account - days are 0
		$days= 0;
	}
	else
	{
		$sql="SELECT days FROM pos_accounts WHERE pos_account_id = '". $account[0]['pos_account_id'] ."'";
		$day = getSQL($sql);
		$days = $day[0]['days'];
	}
	return $days;
	
}
function getDiscount($pos_manufacturer_id)
{
	//is the mfg on a credit account?
	$sql = "SELECT pos_account_id FROM pos_manufacturers WHERE pos_manufacturer_id = '$pos_manufacturer_id'";
	$account = getSQL($sql);
	if($account[0]['pos_account_id'] == 0)
	{
		//no account - days are 0
		$discount= 0;
	}
	else
	{
		$sql="SELECT discount FROM pos_accounts WHERE pos_account_id = '". $account[0]['pos_account_id'] ."'";
		$day = getSQL($sql);
		$discount = $day[0]['discount'];
	}
	return $discount;
}



function createGenericSelect($name, $values, $captions, $select_value, $option_all = 'off', $select_events = ' onchange="needToConfirm=true" ')
{
	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= $select_events;
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Value</option>';
	//add an option for all accounts
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_purchase_order_id == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Values</option>';
	}
	for($i = 0;$i < sizeof($values); $i++)
	{
		$html .= '<option value="' . $values[$i] . '"';
		
		if ( ($values[$i] == $select_value) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' .$captions[$i] . '</option>';
	}
	$html .= '</select>';
	return $html;
}
function createProductSubIDSelect($name, $pos_product_sub_id, $pos_manufacturer_brand_id, $option_all = 'off', $select_events = ' onchange="needToConfirm=true" ')
{	
	$product_sub_ids = getProductSubIdsFromBrand($pos_manufacturer_brand_id);
	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= $select_events;
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Sub Id</option>';
	//add an option for all accounts
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_purchase_order_id == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Purchase Order\'s</option>';
	}
	for($i = 0;$i < sizeof($purchase_orders); $i++)
	{
		$html .= '<option value="' . $purchase_orders[$i]['pos_purchase_order_id'] . '"';
		
		if ( ($purchase_orders[$i]['pos_purchase_order_id'] == $pos_purchase_order_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>System PO#: ' . $purchase_orders[$i]['pos_purchase_order_id'] . ' Custom po#: ' .$purchase_orders[$i]['purchase_order_number'] . '</option>';
	}
	$html .= '</select>';
	return $html;
}



function createProductCategorySelect($name, $pos_category_id, $option_all)
{
	$product_categories = getProductCategories();

	$html = '<select id = "'.$name .'" name="'.$name.'" ';
	$html .= ' onchange="needToConfirm=true" ';
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Category</option>';
	//add an option for all product categories
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_category_id  == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Categories</option>';
	}
	for($i = 0;$i < sizeof($product_categories); $i++)
	{
		$html .= '<option value="' . $product_categories[$i]['pos_category_id'] . '"';
		
		if ( ($product_categories[$i]['pos_category_id'] == $pos_category_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $product_categories[$i]['name'] .'</option>';
	}
	$html .= '</select>';
	return $html;
}
function getManufacturersOnAccount()
{
	$sql = "SELECT pos_manufacturers.pos_manufacturer_id, pos_manufacturers.company FROM pos_manufacturers
			LEFT JOIN pos_accounts
			ON pos_manufacturers.pos_account_id = pos_accounts.pos_account_id
			LEFT JOIN pos_account_type
			ON pos_accounts.pos_account_type_id = pos_account_type.pos_account_type_id
			WHERE pos_account_type.account_type_name = 'Inventory Account'
			";
	return getSQL($sql);
}
function createManufacturersOnAccountSelect($name, $pos_manufacturer_id, $option_all = 'off', $select_events = '')
{
	$mfgs = getManufacturersOnAccount();

	$html = '<select style="width:100%" id = "'.$name .'" name="'.$name.'" ';
	$html .= $select_events;
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Manufacturer</option>';
	//add an option for all product categories
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_manufacturer_id  == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Manufacturers</option>';
	}
	for($i = 0;$i < sizeof($mfgs); $i++)
	{
		$html .= '<option value="' . $mfgs[$i]['pos_manufacturer_id'] . '"';
		
		if ( ($mfgs[$i]['pos_manufacturer_id'] == $pos_manufacturer_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $mfgs[$i]['company'] .'</option>';
	}
	$html .= '</select>';
	return $html;
}

function createBrandCodeBrandIDLookup()
{
	$brands = getBrands();
	for($i = 0;$i < sizeof($brands); $i++)
	{
		$brand_code_id_lookup[$brands[$i]['pos_manufacturer_brand_id']] = $brands[$i]['brand_code'];
	}
	return '<script>var brand_code_id_lookup = ' . json_encode($brand_code_id_lookup) . ';</script>';
}
/*********************EXPENSE FORM HELPERS****************************************/
function createExpenseDescription($name, $value)
{
	$html = '<INPUT TYPE="TEXT"  id = "'.$name.'" NAME="'.$name.'" ';
	$html .= ' onchange="needToConfirm=true" ';
	/*if (isset($_COOKIE[$name]))
	{
		$html .= ' value = "'.$_COOKIE[$name].'" ' ;
	}
	else
	{
		$html .= ' value = "'.$value.'" ' ;
	}*/
	$html .= ' value = "'.$value.'" ' ;
	$html .= '/>';
	return $html;
}
function createCommentInput($name, $value)
{
	$html =  '<textarea rows="2" cols="10"  NAME="'.$name.'" id = "'.$name.'" ';
	$html .= ' onchange="needToConfirm=true" ';
	$html .= '>';
	/*if (isset($_POST[$name]))
	{
		$html .= $_POST[$name];
	}
	else
	{
		$html .= $value;
	}*/
	$html .= $value;
	$html .= '</textarea>';
	return $html;
}
function createSupplierInput($name, $value)
{
	$html = '<INPUT TYPE="TEXT"  id = "'.$name.'" size = "9" NAME="'.$name.'" ';
	$html .= ' onchange="needToConfirm=true" ';
	$html .= ' value = "'.$value.'" ' ;
	$html .= '/>';
	return $html;
}
function createExpenseCategorySelect($name, $expense_id, $option_all)
{
	$expenses = getExpenseCategories();

	$html = '<select id = "'.$name.'" name="' . $name . '"';
	$html .= ' onchange="needToConfirm=true" ';
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Category</option>';
	//add an option for all employees
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($expense_id == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Categories</option>';
	}
	for($i = 0;$i < sizeof($expenses); $i++)
	{
		$html .= '<option value="' . $expenses[$i]['pos_expense_category_id'] . '"';
		
		if ( ($expenses[$i]['pos_expense_category_id'] == $expense_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $expenses[$i]['caption'] .'</option>';
	}
	$html .= '</select>';
	return $html;
}
function createSysIDInput($name, $value)
{
	$html = '<INPUT readonly="readonly" size="5" TYPE="TEXT"  id = "'.$name.'" NAME="'.$name.'" ';
	$html .= ' value = "'.$value.'" ';
	$html .= ' />';
	return $html;
}
function createCostInput($name, $value)
{
	$html = '<INPUT size="5" TYPE="TEXT"  id = "'.$name.'" NAME="'.$name.'" ';
	if ($value == '')
	{
		$html .= ' value = "" ';
	}
	else
	{
		$html .= ' value = "'.round($value,2).'" ';
	}
	$html .= ' onchange="needToConfirm=true" ';
	$html .= ' onkeyup  = \'checkInput(this,"0123456789.")\' ';
	$html .= ' />';
	return $html;
}

function createInventoryAccountSelect($name, $pos_account_id, $option_all = 'off', $select_events ='')
{	
	$accounts = getInventoryAccounts();
	
	$html = '<select style="width:100%" id = "'.$name.'" name="'.$name.'" ';
	$html .= ' onchange="needToConfirm=true" ';
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Account</option>';
	//add an option for all accounts
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_account_id == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Accounts</option>';
	}
	for($i = 0;$i < sizeof($accounts); $i++)
	{
		$html .= '<option value="' . $accounts[$i]['pos_account_id'] . '"';
		
		if ( ($accounts[$i]['pos_account_id'] == $pos_account_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $accounts[$i]['company'] . ' - ' . craigsDecryption($accounts[$i]['account_number']) . '</option>';
	}
	$html .= '</select>';
	return $html;
}
function getInventoryAccounts()
{
	$sql = "
	SELECT pos_accounts.pos_account_id, pos_accounts.account_number, pos_accounts.company FROM pos_accounts 
	LEFT JOIN pos_account_type
	ON pos_account_type.pos_account_type_id = pos_accounts.pos_account_type_id
	WHERE pos_accounts.active=1 AND (pos_account_type.account_type_name = 'Inventory Account')
	";
	return getSQL($sql);
}

function generateInventoryTrackingNumber($store_name)
{
	return uniqid('inventory_'.$store_name.'_');
}
function createPaymentMethodSelect($name, $pos_expense_payment_method_id, $option_all)
{
	$payment_methods = getPaymentMethods();

	$html = '<select id = "'.$name.'" name="'.$name.'" ';
	$html .= ' onchange="needToConfirm=true" ';
	$html .= '>';
	//Add an option for not selected
	$html .= '<option value="false">Select Payment</option>';
	//add an option for all employees
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($pos_expense_payment_method_id == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All Payment Methods</option>';
	}
	for($i = 0;$i < sizeof($payment_methods); $i++)
	{
		$html .= '<option value="' . $payment_methods[$i]['pos_expense_payment_method_id'] . '"';
		
		if ( ($payment_methods[$i]['pos_expense_payment_method_id'] == $pos_expense_payment_method_id) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $payment_methods[$i]['caption'] . '</option>';
	}
	$html .= '</select>';
	return $html;
}
/************************SearchBox *********************************************/
function createSearchInput($field)
{

	$html = '<INPUT TYPE="TEXT" id="'.$field.'" NAME="'.$field.'"	value = "' ;
		if (isset($_GET[$field]))
		{
			$html .=  htmlspecialchars(stripslashes($_GET[$field]));
		}
		$html .= '"/>';
	return $html;
}
function createSearchSelect($search_field)
{
	$selected = (isset($_GET[$search_field['db_field'] .'_search_select'])) ? $_GET[$search_field['db_field'] .'_search_select'] : 'AND';
	$search_options = array('AND', 'OR');
	$name = $search_field['db_field'] .'_search_select';
	$html = '<select style = "width:100%;" id = "'.$name.'" name="'.$name.'" ';
	$html .= '>';
	//Add an option for not selected

	for($i = 0;$i < sizeof($search_options); $i++)
	{
		$html .= '<option value="' . $search_options[$i] . '"';
		
		if ( 
		($search_options[$i] == $selected) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $search_options[$i] . '</option>';
	}
	$html .= '</select>';
	return $html;
}
function createSearchForm($search_fields, $action)
{
		$html = '<form id = "search_form" name="search_form" action="'.$action.'" method="get">';
		$html .= '<div class = "search_div">';	
		$html .=  '<table class = "search_table">' . newline();
		$html .= '<thead>' . newline();
		$html .= '<tr>' . newline();
		foreach($search_fields as $caption)
		{
			$html .= '<th>' . $caption['caption'] . '</th>' . newline();
		}
		$html .= '</tr>' . newline();
		$html .= '</thead>' . newline();
		$html .= '<tbody>' .newline();
		$html .= '<tr>' .newline();
		foreach($search_fields as $caption)
		{
			$html .= '<td>' . $caption['html'] . '</td>' . newline();
		}
		$html .= '</tr>' .newline();
		/*
		//now add the search criteria
		$html .= '<tr>' .newline();
		foreach($search_fields as $caption)
		{
			if ($caption['type'] =='input')
			{
			}
			$html .= '<td>' . createSearchSelect($caption) . '</td>' . newline();
		}		
		$html .= '</tr>' .newline();
		
		*/
		$html .= '</tbody>' .newline();
		$html .= '</table>' .newline();
		$html .= '<Table style="width:100%;"><tr><td>';
		$html .= '<input class = "button" type="submit" name = "search" value="Search" />';
		$html .= '<input type="button" class ="button" value="Clear" onclick="reset_search_form(\'search_form\', \''.$action.'?reset_form=true\')"/>';
		$html .= '</td><td style="text-align:right;font-size:0.8em;">Tip: Use "exact term",OR,AND for search parameters</td></tr></table>';
		$html .= '</div></form>';
		$html .="<script>
		function reset_search_form(formId, location)
		{
			//For each form element set the value to default
			var elem = document.getElementById(formId).elements;
			for(var i = 0; i < elem.length; i++)
			{
				if (elem[i].type == 'text')
				{
					elem[i].value = '';
				}
				if (elem[i].type == 'select-one')
				{
					elem[i].value = 'false';
				}
			}
			window.location = location;
			
		}
		</script>";
		
		return $html;			
}
function createSearchFormWithID($search_fields, $action, $key_val_id)
{
		$html = '<form id = "search_form" name="search_form" action="'.$action.'" method="get">';
		$html .= '<div class = "search_div">';
		$html .= '<h2>Search</h2>';
		$html .= createHiddenInput(key($key_val_id), $key_val_id[key($key_val_id)]);
		
		$html .=  '<table class = "search_table">' . newline();
		$html .= '<thead>' . newline();
		$html .= '<tr>' . newline();
		foreach($search_fields as $caption)
		{
			$html .= '<th>' . $caption['caption'] . '</th>' . newline();
		}
		$html .= '</tr>' . newline();
		$html .= '</thead>' . newline();
		$html .= '<tbody>' .newline();
		$html .= '<tr>' .newline();
		foreach($search_fields as $caption)
		{
			$html .= '<td>' . $caption['html'] . '</td>' . newline();
		}
		$html .= '</tr>' .newline();
		$html .= '</tbody>' .newline();
		$html .= '</table>' .newline();
		$html .= '<p><input class = "button" type="submit" name="search" value="Search" />';
		$html .= '<input type="button" class ="button" value="Clear" onclick="reset_search_form(\'search_form\', \''.$action.'?'.key($key_val_id).'='. $key_val_id[key($key_val_id)] .'&reset_form=true\')"/></p>';
		$html .= '</div></form>';
		$html .="<script>
		function reset_search_form(formId, location)
		{
			//For each form element set the value to default
			var elem = document.getElementById(formId).elements;
			for(var i = 0; i < elem.length; i++)
			{
				if (elem[i].type == 'text')
				{
					elem[i].value = '';
				}
				if (elem[i].type == 'select-one')
				{
					elem[i].value = 'false';
				}
			}
			window.location = location;
			
		}
		</script>";
		
		return $html;			
}
function createHTMLSeachTable($search_fields, $table_tags = ' class = "search_table" ' )
{
	$html =  '<table ' . $table_tags .' >' . newline();
	$html .= '<thead>' . newline();
	$html .= '<tr>' . newline();
	foreach($search_fields as $caption)
	{
		$html .= '<th>' . $caption['caption'] . '</th>' . newline();
	}
	$html .= '</tr>' . newline();
	$html .= '</thead>' . newline();
	$html .= '<tbody>' .newline();
	$html .= '<tr>' .newline();
	foreach($search_fields as $caption)
	{
		$html .= '<td>' . $caption['html'] . '</td>' . newline();
	}
	$html .= '</tr>' .newline();
	$html .= '</tbody>' .newline();
	$html .= '</table>' .newline();
	return $html;
}
function creatAjaxSearchFormAndTable($search_fields, $search_table_object_name, $php_sql_processing_file)
{
		$html =  '<script src="'.AJAX_SEARCH_TABLE_OBJECT.'"></script>'.newline();
		//this creates the table object
	
	
	
		$html .= '<div class = "'.$search_table_object_name.'_search_div">';		
		$html .=  '<table class = "search_table">' . newline();
		$html .= '<thead>' . newline();
		$html .= '<tr>' . newline();
		foreach($search_fields as $caption)
		{
			$html .= '<th>' . $caption['caption'] . '</th>' . newline();
		}
		$html .= '</tr>' . newline();
		$html .= '</thead>' . newline();
		$html .= '<tbody>' .newline();
		$html .= '<tr>' .newline();
		foreach($search_fields as $caption)
		{
			$html .= '<td>' . $caption['html'] . '</td>' . newline();
		}
		$html .= '</tr>' .newline();
		$html .= '</tbody>' .newline();
		$html .= '</table>' .newline();
		$html .= '<Table style="width:100%;"><tr><td>';
		$html .= '<input type="button" class ="button" value="Search" onclick="'.$search_table_object_name.'.ajax_search_form()"/>';
		$html .= '<input type="button" class ="button" value="Clear" onclick="'.$search_table_object_name.'.reset_ajax_search_form()"/>';
		$html .= '</td><td style="text-align:right;font-size:0.8em;">Tip: Use "exact term",OR,AND for search parameters</td></tr></table>';
		$html .= '</div>';
		$html .= '<div id = "'.$search_table_object_name.'_search_results_div">';
		$html .= '</div>';
		$html .="<script>


		</script>";
		$html .= '<script>var '.$search_table_object_name.' = new search_table_object("'.$search_table_object_name.'",'. json_encode($search_fields).', "'.$php_sql_processing_file. '");</script>';
		return $html;			
}
function createSearchFromWithMultiLineEdit($search_fields, $action)
{
		$html = '<form id = "search_form" name="search_form" action="'.$action.'" method="get">';
		$html .= '<div class = "search_div">';
		$html .= '<h2>Search</h2>';
		$html .=  '<table class = "search_table">' . newline();
		$html .= '<thead>' . newline();
		$html .= '<tr>' . newline();
		foreach($search_fields as $caption)
		{
			$html .= '<th>' . $caption['caption'] . '</th>' . newline();
		}
		$html .= '</tr>' . newline();
		$html .= '</thead>' . newline();
		$html .= '<tbody>' .newline();
		$html .= '<tr>' .newline();
		foreach($search_fields as $caption)
		{
			$html .= '<td>' . $caption['html'] . '</td>' . newline();
		}
		$html .= '</tr>' .newline();
		$html .= '</tbody>' .newline();
		$html .= '</table>' .newline();
		$html .= '<p><input class = "button" type="submit" name="submit" value="Search" />';
		$html .= '<input type="button" class ="button" value="Clear" onclick="reset_search_form(\'search_form\', \''.$action.'\')"/>';
		$html .= '<input class = "button" type="submit" name="edit" value="Edit Results" />';
		$html .= '</p></div></form>';
		$html .="<script>
		function reset_search_form(formId, location)
		{
			//For each form element set the value to default
			var elem = document.getElementById(formId).elements;
			for(var i = 0; i < elem.length; i++)
			{
				if (elem[i].type == 'text')
				{
					elem[i].value = '';
				}
				if (elem[i].type == 'select-one')
				{
					elem[i].value = 'false';
				}
			}
			window.location = location;
			
		}
		</script>";
		
		return $html;			
}

function createSearchSQLString($search_fields)
{
	//Create Search String (AND's after MYSQL WHERE from $_GET data)
	$search_sql = '';
	foreach($_GET as $key => $value)
	{
		$table = '';
		$type = '';
		//find the table name and input type for the key:
		foreach($search_fields as $field)
		{
			// need to find out if it is a start or end date and then we need to know which one is which...
			if ($key == $field['db_field'])
			{
				$table = $field['table'];
				$type = $field['type'];
			}
		}
		if ($type == 'select')
		{
			if ($value == 'false' || $value =='all')
			{
			}
			else
			{
				$search_sql .= " AND " . $table . " = '" .$value."' ";
			}
		}
		if ($type == 'input')
		{
			$search_sql .=  " AND ". $table ." LIKE '%". $value . "%' ";
		}
	}
	if ($search_sql !='')
	{
		//force add the date range if there is one
		if (isset($_GET['start_date'])) $start_date = $_GET['start_date'];
		if (isset($_GET['end_date'])) $end_date = $_GET['end_date'];
		foreach($search_fields as $field)
		{
			if ($field['type'] == 'start_date' )
			{
				$table = $field['table'];
				$mysql_field = $field['db_field'];
			}
		}
		if (isset($start_date) && isset($end_date) && $end_date != '')
		{
			$search_sql.= " AND " . $table ."." .$mysql_field." BETWEEN '" . $start_date ."' AND '" .$end_date. "' ";
		}
		if (isset($start_date) && isset($end_date) && $end_date == '')
		{
			$search_sql.= " AND " . $table ."." .$mysql_field." >= '" . $start_date ."' ";
		}
		
	}
	return $search_sql;
}
function createSearchSQLStringMultipleDates($search_fields, $user_data = "get")
{
	
	if($user_data == 'get')
	{
		$user_data = $_GET;
	}
	//Create Search String (AND's after MYSQL WHERE from $_GET data)
	$search_sql = '';
	//the regular input/select stuff - find it and create the string
	
	foreach($user_data as $key => $value)
	{
		$value = scrubInput($value);
		$mysql_search_result = '';
		$type = '';
		//find the table name and input type for the key:
		foreach($search_fields as $field)
		{
			// need to find out if it is a start or end date and then we need to know which one is which...
			if ($key == $field['db_field'])
			{
				$mysql_search_result = $field['mysql_search_result'];
				$type = $field['type'];
				if(isset($field['exact_match']))
				{
					$match = true;
				}
				else
				{
					$match = false;
				}
			}
		}
		if ($type == 'select')
		{
			if (is_array($value))
			{
				foreach($value as $array_val)
				{
					if ($value != 'false' && $value !='all')
					{
						$search_sql .= " AND " . $mysql_search_result . " LIKE '%" .$array_val."%' ";
					}
				}
			}
			else
			{
				if ($value == 'false' || $value =='all')
				{
				}
				else
				{
					$search_sql .= " AND " . $mysql_search_result . " = '" .$value."' ";
				}
			}
		}
		if ($type == 'input')
		{
			// search characters: OR || AND && " '
			if ($value !='')
			{
				if (strpos($value, '"') OR strpos($value, '\'') OR $match)
				{
					//search is exact
					$new_value = str_replace('\"', '', $value);
					$new_value = str_replace('\\\'', '', $new_value);
					$search_sql .=  " AND ". $mysql_search_result ." = '". $new_value . "' ";
				}
				else if (strpos($value, '!'))
				{
					//search is not
				}
				else if (strpos($value, 'AND') OR strpos($value, '&&'))
				{
					//and search
				}
				else if (strpos($value, 'OR') OR strpos($value, '||') )
				{
					$values = (strpos($value, 'OR')) ? explode('OR', $value) : explode('||', $value);
					$search_sql .=  " AND (" ;
					for($i=0;$i<sizeof($values);$i++)
					{
						$search_sql_array[] =  $mysql_search_result ." = '". trim($values[$i]) . "' ";
					}
					$search_sql .= implode('OR ' , $search_sql_array);
					$search_sql .= ') ';
				}
				else
				{
					//regular
					$search_sql .=  " AND ". $mysql_search_result ." LIKE '%". $value . "%' ";
				}
			}
		}
	}
	//find start and end dates if any - create an array like	$dates['expense_date']['start_date'] = '2012-01-01'
	//															$dates['expense_date']['end_date'] = '2012-02-01'
	$dates = array();
	foreach($user_data as $key => $value)
	{
		if (strpos($key, '_start_date'))
		{
			// what is the name of the db_field for this date?
			$db_date_field = str_replace('_start_date', '', $key);
			foreach($search_fields as $field)
			{
				// need to find out if it is a start or end date and then we need to know which one is which...
				if ($field['db_field'] == $db_date_field)
				{
					$dates[$db_date_field]['mysql_search_result'] = $field['mysql_search_result'];
					$dates[$db_date_field]['type'] = $field['type'];
				}
			}
			$dates[$db_date_field]['start_date'] = getDateFromDatetime($value);
			$dates[$db_date_field]['start_time'] = getTimeFromDatetime($value);
		}
		if (strpos($key, '_end_date'))
		{
			$db_date_field = str_replace('_end_date', '', $key);
			$dates[$db_date_field]['end_date'] = getDateFromDatetime($value);
			$dates[$db_date_field]['end_time'] = getTimeFromDatetime($value);
		}
	}
	foreach($dates as $key=> $value)
	{
		if (isset($dates[$key]['end_date']))
		{
			if ($dates[$key]['start_date'] != '' && $dates[$key]['end_date'] != '')
			{
				$search_sql.= " AND TIMESTAMP(" . $dates[$key]['mysql_search_result'] .") >= TIMESTAMP('" . $dates[$key]['start_date'] ."','".$dates[$key]['start_time']."') AND TIMESTAMP(" . $dates[$key]['mysql_search_result'] .") <=	 TIMESTAMP('" .$dates[$key]['end_date']. "','".$dates[$key]['end_time']."') ";
			}
			else if ($dates[$key]['start_date'] != '')
			{
				$search_sql.= " AND TIMESTAMP(" . $dates[$key]['mysql_search_result'] .") >= TIMESTAMP('" . $dates[$key]['start_date'] ."','".$dates[$key]['start_time']."') ";
			}
		}
		else
		{
			$search_sql.= " AND " . $dates[$key]['mysql_search_result'] ." >= '" . $dates[$key]['start_date'] ."' ";
		}
	}
	return $search_sql;
}
function createSortSQLString($table_columns, $default_sort, $order='DESC')
{
	
	// Determine the sort...
	// Default is by ProductID.
	if (isset($_GET['sort']))
	{
		$sort = $_GET['sort'];
		$order = $_GET['order'];
		if ($order == 'ASC')
		{
			$order = 'DESC';
		}
		else
		{
			$order ='ASC';
		}
	}
	else
	{
		$sort = $default_sort;
		//$order = 'DESC';
	}
	foreach ($table_columns as $column)
	{
		if (isset($column['sort']))
		{
			if ($sort == $column['mysql_field'])
			{
				$order_by = $column['sort'] . ' ' . $order;
			}
		}
	}
	if (isset($order_by))
	{
		return $order_by;
	} 
	else
	{
		return false;
	}
}
/************************Tables and Forms *********************************************/
function createRecordsTable($data, $table_columns, $class = 'generalTable')
{
	$order = recordsTableSortOrder();
	if (sizeof($data) > 0) 
	{ 
		// Table header:
		$getURL = getOrderSortUrl();
	
		$html = '<table class = "'. $class . '">' .newline();
		$html .= '<thead><tr>' . newline();
		foreach($table_columns as $column)
		{
			if (isset($column['sort']))
			{
				$html .= '<th><a href="'.$getURL.'sort='.$column['mysql_field'].'&order='.$order.'">'.$column['th'].'</a></th>'.newline();
			}
			else
			{
				$html .= '<th>'.$column['th'].'</th>'.newline();
			}
		}
		$html .= '</tr></thead>'.newline();
		$html .= '<tbody>';
		
		// Fetch and print all the records....
		for($i=0;$i<sizeof($data);$i++)
		{
			$html .= '<tr>';
			foreach($table_columns as $column_data)
			{
				$html .= recordsTableTD($column_data,$data[$i]);
			}
			$html .= '</tr>'.newline();
		}
		$html .= '</tbody></table>'.newline(); // Close the table.
		$html .= '<p>' . sizeof($data) . ' Records Returned</p>';
	} 
	else 
	{ // If no records were returned.
		$html = '<p class="error">There are currently no records.</p>';
	}
	return $html;
}
function createSelectableRecordsTable($data, $table_columns, $class = 'generalTable')
{
	/* this is the exact same code as create records table, so probably don't use it... */
	
	$order = recordsTableSortOrder();
	if (sizeof($data) > 0) 
	{ 
		// Table header:
		$getURL = getOrderSortUrl();
		$html = '<table class = "'. $class . '">' .newline();
		$html .= '<thead><tr>' . newline();
		foreach($table_columns as $column)
		{
			if (isset($column['sort']))
			{
				$html .= '<th><a href="'.$getURL.'sort='.$column['mysql_field'].'&order='.$order.'">'.$column['th'].'</a></th>'.newline();
			}
			else
			{
				$html .= '<th>'.$column['th'].'</th>'.newline();
			}
		}
		$html .= '</tr></thead>'.newline();
		$html .= '<tbody>';
		
		// Fetch and print all the records....
		for($i=0;$i<sizeof($data);$i++)
		{
			$html .= '<tr>';
			foreach($table_columns as $column_data)
			{
				$html .= recordsTableTD($column_data,$data[$i]);
			}
			$html .= '</tr>'.newline();
		}
		$html .= '</tbody></table>'.newline(); // Close the table.
	} 
	else 
	{ // If no records were returned.
		$html = '<p class="error">There are currently no records.</p>';
	}
	return $html;
}
function createEditableRecordsTable($data, $table_columns, $class = 'generalTable')
{
	
	if (isset($_GET['order']))
	{
		$order = $_GET['order'];
		if ($order == 'ASC')
		{
			$order = 'DESC';
		}
		else
		{
			$order ='ASC';
		}
	}
	else
	{
		$order = 'DESC';
	}
	
	if (sizeof($data) > 0) 
	{ 
		// Table header:
		$getURL = getPageURLwithGETS();
	
		$html = '<table class = "'. $class . '">' .newline();
		$html .= '<thead><tr>' . newline();
		foreach($table_columns as $column)
		{
			if (isset($column['sort']))
			{
				$html .= '<th><a href="'.$getURL.'sort='.$column['mysql_field'].'&order='.$order.'">'.$column['th'].'</a></th>'.newline();
			}
			else
			{
				$html .= '<th>'.$column['th'].'</th>'.newline();
			}
		}
		$html .= '</tr></thead>'.newline();
		$html .= '<tbody>';
		
		// Fetch and print all the records....
		for($i=0;$i<sizeof($data);$i++)
		{
			$html .= '<tr>';
			foreach($table_columns as $column_data)
			{
				if (isset($column_data['get_url_link']))
				{		
					$html .= '<td><a href="'.$column_data['get_url_link'].'?'. $column_data['mysql_field'].'=' . $data[$i][$column_data['mysql_field']] . '">'.$column_data['url_caption'].'</a></td>'.newline();
				}
				else if (isset($column_data['round']))
				{
					$html .= '<td>' . number_format($data[$i][$column_data['mysql_field']], $column_data['round']).'</td>'.newline();
				}
				else if (isset($column_data['mysql_field']))
				{
					
					
					if (isset($column_data['editable']))
					{
						//$html .= '<td>' . nl2br($data[$i][$column_data['mysql_field']]).'</td>'.newline();
						$html .= '<td><input onchange="needToConfirm=true" id = "'.$column_data['mysql_field'] .'[]" name="'.$column_data['mysql_field'] .'[]" ';
						if(isset($column_data['tags']))
						{
		 					$html .= $column_data['tags'];
						}
						$html .= ' value="' . $data[$i][$column_data['mysql_field']] . '" ';
						$html .= '/></td>' .newline();
					}
					elseif(isset($column_data['month_day']))
					{
						list($year,$month,$day) = split("-",$data[$i][$column_data['mysql_field']]);
						if ($month == '00')
						{
							$html .= '<td></td>'.newline();
						}
						else
						{
							$html .= '<td>' . $month . '-' .$day.'</td>'.newline();
						}
					}
					elseif(isset($column_data['encrypted']))
					{
						$html .= '<td>' . nl2br(craigsDecryption($data[$i][$column_data['mysql_field']],0)).'</td>'.newline();
					}
					else
					{
						$html .= '<td>' . nl2br($data[$i][$column_data['mysql_field']]).'</td>'.newline();
					}
				}
				else
				{
					$html .= '<td></td>'.newline();
				}
			
				
			}
			$html .= '</tr>'.newline();
		}
		$html .= '</tbody></table>'.newline(); // Close the table.
	} 
	else 
	{ // If no records were returned.
		$html = '<p class="error">There are currently no records.</p>';
	}
	return $html;
}
function recordsTableSortOrder()
{
	if (isset($_GET['order']))
	{
		$order = $_GET['order'];
		if ($order == 'ASC')
		{
			$order = 'DESC';
		}
		else
		{
			$order ='ASC';
		}
	}
	else
	{
		$order = 'DESC';
	}
	return $order;
}
function createRecordsTableWithTotals($data, $table_columns, $class = 'generalTable', $number_of_rows_to_be_used_for_totals_caption = 1)
{
	$order = recordsTableSortOrder();
	
	if (sizeof($data) > 0) 
	{ 
		// Table header:
		$getURL = getOrderSortUrl();
	
		$html = '<table class = "'. $class . '">' .newline();
		$html .= '<thead><tr>' . newline();
		foreach($table_columns as $column)
		{
			if (isset($column['sort']))
			{
				$html .= '<th><a href="'.$getURL.'sort='.$column['mysql_field'].'&order='.$order.'">'.$column['th'].'</a></th>'.newline();
			}
			else
			{
				$html .= '<th>'.$column['th'].'</th>'.newline();
			}
		}
		$html .= '</tr></thead>'.newline();
		$html .= '<tbody>';
		$html .= '<tr style="background-color:yellow;border-bottom:1px solid rgb(50,50,50);">';
		if($number_of_rows_to_be_used_for_totals_caption > 0)
		{
			$html .= '<td colspan="'.$number_of_rows_to_be_used_for_totals_caption.'"><b>Totals</b></td>';
		}
		for($i=$number_of_rows_to_be_used_for_totals_caption;$i<sizeof($table_columns);$i++)
		{
			//
			if(isset($table_columns[$i]['total']))
			{	
				$html .= '<td><b>' .number_format(calculateSQLTotal($data, $table_columns[$i]['mysql_field']),$table_columns[$i]['total']).'</b></td>';
			}
			else
			{
				$html .= '<td></td>';
			}
		}
		$html .= '</tr>';
		// Fetch and print all the records....
		for($i=0;$i<sizeof($data);$i++)
		{
			$html .= '<tr>';
			foreach($table_columns as $column_data)
			{
				$html .= recordsTableTD($column_data,$data[$i]);
				
			}
			$html .= '</tr>'.newline();
		}
		$html .= '</tbody></table>'.newline(); // Close the table.
		$html .= '<p>' . sizeof($data) . ' Records Returned</p>';
	} 
	else 
	{ // If no records were returned.
		$html = '<p class="error">There are currently no records.</p>';
	}
	return $html;
}
function recordsTableTD($column_data,$data)
{
	$html = '';
	if (isset($column_data['get_url_link']))
	{		
		
		if(isset($column_data['url_caption']))
		{
			$caption = $column_data['url_caption'];
		}
		else
		{
			$caption = $data[$column_data['mysql_field']];
		}
		if (strpos($column_data['get_url_link'],'?')!==false)
		{
			$html .= '<td><a href="'.$column_data['get_url_link'].'&'. $column_data['get_id_link'].'=' . $data[$column_data['mysql_field']] . '">'.$caption.'</a></td>'.newline();
		}
		else
		{
			$html .= '<td><a href="'.$column_data['get_url_link'].'?'. $column_data['get_id_link'].'=' . $data[$column_data['mysql_field']] . '">'.$caption.'</a></td>'.newline();
		}
		
		
	}
	elseif (isset($column_data['variable_get_url_link']))
	{		
		$html .= '<td><a href="'.$column_data['variable_get_url_link'][$data[$column_data['variable_get_url_link']['row_result_lookup']]]['url'];
		$html .= '?';
		$get_array= array();
		foreach($column_data['variable_get_url_link'][$data[$column_data['variable_get_url_link']['row_result_lookup']]]['get_data'] as $key=>$value)
		{
			$get_array[] = $key . '=' .$data[$value];
		}
		
		$html .= implode('&', $get_array);
		$html .= '"';
		if(isset($column_data['target']))
		{
			if($column_data['target'] == 'blank')
			{
				$html .= 'target ="_blank" ';
			}
			
		}
		
		$html .= ' >';
		if(isset($column_data['url_caption']))
		{
			$html .= $column_data['url_caption'];
		}
		else
		{
			$html .= $data[$column_data['mysql_field']];
		}
		$html .='</a></td>'.newline();
	}
	elseif (isset($column_data['select']))
	{
		$html .= '<td><input   type = "radio" value = "'.$data[$column_data['mysql_field']].'" id="'.$data[$column_data['mysql_field']].'" name="radio" ';
		// took i out!if($i==0) $html .= ' checked="cehcked" ';
		$html .= ' />'.newline();
	}
	elseif (isset($column_data['checkbox']))
	{
		$html .= '<td><input  onchange="needToConfirm=true" type = "checkbox" id="checkbox" name="'.$data[$column_data['mysql_field']].'" ';
			if ($column_data['checkbox'] != 'enabled')
			{
				$html .= ' disabled = "disabled" ';
			}
			$html .=  ' checked = "checked" ';
			$html .= ' />'.newline();
		
		
		
	}
	else if (isset($column_data['round']))
	{
		$html .= '<td>' . number_format($data[$column_data['mysql_field']], $column_data['round']).'</td>'.newline();
	}
	else if (isset($column_data['thumbnail_name']))
	{
		//if the thumb exists display it...
		$thumbnail_path = POS_PATH . $column_data['thumbnail_path'] . $data[$column_data['thumbnail_name']] . $column_data['thumbnail_prefix'];
		$linked_path = POS_PATH . $column_data['thumbnail_link'] . $data[$column_data['thumbnail_name']] . $column_data['thumbnail_prefix'];
		
		$thumbnail_url = POS_URL . $column_data['thumbnail_path'] . $data[$column_data['thumbnail_name']] . $column_data['thumbnail_prefix'];
		$linked_file = POS_URL . $column_data['thumbnail_link'] . $data[$column_data['thumbnail_name']] . $column_data['thumbnail_prefix'];

		
		if (file_exists ( $thumbnail_path ) && file_exists ( $linked_path ))
		{
			$html .= '<td>';
			$html .= '<a href="'.$linked_file.'" target="_blank"><img src="'.$thumbnail_url.'" /></a>'.newline();
			$html .= '</td>'.newline();
		} else
		{
			$html .= '<td>';
			$html .= 'No Thumbnail';
			//$html .= $thumbnail_path . '<br>' . $linked_path;
			$html .= '</td>'.newline();
		}
	}
	else if (isset($column_data['html_new_link']))
	{
	
			$html .= '<td>';
			$html .= '<a href="'.addhttp($data[$column_data['mysql_field']]).'" target="_blank">'.$data[$column_data['mysql_field']].'</a>'.newline();
			$html .= '</td>'.newline();
	
	}
	else if (isset($column_data['mysql_field']))
	{
		if(isset($column_data['month_day']))
		{
			list($year,$month,$day) = split("-",$data[$column_data['mysql_field']]);
			if ($month == '00')
			{
				$html .= '<td></td>'.newline();
			}
			else
			{
				$html .= '<td>' . $month . '-' .$day.'</td>'.newline();
			}
		}
		else if(isset($column_data['date_format']))
		{
			if($column_data['date_format'] =='date')
			{
				$html .= '<td>' . getDateFromDatetime($data[$column_data['mysql_field']]) .'</td>'.newline();
			}
		}
		elseif(isset($column_data['encrypted']))
		{
			$html .= '<td>' . nl2br(craigsDecryption($data[$column_data['mysql_field']],0)).'</td>'.newline();
		}
		elseif(isset($column_data['type']))
		{
			if ($column_data['type'] =='checkbox')
			{
			
				$html .= '<td><input type = "checkbox" disabled = "disabled" id="'.$data[$column_data['mysql_field']].'" name="'.$data[$column_data['mysql_field']].'" ';

				if ($data[$column_data['mysql_field']])
				{
					$html .=  ' checked = "checked" ';
				}
				$html .= ' />'.newline();
			}
		}
		else
		{
			$html .= '<td>' . nl2br($data[$column_data['mysql_field']]).'</td>'.newline();
		}
	}
	
	else
	{
		$html .= '<td></td>'.newline();
	}
	return $html;
}
function addhttp($url) {
    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "http://" . $url;
    }
    return $url;
}
function createTransactionRecordsTableWithTotals($sql_statement_array, $table_columns, $class = 'generalTable', $number_of_rows_to_be_used_for_totals_caption = 1)
{
	if (isset($_GET['order']))
	{
		$order = $_GET['order'];
		if ($order == 'ASC')
		{
			$order = 'DESC';
		}
		else
		{
			$order ='ASC';
		}
	}
	else
	{
		$order = 'DESC';
	}
	//assuming the last statement is the one that gets the data
	$dbc = startTransaction();
	for($i=0;$i<sizeof($sql_statement_array)-1;$i++)
	{
		$result[] = runTransactionSQL($dbc, $sql_statement_array[$i]);
	}
		
	$data = getTransactionSQL($dbc, $sql_statement_array[sizeof($sql_statement_array)-1]);
	if (sizeof($data) > 0) 
	{ 
		// Table header:
		$getURL = getPageURLwithGETS();
	
		$html = '<table class = "'. $class . '">' .newline();
		$html .= '<thead><tr>' . newline();
		foreach($table_columns as $column)
		{
			if (isset($column['sort']))
			{
				$html .= '<th><a href="'.$getURL.'sort='.$column['mysql_field'].'&order='.$order.'">'.$column['th'].'</a></th>'.newline();
			}
			else
			{
				$html .= '<th>'.$column['th'].'</th>'.newline();
			}
		}
		$html .= '</tr></thead>'.newline();
		$html .= '<tbody>';
		$html .= '<tr style="background-color:yellow;border-bottom:1px solid rgb(50,50,50);">';
		if($number_of_rows_to_be_used_for_totals_caption > 0)
		{
			$html .= '<td colspan="'.$number_of_rows_to_be_used_for_totals_caption.'"><b>Totals</b></td>';
		}
		for($i=$number_of_rows_to_be_used_for_totals_caption;$i<sizeof($table_columns);$i++)
		{
			//
			if(isset($table_columns[$i]['total']))
			{	
				$html .= '<td><b>' .number_format(calculateSQLTotal($data, $table_columns[$i]['mysql_field']),0).'</b></td>';
			}
			else
			{
				$html .= '<td></td>';
			}
		}
		$html .= '</tr>';
		// Fetch and print all the records....
		for($i=0;$i<sizeof($data);$i++)
		{
			$html .= '<tr>';
			foreach($table_columns as $column_data)
			{
				if (isset($column_data['get_url_link']))
				{		
					$html .= '<td><a href="'.$column_data['get_url_link'].'?'. $column_data['mysql_field'].'=' . $data[$i][$column_data['mysql_field']] . '">'.$column_data['url_caption'].'</a></td>'.newline();
				}
				else if (isset($column_data['round']))
				{
					if($data[$i][$column_data['mysql_field']] == 0 || $data[$i][$column_data['mysql_field']] == null)
					{
						$html .= '<td>' . '</td>';
					}
					else
					{
						$html .= '<td>' . number_format($data[$i][$column_data['mysql_field']], $column_data['round']).'</td>'.newline();
					}
				}
				else if (isset($column_data['mysql_field']))
				{
					if(isset($column_data['month_day']))
					{
						list($year,$month,$day) = split("-",$data[$i][$column_data['mysql_field']]);
						if ($month == '00')
						{
							$html .= '<td></td>'.newline();
						}
						else
						{
							$html .= '<td>' . $month . '-' .$day.'</td>'.newline();
						}
					}
					elseif(isset($column_data['encrypted']))
					{
						$html .= '<td>' . nl2br(craigsDecryption($data[$i][$column_data['mysql_field']],0)).'</td>'.newline();
					}
					else
					{
						$html .= '<td>' . nl2br($data[$i][$column_data['mysql_field']]).'</td>'.newline();
					}
				}
				
				else
				{
					$html .= '<td></td>'.newline();
				}
			
				
			}
			$html .= '</tr>'.newline();
		}
		$html .= '</tbody></table>'.newline(); // Close the table.
	} 
	else 
	{ // If no records were returned.
		$html = '<p class="error">There are currently no records.</p>';
	}
	return $html;
}
function calculateSQLTotal($data, $field)
{
	$total = 0;
	for($i=0;$i<sizeof($data);$i++)
	{
		
		$total = $total + $data[$i][$field];
	}
	return $total;
}
function calculateSQLTotalForTableDefArray($td_data_array, $column)
{
	$total = 0;
	for($i=0;$i<sizeof($td_data_array);$i++)
	{
		$total = $total + $td_data_array[$i][$column]['value'];
	}
	return $total;
}
function createAccountRecordsTable($balance_array, $account_array, $class = 'generalTable')
{
		$balance = $balance_array[0]['balance'];
		$html = '<table class = "'. $class . '">' .newline();
		$html .= '<thead><tr>' . newline();
		$html .= '<th>Date</th>';
		$html .= '<th>Journal ID</th>';
		$html .= '<th>Journal</th>';
		$html .= '<th>Account</th>';
		$html .= '<th>Chart of <br> Account</th>';
		$html .= '<th>Description</th>';
		$html .= '<th>DEBIT</th>';
		$html .= '<th>CREDIT</th>';
		$html .= '<th>BALANCE</th>';
		$html.= '</thead>'.newline();
		$html .= '<tbody>';
		$html .= '<tr><td>'.$balance_array[0]['date'].'</td>';
		$html .= '<td></td>';
		$html .= '<td></td>';
		$html .= '<td></td>';
		$html .= '<td></td>';
		$html .= '<td>Opening Balance</td>';
		$html .= '<td></td>';
		$html .= '<td></td>';
		$html .= '<td>'.number_format($balance_array[0]['balance'],2).'</td></tr>';
		for($i=0;$i<sizeof($account_array);$i++)
		{
			$html .= '<tr>'.newline();
			$html .='<td>' . $account_array[$i]['date'] . '</td>';
			$html .='<td>' . $account_array[$i]['journal_id'] . '</td>';
			$html .='<td>' . $account_array[$i]['journal'] . '</td>';
			$html .='<td>' . $account_array[$i]['account_name'] . '</td>';
			$html .='<td>' . $account_array[$i]['chart_of_account_name'] . '</td>';
			$html .='<td>' . $account_array[$i]['description'] . '</td>';
			$debit = ($account_array[$i]['debit'] == 0) ? '' : number_format($account_array[$i]['debit'],2);
			$html .='<td>' . $debit . '</td>';
			$credit = ($account_array[$i]['credit'] == 0) ? '' : number_format($account_array[$i]['credit'],2);
			$html .='<td>' . $credit . '</td>';
			$balance = $balance - $account_array[$i]['debit'] + $account_array[$i]['credit'];
			$html .='<td>' . $balance . '</td>';
			$html .= '</tr>'.newline();
		}
		$html .= '</tbody></table>';
		return $html;
}

function createDataTableFromArray($array)
{
	$html = '<table class = "generalTable"><thead><tr>';
	$td_width = round(100 / sizeof($array[0]),2);
	for($j=0;$j<sizeof($array[0]);$j++)
	{
		$html.= '<th style="width:'.$td_width.'%;font-size: 0.7em">' . $array[0][$j] .'</th>';
	}
	$html .= '</tr></thead>' .newline();
	$html .= '<tbody>' .newline();
	for($i=1;$i<sizeof($array);$i++)
	{
		$html .= '<tr>';
		for($j=0;$j<sizeof($array[0]);$j++)
		{
			$html.= '<td style="width:'.$td_width.'%;font-size: 0.7em;">' . $array[$i][$j] .'</td>';
		}
		$html .= '</tr>' .newline();
	}
	$html .= '</tbody></table>';
	return $html;
}
function createHTMLTableFromMYSQLReturnArray($mysql_result_array, $class)
{
	$html = '<table class = "'.$class.'"><thead><tr>';
	//$td_width = round(100 / sizeof($array[0]),2);
	if (sizeof($mysql_result_array)>0)
	{
	
			foreach($mysql_result_array[0] as $key => $value)
			{
				$html.= '<th>' . $key .'</th>';
    		}
		
		$html .= '</tr></thead>' .newline();
		$html .= '<tbody>' .newline();
		for($i=0;$i<sizeof($mysql_result_array);$i++)
		{
			$html .= '<tr>';
			foreach($mysql_result_array[$i] as $key => $value)
			{
				$html.= '<td>' . $value .'</td>';
			}
			$html .= '</tr>' .newline();
		}
		$html .= '</tbody></table>';
	}
	else
	{
		$html = 'No Records Returned';
	}
	return $html;
}
function createNoRecordsTable($table_def_array, $class, $tbody_id)
{
		$html = '<table class = "'. $class . '" id = "'. $class . '">' .newline();
		$html .= '<thead><tr>' . newline();
		foreach($table_def_array as $td_array)
		{
			$html .= createTHForTableArrayFromTD_def($td_array);
		}
		$html .= '</tr></thead>'.newline();
		$html .= '<tbody id ="'.$tbody_id.'" >';
		$html .= '<tr><td colspan = "'.sizeof($table_def_array) .'" class="error">There are currently no records.</td></tr>';
		$html .= '</tbody>';
		$html .= '</table>'.newline(); // Close the table.
		return $html;
}
function createMYSQLArrayHTMLTable($table_def_array_with_data, $class, $tbody_id)
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
		$html = '<table class = "'. $class . '" id = "'. $class . '">' .newline();
		$html .= '<thead id="'. $tbody_id . '_thead"><tr>' . newline();
		foreach($table_def_array_with_data[0] as $td_array)
		{
			$html .= createTHForTableArrayFromTD_def($td_array);
		}
		$html .= '</tr></thead>'.newline();
		
		$html .= '<tbody id ="'.$tbody_id.'" >';
		for ($i=0;$i<sizeof($table_def_array_with_data);$i++)
		{
			$html .= '<tr>';
			foreach($table_def_array_with_data[$i] as $td_array)
			{
					$html .= createTDForTableArrayFromTD_def($td_array, $i, $tbody_id);
			}
			$html .= '</tr>'.newline();
		}
		$html .= '</tbody>';
		// if there are totals add them here
		if($totals == true)
		{
			$html .= '<tfoot id = "'.$tbody_id. '_tfoot" >';
			$html .= '<tr>';
			$html .= '<td>Totals</td>';
			$column_counter = 0;
			for($t=1;$t<sizeof($table_def_array_with_data[0]);$t++)
			{
				$column_counter++;
				if(isset($table_def_array_with_data[0][$t]['total']))
				{	
					$html .= '<td class="lined_cell">' .number_format(calculateSQLTotalForTableDefARray($table_def_array_with_data, $t),$table_def_array_with_data[0][$t]['total']).'</td>';
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
function createArrayHTMLTable($table_def_array_with_data, $class, $tbody_id)
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
		$html = '<table class = "'. $class . '" id = "'. $class . '">' .newline();
		$html .= '<thead id="'. $tbody_id . '_thead"><tr>' . newline();
		foreach($table_def_array_with_data[0] as $td_array)
		{
			$html .= createTHForTableArrayFromTD_def($td_array);
		}
		$html .= '</tr></thead>'.newline();
		
		$html .= '<tbody id ="'.$tbody_id.'" >';
		for ($i=0;$i<sizeof($table_def_array_with_data);$i++)
		{
			$html .= '<tr>';
			foreach($table_def_array_with_data[$i] as $td_array)
			{
					$html .= '<td>' . $td_array['value'] .'</td>';
			}
			$html .= '</tr>'.newline();
		}
		$html .= '</tbody>';
		// if there are totals add them here
		if($totals == true)
		{
			$html .= '<tfoot id = "'.$tbody_id. '_tfoot" >';
			$html .= '<tr>';
			$column_counter = 0;
			$html .= '<td>Totals</td>';
			for($t=1;$t<sizeof($table_def_array_with_data[0]);$t++)
			{
				$column_counter++;
				
				if(isset($table_def_array_with_data[0][$t]['total']))
				{	
					$html .= '<td class="lined_cell">' .number_format(calculateSQLTotalForTableDefARray($table_def_array_with_data, $t),$table_def_array_with_data[0][$t]['total']).'</td>';
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
function createTDForTableArrayFromTD_def($td_array, $row_number, $tbody_id)
{
	if ($td_array['type'] == 'input')
	{
		$html = '<td><input  id = "' . $td_array['mysql_post_field'] . '_' . $row_number . '" name="' . $td_array['mysql_post_field'] . '_' . $row_number . '" onkeypress = "changeRowAndColumnWithArrow(event, this, \''.$tbody_id .'\');return noEnter(event);" ';
		if(isset($td_array['tags'])) $html .= $td_array['tags'];
		if(isset($td_array['value']))
		{
			if (isset($td_array['round']))
			{
				$html .= ' value="'  . number_format($td_array['value'], $td_array['round']).'" ';
			} 
			else
			{
				$html .= ' value="' . $td_array['value'] . '" ';
			}
		}
		$html .= '/></td>' .newline();
	}
	elseif ($td_array['type'] == 'if_blank_then_input')
	{
		if(isset($td_array['value']) && $td_array['value'] != '')
		{
			if(isset($td_array['round']))
			{
				$html = '<td>' . number_format($td_array['value'], $td_array['round']) . '</td>'.newline();
			}
			else
			{
				$html = '<td>' . $td_array['value'] . '</td>'.newline();
			}
			
		}
		else
		{
			$html = '<td><input  id = "' . $td_array['mysql_post_field'] . '_' . $row_number . '" name="' . $td_array['mysql_post_field'] . '_' . $row_number . '" style="background:yellow;"onkeypress = "changeRowAndColumnWithArrow(event, this, \''.$tbody_id .'\');return noEnter(event);" ';
			if(isset($td_array['tags'])) $html .= $td_array['tags'];
			$html .= '/></td>' .newline();
		}
		
		
		
	}
	elseif ($td_array['type'] == 'hidden_input')
	{
		$html = createHiddenInput($td_array['mysql_post_field'] . '_' . $row_number, $td_array['value']);
		$html .= '<td>';
		if(isset($td_array['value'])) $html .= $td_array['value'];
		$html .= '</td>'.newline();
	}
	elseif ($td_array['type'] == 'textarea')
	{
		$html = '<td><textarea   onchange="needToConfirm=true" type ="text" id = "'.$td_array['mysql_post_field'] .'_' . $row_number .'" name="'.$td_array['mysql_post_field'] . '_' . $row_number . '" ';
		if(isset($td_array['tags'])) $html .= $td_array['tags'];
		$html .= '>';
		if(isset($td_array['value'])) $html .=  $td_array['value'];
		$html .= '</textarea></td>' .newline();
	}
	elseif ($td_array['type'] == 'checkbox')
	{
		$html = '<td><input  onchange="needToConfirm=true" type = "checkbox" id = "'.$td_array['mysql_post_field'] .'_' . $row_number .'" name="'.$td_array['mysql_post_field'] . '_' . $row_number .'" ';
		if(isset($td_array['tags'])) $html .= $td_array['tags'];
		if(isset($td_array['value']))
		{
			if ($td_array['value'] == '1' || strtolower($td_array['value']) == 'true' || strtolower($td_array['value']) == 'checked' || strtolower($td_array['value']) == 'yes')
			{
				$html .=  ' checked = "checked" ';
			}
		}
		
		$html .= '/></td>' .newline();
	}
	elseif ($td_array['type'] == 'select')
	{
		//$name = $td_array['mysql_post_field'] .'_' . $row_number;
//*********************probably will need to change the name if using a select....
		if(isset($td_array['value']))
		{
			$select_html = addValueToSelect($td_array['html'], $td_array['value']);
		}
		else
		{
			$select_html = addValueToSelect($td_array['html'], 'false');
		}
		//if(isset($td_array['tags'])) $select_html .= addTagToSelect($select_html, $td_array['tags']);
		$html = '<td>'. $select_html .'</td>'.newline();
	}
	elseif ($td_array['type'] == 'date')
	{
		if(isset($td_array['html']))
		{
			$html = '<td>'. $td_array['html'] .'</td>'.newline();
		}
		else
		{
			if(isset($td_array['value']))
			{
				$html = '<td>'. dateSelect($td_array['mysql_post_field'], $td_array['value'], $td_array['tags']) .'</td>'.newline();
			}
			else
			{
				$html = '<td>'. dateSelect($td_array['mysql_post_field'], date('Y-m-d'), $td_array['tags']) .'</td>'.newline();
			}
		}
	}
	elseif ($td_array['type'] == 'none')
	{
		if(isset($td_array['html']))
		{
			$html = '<td>' . $td_array['html'] . '</td>'.newline();
		}
		else
		{
			$html = '<td>' . '</td>'.newline();
		}
	}
	elseif ($td_array['type'] == 'td')
	{
		if(isset($td_array['value']))
		{
			if(isset($td_array['round']))
			{
				$html = '<td>' . number_format($td_array['value'], $td_array['round']) . '</td>'.newline();
			}
			else
			{
				$html = '<td>' . $td_array['value'] . '</td>'.newline();
			}
		}
		else
		{
			$html = '<td>' . '</td>'.newline();
		}
	}
	else
	{
		$html = '<td>no type match</td>'.newline();
	}
	return $html;
		
}
function createTHForTableArrayFromTD_def($td_array)
{
		
		if (isset($td_array['caption']))
		{
			$html = '<th>' . $td_array['caption'] .'</th>';
		}
		else if (isset($td_array['th']))
		{
			$html = '<th>'.$td_array['th'].'</th>'.newline();
		}
		else if (isset($td_array['mysql_result_field']))
		{
			$html ='<th>' . $td_array['mysql_result_field'] .'</th>';
		}
		else 
		{
			$html ='<th>' . '</th>';
		}
		return $html;
}
function createHTMLTableForMYSQLInsert($table_def, $class = 'mysqlTable')
{
	$html = '<table class = "'.$class.'">' .newline();
	$html .= '<tbody>';
	for($i=0;$i<sizeof($table_def);$i++)
	{
		$html .= '<tr>';
		$html .= createTHFromTD_def($table_def[$i]);
		$html .= createTDFromTD_def($table_def[$i]);
		$html .= '</tr>'.newline();
	} 
	$html .= '</tbody></table>'.newline(); // Close the table.
	return $html;
}

function createSimpleHorzontalHTMLTable($table_def, $tbody_name = 'simple_tbody')
{
	$html =  '<TABLE >';
	$html .= '<thead >' .newline();
	$html .= '<tr>'.newline();
	for($i=0;$i<sizeof($table_def);$i++)
	{
		$html .= createTHFromTD_def($table_def[$i]);
	}
	$html .= '</tr>'.newline();
	$html .= '</thead>'.newline();
	//this is the body
	$html .=  '	<tbody id = "'.$tbody_name.'" name = "'.$tbody_name.'" class = "static_contents_tbody" ></tbody>';
	$html .= '<tr>'.newline();
	for($col=0;$col<sizeof($table_def);$col++)
	{
		$html .= createTDFromTD_def($table_def[$col]);
	}
	$html .= '</tr>'.newline();
	$html .=  '</table>';
	return $html;

}
function getEnumValues($db_table, $db_field)
{
	
	$dbc = openPOSDatabase();
	//this is taking a full second!, trying this addition....
	//runTransactionSQL($dbc,"set global innodb_stats_on_metadata=0;");
	$sql = "SELECT COLUMN_TYPE
			FROM information_schema.columns
			WHERE TABLE_NAME = '$db_table'
			AND COLUMN_NAME = '$db_field'
			LIMIT 1
			";
			
	$r_array = getTransactionSQL($dbc, $sql);
	mysqli_close($dbc);
	$result = str_replace(array("enum('", "')", "''"), array('', '', "'"), $r_array[0]['COLUMN_TYPE']);
	$arr = explode("','", $result);
	return $arr;
}
function createEnumSelectFast($name, $enum_values, $selected_value, $option_all = 'off', $select_events ='')
{
	//this is a faster procedure!
	
	$html = '<select style="width:100%;" id = "'.$name .'" name="'.$name.'" ';
	$html .= $select_events;
	$html .= '>';
	//Add an option for not selected
	//$html .= '<option value="false">Select Value</option>';
	//add an option for all product categories
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($selected_value  == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All</option>';
	}
	for($i = 0;$i < sizeof($enum_values); $i++)
	{
		$html .= '<option value="' . $enum_values[$i] . '"';
		if ( ($enum_values[$i] == $selected_value) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $enum_values[$i] .'</option>';
	}
	$html .= '</select>';
	return $html;
}
function createEnumSelect($name, $db_table, $db_field, $selected_value, $option_all = 'off', $select_events ='')
{
	//this is a slow procedure!
	
	$enum_values = getEnumValues($db_table, $db_field);
	$html = '<select style="width:100%;" id = "'.$name .'" name="'.$name.'" ';
	$html .= $select_events;
	$html .= '>';
	//Add an option for not selected
	//$html .= '<option value="false">Select Value</option>';
	//add an option for all product categories
	if ($option_all != 'off')
	{
		$html .= '<option value ="all"';
		if ($selected_value  == 'all')
		{
			$html .= ' selected="selected"';
		}
		$html .= '>All</option>';
	}
	for($i = 0;$i < sizeof($enum_values); $i++)
	{
		$html .= '<option value="' . $enum_values[$i] . '"';
		if ( ($enum_values[$i] == $selected_value) ) 
		{
			$html .= ' selected="selected"';
		}
			
		$html .= '>' . $enum_values[$i] .'</option>';
	}
	$html .= '</select>';
	return $html;
}
function createArrayTDFromTD_def($td_array)
{
	$td_array['db_field'] = $td_array['db_field'] .'[]';
	if (isset($td_array['post_name']))
	{
		$td_array['post_name'] = $td_array['post_name'] .'[]';
	}
	return createTDFromTD_def($td_array);
}
function createTDFromTD_def($td_array)
{
	if (isset($td_array['post_name']))
	{
		$post_name = $td_array['post_name'];
	}
	else
	{
		if(isset($td_array['db_field']))
		{
			$post_name = $td_array['db_field'];
		}
		else
		{
			$post_name = '';
		}
	}
	if ($td_array['type'] == 'input')
	{
		$html = '<td><input id = "'.$post_name .'" name="'.$post_name .'" ';
		if(isset($td_array['tags']))
		{
		 $html .= $td_array['tags'];
		}
		else
		{
			$html .= ' onchange="needToConfirm=true" ';
		}
		if(isset($td_array['value'])) 
		{
			if(isset($td_array['round']))
			{
				$html .= ' value="' . round($td_array['value'], $td_array['round']) . '" ';
			}
			else
			{
				$html .= ' value="' . $td_array['value'] . '" ';
			}
		}
		$html .= '/></td>' .newline();
	}
	elseif ($td_array['type'] == 'file_input')
	{
	
		if(isset($td_array['tags']))
		{
		 $tags = $td_array['tags'];
		}
		else
		{
			$tags = '';
		}
		$html = '<td>';
		
		if($td_array['db_id_val'] != '') 
		{
			//need to put it as a download link - a download.php file needs to do the dirty work
			$html .= '<a href="'.DOWNLOADER_URL.'?db_table='.$td_array['db_table'].'&db_id_name='.$td_array['db_id_name'].'&db_id_val='.$td_array['db_id_val'].'">DOWNLOAD FILE</a>';
			if (isset($td_array['view']) && $td_array['view'] == 'edit')
			{
				//need to add the upload for replacing
				$html .= '<br>'.createFileUploadInput($td_array['name'], $tags);
			}
		}
		else
		{
			
			$html .= createFileUploadInput($td_array['name'], $tags);
		}
		$html .= '</td>' .newline();	}
	elseif ($td_array['type'] == 'textarea')
	{
		$html = '<td><textarea   onchange="needToConfirm=true" type ="text" id="'.$post_name .'" name="'.$post_name .'" ';
		if(isset($td_array['tags'])) $html .= $td_array['tags'];
		$html .= '>';
		if(isset($td_array['value'])) $html .=  $td_array['value'];
		$html .= '</textarea></td>' .newline();
	}
	elseif ($td_array['type'] == 'checkbox')
	{
		$html = '<td><input  onchange="needToConfirm=true" type = "checkbox" id="'.$post_name .'" name="'.$post_name .'" ';
		if(isset($td_array['tags'])) $html .= $td_array['tags'];
		if(isset($td_array['value']))
		{
			if ($td_array['value'] == '1' || strtolower($td_array['value']) == 'true' || strtolower($td_array['value']) == 'checked' || strtolower($td_array['value']) == 'yes')
			{
				$html .=  ' checked = "checked" ';
			}
		}
		
		$html .= '/></td>' .newline();
	}
	elseif ($td_array['type'] == 'row_checkbox')
	{
		$html = '<td><input  onchange="needToConfirm=true" type = "checkbox" id="'.$post_name .'" name="row_checkbox_'.$td_array['checkbox_index'] .'" ';
		if(isset($td_array['tags'])) $html .= $td_array['tags'];
		if(isset($td_array['value']))
		{
			if ($td_array['value'] == '1' || strtolower($td_array['value']) == 'true' || strtolower($td_array['value']) == 'checked' || strtolower($td_array['value']) == 'yes')
			{
				$html .=  ' checked = "checked" ';
			}
		}
		
		$html .= '/></td>' .newline();
	}
	elseif ($td_array['type'] == 'select')
	{
		if(isset($td_array['value']))
		{
			$select_html = addValueToSelect($td_array['html'], $td_array['value']);
		}
		else
		{
			//$select_html = addValueToSelect($td_array['html'], 'false');
			$select_html =$td_array['html'];
		}
		//if(isset($td_array['tags'])) $select_html .= addTagToSelect($select_html, $td_array['tags']);
		$html = '<td>'. $select_html .'</td>'.newline();
	}
	elseif ($td_array['type'] == 'date')
	{
		/*if(isset($td_array['html']))
		{
			$html = '<td>'. $td_array['html'] .'</td>'.newline();
		}
		else
		{*/
			if(isset($td_array['value']))
			{
				if(isset($td_array['separate_date']))
				{
					if($td_array['separate_date'] == 'time')
					{
						$html = '<td>'. timeSelect($post_name . '_time', gettimefromdatetime($td_array['value']), $td_array['tags']) .'</td>'.newline();

					}
					else
					{
						$html = '<td>'. dateSelect($post_name, getdatefromdatetime($td_array['value']), $td_array['tags']) .'</td>'.newline();
					}
				}
				else
				{
					$html = '<td>'. dateSelect($post_name, $td_array['value'], $td_array['tags']) .'</td>'.newline();

				}
			}
			else
			{
				$html = '<td>'. dateSelect($post_name, date('Y-m-d'), $td_array['tags']) .'</td>'.newline();
			}
		//}
	}
	elseif ($td_array['type'] == 'time')
	{
			if(isset($td_array['value']))
			{
				$html = '<td>'. timeSelect($td_array['post_name'], gettimefromdatetime($td_array['value']), $td_array['tags']) .'</td>'.newline();

			}
			else
			{
				$html = '<td>'. timeSelect($td_array['post_name'], '00:00:00', $td_array['tags']) .'</td>'.newline();
			}
		
		
	}
	//probably don't use this one - looks pretty specific to the product
	elseif ($td_array['type'] == 'multiselect')
	{
		if(isset($td_array['value']))
		{
			$html = '<td>'. createSecondaryCategorySelect($post_name, $td_array['value']) .'</td>'.newline();
		}
		else
		{
			$html = '<td>'. createSecondaryCategorySelect($post_name, 'false') .'</td>'.newline();
		}
	}
	else if ($td_array['type'] == 'multi_select')
	{
		$html = '<td>'.$td_array['html'].'</td>';
	}
	elseif ($td_array['type'] == 'td' )
	{
		$html = '<td>';
		if(isset($td_array['value'])) 
		{
			 if(isset($td_array['round'])) 
			 {

				$html .= number_format($td_array['value'], $td_array['round']);
	
			 }
			 else
			 {
			 	$html .=  $td_array['value'];
			 }
		}
		$html .= '</td>'.newline();
	}
	elseif ($td_array['type'] == 'row_number' )
	{
		$html = '<td>';
		if(isset($td_array['value'])) 
		{
			 $html .=  $td_array['value'];
			 $html .=  createHiddenInput($post_name, $td_array['value']);
		}
		$html .= '</td>'.newline();
	}
	elseif ($td_array['type'] == 'td_hidden_input' )
	{
		$html = '<td>';
		if(isset($td_array['value'])) 
		{
			 $html .=  $td_array['value'];
			 $html .=  createHiddenInput($post_name, $td_array['value']);
		}
		$html .= '</td>'.newline();
	}
	elseif ($td_array['type'] == 'hidden_input' )
	{
		if(isset($td_array['value'])) 
		{
			 $html =  createHiddenInput($post_name, $td_array['value']);
		}
	}
	elseif ($td_array['type'] == 'link' )
	{
		if(isset($td_array['value'])) 
		{
			$html = '<td>';
			$get = $td_array['get_id_link'] . '=' . $td_array['value'];
			$caption = (isset($td_array['url_caption'])) ? $td_array['url_caption'] : $td_array['value'];
			$link = addGetToUrl($td_array['get_url_link'],$get);
			 $html .= url_blank_link($link, $caption);
			 $html .= '</td>';
		}
		else
		{
			$html = '<td>';
			 $html .= '</td>';
		}
	}
	elseif ($td_array['type'] == 'html_link' )
	{
		if(isset($td_array['value'])) 
		{
			$html = '<td>';
			//$get = $td_array['get_id_link'] . '=' . $td_array['value'];
			$caption = (isset($td_array['url_caption'])) ? $td_array['url_caption'] : $td_array['value'];
			//$link = addGetToUrl($td_array['get_url_link'],$get);
			 $html .= url_blank_link($td_array['value'], $caption);
			 $html .= '</td>';
		}
		else
		{
			$html = '<td>';
			 $html .= '</td>';
		}
	}
	elseif ($td_array['type'] == 'none' )
	{
		$html = '<td>';
		if(isset($td_array['html']))
		{
		 $html .=  $td_array['html'];
		}
		$html .= '</td>'.newline();
	}
	else
	{				

		$html = '<td>no type match</td>'.newline();
	}
	return $html;
		
}
function createTHFromTD_def($td_array)
{
		$th_width = (isset($td_array['th_width'])) ? ' style="width:'.$td_array['th_width'].'" ' : '';
		if(isset($td_array['type']) && $td_array['type'] != 'hidden' && $td_array['type'] != 'hidden_input')
		{
			if (isset($td_array['caption']))
			{
				$html = '<th' . $th_width .'>' . $td_array['caption'] .'</th>'.newline();;
			}
			else if (isset($td_array['th']))
			{
				$html = '<th' . $th_width .'>'.$td_array['th'].'</th>'.newline();
			}
			else if (isset($td_array['db_field']))
			{
				$html ='<th' . $th_width .'>' . $td_array['db_field'] .'</th>'.newline();;
			}
			else 
			{
				$html ='<th' . $th_width .'>' . '</th>';
			}
			return $html;
		}
}

function createTFootForView($column_defintion, $data=array())
{
	//look for total
	$html = '';
	$total_line = false;
	$footer = false;

	
	for($i=0;$i<sizeof($column_defintion);$i++)
	{
		if(isset($column_defintion[$i]['total']))
		{
			$total_line = true;
		}
		if(isset($column_defintion[$i]['footer']))
		{
			$footer = true;
		}
	}
	$num_columns = 0;
	for($i=0;$i<sizeof($column_defintion);$i++)
	{
		if($column_defintion[$i]['type'] == 'hidden' || $column_defintion[$i]['type'] == 'row_checkbox')
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
		for($i=0;$i<sizeof($column_defintion);$i++)
		{
			if(isset($column_defintion[$i]['total']))
			{
				//get the total
				$total = 0;
				for($tot=0;$tot<sizeof($data);$tot++)
				{
					$total = $total + $data[$tot][$column_defintion[$i]['db_field']];
				}
				$html .= '<td>';
				$html .= number_format($total,$column_defintion[$i]['total']);
				$html .='</td>';
			}
			else
			{
				if($column_defintion[$i]['type'] != 'hidden' && $column_defintion[$i]['type'] != 'row_checkbox')
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
		for($i=0;$i<sizeof($column_defintion);$i++)
		{
			
			if(isset($column_defintion[$i]['footer']))
			{
				for($j=0;$j<sizeof($column_defintion[$i]['footer']);$j++)
				{
					$html .= '<tr>';
					$html .= '<th colspan = "'. ($col_span_counter) . '" class = "emptyCell">';
					$html .= $column_defintion[$i]['footer'][$j]['caption'];
					$html .= '</th>';
					
					$html .= createTDFromTD_def($column_defintion[$i]['footer'][$j]);
					
					/*$html .='<td>';
					$html .= '<input class="footerCell" id = "'.$column_defintion[$i]['footer'][$j]['db_field'] .'" name="'.$column_defintion[$i]['footer'][$j]['db_field'] .'" />';
					$html .= '</td>';*/
					$html .= '<td colspan = "'. ($num_columns - $col_span_counter -1) .'"class = "emptyCell"></td>';
					$html .= '</tr>'.newline();
				}
			}
			else
			{
				if($column_defintion[$i]['type'] != 'hidden')
				{
					$col_span_counter = $col_span_counter+1;
				}
			}
		}
	}
	return $html;
}
function createTFootFromTD_def($column_defintion, $data=array())
{
	//look for total
	$html = '';
	$total_line = false;
	$footer = false;

	
	for($i=0;$i<sizeof($column_defintion);$i++)
	{
		if(isset($column_defintion[$i]['total']))
		{
			$total_line = true;
		}
		if(isset($column_defintion[$i]['footer']))
		{
			$footer = true;
		}
	}
	$num_columns = 0;
	for($i=0;$i<sizeof($column_defintion);$i++)
	{
		if($column_defintion[$i]['type'] != 'hidden')
		{
			$num_columns ++;
		}
	}
	if ($total_line)
	{
		$col_span_counter = 0;
		$html .= '<tr>';
		for($i=0;$i<sizeof($column_defintion);$i++)
		{
			if(isset($column_defintion[$i]['total']))
			{
				//get the total
				$total = 0;
				for($tot=0;$tot<sizeof($data);$tot++)
				{
					$total = $total + $data[$tot][$column_defintion[$i]['db_field']];
				}
				$html .= '<td>';
				$html .= '<input tabindex="-1" readonly = "readonly" id = "'.$column_defintion[$i]['db_field'] .'_total" name="'.$column_defintion[$i]['db_field'] .'_total" />';
				$html .='</td>';
			}
			else
			{
				if($column_defintion[$i]['type'] != 'hidden')
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
		for($i=0;$i<sizeof($column_defintion);$i++)
		{
			
			if(isset($column_defintion[$i]['footer']))
			{
				for($j=0;$j<sizeof($column_defintion[$i]['footer']);$j++)
				{
					$html .= '<tr>';
					$html .= '<th colspan = "'. ($col_span_counter) . '" class = "emptyCell">';
					$html .= $column_defintion[$i]['footer'][$j]['caption'];
					$html .= '</th>';
					
					$html .= createTDFromTD_def($column_defintion[$i]['footer'][$j]);
					
					/*$html .='<td>';
					$html .= '<input class="footerCell" id = "'.$column_defintion[$i]['footer'][$j]['db_field'] .'" name="'.$column_defintion[$i]['footer'][$j]['db_field'] .'" />';
					$html .= '</td>';*/
					$html .= '<td colspan = "'. ($num_columns - $col_span_counter -1) .'"class = "emptyCell"></td>';
					$html .= '</tr>'.newline();
				}
			}
			else
			{
				if($column_defintion[$i]['type'] != 'hidden')
				{
					$col_span_counter = $col_span_counter+1;
				}
			}
		}
	}
	return $html;
}
function createTableForMYSQLInsert($table_def, $form_handler, $complete_location, $cancel_location)
{
	
	$html = confirmNavigation();
	//$html .= includeJavascriptLibrary();
	$html .= '<form action="' . $form_handler.'" method="post" onsubmit="return validateMYSQLInsertForm()">';
	$html .= createHTMLTableForMYSQLInsert($table_def);
	//Add the submit/canel buttons
	$html .= '<p><input class ="button" type="submit" name="submit" value="Submit" onclick="needToConfirm=false;"/>' .newline();
	$html .= '<input class = "button" type="button" name="cancel" value="Cancel" onclick="cancelForm()"/>';
	$html .= createHiddenSerializedInput('table_def', prepareTableDefForPost($table_def));	
	$html .= createHiddenInput('complete_location', $complete_location);
	$html .= createHiddenInput('cancel_location', $cancel_location);
	//close the form
	$html .= '</form>' .newline();
	//drop the array out for form validation
	$json_table_def = json_encode($table_def);
	$html .= '<script>var json_table_def = ' . $json_table_def . ';</script>';
	$html .= '<script>document.getElementsByName("' . $table_def[0]['db_field'] .'")[0].focus();</script>';
	$html .= '<script>var complete_location = "' . $complete_location . '";</script>';
	$html .= '<script>var cancel_location = "' . $cancel_location . '";</script>';
	return $html;
}
function prepareArrayTableForPost($table_def)
{
	
	//want to only keep the data field for the post info... strip out everything else
	$new_table_def = array();
	for ($i=0;$i<sizeof($table_def);$i++)
	{
		for ($j=0;$j<sizeof($table_def[$i]);$j++)
		{
				$new_table_def[$i][$j]['mysql_post_field'] = $table_def[$i][$j]['mysql_post_field'];
				if (isset($table_def[$i][$j]['type']))
				{
					$new_table_def[$i][$j]['type'] = $table_def[$i][$j]['type'];
				}
			
		}
	}
	return $new_table_def;
}
function prepareTableDefArrayForPost($table_def)
{
	$new_def = array();
	for($i=0;$i<sizeof($table_def);$i++)
	{
		$new_def[$i] = prepareTableDefForPost($table_def[$i]);
	}
	return $new_def;
}
function prepareTableDefForPost($table_def)
{
	//want to only keep the data field for the post info... strip out everything else
	$new_table_def = array();
	for ($i=0;$i<sizeof($table_def);$i++)
	{
		if (isset($table_def[$i]['db_field'])&& $table_def[$i]['db_field'] != '')
		{
			$new_table_def[$i]['db_field'] = $table_def[$i]['db_field'];
			if (isset($table_def[$i]['type']))
			{
				$new_table_def[$i]['type'] = $table_def[$i]['type'];
			}
			if (isset($table_def[$i]['validate']))
			{
				$new_table_def[$i]['validate'] = $table_def[$i]['validate'];
			}
			if (isset($table_def[$i]['caption']))
			{
				$new_table_def[$i]['caption'] = $table_def[$i]['caption'];
			}
		}
	}
	return $new_table_def;
}
function prepareArrayTableDefForJavascript($table_def)
{
	$new_table_def = array();
	$counter = 0;
	if (sizeof($table_def)>0)
	{
		for ($i=0;$i<sizeof($table_def[0]);$i++)
		{
			if (isset($table_def[0][$i]['mysql_result_field']))
			{
				$new_table_def[$counter]['mysql_result_field'] = $table_def[0][$i]['mysql_result_field'];
				$new_table_def[$counter]['mysql_post_field'] = $table_def[0][$i]['mysql_post_field'];
				if (isset($table_def[0][$i]['th']))
				{
					$new_table_def[$counter]['th'] = $table_def[0][$i]['th'];
				}
				
				$counter++;
			}
		}
	}
	$json_table_def = json_encode($new_table_def);
	return $json_table_def;
}
function prepareTableDefArrayForJavascriptVerification($table_def_array)
{
	//drop the array out for form validation - just want the db_filed ifdefined, validate
	$new_table_def = array();
	$counter = 0;
	foreach($table_def_array as $table_def)
	{
		for ($i=0;$i<sizeof($table_def);$i++)
		{
			if (isset($table_def[$i]['db_field']) && $table_def[$i]['db_field'] != '')
			{
				$new_table_def[$counter]['db_field'] = $table_def[$i]['db_field'];
				if (isset($table_def[$i]['validate']))
				{
					$new_table_def[$counter]['validate'] = $table_def[$i]['validate'];
				}
				if (isset($table_def[$i]['db_table']))
				{
					$new_table_def[$counter]['db_table'] = $table_def[$i]['db_table'];
				}
				if (isset($table_def[$i]['caption']))
				{
					$new_table_def[$counter]['caption'] = $table_def[$i]['caption'];
				}
				$counter++;
			}
		}
	}
	$json_table_def = json_encode($new_table_def);
	return $json_table_def;
	

}
function prepareTableDefForJavascriptTableGeneration($table_def)
{

	$json_table_def = json_encode($table_def);
	return $json_table_def;
}

//FORMS
function createDeleteForm($form_html, $form_handler)
{

	$html = '<form action="' . $form_handler.'" id="form_id" method="post" >';
	$html.= $form_html;
	$html .= '<p><input class ="button" type="submit" id = "submit" name="submit" value="Delete" />' .newline();
	$html .= '<input class = "button" type="button" name="cancel" value="Cancel" />';
	//close the form
	$html .= '</form>' .newline();
		$html .= '<script>var formId = "form_id";</script>';

	return $html;
}
function createFormForMYSQLInsert($table_def, $table_html, $form_handler, $complete_location, $cancel_location)
{
	$html = confirmNavigation();
	//$html .= includeJavascriptLibrary();
	$html .= '<form action="' . $form_handler.'" id="form_id" method="post" onsubmit="return validateMYSQLInsertForm()">';
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
function createFormWithNoCancelJavascript($table_def, $table_html, $form_handler, $complete_location, $cancel_location)
{
	$html = confirmNavigation();
	//$html .= includeJavascriptLibrary();
	$html .= '<form action="' . $form_handler.'" id="form_id" method="post" ">';
	$html.= $table_html;
	$html .= '<p><input class ="button" type="submit"   id = "submit" name="submit" value="Submit" onclick="needToConfirm=false;"/>' .newline();
	$html .= '<input class = "button" type="submit" id = "cancel" name="cancel"  value="Cancel" />';
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

function createFormForMYSQLArrayInsert($table_def, $table_html, $form_handler, $complete_location, $cancel_location)
{
	
	$html = confirmNavigation();
	//$html .= includeJavascriptLibrary();
	$html .= '<form action="' . $form_handler.'" id="form_id" method="post" >';
	$html.= $table_html;
	$html .= '<p><input class ="button" type="submit" id = "submit" name="submit" value="Submit" onclick="needToConfirm=false;"/>' .newline();
	$html .= '<input class = "button" type="button" name="cancel" value="Cancel" onclick="cancelForm()"/>';
	$html .= createHiddenSerializedInput('table_def', prepareArrayTableForPost($table_def)).newline();	
	$html .= createHiddenInput('complete_location', $complete_location);
	$html .= createHiddenInput('cancel_location', $cancel_location);
	//close the form
	$html .= '</form>' .newline();
	//drop the array out for form validation
	$html .= '<script>var formId = "form_id";</script>';

	$html .= '<script>var json_table_def = ' . prepareArrayTableDefForJavascript($table_def) . ';</script>';
	$html .= '<script>var complete_location = "' . $complete_location . '";</script>';
	$html .= '<script>var cancel_location = "' . $cancel_location . '";</script>';
	return $html;
	
}
function createFormForMultiMYSQLInsert($table_def_array, $table_html, $form_handler, $complete_location, $cancel_location)
{

	$html = confirmNavigation();
	$html .= '<form action="' . $form_handler.'" id="form_id" method="post" onsubmit="return validateMYSQLInsertForm()">';
	$html.= $table_html;
	$html .= '<p><input class ="button" type="submit" id = "submit" name="submit" value="Submit" onclick="needToConfirm=false;"/>' .newline();
	$html .= '<input class = "button" type="button" name="cancel" value="Cancel" onclick="cancelForm()"/>';
	$html .= createHiddenSerializedInput('table_def_array', prepareTableDefArrayForPost($table_def_array));

	$html .= createHiddenInput('complete_location', $complete_location);
	$html .= createHiddenInput('cancel_location', $cancel_location);
	//close the form
	$html .= '</form>' .newline();
	
	$html .= '<script>var json_table_def = ' . prepareTableDefArrayForJavascriptVerification($table_def_array) . ';</script>';
	$html .= '<script>var formId = "form_id";</script>';

	$html .= '<script>document.getElementsByName("' . $table_def_array[0][findElementToFocus($table_def_array[0])]['db_field'] .'")[0].focus();</script>';
	$html .= '<script>var complete_location = "' . $complete_location . '";</script>';
	$html .= '<script>var cancel_location = "' . $cancel_location . '";</script>';
	return $html;
	
}
function createMultiPartFormForMultiMYSQLInsert($table_def_array, $table_html, $form_handler, $complete_location, $cancel_location)
{
	$html = confirmNavigation();
	$html .= '<form enctype="multipart/form-data" id="form_id" action="' . $form_handler.'" method="post" onsubmit="return validateMYSQLInsertForm()">';
	$html.= $table_html;
	$html .= '<p><input class ="button" type="submit" id = "submit" name="submit" value="Submit" onclick="needToConfirm=false;"/>' .newline();
	$html .= '<input class = "button" type="button" name="cancel" value="Cancel" onclick="cancelForm()"/>';
	$html .= createHiddenSerializedInput('table_def_array', prepareTableDefArrayForPost($table_def_array));

	$html .= createHiddenInput('complete_location', $complete_location);
	$html .= createHiddenInput('cancel_location', $cancel_location);
	
	//unique token to prevent double submit
	$double_submit_token = md5(session_id() . time());
	$_SESSION['double_submit_token'] = $double_submit_token;
	$html .= createHiddenInput('double_submit_token', $double_submit_token);
	
	//close the form
	$html .= '</form>' .newline();
	
	$html .= '<script>var json_table_def = ' . prepareTableDefArrayForJavascriptVerification($table_def_array) . ';</script>';
	$html .= '<script>var formId = "form_id";</script>';

	//$html .= '<script>document.getElementsByName("' . $table_def_array[0][findElementToFocus($table_def_array[0])]['db_field'] .'")[0].focus();</script>';
	$html .= '<script>var complete_location = "' . $complete_location . '";</script>';
	$html .= '<script>var cancel_location = "' . $cancel_location . '";</script>';
	return $html;
	
}

function createFormWithHorizontalTableForMYSQLInsert($table_def, $form_handler, $complete_location, $cancel_location)
{
	
	$html = confirmNavigation();
	//$html .= includeJavascriptLibrary();
	$html .= '<form action="' . $form_handler.'" method="post" onsubmit="return validateMYSQLInsertForm()">';
	$html .= '<table class = "mysqlTable">' .newline();
	$html .= '<thead><tr>';
	for($i=0;$i<sizeof($table_def);$i++)
	{
		//if the field is dynamic we should check it here.....
		if(isset($table_def[$i]['caption']))
		{
			$html .= '<th>' . $table_def[$i]['caption'] .'</th>';
		}
		else
		{
			$html .='<th>' . $table_def[$i]['db_field'] .'</th>';
		}
	}
	$html .= '</tr></thead>' .newline();
	$html .= '<tbody>';
	$html .= '<tr>';
	for($i=0;$i<sizeof($table_def);$i++)
	{
		
		if ($table_def[$i]['type'] == 'input')
		{
			$html .= '<td><input onchange="needToConfirm=true" name="'.$table_def[$i]['db_field'] .'" ';
			if(isset($table_def[$i]['tags'])) $html .= $table_def[$i]['tags'];
			if(isset($table_def[$i]['value'])) $html .= ' value="' . $table_def[$i]['value'] . '" ';
			$html .= '/></td>' .newline();
		}
		elseif ($table_def[$i]['type'] == 'textarea')
		{
			$html .= '<td><textarea   onchange="needToConfirm=true" type ="text" name="'.$table_def[$i]['db_field'] .'" ';
			if(isset($table_def[$i]['tags'])) $html .= $table_def[$i]['tags'];
			$html .= '>';
			if(isset($table_def[$i]['value'])) $html .=  $table_def[$i]['value'];
			$html .= '</textarea></td>' .newline();
		}
		elseif ($table_def[$i]['type'] == 'checkbox')
		{
			$html .= '<td><input  onchange="needToConfirm=true" type = "checkbox" name="'.$table_def[$i]['db_field'] .'" ';
			if(isset($table_def[$i]['tags'])) $html .= $table_def[$i]['tags'];
			if(isset($table_def[$i]['value']))
			{
				if ($table_def[$i]['value'] == '1' || strtolower($table_def[$i]['value']) == 'true' || strtolower($table_def[$i]['value']) == 'checked' || strtolower($table_def[$i]['value']) == 'yes')
				{
					$html .=  ' checked = "checked" ';
				}
			}
			
			$html .= '/></td>' .newline();
		}
		elseif ($table_def[$i]['type'] == 'select')
		{
			$html .= '<td>'. $table_def[$i]['html'] .'</td>';
		}
		else
		{
			$html .= '<td>no type match</td>'.newline();
		}
		
	} 
	$html .= '</tr>'.newline();
	$html .= '</tbody></table>'.newline(); // Close the table.
	//Add the submit/canel buttons
	$html .= '<p><input class ="button" type="submit" name="submit" id="submit" value="Submit" onclick="needToConfirm=false;"/>' .newline();
	$html .= '<input class = "button" type="button" name="cancel" value="Cancel" onclick="cancelForm()"/>';
	$html .= createHiddenSerializedInput('table_def', $table_def);	
	$html .= createHiddenInput('complete_location', $complete_location);
	$html .= createHiddenInput('cancel_location', $cancel_location);
	//close the form
	$html .= '</form>' .newline();
	//drop the array out for form validation
	$json_table_def = json_encode($table_def);
	$html .= '<script>var json_table_def = ' . $json_table_def . ';</script>';
	$html .= '<script>document.getElementsByName("' . $table_def[0]['db_field'] .'")[0].focus();</script>';
	$html .= '<script>var complete_location = "' . $complete_location . '";</script>';
	$html .= '<script>var cancel_location = "' . $cancel_location . '";</script>';
		$html .= '<script>var formId = "form_id";</script>';

	return $html;
}
function findElementToFocus($table_def)
{
	for($i=0;$i<sizeof($table_def);$i++)
	{
		if(isset($table_def[$i]['db_field']))
		{
			return $i;
		}
	}
}

function createHorizontalHTMLTableForMYSQLData($sql_statement, $table_def, $class = 'mysqlTable')
{

	
	if (sizeof($table_def) > 0)
	{
		$html = '<table class = "'.$class.'">' .newline();
		$html .= '<thead><tr>' . newline();
		for($i=0;$i<sizeof($table_def[0]);$i++)
		{
				if (isset($table_def[0][$i]['sort']))
				{
					$html .= '<th><a href="'.$getURL.'sort='.$table_def[0][$i]['mysql_field'].'&order='.$order.'">'.$table_def[0][$i]['th'].'</a></th>'.newline();
				}
				else
				{
					$html .= '<th>'.$table_def[0][$i]['th'].'</th>'.newline();
				}
		}
		$html .= '</tr></thead>'.newline();
		$html .= '<tbody>';
		for($i=0;$i<sizeof($table_def);$i++)
		{	
			$html .= '<tr>';
			for($j=0;$j<sizeof($table_def[$i]);$j++)
			{

				if ($table_def[$i][$j]['type'] == 'input')
				{
					$html .= '<td>';
					if(isset($table_def[$i][$j]['value'])) $html .= $table_def[$i][$j]['value'];
					$html .= '</td>' .newline();
				}
				elseif ($table_def[$i][$j]['type'] == 'textarea')
				{
					$html .= '<td>';
					if(isset($table_def[$i][$j]['value'])) $html .=  nl2br($table_def[$i][$j]['value']);
					$html .= '</td>' .newline();
				}
				elseif ($table_def[$i][$j]['type'] == 'checkbox')
				{
					$html .= '<td><input   disabled = "disabled" type = "checkbox" name="'.$table_def[$i][$j]['mysql_field'] .'" ';
					if(isset($table_def[$i][$j]['tags'])) $html .= $table_def[$i][$j]['tags'];
					if(isset($table_def[$i][$j]['value']))
					{
						if ($table_def[$i][$j]['value'] == 1 || strtolower($table_def[$i][$j]['value']) == 'true' || strtolower($table_def[$i][$j]['value']) == 'checked' || strtolower($table_def[$i][$j]['value']) == 'yes')
						{
							$html .=  ' checked = "checked" ';
						}
					}
					
					$html .= '/></td>' .newline();
				}
				elseif ($table_def[$i][$j]['type'] == 'select')
				{
					$html .= '<td>'.addvalueToSelect(disableSelect($table_def[$i][$j]['html']),$table_def[$i][$j]['value']) .'</td>';
				}
				elseif ($table_def[$i][$j]['type'] == 'link')
				{
					$html .= '<td><a href="'.$table_def[$i][$j]['get_url_link'] .'?'. $table_def[$i][$j]['get_id_link'] .'='. $table_def[$i][$j]['value'] . '">'.  $table_def[$i][$j]['url_caption'] . '</a></td>'.newline();
				}
				elseif ($table_def[$i][$j]['type'] == 'url_button')
				{
					$html .= '<td style="vertical-align:middle">';
					$html .= createOpenWinButton($table_def[$i][$j]['button_caption'] ,  $table_def[$i][$j]['location'].'?'.$table_def[$i][$j]['get_id_link'] . '=' .$table_def[$i][$j]['value']);
					$html .= '</td>' .newline();
				}
				else
				{
					$html .= '<td>'.$table_def[$i][$j]['value'].'</td>'.newline();
				}
			}
			$html .= '</tr>'.newline();
		} 
		$html .= '</tbody></table>'.newline(); // Close the table.
	}
	else
	{ // If no records were returned.
		$html = '<p class="error">There are currently no records.</p>';
	}
	return $html;

}
function createHTMLTableForMYSQLData($table_def, $class = 'mysqlTable')
{

	//$html = includeJavascriptLibrary();
	$html = '<table class = "'.$class.'">' .newline();
	$html .= '<tbody>';
	for($i=0;$i<sizeof($table_def);$i++)
	{
		$html .= '<tr>';
		if(isset($table_def[$i]['caption']))
		{
			$html .= '<th>' . $table_def[$i]['caption'] .'</th>';
		}
		else
		{
			$html .='<th>' . $table_def[$i]['db_field'] .'</th>';
		}
		
		if ($table_def[$i]['type'] == 'input')
		{
			$html .= '<td>';
			if(isset($table_def[$i]['value']))
			{
				if(isset($table_def[$i]['round']))
				{
					 $html .= number_format($table_def[$i]['value'],$table_def[$i]['round']);
				}
				else
				{
					$html .= $table_def[$i]['value'];
				}
			}
				
			$html .= '</td>' .newline();
		}
		elseif ($table_def[$i]['type'] == 'date')
		{
			$html .= '<td>';
			if(isset($table_def[$i]['value']))
			{
				if(isset($table_def[$i]['separate_date']))
				{
					 if($table_def[$i]['separate_date'] == 'time')
					 {
					 	$html .= getTimeFromDatetime($table_def[$i]['value']);
					 }
					 else
					 {
					 	$html .= getDateFromDatetime($table_def[$i]['value']);
					 }
				}
				else
				{
					$html .= $table_def[$i]['value'];
				}
			}
				
			$html .= '</td>' .newline();
		}
		elseif ($table_def[$i]['type'] == 'time')
		{
			$html .= '<td>';
			if(isset($table_def[$i]['value']))
			{
				$html .= getTimeFromDateTime($table_def[$i]['value']);
			}
				
			$html .= '</td>' .newline();
		}
		elseif ($table_def[$i]['type'] == 'textarea')
		{
			$html .= '<td>';
			if(isset($table_def[$i]['value'])) $html .=  nl2br($table_def[$i]['value']);
			$html .= '</td>' .newline();
		}
		elseif ($table_def[$i]['type'] == 'checkbox')
		{
			$html .= '<td><input   disabled = "disabled" type = "checkbox" name="'.$table_def[$i]['db_field'] .'" ';
			if(isset($table_def[$i]['tags'])) $html .= $table_def[$i]['tags'];
			if(isset($table_def[$i]['value']))
			{
				if ($table_def[$i]['value'] == 1 || strtolower($table_def[$i]['value']) == 'true' || strtolower($table_def[$i]['value']) == 'checked' || strtolower($table_def[$i]['value']) == 'yes')
				{
					$html .=  ' checked = "checked" ';
				}
			}
			
			$html .= '/></td>' .newline();
		}
		elseif ($table_def[$i]['type'] == 'select')
		{
			$html .= '<td>'.addvalueToSelect(disableSelect($table_def[$i]['html']),$table_def[$i]['value']) .'</td>';
		}
		elseif ($table_def[$i]['type'] == 'multiselect')
		{
			if(isset($table_def[$i]['value']))
			{
				$html .= '<td>'. disableSelect(createSecondaryCategorySelect($table_def[$i]['db_field'], $table_def[$i]['value'])) .'</td>';
			}
			else
			{
				$html .= '<td>'. disableSelect(createSecondaryCategorySelect($table_def[$i]['db_field'], 'false')) .'</td>';
			}
		}
		elseif ($table_def[$i]['type'] == 'multi_select')
		{
			$html .= '<td>' . disableSelect($table_def[$i]['html']) .'</td>' .newline();
		}
		elseif ($table_def[$i]['type'] == 'file_input')
		{
			$html .= '<td>';
			if(isset($table_def[$i]['value'])) $html .= $table_def[$i]['value'];
			$html .= '</td>' .newline();
		}
		elseif ($table_def[$i]['type'] == 'none')
		{
			$html .= '<td>' . $table_def[$i]['html'] . '</td>'.newline();
		}
		else
		{
			$html .= '<td>no type match</td>'.newline();
		}
		$html .= '</tr>'.newline();
	} 
	$html .= '</tbody></table>'.newline(); // Close the table.
	return $html;

}

function makeOneHorizontalTableOutOfMany($table_array)
{
	$html = '<table class = "mergerTable"><tbody><tr>';
	for($i=0;$i<sizeof($table_array);$i++)
	{
		$html.= '<td>'.newline();
		$html.=$table_array[$i];
		$html .= '</td>';
	}
	$html .= '</tr></tbody></table>';
	return $html;
}

function getCompleteLocation($default_location)
{
	if (isset($_GET['referring_page']))
	{
		return $_GET['referring_page'];
	}
	else
	{
		return $default_location;
	}
}
function pos_redirect($pos_url)
{
	header('Location: '.POS_ENGINE_URL . $pos_url);	
}
function preprint($s, $return=false) 
{
        $x = "<pre>";
        $x .= print_r($s, 1);
        $x .= "</pre>";
        if ($return) return $x;
        else print $x;
    } 
function convertArrayTableDefToPostTableDef($array_table_def)
{
	$return_def = array();
	for ($i=0;$i<sizeof($array_table_def);$i++)
	{
		if (isset($array_table_def[$i][0]['db_field']))
		{
			for($j=0;$j<sizeof($array_table_def[$i]);$j++)
			{
				$return_def[] = $array_table_def[$i][$j];
			}
		}
		else
		{
			for ($j=0;$j<sizeof($array_table_def[$i]);$j++)
			{
				for($k=0;$k<sizeof($array_table_def[$i][$j]);$k++)
				{
					$return_def[] = $array_table_def[$i][$j][$k];	
				}
			}
		}
	}
	return $return_def;
}

function convertTableDefToHTMLForMYSQLInsert($table_def)
{
	$html ='';
	//this function breaks the table def up to process individual tables
	for ($i=0;$i<sizeof($table_def);$i++)
	{
		//$table_def[0][$i] is either an array of hirzontal table defs  or a table def
		if (isset($table_def[$i][0]['type']))
		{
			//table def 
			$html .= createHTMLTableForMYSQLInsert($table_def[$i]);
		}
		else
		{
			$horizontal_html_table = array();
			for ($j=0;$j<sizeof($table_def[$i]);$j++)
			{
				//this is a horizontal table array
				$horizontal_html_table_array[$j] = createHTMLTableForMYSQLInsert($table_def[$i][$j]);
			}
			$html .= makeOneHorizontalTableOutOfMany($horizontal_html_table_array);
		}
	}
	return $html;
}			
function convertTableDefToHTMLForView($table_def)
{
	$html ='';
	//this function breaks the table def up to process individual tables
	for ($i=0;$i<sizeof($table_def);$i++)
	{
		//$table_def[0][$i] is either an array of hirzontal table defs  or a table def
		if (isset($table_def[$i][0]['db_field']))
		{
			//table def 
			$html .= createHTMLTableForMYSQLData($table_def[$i]);
		}
		else
		{
			$horizontal_html_table = array();
			for ($j=0;$j<sizeof($table_def[$i]);$j++)
			{
				$horizontal_html_table_array[$j] = createHTMLTableForMYSQLData($table_def[$i][$j]);
			}
			$html .= makeOneHorizontalTableOutOfMany($horizontal_html_table_array);
		}
	}
	return $html;
}   
    
function getFormType()
{
	if(isset($_GET['type']))
	{
		return $_GET['type'];
	}
	elseif (isset($_POST['type']))
	{
		return $_POST['type'];
	}
	else
	{
		//trigger_error('missing type');
		return '';
	}
}
function getScriptOutput($path, $print = FALSE)
{
    ob_start();

    if( is_readable($path) && $path )
    {
        include $path;
    }
    else
    {
        return FALSE;
    }

    if( $print == FALSE )
        return ob_get_clean();
    else
        echo ob_get_clean();
}
?>