<?php
//BindEvents Method @1-7DE46A64
function BindEvents()
{
    global $categories;
    global $categories_maint;
    $categories->CCSEvents["BeforeShowRow"] = "categories_BeforeShowRow";
    $categories_maint->CCSEvents["AfterUpdate"] = "categories_maint_AfterUpdate";
    $categories_maint->CCSEvents["BeforeDelete"] = "categories_maint_BeforeDelete";
    $categories_maint->ds->CCSEvents["AfterExecuteInsert"] = "categories_maint_ds_AfterExecuteInsert";
}
//End BindEvents Method

//categories_BeforeShowRow @33-CC56E037
function categories_BeforeShowRow(& $sender)
{
    $categories_BeforeShowRow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $categories; //Compatibility
//End categories_BeforeShowRow

//Custom Code @56-2A29BDB7
// -------------------------
	if (CCStrLen($Container->category_image->GetValue()))
		$Container->category_image->Visible = true;
	else
		$Container->category_image->Visible = false;
// -------------------------
//End Custom Code

//Close categories_BeforeShowRow @33-8EEB5BAD
    return $categories_BeforeShowRow;
}
//End Close categories_BeforeShowRow

//categories_maint_AfterUpdate @45-C5B81C79
function categories_maint_AfterUpdate(& $sender)
{
    $categories_maint_AfterUpdate = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $categories_maint; //Compatibility
//End categories_maint_AfterUpdate

//Custom Code @52-2A29BDB7
// -------------------------
	global $calendar_config;
	$db = new clsDBcalendar();
	if (CCGetSession("locale") == $calendar_config["default_language"]) {
		$SQL = "UPDATE categories ".
			" SET category_name = ".$db->ToSQL($Container->category_name->GetValue(), ccsText).
			" WHERE category_id = ".$db->ToSQL(CCGetFromGet("category_id"),ccsInteger);
		$db->query($SQL);
	}
	$SQL = "UPDATE categories_langs ".
		" SET category_name= ".$db->ToSQL($Container->category_name->GetValue(), ccsText).
		" WHERE language_id= ".$db->ToSQL(CCGetSession("locale"), ccsText).
		" AND category_id=".$db->ToSQL(CCGetFromGet("category_id"),ccsInteger);
	$db->query($SQL);
	$db->close();
// -------------------------
//End Custom Code

//Close categories_maint_AfterUpdate @45-3456B985
    return $categories_maint_AfterUpdate;
}
//End Close categories_maint_AfterUpdate

//categories_maint_BeforeDelete @45-3500BC32
function categories_maint_BeforeDelete(& $sender)
{
    $categories_maint_BeforeDelete = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $categories_maint; //Compatibility
//End categories_maint_BeforeDelete

//Custom Code @92-2A29BDB7
// -------------------------

	$db = new clsDBcalendar();
	//Delete the related langs
	$SQL = "DELETE FROM categories_langs WHERE category_id=".$db->ToSQL(CCGetFromGet("category_id",""),ccsInteger);
	$db->query($SQL);

	//Update the the related events
	$SQL = "UPDATE events SET category_id = NULL WHERE category_id=".$db->ToSQL(CCGetFromGet("category_id",""),ccsInteger);
	$db->query($SQL);
	$db->close();

// -------------------------
//End Custom Code

//Close categories_maint_BeforeDelete @45-E20C4126
    return $categories_maint_BeforeDelete;
}
//End Close categories_maint_BeforeDelete

//categories_maint_ds_AfterExecuteInsert @45-FE420631
function categories_maint_ds_AfterExecuteInsert(& $sender)
{
    $categories_maint_ds_AfterExecuteInsert = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $categories_maint; //Compatibility
//End categories_maint_ds_AfterExecuteInsert

//Custom Code @93-2A29BDB7
// -------------------------
	global $CCSLocales;
	$category_id = mysql_insert_id();
	$db = new clsDBcalendar();
	$LanguageArray=$CCSLocales->Locales;
	foreach($LanguageArray as $LangKey) {
	  	$SQL = "INSERT INTO categories_langs ( ".
					"language_id, ".
					"category_id, ".
					"category_name ".
				") VALUES ( ".
					$db->ToSQL($LangKey->Name, ccsText).", ".
					$db->ToSQL($category_id,ccsInteger).", ".
					$db->ToSQL($Container->category_name->GetValue(),ccsText).
				")";
		$db->query($SQL);
	}
	$db->close();
// -------------------------
//End Custom Code

//Close categories_maint_ds_AfterExecuteInsert @45-C0AB9766
    return $categories_maint_ds_AfterExecuteInsert;
}
//End Close categories_maint_ds_AfterExecuteInsert
?>
