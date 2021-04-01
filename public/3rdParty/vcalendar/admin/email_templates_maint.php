<?php
//Include Common Files @1-F5250563
define("RelativePath", "..");
define("PathToCurrentPage", "/admin/");
define("FileName", "email_templates_maint.php");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
//End Include Common Files

//Include Page implementation @13-47CFCC1A
include_once(RelativePath . "/admin/header.php");
//End Include Page implementation

class clsRecordemail_templates { //email_templates Class @2-EB637584

//Variables @2-0DF9B1C2

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

//Class_Initialize Event @2-F6FFE5F0
    function clsRecordemail_templates($RelativePath, & $Parent)
    {

        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->Visible = true;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Record email_templates/Error";
        $this->DataSource = new clsemail_templatesDataSource($this);
        $this->ds = & $this->DataSource;
        $this->UpdateAllowed = true;
        $this->DeleteAllowed = true;
        $this->ReadAllowed = true;
        if($this->Visible)
        {
            $this->ComponentName = "email_templates";
            $CCSForm = split(":", CCGetFromGet("ccsForm", ""), 2);
            if(sizeof($CCSForm) == 1)
                $CCSForm[1] = "";
            list($FormName, $FormMethod) = $CCSForm;
            $this->EditMode = ($FormMethod == "Edit");
            $this->FormEnctype = "application/x-www-form-urlencoded";
            $this->FormSubmitted = ($FormName == $this->ComponentName);
            $Method = $this->FormSubmitted ? ccsPost : ccsGet;
            $this->email_template_type = & new clsControl(ccsLabel, "email_template_type", $CCSLocales->GetText("email_template_type"), ccsText, "", CCGetRequestParam("email_template_type", $Method, NULL), $this);
            $this->email_template_desc = & new clsControl(ccsTextBox, "email_template_desc", $CCSLocales->GetText("email_template_desc"), ccsText, "", CCGetRequestParam("email_template_desc", $Method, NULL), $this);
            $this->email_template_from = & new clsControl(ccsTextBox, "email_template_from", $CCSLocales->GetText("email_template_from"), ccsText, "", CCGetRequestParam("email_template_from", $Method, NULL), $this);
            $this->email_template_subject = & new clsControl(ccsTextBox, "email_template_subject", $CCSLocales->GetText("email_template_subject"), ccsText, "", CCGetRequestParam("email_template_subject", $Method, NULL), $this);
            $this->email_template_subject->Required = true;
            $this->email_template_body = & new clsControl(ccsTextArea, "email_template_body", $CCSLocales->GetText("email_template_body"), ccsMemo, "", CCGetRequestParam("email_template_body", $Method, NULL), $this);
            $this->email_template_body->Required = true;
            $this->Button_Preview = & new clsButton("Button_Preview", $Method, $this);
            $this->Button_Update = & new clsButton("Button_Update", $Method, $this);
            $this->Button_Cancel = & new clsButton("Button_Cancel", $Method, $this);
        }
    }
//End Class_Initialize Event

//Initialize Method @2-92103BAE
    function Initialize()
    {

        if(!$this->Visible)
            return;

        $this->DataSource->Parameters["urlemail_template_id"] = CCGetFromGet("email_template_id", NULL);
        $this->DataSource->Parameters["seslocale"] = CCGetSession("locale", NULL);
    }
//End Initialize Method

//Validate Method @2-4730D46F
    function Validate()
    {
        global $CCSLocales;
        $Validation = true;
        $Where = "";
        $Validation = ($this->email_template_desc->Validate() && $Validation);
        $Validation = ($this->email_template_from->Validate() && $Validation);
        $Validation = ($this->email_template_subject->Validate() && $Validation);
        $Validation = ($this->email_template_body->Validate() && $Validation);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate", $this);
        $Validation =  $Validation && ($this->email_template_desc->Errors->Count() == 0);
        $Validation =  $Validation && ($this->email_template_from->Errors->Count() == 0);
        $Validation =  $Validation && ($this->email_template_subject->Errors->Count() == 0);
        $Validation =  $Validation && ($this->email_template_body->Errors->Count() == 0);
        return (($this->Errors->Count() == 0) && $Validation);
    }
//End Validate Method

//CheckErrors Method @2-C60A7F21
    function CheckErrors()
    {
        $errors = false;
        $errors = ($errors || $this->email_template_type->Errors->Count());
        $errors = ($errors || $this->email_template_desc->Errors->Count());
        $errors = ($errors || $this->email_template_from->Errors->Count());
        $errors = ($errors || $this->email_template_subject->Errors->Count());
        $errors = ($errors || $this->email_template_body->Errors->Count());
        $errors = ($errors || $this->Errors->Count());
        $errors = ($errors || $this->DataSource->Errors->Count());
        return $errors;
    }
//End CheckErrors Method

//Operation Method @2-AFE97809
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
            $this->PressedButton = $this->EditMode ? "Button_Update" : "Button_Preview";
            if($this->Button_Preview->Pressed) {
                $this->PressedButton = "Button_Preview";
            } else if($this->Button_Update->Pressed) {
                $this->PressedButton = "Button_Update";
            } else if($this->Button_Cancel->Pressed) {
                $this->PressedButton = "Button_Cancel";
            }
        }
        $Redirect = "email_templates.php" . "?" . CCGetQueryString("QueryString", array("ccsForm", "email_template_id"));
        if($this->PressedButton == "Button_Cancel") {
            if(!CCGetEvent($this->Button_Cancel->CCSEvents, "OnClick", $this->Button_Cancel)) {
                $Redirect = "";
            }
        } else if($this->Validate()) {
            if($this->PressedButton == "Button_Preview") {
                if(!CCGetEvent($this->Button_Preview->CCSEvents, "OnClick", $this->Button_Preview)) {
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

//UpdateRow Method @2-DE9147E1
    function UpdateRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeUpdate", $this);
        if(!$this->UpdateAllowed) return false;
        $this->DataSource->email_template_from->SetValue($this->email_template_from->GetValue(true));
        $this->DataSource->Update();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterUpdate", $this);
        return (!$this->CheckErrors());
    }
//End UpdateRow Method

//DeleteRow Method @2-299D98C3
    function DeleteRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeDelete", $this);
        if(!$this->DeleteAllowed) return false;
        $this->DataSource->Delete();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterDelete", $this);
        return (!$this->CheckErrors());
    }
//End DeleteRow Method

//Show Method @2-9020DEBF
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
                $this->email_template_type->SetValue($this->DataSource->email_template_type->GetValue());
                if(!$this->FormSubmitted){
                    $this->email_template_desc->SetValue($this->DataSource->email_template_desc->GetValue());
                    $this->email_template_from->SetValue($this->DataSource->email_template_from->GetValue());
                    $this->email_template_subject->SetValue($this->DataSource->email_template_subject->GetValue());
                    $this->email_template_body->SetValue($this->DataSource->email_template_body->GetValue());
                }
            } else {
                $this->EditMode = false;
            }
        }

        if($this->FormSubmitted || $this->CheckErrors()) {
            $Error = "";
            $Error = ComposeStrings($Error, $this->email_template_type->Errors->ToString());
            $Error = ComposeStrings($Error, $this->email_template_desc->Errors->ToString());
            $Error = ComposeStrings($Error, $this->email_template_from->Errors->ToString());
            $Error = ComposeStrings($Error, $this->email_template_subject->Errors->ToString());
            $Error = ComposeStrings($Error, $this->email_template_body->Errors->ToString());
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

        $this->email_template_type->Show();
        $this->email_template_desc->Show();
        $this->email_template_from->Show();
        $this->email_template_subject->Show();
        $this->email_template_body->Show();
        $this->Button_Preview->Show();
        $this->Button_Update->Show();
        $this->Button_Cancel->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->DataSource->close();
    }
//End Show Method

} //End email_templates Class @2-FCB6E20C

class clsemail_templatesDataSource extends clsDBcalendar {  //email_templatesDataSource Class @2-1BF23281

//DataSource Variables @2-7A38EFA2
    var $Parent = "";
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;
    var $CmdExecution;

    var $UpdateParameters;
    var $DeleteParameters;
    var $wp;
    var $AllParametersSet;


    // Datasource fields
    var $email_template_type;
    var $email_template_desc;
    var $email_template_from;
    var $email_template_subject;
    var $email_template_body;
//End DataSource Variables

//DataSourceClass_Initialize Event @2-A974BBE5
    function clsemail_templatesDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "Record email_templates/Error";
        $this->Initialize();
        $this->email_template_type = new clsField("email_template_type", ccsText, "");
        $this->email_template_desc = new clsField("email_template_desc", ccsText, "");
        $this->email_template_from = new clsField("email_template_from", ccsText, "");
        $this->email_template_subject = new clsField("email_template_subject", ccsText, "");
        $this->email_template_body = new clsField("email_template_body", ccsMemo, "");

        $this->UpdateFields["email_template_from"] = array("Name" => "email_template_from", "Value" => "", "DataType" => ccsText);
    }
//End DataSourceClass_Initialize Event

//Prepare Method @2-7640906F
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "urlemail_template_id", ccsInteger, "", "", $this->Parameters["urlemail_template_id"], "", false);
        $this->wp->AddParameter("2", "seslocale", ccsText, "", "", $this->Parameters["seslocale"], "", false);
        $this->AllParametersSet = $this->wp->AllParamsSet();
        $this->wp->Criterion[1] = $this->wp->Operation(opEqual, "email_templates.email_template_id", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsInteger),false);
        $this->wp->Criterion[2] = $this->wp->Operation(opEqual, "email_templates_lang.language_id", $this->wp->GetDBValue("2"), $this->ToSQL($this->wp->GetDBValue("2"), ccsText),false);
        $this->Where = $this->wp->opAND(
             false, 
             $this->wp->Criterion[1], 
             $this->wp->Criterion[2]);
    }
//End Prepare Method

//Open Method @2-8275DA74
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->SQL = "SELECT email_templates_lang.email_template_body AS email_templates_lang_email_template_body, email_templates_lang.email_template_subject AS email_templates_lang_email_template_subject,\n\n" .
        "email_templates_lang.email_template_desc AS email_templates_lang_email_template_desc, email_template_type, email_template_from \n\n" .
        "FROM email_templates_lang INNER JOIN email_templates ON\n\n" .
        "email_templates_lang.email_template_id = email_templates.email_template_id {SQL_Where} {SQL_OrderBy}";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect", $this->Parent);
        $this->PageSize = 1;
        $this->query($this->OptimizeSQL(CCBuildSQL($this->SQL, $this->Where, $this->Order)));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect", $this->Parent);
    }
//End Open Method

//SetValues Method @2-AB5B5B9A
    function SetValues()
    {
        $this->email_template_type->SetDBValue($this->f("email_template_type"));
        $this->email_template_desc->SetDBValue($this->f("email_templates_lang_email_template_desc"));
        $this->email_template_from->SetDBValue($this->f("email_template_from"));
        $this->email_template_subject->SetDBValue($this->f("email_templates_lang_email_template_subject"));
        $this->email_template_body->SetDBValue($this->f("email_templates_lang_email_template_body"));
    }
//End SetValues Method

//Update Method @2-56BF5F37
    function Update()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->CmdExecution = true;
        $this->cp["email_template_from"] = new clsSQLParameter("ctrlemail_template_from", ccsText, "", "", $this->email_template_from->GetValue(true), "", false, $this->ErrorBlock);
        $wp = new clsSQLParameters($this->ErrorBlock);
        $wp->AddParameter("1", "urlemail_template_id", ccsInteger, "", "", CCGetFromGet("email_template_id", NULL), "", false);
        if(!$wp->AllParamsSet()) {
            $this->Errors->addError($CCSLocales->GetText("CCS_CustomOperationError_MissingParameters"));
        }
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildUpdate", $this->Parent);
        if (!strlen($this->cp["email_template_from"]->GetText()) and !is_bool($this->cp["email_template_from"]->GetValue())) 
            $this->cp["email_template_from"]->SetValue($this->email_template_from->GetValue(true));
        $wp->Criterion[1] = $wp->Operation(opEqual, "email_templates.email_template_id", $wp->GetDBValue("1"), $this->ToSQL($wp->GetDBValue("1"), ccsInteger),false);
        $Where = 
             $wp->Criterion[1];
        $this->UpdateFields["email_template_from"]["Value"] = $this->cp["email_template_from"]->GetDBValue(true);
        $this->SQL = CCBuildUpdate("email_templates", $this->UpdateFields, $this);
        $this->SQL = CCBuildSQL($this->SQL, $Where, "");
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteUpdate", $this->Parent);
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteUpdate", $this->Parent);
        }
    }
//End Update Method

//Delete Method @2-F4A28F51
    function Delete()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->CmdExecution = true;
        $wp = new clsSQLParameters($this->ErrorBlock);
        $wp->AddParameter("1", "urlemail_template_id", ccsInteger, "", "", CCGetFromGet("email_template_id", NULL), "", false);
        if(!$wp->AllParamsSet()) {
            $this->Errors->addError($CCSLocales->GetText("CCS_CustomOperationError_MissingParameters"));
        }
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildDelete", $this->Parent);
        $wp->Criterion[1] = $wp->Operation(opEqual, "email_templates.email_template_id", $wp->GetDBValue("1"), $this->ToSQL($wp->GetDBValue("1"), ccsInteger),false);
        $Where = 
             $wp->Criterion[1];
        $this->SQL = "DELETE FROM email_templates";
        $this->SQL = CCBuildSQL($this->SQL, $Where, "");
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteDelete", $this->Parent);
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteDelete", $this->Parent);
        }
    }
//End Delete Method

} //End email_templatesDataSource Class @2-FCB6E20C

//Initialize Page @1-51CAD042
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
$TemplateFileName = "email_templates_maint.html";
$BlockToParse = "main";
$TemplateEncoding = "UTF-8";
$PathToRoot = "../";
//End Initialize Page

//Authenticate User @1-132EF5B6
CCSecurityRedirect("100", "");
//End Authenticate User

//Include events file @1-D1273B69
include("./email_templates_maint_events.php");
//End Include events file

//Initialize Objects @1-C04500B7
$DBcalendar = new clsDBcalendar();
$MainPage->Connections["calendar"] = & $DBcalendar;

// Controls
$header = & new clsheader("", "header", $MainPage);
$header->Initialize();
$email_templates = & new clsRecordemail_templates("", $MainPage);
$MainPage->header = & $header;
$MainPage->email_templates = & $email_templates;
$email_templates->Initialize();

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

//Execute Components @1-431131CF
$header->Operations();
$email_templates->Operation();
//End Execute Components

//Go to destination page @1-08EFF6F8
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
    $DBcalendar->close();
    header("Location: " . $Redirect);
    $header->Class_Terminate();
    unset($header);
    unset($email_templates);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-CBEC86E2
$header->Show();
$email_templates->Show();
$Tpl->block_path = "";
$Tpl->Parse($BlockToParse, false);
$main_block = $Tpl->GetVar($BlockToParse);
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeOutput", $MainPage);
if ($CCSEventResult) echo $main_block;
//End Show Page

//Unload Page @1-7C087989
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
$DBcalendar->close();
$header->Class_Terminate();
unset($header);
unset($email_templates);
unset($Tpl);
//End Unload Page


?>
