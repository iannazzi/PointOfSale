<?php
//Include Common Files @1-4BBA832D
define("RelativePath", "..");
define("PathToCurrentPage", "/admin/");
define("FileName", "users_maint.php");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
//End Include Common Files

//Include Page implementation @15-47CFCC1A
include_once(RelativePath . "/admin/header.php");
//End Include Page implementation

class clsRecordusers_maint { //users_maint Class @2-86BB7034

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

//Class_Initialize Event @2-DABD263D
    function clsRecordusers_maint($RelativePath, & $Parent)
    {

        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->Visible = true;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Record users_maint/Error";
        $this->DataSource = new clsusers_maintDataSource($this);
        $this->ds = & $this->DataSource;
        $this->InsertAllowed = true;
        $this->UpdateAllowed = true;
        $this->DeleteAllowed = true;
        $this->ReadAllowed = true;
        if($this->Visible)
        {
            $this->ComponentName = "users_maint";
            $CCSForm = split(":", CCGetFromGet("ccsForm", ""), 2);
            if(sizeof($CCSForm) == 1)
                $CCSForm[1] = "";
            list($FormName, $FormMethod) = $CCSForm;
            $this->EditMode = ($FormMethod == "Edit");
            $this->FormEnctype = "application/x-www-form-urlencoded";
            $this->FormSubmitted = ($FormName == $this->ComponentName);
            $Method = $this->FormSubmitted ? ccsPost : ccsGet;
            $this->user_login = & new clsControl(ccsTextBox, "user_login", $CCSLocales->GetText("user_login"), ccsText, "", CCGetRequestParam("user_login", $Method, NULL), $this);
            $this->user_login_label = & new clsControl(ccsLabel, "user_login_label", "user_login_label", ccsText, "", CCGetRequestParam("user_login_label", $Method, NULL), $this);
            $this->user_password = & new clsControl(ccsTextBox, "user_password", $CCSLocales->GetText("user_password"), ccsText, "", CCGetRequestParam("user_password", $Method, NULL), $this);
            $this->user_password->Required = true;
            $this->user_level = & new clsControl(ccsListBox, "user_level", $CCSLocales->GetText("user_level"), ccsInteger, "", CCGetRequestParam("user_level", $Method, NULL), $this);
            $this->user_level->DSType = dsListOfValues;
            $this->user_level->Values = array(array("1", $CCSLocales->GetText("non_confirmed_user")), array("10", $CCSLocales->GetText("user")), array("100", $CCSLocales->GetText("admin")));
            $this->user_level->Required = true;
            $this->user_email = & new clsControl(ccsTextBox, "user_email", $CCSLocales->GetText("user_email"), ccsText, "", CCGetRequestParam("user_email", $Method, NULL), $this);
            $this->user_email->Required = true;
            $this->user_first_name = & new clsControl(ccsTextBox, "user_first_name", $CCSLocales->GetText("user_first_name"), ccsText, "", CCGetRequestParam("user_first_name", $Method, NULL), $this);
            $this->user_first_name->Required = true;
            $this->user_last_name = & new clsControl(ccsTextBox, "user_last_name", $CCSLocales->GetText("user_last_name"), ccsText, "", CCGetRequestParam("user_last_name", $Method, NULL), $this);
            $this->user_last_name->Required = true;
            $this->user_is_approved = & new clsControl(ccsCheckBox, "user_is_approved", $CCSLocales->GetText("user_is_approved"), ccsInteger, "", CCGetRequestParam("user_is_approved", $Method, NULL), $this);
            $this->user_is_approved->CheckedValue = $this->user_is_approved->GetParsedValue(1);
            $this->user_is_approved->UncheckedValue = $this->user_is_approved->GetParsedValue(0);
            $this->user_date_add = & new clsControl(ccsLabel, "user_date_add", "user_date_add", ccsDate, array("GeneralDate"), CCGetRequestParam("user_date_add", $Method, NULL), $this);
            $this->user_date_add_h = & new clsControl(ccsHidden, "user_date_add_h", "user_date_add_h", ccsDate, array("GeneralDate"), CCGetRequestParam("user_date_add_h", $Method, NULL), $this);
            $this->user_last_login = & new clsControl(ccsLabel, "user_last_login", "user_last_login", ccsDate, array("GeneralDate"), CCGetRequestParam("user_last_login", $Method, NULL), $this);
            $this->Button_Insert = & new clsButton("Button_Insert", $Method, $this);
            $this->Button_Update = & new clsButton("Button_Update", $Method, $this);
            $this->Button_Delete = & new clsButton("Button_Delete", $Method, $this);
            $this->Button_Cancel = & new clsButton("Button_Cancel", $Method, $this);
            if(!$this->FormSubmitted) {
                if(!is_array($this->user_date_add_h->Value) && !strlen($this->user_date_add_h->Value) && $this->user_date_add_h->Value !== false)
                    $this->user_date_add_h->SetValue(time());
            }
        }
    }
//End Class_Initialize Event

//Initialize Method @2-53A359F1
    function Initialize()
    {

        if(!$this->Visible)
            return;

        $this->DataSource->Parameters["urluser_id"] = CCGetFromGet("user_id", NULL);
    }
//End Initialize Method

//Validate Method @2-82C909E4
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
        $Validation = ($this->user_level->Validate() && $Validation);
        $Validation = ($this->user_email->Validate() && $Validation);
        $Validation = ($this->user_first_name->Validate() && $Validation);
        $Validation = ($this->user_last_name->Validate() && $Validation);
        $Validation = ($this->user_is_approved->Validate() && $Validation);
        $Validation = ($this->user_date_add_h->Validate() && $Validation);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate", $this);
        $Validation =  $Validation && ($this->user_login->Errors->Count() == 0);
        $Validation =  $Validation && ($this->user_password->Errors->Count() == 0);
        $Validation =  $Validation && ($this->user_level->Errors->Count() == 0);
        $Validation =  $Validation && ($this->user_email->Errors->Count() == 0);
        $Validation =  $Validation && ($this->user_first_name->Errors->Count() == 0);
        $Validation =  $Validation && ($this->user_last_name->Errors->Count() == 0);
        $Validation =  $Validation && ($this->user_is_approved->Errors->Count() == 0);
        $Validation =  $Validation && ($this->user_date_add_h->Errors->Count() == 0);
        return (($this->Errors->Count() == 0) && $Validation);
    }
//End Validate Method

//CheckErrors Method @2-E6974157
    function CheckErrors()
    {
        $errors = false;
        $errors = ($errors || $this->user_login->Errors->Count());
        $errors = ($errors || $this->user_login_label->Errors->Count());
        $errors = ($errors || $this->user_password->Errors->Count());
        $errors = ($errors || $this->user_level->Errors->Count());
        $errors = ($errors || $this->user_email->Errors->Count());
        $errors = ($errors || $this->user_first_name->Errors->Count());
        $errors = ($errors || $this->user_last_name->Errors->Count());
        $errors = ($errors || $this->user_is_approved->Errors->Count());
        $errors = ($errors || $this->user_date_add->Errors->Count());
        $errors = ($errors || $this->user_date_add_h->Errors->Count());
        $errors = ($errors || $this->user_last_login->Errors->Count());
        $errors = ($errors || $this->Errors->Count());
        $errors = ($errors || $this->DataSource->Errors->Count());
        return $errors;
    }
//End CheckErrors Method

//Operation Method @2-B6896AEC
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
            $this->PressedButton = $this->EditMode ? "Button_Update" : "Button_Insert";
            if($this->Button_Insert->Pressed) {
                $this->PressedButton = "Button_Insert";
            } else if($this->Button_Update->Pressed) {
                $this->PressedButton = "Button_Update";
            } else if($this->Button_Delete->Pressed) {
                $this->PressedButton = "Button_Delete";
            } else if($this->Button_Cancel->Pressed) {
                $this->PressedButton = "Button_Cancel";
            }
        }
        $Redirect = "users.php" . "?" . CCGetQueryString("QueryString", array("ccsForm", "user_id", "ret_link"));
        if($this->PressedButton == "Button_Delete") {
            if(!CCGetEvent($this->Button_Delete->CCSEvents, "OnClick", $this->Button_Delete) || !$this->DeleteRow()) {
                $Redirect = "";
            }
        } else if($this->PressedButton == "Button_Cancel") {
            if(!CCGetEvent($this->Button_Cancel->CCSEvents, "OnClick", $this->Button_Cancel)) {
                $Redirect = "";
            }
        } else if($this->Validate()) {
            if($this->PressedButton == "Button_Insert") {
                if(!CCGetEvent($this->Button_Insert->CCSEvents, "OnClick", $this->Button_Insert) || !$this->InsertRow()) {
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

//InsertRow Method @2-EFF76FE0
    function InsertRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeInsert", $this);
        if(!$this->InsertAllowed) return false;
        $this->DataSource->user_login->SetValue($this->user_login->GetValue(true));
        $this->DataSource->user_password->SetValue($this->user_password->GetValue(true));
        $this->DataSource->user_level->SetValue($this->user_level->GetValue(true));
        $this->DataSource->user_email->SetValue($this->user_email->GetValue(true));
        $this->DataSource->user_first_name->SetValue($this->user_first_name->GetValue(true));
        $this->DataSource->user_last_name->SetValue($this->user_last_name->GetValue(true));
        $this->DataSource->user_is_approved->SetValue($this->user_is_approved->GetValue(true));
        $this->DataSource->user_date_add_h->SetValue($this->user_date_add_h->GetValue(true));
        $this->DataSource->Insert();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterInsert", $this);
        return (!$this->CheckErrors());
    }
//End InsertRow Method

//UpdateRow Method @2-6442BD27
    function UpdateRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeUpdate", $this);
        if(!$this->UpdateAllowed) return false;
        $this->DataSource->user_password->SetValue($this->user_password->GetValue(true));
        $this->DataSource->user_level->SetValue($this->user_level->GetValue(true));
        $this->DataSource->user_email->SetValue($this->user_email->GetValue(true));
        $this->DataSource->user_first_name->SetValue($this->user_first_name->GetValue(true));
        $this->DataSource->user_last_name->SetValue($this->user_last_name->GetValue(true));
        $this->DataSource->user_is_approved->SetValue($this->user_is_approved->GetValue(true));
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

//Show Method @2-BD7D1DD8
    function Show()
    {
        global $Tpl;
        global $FileName;
        global $CCSLocales;
        $Error = "";

        if(!$this->Visible)
            return;

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeSelect", $this);

        $this->user_level->Prepare();

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
                $this->user_login_label->SetValue($this->DataSource->user_login_label->GetValue());
                $this->user_date_add->SetValue($this->DataSource->user_date_add->GetValue());
                $this->user_last_login->SetValue($this->DataSource->user_last_login->GetValue());
                if(!$this->FormSubmitted){
                    $this->user_login->SetValue($this->DataSource->user_login->GetValue());
                    $this->user_password->SetValue($this->DataSource->user_password->GetValue());
                    $this->user_level->SetValue($this->DataSource->user_level->GetValue());
                    $this->user_email->SetValue($this->DataSource->user_email->GetValue());
                    $this->user_first_name->SetValue($this->DataSource->user_first_name->GetValue());
                    $this->user_last_name->SetValue($this->DataSource->user_last_name->GetValue());
                    $this->user_is_approved->SetValue($this->DataSource->user_is_approved->GetValue());
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
            $Error = ComposeStrings($Error, $this->user_login_label->Errors->ToString());
            $Error = ComposeStrings($Error, $this->user_password->Errors->ToString());
            $Error = ComposeStrings($Error, $this->user_level->Errors->ToString());
            $Error = ComposeStrings($Error, $this->user_email->Errors->ToString());
            $Error = ComposeStrings($Error, $this->user_first_name->Errors->ToString());
            $Error = ComposeStrings($Error, $this->user_last_name->Errors->ToString());
            $Error = ComposeStrings($Error, $this->user_is_approved->Errors->ToString());
            $Error = ComposeStrings($Error, $this->user_date_add->Errors->ToString());
            $Error = ComposeStrings($Error, $this->user_date_add_h->Errors->ToString());
            $Error = ComposeStrings($Error, $this->user_last_login->Errors->ToString());
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
        $this->Button_Update->Visible = $this->EditMode && $this->UpdateAllowed;
        $this->Button_Delete->Visible = $this->EditMode && $this->DeleteAllowed;

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShow", $this);
        if(!$this->Visible) {
            $Tpl->block_path = $ParentPath;
            return;
        }

        $this->user_login->Show();
        $this->user_login_label->Show();
        $this->user_password->Show();
        $this->user_level->Show();
        $this->user_email->Show();
        $this->user_first_name->Show();
        $this->user_last_name->Show();
        $this->user_is_approved->Show();
        $this->user_date_add->Show();
        $this->user_date_add_h->Show();
        $this->user_last_login->Show();
        $this->Button_Insert->Show();
        $this->Button_Update->Show();
        $this->Button_Delete->Show();
        $this->Button_Cancel->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->DataSource->close();
    }
//End Show Method

} //End users_maint Class @2-FCB6E20C

class clsusers_maintDataSource extends clsDBcalendar {  //users_maintDataSource Class @2-553A97CE

//DataSource Variables @2-75968463
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
    var $user_login_label;
    var $user_password;
    var $user_level;
    var $user_email;
    var $user_first_name;
    var $user_last_name;
    var $user_is_approved;
    var $user_date_add;
    var $user_date_add_h;
    var $user_last_login;
//End DataSource Variables

//DataSourceClass_Initialize Event @2-3A20CF94
    function clsusers_maintDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "Record users_maint/Error";
        $this->Initialize();
        $this->user_login = new clsField("user_login", ccsText, "");
        $this->user_login_label = new clsField("user_login_label", ccsText, "");
        $this->user_password = new clsField("user_password", ccsText, "");
        $this->user_level = new clsField("user_level", ccsInteger, "");
        $this->user_email = new clsField("user_email", ccsText, "");
        $this->user_first_name = new clsField("user_first_name", ccsText, "");
        $this->user_last_name = new clsField("user_last_name", ccsText, "");
        $this->user_is_approved = new clsField("user_is_approved", ccsInteger, "");
        $this->user_date_add = new clsField("user_date_add", ccsDate, array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"));
        $this->user_date_add_h = new clsField("user_date_add_h", ccsDate, array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"));
        $this->user_last_login = new clsField("user_last_login", ccsDate, array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"));

        $this->InsertFields["user_login"] = array("Name" => "user_login", "Value" => "", "DataType" => ccsText);
        $this->InsertFields["user_password"] = array("Name" => "user_password", "Value" => "", "DataType" => ccsText);
        $this->InsertFields["user_level"] = array("Name" => "user_level", "Value" => "", "DataType" => ccsInteger);
        $this->InsertFields["user_email"] = array("Name" => "user_email", "Value" => "", "DataType" => ccsText);
        $this->InsertFields["user_first_name"] = array("Name" => "user_first_name", "Value" => "", "DataType" => ccsText);
        $this->InsertFields["user_last_name"] = array("Name" => "user_last_name", "Value" => "", "DataType" => ccsText);
        $this->InsertFields["user_is_approved"] = array("Name" => "user_is_approved", "Value" => "", "DataType" => ccsInteger);
        $this->InsertFields["user_date_add"] = array("Name" => "user_date_add", "Value" => "", "DataType" => ccsDate);
        $this->UpdateFields["user_password"] = array("Name" => "user_password", "Value" => "", "DataType" => ccsText);
        $this->UpdateFields["user_level"] = array("Name" => "user_level", "Value" => "", "DataType" => ccsInteger);
        $this->UpdateFields["user_email"] = array("Name" => "user_email", "Value" => "", "DataType" => ccsText);
        $this->UpdateFields["user_first_name"] = array("Name" => "user_first_name", "Value" => "", "DataType" => ccsText);
        $this->UpdateFields["user_last_name"] = array("Name" => "user_last_name", "Value" => "", "DataType" => ccsText);
        $this->UpdateFields["user_is_approved"] = array("Name" => "user_is_approved", "Value" => "", "DataType" => ccsInteger);
    }
//End DataSourceClass_Initialize Event

//Prepare Method @2-B49E291C
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

//Open Method @2-0C94DEC2
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

//SetValues Method @2-25711B4C
    function SetValues()
    {
        $this->user_login->SetDBValue($this->f("user_login"));
        $this->user_login_label->SetDBValue($this->f("user_login"));
        $this->user_password->SetDBValue($this->f("user_password"));
        $this->user_level->SetDBValue(trim($this->f("user_level")));
        $this->user_email->SetDBValue($this->f("user_email"));
        $this->user_first_name->SetDBValue($this->f("user_first_name"));
        $this->user_last_name->SetDBValue($this->f("user_last_name"));
        $this->user_is_approved->SetDBValue(trim($this->f("user_is_approved")));
        $this->user_date_add->SetDBValue(trim($this->f("user_date_add")));
        $this->user_last_login->SetDBValue(trim($this->f("user_last_login")));
    }
//End SetValues Method

//Insert Method @2-5945FB01
    function Insert()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->CmdExecution = true;
        $this->cp["user_login"] = new clsSQLParameter("ctrluser_login", ccsText, "", "", $this->user_login->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["user_password"] = new clsSQLParameter("ctrluser_password", ccsText, "", "", $this->user_password->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["user_level"] = new clsSQLParameter("ctrluser_level", ccsInteger, "", "", $this->user_level->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["user_email"] = new clsSQLParameter("ctrluser_email", ccsText, "", "", $this->user_email->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["user_first_name"] = new clsSQLParameter("ctrluser_first_name", ccsText, "", "", $this->user_first_name->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["user_last_name"] = new clsSQLParameter("ctrluser_last_name", ccsText, "", "", $this->user_last_name->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["user_is_approved"] = new clsSQLParameter("ctrluser_is_approved", ccsInteger, "", "", $this->user_is_approved->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["user_date_add"] = new clsSQLParameter("ctrluser_date_add_h", ccsDate, $DefaultDateFormat, array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"), $this->user_date_add_h->GetValue(true), "", false, $this->ErrorBlock);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildInsert", $this->Parent);
        if (!strlen($this->cp["user_login"]->GetText()) and !is_bool($this->cp["user_login"]->GetValue())) 
            $this->cp["user_login"]->SetValue($this->user_login->GetValue(true));
        if (!strlen($this->cp["user_password"]->GetText()) and !is_bool($this->cp["user_password"]->GetValue())) 
            $this->cp["user_password"]->SetValue($this->user_password->GetValue(true));
        if (!strlen($this->cp["user_level"]->GetText()) and !is_bool($this->cp["user_level"]->GetValue())) 
            $this->cp["user_level"]->SetValue($this->user_level->GetValue(true));
        if (!strlen($this->cp["user_email"]->GetText()) and !is_bool($this->cp["user_email"]->GetValue())) 
            $this->cp["user_email"]->SetValue($this->user_email->GetValue(true));
        if (!strlen($this->cp["user_first_name"]->GetText()) and !is_bool($this->cp["user_first_name"]->GetValue())) 
            $this->cp["user_first_name"]->SetValue($this->user_first_name->GetValue(true));
        if (!strlen($this->cp["user_last_name"]->GetText()) and !is_bool($this->cp["user_last_name"]->GetValue())) 
            $this->cp["user_last_name"]->SetValue($this->user_last_name->GetValue(true));
        if (!strlen($this->cp["user_is_approved"]->GetText()) and !is_bool($this->cp["user_is_approved"]->GetValue())) 
            $this->cp["user_is_approved"]->SetValue($this->user_is_approved->GetValue(true));
        if (!strlen($this->cp["user_date_add"]->GetText()) and !is_bool($this->cp["user_date_add"]->GetValue())) 
            $this->cp["user_date_add"]->SetValue($this->user_date_add_h->GetValue(true));
        $this->InsertFields["user_login"]["Value"] = $this->cp["user_login"]->GetDBValue(true);
        $this->InsertFields["user_password"]["Value"] = $this->cp["user_password"]->GetDBValue(true);
        $this->InsertFields["user_level"]["Value"] = $this->cp["user_level"]->GetDBValue(true);
        $this->InsertFields["user_email"]["Value"] = $this->cp["user_email"]->GetDBValue(true);
        $this->InsertFields["user_first_name"]["Value"] = $this->cp["user_first_name"]->GetDBValue(true);
        $this->InsertFields["user_last_name"]["Value"] = $this->cp["user_last_name"]->GetDBValue(true);
        $this->InsertFields["user_is_approved"]["Value"] = $this->cp["user_is_approved"]->GetDBValue(true);
        $this->InsertFields["user_date_add"]["Value"] = $this->cp["user_date_add"]->GetDBValue(true);
        $this->SQL = CCBuildInsert("users", $this->InsertFields, $this);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteInsert", $this->Parent);
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteInsert", $this->Parent);
        }
    }
//End Insert Method

//Update Method @2-64E89E5D
    function Update()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->CmdExecution = true;
        $this->cp["user_password"] = new clsSQLParameter("ctrluser_password", ccsText, "", "", $this->user_password->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["user_level"] = new clsSQLParameter("ctrluser_level", ccsInteger, "", "", $this->user_level->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["user_email"] = new clsSQLParameter("ctrluser_email", ccsText, "", "", $this->user_email->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["user_first_name"] = new clsSQLParameter("ctrluser_first_name", ccsText, "", "", $this->user_first_name->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["user_last_name"] = new clsSQLParameter("ctrluser_last_name", ccsText, "", "", $this->user_last_name->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["user_is_approved"] = new clsSQLParameter("ctrluser_is_approved", ccsInteger, "", "", $this->user_is_approved->GetValue(true), "", false, $this->ErrorBlock);
        $wp = new clsSQLParameters($this->ErrorBlock);
        $wp->AddParameter("1", "urluser_id", ccsInteger, "", "", CCGetFromGet("user_id", NULL), "", false);
        if(!$wp->AllParamsSet()) {
            $this->Errors->addError($CCSLocales->GetText("CCS_CustomOperationError_MissingParameters"));
        }
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildUpdate", $this->Parent);
        if (!strlen($this->cp["user_password"]->GetText()) and !is_bool($this->cp["user_password"]->GetValue())) 
            $this->cp["user_password"]->SetValue($this->user_password->GetValue(true));
        if (!strlen($this->cp["user_level"]->GetText()) and !is_bool($this->cp["user_level"]->GetValue())) 
            $this->cp["user_level"]->SetValue($this->user_level->GetValue(true));
        if (!strlen($this->cp["user_email"]->GetText()) and !is_bool($this->cp["user_email"]->GetValue())) 
            $this->cp["user_email"]->SetValue($this->user_email->GetValue(true));
        if (!strlen($this->cp["user_first_name"]->GetText()) and !is_bool($this->cp["user_first_name"]->GetValue())) 
            $this->cp["user_first_name"]->SetValue($this->user_first_name->GetValue(true));
        if (!strlen($this->cp["user_last_name"]->GetText()) and !is_bool($this->cp["user_last_name"]->GetValue())) 
            $this->cp["user_last_name"]->SetValue($this->user_last_name->GetValue(true));
        if (!strlen($this->cp["user_is_approved"]->GetText()) and !is_bool($this->cp["user_is_approved"]->GetValue())) 
            $this->cp["user_is_approved"]->SetValue($this->user_is_approved->GetValue(true));
        $wp->Criterion[1] = $wp->Operation(opEqual, "user_id", $wp->GetDBValue("1"), $this->ToSQL($wp->GetDBValue("1"), ccsInteger),false);
        $Where = 
             $wp->Criterion[1];
        $this->UpdateFields["user_password"]["Value"] = $this->cp["user_password"]->GetDBValue(true);
        $this->UpdateFields["user_level"]["Value"] = $this->cp["user_level"]->GetDBValue(true);
        $this->UpdateFields["user_email"]["Value"] = $this->cp["user_email"]->GetDBValue(true);
        $this->UpdateFields["user_first_name"]["Value"] = $this->cp["user_first_name"]->GetDBValue(true);
        $this->UpdateFields["user_last_name"]["Value"] = $this->cp["user_last_name"]->GetDBValue(true);
        $this->UpdateFields["user_is_approved"]["Value"] = $this->cp["user_is_approved"]->GetDBValue(true);
        $this->SQL = CCBuildUpdate("users", $this->UpdateFields, $this);
        $this->SQL = CCBuildSQL($this->SQL, $Where, "");
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteUpdate", $this->Parent);
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteUpdate", $this->Parent);
        }
    }
//End Update Method

//Delete Method @2-6BD040D0
    function Delete()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->CmdExecution = true;
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildDelete", $this->Parent);
        $this->SQL = "DELETE FROM users";
        $this->SQL = CCBuildSQL($this->SQL, $this->Where, "");
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteDelete", $this->Parent);
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteDelete", $this->Parent);
        }
    }
//End Delete Method

} //End users_maintDataSource Class @2-FCB6E20C

//Initialize Page @1-BB9FB022
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
$TemplateFileName = "users_maint.html";
$BlockToParse = "main";
$TemplateEncoding = "UTF-8";
$PathToRoot = "../";
//End Initialize Page

//Authenticate User @1-F87A9DA3
CCSecurityRedirect("100", "../login.php");
//End Authenticate User

//Include events file @1-ECF500A8
include("./users_maint_events.php");
//End Include events file

//Initialize Objects @1-E4EAF0F4
$DBcalendar = new clsDBcalendar();
$MainPage->Connections["calendar"] = & $DBcalendar;

// Controls
$header = & new clsheader("", "header", $MainPage);
$header->Initialize();
$users_maint = & new clsRecordusers_maint("", $MainPage);
$MainPage->header = & $header;
$MainPage->users_maint = & $users_maint;
$users_maint->Initialize();

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

//Execute Components @1-32A819ED
$header->Operations();
$users_maint->Operation();
//End Execute Components

//Go to destination page @1-6BACB9C9
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
    $DBcalendar->close();
    header("Location: " . $Redirect);
    $header->Class_Terminate();
    unset($header);
    unset($users_maint);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-2B4333D8
$header->Show();
$users_maint->Show();
$Tpl->block_path = "";
$Tpl->Parse($BlockToParse, false);
$main_block = $Tpl->GetVar($BlockToParse);
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeOutput", $MainPage);
if ($CCSEventResult) echo $main_block;
//End Show Page

//Unload Page @1-D3EFBB4F
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
$DBcalendar->close();
$header->Class_Terminate();
unset($header);
unset($users_maint);
unset($Tpl);
//End Unload Page


?>
