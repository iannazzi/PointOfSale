<?php
//BindEvents Method @1-995B0EBF
function BindEvents()
{
    global $users_maint;
    global $CCSEvents;
    $users_maint->CCSEvents["BeforeShow"] = "users_maint_BeforeShow";
    $users_maint->CCSEvents["OnValidate"] = "users_maint_OnValidate";
    $CCSEvents["AfterInitialize"] = "Page_AfterInitialize";
}
//End BindEvents Method

//users_maint_BeforeShow @2-51FA32FA
function users_maint_BeforeShow(& $sender)
{
    $users_maint_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $users_maint; //Compatibility
//End users_maint_BeforeShow

//Hide-Show Component @18-E010D0B7
    $Parameter1 = CCGetFromGet("user_id", "");
    $Parameter2 = 0;
    if (((is_array($Parameter1) || strlen($Parameter1)) && (is_array($Parameter2) || strlen($Parameter2))) && 0 <  CCCompareValues($Parameter1, $Parameter2, ccsInteger))
        $Container->user_login->Visible = false;
//End Hide-Show Component

//Close users_maint_BeforeShow @2-004FBD03
    return $users_maint_BeforeShow;
}
//End Close users_maint_BeforeShow

//users_maint_OnValidate @2-DABBE095
function users_maint_OnValidate(& $sender)
{
    $users_maint_OnValidate = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $users_maint; //Compatibility
//End users_maint_OnValidate

//Custom Code @37-2A29BDB7
// -------------------------

	if (!($Container->EditMode || CCStrLen($Container->user_login->GetValue()))) {
		global $CCSLocales;
		$Container->Errors->addError($CCSLocales->GetText("CCS_RequiredField",$CCSLocales->GetText("user_login")));
	}

// -------------------------
//End Custom Code

//Close users_maint_OnValidate @2-3FB4D98A
    return $users_maint_OnValidate;
}
//End Close users_maint_OnValidate

//Page_AfterInitialize @1-788DEE01
function Page_AfterInitialize(& $sender)
{
    $Page_AfterInitialize = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $users_maint; //Compatibility
//End Page_AfterInitialize

//Custom Code @45-2A29BDB7
// -------------------------



// -------------------------
//End Custom Code

//Close Page_AfterInitialize @1-379D319D
    return $Page_AfterInitialize;
}
//End Close Page_AfterInitialize

?>