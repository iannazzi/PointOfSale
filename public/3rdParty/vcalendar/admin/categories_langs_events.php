<?php
//BindEvents Method @1-7E66A822
function BindEvents()
{
    global $categories_langs;
    global $CCSEvents;
    $categories_langs->ds->CCSEvents["AfterExecuteUpdate"] = "categories_langs_ds_AfterExecuteUpdate";
    $CCSEvents["AfterInitialize"] = "Page_AfterInitialize";
}
//End BindEvents Method

//categories_langs_ds_AfterExecuteUpdate @3-B9EA5ACC
function categories_langs_ds_AfterExecuteUpdate(& $sender)
{
    $categories_langs_ds_AfterExecuteUpdate = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $categories_langs; //Compatibility
//End categories_langs_ds_AfterExecuteUpdate

//Custom Code @9-34C77522
// -------------------------
global $calendar_config;

	if ($Container->language_id->GetValue() == $calendar_config["default_language"]) {
		$db = new clsDBcalendar();
		$SQL = "UPDATE categories ".
			"SET category_name = ".$db->ToSQL($Container->category_name->GetValue(),ccsText).
			"WHERE category_id = ".$db->ToSQL(CCGetFromGet("category_id",""),ccsInteger);
		$db->query($SQL);
		$db->close();
	}
// -------------------------
//End Custom Code

//Close categories_langs_ds_AfterExecuteUpdate @3-C234BEC8
    return $categories_langs_ds_AfterExecuteUpdate;
}
//End Close categories_langs_ds_AfterExecuteUpdate

//Page_AfterInitialize @1-9A167ED6
function Page_AfterInitialize(& $sender)
{
    $Page_AfterInitialize = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $categories_langs; //Compatibility
//End Page_AfterInitialize

//Custom Code @20-2A29BDB7
// -------------------------
	if (!CCStrLen(CCGetFromGet("category_id",""))) 
		$Container->JavaScriptLabel->SetValue("<script language='JavaScript'>window.opener.location.reload();self.close();</script>");
// -------------------------
//End Custom Code

//Close Page_AfterInitialize @1-379D319D
    return $Page_AfterInitialize;
}
//End Close Page_AfterInitialize
?>
