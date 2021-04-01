<?php
//Include Common Files @1-113397D3
define("RelativePath", "..");
define("PathToCurrentPage", "/admin/");
define("FileName", "content.php");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
//End Include Common Files

//Include Page implementation @2-47CFCC1A
include_once(RelativePath . "/admin/header.php");
//End Include Page implementation

class clsGridcontents { //contents class @5-4AF0AED4

//Variables @5-663D5B8C

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

//Class_Initialize Event @5-3564AA90
    function clsGridcontents($RelativePath, & $Parent)
    {
        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->ComponentName = "contents";
        $this->Visible = True;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Grid contents";
        $this->DataSource = new clscontentsDataSource($this);
        $this->ds = & $this->DataSource;
        $this->PageSize = CCGetParam($this->ComponentName . "PageSize", "");
        if(!is_numeric($this->PageSize) || !strlen($this->PageSize))
            $this->PageSize = 50;
        else
            $this->PageSize = intval($this->PageSize);
        if ($this->PageSize > 100)
            $this->PageSize = 100;
        if($this->PageSize == 0)
            $this->Errors->addError("<p>Form: Grid " . $this->ComponentName . "<br>Error: (CCS06) Invalid page size.</p>");
        $this->PageNumber = intval(CCGetParam($this->ComponentName . "Page", 1));
        if ($this->PageNumber <= 0) $this->PageNumber = 1;

        $this->content_type = & new clsControl(ccsLink, "content_type", "content_type", ccsText, "", CCGetRequestParam("content_type", ccsGet, NULL), $this);
        $this->content_type->Page = "content.php";
        $this->content_desc = & new clsControl(ccsLabel, "content_desc", "content_desc", ccsText, "", CCGetRequestParam("content_desc", ccsGet, NULL), $this);
        $this->translations = & new clsControl(ccsLink, "translations", "translations", ccsText, "", CCGetRequestParam("translations", ccsGet, NULL), $this);
        $this->translations->Page = "content_lang.php";
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

//Show Method @5-35473570
    function Show()
    {
        global $Tpl;
        global $CCSLocales;
        if(!$this->Visible) return;

        $this->RowNumber = 0;

        $this->DataSource->Parameters["seslocale"] = CCGetSession("locale", NULL);

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
            $this->ControlsVisible["content_type"] = $this->content_type->Visible;
            $this->ControlsVisible["content_desc"] = $this->content_desc->Visible;
            $this->ControlsVisible["translations"] = $this->translations->Visible;
            do {
                $this->RowNumber++;
                $this->DataSource->SetValues();
                $Tpl->block_path = $ParentPath . "/" . $GridBlock . "/Row";
                $this->content_type->SetValue($this->DataSource->content_type->GetValue());
                $this->content_type->Parameters = CCGetQueryString("QueryString", array("ccsForm"));
                $this->content_type->Parameters = CCAddParam($this->content_type->Parameters, "content_id", $this->DataSource->f("content_lang_id"));
                $this->content_desc->SetValue($this->DataSource->content_desc->GetValue());
                $this->translations->Parameters = CCGetQueryString("QueryString", array("ccsForm"));
                $this->translations->Parameters = CCAddParam($this->translations->Parameters, "content_id", $this->DataSource->f("contents_content_id"));
                $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowRow", $this);
                $this->content_type->Show();
                $this->content_desc->Show();
                $this->translations->Show();
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
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->DataSource->close();
    }
//End Show Method

//GetErrors Method @5-2388BB58
    function GetErrors()
    {
        $errors = "";
        $errors = ComposeStrings($errors, $this->content_type->Errors->ToString());
        $errors = ComposeStrings($errors, $this->content_desc->Errors->ToString());
        $errors = ComposeStrings($errors, $this->translations->Errors->ToString());
        $errors = ComposeStrings($errors, $this->Errors->ToString());
        $errors = ComposeStrings($errors, $this->DataSource->Errors->ToString());
        return $errors;
    }
//End GetErrors Method

} //End contents Class @5-FCB6E20C

class clscontentsDataSource extends clsDBcalendar {  //contentsDataSource Class @5-80FEA92D

//DataSource Variables @5-993F1111
    var $Parent = "";
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;
    var $CmdExecution;

    var $CountSQL;
    var $wp;


    // Datasource fields
    var $content_type;
    var $content_desc;
//End DataSource Variables

//DataSourceClass_Initialize Event @5-E01F9EF3
    function clscontentsDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "Grid contents";
        $this->Initialize();
        $this->content_type = new clsField("content_type", ccsText, "");
        $this->content_desc = new clsField("content_desc", ccsText, "");

    }
//End DataSourceClass_Initialize Event

//SetOrder Method @5-9E1383D1
    function SetOrder($SorterName, $SorterDirection)
    {
        $this->Order = "";
        $this->Order = CCGetOrder($this->Order, $SorterName, $SorterDirection, 
            "");
    }
//End SetOrder Method

//Prepare Method @5-49D392AD
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "seslocale", ccsText, "", "", $this->Parameters["seslocale"], "en", false);
        $this->wp->Criterion[1] = $this->wp->Operation(opEqual, "contents_langs.language_id", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsText),false);
        $this->Where = 
             $this->wp->Criterion[1];
    }
//End Prepare Method

//Open Method @5-26993D42
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->CountSQL = "SELECT COUNT(*)\n\n" .
        "FROM contents LEFT JOIN contents_langs ON\n\n" .
        "contents.content_id = contents_langs.content_id";
        $this->SQL = "SELECT contents.content_id AS contents_content_id, content_type, contents_langs.content_desc AS contents_langs_content_desc, content_lang_id \n\n" .
        "FROM contents LEFT JOIN contents_langs ON\n\n" .
        "contents.content_id = contents_langs.content_id {SQL_Where} {SQL_OrderBy}";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect", $this->Parent);
        if ($this->CountSQL) 
            $this->RecordsCount = CCGetDBValue(CCBuildSQL($this->CountSQL, $this->Where, ""), $this);
        else
            $this->RecordsCount = "CCS not counted";
        $this->query($this->OptimizeSQL(CCBuildSQL($this->SQL, $this->Where, $this->Order)));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect", $this->Parent);
    }
//End Open Method

//SetValues Method @5-877E8B1C
    function SetValues()
    {
        $this->content_type->SetDBValue($this->f("content_type"));
        $this->content_desc->SetDBValue($this->f("contents_langs_content_desc"));
    }
//End SetValues Method

} //End contentsDataSource Class @5-FCB6E20C

class clsRecordcontents_maint { //contents_maint Class @13-A3F1C92B

//Variables @13-0DF9B1C2

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

//Class_Initialize Event @13-3793959D
    function clsRecordcontents_maint($RelativePath, & $Parent)
    {

        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->Visible = true;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Record contents_maint/Error";
        $this->DataSource = new clscontents_maintDataSource($this);
        $this->ds = & $this->DataSource;
        $this->UpdateAllowed = true;
        $this->ReadAllowed = true;
        if($this->Visible)
        {
            $this->ComponentName = "contents_maint";
            $CCSForm = split(":", CCGetFromGet("ccsForm", ""), 2);
            if(sizeof($CCSForm) == 1)
                $CCSForm[1] = "";
            list($FormName, $FormMethod) = $CCSForm;
            $this->EditMode = ($FormMethod == "Edit");
            $this->FormEnctype = "application/x-www-form-urlencoded";
            $this->FormSubmitted = ($FormName == $this->ComponentName);
            $Method = $this->FormSubmitted ? ccsPost : ccsGet;
            $this->content_type = & new clsControl(ccsLabel, "content_type", $CCSLocales->GetText("content_type"), ccsText, "", CCGetRequestParam("content_type", $Method, NULL), $this);
            $this->content_desc = & new clsControl(ccsTextBox, "content_desc", $CCSLocales->GetText("content_desc"), ccsText, "", CCGetRequestParam("content_desc", $Method, NULL), $this);
            $this->content_desc->Required = true;
            $this->content_value = & new clsControl(ccsTextArea, "content_value", $CCSLocales->GetText("content_value"), ccsMemo, "", CCGetRequestParam("content_value", $Method, NULL), $this);
            $this->content_id = & new clsControl(ccsHidden, "content_id", "content_id", ccsText, "", CCGetRequestParam("content_id", $Method, NULL), $this);
            $this->Preview = & new clsButton("Preview", $Method, $this);
            $this->Button_Update = & new clsButton("Button_Update", $Method, $this);
            $this->Button_Cancel = & new clsButton("Button_Cancel", $Method, $this);
        }
    }
//End Class_Initialize Event

//Initialize Method @13-9453BCB4
    function Initialize()
    {

        if(!$this->Visible)
            return;

        $this->DataSource->Parameters["urlcontent_id"] = CCGetFromGet("content_id", NULL);
        $this->DataSource->Parameters["seslocale"] = CCGetSession("locale", NULL);
    }
//End Initialize Method

//Validate Method @13-2DE5127B
    function Validate()
    {
        global $CCSLocales;
        $Validation = true;
        $Where = "";
        $Validation = ($this->content_desc->Validate() && $Validation);
        $Validation = ($this->content_value->Validate() && $Validation);
        $Validation = ($this->content_id->Validate() && $Validation);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate", $this);
        $Validation =  $Validation && ($this->content_desc->Errors->Count() == 0);
        $Validation =  $Validation && ($this->content_value->Errors->Count() == 0);
        $Validation =  $Validation && ($this->content_id->Errors->Count() == 0);
        return (($this->Errors->Count() == 0) && $Validation);
    }
//End Validate Method

//CheckErrors Method @13-DE8D8D83
    function CheckErrors()
    {
        $errors = false;
        $errors = ($errors || $this->content_type->Errors->Count());
        $errors = ($errors || $this->content_desc->Errors->Count());
        $errors = ($errors || $this->content_value->Errors->Count());
        $errors = ($errors || $this->content_id->Errors->Count());
        $errors = ($errors || $this->Errors->Count());
        $errors = ($errors || $this->DataSource->Errors->Count());
        return $errors;
    }
//End CheckErrors Method

//Operation Method @13-D4073383
    function Operation()
    {
        if(!$this->Visible)
            return;

        global $Redirect;
        global $FileName;

        $this->DataSource->Prepare();
        if(!$this->FormSubmitted) {
            $this->EditMode = $this->DataSource->AllParametersSet;
            return;
        }

        if($this->FormSubmitted) {
            $this->PressedButton = $this->EditMode ? "Button_Update" : "Preview";
            if($this->Preview->Pressed) {
                $this->PressedButton = "Preview";
            } else if($this->Button_Update->Pressed) {
                $this->PressedButton = "Button_Update";
            } else if($this->Button_Cancel->Pressed) {
                $this->PressedButton = "Button_Cancel";
            }
        }
        $Redirect = "content.php";
        if($this->PressedButton == "Button_Cancel") {
            if(!CCGetEvent($this->Button_Cancel->CCSEvents, "OnClick", $this->Button_Cancel)) {
                $Redirect = "";
            }
        } else if($this->Validate()) {
            if($this->PressedButton == "Preview") {
                if(!CCGetEvent($this->Preview->CCSEvents, "OnClick", $this->Preview)) {
                    $Redirect = "";
                }
            } else if($this->PressedButton == "Button_Update") {
                if(!CCGetEvent($this->Button_Update->CCSEvents, "OnClick", $this->Button_Update) || !$this->UpdateRow()) {
                    $Redirect = "";
                }
            }
        } else {
            $Redirect = "";
        }
        if ($Redirect)
            $this->DataSource->close();
    }
//End Operation Method

//UpdateRow Method @13-AA848C0B
    function UpdateRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeUpdate", $this);
        if(!$this->UpdateAllowed) return false;
        $this->DataSource->content_value->SetValue($this->content_value->GetValue(true));
        $this->DataSource->content_desc->SetValue($this->content_desc->GetValue(true));
        $this->DataSource->Update();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterUpdate", $this);
        return (!$this->CheckErrors());
    }
//End UpdateRow Method

//Show Method @13-C701C7D2
    function Show()
    {
        global $Tpl;
        global $FileName;
        global $CCSLocales;
        $Error = "";

        if(!$this->Visible)
            return;

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeSelect", $this);


        $RecordBlock = "Record " . $this->ComponentName;
        $ParentPath = $Tpl->block_path;
        $Tpl->block_path = $ParentPath . "/" . $RecordBlock;
        $this->EditMode = $this->EditMode && $this->ReadAllowed;
        if($this->EditMode) {
            if($this->DataSource->Errors->Count()){
                $this->Errors->AddErrors($this->DataSource->Errors);
                $this->DataSource->Errors->clear();
            }
            $this->DataSource->Open();
            if($this->DataSource->Errors->Count() == 0 && $this->DataSource->next_record()) {
                $this->DataSource->SetValues();
                $this->content_type->SetValue($this->DataSource->content_type->GetValue());
                if(!$this->FormSubmitted){
                    $this->content_desc->SetValue($this->DataSource->content_desc->GetValue());
                    $this->content_value->SetValue($this->DataSource->content_value->GetValue());
                    $this->content_id->SetValue($this->DataSource->content_id->GetValue());
                }
            } else {
                $this->EditMode = false;
            }
        }

        if($this->FormSubmitted || $this->CheckErrors()) {
            $Error = "";
            $Error = ComposeStrings($Error, $this->content_type->Errors->ToString());
            $Error = ComposeStrings($Error, $this->content_desc->Errors->ToString());
            $Error = ComposeStrings($Error, $this->content_value->Errors->ToString());
            $Error = ComposeStrings($Error, $this->content_id->Errors->ToString());
            $Error = ComposeStrings($Error, $this->Errors->ToString());
            $Error = ComposeStrings($Error, $this->DataSource->Errors->ToString());
            $Tpl->SetVar("Error", $Error);
            $Tpl->Parse("Error", false);
        }
        $CCSForm = $this->EditMode ? $this->ComponentName . ":" . "Edit" : $this->ComponentName;
        $this->HTMLFormAction = $FileName . "?" . CCAddParam(CCGetQueryString("QueryString", ""), "ccsForm", $CCSForm);
        $Tpl->SetVar("Action", $this->HTMLFormAction);
        $Tpl->SetVar("HTMLFormName", $this->ComponentName);
        $Tpl->SetVar("HTMLFormEnctype", $this->FormEnctype);
        $this->Button_Update->Visible = $this->EditMode && $this->UpdateAllowed;

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShow", $this);
        if(!$this->Visible) {
            $Tpl->block_path = $ParentPath;
            return;
        }

        $this->content_type->Show();
        $this->content_desc->Show();
        $this->content_value->Show();
        $this->content_id->Show();
        $this->Preview->Show();
        $this->Button_Update->Show();
        $this->Button_Cancel->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->DataSource->close();
    }
//End Show Method

} //End contents_maint Class @13-FCB6E20C

class clscontents_maintDataSource extends clsDBcalendar {  //contents_maintDataSource Class @13-0C6A8ED9

//DataSource Variables @13-237D9183
    var $Parent = "";
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;
    var $CmdExecution;

    var $UpdateParameters;
    var $wp;
    var $AllParametersSet;


    // Datasource fields
    var $content_type;
    var $content_desc;
    var $content_value;
    var $content_id;
//End DataSource Variables

//DataSourceClass_Initialize Event @13-613F37FC
    function clscontents_maintDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "Record contents_maint/Error";
        $this->Initialize();
        $this->content_type = new clsField("content_type", ccsText, "");
        $this->content_desc = new clsField("content_desc", ccsText, "");
        $this->content_value = new clsField("content_value", ccsMemo, "");
        $this->content_id = new clsField("content_id", ccsText, "");

        $this->UpdateFields["content_value"] = array("Name" => "content_value", "Value" => "", "DataType" => ccsMemo);
        $this->UpdateFields["content_desc"] = array("Name" => "content_desc", "Value" => "", "DataType" => ccsText);
    }
//End DataSourceClass_Initialize Event

//Prepare Method @13-C4F1DFF2
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "urlcontent_id", ccsInteger, "", "", $this->Parameters["urlcontent_id"], "", false);
        $this->wp->AddParameter("2", "seslocale", ccsText, "", "", $this->Parameters["seslocale"], "", false);
        $this->AllParametersSet = $this->wp->AllParamsSet();
        $this->wp->Criterion[1] = $this->wp->Operation(opEqual, "contents_langs.content_lang_id", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsInteger),false);
        $this->wp->Criterion[2] = $this->wp->Operation(opEqual, "contents_langs.language_id", $this->wp->GetDBValue("2"), $this->ToSQL($this->wp->GetDBValue("2"), ccsText),false);
        $this->Where = $this->wp->opAND(
             false, 
             $this->wp->Criterion[1], 
             $this->wp->Criterion[2]);
    }
//End Prepare Method

//Open Method @13-49A4E44F
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->SQL = "SELECT contents_langs.content_desc AS contents_langs_content_desc, contents_langs.content_value AS contents_langs_content_value, content_type,\n\n" .
        "contents_langs.content_id AS contents_langs_content_id \n\n" .
        "FROM contents_langs INNER JOIN contents ON\n\n" .
        "contents_langs.content_id = contents.content_id {SQL_Where} {SQL_OrderBy}";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect", $this->Parent);
        $this->PageSize = 1;
        $this->query($this->OptimizeSQL(CCBuildSQL($this->SQL, $this->Where, $this->Order)));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect", $this->Parent);
    }
//End Open Method

//SetValues Method @13-7E8667F4
    function SetValues()
    {
        $this->content_type->SetDBValue($this->f("content_type"));
        $this->content_desc->SetDBValue($this->f("contents_langs_content_desc"));
        $this->content_value->SetDBValue($this->f("contents_langs_content_value"));
        $this->content_id->SetDBValue($this->f("contents_langs_content_id"));
    }
//End SetValues Method

//Update Method @13-77850455
    function Update()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->CmdExecution = true;
        $this->cp["content_value"] = new clsSQLParameter("ctrlcontent_value", ccsMemo, "", "", $this->content_value->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["content_desc"] = new clsSQLParameter("ctrlcontent_desc", ccsText, "", "", $this->content_desc->GetValue(true), "", false, $this->ErrorBlock);
        $wp = new clsSQLParameters($this->ErrorBlock);
        $wp->AddParameter("1", "urlcontent_id", ccsInteger, "", "", CCGetFromGet("content_id", NULL), "", false);
        if(!$wp->AllParamsSet()) {
            $this->Errors->addError($CCSLocales->GetText("CCS_CustomOperationError_MissingParameters"));
        }
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildUpdate", $this->Parent);
        if (!strlen($this->cp["content_value"]->GetText()) and !is_bool($this->cp["content_value"]->GetValue())) 
            $this->cp["content_value"]->SetValue($this->content_value->GetValue(true));
        if (!strlen($this->cp["content_desc"]->GetText()) and !is_bool($this->cp["content_desc"]->GetValue())) 
            $this->cp["content_desc"]->SetValue($this->content_desc->GetValue(true));
        $wp->Criterion[1] = $wp->Operation(opEqual, "content_lang_id", $wp->GetDBValue("1"), $this->ToSQL($wp->GetDBValue("1"), ccsInteger),false);
        $Where = 
             $wp->Criterion[1];
        $this->UpdateFields["content_value"]["Value"] = $this->cp["content_value"]->GetDBValue(true);
        $this->UpdateFields["content_desc"]["Value"] = $this->cp["content_desc"]->GetDBValue(true);
        $this->SQL = CCBuildUpdate("contents_langs", $this->UpdateFields, $this);
        $this->SQL = CCBuildSQL($this->SQL, $Where, "");
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteUpdate", $this->Parent);
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteUpdate", $this->Parent);
        }
    }
//End Update Method

} //End contents_maintDataSource Class @13-FCB6E20C

//Initialize Page @1-7009B0A8
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
$TemplateFileName = "content.html";
$BlockToParse = "main";
$TemplateEncoding = "UTF-8";
$PathToRoot = "../";
//End Initialize Page

//Authenticate User @1-F87A9DA3
CCSecurityRedirect("100", "../login.php");
//End Authenticate User

//Include events file @1-73FDFC70
include("./content_events.php");
//End Include events file

//Initialize Objects @1-DFE5EA91
$DBcalendar = new clsDBcalendar();
$MainPage->Connections["calendar"] = & $DBcalendar;

// Controls
$header = & new clsheader("", "header", $MainPage);
$header->Initialize();
$contents = & new clsGridcontents("", $MainPage);
$contents_maint = & new clsRecordcontents_maint("", $MainPage);
$MainPage->header = & $header;
$MainPage->contents = & $contents;
$MainPage->contents_maint = & $contents_maint;
$contents->Initialize();
$contents_maint->Initialize();

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

//Execute Components @1-A93D694D
$header->Operations();
$contents_maint->Operation();
//End Execute Components

//Go to destination page @1-7521578E
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
    $DBcalendar->close();
    header("Location: " . $Redirect);
    $header->Class_Terminate();
    unset($header);
    unset($contents);
    unset($contents_maint);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-9C576A5E
$header->Show();
$contents->Show();
$contents_maint->Show();
$Tpl->block_path = "";
$Tpl->Parse($BlockToParse, false);
$main_block = $Tpl->GetVar($BlockToParse);
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeOutput", $MainPage);
if ($CCSEventResult) echo $main_block;
//End Show Page

//Unload Page @1-FFA39403
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
$DBcalendar->close();
$header->Class_Terminate();
unset($header);
unset($contents);
unset($contents_maint);
unset($Tpl);
//End Unload Page


?>
