<?php
//BindEvents Method @1-CB4AD2B0
function BindEvents()
{
    global $custom_fields_langs;
    global $CCSEvents;
    $custom_fields_langs->ds->CCSEvents["AfterExecuteUpdate"] = "custom_fields_langs_ds_AfterExecuteUpdate";
    $CCSEvents["AfterInitialize"] = "Page_AfterInitialize";
}
//End BindEvents Method

//custom_fields_langs_ds_AfterExecuteUpdate @5-B3A87ACE
function custom_fields_langs_ds_AfterExecuteUpdate(& $sender)
{
    $custom_fields_langs_ds_AfterExecuteUpdate = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $custom_fields_langs; //Compatibility
//End custom_fields_langs_ds_AfterExecuteUpdate

//Custom Code @14-96722D9D
// -------------------------
global $calendar_config;
	
	if ($Container->language_id->GetValue() == $calendar_config["default_language"]) {
		$db = new clsDBcalendar();
		$SQL = "UPDATE custom_fields ".
			"SET field_label = ".$db->ToSQL($Container->field_translation->GetValue(), ccsText).
			" WHERE field_id = ".$db->ToSQL(CCGetFromGet("field_id",""), ccsInteger);
		$db->query($SQL);
		$db->close();
	}

// -------------------------
//End Custom Code

//Close custom_fields_langs_ds_AfterExecuteUpdate @5-1AA936D7
    return $custom_fields_langs_ds_AfterExecuteUpdate;
}
//End Close custom_fields_langs_ds_AfterExecuteUpdate

//Page_AfterInitialize @1-F6EDAECF
function Page_AfterInitialize(& $sender)
{
    $Page_AfterInitialize = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $custom_fields_lang; //Compatibility
//End Page_AfterInitialize

//Custom Code @24-2A29BDB7
// -------------------------
	global $JavaScriptLabel;

	if (!CCStrLen(CCGetFromGet("field_id","")))
		$JavaScriptLabel->SetValue("<script language='JavaScript'>window.opener.location.reload();self.close()</script>");
// -------------------------
//End Custom Code

//Close Page_AfterInitialize @1-379D319D
    return $Page_AfterInitialize;
}
//End Close Page_AfterInitialize
?>
