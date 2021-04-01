<?php
//BindEvents Method @1-397EAC53
function BindEvents()
{
    global $CCSEvents;
    $CCSEvents["AfterInitialize"] = "Page_AfterInitialize";
}
//End BindEvents Method

//Page_AfterInitialize @1-AD45AA14
function Page_AfterInitialize(& $sender)
{
    $Page_AfterInitialize = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $confirm; //Compatibility
//End Page_AfterInitialize

//Custom Code @2-922BF878
// -------------------------
global $calendar_config;
global $Redirect;

	$flag = 0;	
	if (($login = CCGetParam("name")) && ($code = CCGetParam("acc")) && ($code > 0)) {
		$db = new clsDBcalendar();
		$SQL = "SELECT user_id FROM users ".
			   "WHERE user_login=" . $db->ToSQL($login, ccsText) . 
				" AND user_access_code=" . $db->ToSQL($code, ccsInteger) .
				" AND user_level=1";
		$db->query($SQL);

		if ($db->next_record()) {
			$user_id = $db->f("user_id");
			$SQL = "UPDATE users SET user_level=10, user_is_approved=1 " .
				   "WHERE user_id=" . $db->ToSQL($user_id, ccsInteger);
  			$db->query($SQL);
			$mes = str_replace("{user_login}", $login, GetContent("verification_message"));
			$Component->MessageLabel->SetValue($mes);
			return true;
		} 
	}

	$Redirect = "index.php";
// -------------------------
//End Custom Code

//Close Page_AfterInitialize @1-379D319D
    return $Page_AfterInitialize;
}
//End Close Page_AfterInitialize


?>
