<?php
//Include Common Files @1-BB20971D
define("RelativePath", "..");
define("PathToCurrentPage", "/admin/");
define("FileName", "users.php");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
//End Include Common Files

//Include Page implementation @62-47CFCC1A
include_once(RelativePath . "/admin/header.php");
//End Include Page implementation

class clsRecordusersSearch { //usersSearch Class @3-C4FF86BD

//Variables @3-0DF9B1C2

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

//Class_Initialize Event @3-899B6E9E
    function clsRecordusersSearch($RelativePath, & $Parent)
    {

        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->Visible = true;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Record usersSearch/Error";
        $this->ReadAllowed = true;
        if($this->Visible)
        {
            $this->ComponentName = "usersSearch";
            $CCSForm = split(":", CCGetFromGet("ccsForm", ""), 2);
            if(sizeof($CCSForm) == 1)
                $CCSForm[1] = "";
            list($FormName, $FormMethod) = $CCSForm;
            $this->FormEnctype = "application/x-www-form-urlencoded";
            $this->FormSubmitted = ($FormName == $this->ComponentName);
            $Method = $this->FormSubmitted ? ccsPost : ccsGet;
            $this->s_keyword = & new clsControl(ccsTextBox, "s_keyword", "s_keyword", ccsText, "", CCGetRequestParam("s_keyword", $Method, NULL), $this);
            $this->s_user_level = & new clsControl(ccsListBox, "s_user_level", $CCSLocales->GetText("user_level"), ccsInteger, "", CCGetRequestParam("s_user_level", $Method, NULL), $this);
            $this->s_user_level->DSType = dsListOfValues;
            $this->s_user_level->Values = array(array("1", $CCSLocales->GetText("non_confirmed_user")), array("10", $CCSLocales->GetText("user")), array("100", $CCSLocales->GetText("admin")));
            $this->s_user_is_approved = & new clsControl(ccsListBox, "s_user_is_approved", "s_user_is_approved", ccsText, "", CCGetRequestParam("s_user_is_approved", $Method, NULL), $this);
            $this->s_user_is_approved->DSType = dsListOfValues;
            $this->s_user_is_approved->Values = array(array("1", $CCSLocales->GetText("yes")), array("0", $CCSLocales->GetText("no")));
            $this->Button_DoSearch = & new clsButton("Button_DoSearch", $Method, $this);
        }
    }
//End Class_Initialize Event

//Validate Method @3-F2EFFE23
    function Validate()
    {
        global $CCSLocales;
        $Validation = true;
        $Where = "";
        $Validation = ($this->s_keyword->Validate() && $Validation);
        $Validation = ($this->s_user_level->Validate() && $Validation);
        $Validation = ($this->s_user_is_approved->Validate() && $Validation);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate", $this);
        $Validation =  $Validation && ($this->s_keyword->Errors->Count() == 0);
        $Validation =  $Validation && ($this->s_user_level->Errors->Count() == 0);
        $Validation =  $Validation && ($this->s_user_is_approved->Errors->Count() == 0);
        return (($this->Errors->Count() == 0) && $Validation);
    }
//End Validate Method

//CheckErrors Method @3-41CD9634
    function CheckErrors()
    {
        $errors = false;
        $errors = ($errors || $this->s_keyword->Errors->Count());
        $errors = ($errors || $this->s_user_level->Errors->Count());
        $errors = ($errors || $this->s_user_is_approved->Errors->Count());
        $errors = ($errors || $this->Errors->Count());
        return $errors;
    }
//End CheckErrors Method

//Operation Method @3-AB46278A
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
        $Redirect = "users.php";
        if($this->Validate()) {
            if($this->PressedButton == "Button_DoSearch") {
                $Redirect = "users.php" . "?" . CCMergeQueryStrings(CCGetQueryString("Form", array("Button_DoSearch", "Button_DoSearch_x", "Button_DoSearch_y")));
                if(!CCGetEvent($this->Button_DoSearch->CCSEvents, "OnClick", $this->Button_DoSearch)) {
                    $Redirect = "";
                }
            }
        } else {
            $Redirect = "";
        }
    }
//End Operation Method

//Show Method @3-09BB601D
    function Show()
    {
        global $Tpl;
        global $FileName;
        global $CCSLocales;
        $Error = "";

        if(!$this->Visible)
            return;

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeSelect", $this);

        $this->s_user_level->Prepare();
        $this->s_user_is_approved->Prepare();

        $RecordBlock = "Record " . $this->ComponentName;
        $ParentPath = $Tpl->block_path;
        $Tpl->block_path = $ParentPath . "/" . $RecordBlock;
        $this->EditMode = $this->EditMode && $this->ReadAllowed;
        if (!$this->FormSubmitted) {
        }

        if($this->FormSubmitted || $this->CheckErrors()) {
            $Error = "";
            $Error = ComposeStrings($Error, $this->s_keyword->Errors->ToString());
            $Error = ComposeStrings($Error, $this->s_user_level->Errors->ToString());
            $Error = ComposeStrings($Error, $this->s_user_is_approved->Errors->ToString());
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
        $this->s_user_level->Show();
        $this->s_user_is_approved->Show();
        $this->Button_DoSearch->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
    }
//End Show Method

} //End usersSearch Class @3-FCB6E20C

class clsGridusers { //users class @2-0CB76799

//Variables @2-121877C4

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
    var $Sorter_user_id;
    var $Sorter_user_login;
    var $Sorter_user_first_name;
    var $Sorter_user_last_name;
    var $Sorter_user_level;
    var $Sorter_user_email;
    var $Sorter_user_date_add;
    var $Sorter_user_last_login;
    var $Sorter_user_is_approved;
//End Variables

//Class_Initialize Event @2-A417411B
    function clsGridusers($RelativePath, & $Parent)
    {
        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->ComponentName = "users";
        $this->Visible = True;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Grid users";
        $this->DataSource = new clsusersDataSource($this);
        $this->ds = & $this->DataSource;
        $this->PageSize = CCGetParam($this->ComponentName . "PageSize", "");
        if(!is_numeric($this->PageSize) || !strlen($this->PageSize))
            $this->PageSize = 20;
        else
            $this->PageSize = intval($this->PageSize);
        if ($this->PageSize > 100)
            $this->PageSize = 100;
        if($this->PageSize == 0)
            $this->Errors->addError("<p>Form: Grid " . $this->ComponentName . "<br>Error: (CCS06) Invalid page size.</p>");
        $this->PageNumber = intval(CCGetParam($this->ComponentName . "Page", 1));
        if ($this->PageNumber <= 0) $this->PageNumber = 1;
        $this->SorterName = CCGetParam("usersOrder", "");
        $this->SorterDirection = CCGetParam("usersDir", "");

        $this->user_id = & new clsControl(ccsLabel, "user_id", "user_id", ccsInteger, "", CCGetRequestParam("user_id", ccsGet, NULL), $this);
        $this->user_login = & new clsControl(ccsLink, "user_login", "user_login", ccsText, "", CCGetRequestParam("user_login", ccsGet, NULL), $this);
        $this->user_login->Page = "users_maint.php";
        $this->user_first_name = & new clsControl(ccsLabel, "user_first_name", "user_first_name", ccsText, "", CCGetRequestParam("user_first_name", ccsGet, NULL), $this);
        $this->user_last_name = & new clsControl(ccsLabel, "user_last_name", "user_last_name", ccsText, "", CCGetRequestParam("user_last_name", ccsGet, NULL), $this);
        $this->user_level = & new clsControl(ccsLabel, "user_level", "user_level", ccsText, "", CCGetRequestParam("user_level", ccsGet, NULL), $this);
        $this->user_email = & new clsControl(ccsLabel, "user_email", "user_email", ccsText, "", CCGetRequestParam("user_email", ccsGet, NULL), $this);
        $this->user_date_add = & new clsControl(ccsLabel, "user_date_add", "user_date_add", ccsDate, array("GeneralDate"), CCGetRequestParam("user_date_add", ccsGet, NULL), $this);
        $this->user_last_login = & new clsControl(ccsLabel, "user_last_login", "user_last_login", ccsText, "", CCGetRequestParam("user_last_login", ccsGet, NULL), $this);
        $this->user_is_approved = & new clsControl(ccsLabel, "user_is_approved", "user_is_approved", ccsBoolean, $CCSLocales->GetFormatInfo("BooleanFormat"), CCGetRequestParam("user_is_approved", ccsGet, NULL), $this);
        $this->users_TotalRecords = & new clsControl(ccsLabel, "users_TotalRecords", "users_TotalRecords", ccsText, "", CCGetRequestParam("users_TotalRecords", ccsGet, NULL), $this);
        $this->Sorter_user_id = & new clsSorter($this->ComponentName, "Sorter_user_id", $FileName, $this);
        $this->Sorter_user_login = & new clsSorter($this->ComponentName, "Sorter_user_login", $FileName, $this);
        $this->Sorter_user_first_name = & new clsSorter($this->ComponentName, "Sorter_user_first_name", $FileName, $this);
        $this->Sorter_user_last_name = & new clsSorter($this->ComponentName, "Sorter_user_last_name", $FileName, $this);
        $this->Sorter_user_level = & new clsSorter($this->ComponentName, "Sorter_user_level", $FileName, $this);
        $this->Sorter_user_email = & new clsSorter($this->ComponentName, "Sorter_user_email", $FileName, $this);
        $this->Sorter_user_date_add = & new clsSorter($this->ComponentName, "Sorter_user_date_add", $FileName, $this);
        $this->Sorter_user_last_login = & new clsSorter($this->ComponentName, "Sorter_user_last_login", $FileName, $this);
        $this->Sorter_user_is_approved = & new clsSorter($this->ComponentName, "Sorter_user_is_approved", $FileName, $this);
        $this->users_Insert = & new clsControl(ccsLink, "users_Insert", "users_Insert", ccsText, "", CCGetRequestParam("users_Insert", ccsGet, NULL), $this);
        $this->users_Insert->Parameters = CCGetQueryString("QueryString", array("user_id", "ccsForm"));
        $this->users_Insert->Page = "users_maint.php";
        $this->Navigator = & new clsNavigator($this->ComponentName, "Navigator", $FileName, 10, tpCentered, $this);
    }
//End Class_Initialize Event

//Initialize Method @2-90E704C5
    function Initialize()
    {
        if(!$this->Visible) return;

        $this->DataSource->PageSize = & $this->PageSize;
        $this->DataSource->AbsolutePage = & $this->PageNumber;
        $this->DataSource->SetOrder($this->SorterName, $this->SorterDirection);
    }
//End Initialize Method

//Show Method @2-60B55776
    function Show()
    {
        global $Tpl;
        global $CCSLocales;
        if(!$this->Visible) return;

        $this->RowNumber = 0;

        $this->DataSource->Parameters["urls_keyword"] = CCGetFromGet("s_keyword", NULL);
        $this->DataSource->Parameters["urls_user_level"] = CCGetFromGet("s_user_level", NULL);
        $this->DataSource->Parameters["urls_user_is_approved"] = CCGetFromGet("s_user_is_approved", NULL);

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
            $this->ControlsVisible["user_id"] = $this->user_id->Visible;
            $this->ControlsVisible["user_login"] = $this->user_login->Visible;
            $this->ControlsVisible["user_first_name"] = $this->user_first_name->Visible;
            $this->ControlsVisible["user_last_name"] = $this->user_last_name->Visible;
            $this->ControlsVisible["user_level"] = $this->user_level->Visible;
            $this->ControlsVisible["user_email"] = $this->user_email->Visible;
            $this->ControlsVisible["user_date_add"] = $this->user_date_add->Visible;
            $this->ControlsVisible["user_last_login"] = $this->user_last_login->Visible;
            $this->ControlsVisible["user_is_approved"] = $this->user_is_approved->Visible;
            do {
                $this->RowNumber++;
                $this->DataSource->SetValues();
                $Tpl->block_path = $ParentPath . "/" . $GridBlock . "/Row";
                $this->user_id->SetValue($this->DataSource->user_id->GetValue());
                $this->user_login->SetValue($this->DataSource->user_login->GetValue());
                $this->user_login->Parameters = CCGetQueryString("QueryString", array("ccsForm"));
                $this->user_login->Parameters = CCAddParam($this->user_login->Parameters, "user_id", $this->DataSource->f("user_id"));
                $this->user_first_name->SetValue($this->DataSource->user_first_name->GetValue());
                $this->user_last_name->SetValue($this->DataSource->user_last_name->GetValue());
                $this->user_level->SetValue($this->DataSource->user_level->GetValue());
                $this->user_email->SetValue($this->DataSource->user_email->GetValue());
                $this->user_date_add->SetValue($this->DataSource->user_date_add->GetValue());
                $this->user_last_login->SetValue($this->DataSource->user_last_login->GetValue());
                $this->user_is_approved->SetValue($this->DataSource->user_is_approved->GetValue());
                $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowRow", $this);
                $this->user_id->Show();
                $this->user_login->Show();
                $this->user_first_name->Show();
                $this->user_last_name->Show();
                $this->user_level->Show();
                $this->user_email->Show();
                $this->user_date_add->Show();
                $this->user_last_login->Show();
                $this->user_is_approved->Show();
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
        $this->users_TotalRecords->Show();
        $this->Sorter_user_id->Show();
        $this->Sorter_user_login->Show();
        $this->Sorter_user_first_name->Show();
        $this->Sorter_user_last_name->Show();
        $this->Sorter_user_level->Show();
        $this->Sorter_user_email->Show();
        $this->Sorter_user_date_add->Show();
        $this->Sorter_user_last_login->Show();
        $this->Sorter_user_is_approved->Show();
        $this->users_Insert->Show();
        $this->Navigator->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->DataSource->close();
    }
//End Show Method

//GetErrors Method @2-2EC421B8
    function GetErrors()
    {
        $errors = "";
        $errors = ComposeStrings($errors, $this->user_id->Errors->ToString());
        $errors = ComposeStrings($errors, $this->user_login->Errors->ToString());
        $errors = ComposeStrings($errors, $this->user_first_name->Errors->ToString());
        $errors = ComposeStrings($errors, $this->user_last_name->Errors->ToString());
        $errors = ComposeStrings($errors, $this->user_level->Errors->ToString());
        $errors = ComposeStrings($errors, $this->user_email->Errors->ToString());
        $errors = ComposeStrings($errors, $this->user_date_add->Errors->ToString());
        $errors = ComposeStrings($errors, $this->user_last_login->Errors->ToString());
        $errors = ComposeStrings($errors, $this->user_is_approved->Errors->ToString());
        $errors = ComposeStrings($errors, $this->Errors->ToString());
        $errors = ComposeStrings($errors, $this->DataSource->Errors->ToString());
        return $errors;
    }
//End GetErrors Method

} //End users Class @2-FCB6E20C

class clsusersDataSource extends clsDBcalendar {  //usersDataSource Class @2-1B89833B

//DataSource Variables @2-DB44E0F4
    var $Parent = "";
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;
    var $CmdExecution;

    var $CountSQL;
    var $wp;


    // Datasource fields
    var $user_id;
    var $user_login;
    var $user_first_name;
    var $user_last_name;
    var $user_level;
    var $user_email;
    var $user_date_add;
    var $user_last_login;
    var $user_is_approved;
//End DataSource Variables

//DataSourceClass_Initialize Event @2-8597763D
    function clsusersDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "Grid users";
        $this->Initialize();
        $this->user_id = new clsField("user_id", ccsInteger, "");
        $this->user_login = new clsField("user_login", ccsText, "");
        $this->user_first_name = new clsField("user_first_name", ccsText, "");
        $this->user_last_name = new clsField("user_last_name", ccsText, "");
        $this->user_level = new clsField("user_level", ccsText, "");
        $this->user_email = new clsField("user_email", ccsText, "");
        $this->user_date_add = new clsField("user_date_add", ccsDate, array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"));
        $this->user_last_login = new clsField("user_last_login", ccsText, "");
        $this->user_is_approved = new clsField("user_is_approved", ccsBoolean, array(1, 0, ""));

    }
//End DataSourceClass_Initialize Event

//SetOrder Method @2-FC8EC1AA
    function SetOrder($SorterName, $SorterDirection)
    {
        $this->Order = "user_id";
        $this->Order = CCGetOrder($this->Order, $SorterName, $SorterDirection, 
            array("Sorter_user_id" => array("user_id", ""), 
            "Sorter_user_login" => array("user_login", ""), 
            "Sorter_user_first_name" => array("user_first_name", ""), 
            "Sorter_user_last_name" => array("user_last_name", ""), 
            "Sorter_user_level" => array("user_level", ""), 
            "Sorter_user_email" => array("user_email", ""), 
            "Sorter_user_date_add" => array("user_date_add", ""), 
            "Sorter_user_last_login" => array("user_last_login", ""), 
            "Sorter_user_is_approved" => array("user_is_approved", "")));
    }
//End SetOrder Method

//Prepare Method @2-3D901595
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "urls_keyword", ccsText, "", "", $this->Parameters["urls_keyword"], "", false);
        $this->wp->AddParameter("2", "urls_keyword", ccsText, "", "", $this->Parameters["urls_keyword"], "", false);
        $this->wp->AddParameter("3", "urls_keyword", ccsText, "", "", $this->Parameters["urls_keyword"], "", false);
        $this->wp->AddParameter("4", "urls_keyword", ccsText, "", "", $this->Parameters["urls_keyword"], "", false);
        $this->wp->AddParameter("5", "urls_user_level", ccsInteger, "", "", $this->Parameters["urls_user_level"], "", false);
        $this->wp->AddParameter("6", "urls_user_is_approved", ccsInteger, "", "", $this->Parameters["urls_user_is_approved"], "", false);
        $this->wp->Criterion[1] = $this->wp->Operation(opContains, "user_login", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsText),false);
        $this->wp->Criterion[2] = $this->wp->Operation(opContains, "user_email", $this->wp->GetDBValue("2"), $this->ToSQL($this->wp->GetDBValue("2"), ccsText),false);
        $this->wp->Criterion[3] = $this->wp->Operation(opContains, "user_first_name", $this->wp->GetDBValue("3"), $this->ToSQL($this->wp->GetDBValue("3"), ccsText),false);
        $this->wp->Criterion[4] = $this->wp->Operation(opContains, "user_last_name", $this->wp->GetDBValue("4"), $this->ToSQL($this->wp->GetDBValue("4"), ccsText),false);
        $this->wp->Criterion[5] = $this->wp->Operation(opEqual, "user_level", $this->wp->GetDBValue("5"), $this->ToSQL($this->wp->GetDBValue("5"), ccsInteger),false);
        $this->wp->Criterion[6] = $this->wp->Operation(opEqual, "user_is_approved", $this->wp->GetDBValue("6"), $this->ToSQL($this->wp->GetDBValue("6"), ccsInteger),false);
        $this->Where = $this->wp->opAND(
             false, $this->wp->opAND(
             false, $this->wp->opOR(
             true, $this->wp->opOR(
             false, $this->wp->opOR(
             false, 
             $this->wp->Criterion[1], 
             $this->wp->Criterion[2]), 
             $this->wp->Criterion[3]), 
             $this->wp->Criterion[4]), 
             $this->wp->Criterion[5]), 
             $this->wp->Criterion[6]);
    }
//End Prepare Method

//Open Method @2-5A3D0247
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->CountSQL = "SELECT COUNT(*)\n\n" .
        "FROM users";
        $this->SQL = "SELECT user_id, user_login, user_level, user_email, user_first_name, user_last_name, user_is_approved, user_access_code, user_date_add,\n\n" .
        "user_last_login \n\n" .
        "FROM users {SQL_Where} {SQL_OrderBy}";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect", $this->Parent);
        if ($this->CountSQL) 
            $this->RecordsCount = CCGetDBValue(CCBuildSQL($this->CountSQL, $this->Where, ""), $this);
        else
            $this->RecordsCount = "CCS not counted";
        $this->query($this->OptimizeSQL(CCBuildSQL($this->SQL, $this->Where, $this->Order)));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect", $this->Parent);
    }
//End Open Method

//SetValues Method @2-182D01F6
    function SetValues()
    {
        $this->user_id->SetDBValue(trim($this->f("user_id")));
        $this->user_login->SetDBValue($this->f("user_login"));
        $this->user_first_name->SetDBValue($this->f("user_first_name"));
        $this->user_last_name->SetDBValue($this->f("user_last_name"));
        $this->user_level->SetDBValue($this->f("user_level"));
        $this->user_email->SetDBValue($this->f("user_email"));
        $this->user_date_add->SetDBValue(trim($this->f("user_date_add")));
        $this->user_last_login->SetDBValue($this->f("user_last_login"));
        $this->user_is_approved->SetDBValue(trim($this->f("user_is_approved")));
    }
//End SetValues Method

} //End usersDataSource Class @2-FCB6E20C

//Initialize Page @1-8572E12B
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
$TemplateFileName = "users.html";
$BlockToParse = "main";
$TemplateEncoding = "UTF-8";
$PathToRoot = "../";
//End Initialize Page

//Authenticate User @1-F87A9DA3
CCSecurityRedirect("100", "../login.php");
//End Authenticate User

//Include events file @1-1192EF6F
include("./users_events.php");
//End Include events file

//Initialize Objects @1-86192B1B
$DBcalendar = new clsDBcalendar();
$MainPage->Connections["calendar"] = & $DBcalendar;

// Controls
$header = & new clsheader("", "header", $MainPage);
$header->Initialize();
$usersSearch = & new clsRecordusersSearch("", $MainPage);
$users = & new clsGridusers("", $MainPage);
$MainPage->header = & $header;
$MainPage->usersSearch = & $usersSearch;
$MainPage->users = & $users;
$users->Initialize();

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

//Execute Components @1-4239C016
$header->Operations();
$usersSearch->Operation();
//End Execute Components

//Go to destination page @1-66E88856
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
    $DBcalendar->close();
    header("Location: " . $Redirect);
    $header->Class_Terminate();
    unset($header);
    unset($usersSearch);
    unset($users);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-62E7F47E
$header->Show();
$usersSearch->Show();
$users->Show();
$Tpl->block_path = "";
$Tpl->Parse($BlockToParse, false);
$main_block = $Tpl->GetVar($BlockToParse);
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeOutput", $MainPage);
if ($CCSEventResult) echo $main_block;
//End Show Page

//Unload Page @1-AA4501FF
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
$DBcalendar->close();
$header->Class_Terminate();
unset($header);
unset($usersSearch);
unset($users);
unset($Tpl);
//End Unload Page


?>
