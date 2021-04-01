<?php
//BindEvents Method @1-17538124
function BindEvents()
{
    global $Login;
    global $CCSEvents;
    $Login->Button_DoLogin->CCSEvents["OnClick"] = "Login_Button_DoLogin_OnClick";
    $CCSEvents["AfterInitialize"] = "Page_AfterInitialize";
}
//End BindEvents Method

//Login_Button_DoLogin_OnClick @6-9DE63FBA
function Login_Button_DoLogin_OnClick(& $sender)
{
    $Login_Button_DoLogin_OnClick = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $Login; //Compatibility
//End Login_Button_DoLogin_OnClick

//Custom Code @13-ADD652EE
// -------------------------
global $Login;
global $CCSLocales;

    $db = new clsDBcalendar();

  	$SQL = "SELECT user_is_approved FROM users WHERE user_login=" . $db->ToSQL($Login->login->Value, ccsText);
  	$db->query($SQL);
	if ($db->next_record())
		if ($db->f("user_is_approved")) {
			$db->query("UPDATE users SET user_last_login=NOW() WHERE user_login=" . $db->ToSQL($Login->login->Value, ccsText));
		} else {
	        $Login->Errors->addError($CCSLocales->GetText("CCS_LoginInactive"));
    	    $Login->password->SetValue("");
        	$Login_Button_DoLogin_OnClick = false;
			return;
    	}

// -------------------------
//End Custom Code

//Login @14-8DE77B20
    global $CCSLocales;
    if ( !CCLoginUser( $Container->login->Value, $Container->password->Value)) {
        $Container->Errors->addError($CCSLocales->GetText("CCS_LoginError"));
        $Container->password->SetValue("");
        $Login_Button_DoLogin_OnClick = 0;
    } else {
        global $Redirect;
        $Redirect = CCGetParam("ret_link", $Redirect);
        $Login_Button_DoLogin_OnClick = 1;
    }
//End Login

//Close Login_Button_DoLogin_OnClick @6-0EB5DCFE
    return $Login_Button_DoLogin_OnClick;
}
//End Close Login_Button_DoLogin_OnClick

//Page_AfterInitialize @1-B2728422
function Page_AfterInitialize(& $sender)
{
    $Page_AfterInitialize = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $login; //Compatibility
//End Page_AfterInitialize

//Custom Code @15-2A29BDB7
// -------------------------
	global $calendar_config;

	if ($calendar_config["registration_type"] == "0") 
		$Component->register->Visible = False;
// -------------------------
//End Custom Code

//Close Page_AfterInitialize @1-379D319D
    return $Page_AfterInitialize;
}
//End Close Page_AfterInitialize


?>
