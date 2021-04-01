<?php

//Include Common Files @1-11F7A774
define("RelativePath", ".");
define("PathToCurrentPage", "/");
define("FileName", "profile_events.php");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
//End Include Common Files

//Include Page implementation @2-8EACA429
include_once(RelativePath . "/header.php");
//End Include Page implementation

//Include Page implementation @59-8BC56D3E
include_once(RelativePath . "/profile_menu.php");
//End Include Page implementation

class clsRecordevents_groupsSearch { //events_groupsSearch Class @9-BCB981D0

//Variables @9-0DF9B1C2

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

//Class_Initialize Event @9-F4B8ECA5
    function clsRecordevents_groupsSearch($RelativePath, & $Parent)
    {

        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->Visible = true;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Record events_groupsSearch/Error";
        $this->ReadAllowed = true;
        if($this->Visible)
        {
            $this->ComponentName = "events_groupsSearch";
            $CCSForm = split(":", CCGetFromGet("ccsForm", ""), 2);
            if(sizeof($CCSForm) == 1)
                $CCSForm[1] = "";
            list($FormName, $FormMethod) = $CCSForm;
            $this->FormEnctype = "application/x-www-form-urlencoded";
            $this->FormSubmitted = ($FormName == $this->ComponentName);
            $Method = $this->FormSubmitted ? ccsPost : ccsGet;
            $this->s_keyword = & new clsControl(ccsTextBox, "s_keyword", $CCSLocales->GetText("event_title"), ccsText, "", CCGetRequestParam("s_keyword", $Method, NULL), $this);
            $this->s_category = & new clsControl(ccsListBox, "s_category", $CCSLocales->GetText("cal_category"), ccsText, "", CCGetRequestParam("s_category", $Method, NULL), $this);
            $this->s_category->DSType = dsTable;
            list($this->s_category->BoundColumn, $this->s_category->TextColumn, $this->s_category->DBFormat) = array("category_id", "category_name", "");
            $this->s_category->DataSource = new clsDBcalendar();
            $this->s_category->ds = & $this->s_category->DataSource;
            $this->s_category->DataSource->SQL = "SELECT category_name, category_id \n" .
"FROM categories_langs {SQL_Where} {SQL_OrderBy}";
            $this->s_category->DataSource->Parameters["seslocale"] = CCGetSession("locale", NULL);
            $this->s_category->DataSource->wp = new clsSQLParameters();
            $this->s_category->DataSource->wp->AddParameter("1", "seslocale", ccsText, "", "", $this->s_category->DataSource->Parameters["seslocale"], "", false);
            $this->s_category->DataSource->wp->Criterion[1] = $this->s_category->DataSource->wp->Operation(opEqual, "language_id", $this->s_category->DataSource->wp->GetDBValue("1"), $this->s_category->DataSource->ToSQL($this->s_category->DataSource->wp->GetDBValue("1"), ccsText),false);
            $this->s_category->DataSource->Where = 
                 $this->s_category->DataSource->wp->Criterion[1];
            $this->s_date_from = & new clsControl(ccsTextBox, "s_date_from", "s_date_from", ccsDate, array("ShortDate"), CCGetRequestParam("s_date_from", $Method, NULL), $this);
            $this->DatePicker_s_date_from = & new clsDatePicker("DatePicker_s_date_from", "events_groupsSearch", "s_date_from", $this);
            $this->s_date_to = & new clsControl(ccsTextBox, "s_date_to", "s_date_to", ccsDate, array("ShortDate"), CCGetRequestParam("s_date_to", $Method, NULL), $this);
            $this->DatePicker_s_date_to = & new clsDatePicker("DatePicker_s_date_to", "events_groupsSearch", "s_date_to", $this);
            $this->Button_DoSearch = & new clsButton("Button_DoSearch", $Method, $this);
        }
    }
//End Class_Initialize Event

//Validate Method @9-08101A7A
    function Validate()
    {
        global $CCSLocales;
        $Validation = true;
        $Where = "";
        $Validation = ($this->s_keyword->Validate() && $Validation);
        $Validation = ($this->s_category->Validate() && $Validation);
        $Validation = ($this->s_date_from->Validate() && $Validation);
        $Validation = ($this->s_date_to->Validate() && $Validation);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate", $this);
        $Validation =  $Validation && ($this->s_keyword->Errors->Count() == 0);
        $Validation =  $Validation && ($this->s_category->Errors->Count() == 0);
        $Validation =  $Validation && ($this->s_date_from->Errors->Count() == 0);
        $Validation =  $Validation && ($this->s_date_to->Errors->Count() == 0);
        return (($this->Errors->Count() == 0) && $Validation);
    }
//End Validate Method

//CheckErrors Method @9-73F95412
    function CheckErrors()
    {
        $errors = false;
        $errors = ($errors || $this->s_keyword->Errors->Count());
        $errors = ($errors || $this->s_category->Errors->Count());
        $errors = ($errors || $this->s_date_from->Errors->Count());
        $errors = ($errors || $this->DatePicker_s_date_from->Errors->Count());
        $errors = ($errors || $this->s_date_to->Errors->Count());
        $errors = ($errors || $this->DatePicker_s_date_to->Errors->Count());
        $errors = ($errors || $this->Errors->Count());
        return $errors;
    }
//End CheckErrors Method

//Operation Method @9-43639246
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
            $this->PressedButton = "Button_DoSearch";
            if($this->Button_DoSearch->Pressed) {
                $this->PressedButton = "Button_DoSearch";
            }
        }
        $Redirect = "profile_events.php";
        if($this->Validate()) {
            if($this->PressedButton == "Button_DoSearch") {
                $Redirect = "profile_events.php" . "?" . CCMergeQueryStrings(CCGetQueryString("Form", array("Button_DoSearch", "Button_DoSearch_x", "Button_DoSearch_y")));
                if(!CCGetEvent($this->Button_DoSearch->CCSEvents, "OnClick", $this->Button_DoSearch)) {
                    $Redirect = "";
                }
            }
        } else {
            $Redirect = "";
        }
    }
//End Operation Method

//Show Method @9-A563F5E9
    function Show()
    {
        global $Tpl;
        global $FileName;
        global $CCSLocales;
        $Error = "";

        if(!$this->Visible)
            return;

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeSelect", $this);

        $this->s_category->Prepare();

        $RecordBlock = "Record " . $this->ComponentName;
        $ParentPath = $Tpl->block_path;
        $Tpl->block_path = $ParentPath . "/" . $RecordBlock;
        $this->EditMode = $this->EditMode && $this->ReadAllowed;
        if (!$this->FormSubmitted) {
        }

        if($this->FormSubmitted || $this->CheckErrors()) {
            $Error = "";
            $Error = ComposeStrings($Error, $this->s_keyword->Errors->ToString());
            $Error = ComposeStrings($Error, $this->s_category->Errors->ToString());
            $Error = ComposeStrings($Error, $this->s_date_from->Errors->ToString());
            $Error = ComposeStrings($Error, $this->DatePicker_s_date_from->Errors->ToString());
            $Error = ComposeStrings($Error, $this->s_date_to->Errors->ToString());
            $Error = ComposeStrings($Error, $this->DatePicker_s_date_to->Errors->ToString());
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

        $this->s_keyword->Show();
        $this->s_category->Show();
        $this->s_date_from->Show();
        $this->DatePicker_s_date_from->Show();
        $this->s_date_to->Show();
        $this->DatePicker_s_date_to->Show();
        $this->Button_DoSearch->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
    }
//End Show Method

} //End events_groupsSearch Class @9-FCB6E20C

class clsGridevents_groups { //events_groups class @5-0BFCC92E

//Variables @5-FDBD27A4

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
    var $Sorter_event_date;
    var $Sorter_event_time;
    var $Sorter_event_title;
    var $Sorter_category;
//End Variables

//Class_Initialize Event @5-069B6609
    function clsGridevents_groups($RelativePath, & $Parent)
    {
        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->ComponentName = "events_groups";
        $this->Visible = True;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Grid events_groups";
        $this->DataSource = new clsevents_groupsDataSource($this);
        $this->ds = & $this->DataSource;
        $this->PageSize = CCGetParam($this->ComponentName . "PageSize", "");
        if(!is_numeric($this->PageSize) || !strlen($this->PageSize))
            $this->PageSize = 10;
        else
            $this->PageSize = intval($this->PageSize);
        if ($this->PageSize > 100)
            $this->PageSize = 100;
        if($this->PageSize == 0)
            $this->Errors->addError("<p>Form: Grid " . $this->ComponentName . "<br>Error: (CCS06) Invalid page size.</p>");
        $this->PageNumber = intval(CCGetParam($this->ComponentName . "Page", 1));
        if ($this->PageNumber <= 0) $this->PageNumber = 1;
        $this->SorterName = CCGetParam("events_groupsOrder", "");
        $this->SorterDirection = CCGetParam("events_groupsDir", "");

        $this->EditLink = & new clsControl(ccsLink, "EditLink", "EditLink", ccsText, "", CCGetRequestParam("EditLink", ccsGet, NULL), $this);
        $this->EditLink->Page = "events.php";
        $this->event_date = & new clsControl(ccsLabel, "event_date", "event_date", ccsDate, array("ShortDate"), CCGetRequestParam("event_date", ccsGet, NULL), $this);
        $this->event_time = & new clsControl(ccsLabel, "event_time", "event_time", ccsDate, array("ShortTime"), CCGetRequestParam("event_time", ccsGet, NULL), $this);
        $this->event_time_end = & new clsControl(ccsLabel, "event_time_end", "event_time_end", ccsDate, array("ShortTime"), CCGetRequestParam("event_time_end", ccsGet, NULL), $this);
        $this->event_title = & new clsControl(ccsLink, "event_title", "event_title", ccsText, "", CCGetRequestParam("event_title", ccsGet, NULL), $this);
        $this->event_title->Page = "event_view.php";
        $this->category_name = & new clsControl(ccsLabel, "category_name", "category_name", ccsText, "", CCGetRequestParam("category_name", ccsGet, NULL), $this);
        $this->events_groups_TotalRecords = & new clsControl(ccsLabel, "events_groups_TotalRecords", "events_groups_TotalRecords", ccsText, "", CCGetRequestParam("events_groups_TotalRecords", ccsGet, NULL), $this);
        $this->Sorter_event_date = & new clsSorter($this->ComponentName, "Sorter_event_date", $FileName, $this);
        $this->Sorter_event_time = & new clsSorter($this->ComponentName, "Sorter_event_time", $FileName, $this);
        $this->Sorter_event_title = & new clsSorter($this->ComponentName, "Sorter_event_title", $FileName, $this);
        $this->Sorter_category = & new clsSorter($this->ComponentName, "Sorter_category", $FileName, $this);
        $this->Navigator = & new clsNavigator($this->ComponentName, "Navigator", $FileName, 10, tpSimple, $this);
    }
//End Class_Initialize Event

//Initialize Method @5-90E704C5
    function Initialize()
    {
        if(!$this->Visible) return;

        $this->DataSource->PageSize = & $this->PageSize;
        $this->DataSource->AbsolutePage = & $this->PageNumber;
        $this->DataSource->SetOrder($this->SorterName, $this->SorterDirection);
    }
//End Initialize Method

//Show Method @5-ACE738DA
    function Show()
    {
        global $Tpl;
        global $CCSLocales;
        if(!$this->Visible) return;

        $this->RowNumber = 0;

        $this->DataSource->Parameters["urls_keyword"] = CCGetFromGet("s_keyword", NULL);
        $this->DataSource->Parameters["urls_category"] = CCGetFromGet("s_category", NULL);
        $this->DataSource->Parameters["seslocale"] = CCGetSession("locale", NULL);
        $this->DataSource->Parameters["urlcategories_langs_category_id"] = CCGetFromGet("categories_langs_category_id", NULL);
        $this->DataSource->Parameters["sesUserID"] = CCGetSession("UserID", NULL);
        $this->DataSource->Parameters["urls_date_from"] = CCGetFromGet("s_date_from", NULL);
        $this->DataSource->Parameters["urls_date_to"] = CCGetFromGet("s_date_to", NULL);

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
            $this->ControlsVisible["EditLink"] = $this->EditLink->Visible;
            $this->ControlsVisible["event_date"] = $this->event_date->Visible;
            $this->ControlsVisible["event_time"] = $this->event_time->Visible;
            $this->ControlsVisible["event_time_end"] = $this->event_time_end->Visible;
            $this->ControlsVisible["event_title"] = $this->event_title->Visible;
            $this->ControlsVisible["category_name"] = $this->category_name->Visible;
            do {
                // Parse Separator
                if($this->RowNumber)
                    $Tpl->parseto("Separator", true, "Row");
                $this->RowNumber++;
                $this->DataSource->SetValues();
                $Tpl->block_path = $ParentPath . "/" . $GridBlock . "/Row";
                $this->EditLink->Parameters = "";
                $this->EditLink->Parameters = CCAddParam($this->EditLink->Parameters, "event_id", $this->DataSource->f("event_id"));
                $this->event_date->SetValue($this->DataSource->event_date->GetValue());
                $this->event_time->SetValue($this->DataSource->event_time->GetValue());
                $this->event_time_end->SetValue($this->DataSource->event_time_end->GetValue());
                $this->event_title->SetValue($this->DataSource->event_title->GetValue());
                $this->event_title->Parameters = "";
                $this->event_title->Parameters = CCAddParam($this->event_title->Parameters, "event_id", $this->DataSource->f("event_id"));
                $this->category_name->SetValue($this->DataSource->category_name->GetValue());
                $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowRow", $this);
                $this->EditLink->Show();
                $this->event_date->Show();
                $this->event_time->Show();
                $this->event_time_end->Show();
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
        $this->Navigator->PageNumber = $this->DataSource->AbsolutePage;
        if ($this->DataSource->RecordsCount == "CCS not counted")
            $this->Navigator->TotalPages = $this->DataSource->AbsolutePage + ($this->DataSource->next_record() ? 1 : 0);
        else
            $this->Navigator->TotalPages = $this->DataSource->PageCount();
        $this->events_groups_TotalRecords->Show();
        $this->Sorter_event_date->Show();
        $this->Sorter_event_time->Show();
        $this->Sorter_event_title->Show();
        $this->Sorter_category->Show();
        $this->Navigator->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->DataSource->close();
    }
//End Show Method

//GetErrors Method @5-60C554C2
    function GetErrors()
    {
        $errors = "";
        $errors = ComposeStrings($errors, $this->EditLink->Errors->ToString());
        $errors = ComposeStrings($errors, $this->event_date->Errors->ToString());
        $errors = ComposeStrings($errors, $this->event_time->Errors->ToString());
        $errors = ComposeStrings($errors, $this->event_time_end->Errors->ToString());
        $errors = ComposeStrings($errors, $this->event_title->Errors->ToString());
        $errors = ComposeStrings($errors, $this->category_name->Errors->ToString());
        $errors = ComposeStrings($errors, $this->Errors->ToString());
        $errors = ComposeStrings($errors, $this->DataSource->Errors->ToString());
        return $errors;
    }
//End GetErrors Method

} //End events_groups Class @5-FCB6E20C

class clsevents_groupsDataSource extends clsDBcalendar {  //events_groupsDataSource Class @5-F6B07C68

//DataSource Variables @5-6A8F9921
    var $Parent = "";
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;
    var $CmdExecution;

    var $CountSQL;
    var $wp;


    // Datasource fields
    var $event_date;
    var $event_time;
    var $event_time_end;
    var $event_title;
    var $category_name;
//End DataSource Variables

//DataSourceClass_Initialize Event @5-3D92A6FA
    function clsevents_groupsDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "Grid events_groups";
        $this->Initialize();
        $this->event_date = new clsField("event_date", ccsDate, array("yyyy", "-", "mm", "-", "dd"));
        $this->event_time = new clsField("event_time", ccsDate, array("HH", ":", "nn", ":", "ss"));
        $this->event_time_end = new clsField("event_time_end", ccsDate, array("HH", ":", "nn", ":", "ss"));
        $this->event_title = new clsField("event_title", ccsText, "");
        $this->category_name = new clsField("category_name", ccsText, "");

    }
//End DataSourceClass_Initialize Event

//SetOrder Method @5-D0F5C00A
    function SetOrder($SorterName, $SorterDirection)
    {
        $this->Order = "";
        $this->Order = CCGetOrder($this->Order, $SorterName, $SorterDirection, 
            array("Sorter_event_date" => array("event_date", ""), 
            "Sorter_event_time" => array("event_time, event_time_end", "event_time desc, event_time_end desc"), 
            "Sorter_event_title" => array("event_title", ""), 
            "Sorter_category" => array("category_name", "")));
    }
//End SetOrder Method

//Prepare Method @5-94F47643
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "urls_keyword", ccsText, "", "", $this->Parameters["urls_keyword"], "", false);
        $this->wp->AddParameter("2", "urls_keyword", ccsMemo, "", "", $this->Parameters["urls_keyword"], "", false);
        $this->wp->AddParameter("3", "urls_category", ccsInteger, "", "", $this->Parameters["urls_category"], "", false);
        $this->wp->AddParameter("4", "seslocale", ccsText, "", "", $this->Parameters["seslocale"], "", false);
        $this->wp->AddParameter("5", "urlcategories_langs_category_id", ccsInteger, "", "", $this->Parameters["urlcategories_langs_category_id"], "", true);
        $this->wp->AddParameter("6", "sesUserID", ccsInteger, "", "", $this->Parameters["sesUserID"], "", false);
        $this->wp->AddParameter("7", "urls_date_from", ccsDate, $DefaultDateFormat, array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"), $this->Parameters["urls_date_from"], "", false);
        $this->wp->AddParameter("8", "urls_date_to", ccsDate, $DefaultDateFormat, array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"), $this->Parameters["urls_date_to"], "", false);
        $this->wp->Criterion[1] = $this->wp->Operation(opContains, "events.event_title", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsText),false);
        $this->wp->Criterion[2] = $this->wp->Operation(opContains, "events.event_desc", $this->wp->GetDBValue("2"), $this->ToSQL($this->wp->GetDBValue("2"), ccsMemo),false);
        $this->wp->Criterion[3] = $this->wp->Operation(opEqual, "events.category_id", $this->wp->GetDBValue("3"), $this->ToSQL($this->wp->GetDBValue("3"), ccsInteger),false);
        $this->wp->Criterion[4] = $this->wp->Operation(opEqual, "categories_langs.language_id", $this->wp->GetDBValue("4"), $this->ToSQL($this->wp->GetDBValue("4"), ccsText),false);
        $this->wp->Criterion[5] = $this->wp->Operation(opIsNull, "categories_langs.category_id", $this->wp->GetDBValue("5"), $this->ToSQL($this->wp->GetDBValue("5"), ccsInteger),true);
        $this->wp->Criterion[6] = $this->wp->Operation(opEqual, "events.user_id", $this->wp->GetDBValue("6"), $this->ToSQL($this->wp->GetDBValue("6"), ccsInteger),false);
        $this->wp->Criterion[7] = $this->wp->Operation(opGreaterThanOrEqual, "events.event_date", $this->wp->GetDBValue("7"), $this->ToSQL($this->wp->GetDBValue("7"), ccsDate),false);
        $this->wp->Criterion[8] = $this->wp->Operation(opLessThanOrEqual, "events.event_date", $this->wp->GetDBValue("8"), $this->ToSQL($this->wp->GetDBValue("8"), ccsDate),false);
        $this->Where = $this->wp->opAND(
             false, $this->wp->opAND(
             false, $this->wp->opAND(
             false, $this->wp->opAND(
             false, $this->wp->opAND(
             false, $this->wp->opOR(
             true, 
             $this->wp->Criterion[1], 
             $this->wp->Criterion[2]), 
             $this->wp->Criterion[3]), $this->wp->opOR(
             true, 
             $this->wp->Criterion[4], 
             $this->wp->Criterion[5])), 
             $this->wp->Criterion[6]), 
             $this->wp->Criterion[7]), 
             $this->wp->Criterion[8]);
    }
//End Prepare Method

//Open Method @5-83EE722A
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->CountSQL = "SELECT COUNT(*)\n\n" .
        "FROM events LEFT JOIN categories_langs ON\n\n" .
        "events.category_id = categories_langs.category_id";
        $this->SQL = "SELECT category_name, event_id, event_date, event_title, event_time, event_time_end \n\n" .
        "FROM events LEFT JOIN categories_langs ON\n\n" .
        "events.category_id = categories_langs.category_id {SQL_Where} {SQL_OrderBy}";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect", $this->Parent);
        if ($this->CountSQL) 
            $this->RecordsCount = CCGetDBValue(CCBuildSQL($this->CountSQL, $this->Where, ""), $this);
        else
            $this->RecordsCount = "CCS not counted";
        $this->query($this->OptimizeSQL(CCBuildSQL($this->SQL, $this->Where, $this->Order)));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect", $this->Parent);
    }
//End Open Method

//SetValues Method @5-FE249D81
    function SetValues()
    {
        $this->event_date->SetDBValue(trim($this->f("event_date")));
        $this->event_time->SetDBValue(trim($this->f("event_time")));
        $this->event_time_end->SetDBValue(trim($this->f("event_time_end")));
        $this->event_title->SetDBValue($this->f("event_title"));
        $this->category_name->SetDBValue($this->f("category_name"));
    }
//End SetValues Method

} //End events_groupsDataSource Class @5-FCB6E20C

//Include Page implementation @3-EBA5EA16
include_once(RelativePath . "/footer.php");
//End Include Page implementation

//Initialize Page @1-67F12980
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
$TemplateFileName = "profile_events.html";
$BlockToParse = "main";
$TemplateEncoding = "UTF-8";
$PathToRoot = "./";
//End Initialize Page

//Authenticate User @1-16021C70
CCSecurityRedirect("10;100", "");
//End Authenticate User

//Include events file @1-1694D03F
include("./profile_events_events.php");
//End Include events file

//Initialize Objects @1-4A972670
$DBcalendar = new clsDBcalendar();
$MainPage->Connections["calendar"] = & $DBcalendar;

// Controls
$header = & new clsheader("", "header", $MainPage);
$header->Initialize();
$profile_menu = & new clsprofile_menu("", "profile_menu", $MainPage);
$profile_menu->Initialize();
$events_groupsSearch = & new clsRecordevents_groupsSearch("", $MainPage);
$events_groups = & new clsGridevents_groups("", $MainPage);
$footer = & new clsfooter("", "footer", $MainPage);
$footer->Initialize();
$MainPage->header = & $header;
$MainPage->profile_menu = & $profile_menu;
$MainPage->events_groupsSearch = & $events_groupsSearch;
$MainPage->events_groups = & $events_groups;
$MainPage->footer = & $footer;
$events_groups->Initialize();

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

//Execute Components @1-D45BCAFA
$header->Operations();
$profile_menu->Operations();
$events_groupsSearch->Operation();
$footer->Operations();
//End Execute Components

//Go to destination page @1-18C177BC
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
    $DBcalendar->close();
    header("Location: " . $Redirect);
    $header->Class_Terminate();
    unset($header);
    $profile_menu->Class_Terminate();
    unset($profile_menu);
    unset($events_groupsSearch);
    unset($events_groups);
    $footer->Class_Terminate();
    unset($footer);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-A783D1C2
$header->Show();
$profile_menu->Show();
$events_groupsSearch->Show();
$events_groups->Show();
$footer->Show();
$Tpl->block_path = "";
$Tpl->Parse($BlockToParse, false);
$main_block = $Tpl->GetVar($BlockToParse);
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeOutput", $MainPage);
if ($CCSEventResult) echo $main_block;
//End Show Page

//Unload Page @1-E3BAFC57
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
$DBcalendar->close();
$header->Class_Terminate();
unset($header);
$profile_menu->Class_Terminate();
unset($profile_menu);
unset($events_groupsSearch);
unset($events_groups);
$footer->Class_Terminate();
unset($footer);
unset($Tpl);
//End Unload Page
?>
