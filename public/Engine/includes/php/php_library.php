<?php
require_once(FORM_ELEMENTS);
require_once('file_output.php');
require_once('constants.php');

require_once('html_table_functions.php');
require_once('debug_functions.php');
require_once('product_library.php');
require_once('system_library.php');



require_once('accounting_library.php');
require_once('user_library.php');
require_once('backup.php');
require_once('inventory_functions.php');
require_once('print_library.php');
require_once('purchases_journal_library.php');
require_once('payments_journal_library.php');
require_once('purchase_order_library.php');
require_once('general_journal_library.php');
require_once('manufacturer_library.php');
require_once('tinymce.php');
require_once('email_library.php');
require_once('customer_library.php');
require_once('store_library.php');
require_once('csv_library.php');
require_once('tax_library.php');
require_once('sales_library.php');
require_once('navigation.php');
require_once('login_functions.php');
require_once('dynamic_table.php');
require_once('file_processing.php');
require_once('js_forms.php');
require_once('image_processing.php');
require_once('shipping_library.php');
require_once(POS_ENGINE_PATH. '/includes/javascript/javascript_versions.php');
require_once(MYSQL_INSERT_FUNCTIONS);
require_once(MYSQL_SELECT_FUNCTIONS);
require_once(MYSQL_COMMON_FUNCTIONS);

require_once(MYSQL_TRANSACTION_FUNCTIONS);
require_once(MYSQL_DELETE_FUNCTIONS);


//some third part pieces...




if (WEB_STORE_ACTIVE) 
{
require_once(WEB_STORE_MODULE);
}
function checkHTTPS()
{
	if(!isset($_SERVER['HTTPS']) || $_SERVER['HTTPS'] == "")
	{
   	 $redirect = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
    	header("Location: $redirect");
    	exit();
	}
}
function addGetValue($url, $name, $value)
{
	$separator = "?";
	if (strpos($url,"?")!=false)
	{
  			$separator = "&";
  	}
	$newUrl = $url . $separator .$name . '=' .urlencode($value); 
	return $newUrl;
}
function header_redirect($url)
{
	header('Location: '. $url);
}
function curPageURL() 
{
	return urlencode(BASE_URL . $_SERVER['REQUEST_URI']);
 //$pageURL = 'http';
 //if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
// $pageURL .= "://";
 //if ($_SERVER["SERVER_PORT"] != "80") {
 // $pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 //} else {
 // $pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 //}
 //return $pageURL;
}
function getDateTime()
{
	return date('Y:m:d H:i:s');
}
function getDateFromDatetime($date_time)
{
	$date_array=  date_parse($date_time);
	$year = $date_array['year'];
	$month = $date_array['month'];
	if(strlen($month)==1)$month="0".$month;
	$day = $date_array['day'];
	if(strlen($day)==1)$day="0".$day;
	$date_part =  $year .'-'.$month .'-'.$day;
	if ($date_part =='--')
	{
		return '';
	}
	else if($date_part =='0-0-0')
	{
		return '';
	}
	else
	{
		return $date_part;
	}
}
function getTimeFromDateTime($date_time)
{
	$date_array=  date_parse($date_time);
	$time =  $date_array['hour'] .':'.$date_array['minute'] .':'.$date_array['second'];
	return $time;
	/*if ($time =='::')
	{
		return '';
	}
	else
	{
		return $time;
	}*/
}
function formValidtion($table_def)
{
	/* the table def is an array of array of key-value fields
		For each field we want to look at the validation and check for errors
		return true if everything is OK, otherwise return the errors
	*/
		//want to only keep the data field for the post info... strip out everything else
	
	for ($i=0;$i<sizeof($table_def);$i++)
	{
		if (isset($table_def[$i]['validate']))
		{
				if (isset($table_def[$i]['validate']['unique_group'] ))
				{
				}
				if (isset( $table_def[$i]['validate']['unique'] ))
				{
				}
				if (isset( $table_def[$i]['validate']['min_length'] ))
				{
				}
				if (isset( $table_def[$i]['validate']['select_value'] ))
				{
				}
				if (isset( $table_def[$i]['validate']['multi_select_value'] ))
				{
				}
				if ( $table_def[$i]['validate'] == 'number')
				{
				}
				if ( $table_def[$i]['validate'] == 'date')
				{
				}

		}
	}
	return true;
}
function removeKey($array)
{
	//pass in a single value array - just one key
	$new_array=array();
	$key = array_keys($array);
	for($i=0;$i<sizeof($array);$i++)
	{
		$new_array[$i] = $array[i][$key];
	}
	return $new_array;
}
function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
function cryptare($text, $crypt, $key = 'encytotpGrahmmmmm', $alg ='blowfish')
{
/*
$key = "this is a secret key";
$input = "Let us meet at 9 o'clock at the secret place.";

$encrypted_data = cryptare($input, $key, 1);
echo '<p>Enc: ' . $encrypted_data .'</p>';
$decrypted_data =  cryptare($encrypted_data, $key, 0);
echo '<p>Dec: ' . $decrypted_data .'</p>';
*/

// Parameters:
// $text = The text that you want to encrypt.
// $key = The key you're using to encrypt.
// $alg = The algorithm.
// $crypt = 1 if you want to crypt, or 0 if you want to decrypt. 
    $encrypted_data="";
    switch($alg)
    {
        case "3des":
            $td = mcrypt_module_open('tripledes', '', 'ecb', '');
            break;
        case "cast-128":
            $td = mcrypt_module_open('cast-128', '', 'ecb', '');
            break;   
        case "gost":
            $td = mcrypt_module_open('gost', '', 'ecb', '');
            break;   
        case "rijndael-128":
            $td = mcrypt_module_open('rijndael-128', '', 'ecb', '');
            break;       
        case "twofish":
            $td = mcrypt_module_open('twofish', '', 'ecb', '');
            break;   
        case "arcfour":
            $td = mcrypt_module_open('arcfour', '', 'ecb', '');
            break;
        case "cast-256":
            $td = mcrypt_module_open('cast-256', '', 'ecb', '');
            break;   
        case "loki97":
            $td = mcrypt_module_open('loki97', '', 'ecb', '');
            break;       
        case "rijndael-192":
            $td = mcrypt_module_open('rijndael-192', '', 'ecb', '');
            break;
        case "saferplus":
            $td = mcrypt_module_open('saferplus', '', 'ecb', '');
            break;
        case "wake":
            $td = mcrypt_module_open('wake', '', 'ecb', '');
            break;
        case "blowfish-compat":
            $td = mcrypt_module_open('blowfish-compat', '', 'ecb', '');
            break;
        case "des":
            $td = mcrypt_module_open('des', '', 'ecb', '');
            break;
        case "rijndael-256":
            $td = mcrypt_module_open('rijndael-256', '', 'ecb', '');
            break;
        case "xtea":
            $td = mcrypt_module_open('xtea', '', 'ecb', '');
            break;
        case "enigma":
            $td = mcrypt_module_open('enigma', '', 'ecb', '');
            break;
        case "rc2":
            $td = mcrypt_module_open('rc2', '', 'ecb', '');
            break;   
        default:
            $td = mcrypt_module_open('blowfish', '', 'ecb', '');
            break;                                           
    }
   
    $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
    $key = substr($key, 0, mcrypt_enc_get_key_size($td));
    mcrypt_generic_init($td, $key, $iv);
   
    if($crypt)
    {
        $encrypted_data = mcrypt_generic($td, $text);
    }
    else
    {
        $encrypted_data = mdecrypt_generic($td, $text);
    }
   
    mcrypt_generic_deinit($td);
    mcrypt_module_close($td);
   
    return $encrypted_data;
}
function getSaltedHash($string)
{
    $salted = "yyO9xUytgh24Llk" . $string;
    $hash   = md5($salted);
    return $hash;
}
function craigsEncryption($string)
{
	$key = "iluv2tow";
	//return $string;
	return bcryptHashEncryption($key, $string, 'encrypt');
	
	//return cmencrypt($string, $key);
}
function craigsDecryption($string)
{
	//return $string;
	$key = "iluv2tow";
	if($string == '')
	{
		return '';
	}
	else
	{
		//return cmdecrypt($string, $key);
		return bcryptHashEncryption($key, $string, 'decrypt');
	}
}
function bcryptHashEncryption($key, $string, $mode)
{
	if ($mode == 'encrypt')
	{
		$encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
		return $encrypted;
	}
	else
	{
		$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($string), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
		return $decrypted;
	}
}

function cmencrypt($str, $key)
{
    # Add PKCS7 padding.
    $block = mcrypt_get_block_size('des', 'ecb');
    if (($pad = $block - (strlen($str) % $block)) < $block) {
      $str .= str_repeat(chr($pad), $pad);
    }

    return mcrypt_encrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_ECB);
}
function cmdecrypt($str, $key)
{
    $str = mcrypt_decrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_ECB);

    # Strip padding out.
    $block = mcrypt_get_block_size('des', 'ecb');
    $pad = ord($str[($len = strlen($str)) - 1]);
    if ($pad && $pad < $block && preg_match(
          '/' . chr($pad) . '{' . $pad . '}$/', $str
                                            )
       ) {
      return substr($str, 0, strlen($str) - $pad);
    }
    return $str;
}
function stripWhiteSpace($input)
{
	//remove white space
	$string = preg_replace('/\s+/', '', $input);
	return $string;
}
function sanitizeFileName($dangerous_filename, $platform = 'Unix')
 {
        if (in_array(strtolower($platform), array('unix', 'linux'))) {
                // our list of "dangerous characters", add/remove characters if necessary
                $dangerous_characters = array(" ", '"', "'", "&", "/", "\\", "?", "#");
        }
        else {
                // no OS matched? return the original filename then...
                return $dangerous_filename;
        }

        // every forbidden character is replace by an underscore
        return str_replace($dangerous_characters, '_', $dangerous_filename);
    }
function sksort(&$array, $subkey="id", $sort_ascending=false) {
/*$info = array("peter" => array("age" => 21,
                                           "gender" => "male"
                                           ),
                   "john"  => array("age" => 19,
                                           "gender" => "male"
                                           ),
                   "mary" => array("age" => 20,
                                           "gender" => "female"
                                          )
                  );

sksort($info, "age");
var_dump($info);

sksort($info, "age", true);*/

    if (count($array))
        $temp_array[key($array)] = array_shift($array);

    foreach($array as $key => $val){
        $offset = 0;
        $found = false;
        foreach($temp_array as $tmp_key => $tmp_val)
        {
            if(!$found and strtolower($val[$subkey]) > strtolower($tmp_val[$subkey]))
            {
                $temp_array = array_merge(    (array)array_slice($temp_array,0,$offset),
                                            array($key => $val),
                                            array_slice($temp_array,$offset)
                                          );
                $found = true;
            }
            $offset++;
        }
        if(!$found) $temp_array = array_merge($temp_array, array($key => $val));
    }

    if ($sort_ascending) $array = array_reverse($temp_array);

    else $array = $temp_array;
}
//url cleaners
function slug($z){
    $z = strtolower($z);
    $z = preg_replace('/[^a-z0-9 -]+/', '', $z);
    $z = str_replace(' ', '-', $z);
    return trim($z, '-');
}
function slugify($text)
{
    // Swap out Non "Letters" with a -
    $text = preg_replace('/[^\\pL\d]+/u', '-', $text); 

    // Trim out extra -'s
    $text = trim($text, '-');

    // Convert letters that we have left to the closest ASCII representation
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    // Make text lowercase
    $text = strtolower($text);

    // Strip out anything we haven't been able to convert
    $text = preg_replace('/[^-\w]+/', '', $text);

    return $text;
}
function toAscii($str) {
	$clean = preg_replace("/[^a-zA-Z0-9\/_|+ -]/", '', $str);
	$clean = strtolower(trim($clean, '-'));
	$clean = preg_replace("/[\/_|+ -]+/", '-', $clean);

	return $clean;
}

?>