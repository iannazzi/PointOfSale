<?php
//BindEvents Method @1-7C32D265
function BindEvents()
{
    global $users;
    $users->users_TotalRecords->CCSEvents["BeforeShow"] = "users_users_TotalRecords_BeforeShow";
    $users->CCSEvents["BeforeSelect"] = "users_BeforeSelect";
}
//End BindEvents Method

//users_users_TotalRecords_BeforeShow @8-C04318DB
function users_users_TotalRecords_BeforeShow(& $sender)
{
    $users_users_TotalRecords_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $users; //Compatibility
//End users_users_TotalRecords_BeforeShow

//Retrieve number of records @9-ABE656B4
    $Component->SetValue($Container->DataSource->RecordsCount);
//End Retrieve number of records

//Close users_users_TotalRecords_BeforeShow @8-ADD8CAEB
    return $users_users_TotalRecords_BeforeShow;
}
//End Close users_users_TotalRecords_BeforeShow

//users_BeforeSelect @4-532478EC
function users_BeforeSelect(& $sender)
{
    $users_BeforeSelect = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $users; //Compatibility
//End users_BeforeSelect

//Custom Code @63-2A29BDB7
// -------------------------
global $calendar_config;

	if ($calendar_config["registration_type"] != "8")
		$Component->Visible = false;

// -------------------------
//End Custom Code

//Close users_BeforeSelect @4-B6D080C5
    return $users_BeforeSelect;
}
//End Close users_BeforeSelect


?>
