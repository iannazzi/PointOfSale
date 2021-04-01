<?php
//BindEvents Method @1-B87CAF8C
function BindEvents()
{
    global $users;
    $users->users_TotalRecords->CCSEvents["BeforeShow"] = "users_users_TotalRecords_BeforeShow";
    $users->CCSEvents["BeforeShowRow"] = "users_BeforeShowRow";
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

//users_BeforeShowRow @2-82C07E8D
function users_BeforeShowRow(& $sender)
{
    $users_BeforeShowRow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $users; //Compatibility
//End users_BeforeShowRow

//Custom Code @69-2A29BDB7
// -------------------------
global $CCSLocales;

	switch ($Component->user_level->GetValue()) {
		case 1   : $Component->user_level->SetValue($CCSLocales->GetText("non_confirmed_user")); break;
		case 10  : $Component->user_level->SetValue($CCSLocales->GetText("user")); break;
		case 100 : $Component->user_level->SetValue($CCSLocales->GetText("admin")); break;
	}
// -------------------------
//End Custom Code

//Close users_BeforeShowRow @2-370775E0
    return $users_BeforeShowRow;
}
//End Close users_BeforeShowRow

?>