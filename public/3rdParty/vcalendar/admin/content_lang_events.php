<?php
//BindEvents Method @1-A6EFF099
function BindEvents()
{
    global $contents_lang;
    global $CCSEvents;
    $contents_lang->ds->CCSEvents["AfterExecuteUpdate"] = "contents_lang_ds_AfterExecuteUpdate";
    $CCSEvents["AfterInitialize"] = "Page_AfterInitialize";
}
//End BindEvents Method

//contents_lang_ds_AfterExecuteUpdate @29-5BF005A2
function contents_lang_ds_AfterExecuteUpdate(& $sender)
{
    $contents_lang_ds_AfterExecuteUpdate = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $contents_lang; //Compatibility
//End contents_lang_ds_AfterExecuteUpdate

//Custom Code @40-2A29BDB7
// -------------------------
	global $calendar_config;
	if ($Container->language_id->GetValue() == $calendar_config["default_language"]) {
		$db = new clsDBcalendar();
		$SQL = "UPDATE contents ".
			"SET content_desc = ".$db->ToSQL($Container->content_desc->GetValue(), ccsText).
				", content_value = ".$db->ToSQL($Container->content_value->GetValue(), ccsText).
			" WHERE content_id = ".$db->ToSQL(CCGetFromGet("content_id",""),ccsInteger);
		$db->query($SQL);
		$db->close();
	}
// -------------------------
//End Custom Code

//Close contents_lang_ds_AfterExecuteUpdate @29-6BDED480
    return $contents_lang_ds_AfterExecuteUpdate;
}
//End Close contents_lang_ds_AfterExecuteUpdate

//Page_AfterInitialize @1-BC2C1E3A
function Page_AfterInitialize(& $sender)
{
    $Page_AfterInitialize = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $content_lang; //Compatibility
//End Page_AfterInitialize

//Custom Code @41-2A29BDB7
// -------------------------
    if (!CCStrLen(CCGetFromGet("content_id","")))
		$Container->close->SetValue("<script language='JavaScript'>window.opener.location.reload();self.close()</script>");
// -------------------------
//End Custom Code

//Close Page_AfterInitialize @1-379D319D
    return $Page_AfterInitialize;
}
//End Close Page_AfterInitialize
?>
