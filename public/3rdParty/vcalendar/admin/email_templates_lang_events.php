<?php
//BindEvents Method @1-01E13873
function BindEvents()
{
    global $email_templates_lang;
    global $CCSEvents;
    $email_templates_lang->ds->CCSEvents["AfterExecuteUpdate"] = "email_templates_lang_ds_AfterExecuteUpdate";
    $CCSEvents["AfterInitialize"] = "Page_AfterInitialize";
}
//End BindEvents Method

//email_templates_lang_ds_AfterExecuteUpdate @29-9604D72F
function email_templates_lang_ds_AfterExecuteUpdate(& $sender)
{
    $email_templates_lang_ds_AfterExecuteUpdate = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $email_templates_lang; //Compatibility
//End email_templates_lang_ds_AfterExecuteUpdate

//Custom Code @40-2A29BDB7
// -------------------------
global $calendar_config;

	if ($Container->language_id->GetValue() == $calendar_config["default_language"]) {
		$db = new clsDBcalendar();
		$SQL = "UPDATE email_templates ".
				"SET email_template_desc = ".$db->ToSQL($Container->email_template_desc->GetValue(), ccsMemo).
				", email_template_subject = ".$db->ToSQL($Container->email_template_subject->GetValue(), ccsText).
				", email_template_body = ".$db->ToSQL($Container->email_template_body->GetValue(), ccsText).
				"WHERE email_template_id = ".$db->ToSQL(CCGetFromGet("email_template_id",""),ccsInteger);
		$db->query($SQL);
		$db->close();
	}

// -------------------------
//End Custom Code

//Close email_templates_lang_ds_AfterExecuteUpdate @29-B28D8FEE
    return $email_templates_lang_ds_AfterExecuteUpdate;
}
//End Close email_templates_lang_ds_AfterExecuteUpdate

//Page_AfterInitialize @1-E14BAE2F
function Page_AfterInitialize(& $sender)
{
    $Page_AfterInitialize = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $email_templates_lang; //Compatibility
//End Page_AfterInitialize

//Custom Code @41-2A29BDB7
// -------------------------

	if (!CCStrLen(CCGetFromGet("email_template_id","")))
		$Container->JavaScriptLabel->SetValue("<script language='JavaScript'>window.opener.location.reload();self.close()</script>");

// -------------------------
//End Custom Code

//Close Page_AfterInitialize @1-379D319D
    return $Page_AfterInitialize;
}
//End Close Page_AfterInitialize
?>
