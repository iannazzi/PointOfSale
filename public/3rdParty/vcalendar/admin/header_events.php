<?php
// //Events @1-F81417CB

//header_HMenu_style_BeforeShow @68-BB99F3E3
function header_HMenu_style_BeforeShow(& $sender)
{
    $header_HMenu_style_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $header; //Compatibility
//End header_HMenu_style_BeforeShow

//Custom Code @80-2A29BDB7
// -------------------------
global $CCProjectStyle;

	$Container->style->SetValue($CCProjectStyle);

// -------------------------
//End Custom Code

//Close header_HMenu_style_BeforeShow @68-1C9FC8F9
    return $header_HMenu_style_BeforeShow;
}
//End Close header_HMenu_style_BeforeShow

//header_HMenu_locale_BeforeShow @69-2AB9674F
function header_HMenu_locale_BeforeShow(& $sender)
{
    $header_HMenu_locale_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $header; //Compatibility
//End header_HMenu_locale_BeforeShow

//Custom Code @79-2A29BDB7
// -------------------------
global $calendar_languages;

	$listboxval = split(";",$calendar_languages);
	for($j = 0; $j < Count($listboxval)-1; $j++)
		$arrValues[] = array($listboxval[$j], $listboxval[++$j]);
	$Component->Values = $arrValues;

	$Component->SetValue(CCGetSession("locale"));

// -------------------------
//End Custom Code

//Close header_HMenu_locale_BeforeShow @69-146244BE
    return $header_HMenu_locale_BeforeShow;
}
//End Close header_HMenu_locale_BeforeShow

//header_HMenu_Button_Apply_OnClick @58-61545863
function header_HMenu_Button_Apply_OnClick(& $sender)
{
    $header_HMenu_Button_Apply_OnClick = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $header; //Compatibility
//End header_HMenu_Button_Apply_OnClick

//Custom Code @78-2A29BDB7
// -------------------------
	CCSetSession("locale", $Container->locale->GetValue());
	CCSetSession("style", $Container->style->GetValue());
	CCSetCookie("style", $Container->style->GetValue(), time() + 31536000);
// -------------------------
//End Custom Code

//Close header_HMenu_Button_Apply_OnClick @58-387763A4
    return $header_HMenu_Button_Apply_OnClick;
}
//End Close header_HMenu_Button_Apply_OnClick

?>
