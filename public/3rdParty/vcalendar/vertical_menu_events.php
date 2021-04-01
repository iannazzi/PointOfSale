<?php
// //Events @1-F81417CB

//vertical_menu_VerticalMenu_locale_BeforeShow @141-629F581B
function vertical_menu_VerticalMenu_locale_BeforeShow(& $sender)
{
    $vertical_menu_VerticalMenu_locale_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $vertical_menu; //Compatibility
//End vertical_menu_VerticalMenu_locale_BeforeShow

//Custom Code @169-2A29BDB7
// -------------------------
global $calendar_languages;

	$listboxval = split(";",$calendar_languages);
	for($j = 0; $j < Count($listboxval)-1; $j++)
		$arrValues[] = array($listboxval[$j], $listboxval[++$j]);
	$Component->Values = $arrValues;

// -------------------------
//End Custom Code

//Close vertical_menu_VerticalMenu_locale_BeforeShow @141-15FE49D0
    return $vertical_menu_VerticalMenu_locale_BeforeShow;
}
//End Close vertical_menu_VerticalMenu_locale_BeforeShow

//vertical_menu_VerticalMenu_BeforeShow @127-F1792633
function vertical_menu_VerticalMenu_BeforeShow(& $sender)
{
    $vertical_menu_VerticalMenu_BeforeShow = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $vertical_menu; //Compatibility
//End vertical_menu_VerticalMenu_BeforeShow

//Custom Code @145-2A29BDB7
// -------------------------
global $calendar_config;
global $CCProjectStyle;

	if ($calendar_config["menu_type"] == "Vertical") {
		$Component->categories->SetValue(CCGetSession("category"));

		if (!AddAllowed())
			$Component->add_event->Visible = False;

		if (CCGetUserID())
			$Component->LoginPanel->Visible = False;
		else
			$Component->user_logout->Visible = False;

		if (!$calendar_config["change_style"])
			$Component->style->Visible = False;
		else
			$Component->style->SetValue($CCProjectStyle);

		if (!$calendar_config["change_language"])
			$Component->locale->Visible = False;
		else
			$Component->locale->SetValue(CCGetSession("lang"));

		if (CCGetGroupID() < 100)
			$Component->administration_link->Visible = False;

		if ($calendar_config["registration_type"] == "0") 
			$Component->RegLink->Visible = False;
	} else
		$Component->Visible = False;
// -------------------------
//End Custom Code

//Close vertical_menu_VerticalMenu_BeforeShow @127-BAFF4598
    return $vertical_menu_VerticalMenu_BeforeShow;
}
//End Close vertical_menu_VerticalMenu_BeforeShow

//vertical_menu_VerticalMenu_OnValidate @127-748420D2
function vertical_menu_VerticalMenu_OnValidate(& $sender)
{
    $vertical_menu_VerticalMenu_OnValidate = true;
    $Component = & $sender;
    $Container = CCGetParentContainer($sender);
    global $vertical_menu; //Compatibility
//End vertical_menu_VerticalMenu_OnValidate

//Custom Code @168-2A29BDB7
// -------------------------
global $calendar_config;

	CCSetSession("category", $Component->categories->GetValue());
	if ($calendar_config["change_language"])
		CCSetSession("locale", $Component->locale->GetValue());
	if ($calendar_config["change_style"]) {
		CCSetSession("style", $Component->style->GetValue());
    	CCSetCookie("style", $Container->style->GetValue(), time() + 31536000);
	}
// -------------------------
//End Custom Code

//Close vertical_menu_VerticalMenu_OnValidate @127-85042111
    return $vertical_menu_VerticalMenu_OnValidate;
}
//End Close vertical_menu_VerticalMenu_OnValidate


?>
