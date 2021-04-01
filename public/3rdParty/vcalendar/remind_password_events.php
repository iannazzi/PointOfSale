<?php
//BindEvents Method @1-A628722B
function BindEvents()
{
    global $remind;
    global $CCSEvents;
    $remind->CCSEvents["OnValidate"] = "remind_OnValidate";
    $CCSEvents["AfterInitialize"] = "Page_AfterInitialize";
}
//End BindEvents Method

//remind_OnValidate @4-D456B450
function remind_OnValidate(& $sender)
{
    $remind_OnValidate = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $remind; //Compatibility
//End remind_OnValidate

//Custom Code @8-D257CBE3
// -------------------------
global $CCSLocales;

	if (CCStrLen(trim($Component->login->GetValue())) ) {
		$db = new clsDBcalendar();
		$SQL = "SELECT user_id, user_login, user_email, user_password, user_first_name FROM users " .
			   "WHERE user_login = " . $db->ToSQL($Component->login->GetValue(), ccsText) .
				 " OR user_email = " . $db->ToSQL($Component->login->GetValue(), ccsText);

		$db->query($SQL);

		if ($db->next_record()) {
			$user_id = $db->f("user_id");
			$email_to = $db->f("user_email");

			//Generate new hash for password
			$NewHash = md5($user_id . generateNewPassword(8));

			$parameters = array("{user_login}"   => $db->f("user_login"),
								"{activate_url}" => ServerURL . "lost_password.php?pwd=" . $NewHash );

			$SQL = "UPDATE users SET user_hash = " . $db->ToSQL($NewHash, ccsText) .
				   " WHERE user_id = " . $db->ToSQL($user_id, ccsInteger);
			$db->query($SQL);
			$db->close();

			$sent = SendEmailMessage("forgot_password", $email_to, $parameters);
			if (strlen($sent))
				$Component->Errors->addError($sent);

			CCSetSession("content_param", $parameters);
			CCSetSession("content_type", "password_was_sent");
		}
		else
			$Component->Errors->addError($CCSLocales->GetText("cal_error_nouser"));
	}
// -------------------------
//End Custom Code

//Close remind_OnValidate @4-4BEBE52F
    return $remind_OnValidate;
}
//End Close remind_OnValidate

//Page_AfterInitialize @1-216B2B4B
function Page_AfterInitialize(& $sender)
{
    $Page_AfterInitialize = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $remind_password; //Compatibility
//End Page_AfterInitialize

//Custom Code @7-77BB5271
// -------------------------
global $Redirect;

	if (CCGetUserID())
		$Redirect = "index.php";
// -------------------------
//End Custom Code

//Close Page_AfterInitialize @1-379D319D
    return $Page_AfterInitialize;
}
//End Close Page_AfterInitialize


?>
