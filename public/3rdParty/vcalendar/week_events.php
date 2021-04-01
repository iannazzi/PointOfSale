<?php
//BindEvents Method @1-405F74FA
function BindEvents()
{
    global $ShortViewEventsGrid;
    global $ShortViewEventsNavigator;
    global $CCSEvents;
    $ShortViewEventsGrid->event_title->CCSEvents["BeforeShow"] = "ShortViewEventsGrid_event_title_BeforeShow";
    $ShortViewEventsGrid->CCSEvents["BeforeShow"] = "ShortViewEventsGrid_BeforeShow";
    $ShortViewEventsGrid->CCSEvents["BeforeShowRow"] = "ShortViewEventsGrid_BeforeShowRow";
    $ShortViewEventsGrid->ds->CCSEvents["BeforeBuildSelect"] = "ShortViewEventsGrid_ds_BeforeBuildSelect";
    $ShortViewEventsNavigator->GoWeek->CCSEvents["OnClick"] = "ShortViewEventsNavigator_GoWeek_OnClick";
    $ShortViewEventsNavigator->GoMonth->CCSEvents["OnClick"] = "ShortViewEventsNavigator_GoMonth_OnClick";
    $ShortViewEventsNavigator->CCSEvents["BeforeShow"] = "ShortViewEventsNavigator_BeforeShow";
    $CCSEvents["AfterInitialize"] = "Page_AfterInitialize";
}
//End BindEvents Method

//ShortViewEventsGrid_event_title_BeforeShow @164-95C4EFE7
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

//Close ShortViewEventsGrid_event_title_BeforeShow @164-8186F2DB
    return $ShortViewEventsGrid_event_title_BeforeShow;
}
//End Close ShortViewEventsGrid_event_title_BeforeShow

function GetFormatLink($FirstDay, $LastDay, $AddAllowed) {
	$str = "";
	while (CCCompareValues($FirstDay, $LastDay, ccsDate)) {
		$str .= '<tr class="GroupCaption"><th colspan="2"><a href="day.php?day='
				. CCFormatDate($FirstDay, array("yyyy","-","mm","-","dd")) . '">'
				. CCFormatDate($FirstDay, array("ShortDate")) . '</a>'
				. ($AddAllowed? ' &nbsp;&nbsp;&nbsp;&nbsp;<a href="events.php?event_date='
					. urlencode(CCFormatDate($FirstDay, array("mm","/","dd","/","yyyy")))
					. '&ret_link=' . urlencode(FileName . "?" . CCGetQueryString("QueryString",""))
					. '"><img border="0" src="images/icon-add-big.gif"></a>' : '')
				. '</th></tr><tr class="Row"><td width="10%">&nbsp;</td><td>&nbsp;</td></tr>';
		$FirstDay = CCDateAdd($FirstDay, "1day");
	}
	return $str;
}
//ShortViewEventsGrid_BeforeShow @144-B2EF2F31
function ShortViewEventsGrid_BeforeShow(& $sender)
{
    $ShortViewEventsGrid_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $ShortViewEventsGrid; //Compatibility
//End ShortViewEventsGrid_BeforeShow

//Custom Code @177-2A29BDB7
// -------------------------
global $CCSLocales;

	if (CCGetFromGet("day"))
		$SelectDay = CCParseDate(CCGetFromGet("day"), array("yyyy","-","mm","-","dd"));
	else
		$SelectDay = CCGetDateArray();

	$FirstWeekDay = $CCSLocales->GetFormatInfo("FirstWeekDay");

	$FirstDay = CCDateAdd($SelectDay, (($FirstWeekDay-6-CCDayOfWeek($SelectDay))%7)."days");
	$LastDay = CCDateAdd($FirstDay, "6days");

	$Container->week_date_begin->SetValue($FirstDay);
	$Container->week_date_end->SetValue($LastDay);

	$Component->NoEventsLastDay->SetValue(GetFormatLink($FirstDay, CCDateAdd($LastDay, "1day"), AddAllowed()));

// -------------------------
//End Custom Code

//Close ShortViewEventsGrid_BeforeShow @144-BA46EF88
    return $ShortViewEventsGrid_BeforeShow;
}
//End Close ShortViewEventsGrid_BeforeShow

//ShortViewEventsGrid_BeforeShowRow @144-69568463
function ShortViewEventsGrid_BeforeShowRow(& $sender)
{
    $ShortViewEventsGrid_BeforeShowRow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $ShortViewEventsGrid; //Compatibility
//End ShortViewEventsGrid_BeforeShowRow

//Custom Code @178-2A29BDB7
// -------------------------
global $PrevDate;
global $CCSLocales;

	if (strlen($Component->event_time_end->GetText()))
		$Component->event_time_end->Visible = True;
	else
		$Component->event_time_end->Visible = False;

	if (strlen($Component->category_image->GetValue()))
		$Component->category_image->Visible = True;
	else {
		$Component->category_image->Visible = False;
		if (strlen($Component->category_name->GetValue()))
			$Component->category_name->Visible = True;
		else
			$Component->category_name->Visible = False;
	}

	if (IsSet($PrevDate) && !CCCompareValues($PrevDate, $Component->event_date->GetValue(), ccsDate)) {
		$Component->EventDayPanel->Visible = False;
	} else {
		$FirstWeekDay = $CCSLocales->GetFormatInfo("FirstWeekDay");
		if (!IsSet($PrevDate)) {
			$PrevDate = $Component->event_date->GetValue();
			$FirstDay = CCDateAdd($PrevDate, ((-6-CCDayOfWeek($PrevDate)+$FirstWeekDay)%7)."days");
			$LastDay = CCDateAdd($FirstDay, "7days");
		} else {
			$LastDay = CCDateAdd($PrevDate, (($FirstWeekDay-6-CCDayOfWeek($PrevDate))%7)+7 . "days");
			$FirstDay = CCDateAdd($PrevDate, "1day");
			$PrevDate = $Component->event_date->GetValue();
		}

		if ($add_all = AddAllowed()) {
			$Component->add_event->Visible = True;
			$Component->add_event->Parameters = CCAddParam($Component->add_event->Parameters, "event_date", CCFormatDate($PrevDate, array("mm","/","dd","/","yyyy")));
			$Component->add_event->Parameters = CCAddParam($Component->add_event->Parameters, "ret_link", FileName . "?" . CCGetQueryString("QueryString",""));
		} else
			$Component->add_event->Visible = False;

		$Component->NoEventsDay->SetValue(GetFormatLink($FirstDay, $PrevDate, $add_all));
		$Component->NoEventsLastDay->SetValue(GetFormatLink(CCDateAdd($PrevDate, "1day"), $LastDay, $add_all));

		$Component->EventDayPanel->Visible = True;
	}

// -------------------------
//End Custom Code

//Close ShortViewEventsGrid_BeforeShowRow @144-7BB48E52
    return $ShortViewEventsGrid_BeforeShowRow;
}
//End Close ShortViewEventsGrid_BeforeShowRow

//ShortViewEventsGrid_ds_BeforeBuildSelect @144-C02D6711
function ShortViewEventsGrid_ds_BeforeBuildSelect(& $sender)
{
    $ShortViewEventsGrid_ds_BeforeBuildSelect = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $ShortViewEventsGrid; //Compatibility
//End ShortViewEventsGrid_ds_BeforeBuildSelect

//Custom Code @179-2A29BDB7
// -------------------------
global $CCSLocales;

	$SelectDay = CCParseDate(CCGetFromGet("day",CCFormatDate(CCGetDateArray(),array("yyyy","-","mm","-","dd"))),array("yyyy","-","mm","-","dd"));

	$FirstWeekDay = $CCSLocales->GetFormatInfo("FirstWeekDay");

	$FirstDay = CCDateAdd($SelectDay, ((-6-CCDayOfWeek($SelectDay)+$FirstWeekDay)%7)."days");
	$LastDay = CCDateAdd($FirstDay, "6days");

	if (CCStrLen($Container->ds->Where))
		$Container->ds->Where .= " AND ";

	$Container->ds->Where .= "(event_date >= " . $Container->ds->ToSQL($FirstDay, ccsDate).
						  " AND event_date <= " . $Container->ds->ToSQL($LastDay, ccsDate) . ")";
	$Container->ds->Where .= AddReadFilter($Container->ds->Where);
// -------------------------
//End Custom Code

//Close ShortViewEventsGrid_ds_BeforeBuildSelect @144-A4185BDA
    return $ShortViewEventsGrid_ds_BeforeBuildSelect;
}
//End Close ShortViewEventsGrid_ds_BeforeBuildSelect

//ShortViewEventsNavigator_GoWeek_OnClick @104-2FC0E728
function ShortViewEventsNavigator_GoWeek_OnClick(& $sender)
{
    $ShortViewEventsNavigator_GoWeek_OnClick = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $ShortViewEventsNavigator; //Compatibility
//End ShortViewEventsNavigator_GoWeek_OnClick

//Custom Code @192-2A29BDB7
// -------------------------
global $Redirect;

	$Redirect = "week.php?day=" . $Container->week->GetValue();

// -------------------------
//End Custom Code

//Close ShortViewEventsNavigator_GoWeek_OnClick @104-C39D292D
    return $ShortViewEventsNavigator_GoWeek_OnClick;
}
//End Close ShortViewEventsNavigator_GoWeek_OnClick

//ShortViewEventsNavigator_GoMonth_OnClick @190-E8F4D769
function ShortViewEventsNavigator_GoMonth_OnClick(& $sender)
{
    $ShortViewEventsNavigator_GoMonth_OnClick = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $ShortViewEventsNavigator; //Compatibility
//End ShortViewEventsNavigator_GoMonth_OnClick

//Custom Code @191-2A29BDB7
// -------------------------
global $Redirect;

	$Redirect = "index.php?cal_monthDate=" . $Container->month->GetValue();

// -------------------------
//End Custom Code

//Close ShortViewEventsNavigator_GoMonth_OnClick @190-2C10C617
    return $ShortViewEventsNavigator_GoMonth_OnClick;
}
//End Close ShortViewEventsNavigator_GoMonth_OnClick

//ShortViewEventsNavigator_BeforeShow @102-6B5E3E3E
function ShortViewEventsNavigator_BeforeShow(& $sender)
{
    $ShortViewEventsNavigator_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $ShortViewEventsNavigator; //Compatibility
//End ShortViewEventsNavigator_BeforeShow

//Custom Code @105-2A29BDB7
// -------------------------
global $CCSLocales;

	if (CCGetFromGet("day",""))
		$SelectDay = CCParseDate(CCGetFromGet("day",""),array("yyyy","-","mm","-","dd"));
	else
		$SelectDay = CCGetDateArray();

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

	$FirstDay = $SelectDay;
	$arr = $CCSLocales->GetFormatInfo("MonthNames");

	for($i=1; $i<13; $i++) {
		$FirstDay[ccsMonth] = $i;
		$monthArr[] = array(CCFormatDate($FirstDay, array("yyyy","-","mm")),$arr[$i-1]);
	}
	$Container->month->Values = $monthArr;
	$Component->month->Value = CCFormatDate($SelectDay, array("yyyy","-","mm"));

	$LastDay = CCDateAdd($FirstDay, "6days");

	$Component->prev_week_link->Parameters = CCAddParam($Container->prev_week_link->Parameters,"day",CCFormatDate(CCDateAdd($SelectDay, "-7days"),array("yyyy","-","mm","-","dd")));
	$Component->next_week_link->Parameters = CCAddParam($Container->next_week_link->Parameters,"day",CCFormatDate(CCDateAdd($SelectDay,  "7days"),array("yyyy","-","mm","-","dd")));

//	$Component->WeekIcon->Parameters = CCAddParam($Component->WeekIcon->Parameters, "day", CCFormatDate($SelectDay, array("yyyy","-","mm","-","dd")));
	$Component->MonthIcon->Parameters = CCAddParam($Component->MonthIcon->Parameters, "cal_monthDate", CCFormatDate($SelectDay, array("yyyy","-","mm")));
	$Component->YearIcon->Parameters = CCAddParam($Component->YearIcon->Parameters, "year_eventsDate", CCFormatDate($SelectDay, array("yyyy","-","mm")));

// -------------------------
//End Custom Code

//Close ShortViewEventsNavigator_BeforeShow @102-A970B3F4
    return $ShortViewEventsNavigator_BeforeShow;
}
//End Close ShortViewEventsNavigator_BeforeShow

//Page_AfterInitialize @1-E5CA7449
function Page_AfterInitialize(& $sender)
{
    $Page_AfterInitialize = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $week; //Compatibility
//End Page_AfterInitialize

//Custom Code @72-2A29BDB7
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

//Close Page_AfterInitialize @1-379D319D
    return $Page_AfterInitialize;
}
//End Close Page_AfterInitialize
?>
