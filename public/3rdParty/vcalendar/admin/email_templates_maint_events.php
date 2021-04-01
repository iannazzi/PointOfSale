<?php
//BindEvents Method @1-D5AD5594
function BindEvents()
{
    global $email_templates;
    $email_templates->CCSEvents["AfterUpdate"] = "email_templates_AfterUpdate";
}
//End BindEvents Method

//email_templates_AfterUpdate @2-8897D7D9
function email_templates_AfterUpdate(& $sender)
{
    $email_templates_AfterUpdate = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $email_templates; //Compatibility
//End email_templates_AfterUpdate

//Custom Code @16-2A29BDB7
// -------------------------
global $calendar_config;
	
	$db = new clsDBcalendar();
	if (!strcmp(CCGetSession("locale"),$calendar_config["default_language"])) {
		$SQL = "UPDATE email_templates ".
			"SET email_template_desc = ".$db->ToSQL($Container->email_template_desc->GetValue(),ccsText).
				", email_template_subject = ".$db->ToSQL($Container->email_template_subject->GetValue(),ccsText).
				", email_template_body = ".$db->ToSQL($Container->email_template_body->GetValue(),ccsText).
			"WHERE email_template_id = ".$db->ToSQL(CCGetFromGet("email_template_id",""),ccsInteger);
		$db->query($SQL);
	}
	
	$SQL = "UPDATE email_templates_lang ".
		"SET email_template_desc = ".$db->ToSQL($Container->email_template_desc->GetValue(),ccsText).
			", email_template_subject = ".$db->ToSQL($Container->email_template_subject->GetValue(),ccsText).
			", email_template_body = ".$db->ToSQL($Container->email_template_body->GetValue(),ccsText).
		"WHERE email_templates_lang.language_id = ".$db->ToSQL(CCGetSession("locale"), ccsText).
			" AND email_templates_lang.email_template_id = ".$db->ToSQL(CCGetFromGet("email_template_id",""),ccsInteger);
	$db->query($SQL);
	$db->close();
// -------------------------
//End Custom Code

//Close email_templates_AfterUpdate @2-469150F6
    return $email_templates_AfterUpdate;
}
//End Close email_templates_AfterUpdate

?>
