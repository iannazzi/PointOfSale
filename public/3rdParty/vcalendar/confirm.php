<?php
//Include Common Files @1-CE0A5A51
define("RelativePath", ".");
define("PathToCurrentPage", "/");
define("FileName", "confirm.php");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
//End Include Common Files

//Include Page implementation @4-8EACA429
include_once(RelativePath . "/header.php");
//End Include Page implementation

//Include Page implementation @6-D3FCB384
include_once(RelativePath . "/vertical_menu.php");
//End Include Page implementation

//Include Page implementation @5-EBA5EA16
include_once(RelativePath . "/footer.php");
//End Include Page implementation

//Initialize Page @1-DEC20628
// Variables
$FileName = "";
$Redirect = "";
$Tpl = "";
$TemplateFileName = "";
$BlockToParse = "";
$ComponentName = "";

// Events;
$CCSEvents = "";
$CCSEventResult = "";

$FileName = FileName;
$Redirect = "";
$TemplateFileName = "confirm.html";
$BlockToParse = "main";
$TemplateEncoding = "UTF-8";
$PathToRoot = "./";
//End Initialize Page

//Include events file @1-BE87C6CC
include("./confirm_events.php");
//End Include events file

//Initialize Objects @1-BCACECD7

// Controls
$header = & new clsheader("", "header", $MainPage);
$header->Initialize();
$vertical_menu = & new clsvertical_menu("", "vertical_menu", $MainPage);
$vertical_menu->Initialize();
$MessageLabel = & new clsControl(ccsLabel, "MessageLabel", "MessageLabel", ccsText, "", CCGetRequestParam("MessageLabel", ccsGet, NULL), $MainPage);
$MessageLabel->HTML = true;
$footer = & new clsfooter("", "footer", $MainPage);
$footer->Initialize();
$MainPage->header = & $header;
$MainPage->vertical_menu = & $vertical_menu;
$MainPage->MessageLabel = & $MessageLabel;
$MainPage->footer = & $footer;

BindEvents();

$CCSEventResult = CCGetEvent($CCSEvents, "AfterInitialize", $MainPage);

$Charset = $Charset ? $Charset : "utf-8";
if ($Charset)
    header("Content-Type: text/html; charset=" . $Charset);
//End Initialize Objects

//Initialize HTML Template @1-885748E0
$CCSEventResult = CCGetEvent($CCSEvents, "OnInitializeView", $MainPage);
$Tpl = new clsTemplate($FileEncoding, $TemplateEncoding);
$Tpl->LoadTemplate(PathToCurrentPage . $TemplateFileName, $BlockToParse, "UTF-8", "replace");
$Tpl->block_path = "/$BlockToParse";
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeShow", $MainPage);
//End Initialize HTML Template

//Execute Components @1-F751105C
$header->Operations();
$vertical_menu->Operations();
$footer->Operations();
//End Execute Components

//Go to destination page @1-41825DB9
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
    header("Location: " . $Redirect);
    $header->Class_Terminate();
    unset($header);
    $vertical_menu->Class_Terminate();
    unset($vertical_menu);
    $footer->Class_Terminate();
    unset($footer);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-5A358F13
$header->Show();
$vertical_menu->Show();
$footer->Show();
$MessageLabel->Show();
$Tpl->block_path = "";
$Tpl->Parse($BlockToParse, false);
$main_block = $Tpl->GetVar($BlockToParse);
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeOutput", $MainPage);
if ($CCSEventResult) echo $main_block;
//End Show Page

//Unload Page @1-AC0B8CE4
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
$header->Class_Terminate();
unset($header);
$vertical_menu->Class_Terminate();
unset($vertical_menu);
$footer->Class_Terminate();
unset($footer);
unset($Tpl);
//End Unload Page


?>
