<?php
//Include Common Files @1-E62D7922
define("RelativePath", "..");
define("PathToCurrentPage", "/admin/");
define("FileName", "categories.php");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
//End Include Common Files

//Include Page implementation @2-47CFCC1A
include_once(RelativePath . "/admin/header.php");
//End Include Page implementation

class clsGridcategories { //categories class @33-3B8F933E

//Variables @33-57E36685

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
    var $Sorter_category_name;
    var $Sorter_category_image;
//End Variables

//Class_Initialize Event @33-9B8E9CCD
    function clsGridcategories($RelativePath, & $Parent)
    {
        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->ComponentName = "categories";
        $this->Visible = True;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Grid categories";
        $this->DataSource = new clscategoriesDataSource($this);
        $this->ds = & $this->DataSource;
        $this->PageSize = CCGetParam($this->ComponentName . "PageSize", "");
        if(!is_numeric($this->PageSize) || !strlen($this->PageSize))
            $this->PageSize = 10;
        else
            $this->PageSize = intval($this->PageSize);
        if ($this->PageSize > 100)
            $this->PageSize = 100;
        if($this->PageSize == 0)
            $this->Errors->addError("<p>Form: Grid " . $this->ComponentName . "<br>Error: (CCS06) Invalid page size.</p>");
        $this->PageNumber = intval(CCGetParam($this->ComponentName . "Page", 1));
        if ($this->PageNumber <= 0) $this->PageNumber = 1;
        $this->SorterName = CCGetParam("categoriesOrder", "");
        $this->SorterDirection = CCGetParam("categoriesDir", "");

        $this->category_name = & new clsControl(ccsLink, "category_name", "category_name", ccsText, "", CCGetRequestParam("category_name", ccsGet, NULL), $this);
        $this->category_name->Page = "categories.php";
        $this->category_image = & new clsControl(ccsImage, "category_image", "category_image", ccsText, "", CCGetRequestParam("category_image", ccsGet, NULL), $this);
        $this->translations = & new clsControl(ccsLink, "translations", "translations", ccsText, "", CCGetRequestParam("translations", ccsGet, NULL), $this);
        $this->translations->Page = "categories_langs.php";
        $this->Sorter_category_name = & new clsSorter($this->ComponentName, "Sorter_category_name", $FileName, $this);
        $this->Sorter_category_image = & new clsSorter($this->ComponentName, "Sorter_category_image", $FileName, $this);
        $this->categories_Insert = & new clsControl(ccsLink, "categories_Insert", "categories_Insert", ccsText, "", CCGetRequestParam("categories_Insert", ccsGet, NULL), $this);
        $this->categories_Insert->Parameters = CCGetQueryString("QueryString", array("category_id", "ccsForm"));
        $this->categories_Insert->Page = "categories.php";
        $this->Navigator = & new clsNavigator($this->ComponentName, "Navigator", $FileName, 10, tpSimple, $this);
    }
//End Class_Initialize Event

//Initialize Method @33-90E704C5
    function Initialize()
    {
        if(!$this->Visible) return;

        $this->DataSource->PageSize = & $this->PageSize;
        $this->DataSource->AbsolutePage = & $this->PageNumber;
        $this->DataSource->SetOrder($this->SorterName, $this->SorterDirection);
    }
//End Initialize Method

//Show Method @33-F4DD3216
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
            $this->ControlsVisible["category_name"] = $this->category_name->Visible;
            $this->ControlsVisible["category_image"] = $this->category_image->Visible;
            $this->ControlsVisible["translations"] = $this->translations->Visible;
            do {
                $this->RowNumber++;
                $this->DataSource->SetValues();
                $Tpl->block_path = $ParentPath . "/" . $GridBlock . "/Row";
                $this->category_name->SetValue($this->DataSource->category_name->GetValue());
                $this->category_name->Parameters = CCGetQueryString("QueryString", array("ccsForm"));
                $this->category_name->Parameters = CCAddParam($this->category_name->Parameters, "category_id", $this->DataSource->f("categories_category_id"));
                $this->category_image->SetValue($this->DataSource->category_image->GetValue());
                $this->translations->Parameters = CCGetQueryString("QueryString", array("ccsForm"));
                $this->translations->Parameters = CCAddParam($this->translations->Parameters, "category_id", $this->DataSource->f("categories_category_id"));
                $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowRow", $this);
                $this->category_name->Show();
                $this->category_image->Show();
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
        $this->Navigator->PageNumber = $this->DataSource->AbsolutePage;
        if ($this->DataSource->RecordsCount == "CCS not counted")
            $this->Navigator->TotalPages = $this->DataSource->AbsolutePage + ($this->DataSource->next_record() ? 1 : 0);
        else
            $this->Navigator->TotalPages = $this->DataSource->PageCount();
        $this->Sorter_category_name->Show();
        $this->Sorter_category_image->Show();
        $this->categories_Insert->Show();
        $this->Navigator->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->DataSource->close();
    }
//End Show Method

//GetErrors Method @33-F7D98CE5
    function GetErrors()
    {
        $errors = "";
        $errors = ComposeStrings($errors, $this->category_name->Errors->ToString());
        $errors = ComposeStrings($errors, $this->category_image->Errors->ToString());
        $errors = ComposeStrings($errors, $this->translations->Errors->ToString());
        $errors = ComposeStrings($errors, $this->Errors->ToString());
        $errors = ComposeStrings($errors, $this->DataSource->Errors->ToString());
        return $errors;
    }
//End GetErrors Method

} //End categories Class @33-FCB6E20C

class clscategoriesDataSource extends clsDBcalendar {  //categoriesDataSource Class @33-E21696DE

//DataSource Variables @33-54695642
    var $Parent = "";
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;
    var $CmdExecution;

    var $CountSQL;
    var $wp;


    // Datasource fields
    var $category_name;
    var $category_image;
//End DataSource Variables

//DataSourceClass_Initialize Event @33-C582B165
    function clscategoriesDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "Grid categories";
        $this->Initialize();
        $this->category_name = new clsField("category_name", ccsText, "");
        $this->category_image = new clsField("category_image", ccsText, "");

    }
//End DataSourceClass_Initialize Event

//SetOrder Method @33-32125AD2
    function SetOrder($SorterName, $SorterDirection)
    {
        $this->Order = "";
        $this->Order = CCGetOrder($this->Order, $SorterName, $SorterDirection, 
            array("Sorter_category_name" => array("categories_langs.category_name", ""), 
            "Sorter_category_image" => array("category_image", "")));
    }
//End SetOrder Method

//Prepare Method @33-1CF6AA37
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "seslocale", ccsText, "", "", $this->Parameters["seslocale"], "", false);
        $this->wp->Criterion[1] = $this->wp->Operation(opEqual, "categories_langs.language_id", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsText),false);
        $this->Where = 
             $this->wp->Criterion[1];
    }
//End Prepare Method

//Open Method @33-9AE1FB13
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->CountSQL = "SELECT COUNT(*)\n\n" .
        "FROM categories_langs INNER JOIN categories ON\n\n" .
        "categories_langs.category_id = categories.category_id";
        $this->SQL = "SELECT category_image, categories_langs.category_name AS categories_langs_category_name, categories.category_id AS categories_category_id,\n\n" .
        "category_lang_id \n\n" .
        "FROM categories_langs INNER JOIN categories ON\n\n" .
        "categories_langs.category_id = categories.category_id {SQL_Where} {SQL_OrderBy}";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect", $this->Parent);
        if ($this->CountSQL) 
            $this->RecordsCount = CCGetDBValue(CCBuildSQL($this->CountSQL, $this->Where, ""), $this);
        else
            $this->RecordsCount = "CCS not counted";
        $this->query($this->OptimizeSQL(CCBuildSQL($this->SQL, $this->Where, $this->Order)));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect", $this->Parent);
    }
//End Open Method

//SetValues Method @33-405BC358
    function SetValues()
    {
        $this->category_name->SetDBValue($this->f("categories_langs_category_name"));
        $this->category_image->SetDBValue($this->f("category_image"));
    }
//End SetValues Method

} //End categoriesDataSource Class @33-FCB6E20C

class clsRecordcategories_maint { //categories_maint Class @45-092A45DF

//Variables @45-0DF9B1C2

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

//Class_Initialize Event @45-CEDCE8D7
    function clsRecordcategories_maint($RelativePath, & $Parent)
    {

        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->Visible = true;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Record categories_maint/Error";
        $this->DataSource = new clscategories_maintDataSource($this);
        $this->ds = & $this->DataSource;
        $this->InsertAllowed = true;
        $this->UpdateAllowed = true;
        $this->DeleteAllowed = true;
        $this->ReadAllowed = true;
        if($this->Visible)
        {
            $this->ComponentName = "categories_maint";
            $CCSForm = split(":", CCGetFromGet("ccsForm", ""), 2);
            if(sizeof($CCSForm) == 1)
                $CCSForm[1] = "";
            list($FormName, $FormMethod) = $CCSForm;
            $this->EditMode = ($FormMethod == "Edit");
            $this->FormEnctype = "multipart/form-data";
            $this->FormSubmitted = ($FormName == $this->ComponentName);
            $Method = $this->FormSubmitted ? ccsPost : ccsGet;
            $this->category_name = & new clsControl(ccsTextBox, "category_name", $CCSLocales->GetText("cal_category_name"), ccsText, "", CCGetRequestParam("category_name", $Method, NULL), $this);
            $this->category_name->Required = true;
            $this->category_image = & new clsFileUpload("category_image", $CCSLocales->GetText("cal_category_image"), "../temp/", "../images/categories/", "*.jpg;*.png;*.gif", "", 100000, $this);
            $this->Button_Insert = & new clsButton("Button_Insert", $Method, $this);
            $this->Button_Update = & new clsButton("Button_Update", $Method, $this);
            $this->Button_Delete = & new clsButton("Button_Delete", $Method, $this);
            $this->Button_Cancel = & new clsButton("Button_Cancel", $Method, $this);
        }
    }
//End Class_Initialize Event

//Initialize Method @45-3D7FCFAE
    function Initialize()
    {

        if(!$this->Visible)
            return;

        $this->DataSource->Parameters["urlcategory_id"] = CCGetFromGet("category_id", NULL);
        $this->DataSource->Parameters["seslocale"] = CCGetSession("locale", NULL);
    }
//End Initialize Method

//Validate Method @45-21A6041C
    function Validate()
    {
        global $CCSLocales;
        $Validation = true;
        $Where = "";
        $Validation = ($this->category_name->Validate() && $Validation);
        $Validation = ($this->category_image->Validate() && $Validation);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnValidate", $this);
        $Validation =  $Validation && ($this->category_name->Errors->Count() == 0);
        $Validation =  $Validation && ($this->category_image->Errors->Count() == 0);
        return (($this->Errors->Count() == 0) && $Validation);
    }
//End Validate Method

//CheckErrors Method @45-615960DB
    function CheckErrors()
    {
        $errors = false;
        $errors = ($errors || $this->category_name->Errors->Count());
        $errors = ($errors || $this->category_image->Errors->Count());
        $errors = ($errors || $this->Errors->Count());
        $errors = ($errors || $this->DataSource->Errors->Count());
        return $errors;
    }
//End CheckErrors Method

//Operation Method @45-C563D123
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

        $this->category_image->Upload();

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
        $Redirect = "categories.php" . "?" . CCGetQueryString("QueryString", array("ccsForm", "category_id"));
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

//InsertRow Method @45-B2AD79BF
    function InsertRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeInsert", $this);
        if(!$this->InsertAllowed) return false;
        $this->DataSource->category_name->SetValue($this->category_name->GetValue(true));
        $this->DataSource->category_image->SetValue($this->category_image->GetValue(true));
        $this->DataSource->Insert();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterInsert", $this);
        if($this->DataSource->Errors->Count() == 0) {
            $this->category_image->Move();
        }
        return (!$this->CheckErrors());
    }
//End InsertRow Method

//UpdateRow Method @45-A4A1F95E
    function UpdateRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeUpdate", $this);
        if(!$this->UpdateAllowed) return false;
        $this->DataSource->category_image->SetValue($this->category_image->GetValue(true));
        $this->DataSource->Update();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterUpdate", $this);
        if($this->DataSource->Errors->Count() == 0) {
            $this->category_image->Move();
        }
        return (!$this->CheckErrors());
    }
//End UpdateRow Method

//DeleteRow Method @45-3F33A7BB
    function DeleteRow()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeDelete", $this);
        if(!$this->DeleteAllowed) return false;
        $this->DataSource->Delete();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterDelete", $this);
        if($this->DataSource->Errors->Count() == 0) {
            $this->category_image->Delete();
        }
        return (!$this->CheckErrors());
    }
//End DeleteRow Method

//Show Method @45-BF5744BD
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
                    $this->category_name->SetValue($this->DataSource->category_name->GetValue());
                    $this->category_image->SetValue($this->DataSource->category_image->GetValue());
                }
            } else {
                $this->EditMode = false;
            }
        }

        if($this->FormSubmitted || $this->CheckErrors()) {
            $Error = "";
            $Error = ComposeStrings($Error, $this->category_name->Errors->ToString());
            $Error = ComposeStrings($Error, $this->category_image->Errors->ToString());
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

        $this->category_name->Show();
        $this->category_image->Show();
        $this->Button_Insert->Show();
        $this->Button_Update->Show();
        $this->Button_Delete->Show();
        $this->Button_Cancel->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->DataSource->close();
    }
//End Show Method

} //End categories_maint Class @45-FCB6E20C

class clscategories_maintDataSource extends clsDBcalendar {  //categories_maintDataSource Class @45-E485BCA0

//DataSource Variables @45-D519FD8E
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
    var $category_name;
    var $category_image;
//End DataSource Variables

//DataSourceClass_Initialize Event @45-C7B4ADBC
    function clscategories_maintDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "Record categories_maint/Error";
        $this->Initialize();
        $this->category_name = new clsField("category_name", ccsText, "");
        $this->category_image = new clsField("category_image", ccsText, "");

        $this->InsertFields["category_name"] = array("Name" => "category_name", "Value" => "", "DataType" => ccsText);
        $this->InsertFields["category_image"] = array("Name" => "category_image", "Value" => "", "DataType" => ccsText);
        $this->UpdateFields["category_image"] = array("Name" => "category_image", "Value" => "", "DataType" => ccsText);
    }
//End DataSourceClass_Initialize Event

//Prepare Method @45-806ADFCD
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "urlcategory_id", ccsInteger, "", "", $this->Parameters["urlcategory_id"], "", false);
        $this->wp->AddParameter("2", "seslocale", ccsText, "", "", $this->Parameters["seslocale"], "", false);
        $this->AllParametersSet = $this->wp->AllParamsSet();
        $this->wp->Criterion[1] = $this->wp->Operation(opEqual, "categories.category_id", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsInteger),false);
        $this->wp->Criterion[2] = $this->wp->Operation(opEqual, "categories_langs.language_id", $this->wp->GetDBValue("2"), $this->ToSQL($this->wp->GetDBValue("2"), ccsText),false);
        $this->Where = $this->wp->opAND(
             false, 
             $this->wp->Criterion[1], 
             $this->wp->Criterion[2]);
    }
//End Prepare Method

//Open Method @45-56729725
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->SQL = "SELECT category_image, categories_langs.category_name AS categories_langs_category_name \n\n" .
        "FROM categories_langs INNER JOIN categories ON\n\n" .
        "categories_langs.category_id = categories.category_id {SQL_Where} {SQL_OrderBy}";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect", $this->Parent);
        $this->PageSize = 1;
        $this->query($this->OptimizeSQL(CCBuildSQL($this->SQL, $this->Where, $this->Order)));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect", $this->Parent);
    }
//End Open Method

//SetValues Method @45-405BC358
    function SetValues()
    {
        $this->category_name->SetDBValue($this->f("categories_langs_category_name"));
        $this->category_image->SetDBValue($this->f("category_image"));
    }
//End SetValues Method

//Insert Method @45-325A2F2C
    function Insert()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->CmdExecution = true;
        $this->cp["category_name"] = new clsSQLParameter("ctrlcategory_name", ccsText, "", "", $this->category_name->GetValue(true), "", false, $this->ErrorBlock);
        $this->cp["category_image"] = new clsSQLParameter("ctrlcategory_image", ccsText, "", "", $this->category_image->GetValue(true), "", false, $this->ErrorBlock);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildInsert", $this->Parent);
        if (!strlen($this->cp["category_name"]->GetText()) and !is_bool($this->cp["category_name"]->GetValue())) 
            $this->cp["category_name"]->SetValue($this->category_name->GetValue(true));
        if (!strlen($this->cp["category_image"]->GetText()) and !is_bool($this->cp["category_image"]->GetValue())) 
            $this->cp["category_image"]->SetValue($this->category_image->GetValue(true));
        $this->InsertFields["category_name"]["Value"] = $this->cp["category_name"]->GetDBValue(true);
        $this->InsertFields["category_image"]["Value"] = $this->cp["category_image"]->GetDBValue(true);
        $this->SQL = CCBuildInsert("categories", $this->InsertFields, $this);
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteInsert", $this->Parent);
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteInsert", $this->Parent);
        }
    }
//End Insert Method

//Update Method @45-1962B97D
    function Update()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->CmdExecution = true;
        $this->cp["category_image"] = new clsSQLParameter("ctrlcategory_image", ccsText, "", "", $this->category_image->GetValue(true), "", false, $this->ErrorBlock);
        $wp = new clsSQLParameters($this->ErrorBlock);
        $wp->AddParameter("1", "urlcategory_id", ccsInteger, "", "", CCGetFromGet("category_id", NULL), "", false);
        if(!$wp->AllParamsSet()) {
            $this->Errors->addError($CCSLocales->GetText("CCS_CustomOperationError_MissingParameters"));
        }
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildUpdate", $this->Parent);
        if (!strlen($this->cp["category_image"]->GetText()) and !is_bool($this->cp["category_image"]->GetValue())) 
            $this->cp["category_image"]->SetValue($this->category_image->GetValue(true));
        $wp->Criterion[1] = $wp->Operation(opEqual, "categories.category_id", $wp->GetDBValue("1"), $this->ToSQL($wp->GetDBValue("1"), ccsInteger),false);
        $Where = 
             $wp->Criterion[1];
        $this->UpdateFields["category_image"]["Value"] = $this->cp["category_image"]->GetDBValue(true);
        $this->SQL = CCBuildUpdate("categories", $this->UpdateFields, $this);
        $this->SQL = CCBuildSQL($this->SQL, $Where, "");
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteUpdate", $this->Parent);
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteUpdate", $this->Parent);
        }
    }
//End Update Method

//Delete Method @45-2B085BCD
    function Delete()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->CmdExecution = true;
        $wp = new clsSQLParameters($this->ErrorBlock);
        $wp->AddParameter("1", "urlcategory_id", ccsInteger, "", "", CCGetFromGet("category_id", NULL), "", false);
        if(!$wp->AllParamsSet()) {
            $this->Errors->addError($CCSLocales->GetText("CCS_CustomOperationError_MissingParameters"));
        }
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildDelete", $this->Parent);
        $wp->Criterion[1] = $wp->Operation(opEqual, "categories.category_id", $wp->GetDBValue("1"), $this->ToSQL($wp->GetDBValue("1"), ccsInteger),false);
        $Where = 
             $wp->Criterion[1];
        $this->SQL = "DELETE FROM categories";
        $this->SQL = CCBuildSQL($this->SQL, $Where, "");
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteDelete", $this->Parent);
        if($this->Errors->Count() == 0 && $this->CmdExecution) {
            $this->query($this->SQL);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteDelete", $this->Parent);
        }
    }
//End Delete Method

} //End categories_maintDataSource Class @45-FCB6E20C

//Initialize Page @1-C58E3B80
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
$TemplateFileName = "categories.html";
$BlockToParse = "main";
$TemplateEncoding = "UTF-8";
$PathToRoot = "../";
//End Initialize Page

//Authenticate User @1-132EF5B6
CCSecurityRedirect("100", "");
//End Authenticate User

//Include events file @1-6A74C743
include("./categories_events.php");
//End Include events file

//Initialize Objects @1-939478D1
$DBcalendar = new clsDBcalendar();
$MainPage->Connections["calendar"] = & $DBcalendar;

// Controls
$header = & new clsheader("", "header", $MainPage);
$header->Initialize();
$categories = & new clsGridcategories("", $MainPage);
$categories_maint = & new clsRecordcategories_maint("", $MainPage);
$MainPage->header = & $header;
$MainPage->categories = & $categories;
$MainPage->categories_maint = & $categories_maint;
$categories->Initialize();
$categories_maint->Initialize();

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

//Execute Components @1-ED700D4D
$header->Operations();
$categories_maint->Operation();
//End Execute Components

//Go to destination page @1-AE0B7731
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
    $DBcalendar->close();
    header("Location: " . $Redirect);
    $header->Class_Terminate();
    unset($header);
    unset($categories);
    unset($categories_maint);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-8B97DCDE
$header->Show();
$categories->Show();
$categories_maint->Show();
$Tpl->block_path = "";
$Tpl->Parse($BlockToParse, false);
$main_block = $Tpl->GetVar($BlockToParse);
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeOutput", $MainPage);
if ($CCSEventResult) echo $main_block;
//End Show Page

//Unload Page @1-D5A6E15C
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
$DBcalendar->close();
$header->Class_Terminate();
unset($header);
unset($categories);
unset($categories_maint);
unset($Tpl);
//End Unload Page


?>
