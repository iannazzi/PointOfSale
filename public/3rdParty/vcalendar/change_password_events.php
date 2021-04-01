<?php
//BindEvents Method @1-5E76EDB3
function BindEvents()
{
    global $ChangePassword;
    $ChangePassword->CCSEvents["OnValidate"] = "ChangePassword_OnValidate";
    $ChangePassword->CCSEvents["BeforeShow"] = "ChangePassword_BeforeShow";
    $ChangePassword->CCSEvents["AfterUpdate"] = "ChangePassword_AfterUpdate";
}
//End BindEvents Method

//ChangePassword_OnValidate @5-ACFD7DF9
function ChangePassword_OnValidate(& $sender)
{
    $ChangePassword_OnValidate = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $ChangePassword; //Compatibility
//End ChangePassword_OnValidate

//Custom Code @13-7B413439
// -------------------------
	global $DBcalendar;
	global $CCSLocales;

	if ($Component->current_password->GetValue()) {
		$Where = "user_id=" . $DBcalendar->ToSQL(CCGetUserID(), ccsInteger) .
				 " AND user_password=" . $DBcalendar->ToSQL($Container->current_password->GetValue(), ccsText);
		$user_id = CCDlookUp("user_id", "users", $Where, $DBcalendar);
		if (!$user_id)
			$Component->Errors->addError($CCSLocales->GetText("cal_wrong_pass"));
		else
			if ($Component->new_password->GetValue()) {
				if ($Component->new_password->GetValue() != $Component->new_password_confirm->GetValue() )
					$Component->Errors->addError($CCSLocales->GetText("cal_error_difpass"));
				else
					if (!preg_match("/^[a-zA-Z0-9]{3,16}$/", $Component->new_password->GetValue()) ) 
						$Component->Errors->addError($CCSLocales->GetText("cal_error_pass"));
			}
	}
// -------------------------
//End Custom Code

//Close ChangePassword_OnValidate @5-2BBC8050
    return $ChangePassword_OnValidate;
}
//End Close ChangePassword_OnValidate

//ChangePassword_BeforeShow @5-A5E7242C
function ChangePassword_BeforeShow(& $sender)
{
    $ChangePassword_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $ChangePassword; //Compatibility
//End ChangePassword_BeforeShow

//Custom Code @14-7B413439
// -------------------------
	$Container->current_password->SetValue("");
	$Container->new_password->SetValue("");
	$Container->new_password_confirm->SetValue("");
// -------------------------
//End Custom Code

//Close ChangePassword_BeforeShow @5-1447E4D9
    return $ChangePassword_BeforeShow;
}
//End Close ChangePassword_BeforeShow

//ChangePassword_AfterUpdate @5-CAE9F8E7
function ChangePassword_AfterUpdate(& $sender)
{
    $ChangePassword_AfterUpdate = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $ChangePassword; //Compatibility
//End ChangePassword_AfterUpdate

//Custom Code @15-7B413439
// -------------------------
	CCSetSession("content_param", array(
		"{user_login}" => CCGetUserLogin(),
		"{user_name}" => CCGetUserLogin(),
		"{profile_url}"=> "profile.php") );
	CCSetSession("content_type", "password_changed");
// -------------------------
//End Custom Code

//Close ChangePassword_AfterUpdate @5-499FD6D4
    return $ChangePassword_AfterUpdate;
}
//End Close ChangePassword_AfterUpdate

?>