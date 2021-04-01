<?php
//Include Common Files @1-95B4CAC7
define("RelativePath", "..");
define("PathToCurrentPage", "/admin/");
define("FileName", "custom_fields.php");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
//End Include Common Files

//Include Page implementation @2-47CFCC1A
include_once(RelativePath . "/admin/header.php");
//End Include Page implementation

class clsGridcustom_fields { //custom_fields class @3-E92C2B0D

//Variables @3-1EC8CF8D

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
    var $Sorter_field_name;
    var $Sorter_field_label;
    var $Sorter_field_is_active;
//End Variables

//Class_Initialize Event @3-3765AE8E
    function clsGridcustom_fields($RelativePath, & $Parent)
    {
        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->ComponentName = "custom_fields";
        $this->Visible = True;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Grid custom_fields";
        $this->DataSource = new clscustom_fieldsDataSource($this);
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
        $this->SorterName = CCGetParam("custom_fieldsOrder", "");
        $this->SorterDirection = CCGetParam("custom_fieldsDir", "");

        $this->field_name = & new clsControl(ccsLink, "field_name", "field_name", ccsText, "", CCGetRequestParam("field_name", ccsGet, NULL), $this);
        $this->field_name->Page = "custom_fields.php";
        $this->field_label = & new clsControl(ccsLabel, "field_label", "field_label", ccsText, "", CCGetRequestParam("field_label", ccsGet, NULL), $this);
        $this->field_is_active = & new clsControl(ccsLabel, "field_is_active", "field_is_active", ccsBoolean, $CCSLocales->GetFormatInfo("BooleanFormat"), CCGetRequestParam("field_is_active", ccsGet, NULL), $this);
        $this->translations = & new clsControl(ccsLink, "translations", "translations", ccsText, "", CCGetRequestParam("translations", ccsGet, NULL), $this);
        $this->translations->Page = "custom_fields_lang.php";
        $this->Sorter_field_name = & new clsSorter($this->ComponentName, "Sorter_field_name", $FileName, $this);
        $this->Sorter_field_label = & new clsSorter($this->ComponentName, "Sorter_field_label", $FileName, $this);
        $this->Sorter_field_is_active = & new clsSorter($this->ComponentName, "Sorter_field_is_active", $FileName, $this);
    }
//End Class_Initialize Event

//Initialize Method @3-90E704C5
    function Initialize()
    {
        if(!$this->Visible) return;

        $this->DataSource->PageSize = & $this->PageSize;
        $this->DataSource->AbsolutePage = & $this->PageNumber;
        $this->DataSource->SetOrder($this->SorterName, $this->SorterDirection);
    }
//End Initialize Method

//Show Method @3-B4300F68
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
            $this->ControlsVisible["field_name"] = $this->field_name->Visible;
            $this->ControlsVisible["field_label"] = $this->field_label->Visible;
            $this->ControlsVisible["field_is_active"] = $this->field_is_active->Visible;
            $this->ControlsVisible["translations"] = $this->translations->Visible;
            do {
                $this->RowNumber++;
                $this->DataSource->SetValues();
                $Tpl->block_path = $ParentPath . "/" . $GridBlock . "/Row";
                $this->field_name->SetValue($this->DataSource->field_name->GetValue());
                $this->field_name->Parameters = CCGetQueryString("QueryString", array("ccsForm"));
                $this->field_name->Parameters = CCAddParam($this->field_name->Parameters, "field_id", $this->DataSource->f("custom_fields_field_id"));
                $this->field_label->SetValue($this->DataSource->field_label->GetValue());
                $this->field_is_active->SetValue($this->DataSource->field_is_active->GetValue());
                $this->translations->Parameters = CCGetQueryString("QueryString", array("ccsForm"));
                $this->translations->Parameters = CCAddParam($this->translations->Parameters, "field_id", $this->DataSource->f("custom_fields_field_id"));
                $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowRow", $this);
                $this->field_name->Show();
                $this->field_label->Show();
                $this->field_is_active->Show();
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
        $this->Sorter_field_name->Show();
        $this->Sorter_field_label->Show();
        $this->Sorter_field_is_active->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->DataSource->close();
    }
//End Show Method

//GetErrors Method @3-C5E8179E
    function GetErrors()
    {
        $errors = "";
        $errors = ComposeStrings($errors, $this->field_name->Errors->ToString());
        $errors = ComposeStrings($errors, $this->field_label->Errors->ToString());
        $errors = ComposeStrings($errors, $this->field_is_active->Errors->ToString());
        $errors = ComposeStrings($errors, $this->translations->Errors->ToString());
        $errors = ComposeStrings($errors, $this->Errors->ToString());
        $errors = ComposeStrings($errors, $this->DataSource->Errors->ToString());
        return $errors;
    }
//End GetErrors Method

} //End custom_fields Class @3-FCB6E20C

class clscustom_fieldsDataSource extends clsDBcalendar {  //custom_fieldsDataSource Class @3-294F4F48

//DataSource Variables @3-98664A4F
    var $Parent = "";
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;
    var $CmdExecution;

    var $CountSQL;
    var $wp;


    // Datasource fields
    var $field_name;
    var $field_label;
    var $field_is_active;
//End DataSource Variables

//DataSourceClass_Initialize Event @3-C4905880
    function clscustom_fieldsDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "Grid custom_fields";
        $this->Initialize();
        $this->field_name = new clsField("field_name", ccsText, "");
        $this->field_label = new clsField("field_label", ccsText, "");
        $this->field_is_active = new clsField("field_is_active", ccsBoolean, array(1, 0, ""));

    }
//End DataSourceClass_Initialize Event

//SetOrder Method @3-3D086BD6
    function SetOrder($SorterName, $SorterDirection)
    {
        $this->Order = "custom_fields.field_id";
        $this->Order = CCGetOrder($this->Order, $SorterName, $SorterDirection, 
            array("Sorter_field_name" => array("field_name", ""), 
            "Sorter_field_label" => array("custom_fields_langs.field_label", ""), 
            "Sorter_field_is_active" => array("field_is_active", "")));
    }
//End SetOrder Method

//Prepare Method @3-78B46E7A
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "seslocale", ccsText, "", "", $this->Parameters["seslocale"], "en", false);
        $this->wp->Criterion[1] = $this->wp->Operation(opEqual, "custom_fields_langs.language_id", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsText),false);
        $this->Where = 
             $this->wp->Criterion[1];
    }
//End Prepare Method

//Open Method @3-242EDCCC
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->CountSQL = "SELECT COUNT(*)\n\n" .
        "FROM custom_fields_langs INNER JOIN custom_fields ON\n\n" .
        "custom_fields_langs.field_id = custom_fields.field_id";
        $this->SQL = "SELECT field_name, custom_fields_langs.field_label AS custom_fields_langs_field_label, field_is_active, custom_fields.field_id AS custom_fields_field_id \n\n" .
        "FROM custom_fields_langs INNER JOIN custom_fields ON\n\n" .
        "custom_fields_langs.field_id = custom_fields.field_id {SQL_Where} {SQL_OrderBy}";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect", $this->Parent);
        if ($this->CountSQL) 
            $this->RecordsCount = CCGetDBValue(CCBuildSQL($this->CountSQL, $this->Where, ""), $this);
        else
            $this->RecordsCount = "CCS not counted";
        $this->query($this->OptimizeSQL(CCBuildSQL($this->SQL, $this->Where, $this->Order)));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect", $this->Parent);
    }
//End Open Method

//SetValues Method @3-2EFE102F
    function SetValues()
    {
        $this->field_name->SetDBValue($this->f("field_name"));
        $this->field_label->SetDBValue($this->f("custom_fields_langs_field_label"));
        $this->field_is_active->SetDBValue(trim($this->f("field_is_active")));
    }
//End SetValues Method

} //End custom_fieldsDataSource Class @3-FCB6E20C

class clsRecordcustom_fields_maint { //custom_fields_maint Class @16-A1E32929

//Variables @16-0DF9B1C2

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

//Class_Initialize Event @16-5C128AEE
    function clsRecordcustom_fields_maint($RelativePath, & $Parent)
    {

        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->Visible = true;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Record custom_fields_maint/Error";
        $this->DataSource = new clscustom_fields_maintDataSource($this);
        $this->ds = & $this->DataSource;
        $this->UpdateAllowed = true;
        $this->ReadAllowed = true;
        if($this->Visible)
        {
            $this->ComponentName = "custom_fields_maint";
            $CCSForm = split(":", CCGetFromGet("ccsForm", ""), 2);
            if(sizeof($CCSForm) == 1)
                $CCSForm[1] = "";
            list($FormName, $FormMethod) = $CCSForm;
            $this->EditMode = ($FormMethod == "Edit");
            $this->FormEnctype = "application/x-www-form-urlencoded";
            $this->FormSubmitted = ($FormName == $this->ComponentName);
            $Method = $this->FormSubmitted ? ccsPost : ccsGet;
            $this->field_name = & new clsControl(ccsLabel, "field_name", $CCSLocales->GetText("field_name"), ccsText, "", CCGetRequestParam("field_name", $Method, NULL), $this);
            $this->field_label = & new clsControl(ccsTextBox, "field_label", $CCSLocales->GetText("field_label"), ccsText, "", CCGetRequestParam("field_label", $Method, NULL), $this);
            $this->field_is_active = & new clsControl(ccsCheckBox, "field_is_active", $CCSLocales->GetText("field_is_active"), ccsInteger, "", CCGetRequestParam("field_is_active", $Method, NULL), $this);
            $this->field_is_active->CheckedValue = $this->field_is_active->GetParsedValue(1);
            $this->field_is_active->UncheckedValue = $this->field_is_active->GetParsedValue(0);
            $this->Button_Update = & new clsButton("Button_Update", $Method, $this);
            $this->Button_Cancel = & new clsButton("Button_Cancel", $Method, $this);
        }
    }
//End Class_Initialize Event

//Initialize Method @16-98825D2C
    function Initialize()
    {

        if(!$this->Visible)
            return;

        $this->DataSource->Parameters["urlfield_id"] = CCGetFromGet("field_id", NULL);
        $this->DataSource->Parameters["seslocale"] = CCGetSession("locale", NULL);
    }
//End Initialize Method

//Validate Method @16-70FEE79F
    function Validate()
    {
        global $CCSLocales;
        $Validation = true;
        $Where = "";
        $Validation = ($this->field_label->Validate() && $Validation);
        $Validation = ($this->field_is_active->Validate() && $Validation);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate", $this);
        $Validation =  $Validation && ($this->field_label->Errors->Count() == 0);
        $Validation =  $Validation && ($this->field_is_active->Errors->Count() == 0);
        return (($this->Errors->Count() == 0) && $Validation);
    }
//End Validate Method

//CheckErrors Method @16-9FD2A159
    function CheckErrors()
    {
        $errors = false;
        $errors = ($errors || $this->field_name->Errors->Count());
        $errors = ($errors || $this->field_label->Errors->Count());
        $errors = ($errors || $this->field_is_active->Errors->Count());
        $errors = ($errors || $this->Errors->Count());
        $errors = ($errors || $this->DataSource->Errors->Count());
        return $errors;
    }
//End CheckErrors Method

//Operation Method @16-0600FB52
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
            $this->PressedButton = $this->EditMode ? "Button_Update" : "Button_Cancel";
            if($this->Button_Update->Pressed) {
                $this->PressedButton = "Button_Update";
            } else if($this->Button_Cancel->Pressed) {
                $this->PressedButton = "Button_Cancel";
            }
        }
        $Redirect = "custom_fields.php" . "?" . CCGetQueryString("QueryString", array("ccsForm", "field_id"));
        if($this->PressedButton == "Button_Cancel") {
            if(!CCGetEvent($this->Button_Cancel->CCSEvents, "OnClick", $this->Button_Cancel)) {
                $Redirect = "";
            }
        } else if($this->Validate()) {
            if($this->PressedButton == "Button_Update") {
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

//UpdateRow Method @16-2FF2449E
    function UpdateRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeUpdate", $this);
        if(!$this->UpdateAllowed) return false;
        $this->DataSource->field_label->SetValue($this->field_label->GetValue(true));
        $this->DataSource->Update();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterUpdate", $this);
        return (!$this->CheckErrors());
    }
//End UpdateRow Method

//Show Method @16-A9585FC6
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
                $this->field_name->SetValue($this->DataSource->field_name->GetValue());
                if(!$this->FormSubmitted){
                    $this->field_label->SetValue($this->DataSource->field_label->GetValue());
                    $this->field_is_active->SetValue($this->DataSource->field_is_active->GetValue());
                }
            } else {
                $this->EditMode = false;
            }
        }

        if($this->FormSubmitted || $this->CheckErrors()) {
            $Error = "";
            $Error = ComposeStrings($Error, $this->field_name->Errors->ToString());
            $Error = ComposeStrings($Error, $this->field_label->Errors->ToString());
            $Error = ComposeStrings($Error, $this->field_is_active->Errors->ToString());
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

        $this->field_name->Show();
        $this->field_label->Show();
        $this->field_is_active->Show();
        $this->Button_Update->Show();
        $this->Button_Cancel->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->DataSource->close();
    }
//End Show Method

} //End custom_fields_maint Class @16-FCB6E20C

class clscustom_fields_maintDataSource extends clsDBcalendar {  //custom_fields_maintDataSource Class @16-80A06D5F

//DataSource Variables @16-6AE5EB10
    var $Parent = "";
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;
    var $CmdExecution;

    var $UpdateParameters;
    var $wp;
    var $AllParametersSet;


    // Datasource fields
    var $field_name;
    var $field_label;
    var $field_is_active;
//End DataSource Variables

//DataSourceClass_Initialize Event @16-7DC018C5
    function clscustom_fields_maintDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "Record custom_fields_maint/Error";
        $this->Initialize();
        $this->field_name = new clsField("field_name", ccsText, "");
        $this->field_label = new clsField("field_label", ccsText, "");
        $this->field_is_active = new clsField("field_is_active", ccsInteger, "");

        $this->UpdateFields["custom_fields_langs.field_label"] = array("Name" => "field_label", "Value" => "", "DataType" => ccsText);
    }
//End DataSourceClass_Initialize Event

//Prepare Method @16-76429D2F
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "urlfield_id", ccsInteger, "", "", $this->Parameters["urlfield_id"], "", false);
        $this->wp->AddParameter("2", "seslocale", ccsText, "", "", $this->Parameters["seslocale"], "en", false);
        $this->AllParametersSet = $this->wp->AllParamsSet();
        $this->wp->Criterion[1] = $this->wp->Operation(opEqual, "custom_fields.field_id", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsInteger),false);
        $this->wp->Criterion[2] = $this->wp->Operation(opEqual, "custom_fields_langs.language_id", $this->wp->GetDBValue("2"), $this->ToSQL($this->wp->GetDBValue("2"), ccsText),false);
        $this->Where = $this->wp->opAND(
             false, 
             $this->wp->Criterion[1], 
             $this->wp->Criterion[2]);
    }
//End Prepare Method

//Open Method @16-CFFEDCEA
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->SQL = "SELECT custom_fields_langs.field_label AS custom_fields_langs_field_label, field_name, field_is_active \n\n" .
        "FROM custom_fields_langs INNER JOIN custom_fields ON\n\n" .
        "custom_fields_langs.field_id = custom_fields.field_id {SQL_Where} {SQL_OrderBy}";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect", $this->Parent);
        $this->PageSize = 1;
        $this->query($this->OptimizeSQL(CCBuildSQL($this->SQL, $this->Where, $this->Order)));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect", $this->Parent);
    }
//End Open Method

//SetValues Method @16-2EFE102F
    function SetValues()
    {
        $this->field_name->SetDBValue($this->f("field_name"));
        $this->field_label->SetDBValue($this->f("custom_fields_langs_field_label"));
        $this->field_is_active->SetDBValue(trim($this->f("field_is_active")));
    }
//End SetValues Method

//Update Method @16-DB719538
    function Update()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->CmdExecution = true;
        $this->cp["custom_fields_langs.field_label"] = new clsSQLParameter("ctrlfield_label", ccsText, "", "", $this->field_label->GetValue(true), "", false, $this->ErrorBlock);
        $wp = new clsSQLParameters($this->ErrorBlock);
        $wp->AddParameter("1", "urlfield_id", ccsInteger, "", "", CCGetFromGet("field_id", NULL), "", false);
        if(!$wp->AllParamsSet()) {
            $this->Errors->addError($CCSLocales->GetText("CCS_CustomOperationError_MissingParameters"));
        }
        $wp->AddParameter("2", "seslocale", ccsText, "", "", CCGetSession("locale", NULL), "en", false);
        if(!$wp->AllParamsSet()) {
            $this->Errors->addError($CCSLocales->GetText("CCS_CustomOperationError_MissingParameters"));
        }
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildUpdate", $this->Parent);
        if (!strlen($this->cp["custom_fields_langs.field_label"]->GetText()) and !is_bool($this->cp["custom_fields_langs.field_label"]->GetValue())) 
            $this->cp["custom_fields_langs.field_label"]->SetValue($this->field_label->GetValue(true));
        $wp->Criterion[1] = $wp->Operation(opEqual, "field_id", $wp->GetDBValue("1"), $this->ToSQL($wp->GetDBValue("1"), ccsInteger),false);
        $wp->Criterion[2] = $wp->Operation(opEqual, "language_id", $wp->GetDBValue("2"), $this->ToSQL($wp->GetDBValue("2"), ccsText),false);
        $Where = $wp->opAND(
             false, 
             $wp->Criterion[1], 
             $wp->Criterion[2]);
        $this->UpdateFields["custom_fields_langs.field_label"]["Value"] = $this->cp["custom_fields_langs.field_label"]->GetDBValue(true);
        $this->SQL = CCBuildUpdate("custom_fields_langs", $this->UpdateFields, $this);
        $this->SQL = CCBuildSQL($this->SQL, $Where, "");
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteUpdate", $this->Parent);
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteUpdate", $this->Parent);
        }
    }
//End Update Method

} //End custom_fields_maintDataSource Class @16-FCB6E20C

//Initialize Page @1-B45C2AC1
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
$TemplateFileName = "custom_fields.html";
$BlockToParse = "main";
$TemplateEncoding = "UTF-8";
$PathToRoot = "../";
//End Initialize Page

//Authenticate User @1-132EF5B6
CCSecurityRedirect("100", "");
//End Authenticate User

//Include events file @1-B711F06E
include("./custom_fields_events.php");
//End Include events file

//Initialize Objects @1-EABBEE46
$DBcalendar = new clsDBcalendar();
$MainPage->Connections["calendar"] = & $DBcalendar;

// Controls
$header = & new clsheader("", "header", $MainPage);
$header->Initialize();
$custom_fields = & new clsGridcustom_fields("", $MainPage);
$custom_fields_maint = & new clsRecordcustom_fields_maint("", $MainPage);
$MainPage->header = & $header;
$MainPage->custom_fields = & $custom_fields;
$MainPage->custom_fields_maint = & $custom_fields_maint;
$custom_fields->Initialize();
$custom_fields_maint->Initialize();

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

//Execute Components @1-33D6349B
$header->Operations();
$custom_fields_maint->Operation();
//End Execute Components

//Go to destination page @1-BCF660A8
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
    $DBcalendar->close();
    header("Location: " . $Redirect);
    $header->Class_Terminate();
    unset($header);
    unset($custom_fields);
    unset($custom_fields_maint);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-7E179EA3
$header->Show();
$custom_fields->Show();
$custom_fields_maint->Show();
$Tpl->block_path = "";
$Tpl->Parse($BlockToParse, false);
$main_block = $Tpl->GetVar($BlockToParse);
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeOutput", $MainPage);
if ($CCSEventResult) echo $main_block;
//End Show Page

//Unload Page @1-3230B478
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
$DBcalendar->close();
$header->Class_Terminate();
unset($header);
unset($custom_fields);
unset($custom_fields_maint);
unset($Tpl);
//End Unload Page


?>
