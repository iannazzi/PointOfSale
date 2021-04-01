<?php
//BindEvents Method @1-4E06EB5A
function BindEvents()
{
    global $events_groupsSearch;
    global $events_groups;
    $events_groupsSearch->s_category->CCSEvents["BeforeShow"] = "events_groupsSearch_s_category_BeforeShow";
    $events_groups->events_groups_TotalRecords->CCSEvents["BeforeShow"] = "events_groups_events_groups_TotalRecords_BeforeShow";
    $events_groups->event_time_end->CCSEvents["BeforeShow"] = "events_groups_event_time_end_BeforeShow";
    $events_groups->event_title->CCSEvents["BeforeShow"] = "events_groups_event_title_BeforeShow";
    $events_groups->CCSEvents["BeforeShow"] = "events_groups_BeforeShow";
    $events_groups->ds->CCSEvents["BeforeBuildSelect"] = "events_groups_ds_BeforeBuildSelect";
}
//End BindEvents Method

//events_groupsSearch_s_category_BeforeShow @29-22BC49C4
function events_groupsSearch_s_category_BeforeShow(& $sender)
{
    $events_groupsSearch_s_category_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $events_groupsSearch; //Compatibility
//End events_groupsSearch_s_category_BeforeShow

//Custom Code @58-2A29BDB7
// -------------------------
	if (CCGetFromGet("s_category", -1) == -1)
		$Component->SetValue(CCGetSession("category"));
// -------------------------
//End Custom Code

//Close events_groupsSearch_s_category_BeforeShow @29-47AAD7A2
    return $events_groupsSearch_s_category_BeforeShow;
}
//End Close events_groupsSearch_s_category_BeforeShow

//events_groups_events_groups_TotalRecords_BeforeShow @12-DFB31732
function events_groups_events_groups_TotalRecords_BeforeShow(& $sender)
{
    $events_groups_events_groups_TotalRecords_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $events_groups; //Compatibility
//End events_groups_events_groups_TotalRecords_BeforeShow

//Retrieve number of records @13-ABE656B4
    $Component->SetValue($Container->DataSource->RecordsCount);
//End Retrieve number of records

//Close events_groups_events_groups_TotalRecords_BeforeShow @12-CC3E13F9
    return $events_groups_events_groups_TotalRecords_BeforeShow;
}
//End Close events_groups_events_groups_TotalRecords_BeforeShow

//events_groups_event_time_end_BeforeShow @59-B1CB5FBC
function events_groups_event_time_end_BeforeShow(& $sender)
{
    $events_groups_event_time_end_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $events_groups; //Compatibility
//End events_groups_event_time_end_BeforeShow

//Custom Code @60-2A29BDB7
// -------------------------
	if (strlen($Component->GetText()))
		$Component->Visible = True;
	else
		$Component->Visible = False;
// -------------------------
//End Custom Code

//Close events_groups_event_time_end_BeforeShow @59-47F6CA63
    return $events_groups_event_time_end_BeforeShow;
}
//End Close events_groups_event_time_end_BeforeShow

//events_groups_event_title_BeforeShow @23-F4883734
function events_groups_event_title_BeforeShow(& $sender)
{
    $events_groups_event_title_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $events_groups; //Compatibility
//End events_groups_event_title_BeforeShow

//Custom Code @72-2A29BDB7
// -------------------------
global $calendar_config;

	if ($calendar_config["popup_events"] == "1")
		$Component->SetLink("javascript:openWin('event_view_popup.php?" . CCAddParam($Component->Parameters, "ret_link", FileName."?".CCGetQueryString("QueryString","")) . "')");
// -------------------------
//End Custom Code

//Close events_groups_event_title_BeforeShow @23-A8EC2423
    return $events_groups_event_title_BeforeShow;
}
//End Close events_groups_event_title_BeforeShow

//events_groups_BeforeShow @5-16903323
function events_groups_BeforeShow(& $sender)
{
    $events_groups_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $events_groups; //Compatibility
//End events_groups_BeforeShow

//Custom Code @30-86025892
// -------------------------
	if (CCGetFromGet("s_keyword", -1) == -1 && CCGetParam("s_category", -1) == -1)
		$Component->Visible = false;

	if ($Container->ds->PageCount() < 2)
		$Container->Navigator->Visible = false;
// -------------------------
//End Custom Code

//Close events_groups_BeforeShow @5-B3E375A6
    return $events_groups_BeforeShow;
}
//End Close events_groups_BeforeShow

//events_groups_ds_BeforeBuildSelect @5-B5C62832
function events_groups_ds_BeforeBuildSelect(& $sender)
{
    $events_groups_ds_BeforeBuildSelect = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $events_groups; //Compatibility
//End events_groups_ds_BeforeBuildSelect

//Custom Code @33-86025892
// -------------------------
global $DBcalendar;

	$Container->ds->Where .= AddReadFilter($Container->ds->Where);

	if (strlen(trim(CCGetFromGet("s_keyword", "")))) {
		$SearchWord = $DBcalendar->ToSQL("%" . CCGetFromGet("s_keyword", "") . "%", ccsText);
		if (strlen($Component->ds->Where)) $Component->ds->Where .= " AND ";

		$Component->ds->Where .= "( " .
			" events.event_title LIKE  ". $SearchWord .
			" OR events.event_desc LIKE  ". $SearchWord .
			" OR events.event_location LIKE ". $SearchWord .
			" ) ";
	}

// -------------------------
//End Custom Code

//Close events_groups_ds_BeforeBuildSelect @5-DD020C22
    return $events_groups_ds_BeforeBuildSelect;
}
//End Close events_groups_ds_BeforeBuildSelect

?>