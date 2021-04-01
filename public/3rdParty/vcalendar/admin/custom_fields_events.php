<?php
//BindEvents Method @1-874A4A98
function BindEvents()
{
    global $custom_fields_maint;
    $custom_fields_maint->CCSEvents["BeforeShow"] = "custom_fields_maint_BeforeShow";
    $custom_fields_maint->ds->CCSEvents["BeforeExecuteUpdate"] = "custom_fields_maint_ds_BeforeExecuteUpdate";
}
//End BindEvents Method

//custom_fields_maint_BeforeShow @16-7D768FA7
function custom_fields_maint_BeforeShow(& $sender)
{
    $custom_fields_maint_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $custom_fields_maint; //Compatibility
//End custom_fields_maint_BeforeShow

//Custom Code @50-2A29BDB7
// -------------------------
    if (!CCGetFromGet("field_id"))
        $Component->Visible = false;
// -------------------------
//End Custom Code

//Close custom_fields_maint_BeforeShow @16-2ACF8C14
    return $custom_fields_maint_BeforeShow;
}
//End Close custom_fields_maint_BeforeShow

//custom_fields_maint_ds_BeforeExecuteUpdate @16-A7BCD3D4
function custom_fields_maint_ds_BeforeExecuteUpdate(& $sender)
{
    $custom_fields_maint_ds_BeforeExecuteUpdate = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $custom_fields_maint; //Compatibility
//End custom_fields_maint_ds_BeforeExecuteUpdate

//Custom Code @23-8ED4C4CE
// -------------------------
	global $calendar_config;

	$db = new clsDBcalendar();
	$SQL = "UPDATE custom_fields SET field_is_active = ".$db->ToSQL($Container->field_is_active->GetValue(),ccsInteger);
	if (CCGetSession("locale") == $calendar_config["default_language"])
		$SQL .= ", field_label = ".$db->ToSQL($Container->field_label->GetValue(),ccsText);
	$SQL .= " WHERE field_id = ".$db->ToSQL(CCGetFromGet("field_id",""), ccsInteger);
	$db->query($SQL);
	$db->close();
// -------------------------
//End Custom Code

//Close custom_fields_maint_ds_BeforeExecuteUpdate @16-69D89D59
    return $custom_fields_maint_ds_BeforeExecuteUpdate;
}
//End Close custom_fields_maint_ds_BeforeExecuteUpdate
?>
