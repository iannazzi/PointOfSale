<?php
//Include Common Files @1-0BBE5CFF
define("RelativePath", "..");
define("PathToCurrentPage", "/admin/");
define("FileName", "permissions.php");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
//End Include Common Files

//Include Page implementation @2-47CFCC1A
include_once(RelativePath . "/admin/header.php");
//End Include Page implementation

class clsEditableGridpermissions { //permissions Class @3-3456497A

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

//Class_Initialize Event @3-0D2FEAFC
    function clsEditableGridpermissions($RelativePath, & $Parent)
    {

        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->Visible = true;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "EditableGrid permissions/Error";
        $this->ControlsErrors = array();
        $this->ComponentName = "permissions";
        $this->CachedColumns["permission_id"][0] = "permission_id";
        $this->CachedColumns["perm_trans_id"][0] = "perm_trans_id";
        $this->CachedColumns["permission_lang_id"][0] = "permission_lang_id";
        $this->DataSource = new clspermissionsDataSource($this);
        $this->ds = & $this->DataSource;
        $this->PageSize = CCGetParam($this->ComponentName . "PageSize", "");
        if(!is_numeric($this->PageSize) || !strlen($this->PageSize))
            $this->PageSize = 100;
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

        $this->permission_category = & new clsControl(ccsLabel, "permission_category", $CCSLocales->GetText("permission_categoty"), ccsText, "", NULL, $this);
        $this->permission_desc = & new clsControl(ccsLabel, "permission_desc", $CCSLocales->GetText("permission_desc"), ccsText, "", NULL, $this);
        $this->perms_value = & new clsControl(ccsListBox, "perms_value", $CCSLocales->GetText("permission_value"), ccsText, "", NULL, $this);
        $this->perms_value->DSType = dsListOfValues;
        $this->perms_value->Values = array(array("0", "0"));
        $this->perm_type = & new clsControl(ccsHidden, "perm_type", "perm_type", ccsInteger, "", NULL, $this);
        $this->Button_Submit = & new clsButton("Button_Submit", $Method, $this);
    }
//End Class_Initialize Event

//Initialize Method @3-D469F14D
    function Initialize()
    {
        if(!$this->Visible) return;

        $this->DataSource->PageSize = & $this->PageSize;
        $this->DataSource->AbsolutePage = & $this->PageNumber;
        $this->DataSource->SetOrder($this->SorterName, $this->SorterDirection);

        $this->DataSource->Parameters["seslocale"] = CCGetSession("locale", NULL);
    }
//End Initialize Method

//GetFormParameters Method @3-068B2651
    function GetFormParameters()
    {
        for($RowNumber = 1; $RowNumber <= $this->TotalRows; $RowNumber++)
        {
            $this->FormParameters["perms_value"][$RowNumber] = CCGetFromPost("perms_value_" . $RowNumber, NULL);
            $this->FormParameters["perm_type"][$RowNumber] = CCGetFromPost("perm_type_" . $RowNumber, NULL);
        }
    }
//End GetFormParameters Method

//Validate Method @3-55A9A95E
    function Validate()
    {
        $Validation = true;
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate", $this);

        for($this->RowNumber = 1; $this->RowNumber <= $this->TotalRows; $this->RowNumber++)
        {
            $this->DataSource->CachedColumns["permission_id"] = $this->CachedColumns["permission_id"][$this->RowNumber];
            $this->DataSource->CachedColumns["perm_trans_id"] = $this->CachedColumns["perm_trans_id"][$this->RowNumber];
            $this->DataSource->CachedColumns["permission_lang_id"] = $this->CachedColumns["permission_lang_id"][$this->RowNumber];
            $this->DataSource->CurrentRow = $this->RowNumber;
            $this->perms_value->SetText($this->FormParameters["perms_value"][$this->RowNumber], $this->RowNumber);
            $this->perm_type->SetText($this->FormParameters["perm_type"][$this->RowNumber], $this->RowNumber);
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

//ValidateRow Method @3-4DF43EE3
    function ValidateRow()
    {
        global $CCSLocales;
        $this->perms_value->Validate();
        $this->perm_type->Validate();
        $this->RowErrors = new clsErrors();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidateRow", $this);
        $errors = "";
        $errors = ComposeStrings($errors, $this->perms_value->Errors->ToString());
        $errors = ComposeStrings($errors, $this->perm_type->Errors->ToString());
        $this->perms_value->Errors->Clear();
        $this->perm_type->Errors->Clear();
        $errors = ComposeStrings($errors, $this->RowErrors->ToString());
        $this->RowsErrors[$this->RowNumber] = $errors;
        return $errors != "" ? 0 : 1;
    }
//End ValidateRow Method

//CheckInsert Method @3-B5E3F879
    function CheckInsert()
    {
        $filed = false;
        $filed = ($filed || strlen($this->FormParameters["perms_value"][$this->RowNumber]));
        $filed = ($filed || strlen($this->FormParameters["perm_type"][$this->RowNumber]));
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

//Operation Method @3-909F269B
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

        $Redirect = $FileName . "?" . CCGetQueryString("QueryString", array("ccsForm"));
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

//UpdateGrid Method @3-DC5A11BC
    function UpdateGrid()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeSubmit", $this);
        if(!$this->Validate()) return;
        $Validation = true;
        for($this->RowNumber = 1; $this->RowNumber <= $this->TotalRows; $this->RowNumber++)
        {
            $this->DataSource->CachedColumns["permission_id"] = $this->CachedColumns["permission_id"][$this->RowNumber];
            $this->DataSource->CachedColumns["perm_trans_id"] = $this->CachedColumns["perm_trans_id"][$this->RowNumber];
            $this->DataSource->CachedColumns["permission_lang_id"] = $this->CachedColumns["permission_lang_id"][$this->RowNumber];
            $this->DataSource->CurrentRow = $this->RowNumber;
            $this->perms_value->SetText($this->FormParameters["perms_value"][$this->RowNumber], $this->RowNumber);
            $this->perm_type->SetText($this->FormParameters["perm_type"][$this->RowNumber], $this->RowNumber);
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

//UpdateRow Method @3-BF1A4F1F
    function UpdateRow()
    {
        if(!$this->UpdateAllowed) return false;
        $this->DataSource->perms_value->SetValue($this->perms_value->GetValue(true));
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

//SetFormState Method @3-7B80C118
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
            for($i = 2; $i < sizeof($pieces); $i = $i + 3)  {
                $piece = $pieces[$i + 0];
                $piece = str_replace("\\" . ord("\\"), "\\", $piece);
                $piece = str_replace("\\" . ord(";"), ";", $piece);
                $this->CachedColumns["permission_id"][$RowNumber] = $piece;
                $piece = $pieces[$i + 1];
                $piece = str_replace("\\" . ord("\\"), "\\", $piece);
                $piece = str_replace("\\" . ord(";"), ";", $piece);
                $this->CachedColumns["perm_trans_id"][$RowNumber] = $piece;
                $piece = $pieces[$i + 2];
                $piece = str_replace("\\" . ord("\\"), "\\", $piece);
                $piece = str_replace("\\" . ord(";"), ";", $piece);
                $this->CachedColumns["permission_lang_id"][$RowNumber] = $piece;
                $RowNumber++;
            }

            if(!$RowNumber) { $RowNumber = 1; }
            for($i = 1; $i <= $this->EmptyRows; $i++) {
                $this->CachedColumns["permission_id"][$RowNumber] = "";
                $this->CachedColumns["perm_trans_id"][$RowNumber] = "";
                $this->CachedColumns["permission_lang_id"][$RowNumber] = "";
                $RowNumber++;
            }
        }
    }
//End SetFormState Method

//GetFormState Method @3-81C8FD25
    function GetFormState($NonEmptyRows)
    {
        if(!$this->FormSubmitted) {
            $this->FormState  = $NonEmptyRows . ";";
            $this->FormState .= $this->InsertAllowed ? $this->EmptyRows : "0";
            if($NonEmptyRows) {
                for($i = 0; $i <= $NonEmptyRows; $i++) {
                    $this->FormState .= ";" . str_replace(";", "\\;", str_replace("\\", "\\\\", $this->CachedColumns["permission_id"][$i]));
                    $this->FormState .= ";" . str_replace(";", "\\;", str_replace("\\", "\\\\", $this->CachedColumns["perm_trans_id"][$i]));
                    $this->FormState .= ";" . str_replace(";", "\\;", str_replace("\\", "\\\\", $this->CachedColumns["permission_lang_id"][$i]));
                }
            }
        }
        return $this->FormState;
    }
//End GetFormState Method

//Show Method @3-68BD18AF
    function Show()
    {
        global $Tpl;
        global $FileName;
        global $CCSLocales;
        $Error = "";

        if(!$this->Visible) { return; }

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeSelect", $this);

        $this->perms_value->Prepare();

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
            $this->ControlsVisible["permission_category"] = $this->permission_category->Visible;
            $this->ControlsVisible["permission_desc"] = $this->permission_desc->Visible;
            $this->ControlsVisible["perms_value"] = $this->perms_value->Visible;
            $this->ControlsVisible["perm_type"] = $this->perm_type->Visible;
        if ($is_next_record || ($EmptyRowsLeft && $this->InsertAllowed)) {
            do {
                $this->RowNumber++;
                if($is_next_record) {
                    $NonEmptyRows++;
                    $this->DataSource->SetValues();
                }
                if (!($this->FormSubmitted) && $is_next_record) {
                    $this->CachedColumns["permission_id"][$this->RowNumber] = $this->DataSource->CachedColumns["permission_id"];
                    $this->CachedColumns["perm_trans_id"][$this->RowNumber] = $this->DataSource->CachedColumns["perm_trans_id"];
                    $this->CachedColumns["permission_lang_id"][$this->RowNumber] = $this->DataSource->CachedColumns["permission_lang_id"];
                    $this->permission_category->SetValue($this->DataSource->permission_category->GetValue());
                    $this->permission_desc->SetValue($this->DataSource->permission_desc->GetValue());
                    $this->perms_value->SetValue($this->DataSource->perms_value->GetValue());
                    $this->perm_type->SetValue($this->DataSource->perm_type->GetValue());
                } elseif ($this->FormSubmitted && $is_next_record) {
                    $this->permission_category->SetText("");
                    $this->permission_desc->SetText("");
                    $this->permission_category->SetValue($this->DataSource->permission_category->GetValue());
                    $this->permission_desc->SetValue($this->DataSource->permission_desc->GetValue());
                    $this->perms_value->SetText($this->FormParameters["perms_value"][$this->RowNumber], $this->RowNumber);
                    $this->perm_type->SetText($this->FormParameters["perm_type"][$this->RowNumber], $this->RowNumber);
                } elseif (!$this->FormSubmitted) {
                    $this->CachedColumns["permission_id"][$this->RowNumber] = "";
                    $this->CachedColumns["perm_trans_id"][$this->RowNumber] = "";
                    $this->CachedColumns["permission_lang_id"][$this->RowNumber] = "";
                    $this->permission_category->SetText("");
                    $this->permission_desc->SetText("");
                    $this->perms_value->SetText("");
                    $this->perm_type->SetText("");
                } else {
                    $this->permission_category->SetText("");
                    $this->permission_desc->SetText("");
                    $this->perms_value->SetText($this->FormParameters["perms_value"][$this->RowNumber], $this->RowNumber);
                    $this->perm_type->SetText($this->FormParameters["perm_type"][$this->RowNumber], $this->RowNumber);
                }
                $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowRow", $this);
                $this->permission_category->Show($this->RowNumber);
                $this->permission_desc->Show($this->RowNumber);
                $this->perms_value->Show($this->RowNumber);
                $this->perm_type->Show($this->RowNumber);
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
                        if (($this->DataSource->CachedColumns["permission_id"] == $this->CachedColumns["permission_id"][$this->RowNumber]) && ($this->DataSource->CachedColumns["perm_trans_id"] == $this->CachedColumns["perm_trans_id"][$this->RowNumber]) && ($this->DataSource->CachedColumns["permission_lang_id"] == $this->CachedColumns["permission_lang_id"][$this->RowNumber])) {
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

} //End permissions Class @3-FCB6E20C

class clspermissionsDataSource extends clsDBcalendar {  //permissionsDataSource Class @3-09DEA003

//DataSource Variables @3-9493EB4F
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
    var $permission_category;
    var $permission_desc;
    var $perms_value;
    var $perm_type;
    var $CurrentRow;
//End DataSource Variables

//DataSourceClass_Initialize Event @3-1D4B608A
    function clspermissionsDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "EditableGrid permissions/Error";
        $this->Initialize();
        $this->permission_category = new clsField("permission_category", ccsText, "");
        $this->permission_desc = new clsField("permission_desc", ccsText, "");
        $this->perms_value = new clsField("perms_value", ccsText, "");
        $this->perm_type = new clsField("perm_type", ccsInteger, "");

        $this->UpdateFields["permission_value"] = array("Name" => "permission_value", "Value" => "", "DataType" => ccsInteger);
    }
//End DataSourceClass_Initialize Event

//SetOrder Method @3-7167DE6D
    function SetOrder($SorterName, $SorterDirection)
    {
        $this->Order = "permissions.permission_category, permissions.permission_id";
        $this->Order = CCGetOrder($this->Order, $SorterName, $SorterDirection, 
            "");
    }
//End SetOrder Method

//Prepare Method @3-E8AC6965
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "seslocale", ccsText, "", "", $this->Parameters["seslocale"], "", false);
        $this->AllParametersSet = $this->wp->AllParamsSet();
        $this->wp->Criterion[1] = $this->wp->Operation(opEqual, "permissions_langs.language_id", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsText),false);
        $this->Where = 
             $this->wp->Criterion[1];
    }
//End Prepare Method

//Open Method @3-653B2CCF
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->CountSQL = "SELECT COUNT(*)\n\n" .
        "FROM permissions_langs INNER JOIN permissions ON\n\n" .
        "permissions_langs.permission_id = permissions.permission_id";
        $this->SQL = "SELECT permissions_langs.permission_desc AS permissions_langs_permission_desc, permissions.permission_id AS permissions_permission_id,\n\n" .
        "permission_var, permissions.permission_desc AS permissions_permission_desc, permission_value, permission_type, permission_category \n\n" .
        "FROM permissions_langs INNER JOIN permissions ON\n\n" .
        "permissions_langs.permission_id = permissions.permission_id {SQL_Where} {SQL_OrderBy}";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect", $this->Parent);
        if ($this->CountSQL) 
            $this->RecordsCount = CCGetDBValue(CCBuildSQL($this->CountSQL, $this->Where, ""), $this);
        else
            $this->RecordsCount = "CCS not counted";
        $this->query($this->OptimizeSQL(CCBuildSQL($this->SQL, $this->Where, $this->Order)));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect", $this->Parent);
    }
//End Open Method

//SetValues Method @3-5BD6DC48
    function SetValues()
    {
        $this->CachedColumns["permission_id"] = $this->f("permissions_permission_id");
        $this->CachedColumns["perm_trans_id"] = $this->f("perm_trans_id");
        $this->CachedColumns["permission_lang_id"] = $this->f("permission_lang_id");
        $this->permission_category->SetDBValue($this->f("permission_category"));
        $this->permission_desc->SetDBValue($this->f("permissions_langs_permission_desc"));
        $this->perms_value->SetDBValue($this->f("permission_value"));
        $this->perm_type->SetDBValue(trim($this->f("permission_type")));
    }
//End SetValues Method

//Update Method @3-AD17264B
    function Update()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->CmdExecution = true;
        $this->cp["permission_value"] = new clsSQLParameter("ctrlperms_value", ccsInteger, "", "", $this->perms_value->GetValue(true), "", false, $this->ErrorBlock);
        $wp = new clsSQLParameters($this->ErrorBlock);
        $wp->AddParameter("1", "dspermission_id", ccsInteger, "", "", $this->CachedColumns["permission_id"], "", false);
        if(!$wp->AllParamsSet()) {
            $this->Errors->addError($CCSLocales->GetText("CCS_CustomOperationError_MissingParameters"));
        }
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildUpdate", $this->Parent);
        if (!strlen($this->cp["permission_value"]->GetText()) and !is_bool($this->cp["permission_value"]->GetValue())) 
            $this->cp["permission_value"]->SetValue($this->perms_value->GetValue(true));
        $wp->Criterion[1] = $wp->Operation(opEqual, "permission_id", $wp->GetDBValue("1"), $this->ToSQL($wp->GetDBValue("1"), ccsInteger),false);
        $Where = 
             $wp->Criterion[1];
        $this->UpdateFields["permission_value"]["Value"] = $this->cp["permission_value"]->GetDBValue(true);
        $this->SQL = CCBuildUpdate("permissions", $this->UpdateFields, $this);
        $this->SQL = CCBuildSQL($this->SQL, $Where, "");
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteUpdate", $this->Parent);
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteUpdate", $this->Parent);
        }
    }
//End Update Method

} //End permissionsDataSource Class @3-FCB6E20C

//Initialize Page @1-B29BA818
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
$TemplateFileName = "permissions.html";
$BlockToParse = "main";
$TemplateEncoding = "UTF-8";
$PathToRoot = "../";
//End Initialize Page

//Authenticate User @1-F87A9DA3
CCSecurityRedirect("100", "../login.php");
//End Authenticate User

//Include events file @1-9070F756
include("./permissions_events.php");
//End Include events file

//Initialize Objects @1-66E74B72
$DBcalendar = new clsDBcalendar();
$MainPage->Connections["calendar"] = & $DBcalendar;

// Controls
$header = & new clsheader("", "header", $MainPage);
$header->Initialize();
$permissions = & new clsEditableGridpermissions("", $MainPage);
$MainPage->header = & $header;
$MainPage->permissions = & $permissions;
$permissions->Initialize();

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

//Execute Components @1-4E2DEE13
$header->Operations();
$permissions->Operation();
//End Execute Components

//Go to destination page @1-394C9AD0
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
    $DBcalendar->close();
    header("Location: " . $Redirect);
    $header->Class_Terminate();
    unset($header);
    unset($permissions);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-207A889C
$header->Show();
$permissions->Show();
$Tpl->block_path = "";
$Tpl->Parse($BlockToParse, false);
$main_block = $Tpl->GetVar($BlockToParse);
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeOutput", $MainPage);
if ($CCSEventResult) echo $main_block;
//End Show Page

//Unload Page @1-AF6A4CB1
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
$DBcalendar->close();
$header->Class_Terminate();
unset($header);
unset($permissions);
unset($Tpl);
//End Unload Page


?>
