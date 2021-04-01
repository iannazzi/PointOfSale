<?php
//Include Common Files @1-AA304B87
define("RelativePath", ".");
define("PathToCurrentPage", "/");
define("FileName", "lost_password.php");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
//End Include Common Files

//Include Page implementation @2-8EACA429
include_once(RelativePath . "/header.php");
//End Include Page implementation

//Include Page implementation @24-D3FCB384
include_once(RelativePath . "/vertical_menu.php");
//End Include Page implementation

class clsRecordChangePassword { //ChangePassword Class @5-6A5D6FAA

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

//Class_Initialize Event @5-83C6897C
    function clsRecordChangePassword($RelativePath, & $Parent)
    {

        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->Visible = true;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Record ChangePassword/Error";
        $this->ReadAllowed = true;
        if($this->Visible)
        {
            $this->ComponentName = "ChangePassword";
            $CCSForm = split(":", CCGetFromGet("ccsForm", ""), 2);
            if(sizeof($CCSForm) == 1)
                $CCSForm[1] = "";
            list($FormName, $FormMethod) = $CCSForm;
            $this->FormEnctype = "application/x-www-form-urlencoded";
            $this->FormSubmitted = ($FormName == $this->ComponentName);
            $Method = $this->FormSubmitted ? ccsPost : ccsGet;
            $this->ContentLabel = & new clsControl(ccsLabel, "ContentLabel", "ContentLabel", ccsText, "", CCGetRequestParam("ContentLabel", $Method, NULL), $this);
            $this->ContentLabel->HTML = true;
            $this->new_password = & new clsControl(ccsTextBox, "new_password", $CCSLocales->GetText("cal_new_password"), ccsText, "", CCGetRequestParam("new_password", $Method, NULL), $this);
            $this->new_password_confirm = & new clsControl(ccsTextBox, "new_password_confirm", $CCSLocales->GetText("cal_new_password_confirm"), ccsText, "", CCGetRequestParam("new_password_confirm", $Method, NULL), $this);
            $this->Button_Update = & new clsButton("Button_Update", $Method, $this);
        }
    }
//End Class_Initialize Event

//Validate Method @5-3D7370DA
    function Validate()
    {
        global $CCSLocales;
        $Validation = true;
        $Where = "";
        $Validation = ($this->new_password->Validate() && $Validation);
        $Validation = ($this->new_password_confirm->Validate() && $Validation);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate", $this);
        $Validation =  $Validation && ($this->new_password->Errors->Count() == 0);
        $Validation =  $Validation && ($this->new_password_confirm->Errors->Count() == 0);
        return (($this->Errors->Count() == 0) && $Validation);
    }
//End Validate Method

//CheckErrors Method @5-D7B79897
    function CheckErrors()
    {
        $errors = false;
        $errors = ($errors || $this->ContentLabel->Errors->Count());
        $errors = ($errors || $this->new_password->Errors->Count());
        $errors = ($errors || $this->new_password_confirm->Errors->Count());
        $errors = ($errors || $this->Errors->Count());
        return $errors;
    }
//End CheckErrors Method

//Operation Method @5-2ABF4E36
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
            $this->PressedButton = "Button_Update";
            if($this->Button_Update->Pressed) {
                $this->PressedButton = "Button_Update";
            }
        }
        $Redirect = "info.php" . "?" . CCGetQueryString("QueryString", array("ccsForm"));
        if($this->Validate()) {
            if($this->PressedButton == "Button_Update") {
                if(!CCGetEvent($this->Button_Update->CCSEvents, "OnClick", $this->Button_Update)) {
                    $Redirect = "";
                }
            }
        } else {
            $Redirect = "";
        }
    }
//End Operation Method

//Show Method @5-547E8513
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
        if (!$this->FormSubmitted) {
        }

        if($this->FormSubmitted || $this->CheckErrors()) {
            $Error = "";
            $Error = ComposeStrings($Error, $this->ContentLabel->Errors->ToString());
            $Error = ComposeStrings($Error, $this->new_password->Errors->ToString());
            $Error = ComposeStrings($Error, $this->new_password_confirm->Errors->ToString());
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

        $this->ContentLabel->Show();
        $this->new_password->Show();
        $this->new_password_confirm->Show();
        $this->Button_Update->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
    }
//End Show Method

} //End ChangePassword Class @5-FCB6E20C

//Include Page implementation @3-EBA5EA16
include_once(RelativePath . "/footer.php");
//End Include Page implementation

//Initialize Page @1-B0F9931C
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
$TemplateFileName = "lost_password.html";
$BlockToParse = "main";
$TemplateEncoding = "UTF-8";
$PathToRoot = "./";
//End Initialize Page

//Include events file @1-AD1D876C
include("./lost_password_events.php");
//End Include events file

//Initialize Objects @1-5882C2F3

// Controls
$header = & new clsheader("", "header", $MainPage);
$header->Initialize();
$vertical_menu = & new clsvertical_menu("", "vertical_menu", $MainPage);
$vertical_menu->Initialize();
$ChangePassword = & new clsRecordChangePassword("", $MainPage);
$footer = & new clsfooter("", "footer", $MainPage);
$footer->Initialize();
$MainPage->header = & $header;
$MainPage->vertical_menu = & $vertical_menu;
$MainPage->ChangePassword = & $ChangePassword;
$MainPage->footer = & $footer;

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

//Execute Components @1-7FBDEF95
$header->Operations();
$vertical_menu->Operations();
$ChangePassword->Operation();
$footer->Operations();
//End Execute Components

//Go to destination page @1-F0518D50
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
    header("Location: " . $Redirect);
    $header->Class_Terminate();
    unset($header);
    $vertical_menu->Class_Terminate();
    unset($vertical_menu);
    unset($ChangePassword);
    $footer->Class_Terminate();
    unset($footer);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-F9554073
$header->Show();
$vertical_menu->Show();
$ChangePassword->Show();
$footer->Show();
$Tpl->block_path = "";
$Tpl->Parse($BlockToParse, false);
$main_block = $Tpl->GetVar($BlockToParse);
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeOutput", $MainPage);
if ($CCSEventResult) echo $main_block;
//End Show Page

//Unload Page @1-FB076289
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
$header->Class_Terminate();
unset($header);
$vertical_menu->Class_Terminate();
unset($vertical_menu);
unset($ChangePassword);
$footer->Class_Terminate();
unset($footer);
unset($Tpl);
//End Unload Page


?>
