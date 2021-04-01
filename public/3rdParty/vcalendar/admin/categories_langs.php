<?php
//Include Common Files @1-D676E80D
define("RelativePath", "..");
define("PathToCurrentPage", "/admin/");
define("FileName", "categories_langs.php");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
//End Include Common Files

class clsEditableGridcategories_langs { //categories_langs Class @3-66CCC0C0

//Variables @3-8F7068D1

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

//Class_Initialize Event @3-7EF74354
    function clsEditableGridcategories_langs($RelativePath, & $Parent)
    {

        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->Visible = true;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "EditableGrid categories_langs/Error";
        $this->ControlsErrors = array();
        $this->ComponentName = "categories_langs";
        $this->CachedColumns["category_lang_id"][0] = "category_lang_id";
        $this->DataSource = new clscategories_langsDataSource($this);
        $this->ds = & $this->DataSource;
        $this->PageSize = CCGetParam($this->ComponentName . "PageSize", "");
        if(!is_numeric($this->PageSize) || !strlen($this->PageSize))
            $this->PageSize = 50;
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

        $this->languageLabel = & new clsControl(ccsLabel, "languageLabel", $CCSLocales->GetText("group_trans_lang"), ccsText, "", NULL, $this);
        $this->language_id = & new clsControl(ccsHidden, "language_id", "language_id", ccsText, "", NULL, $this);
        $this->category_name = & new clsControl(ccsTextBox, "category_name", $CCSLocales->GetText("category_translation"), ccsText, "", NULL, $this);
        $this->category_name->Required = true;
        $this->Button_Submit = & new clsButton("Button_Submit", $Method, $this);
    }
//End Class_Initialize Event

//Initialize Method @3-44642BBC
    function Initialize()
    {
        if(!$this->Visible) return;

        $this->DataSource->PageSize = & $this->PageSize;
        $this->DataSource->AbsolutePage = & $this->PageNumber;
        $this->DataSource->SetOrder($this->SorterName, $this->SorterDirection);

        $this->DataSource->Parameters["urlcategory_id"] = CCGetFromGet("category_id", NULL);
    }
//End Initialize Method

//GetFormParameters Method @3-C7FFE753
    function GetFormParameters()
    {
        for($RowNumber = 1; $RowNumber <= $this->TotalRows; $RowNumber++)
        {
            $this->FormParameters["language_id"][$RowNumber] = CCGetFromPost("language_id_" . $RowNumber, NULL);
            $this->FormParameters["category_name"][$RowNumber] = CCGetFromPost("category_name_" . $RowNumber, NULL);
        }
    }
//End GetFormParameters Method

//Validate Method @3-F231FE7D
    function Validate()
    {
        $Validation = true;
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate", $this);

        for($this->RowNumber = 1; $this->RowNumber <= $this->TotalRows; $this->RowNumber++)
        {
            $this->DataSource->CachedColumns["category_lang_id"] = $this->CachedColumns["category_lang_id"][$this->RowNumber];
            $this->DataSource->CurrentRow = $this->RowNumber;
            $this->language_id->SetText($this->FormParameters["language_id"][$this->RowNumber], $this->RowNumber);
            $this->category_name->SetText($this->FormParameters["category_name"][$this->RowNumber], $this->RowNumber);
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

//ValidateRow Method @3-5E2BE89F
    function ValidateRow()
    {
        global $CCSLocales;
        $this->language_id->Validate();
        $this->category_name->Validate();
        $this->RowErrors = new clsErrors();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidateRow", $this);
        $errors = "";
        $errors = ComposeStrings($errors, $this->language_id->Errors->ToString());
        $errors = ComposeStrings($errors, $this->category_name->Errors->ToString());
        $this->language_id->Errors->Clear();
        $this->category_name->Errors->Clear();
        $errors = ComposeStrings($errors, $this->RowErrors->ToString());
        $this->RowsErrors[$this->RowNumber] = $errors;
        return $errors != "" ? 0 : 1;
    }
//End ValidateRow Method

//CheckInsert Method @3-D2029FAA
    function CheckInsert()
    {
        $filed = false;
        $filed = ($filed || strlen($this->FormParameters["language_id"][$this->RowNumber]));
        $filed = ($filed || strlen($this->FormParameters["category_name"][$this->RowNumber]));
        return $filed;
    }
//End CheckInsert Method

//CheckErrors Method @3-F5A3B433
    function CheckErrors()
    {
        $errors = false;
        $errors = ($errors || $this->Errors->Count());
        $errors = ($errors || $this->DataSource->Errors->Count());
        return $errors;
    }
//End CheckErrors Method

//Operation Method @3-0904867A
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

        $Redirect = "categories_langs.php";
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

//UpdateGrid Method @3-9320F07D
    function UpdateGrid()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeSubmit", $this);
        if(!$this->Validate()) return;
        $Validation = true;
        for($this->RowNumber = 1; $this->RowNumber <= $this->TotalRows; $this->RowNumber++)
        {
            $this->DataSource->CachedColumns["category_lang_id"] = $this->CachedColumns["category_lang_id"][$this->RowNumber];
            $this->DataSource->CurrentRow = $this->RowNumber;
            $this->language_id->SetText($this->FormParameters["language_id"][$this->RowNumber], $this->RowNumber);
            $this->category_name->SetText($this->FormParameters["category_name"][$this->RowNumber], $this->RowNumber);
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

//UpdateRow Method @3-57FF3AB0
    function UpdateRow()
    {
        if(!$this->UpdateAllowed) return false;
        $this->DataSource->languageLabel->SetValue($this->languageLabel->GetValue(true));
        $this->DataSource->language_id->SetValue($this->language_id->GetValue(true));
        $this->DataSource->category_name->SetValue($this->category_name->GetValue(true));
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

//FormScript Method @3-59800DB5
    function FormScript($TotalRows)
    {
        $script = "";
        return $script;
    }
//End FormScript Method

//SetFormState Method @3-946FEBC6
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
                $this->CachedColumns["category_lang_id"][$RowNumber] = $piece;
                $RowNumber++;
            }

            if(!$RowNumber) { $RowNumber = 1; }
            for($i = 1; $i <= $this->EmptyRows; $i++) {
                $this->CachedColumns["category_lang_id"][$RowNumber] = "";
                $RowNumber++;
            }
        }
    }
//End SetFormState Method

//GetFormState Method @3-507D3E44
    function GetFormState($NonEmptyRows)
    {
        if(!$this->FormSubmitted) {
            $this->FormState  = $NonEmptyRows . ";";
            $this->FormState .= $this->InsertAllowed ? $this->EmptyRows : "0";
            if($NonEmptyRows) {
                for($i = 0; $i <= $NonEmptyRows; $i++) {
                    $this->FormState .= ";" . str_replace(";", "\\;", str_replace("\\", "\\\\", $this->CachedColumns["category_lang_id"][$i]));
                }
            }
        }
        return $this->FormState;
    }
//End GetFormState Method

//Show Method @3-084B7738
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
            $this->ControlsVisible["category_name"] = $this->category_name->Visible;
        if ($is_next_record || ($EmptyRowsLeft && $this->InsertAllowed)) {
            do {
                $this->RowNumber++;
                if($is_next_record) {
                    $NonEmptyRows++;
                    $this->DataSource->SetValues();
                }
                if (!($this->FormSubmitted) && $is_next_record) {
                    $this->CachedColumns["category_lang_id"][$this->RowNumber] = $this->DataSource->CachedColumns["category_lang_id"];
                    $this->languageLabel->SetValue($this->DataSource->languageLabel->GetValue());
                    $this->language_id->SetValue($this->DataSource->language_id->GetValue());
                    $this->category_name->SetValue($this->DataSource->category_name->GetValue());
                } elseif ($this->FormSubmitted && $is_next_record) {
                    $this->languageLabel->SetText("");
                    $this->languageLabel->SetValue($this->DataSource->languageLabel->GetValue());
                    $this->language_id->SetText($this->FormParameters["language_id"][$this->RowNumber], $this->RowNumber);
                    $this->category_name->SetText($this->FormParameters["category_name"][$this->RowNumber], $this->RowNumber);
                } elseif (!$this->FormSubmitted) {
                    $this->CachedColumns["category_lang_id"][$this->RowNumber] = "";
                    $this->languageLabel->SetText("");
                    $this->language_id->SetText("");
                    $this->category_name->SetText("");
                } else {
                    $this->languageLabel->SetText("");
                    $this->language_id->SetText($this->FormParameters["language_id"][$this->RowNumber], $this->RowNumber);
                    $this->category_name->SetText($this->FormParameters["category_name"][$this->RowNumber], $this->RowNumber);
                }
                $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowRow", $this);
                $this->languageLabel->Show($this->RowNumber);
                $this->language_id->Show($this->RowNumber);
                $this->category_name->Show($this->RowNumber);
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
                        if (($this->DataSource->CachedColumns["category_lang_id"] == $this->CachedColumns["category_lang_id"][$this->RowNumber])) {
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

} //End categories_langs Class @3-FCB6E20C

class clscategories_langsDataSource extends clsDBcalendar {  //categories_langsDataSource Class @3-6CF8DC08

//DataSource Variables @3-304F50B2
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
    var $category_name;
    var $CurrentRow;
//End DataSource Variables

//DataSourceClass_Initialize Event @3-FF11BEC1
    function clscategories_langsDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "EditableGrid categories_langs/Error";
        $this->Initialize();
        $this->languageLabel = new clsField("languageLabel", ccsText, "");
        $this->language_id = new clsField("language_id", ccsText, "");
        $this->category_name = new clsField("category_name", ccsText, "");

        $this->UpdateFields["language_id"] = array("Name" => "language_id", "Value" => "", "DataType" => ccsText);
        $this->UpdateFields["category_name"] = array("Name" => "category_name", "Value" => "", "DataType" => ccsText);
    }
//End DataSourceClass_Initialize Event

//SetOrder Method @3-9E1383D1
    function SetOrder($SorterName, $SorterDirection)
    {
        $this->Order = "";
        $this->Order = CCGetOrder($this->Order, $SorterName, $SorterDirection, 
            "");
    }
//End SetOrder Method

//Prepare Method @3-F0953F71
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "urlcategory_id", ccsInteger, "", "", $this->Parameters["urlcategory_id"], "", false);
        $this->AllParametersSet = $this->wp->AllParamsSet();
        $this->wp->Criterion[1] = $this->wp->Operation(opEqual, "category_id", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsInteger),false);
        $this->Where = 
             $this->wp->Criterion[1];
    }
//End Prepare Method

//Open Method @3-08DD43F6
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->CountSQL = "SELECT COUNT(*)\n\n" .
        "FROM categories_langs";
        $this->SQL = "SELECT *, category_lang_id \n\n" .
        "FROM categories_langs {SQL_Where} {SQL_OrderBy}";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect", $this->Parent);
        if ($this->CountSQL) 
            $this->RecordsCount = CCGetDBValue(CCBuildSQL($this->CountSQL, $this->Where, ""), $this);
        else
            $this->RecordsCount = "CCS not counted";
        $this->query($this->OptimizeSQL(CCBuildSQL($this->SQL, $this->Where, $this->Order)));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect", $this->Parent);
    }
//End Open Method

//SetValues Method @3-D8367185
    function SetValues()
    {
        $this->CachedColumns["category_lang_id"] = $this->f("category_lang_id");
        $this->languageLabel->SetDBValue($this->f("language_id"));
        $this->language_id->SetDBValue($this->f("language_id"));
        $this->category_name->SetDBValue($this->f("category_name"));
    }
//End SetValues Method

//Update Method @3-DB978EE2
    function Update()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->CmdExecution = true;
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildUpdate", $this->Parent);
        $SelectWhere = $this->Where;
        $this->Where = "category_lang_id=" . $this->ToSQL($this->CachedColumns["category_lang_id"], ccsInteger);
        $this->UpdateFields["language_id"]["Value"] = $this->language_id->GetDBValue(true);
        $this->UpdateFields["category_name"]["Value"] = $this->category_name->GetDBValue(true);
        $this->SQL = CCBuildUpdate("categories_langs", $this->UpdateFields, $this);
        $this->SQL = CCBuildSQL($this->SQL, $this->Where, "");
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteUpdate", $this->Parent);
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteUpdate", $this->Parent);
        }
        $this->Where = $SelectWhere;
    }
//End Update Method

} //End categories_langsDataSource Class @3-FCB6E20C

//Initialize Page @1-F4CD7D4C
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
$TemplateFileName = "categories_langs.html";
$BlockToParse = "main";
$TemplateEncoding = "UTF-8";
$PathToRoot = "../";
//End Initialize Page

//Authenticate User @1-132EF5B6
CCSecurityRedirect("100", "");
//End Authenticate User

//Include events file @1-7790C5A2
include("./categories_langs_events.php");
//End Include events file

//Initialize Objects @1-530524C4
$DBcalendar = new clsDBcalendar();
$MainPage->Connections["calendar"] = & $DBcalendar;

// Controls
$JavaScriptLabel = & new clsControl(ccsLabel, "JavaScriptLabel", "JavaScriptLabel", ccsText, "", CCGetRequestParam("JavaScriptLabel", ccsGet, NULL), $MainPage);
$JavaScriptLabel->HTML = true;
$categories_langs = & new clsEditableGridcategories_langs("", $MainPage);
$MainPage->JavaScriptLabel = & $JavaScriptLabel;
$MainPage->categories_langs = & $categories_langs;
$categories_langs->Initialize();

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

//Execute Components @1-8FE4F5B6
$categories_langs->Operation();
//End Execute Components

//Go to destination page @1-C34777A2
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
    $DBcalendar->close();
    header("Location: " . $Redirect);
    unset($categories_langs);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-00C3C52F
$categories_langs->Show();
$JavaScriptLabel->Show();
$Tpl->block_path = "";
$Tpl->Parse($BlockToParse, false);
$main_block = $Tpl->GetVar($BlockToParse);
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeOutput", $MainPage);
if ($CCSEventResult) echo $main_block;
//End Show Page

//Unload Page @1-BEEA9F36
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
$DBcalendar->close();
unset($categories_langs);
unset($Tpl);
//End Unload Page


?>
