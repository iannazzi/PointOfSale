<?php
//Include Common Files @1-AD365165
define("RelativePath", ".");
define("PathToCurrentPage", "/");
define("FileName", "profile.php");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
//End Include Common Files

//Include Page implementation @2-8EACA429
include_once(RelativePath . "/header.php");
//End Include Page implementation

//Include Page implementation @24-8BC56D3E
include_once(RelativePath . "/profile_menu.php");
//End Include Page implementation

class clsRecordusers { //users Class @16-9BE1AF6F

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

//Class_Initialize Event @16-3749E262
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
        $this->UpdateAllowed = true;
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
            $this->user_email = & new clsControl(ccsTextBox, "user_email", $CCSLocales->GetText("user_email"), ccsText, "", CCGetRequestParam("user_email", $Method, NULL), $this);
            $this->user_email->Required = true;
            $this->user_first_name = & new clsControl(ccsTextBox, "user_first_name", $CCSLocales->GetText("user_first_name"), ccsText, "", CCGetRequestParam("user_first_name", $Method, NULL), $this);
            $this->user_first_name->Required = true;
            $this->user_last_name = & new clsControl(ccsTextBox, "user_last_name", $CCSLocales->GetText("user_last_name"), ccsText, "", CCGetRequestParam("user_last_name", $Method, NULL), $this);
            $this->user_last_name->Required = true;
            $this->Button_Update = & new clsButton("Button_Update", $Method, $this);
        }
    }
//End Class_Initialize Event

//Initialize Method @16-D3026D7D
    function Initialize()
    {

        if(!$this->Visible)
            return;

        $this->DataSource->Parameters["sesUserID"] = CCGetSession("UserID", NULL);
    }
//End Initialize Method

//Validate Method @16-BE471C80
    function Validate()
    {
        global $CCSLocales;
        $Validation = true;
        $Where = "";
        $Validation = ($this->user_email->Validate() && $Validation);
        $Validation = ($this->user_first_name->Validate() && $Validation);
        $Validation = ($this->user_last_name->Validate() && $Validation);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate", $this);
        $Validation =  $Validation && ($this->user_email->Errors->Count() == 0);
        $Validation =  $Validation && ($this->user_first_name->Errors->Count() == 0);
        $Validation =  $Validation && ($this->user_last_name->Errors->Count() == 0);
        return (($this->Errors->Count() == 0) && $Validation);
    }
//End Validate Method

//CheckErrors Method @16-C7394761
    function CheckErrors()
    {
        $errors = false;
        $errors = ($errors || $this->user_login->Errors->Count());
        $errors = ($errors || $this->user_email->Errors->Count());
        $errors = ($errors || $this->user_first_name->Errors->Count());
        $errors = ($errors || $this->user_last_name->Errors->Count());
        $errors = ($errors || $this->Errors->Count());
        $errors = ($errors || $this->DataSource->Errors->Count());
        return $errors;
    }
//End CheckErrors Method

//Operation Method @16-87FFBAFF
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
            $this->PressedButton = $this->EditMode ? "Button_Update" : "";
            if($this->Button_Update->Pressed) {
                $this->PressedButton = "Button_Update";
            }
        }
        $Redirect = "profile.php" . "?" . CCGetQueryString("QueryString", array("ccsForm"));
        if($this->Validate()) {
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

//UpdateRow Method @16-467FA672
    function UpdateRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeUpdate", $this);
        if(!$this->UpdateAllowed) return false;
        $this->DataSource->user_login->SetValue($this->user_login->GetValue(true));
        $this->DataSource->user_email->SetValue($this->user_email->GetValue(true));
        $this->DataSource->user_first_name->SetValue($this->user_first_name->GetValue(true));
        $this->DataSource->user_last_name->SetValue($this->user_last_name->GetValue(true));
        $this->DataSource->Update();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterUpdate", $this);
        return (!$this->CheckErrors());
    }
//End UpdateRow Method

//Show Method @16-0261A38D
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
                if(!$this->FormSubmitted){
                    $this->user_email->SetValue($this->DataSource->user_email->GetValue());
                    $this->user_first_name->SetValue($this->DataSource->user_first_name->GetValue());
                    $this->user_last_name->SetValue($this->DataSource->user_last_name->GetValue());
                }
            } else {
                $this->EditMode = false;
            }
        }

        if($this->FormSubmitted || $this->CheckErrors()) {
            $Error = "";
            $Error = ComposeStrings($Error, $this->user_login->Errors->ToString());
            $Error = ComposeStrings($Error, $this->user_email->Errors->ToString());
            $Error = ComposeStrings($Error, $this->user_first_name->Errors->ToString());
            $Error = ComposeStrings($Error, $this->user_last_name->Errors->ToString());
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

        $this->user_login->Show();
        $this->user_email->Show();
        $this->user_first_name->Show();
        $this->user_last_name->Show();
        $this->Button_Update->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->DataSource->close();
    }
//End Show Method

} //End users Class @16-FCB6E20C

class clsusersDataSource extends clsDBcalendar {  //usersDataSource Class @16-1B89833B

//DataSource Variables @16-6225598B
    var $Parent = "";
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;
    var $CmdExecution;

    var $UpdateParameters;
    var $wp;
    var $AllParametersSet;


    // Datasource fields
    var $user_login;
    var $user_email;
    var $user_first_name;
    var $user_last_name;
//End DataSource Variables

//DataSourceClass_Initialize Event @16-4609235E
    function clsusersDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "Record users/Error";
        $this->Initialize();
        $this->user_login = new clsField("user_login", ccsText, "");
        $this->user_email = new clsField("user_email", ccsText, "");
        $this->user_first_name = new clsField("user_first_name", ccsText, "");
        $this->user_last_name = new clsField("user_last_name", ccsText, "");

        $this->UpdateFields["user_email"] = array("Name" => "user_email", "Value" => "", "DataType" => ccsText);
        $this->UpdateFields["user_first_name"] = array("Name" => "user_first_name", "Value" => "", "DataType" => ccsText);
        $this->UpdateFields["user_last_name"] = array("Name" => "user_last_name", "Value" => "", "DataType" => ccsText);
    }
//End DataSourceClass_Initialize Event

//Prepare Method @16-F4AB1646
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "sesUserID", ccsInteger, "", "", $this->Parameters["sesUserID"], "", true);
        $this->AllParametersSet = $this->wp->AllParamsSet();
        $this->wp->Criterion[1] = $this->wp->Operation(opEqual, "user_id", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsInteger),true);
        $this->Where = 
             $this->wp->Criterion[1];
    }
//End Prepare Method

//Open Method @16-0C94DEC2
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

//SetValues Method @16-159D52C7
    function SetValues()
    {
        $this->user_login->SetDBValue($this->f("user_login"));
        $this->user_email->SetDBValue($this->f("user_email"));
        $this->user_first_name->SetDBValue($this->f("user_first_name"));
        $this->user_last_name->SetDBValue($this->f("user_last_name"));
    }
//End SetValues Method

//Update Method @16-6D7D4E17
    function Update()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->CmdExecution = true;
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildUpdate", $this->Parent);
        $this->UpdateFields["user_email"]["Value"] = $this->user_email->GetDBValue(true);
        $this->UpdateFields["user_first_name"]["Value"] = $this->user_first_name->GetDBValue(true);
        $this->UpdateFields["user_last_name"]["Value"] = $this->user_last_name->GetDBValue(true);
        $this->SQL = CCBuildUpdate("users", $this->UpdateFields, $this);
        $this->SQL = CCBuildSQL($this->SQL, $this->Where, "");
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteUpdate", $this->Parent);
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteUpdate", $this->Parent);
        }
    }
//End Update Method

} //End usersDataSource Class @16-FCB6E20C

//Include Page implementation @3-EBA5EA16
include_once(RelativePath . "/footer.php");
//End Include Page implementation

//Initialize Page @1-B4E09C72
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
$TemplateFileName = "profile.html";
$BlockToParse = "main";
$TemplateEncoding = "UTF-8";
$PathToRoot = "./";
//End Initialize Page

//Authenticate User @1-96D16013
CCSecurityRedirect("10;100", "login.php");
//End Authenticate User

//Initialize Objects @1-6A070460
$DBcalendar = new clsDBcalendar();
$MainPage->Connections["calendar"] = & $DBcalendar;

// Controls
$header = & new clsheader("", "header", $MainPage);
$header->Initialize();
$profile_menu = & new clsprofile_menu("", "profile_menu", $MainPage);
$profile_menu->Initialize();
$users = & new clsRecordusers("", $MainPage);
$footer = & new clsfooter("", "footer", $MainPage);
$footer->Initialize();
$MainPage->header = & $header;
$MainPage->profile_menu = & $profile_menu;
$MainPage->users = & $users;
$MainPage->footer = & $footer;
$users->Initialize();

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

//Execute Components @1-4CF5E262
$header->Operations();
$profile_menu->Operations();
$users->Operation();
$footer->Operations();
//End Execute Components

//Go to destination page @1-28FAE459
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
    $DBcalendar->close();
    header("Location: " . $Redirect);
    $header->Class_Terminate();
    unset($header);
    $profile_menu->Class_Terminate();
    unset($profile_menu);
    unset($users);
    $footer->Class_Terminate();
    unset($footer);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-8677E82C
$header->Show();
$profile_menu->Show();
$users->Show();
$footer->Show();
$Tpl->block_path = "";
$Tpl->Parse($BlockToParse, false);
$main_block = $Tpl->GetVar($BlockToParse);
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeOutput", $MainPage);
if ($CCSEventResult) echo $main_block;
//End Show Page

//Unload Page @1-DC3FAEB2
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
$DBcalendar->close();
$header->Class_Terminate();
unset($header);
$profile_menu->Class_Terminate();
unset($profile_menu);
unset($users);
$footer->Class_Terminate();
unset($footer);
unset($Tpl);
//End Unload Page


?>
