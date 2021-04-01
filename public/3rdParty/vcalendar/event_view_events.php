<?php
//BindEvents Method @1-54696C22
function BindEvents()
{
    global $eventGrid;
    global $CCSEvents;
    $eventGrid->edit->CCSEvents["BeforeShow"] = "eventGrid_edit_BeforeShow";
    $eventGrid->CCSEvents["BeforeShowRow"] = "eventGrid_BeforeShowRow";
    $CCSEvents["AfterInitialize"] = "Page_AfterInitialize";
}
//End BindEvents Method

//eventGrid_edit_BeforeShow @42-427CC52F
function eventGrid_edit_BeforeShow(& $sender)
{
    $eventGrid_edit_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $eventGrid; //Compatibility
//End eventGrid_edit_BeforeShow

//Custom Code @43-2A29BDB7
// -------------------------
	if (!EditAllowed(CCGetFromGet("event_id","")))
		$eventGrid->edit->Visible = false;
	else
		$eventGrid->edit_event->Parameters = CCAddParam($eventGrid->edit_event->Parameters,"ret_link",FileName."?".CCGetQueryString("QueryString", array("ccsForm")));
// -------------------------
//End Custom Code

//Close eventGrid_edit_BeforeShow @42-6D314EBE
    return $eventGrid_edit_BeforeShow;
}
//End Close eventGrid_edit_BeforeShow

//eventGrid_BeforeShowRow @5-BCE2C937
function eventGrid_BeforeShowRow(& $sender)
{
    $eventGrid_BeforeShowRow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $eventGrid; //Compatibility
//End eventGrid_BeforeShowRow

//Custom Code @39-2A29BDB7
// -------------------------
	global $calendar_config;

	if (strlen($Component->event_time_end->GetText()))
		$Component->event_time_end->Visible = True;
	else
		$Component->event_time_end->Visible = False;

	if (strlen($Component->event_time->GetText()))
		$Component->event_time->Visible = True;
	else
		$Component->event_time->Visible = False;		

	if (strlen($Component->category_id->GetValue()))
		$Component->category_id->Visible = True;
	else
		$Component->category_id->Visible = False;

	processCustomFields($Component, 1);
// -------------------------
//End Custom Code

//Close eventGrid_BeforeShowRow @5-0014368C
    return $eventGrid_BeforeShowRow;
}
//End Close eventGrid_BeforeShowRow

//Page_AfterInitialize @1-ED9E1B21
function Page_AfterInitialize(& $sender)
{
    $Page_AfterInitialize = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $event_view; //Compatibility
//End Page_AfterInitialize

//Custom Code @179-2A29BDB7
// -------------------------
global $Redirect;

	if (!ReadAllowed(CCGetFromGet("event_id"))) {
		header("Location: index.php");
	}
// -------------------------
//End Custom Code

//Close Page_AfterInitialize @1-379D319D
    return $Page_AfterInitialize;
}
//End Close Page_AfterInitialize

?>