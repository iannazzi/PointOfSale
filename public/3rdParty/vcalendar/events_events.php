<?php
//BindEvents Method @1-64D6D6DF
function BindEvents()
{
    global $events_rec;
    global $CCSEvents;
    $events_rec->event_time_hrs->CCSEvents["BeforeShow"] = "events_rec_event_time_hrs_BeforeShow";
    $events_rec->time_hrs_end->CCSEvents["BeforeShow"] = "events_rec_time_hrs_end_BeforeShow";
    $events_rec->Button_Delete->CCSEvents["BeforeShow"] = "events_rec_Button_Delete_BeforeShow";
    $events_rec->Button_Cancel->CCSEvents["OnClick"] = "events_rec_Button_Cancel_OnClick";
    $events_rec->CCSEvents["BeforeShow"] = "events_rec_BeforeShow";
    $events_rec->CCSEvents["BeforeInsert"] = "events_rec_BeforeInsert";
    $events_rec->CCSEvents["BeforeUpdate"] = "events_rec_BeforeUpdate";
    $events_rec->CCSEvents["OnValidate"] = "events_rec_OnValidate";
    $events_rec->ds->CCSEvents["AfterExecuteInsert"] = "events_rec_ds_AfterExecuteInsert";
    $events_rec->ds->CCSEvents["AfterExecuteUpdate"] = "events_rec_ds_AfterExecuteUpdate";
    $events_rec->ds->CCSEvents["AfterExecuteDelete"] = "events_rec_ds_AfterExecuteDelete";
    $CCSEvents["AfterInitialize"] = "Page_AfterInitialize";
}
//End BindEvents Method

//events_rec_event_time_hrs_BeforeShow @25-CD213FA2
function events_rec_event_time_hrs_BeforeShow(& $sender)
{
    $events_rec_event_time_hrs_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $events_rec; //Compatibility
//End events_rec_event_time_hrs_BeforeShow

//Custom Code @102-2A29BDB7
// -------------------------
global $calendar_config;
global $CCSLocales;

	if ($calendar_config["time_format"] == "2" || ($calendar_config["time_format"] == "1" && Count($CCSLocales->GetFormatInfo("ShortTime")) == 3)) {
		$arrValues = array();
		for($j = 0; $j < 24; $j++) {
			$dig = sprintf("%02d",$j);
			$arrValues[] = array($dig, $dig);
		}
		$Component->Values = $arrValues;
	} 

// -------------------------
//End Custom Code

//Close events_rec_event_time_hrs_BeforeShow @25-DA1330E0
    return $events_rec_event_time_hrs_BeforeShow;
}
//End Close events_rec_event_time_hrs_BeforeShow

//events_rec_time_hrs_end_BeforeShow @27-01CCA9DD
function events_rec_time_hrs_end_BeforeShow(& $sender)
{
    $events_rec_time_hrs_end_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $events_rec; //Compatibility
//End events_rec_time_hrs_end_BeforeShow

//Custom Code @101-2A29BDB7
// -------------------------
global $calendar_config;
global $CCSLocales;

	if ($calendar_config["time_format"] == "2" || ($calendar_config["time_format"] == "1" && Count($CCSLocales->GetFormatInfo("ShortTime")) == 3)) {
		$arrValues = array();
		for($j = 0; $j < 24; $j++) {
			$dig = sprintf("%02d",$j);
			$arrValues[] = array($dig, $dig);
		}
		$Component->Values = $arrValues;
	} 
// -------------------------
//End Custom Code

//Close events_rec_time_hrs_end_BeforeShow @27-888586EA
    return $events_rec_time_hrs_end_BeforeShow;
}
//End Close events_rec_time_hrs_end_BeforeShow

//events_rec_Button_Delete_BeforeShow @8-561751A8
function events_rec_Button_Delete_BeforeShow(& $sender)
{
    $events_rec_Button_Delete_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $events_rec; //Compatibility
//End events_rec_Button_Delete_BeforeShow

//Custom Code @34-2A29BDB7
// -------------------------
	if (!DeleteAllowed(CCGetFromGet("event_id","")))
		$Component->Visible = false;
// -------------------------
//End Custom Code

//Close events_rec_Button_Delete_BeforeShow @8-707150D7
    return $events_rec_Button_Delete_BeforeShow;
}
//End Close events_rec_Button_Delete_BeforeShow

//events_rec_Button_Cancel_OnClick @81-072FE21C
function events_rec_Button_Cancel_OnClick(& $sender)
{
    $events_rec_Button_Cancel_OnClick = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $events_rec; //Compatibility
//End events_rec_Button_Cancel_OnClick

//Custom Code @82-2A29BDB7
// -------------------------
global $Redirect;
	$Redirect = CCGetFromGet("ret_link", $Redirect);
// -------------------------
//End Custom Code

//Close events_rec_Button_Cancel_OnClick @81-BCA16872
    return $events_rec_Button_Cancel_OnClick;
}
//End Close events_rec_Button_Cancel_OnClick

//events_rec_BeforeShow @5-F2702AF9
function events_rec_BeforeShow(& $sender)
{
    $events_rec_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $events_rec; //Compatibility
//End events_rec_BeforeShow

//Custom Code @19-2A29BDB7
// -------------------------
global $calendar_config;
global $Redirect;
global $DBcalendar;

	$EventID = CCGetFromGet("event_id");

	if (!$events_rec->FormSubmitted) {
		$str = $events_rec->event_time->GetValue();
		if ($str) {
			$events_rec->allday->SetValue("0");
			$events_rec->event_time_hrs->SetValue(substr("0".$str[ccsHour],-2));
			$events_rec->event_time_mns->SetValue(substr("0".$str[ccsMinute],-2));
		} else
			$events_rec->allday->SetValue("1");

		$str_end = $events_rec->event_time_end->GetValue();
		if ($str_end) {
			$events_rec->time_hrs_end->SetValue(substr("0".$str_end[ccsHour],-2));
			$events_rec->time_mns_end->SetValue(substr("0".$str_end[ccsMinute],-2));
		}

		if (!$EventID)
			$Component->category_id->SetValue(CCGetSession("category"));

		if (!$Component->event_URL->GetValue())
			$Component->event_URL->SetValue("http://");


	}

	processCustomFields($events_rec);

	if ($EventID) {
		$Component->RepeatEvent->Visible = False;
		if (!strlen($Component->event_parent_id->GetValue())) {
			$EventID = CCDLookUp("count(event_id)", "events", "event_parent_id = " . $EventID, $DBcalendar);
			if (!$EventID)
				$Component->PanelRecurrentSubmit->Visible = False;
		}
	} else
		$Component->PanelRecurrentSubmit->Visible = False;
// -------------------------
//End Custom Code

//Close events_rec_BeforeShow @5-C1C4FCF3
    return $events_rec_BeforeShow;
}
//End Close events_rec_BeforeShow

//events_rec_BeforeInsert @5-B9547AFB
function events_rec_BeforeInsert(& $sender)
{
    $events_rec_BeforeInsert = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $events_rec; //Compatibility
//End events_rec_BeforeInsert

//Custom Code @20-2A29BDB7
// -------------------------
	if ($events_rec->allday->GetValue() == "1") {
		$events_rec->event_time->SetValue("");
		$events_rec->event_time_end->SetValue("");
	}
	else {
		$event_time[ccsHour]   = $events_rec->event_time_hrs->GetValue();
		$event_time[ccsMinute] = $events_rec->event_time_mns->GetValue();
		$event_time[ccsSecond] = 0;
		$events_rec->event_time->SetValue($event_time);

		$event_time[ccsHour]   = $events_rec->time_hrs_end->GetValue();
		$event_time[ccsMinute] = $events_rec->time_mns_end->GetValue();
		$events_rec->event_time_end->SetValue($event_time);
	}

	if (trim($Component->event_URL->GetValue()) == "http://")
		$Component->event_URL->SetValue("");

	if (CCGetUserID())
		$events_rec->user_id->SetValue(CCGetUserID());
	else 
		$events_rec->user_id->SetValue(0);
// -------------------------
//End Custom Code

//Close events_rec_BeforeInsert @5-D1170F28
    return $events_rec_BeforeInsert;
}
//End Close events_rec_BeforeInsert

//events_rec_BeforeUpdate @5-6C505E07
function events_rec_BeforeUpdate(& $sender)
{
    $events_rec_BeforeUpdate = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $events_rec; //Compatibility
//End events_rec_BeforeUpdate

//Custom Code @32-2A29BDB7
// -------------------------
global $DBcalendar;

	if ($events_rec->allday->GetValue() == "1") {
		$events_rec->event_time->SetValue("");
		$events_rec->event_time_end->SetValue("");
	}
	else {
		$event_time[ccsHour]   = $events_rec->event_time_hrs->GetValue();
		$event_time[ccsMinute] = $events_rec->event_time_mns->GetValue();
		$event_time[ccsSecond] = 0;
		$events_rec->event_time->SetValue($event_time);

		$event_time[ccsHour]   = $events_rec->time_hrs_end->GetValue();
		$event_time[ccsMinute] = $events_rec->time_mns_end->GetValue();
		$events_rec->event_time_end->SetValue($event_time);
	}

	if (trim($Component->event_URL->GetValue()) == "http://")
		$Component->event_URL->SetValue("");

	$events_rec->user_id->SetValue(CCDLookUp("user_id","events","event_id=".$DBcalendar->ToSQL(CCGetFromGet("event_id", "0"), ccsInteger),$DBcalendar));

	$Component->event_desc->SetValue(trim($Component->event_desc->GetValue()));
// -------------------------
//End Custom Code

//Close events_rec_BeforeUpdate @5-1E3ECEA7
    return $events_rec_BeforeUpdate;
}
//End Close events_rec_BeforeUpdate

//events_rec_OnValidate @5-D1AC0BB7
function events_rec_OnValidate(& $sender)
{
    $events_rec_OnValidate = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $events_rec; //Compatibility
//End events_rec_OnValidate

//Custom Code @93-2A29BDB7
// -------------------------
global $CCSLocales;

	if ($Component->RepeatEvent->GetValue() == 1) {
		if (!strlen($Component->RepeatNum->GetValue()))
			$Component->Errors->addError($CCSLocales->GetText("CCS_RequiredField", $Component->RepeatNum->Caption));
		if (!strlen($Component->event_todate->GetText()))
			$Component->Errors->addError($CCSLocales->GetText("CCS_RequiredField", $Component->event_todate->Caption));
	}

// -------------------------
//End Custom Code

//Close events_rec_OnValidate @5-FE3F987A
    return $events_rec_OnValidate;
}
//End Close events_rec_OnValidate

//events_rec_ds_AfterExecuteInsert @5-28EC0984
function events_rec_ds_AfterExecuteInsert(& $sender)
{
    $events_rec_ds_AfterExecuteInsert = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $events_rec; //Compatibility
//End events_rec_ds_AfterExecuteInsert

//Custom Code @92-2A29BDB7
// -------------------------
global $Redirect;
global $DBcalendar;

	if ($Component->RepeatEvent->GetValue() == 1) {
		$EventID = CCDLookUp("MAX(event_id)", "events", "", $DBcalendar);
		$SQL = "SELECT * FROM events WHERE event_id = " . $DBcalendar->ToSQL($EventID, ccsInteger);
		$DBcalendar->query($SQL);

		$SQL = "INSERT INTO events (event_parent_id, event_date ";
		$SQL_end = ") VALUES (" . $DBcalendar->ToSQL($EventID, ccsInteger) . ",  {date}";

		$FieldsArr = array("user_id", "category_id", "event_title", "event_desc", "event_time", "event_time_end", "event_date_add", 
						  "event_user_add", "event_is_public", "event_location", "event_cost", "event_url", "custom_TextBox1", 
						  "custom_TextBox2", "custom_TextBox3", "custom_TextArea1", "custom_TextArea2", "custom_TextArea3", 
						  "custom_CheckBox1", "custom_CheckBox2", "custom_CheckBox3");

		$FieldType = array(ccsInteger, ccsInteger, ccsText, ccsText, ccsDate, ccsDate, ccsDate, 
							ccsDate, ccsInteger, ccsText, ccsText, ccsText, ccsText, 
							ccsText, ccsText, ccsText, ccsText, ccsText, 
							ccsInteger, ccsInteger, ccsInteger);

		$DBcalendar->next_record();	
		for($i=0; $i<Count($FieldsArr); $i++) {
			$SQL = $SQL . ", " . $FieldsArr[$i];
			$SQL_end = $SQL_end . ", " . $DBcalendar->ToSQL($DBcalendar->f($FieldsArr[$i]),$FieldType[$i]);
		}

		$SQL = $SQL . $SQL_end . ")";

		$RepeatNum = $Component->RepeatNum->GetValue();
		$DateStart = $Component->event_date->GetValue();
		$RepeatType = $Component->RepeatType->GetValue();
		$k = 1;

		switch($RepeatType) {
			case 0  : $Interval = "day";   $k=1; break;
			case 8  : $Interval = "day";   $k=7; break;
			case 30 : $Interval = "month"; $k=1; break;
			case 1 : case 2 : case 3 : case 4 : case 5 : case 6 : case 7 : $Interval = "day"; $k=7;
				$DateStart = CCDateAdd($DateStart, $RepeatType - CCDayOfWeek($DateStart) + ($RepeatType <= CCDayOfWeek($DateStart)? 7 : 0) - $RepeatNum*7 . "days");
		}

		$DateStart = CCDateAdd($DateStart, $RepeatNum * $k . $Interval);
		$DateFinish = $Component->event_todate->GetValue();
		while (CCCompareValues($DateStart, $DateFinish, ccsDate) <= 0) {
			$DBcalendar->query(str_replace("{date}", $DBcalendar->ToSQL($DateStart, ccsDate), $SQL));
			$DateStart = CCDateAdd($DateStart, $RepeatNum * $k . $Interval);
		}
	}

	CCSetSession("category", "");

	if (strlen($ret_link = CCGetFromGet("ret_link"))) {
		$ret_link = substr($ret_link, 0, strpos($ret_link, "?"));
		if ($ret_link == "index.php")
			$Redirect = "index.php?cal_monthDate=" . CCFormatDate($Container->event_date->GetValue(), array("yyyy","-","mm"));
		else
			$Redirect = $ret_link . "?day=" . CCFormatDate($Container->event_date->GetValue(), array("yyyy","-","mm","-","dd"));
	}

// -------------------------
//End Custom Code

//Close events_rec_ds_AfterExecuteInsert @5-BC9E8B2F
    return $events_rec_ds_AfterExecuteInsert;
}
//End Close events_rec_ds_AfterExecuteInsert

//events_rec_ds_AfterExecuteUpdate @5-3A837E0F
function events_rec_ds_AfterExecuteUpdate(& $sender)
{
    $events_rec_ds_AfterExecuteUpdate = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $events_rec; //Compatibility
//End events_rec_ds_AfterExecuteUpdate

//Custom Code @99-2A29BDB7
// -------------------------
global $Redirect;
global $DBcalendar;

	if ($Component->RecurrentApply->GetValue()) {

		$EventID = CCGetFromGet("event_id");
		$SQL = "SELECT * FROM events WHERE event_id = " . $DBcalendar->ToSQL($EventID, ccsInteger);
		$DBcalendar->query($SQL);
		$DBcalendar->next_record();

		if (strlen($Component->event_parent_id->GetValue()))
			$EventID = $Component->event_parent_id->GetValue();

		$SQL = "UPDATE events SET ";

		$FieldsArr = array("user_id", "category_id", "event_title", "event_desc", "event_time", "event_time_end", "event_date_add", 
						  "event_user_add", "event_is_public", "event_location", "event_cost", "event_url", "custom_TextBox1", 
						  "custom_TextBox2", "custom_TextBox3", "custom_TextArea1", "custom_TextArea2", "custom_TextArea3", 
						  "custom_CheckBox1", "custom_CheckBox2", "custom_CheckBox3");

		$FieldType = array(ccsInteger, ccsInteger, ccsText, ccsText, ccsDate, ccsDate, ccsDate, 
							ccsDate, ccsInteger, ccsText, ccsText, ccsText, ccsText, 
							ccsText, ccsText, ccsText, ccsText, ccsText, 
							ccsInteger, ccsInteger, ccsInteger);

		for($i=0; $i<Count($FieldsArr); $i++)
			$SQL = $SQL . $FieldsArr[$i] . " = " . $DBcalendar->ToSQL($DBcalendar->f($FieldsArr[$i]),$FieldType[$i]) . ", ";

		$SQL = substr($SQL, 0, - 2) . " WHERE event_id = " . $DBcalendar->ToSQL($EventID, ccsInteger) . " OR event_parent_id = " . $DBcalendar->ToSQL($EventID, ccsInteger);

		$DBcalendar->query($SQL);
	}

	CCSetSession("category", "");

	if (strlen($ret_link = CCGetFromGet("ret_link"))) {
		$file_name = substr($ret_link, 0, strpos($ret_link, "?"));
		switch ($file_name) {
			case "index.php" : $Redirect = "index.php?cal_monthDate=" . CCFormatDate($Container->event_date->GetValue(), array("yyyy","-","mm")); break;
			case "day.php" : case "week.php" : $Redirect = $file_name . "?day=" . CCFormatDate($Container->event_date->GetValue(), array("yyyy","-","mm","-","dd")); break;
			default : $Redirect = $ret_link;
		}
	}
// -------------------------
//End Custom Code

//Close events_rec_ds_AfterExecuteUpdate @5-73B74AA0
    return $events_rec_ds_AfterExecuteUpdate;
}
//End Close events_rec_ds_AfterExecuteUpdate

//events_rec_ds_AfterExecuteDelete @5-DFA0D70E
function events_rec_ds_AfterExecuteDelete(& $sender)
{
    $events_rec_ds_AfterExecuteDelete = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $events_rec; //Compatibility
//End events_rec_ds_AfterExecuteDelete

//Custom Code @100-2A29BDB7
// -------------------------
global $DBcalendar;

	if ($Component->RecurrentApply->GetValue()) {

		if (strlen($Component->event_parent_id->GetValue()))
			$EventID = $events_rec->event_parent_id->GetValue();
		else
			$EventID = CCGetFromGet("event_id");

		$SQL = "DELETE FROM events WHERE event_id = " . $EventID . " OR event_parent_id = " . $EventID;
		$DBcalendar->query($SQL);
	}

// -------------------------
//End Custom Code

//Close events_rec_ds_AfterExecuteDelete @5-EF93ECD1
    return $events_rec_ds_AfterExecuteDelete;
}
//End Close events_rec_ds_AfterExecuteDelete

//Page_AfterInitialize @1-BDA654CC
function Page_AfterInitialize(& $sender)
{
    $Page_AfterInitialize = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $events; //Compatibility
//End Page_AfterInitialize

//Custom Code @21-2A29BDB7
// -------------------------
global $events_rec;

	$event_id = CCGetFromGet("event_id","");
	if (CCStrLen($event_id)) {
	//Edit mode
		if (!EditAllowed($event_id)) {
			header("Location: ".CCGetFromGet("ret_link","index.php"));
			exit;
		}
	} else {
	//Add mode
		if (!AddAllowed()) {
			header("Location: ".CCGetFromGet("ret_link","index.php"));
			exit;
		}

		if (!$events_rec->FormSubmitted && strlen(CCGetFromGet("event_date", ""))) {
			$events_rec->event_date->SetValue(CCParseDate(CCGetFromGet("event_date", ""), array("mm","/","dd","/","yyyy")) );
			$events_rec->event_date->Errors->Clear();
		} 
	}
// -------------------------
//End Custom Code

//Close Page_AfterInitialize @1-379D319D
    return $Page_AfterInitialize;
}
//End Close Page_AfterInitialize


?>