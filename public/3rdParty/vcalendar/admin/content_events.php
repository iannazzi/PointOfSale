<?php
//BindEvents Method @1-D441D4F3
function BindEvents()
{
    global $contents_maint;
    $contents_maint->CCSEvents["BeforeShow"] = "contents_maint_BeforeShow";
    $contents_maint->CCSEvents["AfterUpdate"] = "contents_maint_AfterUpdate";
}
//End BindEvents Method

//contents_maint_BeforeShow @13-039BD2C2
function contents_maint_BeforeShow(& $sender)
{
    $contents_maint_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $contents_maint; //Compatibility
//End contents_maint_BeforeShow

//Hide-Show Component @37-9819841A
    $Parameter1 = CCGetFromGet("content_id", "");
    $Parameter2 = 1;
    if (((is_array($Parameter1) || strlen($Parameter1)) && (is_array($Parameter2) || strlen($Parameter2))) && 0 >  CCCompareValues($Parameter1, $Parameter2, ccsInteger))
        $Component->Visible = false;
//End Hide-Show Component

//Close contents_maint_BeforeShow @13-51EDE811
    return $contents_maint_BeforeShow;
}
//End Close contents_maint_BeforeShow

//contents_maint_AfterUpdate @13-2E8E5832
function contents_maint_AfterUpdate(& $sender)
{
    $contents_maint_AfterUpdate = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $contents_maint; //Compatibility
//End contents_maint_AfterUpdate

//Custom Code @55-2A29BDB7
// -------------------------
	global $calendar_config;
	if (CCGetSession("locale") == $calendar_config["default_language"]) {
		$db = new clsDBcalendar();
		$SQL = "UPDATE contents ".
			"SET content_desc = ".$db->ToSQL($Container->content_desc->GetValue(), ccsText).
				", content_value = ".$db->ToSQL($Container->content_value->GetValue(), ccsText).
			" WHERE content_id = ".$db->ToSQL($Container->content_id->GetValue(),ccsInteger);
		$db->query($SQL);
		$db->close();
	}		
// -------------------------
//End Custom Code

//Close contents_maint_AfterUpdate @13-DC65365A
    return $contents_maint_AfterUpdate;
}
//End Close contents_maint_AfterUpdate
?>