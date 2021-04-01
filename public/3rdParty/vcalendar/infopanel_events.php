<?php
// //Events @1-F81417CB

//infopanel_InfoCalendar_GoWeekHeader_BeforeShow @56-AF0BB295
function infopanel_InfoCalendar_GoWeekHeader_BeforeShow(& $sender)
{
    $infopanel_InfoCalendar_GoWeekHeader_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $infopanel; //Compatibility
//End infopanel_InfoCalendar_GoWeekHeader_BeforeShow

//Custom Code @58-2A29BDB7
// -------------------------
global $calendar_config;

	if ($calendar_config["info_week_icon"] == 1)
		$Component->Visible = True;		
	else
		$Component->Visible = False;
// -------------------------
//End Custom Code

//Close infopanel_InfoCalendar_GoWeekHeader_BeforeShow @56-0C0A02C0
    return $infopanel_InfoCalendar_GoWeekHeader_BeforeShow;
}
//End Close infopanel_InfoCalendar_GoWeekHeader_BeforeShow

//infopanel_InfoCalendar_category_image_BeforeShow @35-17DD4C3E
function infopanel_InfoCalendar_category_image_BeforeShow(& $sender)
{
    $infopanel_InfoCalendar_category_image_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $infopanel; //Compatibility
//End infopanel_InfoCalendar_category_image_BeforeShow

//Custom Code @36-2A29BDB7
// -------------------------
	if (strlen($Component->GetValue()))
		$Component->Visible = True;
	else
		$Component->Visible = False;
// -------------------------
//End Custom Code

//Close infopanel_InfoCalendar_category_image_BeforeShow @35-F82C392C
    return $infopanel_InfoCalendar_category_image_BeforeShow;
}
//End Close infopanel_InfoCalendar_category_image_BeforeShow

//infopanel_InfoCalendar_EventTime_BeforeShow @153-51C65885
function infopanel_InfoCalendar_EventTime_BeforeShow(& $sender)
{
    $infopanel_InfoCalendar_EventTime_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $infopanel; //Compatibility
//End infopanel_InfoCalendar_EventTime_BeforeShow

//Custom Code @25-2A29BDB7
// -------------------------
	if (strlen($Component->GetText()))
		$Component->Visible = True;
	else
		$Component->Visible = False;
// -------------------------
//End Custom Code

//Close infopanel_InfoCalendar_EventTime_BeforeShow @153-993812FC
    return $infopanel_InfoCalendar_EventTime_BeforeShow;
}
//End Close infopanel_InfoCalendar_EventTime_BeforeShow

//infopanel_InfoCalendar_GoWeek_BeforeShow @27-74F35FB9
function infopanel_InfoCalendar_GoWeek_BeforeShow(& $sender)
{
    $infopanel_InfoCalendar_GoWeek_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $infopanel; //Compatibility
//End infopanel_InfoCalendar_GoWeek_BeforeShow

//Custom Code @57-2A29BDB7
// -------------------------
global $calendar_config;

	if ($calendar_config["info_week_icon"] == 1)
		$Component->Visible = True;		
	else
		$Component->Visible = False;
// -------------------------
//End Custom Code

//Close infopanel_InfoCalendar_GoWeek_BeforeShow @27-85BC864A
    return $infopanel_InfoCalendar_GoWeek_BeforeShow;
}
//End Close infopanel_InfoCalendar_GoWeek_BeforeShow

//infopanel_InfoCalendar_InfoNavigator_BeforeShow @170-9886CF10
function infopanel_InfoCalendar_InfoNavigator_BeforeShow(& $sender)
{
    $infopanel_InfoCalendar_InfoNavigator_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $infopanel; //Compatibility
//End infopanel_InfoCalendar_InfoNavigator_BeforeShow

//Custom Code @172-2A29BDB7
// -------------------------
	$CurrDate = $Container->CurrentProcessingDate;
	$Container->PrevMonth->Parameters = CCAddParam($Container->PrevMonth->Parameters, "InfoCalendar", CCFormatDate(CCDateAdd($CurrDate, "-1month"), array("yyyy","-","mm")));
	$Container->NextMonth->Parameters = CCAddParam($Container->NextMonth->Parameters, "InfoCalendar", CCFormatDate(CCDateAdd($CurrDate,  "1month"), array("yyyy","-","mm")));
// -------------------------
//End Custom Code

//Close infopanel_InfoCalendar_InfoNavigator_BeforeShow @170-7347E93F
    return $infopanel_InfoCalendar_InfoNavigator_BeforeShow;
}
//End Close infopanel_InfoCalendar_InfoNavigator_BeforeShow

//infopanel_InfoCalendar_BeforeShowDay @108-BD8C482D
function infopanel_InfoCalendar_BeforeShowDay(& $sender)
{
    $infopanel_InfoCalendar_BeforeShowDay = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $infopanel; //Compatibility
//End infopanel_InfoCalendar_BeforeShowDay

//Custom Code @114-2A29BDB7
// -------------------------
global $divID;
global $Tpl;
global $calendar_config;
	
	$CurrentDay = $Container->CurrentProcessingDate;
	$CurrentDayStr = sprintf("%4d%02d%02d", $CurrentDay[ccsYear], $CurrentDay[ccsMonth], $CurrentDay[ccsDay]);

	if (IsSet($Container->Events[$CurrentDayStr])) {
		$divID++;
		$EventDayStyle = $calendar_config["event_day_style"];
		$Container->div_begin->SetValue("<div style=\"position: absolute; visibility: hidden; padding: 6px; border: 1px solid black; text-align: left; background: #ffffff\" name=\"float\" id=\"div".$divID."\">");
		$Container->div_end->SetValue("</div>");
		$LinkStyle = "style=\"".$EventDayStyle."\" onmouseover=\"javascript:show('".$divID."')\" onmouseout=\"javascript:hide('".$divID."')\"";
	} else {
		$Container->div_begin->SetValue("");
		$Container->div_end->SetValue("");
		$LinkStyle = "";
	}

	if (FileName == "day.php" || FileName == "week.php") {
		$SelectDay = CCParseDate(CCGetFromGet("day",CCFormatDate(CCGetDateArray(), array("yyyy","-","mm","-","dd"))), array("yyyy","-","mm","-","dd"));
		if (FileName == "week.php") {
			$FirstWeekDay = $Container->FirstWeekDay;
			$SelectDay = CCDateAdd($SelectDay, ((-6-CCDayOfWeek($SelectDay)+$FirstWeekDay)%7)."days");
			$LastDay = CCDateAdd($SelectDay, "6days");
		} else 
			$LastDay = $SelectDay;

		if (CCCompareValues($CurrentDay, $SelectDay, ccsDate) >= 0 && CCCompareValues($CurrentDay, $LastDay, ccsDate) <= 0) {
			$Component->CurrentStyle = "class=\"CalendarSelectedDay\"";
			if (!strlen($LinkStyle))
				$LinkStyle = "style=\"font-weight: normal\"";
		}
	}

	$Tpl->setvar("LinkStyle", $LinkStyle);
// -------------------------
//End Custom Code

//Close infopanel_InfoCalendar_BeforeShowDay @108-9C7BA354
    return $infopanel_InfoCalendar_BeforeShowDay;
}
//End Close infopanel_InfoCalendar_BeforeShowDay

//infopanel_InfoCalendar_ds_BeforeBuildSelect @108-C6F0614B
function infopanel_InfoCalendar_ds_BeforeBuildSelect(& $sender)
{
    $infopanel_InfoCalendar_ds_BeforeBuildSelect = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $infopanel; //Compatibility
//End infopanel_InfoCalendar_ds_BeforeBuildSelect

//Custom Code @115-187067D8
// -------------------------
	$FirstDate = CCParseDate(CCFormatDate($Container->CurrentDate, array("yyyy","-","mm","-01 00:00:00")), array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"));
	$LastDate = CCDateAdd($FirstDate, "1month -1second");

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

//Close infopanel_InfoCalendar_ds_BeforeBuildSelect @108-F38D2D2A
    return $infopanel_InfoCalendar_ds_BeforeBuildSelect;
}
//End Close infopanel_InfoCalendar_ds_BeforeBuildSelect

//infopanel_AfterInitialize @1-27C05026
function infopanel_AfterInitialize(& $sender)
{
    $infopanel_AfterInitialize = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $infopanel; //Compatibility
//End infopanel_AfterInitialize

//Custom Code @20-187067D8
// -------------------------
global $calendar_config;

	if (FileName == "mini_calendar.php") {
		$Container->vertical_menu->Visible = false;
		$Container->InfoJavaScriptPanel->Visible = false;
		$Container->InfoCalendar->Visible = true;
		$Container->InfoCalendar->InfoNavigator->Visible = true;
		if (strlen(CCGetFromGet("InfoCalendar")) ) {
			$Container->InfoCalendar->CurrentDate = CCParseDate(CCGetFromGet("InfoCalendar"), array("yyyy","-","mm"));
		}
		return;
	}

	switch ($calendar_config["info_calendar"]) {
		case "None" : $Container->InfoCalendar->Visible = False; break;
		case "Selected":
			switch (FileName) {
				case "index.php" :
					if ($calendar_config["info_in_views"] == 2) {
						$QueryString = CCGetQueryString("QueryString", "");
						if (strlen(CCGetFromGet("cal_monthDate")) && (!strlen(CCGetFromGet("InfoCalendar")) || strpos($QueryString, "cal_monthDate") > strpos($QueryString, "InfoCalendar")))
							$Container->InfoCalendar->CurrentDate = CCParseDate(CCGetFromGet("cal_monthDate"), array("yyyy","-","mm"));
						elseif ((strlen(CCGetFromGet("cal_monthMonth")) || strlen(CCGetFromGet("cal_monthYear"))) && (!strlen(CCGetFromGet("InfoCalendar")) || strpos($QueryString, "cal_monthYear") > strpos($QueryString, "InfoCalendar") || strpos($QueryString, "cal_monthMonth") > strpos($QueryString, "InfoCalendar"))) {
							$Container->InfoCalendar->CurrentDate[ccsMonth] = CCGetFromGet("cal_monthMonth");
							$Container->InfoCalendar->CurrentDate[ccsYear] = CCGetFromGet("cal_monthYear");
						} elseif (strlen(CCGetFromGet("InfoCalendar")))
							$Container->InfoCalendar->CurrentDate = CCParseDate(CCGetFromGet("InfoCalendar"), array("yyyy","-","mm"));
					} else
						$Container->InfoCalendar->Visible = False;
					break;

				case "day.php" : case "week.php" :
					if (strlen(CCGetFromGet("day")) && !strlen(CCGetFromGet("InfoCalendar")))
						$Container->InfoCalendar->CurrentDate = CCParseDate(CCGetFromGet("day"), array("yyyy","-","mm","-","dd"));
					elseif (strlen(CCGetFromGet("InfoCalendar")))
						$Container->InfoCalendar->CurrentDate = CCParseDate(CCGetFromGet("InfoCalendar"), array("yyyy","-","mm"));
					break;

				default : $Container->InfoCalendar->Visible = False;
			}
			if ($calendar_config["info_navigator"] != 0)
				$Container->InfoCalendar->InfoNavigator->Visible = True;
			else
				$Container->InfoCalendar->InfoNavigator->Visible = False;
			break;
		default:
			switch (FileName) {
				case "index.php" :
					if ($calendar_config["info_in_views"] != 2)
						$Container->InfoCalendar->Visible = False;
					break;

				case "day.php" : case "week.php" : break;

				default : $Container->InfoCalendar->Visible = False;
			}
			$Container->InfoCalendar->InfoNavigator->Visible = False;
	}

// -------------------------
//End Custom Code

//Close infopanel_AfterInitialize @1-5C19CAA4
    return $infopanel_AfterInitialize;
}
//End Close infopanel_AfterInitialize

?>