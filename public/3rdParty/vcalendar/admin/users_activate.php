<?php
//Include Common Files @1-9F9A8D26
define("RelativePath", "..");
define("PathToCurrentPage", "/admin/");
define("FileName", "users_activate.php");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
//End Include Common Files

//Include Page implementation @2-47CFCC1A
include_once(RelativePath . "/admin/header.php");
//End Include Page implementation

class clsRecordusers { //users Class @3-9BE1AF6F

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

//Class_Initialize Event @3-AC720ACE
    function clsRecordusers($RelativePath, & $Parent)
    {

        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->Visible = true;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Record users/Error";
        $this->DataSource = new clsusersDataSource($this);
        $this->ds = & $this->DataSource;
        $this->InsertAllowed = true;
        $this->UpdateAllowed = true;
        $this->DeleteAllowed = true;
        $this->ReadAllowed = true;
        if($this->Visible)
        {
            $this->ComponentName = "users";
            $CCSForm = split(":", CCGetFromGet("ccsForm", ""), 2);
            if(sizeof($CCSForm) == 1)
                $CCSForm[1] = "";
            list($FormName, $FormMethod) = $CCSForm;
            $this->EditMode = ($FormMethod == "Edit");
            $this->FormEnctype = "application/x-www-form-urlencoded";
            $this->FormSubmitted = ($FormName == $this->ComponentName);
            $Method = $this->FormSubmitted ? ccsPost : ccsGet;
            $this->user_login = & new clsControl(ccsLabel, "user_login", $CCSLocales->GetText("user_login"), ccsText, "", CCGetRequestParam("user_login", $Method, NULL), $this);
            $this->user_login_ = & new clsControl(ccsHidden, "user_login_", "user_login_", ccsText, "", CCGetRequestParam("user_login_", $Method, NULL), $this);
            $this->user_first_name = & new clsControl(ccsLabel, "user_first_name", $CCSLocales->GetText("user_first_name"), ccsText, "", CCGetRequestParam("user_first_name", $Method, NULL), $this);
            $this->user_last_name = & new clsControl(ccsLabel, "user_last_name", $CCSLocales->GetText("user_last_name"), ccsText, "", CCGetRequestParam("user_last_name", $Method, NULL), $this);
            $this->user_email = & new clsControl(ccsLabel, "user_email", $CCSLocales->GetText("user_email"), ccsText, "", CCGetRequestParam("user_email", $Method, NULL), $this);
            $this->user_email_ = & new clsControl(ccsHidden, "user_email_", "user_email_", ccsText, "", CCGetRequestParam("user_email_", $Method, NULL), $this);
            $this->user_date_add = & new clsControl(ccsLabel, "user_date_add", $CCSLocales->GetText("user_date_add"), ccsDate, array("GeneralDate"), CCGetRequestParam("user_date_add", $Method, NULL), $this);
            $this->Button_Update = & new clsButton("Button_Update", $Method, $this);
            $this->Button_Delete = & new clsButton("Button_Delete", $Method, $this);
            $this->Button_Cancel = & new clsButton("Button_Cancel", $Method, $this);
        }
    }
//End Class_Initialize Event

//Initialize Method @3-EC5B1549
    function Initialize()
    {

        if(!$this->Visible)
            return;

        $this->DataSource->Parameters["urluser_id"] = CCGetFromGet("user_id", NULL);
        $this->DataSource->Parameters["expr26"] = 0;
        $this->DataSource->Parameters["expr27"] = 1;
    }
//End Initialize Method

//Validate Method @3-157186E6
    function Validate()
    {
        global $CCSLocales;
        $Validation = true;
        $Where = "";
        $Validation = ($this->user_login_->Validate() && $Validation);
        $Validation = ($this->user_email_->Validate() && $Validation);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate", $this);
        $Validation =  $Validation && ($this->user_login_->Errors->Count() == 0);
        $Validation =  $Validation && ($this->user_email_->Errors->Count() == 0);
        return (($this->Errors->Count() == 0) && $Validation);
    }
//End Validate Method

//CheckErrors Method @3-6FF4B758
    function CheckErrors()
    {
        $errors = false;
        $errors = ($errors || $this->user_login->Errors->Count());
        $errors = ($errors || $this->user_login_->Errors->Count());
        $errors = ($errors || $this->user_first_name->Errors->Count());
        $errors = ($errors || $this->user_last_name->Errors->Count());
        $errors = ($errors || $this->user_email->Errors->Count());
        $errors = ($errors || $this->user_email_->Errors->Count());
        $errors = ($errors || $this->user_date_add->Errors->Count());
        $errors = ($errors || $this->Errors->Count());
        $errors = ($errors || $this->DataSource->Errors->Count());
        return $errors;
    }
//End CheckErrors Method

//Operation Method @3-5F0888D8
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
            } else if($this->Button_Delete->Pressed) {
                $this->PressedButton = "Button_Delete";
            } else if($this->Button_Cancel->Pressed) {
                $this->PressedButton = "Button_Cancel";
            }
        }
        $Redirect = "index.php";
        if($this->PressedButton == "Button_Delete") {
            if(!CCGetEvent($this->Button_Delete->CCSEvents, "OnClick", $this->Button_Delete) || !$this->DeleteRow()) {
                $Redirect = "";
            }
        } else if($this->PressedButton == "Button_Cancel") {
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

//InsertRow Method @3-0C26DFB6
    function InsertRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeInsert", $this);
        if(!$this->InsertAllowed) return false;
        $this->DataSource->user_login->SetValue($this->user_login->GetValue(true));
        $this->DataSource->user_login_->SetValue($this->user_login_->GetValue(true));
        $this->DataSource->user_first_name->SetValue($this->user_first_name->GetValue(true));
        $this->DataSource->user_last_name->SetValue($this->user_last_name->GetValue(true));
        $this->DataSource->user_email->SetValue($this->user_email->GetValue(true));
        $this->DataSource->user_email_->SetValue($this->user_email_->GetValue(true));
        $this->DataSource->user_date_add->SetValue($this->user_date_add->GetValue(true));
        $this->DataSource->Insert();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterInsert", $this);
        return (!$this->CheckErrors());
    }
//End InsertRow Method

//UpdateRow Method @3-92E06AB0
    function UpdateRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeUpdate", $this);
        if(!$this->UpdateAllowed) return false;
        $this->DataSource->Update();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterUpdate", $this);
        return (!$this->CheckErrors());
    }
//End UpdateRow Method

//DeleteRow Method @3-299D98C3
    function DeleteRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeDelete", $this);
        if(!$this->DeleteAllowed) return false;
        $this->DataSource->Delete();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterDelete", $this);
        return (!$this->CheckErrors());
    }
//End DeleteRow Method

//Show Method @3-8A48A951
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
                $this->user_login->SetValue($this->DataSource->user_login->GetValue());
                $this->user_first_name->SetValue($this->DataSource->user_first_name->GetValue());
                $this->user_last_name->SetValue($this->DataSource->user_last_name->GetValue());
                $this->user_email->SetValue($this->DataSource->user_email->GetValue());
                $this->user_date_add->SetValue($this->DataSource->user_date_add->GetValue());
                if(!$this->FormSubmitted){
                    $this->user_login_->SetValue($this->DataSource->user_login_->GetValue());
                    $this->user_email_->SetValue($this->DataSource->user_email_->GetValue());
                }
            } else {
                $this->EditMode = false;
            }
        }

        if($this->FormSubmitted || $this->CheckErrors()) {
            $Error = "";
            $Error = ComposeStrings($Error, $this->user_login->Errors->ToString());
            $Error = ComposeStrings($Error, $this->user_login_->Errors->ToString());
            $Error = ComposeStrings($Error, $this->user_first_name->Errors->ToString());
            $Error = ComposeStrings($Error, $this->user_last_name->Errors->ToString());
            $Error = ComposeStrings($Error, $this->user_email->Errors->ToString());
            $Error = ComposeStrings($Error, $this->user_email_->Errors->ToString());
            $Error = ComposeStrings($Error, $this->user_date_add->Errors->ToString());
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
        $this->Button_Delete->Visible = $this->EditMode && $this->DeleteAllowed;

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShow", $this);
        if(!$this->Visible) {
            $Tpl->block_path = $ParentPath;
            return;
        }

        $this->user_login->Show();
        $this->user_login_->Show();
        $this->user_first_name->Show();
        $this->user_last_name->Show();
        $this->user_email->Show();
        $this->user_email_->Show();
        $this->user_date_add->Show();
        $this->Button_Update->Show();
        $this->Button_Delete->Show();
        $this->Button_Cancel->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->DataSource->close();
    }
//End Show Method

} //End users Class @3-FCB6E20C

class clsusersDataSource extends clsDBcalendar {  //usersDataSource Class @3-1B89833B

//DataSource Variables @3-3D79D35F
    var $Parent = "";
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;
    var $CmdExecution;

    var $InsertParameters;
    var $UpdateParameters;
    var $DeleteParameters;
    var $wp;
    var $AllParametersSet;


    // Datasource fields
    var $user_login;
    var $user_login_;
    var $user_first_name;
    var $user_last_name;
    var $user_email;
    var $user_email_;
    var $user_date_add;
//End DataSource Variables

//DataSourceClass_Initialize Event @3-9D21BC52
    function clsusersDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "Record users/Error";
        $this->Initialize();
        $this->user_login = new clsField("user_login", ccsText, "");
        $this->user_login_ = new clsField("user_login_", ccsText, "");
        $this->user_first_name = new clsField("user_first_name", ccsText, "");
        $this->user_last_name = new clsField("user_last_name", ccsText, "");
        $this->user_email = new clsField("user_email", ccsText, "");
        $this->user_email_ = new clsField("user_email_", ccsText, "");
        $this->user_date_add = new clsField("user_date_add", ccsDate, array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"));

        $this->InsertFields["user_login"] = array("Name" => "user_login", "Value" => "", "DataType" => ccsText);
        $this->InsertFields["user_email"] = array("Name" => "user_email", "Value" => "", "DataType" => ccsText);
        $this->UpdateFields["user_is_approved"] = array("Name" => "user_is_approved", "Value" => "", "DataType" => ccsInteger);
        $this->UpdateFields["user_level"] = array("Name" => "user_level", "Value" => "", "DataType" => ccsInteger);
    }
//End DataSourceClass_Initialize Event

//Prepare Method @3-CA472D5B
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "urluser_id", ccsInteger, "", "", $this->Parameters["urluser_id"], "", false);
        $this->wp->AddParameter("2", "expr26", ccsInteger, "", "", $this->Parameters["expr26"], "", false);
        $this->wp->AddParameter("3", "expr27", ccsInteger, "", "", $this->Parameters["expr27"], "", false);
        $this->AllParametersSet = $this->wp->AllParamsSet();
        $this->wp->Criterion[1] = $this->wp->Operation(opEqual, "user_id", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsInteger),false);
        $this->wp->Criterion[2] = $this->wp->Operation(opEqual, "user_is_approved", $this->wp->GetDBValue("2"), $this->ToSQL($this->wp->GetDBValue("2"), ccsInteger),false);
        $this->wp->Criterion[3] = $this->wp->Operation(opEqual, "user_level", $this->wp->GetDBValue("3"), $this->ToSQL($this->wp->GetDBValue("3"), ccsInteger),false);
        $this->Where = $this->wp->opAND(
             false, $this->wp->opAND(
             false, 
             $this->wp->Criterion[1], 
             $this->wp->Criterion[2]), 
             $this->wp->Criterion[3]);
    }
//End Prepare Method

//Open Method @3-0A9AA136
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->SQL = "SELECT user_login, user_email, user_first_name, user_last_name, user_date_add \n\n" .
        "FROM users {SQL_Where} {SQL_OrderBy}";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect", $this->Parent);
        $this->PageSize = 1;
        $this->query($this->OptimizeSQL(CCBuildSQL($this->SQL, $this->Where, $this->Order)));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect", $this->Parent);
    }
//End Open Method

//SetValues Method @3-61943A57
    function SetValues()
    {
        $this->user_login->SetDBValue($this->f("user_login"));
        $this->user_login_->SetDBValue($this->f("user_login"));
        $this->user_first_name->SetDBValue($this->f("user_first_name"));
        $this->user_last_name->SetDBValue($this->f("user_last_name"));
        $this->user_email->SetDBValue($this->f("user_email"));
        $this->user_email_->SetDBValue($this->f("user_email"));
        $this->user_date_add->SetDBValue(trim($this->f("user_date_add")));
    }
//End SetValues Method

//Insert Method @3-373AEBAD
    function Insert()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->CmdExecution = true;
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildInsert", $this->Parent);
        $this->InsertFields["user_login"]["Value"] = $this->user_login_->GetDBValue(true);
        $this->InsertFields["user_email"]["Value"] = $this->user_email_->GetDBValue(true);
        $this->SQL = CCBuildInsert("users", $this->InsertFields, $this);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteInsert", $this->Parent);
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteInsert", $this->Parent);
        }
    }
//End Insert Method

//Update Method @3-CBB8C738
    function Update()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->CmdExecution = true;
        $this->cp["user_is_approved"] = new clsSQLParameter("expr20", ccsInteger, "", "", 1, "", false, $this->ErrorBlock);
        $this->cp["user_level"] = new clsSQLParameter("expr22", ccsInteger, "", "", 10, "", false, $this->ErrorBlock);
        $wp = new clsSQLParameters($this->ErrorBlock);
        $wp->AddParameter("1", "urluser_id", ccsInteger, "", "", CCGetFromGet("user_id", NULL), "", false);
        if(!$wp->AllParamsSet()) {
            $this->Errors->addError($CCSLocales->GetText("CCS_CustomOperationError_MissingParameters"));
        }
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildUpdate", $this->Parent);
        if (!strlen($this->cp["user_is_approved"]->GetText()) and !is_bool($this->cp["user_is_approved"]->GetValue())) 
            $this->cp["user_is_approved"]->SetValue(1);
        if (!strlen($this->cp["user_level"]->GetText()) and !is_bool($this->cp["user_level"]->GetValue())) 
            $this->cp["user_level"]->SetValue(10);
        $wp->Criterion[1] = $wp->Operation(opEqual, "user_id", $wp->GetDBValue("1"), $this->ToSQL($wp->GetDBValue("1"), ccsInteger),false);
        $Where = 
             $wp->Criterion[1];
        $this->UpdateFields["user_is_approved"]["Value"] = $this->cp["user_is_approved"]->GetDBValue(true);
        $this->UpdateFields["user_level"]["Value"] = $this->cp["user_level"]->GetDBValue(true);
        $this->SQL = CCBuildUpdate("users", $this->UpdateFields, $this);
        $this->SQL = CCBuildSQL($this->SQL, $Where, "");
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteUpdate", $this->Parent);
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteUpdate", $this->Parent);
        }
    }
//End Update Method

//Delete Method @3-CD45C16E
    function Delete()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->CmdExecution = true;
        $wp = new clsSQLParameters($this->ErrorBlock);
        $wp->AddParameter("1", "urluser_id", ccsInteger, "", "", CCGetFromGet("user_id", NULL), "", false);
        if(!$wp->AllParamsSet()) {
            $this->Errors->addError($CCSLocales->GetText("CCS_CustomOperationError_MissingParameters"));
        }
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildDelete", $this->Parent);
        $wp->Criterion[1] = $wp->Operation(opEqual, "user_id", $wp->GetDBValue("1"), $this->ToSQL($wp->GetDBValue("1"), ccsInteger),false);
        $Where = 
             $wp->Criterion[1];
        $this->SQL = "DELETE FROM users";
        $this->SQL = CCBuildSQL($this->SQL, $Where, "");
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteDelete", $this->Parent);
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteDelete", $this->Parent);
        }
    }
//End Delete Method

} //End usersDataSource Class @3-FCB6E20C

//Initialize Page @1-2AB062FF
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
$TemplateFileName = "users_activate.html";
$BlockToParse = "main";
$TemplateEncoding = "UTF-8";
$PathToRoot = "../";
//End Initialize Page

//Authenticate User @1-132EF5B6
CCSecurityRedirect("100", "");
//End Authenticate User

//Include events file @1-9B599878
include("./users_activate_events.php");
//End Include events file

//Initialize Objects @1-2D11AC98
$DBcalendar = new clsDBcalendar();
$MainPage->Connections["calendar"] = & $DBcalendar;

// Controls
$header = & new clsheader("", "header", $MainPage);
$header->Initialize();
$users = & new clsRecordusers("", $MainPage);
$MainPage->header = & $header;
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

//Execute Components @1-C396ECC4
$header->Operations();
$users->Operation();
//End Execute Components

//Go to destination page @1-B9B360A9
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
    $DBcalendar->close();
    header("Location: " . $Redirect);
    $header->Class_Terminate();
    unset($header);
    unset($users);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-5CBAD8F6
$header->Show();
$users->Show();
$Tpl->block_path = "";
$Tpl->Parse($BlockToParse, false);
$main_block = $Tpl->GetVar($BlockToParse);
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeOutput", $MainPage);
if ($CCSEventResult) echo $main_block;
//End Show Page

//Unload Page @1-2003E681
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
$DBcalendar->close();
$header->Class_Terminate();
unset($header);
unset($users);
unset($Tpl);
//End Unload Page


?>
