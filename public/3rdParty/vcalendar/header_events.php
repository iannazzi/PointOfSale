<?php
// //Events @1-F81417CB

//header_html_header_BeforeShow @30-2EFB2BDF
function header_html_header_BeforeShow(& $sender)
{
    $header_html_header_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $header; //Compatibility
//End header_html_header_BeforeShow

//Custom Code @31-781B43E6
// -------------------------
global $calendar_config;

	$Container->html_header->SetValue($calendar_config["html_header"]);

// -------------------------
//End Custom Code

//Close header_html_header_BeforeShow @30-4CC26CD0
    return $header_html_header_BeforeShow;
}
//End Close header_html_header_BeforeShow

//header_HMenu_locale_BeforeShow @87-2AB9674F
function header_HMenu_locale_BeforeShow(& $sender)
{
    $header_HMenu_locale_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $header; //Compatibility
//End header_HMenu_locale_BeforeShow

//Custom Code @104-2A29BDB7
// -------------------------
global $calendar_languages;

	$listboxval = split(";",$calendar_languages);
	for($j = 0; $j < Count($listboxval)-1; $j++)
		$arrValues[] = array($listboxval[$j], $listboxval[++$j]);
	$Component->Values = $arrValues;

// -------------------------
//End Custom Code

//Close header_HMenu_locale_BeforeShow @87-146244BE
    return $header_HMenu_locale_BeforeShow;
}
//End Close header_HMenu_locale_BeforeShow

//header_HMenu_Button_Apply_OnClick @89-61545863
function header_HMenu_Button_Apply_OnClick(& $sender)
{
    $header_HMenu_Button_Apply_OnClick = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $header; //Compatibility
//End header_HMenu_Button_Apply_OnClick

//Custom Code @90-781B43E6
// -------------------------
global $calendar_config;

	CCSetSession("category", $Container->categories->GetValue());
	if ($calendar_config["change_language"])
		CCSetSession("locale", $Container->locale->GetValue());
	if ($calendar_config["change_style"]) {
		CCSetSession("style", $Container->style->GetValue());
		CCSetCookie("style", $Container->style->GetValue(), time() + 31536000);
	}
// -------------------------
//End Custom Code

//Close header_HMenu_Button_Apply_OnClick @89-387763A4
    return $header_HMenu_Button_Apply_OnClick;
}
//End Close header_HMenu_Button_Apply_OnClick

//header_HMenu_BeforeShow @65-F8C312A9
function header_HMenu_BeforeShow(& $sender)
{
    $header_HMenu_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $header; //Compatibility
//End header_HMenu_BeforeShow

//Custom Code @85-781B43E6
// -------------------------
global $calendar_config;
global $CCProjectStyle;

	$Container->categories->SetValue(CCGetSession("category"));

	if (!AddAllowed())
		$Container->add_event->Visible = false;

	if (CCGetUserID())
		$Container->LoginPanel->Visible = false;
	else
		$Container->user_logout->Visible = false;

	if (!$calendar_config["change_style"])
		$Container->style->Visible = false;
	else
		$Container->style->SetValue($CCProjectStyle);

	if (!$calendar_config["change_language"])
		$Container->locale->Visible = false;
	else
		$Container->locale->SetValue(CCGetSession("locale"));

	if (CCGetGroupID() < 100) 
		$Container->administration_link->Visible = false;

	if ($calendar_config["registration_type"] == "0") 
		$Container->RegLink->Visible = false;

// -------------------------
//End Custom Code

//Close header_HMenu_BeforeShow @65-1930A679
    return $header_HMenu_BeforeShow;
}
//End Close header_HMenu_BeforeShow

//header_AfterInitialize @1-5085DB55
function header_AfterInitialize(& $sender)
{
    $header_AfterInitialize = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $header; //Compatibility
//End header_AfterInitialize

//Custom Code @91-781B43E6
// -------------------------
	global $calendar_config;

	if (!IsSet($_SESSION['style'])) {
		$prev_style = CCGetCookie("style");
		if (CCSetProjectStyle($prev_style))
			CCSetSession("style", $prev_style);
		else
			if (strlen($calendar_config["default_style"])) {
				CCSetSession("style", $calendar_config["default_style"]);
				CCSetProjectStyle($calendar_config["default_style"]);
			}
	}

	if (strlen($calendar_config["default_language"]) && !IsSet($_SESSION['lang']))
      	CCSetSession("locale", $calendar_config["default_language"]);
	
	if (strlen($calendar_config["menu_type"]) && strcmp($calendar_config["menu_type"],"Horizontal"))
		$Container->HMenu->Visible = false;
// -------------------------
//End Custom Code

//Close header_AfterInitialize @1-2FE08AE2
    return $header_AfterInitialize;
}
//End Close header_AfterInitialize

?>