<?php
//Include Common Files @1-D2767BAC
define("RelativePath", ".");
define("PathToCurrentPage", "/");
define("FileName", "mini_calendar.php");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
//End Include Common Files

//Include Page implementation @4-A5E85701
include_once(RelativePath . "/infopanel.php");
//End Include Page implementation

//Initialize Page @1-4EF4CF80
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
$TemplateFileName = "mini_calendar.html";
$BlockToParse = "main";
$TemplateEncoding = "UTF-8";
$PathToRoot = "./";
//End Initialize Page

//Initialize Objects @1-9EA9E8D9

// Controls
$infopanel = & new clsinfopanel("", "infopanel", $MainPage);
$infopanel->Initialize();
$MainPage->infopanel = & $infopanel;

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

//Execute Components @1-2947EF2F
$infopanel->Operations();
//End Execute Components

//Go to destination page @1-45EDE4C0
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
    header("Location: " . $Redirect);
    $infopanel->Class_Terminate();
    unset($infopanel);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-FEE42B0F
$infopanel->Show();
$Tpl->block_path = "";
$Tpl->Parse($BlockToParse, false);
$main_block = $Tpl->GetVar($BlockToParse);
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeOutput", $MainPage);
if ($CCSEventResult) echo $main_block;
//End Show Page

//Unload Page @1-DB545C8F
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
$infopanel->Class_Terminate();
unset($infopanel);
unset($Tpl);
//End Unload Page


?>
