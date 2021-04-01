<?php
class clsRecordheaderHMenu { //HMenu Class @38-905DB1A6

//Variables @38-0DF9B1C2

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

//Class_Initialize Event @38-619EEA79
    function clsRecordheaderHMenu($RelativePath, & $Parent)
    {

        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->Visible = true;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Record HMenu/Error";
        $this->ReadAllowed = true;
        if($this->Visible)
        {
            $this->ComponentName = "HMenu";
            $CCSForm = split(":", CCGetFromGet("ccsForm", ""), 2);
            if(sizeof($CCSForm) == 1)
                $CCSForm[1] = "";
            list($FormName, $FormMethod) = $CCSForm;
            $this->FormEnctype = "application/x-www-form-urlencoded";
            $this->FormSubmitted = ($FormName == $this->ComponentName);
            $Method = $this->FormSubmitted ? ccsPost : ccsGet;
            $this->home = & new clsControl(ccsLink, "home", "home", ccsText, "", CCGetRequestParam("home", $Method, NULL), $this);
            $this->home->Page = $this->RelativePath . "../index.php";
            $this->users = & new clsControl(ccsLink, "users", "users", ccsText, "", CCGetRequestParam("users", $Method, NULL), $this);
            $this->users->Page = $this->RelativePath . "users.php";
            $this->categories = & new clsControl(ccsLink, "categories", "categories", ccsText, "", CCGetRequestParam("categories", $Method, NULL), $this);
            $this->categories->Page = $this->RelativePath . "categories.php";
            $this->config = & new clsControl(ccsLink, "config", "config", ccsText, "", CCGetRequestParam("config", $Method, NULL), $this);
            $this->config->Page = $this->RelativePath . "config.php";
            $this->messages = & new clsControl(ccsLink, "messages", "messages", ccsText, "", CCGetRequestParam("messages", $Method, NULL), $this);
            $this->messages->Page = $this->RelativePath . "content.php";
            $this->permissions = & new clsControl(ccsLink, "permissions", "permissions", ccsText, "", CCGetRequestParam("permissions", $Method, NULL), $this);
            $this->permissions->Page = $this->RelativePath . "permissions.php";
            $this->email_templates = & new clsControl(ccsLink, "email_templates", "email_templates", ccsText, "", CCGetRequestParam("email_templates", $Method, NULL), $this);
            $this->email_templates->Page = $this->RelativePath . "email_templates.php";
            $this->custom_fields = & new clsControl(ccsLink, "custom_fields", "custom_fields", ccsText, "", CCGetRequestParam("custom_fields", $Method, NULL), $this);
            $this->custom_fields->Page = $this->RelativePath . "custom_fields.php";
            $this->logout = & new clsControl(ccsLink, "logout", "logout", ccsText, "", CCGetRequestParam("logout", $Method, NULL), $this);
            $this->logout->Page = $this->RelativePath . "../index.php";
            $this->user_login = & new clsControl(ccsLabel, "user_login", "user_login", ccsText, "", CCGetRequestParam("user_login", $Method, NULL), $this);
            $this->style = & new clsControl(ccsListBox, "style", "style", ccsText, "", CCGetRequestParam("style", $Method, NULL), $this);
            $this->style->DSType = dsListOfValues;
            $this->style->Values = array(array("Basic", "Basic"), array("Blueprint", "Blueprint"), array("CoffeeBreak", "CoffeeBreak"), array("Compact", "Compact"), array("GreenApple", "GreenApple"), array("Innovation", "Innovation"), array("Pine", "Pine"), array("SandBeach", "SandBeach"), array("School", "School"));
            $this->locale = & new clsControl(ccsListBox, "locale", "locale", ccsText, "", CCGetRequestParam("locale", $Method, NULL), $this);
            $this->locale->DSType = dsListOfValues;
            $this->locale->Values = array(array("en", $CCSLocales->GetText("cal_english")), array("ru", $CCSLocales->GetText("cal_russian")));
            $this->Button_Apply = & new clsButton("Button_Apply", $Method, $this);
            if(!is_array($this->user_login->Value) && !strlen($this->user_login->Value) && $this->user_login->Value !== false)
                $this->user_login->SetText(CCGetUserLogin());
        }
    }
//End Class_Initialize Event

//Validate Method @38-8DF0FD0E
    function Validate()
    {
        global $CCSLocales;
        $Validation = true;
        $Where = "";
        $Validation = ($this->style->Validate() && $Validation);
        $Validation = ($this->locale->Validate() && $Validation);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate", $this);
        $Validation =  $Validation && ($this->style->Errors->Count() == 0);
        $Validation =  $Validation && ($this->locale->Errors->Count() == 0);
        return (($this->Errors->Count() == 0) && $Validation);
    }
//End Validate Method

//CheckErrors Method @38-48F6518D
    function CheckErrors()
    {
        $errors = false;
        $errors = ($errors || $this->home->Errors->Count());
        $errors = ($errors || $this->users->Errors->Count());
        $errors = ($errors || $this->categories->Errors->Count());
        $errors = ($errors || $this->config->Errors->Count());
        $errors = ($errors || $this->messages->Errors->Count());
        $errors = ($errors || $this->permissions->Errors->Count());
        $errors = ($errors || $this->email_templates->Errors->Count());
        $errors = ($errors || $this->custom_fields->Errors->Count());
        $errors = ($errors || $this->logout->Errors->Count());
        $errors = ($errors || $this->user_login->Errors->Count());
        $errors = ($errors || $this->style->Errors->Count());
        $errors = ($errors || $this->locale->Errors->Count());
        $errors = ($errors || $this->Errors->Count());
        return $errors;
    }
//End CheckErrors Method

//Operation Method @38-7EEB6976
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
            $this->PressedButton = "Button_Apply";
            if($this->Button_Apply->Pressed) {
                $this->PressedButton = "Button_Apply";
            }
        }
        $Redirect = $FileName . "?" . CCGetQueryString("QueryString", array("ccsForm"));
        if($this->Validate()) {
            if($this->PressedButton == "Button_Apply") {
                if(!CCGetEvent($this->Button_Apply->CCSEvents, "OnClick", $this->Button_Apply)) {
                    $Redirect = "";
                }
            }
        } else {
            $Redirect = "";
        }
    }
//End Operation Method

//Show Method @38-D205B4E1
    function Show()
    {
        global $Tpl;
        global $FileName;
        global $CCSLocales;
        $Error = "";

        if(!$this->Visible)
            return;

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeSelect", $this);

        $this->style->Prepare();
        $this->locale->Prepare();

        $RecordBlock = "Record " . $this->ComponentName;
        $ParentPath = $Tpl->block_path;
        $Tpl->block_path = $ParentPath . "/" . $RecordBlock;
        $this->EditMode = $this->EditMode && $this->ReadAllowed;
        if (!$this->FormSubmitted) {
        }
        $this->logout->Parameters = "";
        $this->logout->Parameters = CCAddParam($this->logout->Parameters, "Logout", 1);

        if($this->FormSubmitted || $this->CheckErrors()) {
            $Error = "";
            $Error = ComposeStrings($Error, $this->home->Errors->ToString());
            $Error = ComposeStrings($Error, $this->users->Errors->ToString());
            $Error = ComposeStrings($Error, $this->categories->Errors->ToString());
            $Error = ComposeStrings($Error, $this->config->Errors->ToString());
            $Error = ComposeStrings($Error, $this->messages->Errors->ToString());
            $Error = ComposeStrings($Error, $this->permissions->Errors->ToString());
            $Error = ComposeStrings($Error, $this->email_templates->Errors->ToString());
            $Error = ComposeStrings($Error, $this->custom_fields->Errors->ToString());
            $Error = ComposeStrings($Error, $this->logout->Errors->ToString());
            $Error = ComposeStrings($Error, $this->user_login->Errors->ToString());
            $Error = ComposeStrings($Error, $this->style->Errors->ToString());
            $Error = ComposeStrings($Error, $this->locale->Errors->ToString());
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

        $this->home->Show();
        $this->users->Show();
        $this->categories->Show();
        $this->config->Show();
        $this->messages->Show();
        $this->permissions->Show();
        $this->email_templates->Show();
        $this->custom_fields->Show();
        $this->logout->Show();
        $this->user_login->Show();
        $this->style->Show();
        $this->locale->Show();
        $this->Button_Apply->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
    }
//End Show Method

} //End HMenu Class @38-FCB6E20C

class clsheader { //header class @1-0325152D

//Variables @1-5DD9E934
    var $ComponentType = "IncludablePage";
    var $Connections = array();
    var $FileName = "";
    var $Redirect = "";
    var $Tpl = "";
    var $TemplateFileName = "";
    var $BlockToParse = "";
    var $ComponentName = "";

    // Events;
    var $CCSEvents = "";
    var $CCSEventResult = "";
    var $RelativePath;
    var $Visible;
    var $Parent;
//End Variables

//Class_Initialize Event @1-4E27C902
    function clsheader($RelativePath, $ComponentName, & $Parent)
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->ComponentName = $ComponentName;
        $this->RelativePath = $RelativePath;
        $this->Visible = true;
        $this->Parent = & $Parent;
        $this->FileName = "header.php";
        $this->Redirect = "";
        $this->TemplateFileName = "header.html";
        $this->BlockToParse = "main";
        $this->TemplateEncoding = "UTF-8";
    }
//End Class_Initialize Event

//Class_Terminate Event @1-0F4F21B7
    function Class_Terminate()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeUnload", $this);
        unset($this->HMenu);
    }
//End Class_Terminate Event

//BindEvents Method @1-855C4399
    function BindEvents()
    {
        $this->HMenu->style->CCSEvents["BeforeShow"] = "header_HMenu_style_BeforeShow";
        $this->HMenu->locale->CCSEvents["BeforeShow"] = "header_HMenu_locale_BeforeShow";
        $this->HMenu->Button_Apply->CCSEvents["OnClick"] = "header_HMenu_Button_Apply_OnClick";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterInitialize", $this);
    }
//End BindEvents Method

//Operations Method @1-E61862D0
    function Operations()
    {
        global $Redirect;
        if(!$this->Visible)
            return "";
        $this->HMenu->Operation();
    }
//End Operations Method

//Initialize Method @1-EE78286A
    function Initialize()
    {
        global $FileName;
        global $CCSLocales;
        if(!$this->Visible)
            return "";
        $this->DBcalendar = new clsDBcalendar();
        $this->Connections["calendar"] = & $this->DBcalendar;

        // Create Components
        $this->HMenu = & new clsRecordheaderHMenu($this->RelativePath, $this);
        $this->BindEvents();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnInitializeView", $this);
    }
//End Initialize Method

//Show Method @1-142D0C1E
    function Show()
    {
        global $Tpl;
        global $CCSLocales;
        $block_path = $Tpl->block_path;
        $Tpl->LoadTemplate("/admin/" . $this->TemplateFileName, $this->ComponentName, $this->TemplateEncoding, "remove");
        $Tpl->block_path = $Tpl->block_path . "/" . $this->ComponentName;
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShow", $this);
        if(!$this->Visible) {
            $Tpl->block_path = $block_path;
            $Tpl->SetVar($this->ComponentName, "");
            return "";
        }
        $this->HMenu->Show();
        $Tpl->Parse();
        $Tpl->block_path = $block_path;
        $Tpl->SetVar($this->ComponentName, $Tpl->GetVar($this->ComponentName));
    }
//End Show Method

} //End header Class @1-FCB6E20C

//Include Event File @1-1BEB9E34
include(RelativePath . "/admin/header_events.php");
//End Include Event File


?>
