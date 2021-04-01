<?php
//BindEvents Method @1-4548B41D
function BindEvents()
{
    global $year_events;
    $year_events->GoWeekHeader->CCSEvents["BeforeShow"] = "year_events_GoWeekHeader_BeforeShow";
    $year_events->CategoryImage->CCSEvents["BeforeShow"] = "year_events_CategoryImage_BeforeShow";
    $year_events->EventTime->CCSEvents["BeforeShow"] = "year_events_EventTime_BeforeShow";
    $year_events->GoWeek->CCSEvents["BeforeShow"] = "year_events_GoWeek_BeforeShow";
    $year_events->ds->CCSEvents["BeforeBuildSelect"] = "year_events_ds_BeforeBuildSelect";
    $year_events->CCSEvents["BeforeShowDay"] = "year_events_BeforeShowDay";
    $year_events->CCSEvents["BeforeShow"] = "year_events_BeforeShow";
}
//End BindEvents Method

//year_events_GoWeekHeader_BeforeShow @56-50A3F6E5
function year_events_GoWeekHeader_BeforeShow(& $sender)
{
    $year_events_GoWeekHeader_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $year_events; //Compatibility
//End year_events_GoWeekHeader_BeforeShow

//Custom Code @58-2A29BDB7
// -------------------------
global $calendar_config;

	if ($calendar_config["year_week_icon"] == 1)
		$Component->Visible = True;		
	else
		$Component->Visible = False;
// -------------------------
//End Custom Code

//Close year_events_GoWeekHeader_BeforeShow @56-4A6A35D5
    return $year_events_GoWeekHeader_BeforeShow;
}
//End Close year_events_GoWeekHeader_BeforeShow

//year_events_CategoryImage_BeforeShow @35-AFE8FC1B
function year_events_CategoryImage_BeforeShow(& $sender)
{
    $year_events_CategoryImage_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $year_events; //Compatibility
//End year_events_CategoryImage_BeforeShow

//Custom Code @36-06A42B47
// -------------------------
	if (strlen($Component->GetValue()))
		$Component->Visible = True;
	else
		$Component->Visible = False;
// -------------------------
//End Custom Code

//Close year_events_CategoryImage_BeforeShow @35-DEF43079
    return $year_events_CategoryImage_BeforeShow;
}
//End Close year_events_CategoryImage_BeforeShow

//year_events_EventTime_BeforeShow @20-E84B5684
function year_events_EventTime_BeforeShow(& $sender)
{
    $year_events_EventTime_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $year_events; //Compatibility
//End year_events_EventTime_BeforeShow

//Custom Code @25-0F2C665A
// -------------------------
	if (strlen($Component->GetText()))
		$Component->Visible = True;		
	else
		$Component->Visible = False;
// -------------------------
//End Custom Code

//Close year_events_EventTime_BeforeShow @20-0CBAC35A
    return $year_events_EventTime_BeforeShow;
}
//End Close year_events_EventTime_BeforeShow

//year_events_GoWeek_BeforeShow @27-C57DCD98
function year_events_GoWeek_BeforeShow(& $sender)
{
    $year_events_GoWeek_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $year_events; //Compatibility
//End year_events_GoWeek_BeforeShow

//Custom Code @57-2A29BDB7
// -------------------------
global $calendar_config;

	if ($calendar_config["year_week_icon"] == 1)
		$Component->Visible = True;		
	else
		$Component->Visible = False;
// -------------------------
//End Custom Code

//Close year_events_GoWeek_BeforeShow @27-5D4A8922
    return $year_events_GoWeek_BeforeShow;
}
//End Close year_events_GoWeek_BeforeShow

//year_events_ds_BeforeBuildSelect @5-A2659801
function year_events_ds_BeforeBuildSelect(& $sender)
{
    $year_events_ds_BeforeBuildSelect = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $year_events; //Compatibility
//End year_events_ds_BeforeBuildSelect

//Custom Code @24-0F2C665A
// -------------------------
	$FirstDate = CCParseDate(CCFormatDate($Container->CurrentDate, array("yyyy","-","mm","-01 00:00:00")), array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"));
	$LastDate = CCDateAdd($FirstDate, "1year -1second");

	$Days = (CCFormatDate($FirstDate, array("w")) - $Container->FirstWeekDay + 6) % 7;
	$FirstDate = CCDateAdd($FirstDate, "-" . $Days . "day");
	$Days = ($Container->FirstWeekDay - CCFormatDate($LastDate, array("w")) + 7) % 7;
	$LastDate = CCDateAdd($LastDate, $Days . "day");

	$Container->ds->Where .= AddReadFilter($Container->ds->Where);
	if (strlen($Container->ds->Where))
		$Container->ds->Where .= " AND ";
	$Container->ds->Where .= "event_date >= " . $Container->ds->ToSQL($FirstDate, ccsDate).
						" AND event_date <= " . $Container->ds->ToSQL($LastDate, ccsDate);

// -------------------------
//End Custom Code

//Close year_events_ds_BeforeBuildSelect @5-660FFC8C
    return $year_events_ds_BeforeBuildSelect;
}
//End Close year_events_ds_BeforeBuildSelect

//year_events_BeforeShowDay @5-BC718351
function year_events_BeforeShowDay(& $sender)
{
    $year_events_BeforeShowDay = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $year_events; //Compatibility
//End year_events_BeforeShowDay

//Custom Code @26-06A42B47
// -------------------------
global $divID;
global $Tpl;
global $calendar_config;

	$currentDay = $Container->CurrentProcessingDate;
	$dateStr = sprintf("%4d%02d%02d", $currentDay[ccsYear], $currentDay[ccsMonth], $currentDay[ccsDay]);

	if (isset($Container->Events[$dateStr])) {
		$divID++;
		$EventDayStyle = $calendar_config["event_day_style"];
		$Container->div_begin->SetValue("<div style=\"position: absolute; visibility: hidden; padding: 6px; border: 1px solid black; text-align: left; background: #ffffff\" name=\"float\" id=\"div".$divID."\">");
		$Container->div_end->SetValue("</div>");
		$Tpl->setvar("LinkStyle","style=\"".$EventDayStyle."\" onmouseover=\"javascript:show('".$divID."')\" onmouseout=\"javascript:hide('".$divID."')\"");
	}
	else {
		$Container->div_begin->SetValue("");
		$Container->div_end->SetValue("");
		$Tpl->setvar("LinkStyle","");
	}
// -------------------------
//End Custom Code

//Close year_events_BeforeShowDay @5-16827185
    return $year_events_BeforeShowDay;
}
//End Close year_events_BeforeShowDay

//year_events_BeforeShow @5-0D3744A8
function year_events_BeforeShow(& $sender)
{
    $year_events_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $year_events; //Compatibility
//End year_events_BeforeShow

//Custom Code @51-2A29BDB7
// -------------------------
	$year_events->CurYearLabel->SetValue($year_events->CurrentDate[ccsYear]);
// -------------------------
//End Custom Code

//Close year_events_BeforeShow @5-9161FB0B
    return $year_events_BeforeShow;
}
//End Close year_events_BeforeShow
?>
