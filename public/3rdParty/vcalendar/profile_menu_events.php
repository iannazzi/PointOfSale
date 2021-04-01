<?php
// //Events @1-F81417CB

//profile_menu_my_events_BeforeShow @9-BE761ED5
function profile_menu_my_events_BeforeShow(& $sender)
{
    $profile_menu_my_events_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $profile_menu; //Compatibility
//End profile_menu_my_events_BeforeShow

//Custom Code @11-2A29BDB7
// -------------------------
	if (!AddAllowed())
		$Component->Visible = False;
// -------------------------
//End Custom Code

//Close profile_menu_my_events_BeforeShow @9-64D32AF1
    return $profile_menu_my_events_BeforeShow;
}
//End Close profile_menu_my_events_BeforeShow


?>
