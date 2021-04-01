<?php
//BindEvents Method @1-7844DD4F
function BindEvents()
{
    global $config;
    $config->ListBox_value->CCSEvents["BeforeShow"] = "config_ListBox_value_BeforeShow";
    $config->CCSEvents["BeforeShowRow"] = "config_BeforeShowRow";
    $config->ds->CCSEvents["BeforeBuildUpdate"] = "config_ds_BeforeBuildUpdate";
}
//End BindEvents Method

//config_ListBox_value_BeforeShow @17-B2803A95
function config_ListBox_value_BeforeShow(& $sender)
{
    $config_ListBox_value_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $config; //Compatibility
//End config_ListBox_value_BeforeShow

//Custom Code @40-2A29BDB7
// -------------------------
    // Write your own code here.
// -------------------------
//End Custom Code

//Close config_ListBox_value_BeforeShow @17-C49C477A
    return $config_ListBox_value_BeforeShow;
}
//End Close config_ListBox_value_BeforeShow

//config_BeforeShowRow @3-DB2FB3CB
function config_BeforeShowRow(& $sender)
{
    $config_BeforeShowRow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $config; //Compatibility
//End config_BeforeShowRow

//Custom Code @15-2A29BDB7
// -------------------------
global $PrevCat;
global $CCSLocales;

	$ConfCat = array(1=>"info_calendar", 2=>"calendars_options", 3=>"users_options", 4=>"email_options", 5=>"site_options");

	if ($PrevCat != $Component->config_category->GetValue()) {
		$PrevCat = $Component->config_category->GetValue();
		$Component->config_category->Visible = True;
		$Component->config_category->SetValue($CCSLocales->GetText($ConfCat[$PrevCat]));
	} else
		$Component->config_category->Visible = False;

	$TypeComp = array(1=>&$Container->Check_value, 2=>&$Container->Box_value, 3=>&$Container->Area_value, 4=>&$Container->ListBox_value);

	for($i = 1; $i < 5; $i++) {
		if ($i == $Container->value_type->GetValue()) {
			$TypeComp[$i]->Visible = True;
			$TypeComp[$i]->SetValue($Container->Area_value->GetValue());

			if ($i == 4) {
				$listboxval = split(";",$Container->ListBox_Values->GetValue());
				for($j = 0; $j < Count($listboxval)-1; $j++)
					$arrValues[] = array($listboxval[$j], $listboxval[++$j]);
				$Container->ListBox_value->Values = $arrValues;
			}
		}
		else
			$TypeComp[$i]->Visible = False;
	}
// -------------------------
//End Custom Code

//Close config_BeforeShowRow @3-117AFBF0
    return $config_BeforeShowRow;
}
//End Close config_BeforeShowRow

//config_ds_BeforeBuildUpdate @3-96B222AA
function config_ds_BeforeBuildUpdate(& $sender)
{
    $config_ds_BeforeBuildUpdate = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $config; //Compatibility
//End config_ds_BeforeBuildUpdate

//Custom Code @16-2A29BDB7
// -------------------------
	$type = $Container->value_type->GetValue();

	switch ($type) {
	case 1: $Container->ds->Area_value->SetValue($Container->Check_value->GetValue());
		break;
	case 2: $Container->ds->Area_value->SetValue(trim($Container->Box_value->GetValue()));
		break;
	case 3: $Container->ds->Area_value->SetValue(trim($Container->ds->Area_value->GetValue()));
		break;	
	case 4: $Container->ds->Area_value->SetValue($Container->ListBox_value->GetValue());
		break;
	}

// -------------------------
//End Custom Code
	
//Close config_ds_BeforeBuildUpdate @3-170D7C10
    return $config_ds_BeforeBuildUpdate;
}
//End Close config_ds_BeforeBuildUpdate


?>
