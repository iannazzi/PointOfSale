<?php
//BindEvents Method @1-83D44726
function BindEvents()
{
    global $ShortViewEventsGrid;
    global $ShortViewEventsNavigator;
    global $CCSEvents;
    $ShortViewEventsGrid->event_title->CCSEvents["BeforeShow"] = "ShortViewEventsGrid_event_title_BeforeShow";
    $ShortViewEventsGrid->ds->CCSEvents["BeforeBuildSelect"] = "ShortViewEventsGrid_ds_BeforeBuildSelect";
    $ShortViewEventsGrid->CCSEvents["BeforeShow"] = "ShortViewEventsGrid_BeforeShow";
    $ShortViewEventsGrid->CCSEvents["BeforeShowRow"] = "ShortViewEventsGrid_BeforeShowRow";
    $ShortViewEventsNavigator->ButtonGo->CCSEvents["OnClick"] = "ShortViewEventsNavigator_ButtonGo_OnClick";
    $ShortViewEventsNavigator->Button_DoSearch->CCSEvents["OnClick"] = "ShortViewEventsNavigator_Button_DoSearch_OnClick";
    $ShortViewEventsNavigator->CCSEvents["BeforeShow"] = "ShortViewEventsNavigator_BeforeShow";
    $CCSEvents["BeforeShow"] = "Page_BeforeShow";
}
//End BindEvents Method

//ShortViewEventsGrid_event_title_BeforeShow @10-95C4EFE7
function ShortViewEventsGrid_event_title_BeforeShow(& $sender)
{
    $ShortViewEventsGrid_event_title_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $ShortViewEventsGrid; //Compatibility
//End ShortViewEventsGrid_event_title_BeforeShow

//Custom Code @196-2A29BDB7
// -------------------------
	global $calendar_config;

	if ($calendar_config["popup_events"] == "1")
		$Component->SetLink("javascript:openWin('event_view_popup.php?" . CCAddParam($Component->Parameters, "ret_link", FileName."?".CCGetQueryString("QueryString","")) . "')");
// -------------------------
//End Custom Code

//Close ShortViewEventsGrid_event_title_BeforeShow @10-8186F2DB
    return $ShortViewEventsGrid_event_title_BeforeShow;
}
//End Close ShortViewEventsGrid_event_title_BeforeShow

//ShortViewEventsGrid_ds_BeforeBuildSelect @7-C02D6711
function ShortViewEventsGrid_ds_BeforeBuildSelect(& $sender)
{
    $ShortViewEventsGrid_ds_BeforeBuildSelect = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $ShortViewEventsGrid; //Compatibility
//End ShortViewEventsGrid_ds_BeforeBuildSelect

//Custom Code @12-86025892
// -------------------------
	if (!CCGetFromGet("day")) {
		if (CCStrLen($Container->ds->Where)) 
			$Container->ds->Where .= " AND ";
		$Container->ds->Where .= "event_date=" . $Container->ds->ToSQL(CCGetDateArray(), ccsDate);
	}
	$Container->ds->Where .= AddReadFilter($Container->ds->Where);
// -------------------------
//End Custom Code

//Close ShortViewEventsGrid_ds_BeforeBuildSelect @7-A4185BDA
    return $ShortViewEventsGrid_ds_BeforeBuildSelect;
}
//End Close ShortViewEventsGrid_ds_BeforeBuildSelect

//ShortViewEventsGrid_BeforeShow @7-B2EF2F31
function ShortViewEventsGrid_BeforeShow(& $sender)
{
    $ShortViewEventsGrid_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $ShortViewEventsGrid; //Compatibility
//End ShortViewEventsGrid_BeforeShow

//Custom Code @13-86025892
// -------------------------
global $calendar_config;

	if (CCGetFromGet("day"))
		$SelectDate = CCParseDate(CCGetFromGet("day"),array("yyyy","-","mm","-","dd"));
	else
		$SelectDate = CCGetDateArray();

	$Component->CalendarDate->SetValue($SelectDate);

	if (AddAllowed()) {
		$Component->add_event->Parameters = CCAddParam($Component->add_event->Parameters, "event_date", CCFormatDate($SelectDate, array("mm","/","dd","/","yyyy")));
		$Component->add_event->Parameters = CCAddParam($Component->add_event->Parameters, "ret_link", FileName . "?" . CCGetQueryString("QueryString",""));
	} else {
		$Component->add_event->Visible = False;
	}

// -------------------------
//End Custom Code

//Close ShortViewEventsGrid_BeforeShow @7-BA46EF88
    return $ShortViewEventsGrid_BeforeShow;
}
//End Close ShortViewEventsGrid_BeforeShow

//ShortViewEventsGrid_BeforeShowRow @7-69568463
function ShortViewEventsGrid_BeforeShowRow(& $sender)
{
    $ShortViewEventsGrid_BeforeShowRow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $ShortViewEventsGrid; //Compatibility
//End ShortViewEventsGrid_BeforeShowRow

//Custom Code @112-2A29BDB7
// -------------------------
	if (strlen($Component->category_image->GetValue())) {
		$Component->category_image->Visible = True;
		$Component->category_name->Visible = False;
	} else {
		$Component->category_image->Visible = False;
		if (strlen($Component->category_name->GetValue()))
			$Component->category_name->Visible = True;
		else
			$Component->category_name->Visible = False;
	}

	if (strlen($Component->event_time->GetText()))
		$Component->event_time->Visible = True;
	else
		$Component->event_time->Visible = False;
// -------------------------
//End Custom Code

//Close ShortViewEventsGrid_BeforeShowRow @7-7BB48E52
    return $ShortViewEventsGrid_BeforeShowRow;
}
//End Close ShortViewEventsGrid_BeforeShowRow

//ShortViewEventsNavigator_ButtonGo_OnClick @88-E32AD2FB
function ShortViewEventsNavigator_ButtonGo_OnClick(& $sender)
{
    $ShortViewEventsNavigator_ButtonGo_OnClick = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $ShortViewEventsNavigator; //Compatibility
//End ShortViewEventsNavigator_ButtonGo_OnClick

//Custom Code @92-2A29BDB7
// -------------------------
global $Redirect;
	if (CCGetFromGet("day"))
		$SelectDate = CCParseDate(CCGetFromGet("day"),array("yyyy","-","mm","-","dd"));
	else
		$SelectDate = CCGetDateArray();
	$SelectDate[ccsDay] = $Container->date_day->GetValue();
	$Redirect = FileName . "?day=" . CCFormatDate($SelectDate, array("yyyy","-","mm","-","dd"));
// -------------------------
//End Custom Code

//Close ShortViewEventsNavigator_ButtonGo_OnClick @88-A5574269
    return $ShortViewEventsNavigator_ButtonGo_OnClick;
}
//End Close ShortViewEventsNavigator_ButtonGo_OnClick

//ShortViewEventsNavigator_Button_DoSearch_OnClick @104-74D8A044
function ShortViewEventsNavigator_Button_DoSearch_OnClick(& $sender)
{
    $ShortViewEventsNavigator_Button_DoSearch_OnClick = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $ShortViewEventsNavigator; //Compatibility
//End ShortViewEventsNavigator_Button_DoSearch_OnClick

//Custom Code @105-2A29BDB7
// -------------------------
global $Redirect;
	$Redirect = "week.php?day=" . $Container->week->GetValue();
// -------------------------
//End Custom Code

//Close ShortViewEventsNavigator_Button_DoSearch_OnClick @104-595B5E38
    return $ShortViewEventsNavigator_Button_DoSearch_OnClick;
}
//End Close ShortViewEventsNavigator_Button_DoSearch_OnClick

//ShortViewEventsNavigator_BeforeShow @85-6B5E3E3E
function ShortViewEventsNavigator_BeforeShow(& $sender)
{
    $ShortViewEventsNavigator_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $ShortViewEventsNavigator; //Compatibility
//End ShortViewEventsNavigator_BeforeShow

//Custom Code @91-2A29BDB7
// -------------------------
global $CCSLocales;
	if (CCGetFromGet("day",""))
		$SelectDay = CCParseDate(CCGetFromGet("day",""),array("yyyy","-","mm","-","dd"));
	else
		$SelectDay = CCGetDateArray();
	$Container->prev_day_link->Parameters = CCAddParam($Container->prev_day_link->Parameters,"day",CCFormatDate(CCDateAdd($SelectDay, "-1days"),array("yyyy","-","mm","-","dd")));
	$Container->next_day_link->Parameters = CCAddParam($Container->next_day_link->Parameters,"day",CCFormatDate(CCDateAdd($SelectDay,  "1days"),array("yyyy","-","mm","-","dd")));

	for($i=1; $i<=CCDaysInMonth($SelectDay[ccsYear], $SelectDay[ccsMonth]); $i++)
		$arr[] = array($i, $i);
	$Component->date_day->Values = $arr;
	$Component->date_day->Value = $SelectDay[ccsDay];

	$FirstWeekDay = $CCSLocales->GetFormatInfo("FirstWeekDay");
	$LastYearDay = $SelectDay;
	$LastYearDay[ccsDay] = 31;
	$LastYearDay[ccsMonth] = 12;

	$FirstDay = $SelectDay;
	$FirstDay[ccsDay] = 1;
	$FirstDay[ccsMonth] = 1;
	$FirstDay = CCDateAdd($FirstDay, ((-6-CCDayOfWeek($FirstDay)+$FirstWeekDay)%7)."days");

	do {
		$LastDay = CCDateAdd($FirstDay, "6days");
		$WeekTxt = CCFormatDate($FirstDay, array("mmm"," ","d")) . " - " . CCFormatDate($LastDay, array("mmm"," ","d"));
		$WeekArr[] = array(CCFormatDate($FirstDay, array("yyyy","-","mm","-","dd")), $WeekTxt);
		$FirstDay = CCDateAdd($FirstDay, "7days");
	} while (CCCompareValues($FirstDay, $LastYearDay, ccsDate) < 1);

	$Component->week->Values = $WeekArr;
	$Component->week->Value = CCFormatDate(CCDateAdd($SelectDay, ((-6-CCDayOfWeek($SelectDay)+$FirstWeekDay)%7)."days"), array("yyyy","-","mm","-","dd"));

	$Component->WeekIcon->Parameters = CCAddParam($Component->WeekIcon->Parameters, "day", CCFormatDate($SelectDay, array("yyyy","-","mm","-","dd")));
	$Component->MonthIcon->Parameters = CCAddParam($Component->MonthIcon->Parameters, "cal_monthDate", CCFormatDate($SelectDay, array("yyyy","-","mm")));
	$Component->YearIcon->Parameters = CCAddParam($Component->YearIcon->Parameters, "year_eventsDate", CCFormatDate($SelectDay, array("yyyy","-","mm")));

// -------------------------
//End Custom Code

//Close ShortViewEventsNavigator_BeforeShow @85-A970B3F4
    return $ShortViewEventsNavigator_BeforeShow;
}
//End Close ShortViewEventsNavigator_BeforeShow

//Page_BeforeShow @1-DD2C1DE9
function Page_BeforeShow(& $sender)
{
    $Page_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $day; //Compatibility
//End Page_BeforeShow

//Custom Code @6-DBF57073
// -------------------------
	global $calendar_config;
	global $FullViewEvents;
	global $ShortViewEvents;

//	if ($calendar_config["week_short"]) {
		$ShortViewEvents->Visible = True;
//		$FullViewEvents->Visible = False;
//	}
//	else {
//		$ShortViewEvents->Visible = False;
//		$FullViewEvents->Visible = True;
//	}
// -------------------------
//End Custom Code

//Close Page_BeforeShow @1-4BC230CD
    return $Page_BeforeShow;
}
//End Close Page_BeforeShow


?>
