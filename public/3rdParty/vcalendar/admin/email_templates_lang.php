<?php
//Include Common Files @1-E5F120F1
define("RelativePath", "..");
define("PathToCurrentPage", "/admin/");
define("FileName", "email_templates_lang.php");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
//End Include Common Files

class clsEditableGridemail_templates_lang { //email_templates_lang Class @29-92CEB106

//Variables @29-A503BFD4

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
    var $Sorter_language_id;
//End Variables

//Class_Initialize Event @29-77CDAB9F
    function clsEditableGridemail_templates_lang($RelativePath, & $Parent)
    {

        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->Visible = true;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "EditableGrid email_templates_lang/Error";
        $this->ControlsErrors = array();
        $this->ComponentName = "email_templates_lang";
        $this->CachedColumns["email_template_lang_id"][0] = "email_template_lang_id";
        $this->DataSource = new clsemail_templates_langDataSource($this);
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

        $this->SorterName = CCGetParam("email_templates_langOrder", "");
        $this->SorterDirection = CCGetParam("email_templates_langDir", "");

        $this->Sorter_language_id = & new clsSorter($this->ComponentName, "Sorter_language_id", $FileName, $this);
        $this->languageLabel = & new clsControl(ccsLabel, "languageLabel", $CCSLocales->GetText("language_id"), ccsText, "", NULL, $this);
        $this->language_id = & new clsControl(ccsHidden, "language_id", "language_id", ccsText, "", NULL, $this);
        $this->email_template_desc = & new clsControl(ccsTextBox, "email_template_desc", $CCSLocales->GetText("email_template_desc"), ccsText, "", NULL, $this);
        $this->email_template_subject = & new clsControl(ccsTextBox, "email_template_subject", $CCSLocales->GetText("email_template_subject"), ccsText, "", NULL, $this);
        $this->email_template_subject->Required = true;
        $this->email_template_body = & new clsControl(ccsTextArea, "email_template_body", $CCSLocales->GetText("email_template_body"), ccsMemo, "", NULL, $this);
        $this->email_template_body->Required = true;
        $this->Button_Submit = & new clsButton("Button_Submit", $Method, $this);
    }
//End Class_Initialize Event

//Initialize Method @29-B4B9AF01
    function Initialize()
    {
        if(!$this->Visible) return;

        $this->DataSource->PageSize = & $this->PageSize;
        $this->DataSource->AbsolutePage = & $this->PageNumber;
        $this->DataSource->SetOrder($this->SorterName, $this->SorterDirection);

        $this->DataSource->Parameters["urlemail_template_id"] = CCGetFromGet("email_template_id", NULL);
    }
//End Initialize Method

//GetFormParameters Method @29-194E1F5A
    function GetFormParameters()
    {
        for($RowNumber = 1; $RowNumber <= $this->TotalRows; $RowNumber++)
        {
            $this->FormParameters["language_id"][$RowNumber] = CCGetFromPost("language_id_" . $RowNumber, NULL);
            $this->FormParameters["email_template_desc"][$RowNumber] = CCGetFromPost("email_template_desc_" . $RowNumber, NULL);
            $this->FormParameters["email_template_subject"][$RowNumber] = CCGetFromPost("email_template_subject_" . $RowNumber, NULL);
            $this->FormParameters["email_template_body"][$RowNumber] = CCGetFromPost("email_template_body_" . $RowNumber, NULL);
        }
    }
//End GetFormParameters Method

//Validate Method @29-D2D89008
    function Validate()
    {
        $Validation = true;
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate", $this);

        for($this->RowNumber = 1; $this->RowNumber <= $this->TotalRows; $this->RowNumber++)
        {
            $this->DataSource->CachedColumns["email_template_lang_id"] = $this->CachedColumns["email_template_lang_id"][$this->RowNumber];
            $this->DataSource->CurrentRow = $this->RowNumber;
            $this->language_id->SetText($this->FormParameters["language_id"][$this->RowNumber], $this->RowNumber);
            $this->email_template_desc->SetText($this->FormParameters["email_template_desc"][$this->RowNumber], $this->RowNumber);
            $this->email_template_subject->SetText($this->FormParameters["email_template_subject"][$this->RowNumber], $this->RowNumber);
            $this->email_template_body->SetText($this->FormParameters["email_template_body"][$this->RowNumber], $this->RowNumber);
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

//ValidateRow Method @29-D1100163
    function ValidateRow()
    {
        global $CCSLocales;
        $this->language_id->Validate();
        $this->email_template_desc->Validate();
        $this->email_template_subject->Validate();
        $this->email_template_body->Validate();
        $this->RowErrors = new clsErrors();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidateRow", $this);
        $errors = "";
        $errors = ComposeStrings($errors, $this->language_id->Errors->ToString());
        $errors = ComposeStrings($errors, $this->email_template_desc->Errors->ToString());
        $errors = ComposeStrings($errors, $this->email_template_subject->Errors->ToString());
        $errors = ComposeStrings($errors, $this->email_template_body->Errors->ToString());
        $this->language_id->Errors->Clear();
        $this->email_template_desc->Errors->Clear();
        $this->email_template_subject->Errors->Clear();
        $this->email_template_body->Errors->Clear();
        $errors = ComposeStrings($errors, $this->RowErrors->ToString());
        $this->RowsErrors[$this->RowNumber] = $errors;
        return $errors != "" ? 0 : 1;
    }
//End ValidateRow Method

//CheckInsert Method @29-A9FF2871
    function CheckInsert()
    {
        $filed = false;
        $filed = ($filed || strlen($this->FormParameters["language_id"][$this->RowNumber]));
        $filed = ($filed || strlen($this->FormParameters["email_template_desc"][$this->RowNumber]));
        $filed = ($filed || strlen($this->FormParameters["email_template_subject"][$this->RowNumber]));
        $filed = ($filed || strlen($this->FormParameters["email_template_body"][$this->RowNumber]));
        return $filed;
    }
//End CheckInsert Method

//CheckErrors Method @29-F5A3B433
    function CheckErrors()
    {
        $errors = false;
        $errors = ($errors || $this->Errors->Count());
        $errors = ($errors || $this->DataSource->Errors->Count());
        return $errors;
    }
//End CheckErrors Method

//Operation Method @29-C39B6EE5
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

        $Redirect = $FileName . "?" . CCGetQueryString("QueryString", array("ccsForm", "email_template_id"));
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

//UpdateGrid Method @29-FDF87559
    function UpdateGrid()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeSubmit", $this);
        if(!$this->Validate()) return;
        $Validation = true;
        for($this->RowNumber = 1; $this->RowNumber <= $this->TotalRows; $this->RowNumber++)
        {
            $this->DataSource->CachedColumns["email_template_lang_id"] = $this->CachedColumns["email_template_lang_id"][$this->RowNumber];
            $this->DataSource->CurrentRow = $this->RowNumber;
            $this->language_id->SetText($this->FormParameters["language_id"][$this->RowNumber], $this->RowNumber);
            $this->email_template_desc->SetText($this->FormParameters["email_template_desc"][$this->RowNumber], $this->RowNumber);
            $this->email_template_subject->SetText($this->FormParameters["email_template_subject"][$this->RowNumber], $this->RowNumber);
            $this->email_template_body->SetText($this->FormParameters["email_template_body"][$this->RowNumber], $this->RowNumber);
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

//UpdateRow Method @29-2898C578
    function UpdateRow()
    {
        if(!$this->UpdateAllowed) return false;
        $this->DataSource->languageLabel->SetValue($this->languageLabel->GetValue(true));
        $this->DataSource->language_id->SetValue($this->language_id->GetValue(true));
        $this->DataSource->email_template_desc->SetValue($this->email_template_desc->GetValue(true));
        $this->DataSource->email_template_subject->SetValue($this->email_template_subject->GetValue(true));
        $this->DataSource->email_template_body->SetValue($this->email_template_body->GetValue(true));
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

//FormScript Method @29-59800DB5
    function FormScript($TotalRows)
    {
        $script = "";
        return $script;
    }
//End FormScript Method

//SetFormState Method @29-33FDFF49
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
                $this->CachedColumns["email_template_lang_id"][$RowNumber] = $piece;
                $RowNumber++;
            }

            if(!$RowNumber) { $RowNumber = 1; }
            for($i = 1; $i <= $this->EmptyRows; $i++) {
                $this->CachedColumns["email_template_lang_id"][$RowNumber] = "";
                $RowNumber++;
            }
        }
    }
//End SetFormState Method

//GetFormState Method @29-1A9ED507
    function GetFormState($NonEmptyRows)
    {
        if(!$this->FormSubmitted) {
            $this->FormState  = $NonEmptyRows . ";";
            $this->FormState .= $this->InsertAllowed ? $this->EmptyRows : "0";
            if($NonEmptyRows) {
                for($i = 0; $i <= $NonEmptyRows; $i++) {
                    $this->FormState .= ";" . str_replace(";", "\\;", str_replace("\\", "\\\\", $this->CachedColumns["email_template_lang_id"][$i]));
                }
            }
        }
        return $this->FormState;
    }
//End GetFormState Method

//Show Method @29-2A1AA400
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
            $this->ControlsVisible["email_template_desc"] = $this->email_template_desc->Visible;
            $this->ControlsVisible["email_template_subject"] = $this->email_template_subject->Visible;
            $this->ControlsVisible["email_template_body"] = $this->email_template_body->Visible;
        if ($is_next_record || ($EmptyRowsLeft && $this->InsertAllowed)) {
            do {
                $this->RowNumber++;
                if($is_next_record) {
                    $NonEmptyRows++;
                    $this->DataSource->SetValues();
                }
                if (!($this->FormSubmitted) && $is_next_record) {
                    $this->CachedColumns["email_template_lang_id"][$this->RowNumber] = $this->DataSource->CachedColumns["email_template_lang_id"];
                    $this->languageLabel->SetValue($this->DataSource->languageLabel->GetValue());
                    $this->language_id->SetValue($this->DataSource->language_id->GetValue());
                    $this->email_template_desc->SetValue($this->DataSource->email_template_desc->GetValue());
                    $this->email_template_subject->SetValue($this->DataSource->email_template_subject->GetValue());
                    $this->email_template_body->SetValue($this->DataSource->email_template_body->GetValue());
                } elseif ($this->FormSubmitted && $is_next_record) {
                    $this->languageLabel->SetText("");
                    $this->languageLabel->SetValue($this->DataSource->languageLabel->GetValue());
                    $this->language_id->SetText($this->FormParameters["language_id"][$this->RowNumber], $this->RowNumber);
                    $this->email_template_desc->SetText($this->FormParameters["email_template_desc"][$this->RowNumber], $this->RowNumber);
                    $this->email_template_subject->SetText($this->FormParameters["email_template_subject"][$this->RowNumber], $this->RowNumber);
                    $this->email_template_body->SetText($this->FormParameters["email_template_body"][$this->RowNumber], $this->RowNumber);
                } elseif (!$this->FormSubmitted) {
                    $this->CachedColumns["email_template_lang_id"][$this->RowNumber] = "";
                    $this->languageLabel->SetText("");
                    $this->language_id->SetText("");
                    $this->email_template_desc->SetText("");
                    $this->email_template_subject->SetText("");
                    $this->email_template_body->SetText("");
                } else {
                    $this->languageLabel->SetText("");
                    $this->language_id->SetText($this->FormParameters["language_id"][$this->RowNumber], $this->RowNumber);
                    $this->email_template_desc->SetText($this->FormParameters["email_template_desc"][$this->RowNumber], $this->RowNumber);
                    $this->email_template_subject->SetText($this->FormParameters["email_template_subject"][$this->RowNumber], $this->RowNumber);
                    $this->email_template_body->SetText($this->FormParameters["email_template_body"][$this->RowNumber], $this->RowNumber);
                }
                $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowRow", $this);
                $this->languageLabel->Show($this->RowNumber);
                $this->language_id->Show($this->RowNumber);
                $this->email_template_desc->Show($this->RowNumber);
                $this->email_template_subject->Show($this->RowNumber);
                $this->email_template_body->Show($this->RowNumber);
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
                        if (($this->DataSource->CachedColumns["email_template_lang_id"] == $this->CachedColumns["email_template_lang_id"][$this->RowNumber])) {
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
        $this->Sorter_language_id->Show();
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

} //End email_templates_lang Class @29-FCB6E20C

class clsemail_templates_langDataSource extends clsDBcalendar {  //email_templates_langDataSource Class @29-B4250C93

//DataSource Variables @29-FFB1A15F
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
    var $email_template_desc;
    var $email_template_subject;
    var $email_template_body;
    var $CurrentRow;
//End DataSource Variables

//DataSourceClass_Initialize Event @29-CAEEB111
    function clsemail_templates_langDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "EditableGrid email_templates_lang/Error";
        $this->Initialize();
        $this->languageLabel = new clsField("languageLabel", ccsText, "");
        $this->language_id = new clsField("language_id", ccsText, "");
        $this->email_template_desc = new clsField("email_template_desc", ccsText, "");
        $this->email_template_subject = new clsField("email_template_subject", ccsText, "");
        $this->email_template_body = new clsField("email_template_body", ccsMemo, "");

        $this->UpdateFields["language_id"] = array("Name" => "language_id", "Value" => "", "DataType" => ccsText);
        $this->UpdateFields["email_template_desc"] = array("Name" => "email_template_desc", "Value" => "", "DataType" => ccsText);
        $this->UpdateFields["email_template_subject"] = array("Name" => "email_template_subject", "Value" => "", "DataType" => ccsText);
        $this->UpdateFields["email_template_body"] = array("Name" => "email_template_body", "Value" => "", "DataType" => ccsMemo);
    }
//End DataSourceClass_Initialize Event

//SetOrder Method @29-B38A5A97
    function SetOrder($SorterName, $SorterDirection)
    {
        $this->Order = "";
        $this->Order = CCGetOrder($this->Order, $SorterName, $SorterDirection, 
            array("Sorter_language_id" => array("language_id", "")));
    }
//End SetOrder Method

//Prepare Method @29-35BD3FC0
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "urlemail_template_id", ccsInteger, "", "", $this->Parameters["urlemail_template_id"], "", true);
        $this->AllParametersSet = $this->wp->AllParamsSet();
        $this->wp->Criterion[1] = $this->wp->Operation(opEqual, "email_template_id", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsInteger),true);
        $this->Where = 
             $this->wp->Criterion[1];
    }
//End Prepare Method

//Open Method @29-2A38FA0E
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->CountSQL = "SELECT COUNT(*)\n\n" .
        "FROM email_templates_lang";
        $this->SQL = "SELECT * \n\n" .
        "FROM email_templates_lang {SQL_Where} {SQL_OrderBy}";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect", $this->Parent);
        if ($this->CountSQL) 
            $this->RecordsCount = CCGetDBValue(CCBuildSQL($this->CountSQL, $this->Where, ""), $this);
        else
            $this->RecordsCount = "CCS not counted";
        $this->query($this->OptimizeSQL(CCBuildSQL($this->SQL, $this->Where, $this->Order)));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect", $this->Parent);
    }
//End Open Method

//SetValues Method @29-A9C1768B
    function SetValues()
    {
        $this->CachedColumns["email_template_lang_id"] = $this->f("email_template_lang_id");
        $this->languageLabel->SetDBValue($this->f("language_id"));
        $this->language_id->SetDBValue($this->f("language_id"));
        $this->email_template_desc->SetDBValue($this->f("email_template_desc"));
        $this->email_template_subject->SetDBValue($this->f("email_template_subject"));
        $this->email_template_body->SetDBValue($this->f("email_template_body"));
    }
//End SetValues Method

//Update Method @29-1D166D41
    function Update()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->CmdExecution = true;
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildUpdate", $this->Parent);
        $SelectWhere = $this->Where;
        $this->Where = "email_template_lang_id=" . $this->ToSQL($this->CachedColumns["email_template_lang_id"], ccsInteger);
        $this->UpdateFields["language_id"]["Value"] = $this->language_id->GetDBValue(true);
        $this->UpdateFields["email_template_desc"]["Value"] = $this->email_template_desc->GetDBValue(true);
        $this->UpdateFields["email_template_subject"]["Value"] = $this->email_template_subject->GetDBValue(true);
        $this->UpdateFields["email_template_body"]["Value"] = $this->email_template_body->GetDBValue(true);
        $this->SQL = CCBuildUpdate("email_templates_lang", $this->UpdateFields, $this);
        $this->SQL = CCBuildSQL($this->SQL, $this->Where, "");
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteUpdate", $this->Parent);
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteUpdate", $this->Parent);
        }
        $this->Where = $SelectWhere;
    }
//End Update Method

} //End email_templates_langDataSource Class @29-FCB6E20C

//Initialize Page @1-733B5C94
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
$TemplateFileName = "email_templates_lang.html";
$BlockToParse = "main";
$TemplateEncoding = "UTF-8";
$PathToRoot = "../";
//End Initialize Page

//Authenticate User @1-132EF5B6
CCSecurityRedirect("100", "");
//End Authenticate User

//Include events file @1-0102CE27
include("./email_templates_lang_events.php");
//End Include events file

//Initialize Objects @1-6F522D08
$DBcalendar = new clsDBcalendar();
$MainPage->Connections["calendar"] = & $DBcalendar;

// Controls
$JavaScriptLabel = & new clsControl(ccsLabel, "JavaScriptLabel", "JavaScriptLabel", ccsText, "", CCGetRequestParam("JavaScriptLabel", ccsGet, NULL), $MainPage);
$JavaScriptLabel->HTML = true;
$email_templates_lang = & new clsEditableGridemail_templates_lang("", $MainPage);
$MainPage->JavaScriptLabel = & $JavaScriptLabel;
$MainPage->email_templates_lang = & $email_templates_lang;
$email_templates_lang->Initialize();

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

//Execute Components @1-27FE4A80
$email_templates_lang->Operation();
//End Execute Components

//Go to destination page @1-09D6913A
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
    $DBcalendar->close();
    header("Location: " . $Redirect);
    unset($email_templates_lang);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-CBCE4907
$email_templates_lang->Show();
$JavaScriptLabel->Show();
$Tpl->block_path = "";
$Tpl->Parse($BlockToParse, false);
$main_block = $Tpl->GetVar($BlockToParse);
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeOutput", $MainPage);
if ($CCSEventResult) echo $main_block;
//End Show Page

//Unload Page @1-2A088413
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
$DBcalendar->close();
unset($email_templates_lang);
unset($Tpl);
//End Unload Page


?>
