<?php

//----//
$db = new clsDBcalendar();

//-----------------------------
// Read config for calendar
//-----------------------------
global $calendar_config;
global $CCSLocales;
global $calendar_languages;

$db->query("SELECT config_var, config_value, config_listbox FROM config");
while ($db->next_record()) {
	$calendar_config[$db->f("config_var")] = $db->f("config_value");
	if ($db->f("config_var") == "default_language") { 
		$calendar_languages = $db->f("config_listbox");
	}
}

if (IsSet($calendar_config["time_format"]) && $calendar_config["time_format"] > 1 ) {
	if ($calendar_config["time_format"] == 2)
		$CCSLocales->Locales[$CCSLocales->Locale]->LocaleInfo->ShortTime = array("HH",":","nn");
	else
		$CCSLocales->Locales[$CCSLocales->Locale]->LocaleInfo->ShortTime = array("hh",":","nn"," ","AM/PM");		
}

//-----------------------------
// Read permissions for calendar
//-----------------------------
global $calendar_permissions;
$db->query("SELECT permission_var, permission_value FROM permissions");
while ($db->next_record())
	$calendar_permissions[$db->f("permission_var")] = $db->f("permission_value");


//-----------------------------
// Delete Non-Confirmed users after 1 day
// If New registration confirmed by E-Mail
//-----------------------------
if ($calendar_config["registration_type"] == "4") {
	$Yesterday = CCDateAdd(CCGetDateArray(), "-1day");
	$db->query("DELETE FROM users WHERE user_level=1 AND user_date_add < " . $db->ToSQL($Yesterday, ccsDate));
}

$db->close();

//-----------------------------
//Generate new password
//Return the string
//-----------------------------
function generateNewPassword($PassLength) {
	return substr(md5(date("YmdHis").microtime()), mt_rand ( 0,24), $PassLength);
}

//-----------------------------
//
// Return condition for WHERE 
//-----------------------------
function AddReadFilter($where) {

	global $calendar_permissions;

	if (CCGetUserID())
		$user_level = CCGetGroupID(); // 10 for registred and 100 for Admin
	else
		$user_level = 0; //Not logged-in user -- Everyone

	if ($user_level < $calendar_permissions["private_read"] || ($user_level >= $calendar_permissions["private_read"] && $calendar_permissions["private_read"] == 50))
		$result = (CCStrLen($where) ? " AND " : "") . " (events.event_is_public = 1" .
				  ($calendar_permissions["private_read"] == 50? " OR (events.event_is_public=0 AND events.user_id=" . doubleval(CCGetUserID()) .")" : "") . ")";
	else
		$result = "";

	return $result;
}

//-----------------------------
//
// Return condition for WHERE 
//-----------------------------
function EditAllowed($event_id) {

	if (strlen($event_id)) {
		global $calendar_permissions;

		$db = new clsDBcalendar();
		$SQL = "SELECT event_is_public, user_id FROM events WHERE event_id=".$db->ToSQL($event_id,ccsInteger);
		$db->query($SQL);
		$db->next_record();
		$event_is_public = $db->f("event_is_public");
		$event_owner = $db->f("user_id");
		$db->close();

		$effective_rights = $event_is_public ? $calendar_permissions["public_update"] : $calendar_permissions["private_update"];

		if (CCGetUserID()) {
			$user_level = CCGetGroupID(); // 10 for registred and 100 for Admin
			if ($event_owner == CCGetUserID() && ($user_level < 100 || $effective_rights == 50))
				$user_level = 50; // if logged_in user and not admin but owner of event
		}
		else
			$user_level = 0; //Not logged-in user -- Everyone

		if ($user_level < $effective_rights || (!$event_is_public && $effective_rights == 50 && $user_level != 50))
			return false;
		else
			return true;
	}
	else
		return false;
}

//-----------------------------
//
// Return condition for WHERE 
//-----------------------------
function AddAllowed() {

	global $calendar_permissions;

	if (CCGetUserID())
  		$user_level = CCGetGroupID(); // 10 for registred and 100 for Admin
	else
		$user_level = 0; //Not logged-in user -- Everyone

	if ($user_level < $calendar_permissions["new_event"])
		return false;
	else
		return true;
}

//-----------------------------
//
// Return condition for WHERE 
//-----------------------------
function DeleteAllowed($event_id) {

	if (strlen($event_id)) {
		global $calendar_permissions;

		$db = new clsDBcalendar();
		$SQL = "SELECT event_is_public, user_id FROM events WHERE event_id=".$db->ToSQL($event_id,ccsInteger);
		$db->query($SQL);
		$db->next_record();
		$event_is_public = $db->f("event_is_public");
		$event_owner = $db->f("user_id");
		$db->close();

		$effective_rights = $event_is_public ? $calendar_permissions["public_delete"] : $calendar_permissions["private_delete"];

		if (CCGetUserID()) {
			$user_level = CCGetGroupID(); // 10 for registred and 100 for Admin
			if ($event_owner == CCGetUserID() && ($user_level < 100 || $effective_rights == 50))
					$user_level = 50; // if logged_in user and not admin but owner of event
		}
		else
			$user_level = 0; //Not logged-in user -- Everyone


		if ($user_level < $effective_rights || (!$event_is_public && $effective_rights == 50 && $user_level != 50))
			return false;
		else
			return true;
	}
	else
		return false;
}
//-----------------------------
//
// Return condition for WHERE 
//-----------------------------
function ReadAllowed($event_id) {

	if (strlen($event_id)) {
		global $calendar_permissions;

		$db = new clsDBcalendar();
		$SQL = "SELECT event_is_public, user_id FROM events WHERE event_id=".$db->ToSQL($event_id,ccsInteger);
		$db->query($SQL);
		$db->next_record();
		$event_is_public = $db->f("event_is_public");
		$event_owner = $db->f("user_id");
		$db->close();

		if ($event_is_public)
			return true;
		else {
			if (CCGetUserID()) {
				$user_level = CCGetGroupID(); // 10 for registred and 100 for Admin
				if ($event_owner == CCGetUserID() && ($user_level < 100 || $calendar_permissions['private_read'] == 50))
					$user_level = 50; // if logged_in user and not admin but owner of event
			}
			else
				$user_level = 0; //Not logged-in user -- Everyone

			if ($user_level < $calendar_permissions['private_read'] || ($calendar_permissions['private_read'] == 50 && $user_level != 50))
				return false;
			else
				return true;
		}
	}
	else
		return false;
}

//-----------------------------
//
// Return condition for WHERE 
//-----------------------------
function processCustomFields(& $sender, $is_req=0) {
	$Component = & $sender;

	$db = new clsDBcalendar();

	$SQL = "SELECT field_name, ".
				"custom_fields.field_label AS mainLabel, ".
				"custom_fields_langs.field_label AS localeLabel, ".
				"field_is_active ".
		   "FROM custom_fields_langs INNER JOIN custom_fields ".
		   		"ON custom_fields.field_id = custom_fields_langs.field_id ".
		   "WHERE language_id = ".$db->ToSQL(CCGetSession("locale"), ccsText);

	$db->query($SQL);

	while ($db->next_record()) {
		$flag = 0;
		if ($db->f("field_is_active"))
			if ($is_req) {
				eval("\$flag = \$Component->event_" . $db->f("field_name") . "->GetValue();");
				$flag = strlen(trim($flag)) || is_bool($flag);
			} else
				$flag = 1;
			eval("\$Component->Label" . $db->f("field_name") . "->SetValue(\"" . (CCStrLen($db->f("localeLabel"))? $db->f("localeLabel") : $db->f("mainLabel"))."\");");
		if (!$flag)
			eval("\$Component->Panel" . $db->f("field_name") . "->Visible = false;");
	}

	$db->close();
}

//-----------------------------
// Read content
//-----------------------------
function GetContent($strkey) {
	$Result = "";
	if (CCStrLen($strkey)) {
		$db = new clsDBcalendar();
		$SQL = "SELECT contents.content_value as mainValue, contents_langs.content_value as localeValue ".
			   "FROM contents LEFT JOIN contents_langs ON contents.content_id = contents_langs.content_id ".
			   "WHERE language_id = ".$db->ToSQL(CCGetSession("locale"),ccsText).
			   " AND content_type = ".$db->ToSQL($strkey,ccsText);

		$db->query($SQL);
		if ($db->next_record())
			$Result = CCStrLen(trim($db->f("localeValue")))? $db->f("localeValue") : $db->f("mainValue");

		$db->close();
	}
	return $Result;
}

//-----------------------------
//
// Return condition for WHERE 
//-----------------------------
function SendEmailMessage($variable, $email_to, $parameters) {
	global $calendar_config;
	$value = 0;

	if (strlen($variable)) {
		$db = new clsDBcalendar();
		$SQL = "SELECT email_template_from, ".
					"email_templates.email_template_subject AS mainSubject, ".
					"email_templates_lang.email_template_subject AS localeSubject, ".
					"email_templates.email_template_body AS mainBody, ".
					"email_templates_lang.email_template_body AS localeBody ".
			   "FROM email_templates LEFT JOIN email_templates_lang ".
					"ON email_templates.email_template_id = email_templates_lang.email_template_id ".
			   "WHERE email_template_type = ".$db->ToSQL($variable, ccsText).
			   		" AND language_id = ".$db->ToSQL(CCGetSession("locale"), ccsText);
		$db->query($SQL);
		if ($db->next_record()) {
			$email_subject = CCStrLen($db->f("localeSubject"))? $db->f("localeSubject") : $db->f("mainSubject");
			$email_body = CCStrLen($db->f("localeBody"))? $db->f("localeBody") : $db->f("mainBody");
			$email_from = CCStrLen($db->f("email_template_from"))? $db->f("email_template_from") : $calendar_config["site_email"];

			while (list($key,$value) = each($parameters))
				$email_body = str_replace($key,$value,$email_body);

			$email_body = str_replace("{site_url}",ServerURL,$email_body);

			if (strlen(trim($calendar_config["SMTP"]))) {
				$value = mailsmtp($email_from, $email_to, $email_subject, $email_body, "From: $email_from\nContent-Type: text/plain");
			} else {
				$value = @mail($email_to, $email_subject, $email_body, "From: $email_from\nContent-Type: text/plain");
				if ($value)
					$value = "";
				else
					$value = "Email sending ERROR";
			}
		}
		$db->close();
    }
    return $value;
}


?>