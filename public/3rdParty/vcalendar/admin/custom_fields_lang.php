<?php
//Include Common Files @1-7A58D704
define("RelativePath", "..");
define("PathToCurrentPage", "/admin/");
define("FileName", "custom_fields_lang.php");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
//End Include Common Files

class clsEditableGridcustom_fields_langs { //custom_fields_langs Class @5-E8EC0709

//Variables @5-8F7068D1

    // Public variables
    var $ComponentType = "EditableGrid";
    var $ComponentName;
    var $HTMLFormAction;
    var $PressedButton;
    var $Errors;
    var $ErrorBlock;
    var $FormSubmitted;
    var $FormParameters;
    var $FormState;
    var $FormEnctype;
    var $CachedColumns;
    var $TotalRows;
    var $UpdatedRows;
    var $EmptyRows;
    var $Visible;
    var $RowsErrors;
    var $ds;
    var $DataSource;
    var $PageSize;
    var $IsEmpty;
    var $SorterName = "";
    var $SorterDirection = "";
    var $PageNumber;
    var $ControlsVisible = array();

    var $CCSEvents = "";
    var $CCSEventResult;

    var $RelativePath = "";

    var $InsertAllowed = false;
    var $UpdateAllowed = false;
    var $DeleteAllowed = false;
    var $ReadAllowed   = false;
    var $EditMode;
    var $ValidatingControls;
    var $Controls;
    var $ControlsErrors;
    var $RowNumber;

    // Class variables
//End Variables

//Class_Initialize Event @5-CDCDDCFC
    function clsEditableGridcustom_fields_langs($RelativePath, & $Parent)
    {

        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->Visible = true;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "EditableGrid custom_fields_langs/Error";
        $this->ControlsErrors = array();
        $this->ComponentName = "custom_fields_langs";
        $this->CachedColumns["field_lang_id"][0] = "field_lang_id";
        $this->DataSource = new clscustom_fields_langsDataSource($this);
        $this->ds = & $this->DataSource;
        $this->PageSize = CCGetParam($this->ComponentName . "PageSize", "");
        if(!is_numeric($this->PageSize) || !strlen($this->PageSize))
            $this->PageSize = 10;
        else
            $this->PageSize = intval($this->PageSize);
        if ($this->PageSize > 100)
            $this->PageSize = 100;
        if($this->PageSize == 0)
            $this->Errors->addError("<p>Form: EditableGrid " . $this->ComponentName . "<br>Error: (CCS06) Invalid page size.</p>");
        $this->PageNumber = intval(CCGetParam($this->ComponentName . "Page", 1));
        if ($this->PageNumber <= 0) $this->PageNumber = 1;

        $this->EmptyRows = 0;
        $this->UpdateAllowed = true;
        $this->ReadAllowed = true;
        if(!$this->Visible) return;

        $CCSForm = CCGetFromGet("ccsForm", "");
        $this->FormEnctype = "application/x-www-form-urlencoded";
        $this->FormSubmitted = ($CCSForm == $this->ComponentName);
        if($this->FormSubmitted) {
            $this->FormState = CCGetFromPost("FormState", "");
            $this->SetFormState($this->FormState);
        } else {
            $this->FormState = "";
        }
        $Method = $this->FormSubmitted ? ccsPost : ccsGet;

        $this->languageLabel = & new clsControl(ccsLabel, "languageLabel", $CCSLocales->GetText("field_trans_lang"), ccsText, "", NULL, $this);
        $this->language_id = & new clsControl(ccsHidden, "language_id", "language_id", ccsText, "", NULL, $this);
        $this->field_translation = & new clsControl(ccsTextBox, "field_translation", $CCSLocales->GetText("field_translation"), ccsText, "", NULL, $this);
        $this->Button_Submit = & new clsButton("Button_Submit", $Method, $this);
    }
//End Class_Initialize Event

//Initialize Method @5-0A281781
    function Initialize()
    {
        if(!$this->Visible) return;

        $this->DataSource->PageSize = & $this->PageSize;
        $this->DataSource->AbsolutePage = & $this->PageNumber;
        $this->DataSource->SetOrder($this->SorterName, $this->SorterDirection);

        $this->DataSource->Parameters["urlfield_id"] = CCGetFromGet("field_id", NULL);
    }
//End Initialize Method

//GetFormParameters Method @5-B1791F19
    function GetFormParameters()
    {
        for($RowNumber = 1; $RowNumber <= $this->TotalRows; $RowNumber++)
        {
            $this->FormParameters["language_id"][$RowNumber] = CCGetFromPost("language_id_" . $RowNumber, NULL);
            $this->FormParameters["field_translation"][$RowNumber] = CCGetFromPost("field_translation_" . $RowNumber, NULL);
        }
    }
//End GetFormParameters Method

//Validate Method @5-9AC0DC96
    function Validate()
    {
        $Validation = true;
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate", $this);

        for($this->RowNumber = 1; $this->RowNumber <= $this->TotalRows; $this->RowNumber++)
        {
            $this->DataSource->CachedColumns["field_lang_id"] = $this->CachedColumns["field_lang_id"][$this->RowNumber];
            $this->DataSource->CurrentRow = $this->RowNumber;
            $this->language_id->SetText($this->FormParameters["language_id"][$this->RowNumber], $this->RowNumber);
            $this->field_translation->SetText($this->FormParameters["field_translation"][$this->RowNumber], $this->RowNumber);
            if ($this->UpdatedRows >= $this->RowNumber) {
                $Validation = ($this->ValidateRow($this->RowNumber) && $Validation);
            }
            else if($this->CheckInsert())
            {
                $Validation = ($this->ValidateRow() && $Validation);
            }
        }
        return (($this->Errors->Count() == 0) && $Validation);
    }
//End Validate Method

//ValidateRow Method @5-8FBB2DA4
    function ValidateRow()
    {
        global $CCSLocales;
        $this->language_id->Validate();
        $this->field_translation->Validate();
        $this->RowErrors = new clsErrors();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidateRow", $this);
        $errors = "";
        $errors = ComposeStrings($errors, $this->language_id->Errors->ToString());
        $errors = ComposeStrings($errors, $this->field_translation->Errors->ToString());
        $this->language_id->Errors->Clear();
        $this->field_translation->Errors->Clear();
        $errors = ComposeStrings($errors, $this->RowErrors->ToString());
        $this->RowsErrors[$this->RowNumber] = $errors;
        return $errors != "" ? 0 : 1;
    }
//End ValidateRow Method

//CheckInsert Method @5-8F8C1D5E
    function CheckInsert()
    {
        $filed = false;
        $filed = ($filed || strlen($this->FormParameters["language_id"][$this->RowNumber]));
        $filed = ($filed || strlen($this->FormParameters["field_translation"][$this->RowNumber]));
        return $filed;
    }
//End CheckInsert Method

//CheckErrors Method @5-F5A3B433
    function CheckErrors()
    {
        $errors = false;
        $errors = ($errors || $this->Errors->Count());
        $errors = ($errors || $this->DataSource->Errors->Count());
        return $errors;
    }
//End CheckErrors Method

//Operation Method @5-499CA21D
    function Operation()
    {
        if(!$this->Visible)
            return;

        global $Redirect;
        global $FileName;

        $this->DataSource->Prepare();
        if(!$this->FormSubmitted)
            return;

        $this->GetFormParameters();
        $this->PressedButton = "Button_Submit";
        if($this->Button_Submit->Pressed) {
            $this->PressedButton = "Button_Submit";
        }

        $Redirect = $FileName;
        if($this->PressedButton == "Button_Submit") {
            if(!CCGetEvent($this->Button_Submit->CCSEvents, "OnClick", $this->Button_Submit) || !$this->UpdateGrid()) {
                $Redirect = "";
            }
        } else {
            $Redirect = "";
        }
        if ($Redirect)
            $this->DataSource->close();
    }
//End Operation Method

//UpdateGrid Method @5-2D7227B5
    function UpdateGrid()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeSubmit", $this);
        if(!$this->Validate()) return;
        $Validation = true;
        for($this->RowNumber = 1; $this->RowNumber <= $this->TotalRows; $this->RowNumber++)
        {
            $this->DataSource->CachedColumns["field_lang_id"] = $this->CachedColumns["field_lang_id"][$this->RowNumber];
            $this->DataSource->CurrentRow = $this->RowNumber;
            $this->language_id->SetText($this->FormParameters["language_id"][$this->RowNumber], $this->RowNumber);
            $this->field_translation->SetText($this->FormParameters["field_translation"][$this->RowNumber], $this->RowNumber);
            if ($this->UpdatedRows >= $this->RowNumber) {
                if($this->UpdateAllowed) { $Validation = ($this->UpdateRow() && $Validation); }
            }
            else if($this->CheckInsert() && $this->InsertAllowed)
            {
                $Validation = ($Validation && $this->InsertRow());
            }
        }
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterSubmit", $this);
        if ($this->Errors->Count() == 0 && $Validation){
            $this->DataSource->close();
            return true;
        }
        return false;
    }
//End UpdateGrid Method

//UpdateRow Method @5-74BC2F34
    function UpdateRow()
    {
        if(!$this->UpdateAllowed) return false;
        $this->DataSource->languageLabel->SetValue($this->languageLabel->GetValue(true));
        $this->DataSource->language_id->SetValue($this->language_id->GetValue(true));
        $this->DataSource->field_translation->SetValue($this->field_translation->GetValue(true));
        $this->DataSource->Update();
        $errors = "";
        if($this->DataSource->Errors->Count() > 0) {
            $errors = $this->DataSource->Errors->ToString();
            $this->RowsErrors[$this->RowNumber] = $errors;
            $this->DataSource->Errors->Clear();
        }
        return (($this->Errors->Count() == 0) && !strlen($errors));
    }
//End UpdateRow Method

//FormScript Method @5-59800DB5
    function FormScript($TotalRows)
    {
        $script = "";
        return $script;
    }
//End FormScript Method

//SetFormState Method @5-EEC6C73B
    function SetFormState($FormState)
    {
        if(strlen($FormState)) {
            $FormState = str_replace("\\\\", "\\" . ord("\\"), $FormState);
            $FormState = str_replace("\\;", "\\" . ord(";"), $FormState);
            $pieces = explode(";", $FormState);
            $this->UpdatedRows = $pieces[0];
            $this->EmptyRows   = $pieces[1];
            $this->TotalRows = $this->UpdatedRows + $this->EmptyRows;
            $RowNumber = 0;
            for($i = 2; $i < sizeof($pieces); $i = $i + 1)  {
                $piece = $pieces[$i + 0];
                $piece = str_replace("\\" . ord("\\"), "\\", $piece);
                $piece = str_replace("\\" . ord(";"), ";", $piece);
                $this->CachedColumns["field_lang_id"][$RowNumber] = $piece;
                $RowNumber++;
            }

            if(!$RowNumber) { $RowNumber = 1; }
            for($i = 1; $i <= $this->EmptyRows; $i++) {
                $this->CachedColumns["field_lang_id"][$RowNumber] = "";
                $RowNumber++;
            }
        }
    }
//End SetFormState Method

//GetFormState Method @5-8CF8C60E
    function GetFormState($NonEmptyRows)
    {
        if(!$this->FormSubmitted) {
            $this->FormState  = $NonEmptyRows . ";";
            $this->FormState .= $this->InsertAllowed ? $this->EmptyRows : "0";
            if($NonEmptyRows) {
                for($i = 0; $i <= $NonEmptyRows; $i++) {
                    $this->FormState .= ";" . str_replace(";", "\\;", str_replace("\\", "\\\\", $this->CachedColumns["field_lang_id"][$i]));
                }
            }
        }
        return $this->FormState;
    }
//End GetFormState Method

//Show Method @5-0A15548F
    function Show()
    {
        global $Tpl;
        global $FileName;
        global $CCSLocales;
        $Error = "";

        if(!$this->Visible) { return; }

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeSelect", $this);


        $this->DataSource->open();
        $is_next_record = ($this->ReadAllowed && $this->DataSource->next_record());
        $this->IsEmpty = ! $is_next_record;

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShow", $this);
        if(!$this->Visible) { return; }

        $this->Button_Submit->Visible = $this->Button_Submit->Visible && ($this->InsertAllowed || $this->UpdateAllowed || $this->DeleteAllowed);
        $ParentPath = $Tpl->block_path;
        $EditableGridPath = $ParentPath . "/EditableGrid " . $this->ComponentName;
        $EditableGridRowPath = $ParentPath . "/EditableGrid " . $this->ComponentName . "/Row";
        $Tpl->block_path = $EditableGridRowPath;
        $this->RowNumber = 0;
        $NonEmptyRows = 0;
        $EmptyRowsLeft = $this->EmptyRows;
            $this->ControlsVisible["languageLabel"] = $this->languageLabel->Visible;
            $this->ControlsVisible["language_id"] = $this->language_id->Visible;
            $this->ControlsVisible["field_translation"] = $this->field_translation->Visible;
        if ($is_next_record || ($EmptyRowsLeft && $this->InsertAllowed)) {
            do {
                $this->RowNumber++;
                if($is_next_record) {
                    $NonEmptyRows++;
                    $this->DataSource->SetValues();
                }
                if (!($this->FormSubmitted) && $is_next_record) {
                    $this->CachedColumns["field_lang_id"][$this->RowNumber] = $this->DataSource->CachedColumns["field_lang_id"];
                    $this->languageLabel->SetValue($this->DataSource->languageLabel->GetValue());
                    $this->language_id->SetValue($this->DataSource->language_id->GetValue());
                    $this->field_translation->SetValue($this->DataSource->field_translation->GetValue());
                } elseif ($this->FormSubmitted && $is_next_record) {
                    $this->languageLabel->SetText("");
                    $this->languageLabel->SetValue($this->DataSource->languageLabel->GetValue());
                    $this->language_id->SetText($this->FormParameters["language_id"][$this->RowNumber], $this->RowNumber);
                    $this->field_translation->SetText($this->FormParameters["field_translation"][$this->RowNumber], $this->RowNumber);
                } elseif (!$this->FormSubmitted) {
                    $this->CachedColumns["field_lang_id"][$this->RowNumber] = "";
                    $this->languageLabel->SetText("");
                    $this->language_id->SetText("");
                    $this->field_translation->SetText("");
                } else {
                    $this->languageLabel->SetText("");
                    $this->language_id->SetText($this->FormParameters["language_id"][$this->RowNumber], $this->RowNumber);
                    $this->field_translation->SetText($this->FormParameters["field_translation"][$this->RowNumber], $this->RowNumber);
                }
                $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowRow", $this);
                $this->languageLabel->Show($this->RowNumber);
                $this->language_id->Show($this->RowNumber);
                $this->field_translation->Show($this->RowNumber);
                if (isset($this->RowsErrors[$this->RowNumber]) && ($this->RowsErrors[$this->RowNumber] != "")) {
                    $Tpl->setblockvar("RowError", "");
                    $Tpl->setvar("Error", $this->RowsErrors[$this->RowNumber]);
                    $Tpl->parse("RowError", false);
                } else {
                    $Tpl->setblockvar("RowError", "");
                }
                $Tpl->setvar("FormScript", $this->FormScript($this->RowNumber));
                $Tpl->parse();
                if ($is_next_record) {
                    if ($this->FormSubmitted) {
                        $is_next_record = $this->RowNumber < $this->UpdatedRows;
                        if (($this->DataSource->CachedColumns["field_lang_id"] == $this->CachedColumns["field_lang_id"][$this->RowNumber])) {
                            if ($this->ReadAllowed) $this->DataSource->next_record();
                        }
                    }else{
                        $is_next_record = ($this->RowNumber < $this->PageSize) &&  $this->ReadAllowed && $this->DataSource->next_record();
                    }
                } else { 
                    $EmptyRowsLeft--;
                }
            } while($is_next_record || ($EmptyRowsLeft && $this->InsertAllowed));
        } else {
            $Tpl->block_path = $EditableGridPath;
            $Tpl->parse("NoRecords", false);
        }

        $Tpl->block_path = $EditableGridPath;
        $this->Button_Submit->Show();

        if($this->CheckErrors()) {
            $Error = ComposeStrings($Error, $this->Errors->ToString());
            $Error = ComposeStrings($Error, $this->DataSource->Errors->ToString());
            $Tpl->SetVar("Error", $Error);
            $Tpl->Parse("Error", false);
        }
        $CCSForm = $this->ComponentName;
        $this->HTMLFormAction = $FileName . "?" . CCAddParam(CCGetQueryString("QueryString", ""), "ccsForm", $CCSForm);
        $Tpl->SetVar("Action", $this->HTMLFormAction);
        $Tpl->SetVar("HTMLFormName", $this->ComponentName);
        $Tpl->SetVar("HTMLFormEnctype", $this->FormEnctype);
        $Tpl->SetVar("HTMLFormProperties", "method=\"POST\" action=\"" . $this->HTMLFormAction . "\" name=\"" . $this->ComponentName . "\"");
        $Tpl->SetVar("FormState", CCToHTML($this->GetFormState($NonEmptyRows)));
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->DataSource->close();
    }
//End Show Method

} //End custom_fields_langs Class @5-FCB6E20C

class clscustom_fields_langsDataSource extends clsDBcalendar {  //custom_fields_langsDataSource Class @5-08DD0DF7

//DataSource Variables @5-BF9B725A
    var $Parent = "";
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;
    var $CmdExecution;

    var $UpdateParameters;
    var $CountSQL;
    var $wp;
    var $AllParametersSet;

    var $CachedColumns;

    // Datasource fields
    var $languageLabel;
    var $language_id;
    var $field_translation;
    var $CurrentRow;
//End DataSource Variables

//DataSourceClass_Initialize Event @5-8AAFC9DF
    function clscustom_fields_langsDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "EditableGrid custom_fields_langs/Error";
        $this->Initialize();
        $this->languageLabel = new clsField("languageLabel", ccsText, "");
        $this->language_id = new clsField("language_id", ccsText, "");
        $this->field_translation = new clsField("field_translation", ccsText, "");

        $this->UpdateFields["language_id"] = array("Name" => "language_id", "Value" => "", "DataType" => ccsText);
        $this->UpdateFields["field_label"] = array("Name" => "field_label", "Value" => "", "DataType" => ccsText);
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

//Prepare Method @5-5D76CD98
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "urlfield_id", ccsInteger, "", "", $this->Parameters["urlfield_id"], "", true);
        $this->AllParametersSet = $this->wp->AllParamsSet();
        $this->wp->Criterion[1] = $this->wp->Operation(opEqual, "field_id", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsInteger),true);
        $this->Where = 
             $this->wp->Criterion[1];
    }
//End Prepare Method

//Open Method @5-ED4008AE
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->CountSQL = "SELECT COUNT(*)\n\n" .
        "FROM custom_fields_langs";
        $this->SQL = "SELECT *, field_lang_id \n\n" .
        "FROM custom_fields_langs {SQL_Where} {SQL_OrderBy}";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect", $this->Parent);
        if ($this->CountSQL) 
            $this->RecordsCount = CCGetDBValue(CCBuildSQL($this->CountSQL, $this->Where, ""), $this);
        else
            $this->RecordsCount = "CCS not counted";
        $this->query($this->OptimizeSQL(CCBuildSQL($this->SQL, $this->Where, $this->Order)));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect", $this->Parent);
    }
//End Open Method

//SetValues Method @5-B9D91B93
    function SetValues()
    {
        $this->CachedColumns["field_lang_id"] = $this->f("field_lang_id");
        $this->languageLabel->SetDBValue($this->f("language_id"));
        $this->language_id->SetDBValue($this->f("language_id"));
        $this->field_translation->SetDBValue($this->f("field_label"));
    }
//End SetValues Method

//Update Method @5-C0FF33EF
    function Update()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->CmdExecution = true;
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildUpdate", $this->Parent);
        $SelectWhere = $this->Where;
        $this->Where = "field_lang_id=" . $this->ToSQL($this->CachedColumns["field_lang_id"], ccsInteger);
        $this->UpdateFields["language_id"]["Value"] = $this->language_id->GetDBValue(true);
        $this->UpdateFields["field_label"]["Value"] = $this->field_translation->GetDBValue(true);
        $this->SQL = CCBuildUpdate("custom_fields_langs", $this->UpdateFields, $this);
        $this->SQL = CCBuildSQL($this->SQL, $this->Where, "");
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteUpdate", $this->Parent);
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteUpdate", $this->Parent);
        }
        $this->Where = $SelectWhere;
    }
//End Update Method

} //End custom_fields_langsDataSource Class @5-FCB6E20C

//Initialize Page @1-C4BADF31
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
$TemplateFileName = "custom_fields_lang.html";
$BlockToParse = "main";
$TemplateEncoding = "UTF-8";
$PathToRoot = "../";
//End Initialize Page

//Authenticate User @1-132EF5B6
CCSecurityRedirect("100", "");
//End Authenticate User

//Include events file @1-2598088D
include("./custom_fields_lang_events.php");
//End Include events file

//Initialize Objects @1-7AE1A1F9
$DBcalendar = new clsDBcalendar();
$MainPage->Connections["calendar"] = & $DBcalendar;

// Controls
$JavaScriptLabel = & new clsControl(ccsLabel, "JavaScriptLabel", "JavaScriptLabel", ccsText, "", CCGetRequestParam("JavaScriptLabel", ccsGet, NULL), $MainPage);
$JavaScriptLabel->HTML = true;
$custom_fields_langs = & new clsEditableGridcustom_fields_langs("", $MainPage);
$MainPage->JavaScriptLabel = & $JavaScriptLabel;
$MainPage->custom_fields_langs = & $custom_fields_langs;
$custom_fields_langs->Initialize();

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

//Execute Components @1-D94B3C1D
$custom_fields_langs->Operation();
//End Execute Components

//Go to destination page @1-CB4AEFC5
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
    $DBcalendar->close();
    header("Location: " . $Redirect);
    unset($custom_fields_langs);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-511D1217
$custom_fields_langs->Show();
$JavaScriptLabel->Show();
$Tpl->block_path = "";
$Tpl->Parse($BlockToParse, false);
$main_block = $Tpl->GetVar($BlockToParse);
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeOutput", $MainPage);
if ($CCSEventResult) echo $main_block;
//End Show Page

//Unload Page @1-9DFD4B73
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
$DBcalendar->close();
unset($custom_fields_langs);
unset($Tpl);
//End Unload Page


?>
