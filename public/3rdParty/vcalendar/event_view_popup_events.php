<?php
//BindEvents Method @1-4330DB5D
function BindEvents()
{
    global $event_name;
    global $eventGrid;
    global $CCSEvents;
    $event_name->CCSEvents["BeforeShow"] = "event_name_BeforeShow";
    $eventGrid->edit->CCSEvents["BeforeShow"] = "eventGrid_edit_BeforeShow";
    $eventGrid->CCSEvents["BeforeShowRow"] = "eventGrid_BeforeShowRow";
    $CCSEvents["AfterInitialize"] = "Page_AfterInitialize";
}
//End BindEvents Method

//event_name_BeforeShow @207-7886B869
function event_name_BeforeShow(& $sender)
{
    $event_name_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $event_name; //Compatibility
//End event_name_BeforeShow

//Custom Code @208-2A29BDB7
// -------------------------	
	global $DBcalendar;
	
    $Component->SetValue(CCDLookUp("event_title", "events", "event_id = " . $DBcalendar->ToSQL(CCGetFromGet("event_id", "0"), ccsInteger), $DBcalendar));
// -------------------------
//End Custom Code

//Close event_name_BeforeShow @207-C68B5050
    return $event_name_BeforeShow;
}
//End Close event_name_BeforeShow

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

//Page_AfterInitialize @1-19D9068A
function Page_AfterInitialize(& $sender)
{
    $Page_AfterInitialize = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $event_view_popup; //Compatibility
//End Page_AfterInitialize

//Custom Code @209-2A29BDB7
// -------------------------
	if (!ReadAllowed(CCGetFromGet("event_id"))) {
		echo "<script language=\"javascript\">self.close()</script>";
		exit;
	}
// -------------------------
//End Custom Code

//Close Page_AfterInitialize @1-379D319D
    return $Page_AfterInitialize;
}
//End Close Page_AfterInitialize

?>
