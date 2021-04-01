<?php

# emails accepted in one of in forms:
# user@domain.com
# "John Smith" <user@domain.com>
#
# host: FQDN
# port: usually 25
# from: email in one of above forms
# to: email in one of above forms
# subject: E-mail subject
# body: E-mail text
# headers: header <CR><LF> header <CR><LF> ...
# returns:
#   "" on success
#   error message on error

function mailsmtp($from, $to, $subject, $body, $headers) {
	global $calendar_config;
	return phpmail($calendar_config["SMTP"], $calendar_config["SMTP_port"]? $calendar_config["SMTP_port"] : 25, $from, $to, $subject, $body, $headers);
}

function phpmail($host, $port, $from, $to, $subject, $body, $headers) {
	$from = preg_replace('/[\r\n]/s',' ',$from);
	$to = preg_replace('/[\r\n]/s',' ',$to);
	$email = "Subject: $subject\r\n" .

	$headers . "\r\n\r\n" . $body;
	$smtpfrom = email2smtp($from);

	if ($smtpfrom === false) {
		return "Email sending ERROR: Bad 'from' parameter: '$from'";
	}

	$smtpto = email2smtp($to);
	if ($smtpto === false) {
		return "Email sending ERROR: Bad 'to' parameter: '$to'";
	}

	return phpmail2($host,$port,$smtpfrom,$smtpto,$email);
}

function email2smtp($email) {
	$email = preg_replace('/\\\\"/s','',$email);
	$new = preg_replace('/^\s*"?[^"]*"?\s+<([-_.+a-zA-Z0-9]+@[-.a-zA-Z0-9]+)>\s*$/','\1',$email);
	if (!preg_match('/^[-_.+a-zA-Z0-9]+@[-.a-zA-Z0-9]+$/', $new)) {
		return false;
	}
	return $new;
}

# from: username@fully.qualified.domain.name (no angle brackets, quotes,...)
# to: username@fully.qualified.domain.name (no angle brackets, quotes,...)
function phpmail2($host,$port,$from,$to,$email) {
	$helostring = "phpmail";
	$olduser_rep = error_reporting(0);
	$fp = fsockopen($host,$port,$errno,$errstr);
	error_reporting($olduser_rep);

	if (!$fp) {
		return "Email sending ERROR: Cant connect to SMTP-server - \"$host:$port\" - $errno ($errstr)";
	}

	$greeting = fgets($fp,1024);
	list($code,$text) = explode(' ',$greeting,2);
	if ($code != "220") {
		return "Email sending ERROR: Bad greeting: $greeting";
	}


	$ret = check_reply($fp,"helo $helostring\r\n","250");
	if ($ret != "") { return $ret; }

	$ret = check_reply($fp,"mail from: <$from>\r\n","250");
	if ($ret != "") { return $ret; }

	$ToArray = split (",",$to);
	$isOneValid = 0;
	while (list($tokey,$toval) = each($ToArray) ) {
		$ret = check_reply($fp,"rcpt to: <$toval>\r\n","250");
		if ($ret == "") { 
			$isOneValid = 1;
		}
	}

	if (!$isOneValid) {return "Bad reply to 'to' command";} 

	$ret = check_reply($fp,"data\r\n","354");
	if ($ret != "") { return $ret; }

	$email = preg_replace("/([^\r])\n/","$1\r\n",$email);
	fputs($fp,preg_replace("/\r\n\./s","\r\n..",$email));

	$ret = check_reply($fp,"\r\n.\r\n","250");
	if ($ret != "") { return $ret; }

	$ret = check_reply($fp,"quit\r\n","221");
	if ($ret != "") { return $ret; }

	fclose($fp);
	return "";
}

function check_reply($fp,$command,$check_code) {
	fputs($fp,$command);
	$reply = fgets($fp,1024);
	list($code,$text) = explode(' ',$reply,2);
	if ($code != $check_code) {
		return "Email sending ERROR: Bad reply to '$command': '$reply'";
	}
	return "";
}

?>
