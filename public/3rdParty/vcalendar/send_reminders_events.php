<?php
//BindEvents Method @1-397EAC53
function BindEvents()
{
    global $CCSEvents;
    $CCSEvents["AfterInitialize"] = "Page_AfterInitialize";
}
//End BindEvents Method

//Page_AfterInitialize @1-0B04832B
function Page_AfterInitialize(& $sender)
{
    $Page_AfterInitialize = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $send_reminders; //Compatibility
//End Page_AfterInitialize

//Custom Code @2-A1D4548D
// -------------------------
    global $send_reminders;

	$db = new clsDBcalendar();

	$SQL = "SELECT users.user_email as email, ".
				"users.user_last_name as last_name, ".
				"users.user_first_name as first_name, ".
				"event_remind.remind_time as time, ".
				"event_remind.remind_date as date, ".
				"event_remind.remind_id as remind_id, ".
				"events.event_id as event_id, ".
				"events.event_title as title ".
			"FROM event_remind ".
				"INNER JOIN events ON (event_remind.event_id = events.event_id) ".
				"INNER JOIN users ON (event_remind.user_id = users.user_id) ".
			"WHERE remind_date < NOW() OR (remind_date = NOW() AND remind_time <= NOW())";
	$db->query($SQL);

	$remind_id = "";
	while ($db->next_record()) { 
		$attr["{user_name}"] = $db->f("first_name")." ".$db->f("last_name");
		$attr["{event_title}"] = $db->f("title");
		$attr["{event_date_time}"] = $db->f("date")." ".$db->f("time");
		$attr["{event_url}"] = ServerURL . "event_view.php?event_id=".$db->f("event_id");
		$email_to = $db->f("email");
		$remind_id .= $db->f("remind_id").", ";

		print_r($attr);

		$sent = SendEmailMessage("remind_event",$email_to,$attr);
		if (strlen($sent))
			echo $sent;
	}

	if (CCStrLen($remind_id)) {
		$SQL = "DELETE FROM event_remind WHERE remind_id in (".substr($remind_id,0,-2).")";
		$db->query($SQL);
	}
	$db->close();
// -------------------------
//End Custom Code

//Close Page_AfterInitialize @1-379D319D
    return $Page_AfterInitialize;
}
//End Close Page_AfterInitialize
?>