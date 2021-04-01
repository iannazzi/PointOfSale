<?php
//BindEvents Method @1-D452F75B
function BindEvents()
{
    global $permissions;
    $permissions->perms_value->CCSEvents["BeforeShow"] = "permissions_perms_value_BeforeShow";
    $permissions->CCSEvents["BeforeShowRow"] = "permissions_BeforeShowRow";
}
//End BindEvents Method

//permissions_perms_value_BeforeShow @7-FA5DB1D5
function permissions_perms_value_BeforeShow(& $sender)
{
    $permissions_perms_value_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $permissions; //Compatibility
//End permissions_perms_value_BeforeShow

//Custom Code @44-2A29BDB7
// -------------------------
    // Write your own code here.
// -------------------------
//End Custom Code

//Close permissions_perms_value_BeforeShow @7-9D0D40E5
    return $permissions_perms_value_BeforeShow;
}
//End Close permissions_perms_value_BeforeShow

//permissions_BeforeShowRow @3-B9049CAC
function permissions_BeforeShowRow(& $sender)
{
    $permissions_BeforeShowRow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $permissions; //Compatibility
//End permissions_BeforeShowRow

//Custom Code @12-6E96695A
// -------------------------
	global $category;
	global $CCSLocales;

	$perms_value_arr=array(array(0,$CCSLocales->GetText("cal_everyone")),array(10,$CCSLocales->GetText("cal_registered")),array(50,$CCSLocales->GetText("cal_owner")),array(100,$CCSLocales->GetText("cal_admin")));

	switch ($Container->perm_type->GetValue()) {
		case 1:
			$Container->perms_value->Values = $perms_value_arr;
			break;
		case 2:
			$Container->perms_value->Values = array($perms_value_arr[1], $perms_value_arr[3]);
			break;
		case 3:
			$Container->perms_value->Values = array($perms_value_arr[0],$perms_value_arr[1],$perms_value_arr[3]);
			break;
	}

	$conf_category = array(1=>"cal_general", 2=>"cal_public_events", 3=>"cal_private_events");

	if ($category == $Container->permission_category->GetValue())
		$Container->permission_category->Visible = False;
	else {
		$Container->permission_category->Visible = True;
		$category = $Container->permission_category->GetValue();
		$Container->permission_category->SetValue($CCSLocales->GetText($conf_category[$category]));
	}
// -------------------------
//End Custom Code

//Close permissions_BeforeShowRow @3-CE632AF2
    return $permissions_BeforeShowRow;
}
//End Close permissions_BeforeShowRow

?>