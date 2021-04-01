<?php
// //Events @1-F81417CB

//footer_html_footer_BeforeShow @2-00DF8F71
function footer_html_footer_BeforeShow(& $sender)
{
    $footer_html_footer_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $footer; //Compatibility
//End footer_html_footer_BeforeShow

//Custom Code @3-240E2B49
// -------------------------
	global $calendar_config;
	$Container->html_footer->SetValue($calendar_config["html_footer"]);
// -------------------------
//End Custom Code

//Close footer_html_footer_BeforeShow @2-58035155
    return $footer_html_footer_BeforeShow;
}
//End Close footer_html_footer_BeforeShow


?>
