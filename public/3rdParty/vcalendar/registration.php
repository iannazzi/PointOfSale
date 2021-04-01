<?php
//Include Common Files @1-A6672A7B
define("RelativePath", ".");
define("PathToCurrentPage", "/");
define("FileName", "registration.php");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
//End Include Common Files

//Include Page implementation @17-8EACA429
include_once(RelativePath . "/header.php");
//End Include Page implementation

//Include Page implementation @25-D3FCB384
include_once(RelativePath . "/vertical_menu.php");
//End Include Page implementation

class clsRecordusers { //users Class @5-9BE1AF6F

//Variables @5-0DF9B1C2

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

//Class_Initialize Event @5-B906C3A3
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
            $this->user_login = & new clsControl(ccsTextBox, "user_login", $CCSLocales->GetText("user_login"), ccsText, "", CCGetRequestParam("user_login", $Method, NULL), $this);
            $this->user_login->Required = true;
            $this->user_password = & new clsControl(ccsTextBox, "user_password", $CCSLocales->GetText("user_password"), ccsText, "", CCGetRequestParam("user_password", $Method, NULL), $this);
            $this->user_password->Required = true;
            $this->ConfirmPassword = & new clsControl(ccsTextBox, "ConfirmPassword", $CCSLocales->GetText("user_confirm_password"), ccsText, "", CCGetRequestParam("ConfirmPassword", $Method, NULL), $this);
            $this->ConfirmPassword->Required = true;
            $this->user_first_name = & new clsControl(ccsTextBox, "user_first_name", $CCSLocales->GetText("user_first_name"), ccsText, "", CCGetRequestParam("user_first_name", $Method, NULL), $this);
            $this->user_first_name->Required = true;
            $this->user_last_name = & new clsControl(ccsTextBox, "user_last_name", $CCSLocales->GetText("user_last_name"), ccsText, "", CCGetRequestParam("user_last_name", $Method, NULL), $this);
            $this->user_last_name->Required = true;
            $this->user_email = & new clsControl(ccsTextBox, "user_email", $CCSLocales->GetText("user_email"), ccsText, "", CCGetRequestParam("user_email", $Method, NULL), $this);
            $this->user_email->Required = true;
            $this->Button_Insert = & new clsButton("Button_Insert", $Method, $this);
            $this->user_is_approved = & new clsControl(ccsHidden, "user_is_approved", $CCSLocales->GetText("user_is_approved"), ccsInteger, "", CCGetRequestParam("user_is_approved", $Method, NULL), $this);
            $this->user_level = & new clsControl(ccsHidden, "user_level", $CCSLocales->GetText("user_level"), ccsInteger, "", CCGetRequestParam("user_level", $Method, NULL), $this);
            $this->user_access_code = & new clsControl(ccsHidden, "user_access_code", "user_access_code", ccsInteger, "", CCGetRequestParam("user_access_code", $Method, NULL), $this);
            $this->user_date_add = & new clsControl(ccsHidden, "user_date_add", "user_date_add", ccsDate, array("GeneralDate"), CCGetRequestParam("user_date_add", $Method, NULL), $this);
            if(!$this->FormSubmitted) {
                if(!is_array($this->user_access_code->Value) && !strlen($this->user_access_code->Value) && $this->user_access_code->Value !== false)
                    $this->user_access_code->SetText(0);
                if(!is_array($this->user_date_add->Value) && !strlen($this->user_date_add->Value) && $this->user_date_add->Value !== false)
                    $this->user_date_add->SetValue(time());
            }
        }
    }
//End Class_Initialize Event

//Initialize Method @5-53A359F1
    function Initialize()
    {

        if(!$this->Visible)
            return;

        $this->DataSource->Parameters["urluser_id"] = CCGetFromGet("user_id", NULL);
    }
//End Initialize Method

//Validate Method @5-07B5E00D
    function Validate()
    {
        global $CCSLocales;
        $Validation = true;
        $Where = "";
        if($this->EditMode && strlen($this->DataSource->Where))
            $Where = " AND NOT (" . $this->DataSource->Where . ")";
        $this->DataSource->user_login->SetValue($this->user_login->GetValue());
        if(CCDLookUp("COUNT(*)", "users", "user_login=" . $this->DataSource->ToSQL($this->DataSource->user_login->GetDBValue(), $this->DataSource->user_login->DataType) . $Where, $this->DataSource) > 0)
            $this->user_login->Errors->addError($CCSLocales->GetText("CCS_UniqueValue", $CCSLocales->GetText("user_login")));
        $this->DataSource->user_email->SetValue($this->user_email->GetValue());
        if(CCDLookUp("COUNT(*)", "users", "user_email=" . $this->DataSource->ToSQL($this->DataSource->user_email->GetDBValue(), $this->DataSource->user_email->DataType) . $Where, $this->DataSource) > 0)
            $this->user_email->Errors->addError($CCSLocales->GetText("CCS_UniqueValue", $CCSLocales->GetText("user_email")));
        if(strlen($this->user_email->GetText()) && !preg_match ("/^[\w\.-]{1,}\@([\da-zA-Z-]{1,}\.){1,}[\da-zA-Z-]+$/", $this->user_email->GetText())) {
            $this->user_email->Errors->addError($CCSLocales->GetText("CCS_MaskValidation", $CCSLocales->GetText("user_email")));
        }
        $Validation = ($this->user_login->Validate() && $Validation);
        $Validation = ($this->user_password->Validate() && $Validation);
        $Validation = ($this->ConfirmPassword->Validate() && $Validation);
        $Validation = ($this->user_first_name->Validate() && $Validation);
        $Validation = ($this->user_last_name->Validate() && $Validation);
        $Validation = ($this->user_email->Validate() && $Validation);
        $Validation = ($this->user_is_approved->Validate() && $Validation);
        $Validation = ($this->user_level->Validate() && $Validation);
        $Validation = ($this->user_access_code->Validate() && $Validation);
        $Validation = ($this->user_date_add->Validate() && $Validation);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate", $this);
        $Validation =  $Validation && ($this->user_login->Errors->Count() == 0);
        $Validation =  $Validation && ($this->user_password->Errors->Count() == 0);
        $Validation =  $Validation && ($this->ConfirmPassword->Errors->Count() == 0);
        $Validation =  $Validation && ($this->user_first_name->Errors->Count() == 0);
        $Validation =  $Validation && ($this->user_last_name->Errors->Count() == 0);
        $Validation =  $Validation && ($this->user_email->Errors->Count() == 0);
        $Validation =  $Validation && ($this->user_is_approved->Errors->Count() == 0);
        $Validation =  $Validation && ($this->user_level->Errors->Count() == 0);
        $Validation =  $Validation && ($this->user_access_code->Errors->Count() == 0);
        $Validation =  $Validation && ($this->user_date_add->Errors->Count() == 0);
        return (($this->Errors->Count() == 0) && $Validation);
    }
//End Validate Method

//CheckErrors Method @5-FE6C4534
    function CheckErrors()
    {
        $errors = false;
        $errors = ($errors || $this->user_login->Errors->Count());
        $errors = ($errors || $this->user_password->Errors->Count());
        $errors = ($errors || $this->ConfirmPassword->Errors->Count());
        $errors = ($errors || $this->user_first_name->Errors->Count());
        $errors = ($errors || $this->user_last_name->Errors->Count());
        $errors = ($errors || $this->user_email->Errors->Count());
        $errors = ($errors || $this->user_is_approved->Errors->Count());
        $errors = ($errors || $this->user_level->Errors->Count());
        $errors = ($errors || $this->user_access_code->Errors->Count());
        $errors = ($errors || $this->user_date_add->Errors->Count());
        $errors = ($errors || $this->Errors->Count());
        $errors = ($errors || $this->DataSource->Errors->Count());
        return $errors;
    }
//End CheckErrors Method

//Operation Method @5-6C77A9C9
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
            $this->PressedButton = "Button_Insert";
            if($this->Button_Insert->Pressed) {
                $this->PressedButton = "Button_Insert";
            }
        }
        $Redirect = "info.php" . "?" . CCGetQueryString("QueryString", array("ccsForm"));
        if($this->Validate()) {
            if($this->PressedButton == "Button_Insert") {
                if(!CCGetEvent($this->Button_Insert->CCSEvents, "OnClick", $this->Button_Insert) || !$this->InsertRow()) {
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

//InsertRow Method @5-44ADFC7E
    function InsertRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeInsert", $this);
        if(!$this->InsertAllowed) return false;
        $this->DataSource->user_login->SetValue($this->user_login->GetValue(true));
        $this->DataSource->user_password->SetValue($this->user_password->GetValue(true));
        $this->DataSource->ConfirmPassword->SetValue($this->ConfirmPassword->GetValue(true));
        $this->DataSource->user_first_name->SetValue($this->user_first_name->GetValue(true));
        $this->DataSource->user_last_name->SetValue($this->user_last_name->GetValue(true));
        $this->DataSource->user_email->SetValue($this->user_email->GetValue(true));
        $this->DataSource->user_is_approved->SetValue($this->user_is_approved->GetValue(true));
        $this->DataSource->user_level->SetValue($this->user_level->GetValue(true));
        $this->DataSource->user_access_code->SetValue($this->user_access_code->GetValue(true));
        $this->DataSource->user_date_add->SetValue($this->user_date_add->GetValue(true));
        $this->DataSource->Insert();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterInsert", $this);
        return (!$this->CheckErrors());
    }
//End InsertRow Method

//Show Method @5-A52CF99C
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
                if(!$this->FormSubmitted){
                    $this->user_login->SetValue($this->DataSource->user_login->GetValue());
                    $this->user_password->SetValue($this->DataSource->user_password->GetValue());
                    $this->user_first_name->SetValue($this->DataSource->user_first_name->GetValue());
                    $this->user_last_name->SetValue($this->DataSource->user_last_name->GetValue());
                    $this->user_email->SetValue($this->DataSource->user_email->GetValue());
                    $this->user_is_approved->SetValue($this->DataSource->user_is_approved->GetValue());
                    $this->user_level->SetValue($this->DataSource->user_level->GetValue());
                    $this->user_access_code->SetValue($this->DataSource->user_access_code->GetValue());
                    $this->user_date_add->SetValue($this->DataSource->user_date_add->GetValue());
                }
            } else {
                $this->EditMode = false;
            }
        }
        if (!$this->FormSubmitted) {
        }

        if($this->FormSubmitted || $this->CheckErrors()) {
            $Error = "";
            $Error = ComposeStrings($Error, $this->user_login->Errors->ToString());
            $Error = ComposeStrings($Error, $this->user_password->Errors->ToString());
            $Error = ComposeStrings($Error, $this->ConfirmPassword->Errors->ToString());
            $Error = ComposeStrings($Error, $this->user_first_name->Errors->ToString());
            $Error = ComposeStrings($Error, $this->user_last_name->Errors->ToString());
            $Error = ComposeStrings($Error, $this->user_email->Errors->ToString());
            $Error = ComposeStrings($Error, $this->user_is_approved->Errors->ToString());
            $Error = ComposeStrings($Error, $this->user_level->Errors->ToString());
            $Error = ComposeStrings($Error, $this->user_access_code->Errors->ToString());
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
        $this->Button_Insert->Visible = !$this->EditMode && $this->InsertAllowed;

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShow", $this);
        if(!$this->Visible) {
            $Tpl->block_path = $ParentPath;
            return;
        }

        $this->user_login->Show();
        $this->user_password->Show();
        $this->ConfirmPassword->Show();
        $this->user_first_name->Show();
        $this->user_last_name->Show();
        $this->user_email->Show();
        $this->Button_Insert->Show();
        $this->user_is_approved->Show();
        $this->user_level->Show();
        $this->user_access_code->Show();
        $this->user_date_add->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->DataSource->close();
    }
//End Show Method

} //End users Class @5-FCB6E20C

class clsusersDataSource extends clsDBcalendar {  //usersDataSource Class @5-1B89833B

//DataSource Variables @5-12AA2BCC
    var $Parent = "";
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;
    var $CmdExecution;

    var $InsertParameters;
    var $wp;
    var $AllParametersSet;


    // Datasource fields
    var $user_login;
    var $user_password;
    var $ConfirmPassword;
    var $user_first_name;
    var $user_last_name;
    var $user_email;
    var $user_is_approved;
    var $user_level;
    var $user_access_code;
    var $user_date_add;
//End DataSource Variables

//DataSourceClass_Initialize Event @5-E0C3A47C
    function clsusersDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "Record users/Error";
        $this->Initialize();
        $this->user_login = new clsField("user_login", ccsText, "");
        $this->user_password = new clsField("user_password", ccsText, "");
        $this->ConfirmPassword = new clsField("ConfirmPassword", ccsText, "");
        $this->user_first_name = new clsField("user_first_name", ccsText, "");
        $this->user_last_name = new clsField("user_last_name", ccsText, "");
        $this->user_email = new clsField("user_email", ccsText, "");
        $this->user_is_approved = new clsField("user_is_approved", ccsInteger, "");
        $this->user_level = new clsField("user_level", ccsInteger, "");
        $this->user_access_code = new clsField("user_access_code", ccsInteger, "");
        $this->user_date_add = new clsField("user_date_add", ccsDate, array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"));

        $this->InsertFields["user_login"] = array("Name" => "user_login", "Value" => "", "DataType" => ccsText);
        $this->InsertFields["user_password"] = array("Name" => "user_password", "Value" => "", "DataType" => ccsText);
        $this->InsertFields["user_first_name"] = array("Name" => "user_first_name", "Value" => "", "DataType" => ccsText);
        $this->InsertFields["user_last_name"] = array("Name" => "user_last_name", "Value" => "", "DataType" => ccsText);
        $this->InsertFields["user_email"] = array("Name" => "user_email", "Value" => "", "DataType" => ccsText);
        $this->InsertFields["user_is_approved"] = array("Name" => "user_is_approved", "Value" => "", "DataType" => ccsInteger);
        $this->InsertFields["user_level"] = array("Name" => "user_level", "Value" => "", "DataType" => ccsInteger);
        $this->InsertFields["user_access_code"] = array("Name" => "user_access_code", "Value" => "", "DataType" => ccsInteger);
        $this->InsertFields["user_date_add"] = array("Name" => "user_date_add", "Value" => "", "DataType" => ccsDate);
    }
//End DataSourceClass_Initialize Event

//Prepare Method @5-B49E291C
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "urluser_id", ccsInteger, "", "", $this->Parameters["urluser_id"], "", false);
        $this->AllParametersSet = $this->wp->AllParamsSet();
        $this->wp->Criterion[1] = $this->wp->Operation(opEqual, "user_id", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsInteger),false);
        $this->Where = 
             $this->wp->Criterion[1];
    }
//End Prepare Method

//Open Method @5-0C94DEC2
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->SQL = "SELECT * \n\n" .
        "FROM users {SQL_Where} {SQL_OrderBy}";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect", $this->Parent);
        $this->PageSize = 1;
        $this->query($this->OptimizeSQL(CCBuildSQL($this->SQL, $this->Where, $this->Order)));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect", $this->Parent);
    }
//End Open Method

//SetValues Method @5-FD834952
    function SetValues()
    {
        $this->user_login->SetDBValue($this->f("user_login"));
        $this->user_password->SetDBValue($this->f("user_password"));
        $this->user_first_name->SetDBValue($this->f("user_first_name"));
        $this->user_last_name->SetDBValue($this->f("user_last_name"));
        $this->user_email->SetDBValue($this->f("user_email"));
        $this->user_is_approved->SetDBValue(trim($this->f("user_is_approved")));
        $this->user_level->SetDBValue(trim($this->f("user_level")));
        $this->user_access_code->SetDBValue(trim($this->f("user_access_code")));
        $this->user_date_add->SetDBValue(trim($this->f("user_date_add")));
    }
//End SetValues Method

//Insert Method @5-7AECCFB0
    function Insert()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->CmdExecution = true;
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildInsert", $this->Parent);
        $this->InsertFields["user_login"]["Value"] = $this->user_login->GetDBValue(true);
        $this->InsertFields["user_password"]["Value"] = $this->user_password->GetDBValue(true);
        $this->InsertFields["user_first_name"]["Value"] = $this->user_first_name->GetDBValue(true);
        $this->InsertFields["user_last_name"]["Value"] = $this->user_last_name->GetDBValue(true);
        $this->InsertFields["user_email"]["Value"] = $this->user_email->GetDBValue(true);
        $this->InsertFields["user_is_approved"]["Value"] = $this->user_is_approved->GetDBValue(true);
        $this->InsertFields["user_level"]["Value"] = $this->user_level->GetDBValue(true);
        $this->InsertFields["user_access_code"]["Value"] = $this->user_access_code->GetDBValue(true);
        $this->InsertFields["user_date_add"]["Value"] = $this->user_date_add->GetDBValue(true);
        $this->SQL = CCBuildInsert("users", $this->InsertFields, $this);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteInsert", $this->Parent);
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteInsert", $this->Parent);
        }
    }
//End Insert Method

} //End usersDataSource Class @5-FCB6E20C

//Include Page implementation @19-EBA5EA16
include_once(RelativePath . "/footer.php");
//End Include Page implementation

//Initialize Page @1-C7FA8F62
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
$TemplateFileName = "registration.html";
$BlockToParse = "main";
$TemplateEncoding = "UTF-8";
$PathToRoot = "./";
//End Initialize Page

//Include events file @1-F4AACDE6
include("./registration_events.php");
//End Include events file

//Initialize Objects @1-537DC63F
$DBcalendar = new clsDBcalendar();
$MainPage->Connections["calendar"] = & $DBcalendar;

// Controls
$header = & new clsheader("", "header", $MainPage);
$header->Initialize();
$vertical_menu = & new clsvertical_menu("", "vertical_menu", $MainPage);
$vertical_menu->Initialize();
$users = & new clsRecordusers("", $MainPage);
$footer = & new clsfooter("", "footer", $MainPage);
$footer->Initialize();
$MainPage->header = & $header;
$MainPage->vertical_menu = & $vertical_menu;
$MainPage->users = & $users;
$MainPage->footer = & $footer;
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

//Execute Components @1-8AD289C1
$header->Operations();
$vertical_menu->Operations();
$users->Operation();
$footer->Operations();
//End Execute Components

//Go to destination page @1-1147220E
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
    $DBcalendar->close();
    header("Location: " . $Redirect);
    $header->Class_Terminate();
    unset($header);
    $vertical_menu->Class_Terminate();
    unset($vertical_menu);
    unset($users);
    $footer->Class_Terminate();
    unset($footer);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-84CD612C
$header->Show();
$vertical_menu->Show();
$users->Show();
$footer->Show();
$Tpl->block_path = "";
$Tpl->Parse($BlockToParse, false);
$main_block = $Tpl->GetVar($BlockToParse);
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeOutput", $MainPage);
if ($CCSEventResult) echo $main_block;
//End Show Page

//Unload Page @1-FA13854C
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
$DBcalendar->close();
$header->Class_Terminate();
unset($header);
$vertical_menu->Class_Terminate();
unset($vertical_menu);
unset($users);
$footer->Class_Terminate();
unset($footer);
unset($Tpl);
//End Unload Page


?>
