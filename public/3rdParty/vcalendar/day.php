<?php
//Include Common Files @1-8598A327
define("RelativePath", ".");
define("PathToCurrentPage", "/");
define("FileName", "day.php");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
//End Include Common Files

//Include Page implementation @2-8EACA429
include_once(RelativePath . "/header.php");
//End Include Page implementation

//Include Page implementation @4-A5E85701
include_once(RelativePath . "/infopanel.php");
//End Include Page implementation

class clsGridShortViewEventsGrid { //ShortViewEventsGrid class @7-8E7161DD

//Variables @7-663D5B8C

    // Public variables
    var $ComponentType = "Grid";
    var $ComponentName;
    var $Visible;
    var $Errors;
    var $ErrorBlock;
    var $ds;
    var $DataSource;
    var $PageSize;
    var $IsEmpty;
    var $SorterName = "";
    var $SorterDirection = "";
    var $PageNumber;
    var $RowNumber;
    var $ControlsVisible = array();

    var $CCSEvents = "";
    var $CCSEventResult;

    var $RelativePath = "";

    // Grid Controls
    var $StaticControls;
    var $RowControls;
//End Variables

//Class_Initialize Event @7-5ACBAE15
    function clsGridShortViewEventsGrid($RelativePath, & $Parent)
    {
        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->ComponentName = "ShortViewEventsGrid";
        $this->Visible = True;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Grid ShortViewEventsGrid";
        $this->DataSource = new clsShortViewEventsGridDataSource($this);
        $this->ds = & $this->DataSource;
        $this->PageSize = CCGetParam($this->ComponentName . "PageSize", "");
        if(!is_numeric($this->PageSize) || !strlen($this->PageSize))
            $this->PageSize = 100;
        else
            $this->PageSize = intval($this->PageSize);
        if ($this->PageSize > 100)
            $this->PageSize = 100;
        if($this->PageSize == 0)
            $this->Errors->addError("<p>Form: Grid " . $this->ComponentName . "<br>Error: (CCS06) Invalid page size.</p>");
        $this->PageNumber = intval(CCGetParam($this->ComponentName . "Page", 1));
        if ($this->PageNumber <= 0) $this->PageNumber = 1;

        $this->event_time = & new clsControl(ccsLabel, "event_time", "event_time", ccsDate, array("ShortTime"), CCGetRequestParam("event_time", ccsGet, NULL), $this);
        $this->event_time_end = & new clsControl(ccsLabel, "event_time_end", "event_time_end", ccsDate, array("ShortTime"), CCGetRequestParam("event_time_end", ccsGet, NULL), $this);
        $this->category_image = & new clsControl(ccsImage, "category_image", "category_image", ccsText, "", CCGetRequestParam("category_image", ccsGet, NULL), $this);
        $this->event_title = & new clsControl(ccsLink, "event_title", "event_title", ccsText, "", CCGetRequestParam("event_title", ccsGet, NULL), $this);
        $this->event_title->Page = "event_view.php";
        $this->category_name = & new clsControl(ccsLabel, "category_name", "category_name", ccsText, "", CCGetRequestParam("category_name", ccsGet, NULL), $this);
        $this->CalendarDate = & new clsControl(ccsLabel, "CalendarDate", "CalendarDate", ccsDate, array("mmmm", " ", "d", ", ", "yyyy", ", ", "dddd"), CCGetRequestParam("CalendarDate", ccsGet, NULL), $this);
        $this->add_event = & new clsControl(ccsLink, "add_event", "add_event", ccsText, "", CCGetRequestParam("add_event", ccsGet, NULL), $this);
        $this->add_event->Page = "events.php";
    }
//End Class_Initialize Event

//Initialize Method @7-90E704C5
    function Initialize()
    {
        if(!$this->Visible) return;

        $this->DataSource->PageSize = & $this->PageSize;
        $this->DataSource->AbsolutePage = & $this->PageNumber;
        $this->DataSource->SetOrder($this->SorterName, $this->SorterDirection);
    }
//End Initialize Method

//Show Method @7-943C5D8E
    function Show()
    {
        global $Tpl;
        global $CCSLocales;
        if(!$this->Visible) return;

        $this->RowNumber = 0;

        $this->DataSource->Parameters["urlday"] = CCGetFromGet("day", NULL);
        $this->DataSource->Parameters["sescategory"] = CCGetSession("category", NULL);
        $this->DataSource->Parameters["seslocale"] = CCGetSession("locale", NULL);
        $this->DataSource->Parameters["urlevents_category_id"] = CCGetFromGet("events_category_id", NULL);

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeSelect", $this);


        $this->DataSource->Prepare();
        $this->DataSource->Open();
        $this->IsEmpty = ! $this->DataSource->next_record();

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShow", $this);
        if(!$this->Visible) return;

        $GridBlock = "Grid " . $this->ComponentName;
        $ParentPath = $Tpl->block_path;
        $Tpl->block_path = $ParentPath . "/" . $GridBlock;


        if (!$this->IsEmpty) {
            $this->IsEmpty = false;
            $this->ControlsVisible["event_time"] = $this->event_time->Visible;
            $this->ControlsVisible["event_time_end"] = $this->event_time_end->Visible;
            $this->ControlsVisible["category_image"] = $this->category_image->Visible;
            $this->ControlsVisible["event_title"] = $this->event_title->Visible;
            $this->ControlsVisible["category_name"] = $this->category_name->Visible;
            do {
                $this->RowNumber++;
                $this->DataSource->SetValues();
                $Tpl->block_path = $ParentPath . "/" . $GridBlock . "/Row";
                $this->event_time->SetValue($this->DataSource->event_time->GetValue());
                $this->event_time_end->SetValue($this->DataSource->event_time_end->GetValue());
                $this->category_image->SetValue($this->DataSource->category_image->GetValue());
                $this->event_title->SetValue($this->DataSource->event_title->GetValue());
                $this->event_title->Parameters = "";
                $this->event_title->Parameters = CCAddParam($this->event_title->Parameters, "event_id", $this->DataSource->f("event_id"));
                $this->category_name->SetValue($this->DataSource->category_name->GetValue());
                $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowRow", $this);
                $this->event_time->Show();
                $this->event_time_end->Show();
                $this->category_image->Show();
                $this->event_title->Show();
                $this->category_name->Show();
                $Tpl->block_path = $ParentPath . "/" . $GridBlock;
                $Tpl->parse("Row", true);
            } while (($this->RowNumber < $this->PageSize) && $this->DataSource->next_record());
        }
        else // Show NoRecords block if no records are found
        {
            $this->IsEmpty = true;
            $Tpl->parse("NoRecords", false);
        }

        $errors = $this->GetErrors();
        if(strlen($errors))
        {
            $Tpl->replaceblock("", $errors);
            $Tpl->block_path = $ParentPath;
            return;
        }
        $this->CalendarDate->Show();
        $this->add_event->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->DataSource->close();
    }
//End Show Method

//GetErrors Method @7-A2AA58EC
    function GetErrors()
    {
        $errors = "";
        $errors = ComposeStrings($errors, $this->event_time->Errors->ToString());
        $errors = ComposeStrings($errors, $this->event_time_end->Errors->ToString());
        $errors = ComposeStrings($errors, $this->category_image->Errors->ToString());
        $errors = ComposeStrings($errors, $this->event_title->Errors->ToString());
        $errors = ComposeStrings($errors, $this->category_name->Errors->ToString());
        $errors = ComposeStrings($errors, $this->Errors->ToString());
        $errors = ComposeStrings($errors, $this->DataSource->Errors->ToString());
        return $errors;
    }
//End GetErrors Method

} //End ShortViewEventsGrid Class @7-FCB6E20C

class clsShortViewEventsGridDataSource extends clsDBcalendar {  //ShortViewEventsGridDataSource Class @7-B78D2E98

//DataSource Variables @7-CB84FE7A
    var $Parent = "";
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;
    var $CmdExecution;

    var $CountSQL;
    var $wp;


    // Datasource fields
    var $event_time;
    var $event_time_end;
    var $category_image;
    var $event_title;
    var $category_name;
//End DataSource Variables

//DataSourceClass_Initialize Event @7-D5DC73DB
    function clsShortViewEventsGridDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "Grid ShortViewEventsGrid";
        $this->Initialize();
        $this->event_time = new clsField("event_time", ccsDate, array("HH", ":", "nn", ":", "ss"));
        $this->event_time_end = new clsField("event_time_end", ccsDate, array("HH", ":", "nn", ":", "ss"));
        $this->category_image = new clsField("category_image", ccsText, "");
        $this->event_title = new clsField("event_title", ccsText, "");
        $this->category_name = new clsField("category_name", ccsText, "");

    }
//End DataSourceClass_Initialize Event

//SetOrder Method @7-0415FF5F
    function SetOrder($SorterName, $SorterDirection)
    {
        $this->Order = "events.event_time, event_date";
        $this->Order = CCGetOrder($this->Order, $SorterName, $SorterDirection, 
            "");
    }
//End SetOrder Method

//Prepare Method @7-C105AD41
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "urlday", ccsDate, array("yyyy", "-", "mm", "-", "dd"), array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"), $this->Parameters["urlday"], "", false);
        $this->wp->AddParameter("2", "sescategory", ccsInteger, "", "", $this->Parameters["sescategory"], "", false);
        $this->wp->AddParameter("3", "seslocale", ccsText, "", "", $this->Parameters["seslocale"], "", false);
        $this->wp->AddParameter("4", "urlevents_category_id", ccsInteger, "", "", $this->Parameters["urlevents_category_id"], "", true);
        $this->wp->Criterion[1] = $this->wp->Operation(opEqual, "events.event_date", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsDate),false);
        $this->wp->Criterion[2] = $this->wp->Operation(opEqual, "events.category_id", $this->wp->GetDBValue("2"), $this->ToSQL($this->wp->GetDBValue("2"), ccsInteger),false);
        $this->wp->Criterion[3] = $this->wp->Operation(opEqual, "categories_langs.language_id", $this->wp->GetDBValue("3"), $this->ToSQL($this->wp->GetDBValue("3"), ccsText),false);
        $this->wp->Criterion[4] = $this->wp->Operation(opIsNull, "events.category_id", $this->wp->GetDBValue("4"), $this->ToSQL($this->wp->GetDBValue("4"), ccsInteger),true);
        $this->Where = $this->wp->opAND(
             false, $this->wp->opAND(
             false, 
             $this->wp->Criterion[1], 
             $this->wp->Criterion[2]), $this->wp->opOR(
             true, 
             $this->wp->Criterion[3], 
             $this->wp->Criterion[4]));
    }
//End Prepare Method

//Open Method @7-269C2871
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->CountSQL = "SELECT COUNT(*)\n\n" .
        "FROM (categories RIGHT JOIN events ON\n\n" .
        "events.category_id = categories.category_id) LEFT JOIN categories_langs ON\n\n" .
        "categories.category_id = categories_langs.category_id";
        $this->SQL = "SELECT event_title, event_time, event_id, categories_langs.category_name AS category_name, category_image, event_time_end \n\n" .
        "FROM (categories RIGHT JOIN events ON\n\n" .
        "events.category_id = categories.category_id) LEFT JOIN categories_langs ON\n\n" .
        "categories.category_id = categories_langs.category_id {SQL_Where} {SQL_OrderBy}";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect", $this->Parent);
        if ($this->CountSQL) 
            $this->RecordsCount = CCGetDBValue(CCBuildSQL($this->CountSQL, $this->Where, ""), $this);
        else
            $this->RecordsCount = "CCS not counted";
        $this->query($this->OptimizeSQL(CCBuildSQL($this->SQL, $this->Where, $this->Order)));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect", $this->Parent);
    }
//End Open Method

//SetValues Method @7-B08AD7E2
    function SetValues()
    {
        $this->event_time->SetDBValue(trim($this->f("event_time")));
        $this->event_time_end->SetDBValue(trim($this->f("event_time_end")));
        $this->category_image->SetDBValue($this->f("category_image"));
        $this->event_title->SetDBValue($this->f("event_title"));
        $this->category_name->SetDBValue($this->f("category_name"));
    }
//End SetValues Method

} //End ShortViewEventsGridDataSource Class @7-FCB6E20C

class clsRecordShortViewEventsNavigator { //ShortViewEventsNavigator Class @85-1200C205

//Variables @85-0DF9B1C2

    // Public variables
    var $ComponentType = "Record";
    var $ComponentName;
    var $Parent;
    var $HTMLFormAction;
    var $PressedButton;
    var $Errors;
    var $ErrorBlock;
    var $FormSubmitted;
    var $FormEnctype;
    var $Visible;
    var $IsEmpty;

    var $CCSEvents = "";
    var $CCSEventResult;

    var $RelativePath = "";

    var $InsertAllowed = false;
    var $UpdateAllowed = false;
    var $DeleteAllowed = false;
    var $ReadAllowed   = false;
    var $EditMode      = false;
    var $ds;
    var $DataSource;
    var $ValidatingControls;
    var $Controls;

    // Class variables
//End Variables

//Class_Initialize Event @85-D347F1C7
    function clsRecordShortViewEventsNavigator($RelativePath, & $Parent)
    {

        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->Visible = true;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Record ShortViewEventsNavigator/Error";
        $this->ReadAllowed = true;
        if($this->Visible)
        {
            $this->ComponentName = "ShortViewEventsNavigator";
            $CCSForm = split(":", CCGetFromGet("ccsForm", ""), 2);
            if(sizeof($CCSForm) == 1)
                $CCSForm[1] = "";
            list($FormName, $FormMethod) = $CCSForm;
            $this->FormEnctype = "application/x-www-form-urlencoded";
            $this->FormSubmitted = ($FormName == $this->ComponentName);
            $Method = $this->FormSubmitted ? ccsPost : ccsGet;
            $this->prev_day_link = & new clsControl(ccsLink, "prev_day_link", "prev_day_link", ccsText, "", CCGetRequestParam("prev_day_link", $Method, NULL), $this);
            $this->prev_day_link->Page = "day.php";
            $this->date_day = & new clsControl(ccsListBox, "date_day", "date_day", ccsText, "", CCGetRequestParam("date_day", $Method, NULL), $this);
            $this->ButtonGo = & new clsButton("ButtonGo", $Method, $this);
            $this->next_day_link = & new clsControl(ccsLink, "next_day_link", "next_day_link", ccsText, "", CCGetRequestParam("next_day_link", $Method, NULL), $this);
            $this->next_day_link->Page = "day.php";
            $this->week = & new clsControl(ccsListBox, "week", "week", ccsText, "", CCGetRequestParam("week", $Method, NULL), $this);
            $this->Button_DoSearch = & new clsButton("Button_DoSearch", $Method, $this);
            $this->YearIcon = & new clsControl(ccsLink, "YearIcon", "YearIcon", ccsText, "", CCGetRequestParam("YearIcon", $Method, NULL), $this);
            $this->YearIcon->Page = "year.php";
            $this->MonthIcon = & new clsControl(ccsLink, "MonthIcon", "MonthIcon", ccsText, "", CCGetRequestParam("MonthIcon", $Method, NULL), $this);
            $this->MonthIcon->Page = "index.php";
            $this->WeekIcon = & new clsControl(ccsLink, "WeekIcon", "WeekIcon", ccsText, "", CCGetRequestParam("WeekIcon", $Method, NULL), $this);
            $this->WeekIcon->Page = "week.php";
        }
    }
//End Class_Initialize Event

//Validate Method @85-AC9A3A65
    function Validate()
    {
        global $CCSLocales;
        $Validation = true;
        $Where = "";
        $Validation = ($this->date_day->Validate() && $Validation);
        $Validation = ($this->week->Validate() && $Validation);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate", $this);
        $Validation =  $Validation && ($this->date_day->Errors->Count() == 0);
        $Validation =  $Validation && ($this->week->Errors->Count() == 0);
        return (($this->Errors->Count() == 0) && $Validation);
    }
//End Validate Method

//CheckErrors Method @85-00C2001D
    function CheckErrors()
    {
        $errors = false;
        $errors = ($errors || $this->prev_day_link->Errors->Count());
        $errors = ($errors || $this->date_day->Errors->Count());
        $errors = ($errors || $this->next_day_link->Errors->Count());
        $errors = ($errors || $this->week->Errors->Count());
        $errors = ($errors || $this->YearIcon->Errors->Count());
        $errors = ($errors || $this->MonthIcon->Errors->Count());
        $errors = ($errors || $this->WeekIcon->Errors->Count());
        $errors = ($errors || $this->Errors->Count());
        return $errors;
    }
//End CheckErrors Method

//Operation Method @85-5DBE27E8
    function Operation()
    {
        if(!$this->Visible)
            return;

        global $Redirect;
        global $FileName;

        if(!$this->FormSubmitted) {
            return;
        }

        if($this->FormSubmitted) {
            $this->PressedButton = "ButtonGo";
            if($this->ButtonGo->Pressed) {
                $this->PressedButton = "ButtonGo";
            } else if($this->Button_DoSearch->Pressed) {
                $this->PressedButton = "Button_DoSearch";
            }
        }
        $Redirect = $FileName;
        if($this->Validate()) {
            if($this->PressedButton == "ButtonGo") {
                if(!CCGetEvent($this->ButtonGo->CCSEvents, "OnClick", $this->ButtonGo)) {
                    $Redirect = "";
                }
            } else if($this->PressedButton == "Button_DoSearch") {
                if(!CCGetEvent($this->Button_DoSearch->CCSEvents, "OnClick", $this->Button_DoSearch)) {
                    $Redirect = "";
                }
            }
        } else {
            $Redirect = "";
        }
    }
//End Operation Method

//Show Method @85-5E759941
    function Show()
    {
        global $Tpl;
        global $FileName;
        global $CCSLocales;
        $Error = "";

        if(!$this->Visible)
            return;

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeSelect", $this);

        $this->date_day->Prepare();
        $this->week->Prepare();

        $RecordBlock = "Record " . $this->ComponentName;
        $ParentPath = $Tpl->block_path;
        $Tpl->block_path = $ParentPath . "/" . $RecordBlock;
        $this->EditMode = $this->EditMode && $this->ReadAllowed;
        if (!$this->FormSubmitted) {
        }

        if($this->FormSubmitted || $this->CheckErrors()) {
            $Error = "";
            $Error = ComposeStrings($Error, $this->prev_day_link->Errors->ToString());
            $Error = ComposeStrings($Error, $this->date_day->Errors->ToString());
            $Error = ComposeStrings($Error, $this->next_day_link->Errors->ToString());
            $Error = ComposeStrings($Error, $this->week->Errors->ToString());
            $Error = ComposeStrings($Error, $this->YearIcon->Errors->ToString());
            $Error = ComposeStrings($Error, $this->MonthIcon->Errors->ToString());
            $Error = ComposeStrings($Error, $this->WeekIcon->Errors->ToString());
            $Error = ComposeStrings($Error, $this->Errors->ToString());
            $Tpl->SetVar("Error", $Error);
            $Tpl->Parse("Error", false);
        }
        $CCSForm = $this->EditMode ? $this->ComponentName . ":" . "Edit" : $this->ComponentName;
        $this->HTMLFormAction = $FileName . "?" . CCAddParam(CCGetQueryString("QueryString", ""), "ccsForm", $CCSForm);
        $Tpl->SetVar("Action", $this->HTMLFormAction);
        $Tpl->SetVar("HTMLFormName", $this->ComponentName);
        $Tpl->SetVar("HTMLFormEnctype", $this->FormEnctype);

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShow", $this);
        if(!$this->Visible) {
            $Tpl->block_path = $ParentPath;
            return;
        }

        $this->prev_day_link->Show();
        $this->date_day->Show();
        $this->ButtonGo->Show();
        $this->next_day_link->Show();
        $this->week->Show();
        $this->Button_DoSearch->Show();
        $this->YearIcon->Show();
        $this->MonthIcon->Show();
        $this->WeekIcon->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
    }
//End Show Method

} //End ShortViewEventsNavigator Class @85-FCB6E20C

//Include Page implementation @3-EBA5EA16
include_once(RelativePath . "/footer.php");
//End Include Page implementation

//Initialize Page @1-407B42DD
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
$TemplateFileName = "day.html";
$BlockToParse = "main";
$TemplateEncoding = "UTF-8";
$PathToRoot = "./";
//End Initialize Page

//Include events file @1-5FCD6CEF
include("./day_events.php");
//End Include events file

//Initialize Objects @1-EE2C185E
$DBcalendar = new clsDBcalendar();
$MainPage->Connections["calendar"] = & $DBcalendar;

// Controls
$header = & new clsheader("", "header", $MainPage);
$header->Initialize();
$infopanel = & new clsinfopanel("", "infopanel", $MainPage);
$infopanel->Initialize();
$FullViewEvents = & new clsControl(ccsLabel, "FullViewEvents", "FullViewEvents", ccsText, "", CCGetRequestParam("FullViewEvents", ccsGet, NULL), $MainPage);
$FullViewEvents->HTML = true;
$ShortViewEvents = & new clsPanel("ShortViewEvents", $MainPage);
$ShortViewEventsGrid = & new clsGridShortViewEventsGrid("", $MainPage);
$ShortViewEventsNavigator = & new clsRecordShortViewEventsNavigator("", $MainPage);
$footer = & new clsfooter("", "footer", $MainPage);
$footer->Initialize();
$MainPage->header = & $header;
$MainPage->infopanel = & $infopanel;
$MainPage->FullViewEvents = & $FullViewEvents;
$MainPage->ShortViewEvents = & $ShortViewEvents;
$MainPage->ShortViewEventsGrid = & $ShortViewEventsGrid;
$MainPage->ShortViewEventsNavigator = & $ShortViewEventsNavigator;
$MainPage->footer = & $footer;
$ShortViewEvents->AddComponent("ShortViewEventsGrid", $ShortViewEventsGrid);
$ShortViewEvents->AddComponent("ShortViewEventsNavigator", $ShortViewEventsNavigator);
$ShortViewEventsGrid->Initialize();

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

//Execute Components @1-3718B9B8
$header->Operations();
$infopanel->Operations();
$ShortViewEventsNavigator->Operation();
$footer->Operations();
//End Execute Components

//Go to destination page @1-17E971D4
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
    $DBcalendar->close();
    header("Location: " . $Redirect);
    $header->Class_Terminate();
    unset($header);
    $infopanel->Class_Terminate();
    unset($infopanel);
    unset($ShortViewEventsGrid);
    unset($ShortViewEventsNavigator);
    $footer->Class_Terminate();
    unset($footer);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-F7A5637D
$header->Show();
$infopanel->Show();
$footer->Show();
$FullViewEvents->Show();
$ShortViewEvents->Show();
$Tpl->block_path = "";
$Tpl->Parse($BlockToParse, false);
$main_block = $Tpl->GetVar($BlockToParse);
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeOutput", $MainPage);
if ($CCSEventResult) echo $main_block;
//End Show Page

//Unload Page @1-6780012D
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
$DBcalendar->close();
$header->Class_Terminate();
unset($header);
$infopanel->Class_Terminate();
unset($infopanel);
unset($ShortViewEventsGrid);
unset($ShortViewEventsNavigator);
$footer->Class_Terminate();
unset($footer);
unset($Tpl);
//End Unload Page


?>
