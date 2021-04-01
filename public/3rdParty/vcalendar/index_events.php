<?php
//BindEvents Method @1-B59319A0
function BindEvents()
{
    global $cal_month;
    global $CCSEvents;
    $cal_month->add_event->CCSEvents["BeforeShow"] = "cal_month_add_event_BeforeShow";
    $cal_month->category_image->CCSEvents["BeforeShow"] = "cal_month_category_image_BeforeShow";
    $cal_month->EventTime->CCSEvents["BeforeShow"] = "cal_month_EventTime_BeforeShow";
    $cal_month->EventDescription->CCSEvents["BeforeShow"] = "cal_month_EventDescription_BeforeShow";
    $cal_month->CalendarTypes->CCSEvents["BeforeShow"] = "cal_month_CalendarTypes_BeforeShow";
    $cal_month->ds->CCSEvents["BeforeBuildSelect"] = "cal_month_ds_BeforeBuildSelect";
    $CCSEvents["AfterInitialize"] = "Page_AfterInitialize";
}
//End BindEvents Method

//cal_month_add_event_BeforeShow @47-5DE6EB4E
function cal_month_add_event_BeforeShow(& $sender)
{
    $cal_month_add_event_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $cal_month; //Compatibility
//End cal_month_add_event_BeforeShow

//Custom Code @53-2A29BDB7
// -------------------------
	if (AddAllowed()) {
		$Component->Visible = True;
		$Component->Parameters = CCAddParam($Component->Parameters, "ret_link", FileName."?".CCGetQueryString("QueryString","")	);
	} else
		$Component->Visible = False;
// -------------------------
//End Custom Code

//Close cal_month_add_event_BeforeShow @47-105F2093
    return $cal_month_add_event_BeforeShow;
}
//End Close cal_month_add_event_BeforeShow

//cal_month_category_image_BeforeShow @35-7937CCE5
function cal_month_category_image_BeforeShow(& $sender)
{
    $cal_month_category_image_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $cal_month; //Compatibility
//End cal_month_category_image_BeforeShow

//Custom Code @51-2A29BDB7
// -------------------------
  	if (strlen($Component->GetValue()))
  		$Component->Visible = True;
  	else
  		$Component->Visible = False;
// -------------------------
//End Custom Code

//Close cal_month_category_image_BeforeShow @35-B9DA8225
    return $cal_month_category_image_BeforeShow;
}
//End Close cal_month_category_image_BeforeShow

//cal_month_EventTime_BeforeShow @20-7EE3498E
function cal_month_EventTime_BeforeShow(& $sender)
{
    $cal_month_EventTime_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $cal_month; //Compatibility
//End cal_month_EventTime_BeforeShow

//Custom Code @52-2A29BDB7
// -------------------------
  	if (strlen($Component->GetText()))
  		$Component->Visible = True;
  	else
  		$Component->Visible = False;
// -------------------------
//End Custom Code

//Close cal_month_EventTime_BeforeShow @20-9A8EE8AE
    return $cal_month_EventTime_BeforeShow;
}
//End Close cal_month_EventTime_BeforeShow

//cal_month_EventDescription_BeforeShow @21-61B7E8B8
function cal_month_EventDescription_BeforeShow(& $sender)
{
    $cal_month_EventDescription_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $cal_month; //Compatibility
//End cal_month_EventDescription_BeforeShow

//Custom Code @57-2A29BDB7
// -------------------------
	global $calendar_config;

	if ($calendar_config["popup_events"] == "1")
		$Component->SetLink("javascript:openWin('event_view_popup.php?" . CCAddParam($Component->Parameters, "ret_link", FileName."?".CCGetQueryString("QueryString","")) . "')");
// -------------------------
//End Custom Code

//Close cal_month_EventDescription_BeforeShow @21-A4D71BC7
    return $cal_month_EventDescription_BeforeShow;
}
//End Close cal_month_EventDescription_BeforeShow

//cal_month_CalendarTypes_BeforeShow @198-31B206EA
function cal_month_CalendarTypes_BeforeShow(& $sender)
{
    $cal_month_CalendarTypes_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $cal_month; //Compatibility
//End cal_month_CalendarTypes_BeforeShow

//Custom Code @199-2A29BDB7
// -------------------------
	$SelectDay = $Container->CurrentDate;
	$Container->WeekIcon->Parameters = CCAddParam($Container->WeekIcon->Parameters, "day", CCFormatDate($SelectDay, array("yyyy","-","mm","-","dd")));
//	$Container->MonthIcon->Parameters = CCAddParam($Container->MonthIcon->Parameters, "cal_monthDate", CCFormatDate($SelectDay, array("yyyy","-","mm")));
	$Container->YearIcon->Parameters = CCAddParam($Container->YearIcon->Parameters, "year_eventsDate", CCFormatDate($SelectDay, array("yyyy","-","mm")));
// -------------------------
//End Custom Code

//Close cal_month_CalendarTypes_BeforeShow @198-3E3F6E1E
    return $cal_month_CalendarTypes_BeforeShow;
}
//End Close cal_month_CalendarTypes_BeforeShow

//cal_month_ds_BeforeBuildSelect @5-25FFAD25
function cal_month_ds_BeforeBuildSelect(& $sender)
{
    $cal_month_ds_BeforeBuildSelect = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $cal_month; //Compatibility
//End cal_month_ds_BeforeBuildSelect

//Custom Code @26-89F339A8
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

//Close cal_month_ds_BeforeBuildSelect @5-F03BD778
    return $cal_month_ds_BeforeBuildSelect;
}
//End Close cal_month_ds_BeforeBuildSelect

//Page_AfterInitialize @1-55FCE997
function Page_AfterInitialize(& $sender)
{
    $Page_AfterInitialize = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $index; //Compatibility
//End Page_AfterInitialize

//Logout @29-A948B367
    if(strlen(CCGetParam("Logout", ""))) 
    {
        CCLogoutUser();
    }
//End Logout

//Close Page_AfterInitialize @1-379D319D
    return $Page_AfterInitialize;
}
//End Close Page_AfterInitialize

?>