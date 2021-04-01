<?php
//BindEvents Method @1-8AB3C33B
function BindEvents()
{
    global $ChangePassword;
    global $CCSEvents;
    $ChangePassword->ContentLabel->CCSEvents["BeforeShow"] = "ChangePassword_ContentLabel_BeforeShow";
    $ChangePassword->CCSEvents["BeforeShow"] = "ChangePassword_BeforeShow";
    $ChangePassword->CCSEvents["OnValidate"] = "ChangePassword_OnValidate";
    $CCSEvents["AfterInitialize"] = "Page_AfterInitialize";
}
//End BindEvents Method

//ChangePassword_ContentLabel_BeforeShow @22-D39752CA
function ChangePassword_ContentLabel_BeforeShow(& $sender)
{
    $ChangePassword_ContentLabel_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $ChangePassword; //Compatibility
//End ChangePassword_ContentLabel_BeforeShow

//Custom Code @23-2A29BDB7
// -------------------------
global $UserLogin;

	$Content = GetContent("lost_password");
	$Component->SetValue(str_replace("{user_login}", $UserLogin, $Content));

// -------------------------
//End Custom Code

//Close ChangePassword_ContentLabel_BeforeShow @22-EB180D4B
    return $ChangePassword_ContentLabel_BeforeShow;
}
//End Close ChangePassword_ContentLabel_BeforeShow

//ChangePassword_BeforeShow @5-A5E7242C
function ChangePassword_BeforeShow(& $sender)
{
    $ChangePassword_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $ChangePassword; //Compatibility
//End ChangePassword_BeforeShow

//Custom Code @14-2A29BDB7
// -------------------------

	$ChangePassword->new_password->SetValue("");
	$ChangePassword->new_password_confirm->SetValue("");

// -------------------------
//End Custom Code

//Close ChangePassword_BeforeShow @5-1447E4D9
    return $ChangePassword_BeforeShow;
}
//End Close ChangePassword_BeforeShow

//ChangePassword_OnValidate @5-ACFD7DF9
function ChangePassword_OnValidate(& $sender)
{
    $ChangePassword_OnValidate = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $ChangePassword; //Compatibility
//End ChangePassword_OnValidate

//Custom Code @26-2A29BDB7
// -------------------------
global $CCSLocales;
global $PasswordHash;
global $UserLogin;

	if (strlen($Component->new_password->GetValue()) == 0) {
		$Component->Errors->addError($CCSLocales->GetText("CCS_RequiredField", array($CCSLocales->GetText("cal_new_password") )));
	} elseif ($Component->new_password->GetValue() != $Component->new_password_confirm->GetValue() ) {
		$Component->Errors->addError($CCSLocales->GetText("cal_error_difpass"));
	} elseif (!preg_match("/^[a-zA-Z0-9]{3,16}$/", $Component->new_password->GetValue()) ) {
		$Component->Errors->addError($CCSLocales->GetText("cal_error_pass"));
	}

  	if ($Component->Errors->Count() == 0) {
   		$db = new clsDBcalendar();
  		$SQL = "UPDATE users SET user_hash='', user_password = ". $db->ToSQL($Component->new_password->GetValue(), ccsText) .
  			  " WHERE user_hash=" . $db->ToSQL($PasswordHash, ccsText);

		$db->query($SQL);
		$db->close();

		CCSetSession("content_param", array(
			"{user_login}" => $UserLogin,
			"{user_name}" => $UserLogin,
			"{profile_url}"=> "profile.php") );
		CCSetSession("content_type", "password_changed");
		header("Location: info.php");
		exit;
  	}

// -------------------------
//End Custom Code

//Close ChangePassword_OnValidate @5-2BBC8050
    return $ChangePassword_OnValidate;
}
//End Close ChangePassword_OnValidate

//Page_AfterInitialize @1-757739B0
function Page_AfterInitialize(& $sender)
{
    $Page_AfterInitialize = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $lost_password; //Compatibility
//End Page_AfterInitialize

//Custom Code @19-2A29BDB7
// -------------------------
global $PasswordHash;
global $UserLogin;

	$PasswordHash = CCGetFromGet("pwd", "");

	//If user is logged in - back to index
	if (CCGetUserID()) {
		header("Location: index.php");
		exit;
	}

	//If PasswordHash parameter is empty - back to index
	If (strlen($PasswordHash) == 0) {
		header("Location: index.php");
		exit;
	}
	
	$db = new clsDBcalendar();
	$SQL = "SELECT user_id, user_login, user_level FROM users ".
		  " WHERE user_hash =" . $db->ToSQL($PasswordHash, ccsText);
	$db->query($SQL);
    if ($db->next_record()) {
		$UserID = $db->f("user_id");
		$UserLogin = $db->f("user_login");
		$GroupID = $db->f("user_level");
	} 

	$db->close();

	//If user not found - back to index
	If (strlen($UserLogin) == 0) {
		header("Location: index.php");
		exit;
	}

// -------------------------
//End Custom Code

//Close Page_AfterInitialize @1-379D319D
    return $Page_AfterInitialize;
}
//End Close Page_AfterInitialize


?>
