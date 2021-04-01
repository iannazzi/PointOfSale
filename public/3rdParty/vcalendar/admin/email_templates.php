<?php
//Include Common Files @1-EEDE9B86
define("RelativePath", "..");
define("PathToCurrentPage", "/admin/");
define("FileName", "email_templates.php");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
//End Include Common Files

//Include Page implementation @68-47CFCC1A
include_once(RelativePath . "/admin/header.php");
//End Include Page implementation

class clsGridemail_templates { //email_templates class @2-4C089AF9

//Variables @2-4F066FAA

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
    var $Sorter_email_template_id;
    var $Sorter_email_template_subject;
    var $Sorter_email_template_type;
    var $Sorter_email_template_desc;
    var $Sorter_email_template_from;
//End Variables

//Class_Initialize Event @2-8B4F42BB
    function clsGridemail_templates($RelativePath, & $Parent)
    {
        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->ComponentName = "email_templates";
        $this->Visible = True;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Grid email_templates";
        $this->DataSource = new clsemail_templatesDataSource($this);
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
        $this->SorterName = CCGetParam("email_templatesOrder", "");
        $this->SorterDirection = CCGetParam("email_templatesDir", "");

        $this->email_template_id = & new clsControl(ccsLabel, "email_template_id", "email_template_id", ccsInteger, "", CCGetRequestParam("email_template_id", ccsGet, NULL), $this);
        $this->email_template_subject = & new clsControl(ccsLink, "email_template_subject", "email_template_subject", ccsText, "", CCGetRequestParam("email_template_subject", ccsGet, NULL), $this);
        $this->email_template_subject->Page = "email_templates_maint.php";
        $this->email_template_type = & new clsControl(ccsLabel, "email_template_type", "email_template_type", ccsText, "", CCGetRequestParam("email_template_type", ccsGet, NULL), $this);
        $this->email_template_desc = & new clsControl(ccsLabel, "email_template_desc", "email_template_desc", ccsText, "", CCGetRequestParam("email_template_desc", ccsGet, NULL), $this);
        $this->email_template_desc->HTML = true;
        $this->email_template_from = & new clsControl(ccsLabel, "email_template_from", "email_template_from", ccsText, "", CCGetRequestParam("email_template_from", ccsGet, NULL), $this);
        $this->translations = & new clsControl(ccsLink, "translations", "translations", ccsText, "", CCGetRequestParam("translations", ccsGet, NULL), $this);
        $this->translations->Page = "email_templates_lang.php";
        $this->Sorter_email_template_id = & new clsSorter($this->ComponentName, "Sorter_email_template_id", $FileName, $this);
        $this->Sorter_email_template_subject = & new clsSorter($this->ComponentName, "Sorter_email_template_subject", $FileName, $this);
        $this->Sorter_email_template_type = & new clsSorter($this->ComponentName, "Sorter_email_template_type", $FileName, $this);
        $this->Sorter_email_template_desc = & new clsSorter($this->ComponentName, "Sorter_email_template_desc", $FileName, $this);
        $this->Sorter_email_template_from = & new clsSorter($this->ComponentName, "Sorter_email_template_from", $FileName, $this);
        $this->Navigator = & new clsNavigator($this->ComponentName, "Navigator", $FileName, 10, tpSimple, $this);
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

//Show Method @2-D217F537
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
            $this->ControlsVisible["email_template_id"] = $this->email_template_id->Visible;
            $this->ControlsVisible["email_template_subject"] = $this->email_template_subject->Visible;
            $this->ControlsVisible["email_template_type"] = $this->email_template_type->Visible;
            $this->ControlsVisible["email_template_desc"] = $this->email_template_desc->Visible;
            $this->ControlsVisible["email_template_from"] = $this->email_template_from->Visible;
            $this->ControlsVisible["translations"] = $this->translations->Visible;
            do {
                $this->RowNumber++;
                $this->DataSource->SetValues();
                $Tpl->block_path = $ParentPath . "/" . $GridBlock . "/Row";
                $this->email_template_id->SetValue($this->DataSource->email_template_id->GetValue());
                $this->email_template_subject->SetValue($this->DataSource->email_template_subject->GetValue());
                $this->email_template_subject->Parameters = CCGetQueryString("QueryString", array("ccsForm"));
                $this->email_template_subject->Parameters = CCAddParam($this->email_template_subject->Parameters, "email_template_id", $this->DataSource->f("email_templates_email_template_id"));
                $this->email_template_type->SetValue($this->DataSource->email_template_type->GetValue());
                $this->email_template_desc->SetValue($this->DataSource->email_template_desc->GetValue());
                $this->email_template_from->SetValue($this->DataSource->email_template_from->GetValue());
                $this->translations->Parameters = CCGetQueryString("QueryString", array("ccsForm"));
                $this->translations->Parameters = CCAddParam($this->translations->Parameters, "email_template_id", $this->DataSource->f("email_templates_email_template_id"));
                $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowRow", $this);
                $this->email_template_id->Show();
                $this->email_template_subject->Show();
                $this->email_template_type->Show();
                $this->email_template_desc->Show();
                $this->email_template_from->Show();
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
        $this->Sorter_email_template_id->Show();
        $this->Sorter_email_template_subject->Show();
        $this->Sorter_email_template_type->Show();
        $this->Sorter_email_template_desc->Show();
        $this->Sorter_email_template_from->Show();
        $this->Navigator->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->DataSource->close();
    }
//End Show Method

//GetErrors Method @2-5713005B
    function GetErrors()
    {
        $errors = "";
        $errors = ComposeStrings($errors, $this->email_template_id->Errors->ToString());
        $errors = ComposeStrings($errors, $this->email_template_subject->Errors->ToString());
        $errors = ComposeStrings($errors, $this->email_template_type->Errors->ToString());
        $errors = ComposeStrings($errors, $this->email_template_desc->Errors->ToString());
        $errors = ComposeStrings($errors, $this->email_template_from->Errors->ToString());
        $errors = ComposeStrings($errors, $this->translations->Errors->ToString());
        $errors = ComposeStrings($errors, $this->Errors->ToString());
        $errors = ComposeStrings($errors, $this->DataSource->Errors->ToString());
        return $errors;
    }
//End GetErrors Method

} //End email_templates Class @2-FCB6E20C

class clsemail_templatesDataSource extends clsDBcalendar {  //email_templatesDataSource Class @2-1BF23281

//DataSource Variables @2-70297FAC
    var $Parent = "";
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;
    var $CmdExecution;

    var $CountSQL;
    var $wp;


    // Datasource fields
    var $email_template_id;
    var $email_template_subject;
    var $email_template_type;
    var $email_template_desc;
    var $email_template_from;
//End DataSource Variables

//DataSourceClass_Initialize Event @2-3960B405
    function clsemail_templatesDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "Grid email_templates";
        $this->Initialize();
        $this->email_template_id = new clsField("email_template_id", ccsInteger, "");
        $this->email_template_subject = new clsField("email_template_subject", ccsText, "");
        $this->email_template_type = new clsField("email_template_type", ccsText, "");
        $this->email_template_desc = new clsField("email_template_desc", ccsText, "");
        $this->email_template_from = new clsField("email_template_from", ccsText, "");

    }
//End DataSourceClass_Initialize Event

//SetOrder Method @2-B9DE898D
    function SetOrder($SorterName, $SorterDirection)
    {
        $this->Order = "";
        $this->Order = CCGetOrder($this->Order, $SorterName, $SorterDirection, 
            array("Sorter_email_template_id" => array("email_template_id", ""), 
            "Sorter_email_template_subject" => array("email_templates_lang.email_template_subject", ""), 
            "Sorter_email_template_type" => array("email_template_type", ""), 
            "Sorter_email_template_desc" => array("email_templates_lang.email_template_desc", ""), 
            "Sorter_email_template_from" => array("email_template_from", "")));
    }
//End SetOrder Method

//Prepare Method @2-2F06E7A8
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "seslocale", ccsText, "", "", $this->Parameters["seslocale"], "", false);
        $this->wp->Criterion[1] = $this->wp->Operation(opEqual, "email_templates_lang.language_id", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsText),false);
        $this->Where = 
             $this->wp->Criterion[1];
    }
//End Prepare Method

//Open Method @2-5EF3B79A
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->CountSQL = "SELECT COUNT(*)\n\n" .
        "FROM email_templates_lang INNER JOIN email_templates ON\n\n" .
        "email_templates_lang.email_template_id = email_templates.email_template_id";
        $this->SQL = "SELECT email_templates.email_template_id AS email_templates_email_template_id, email_template_type, email_template_from, email_templates_lang.email_template_desc AS email_templates_lang_email_template_desc,\n\n" .
        "email_templates_lang.email_template_subject AS email_templates_lang_email_template_subject \n\n" .
        "FROM email_templates_lang INNER JOIN email_templates ON\n\n" .
        "email_templates_lang.email_template_id = email_templates.email_template_id {SQL_Where} {SQL_OrderBy}";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect", $this->Parent);
        if ($this->CountSQL) 
            $this->RecordsCount = CCGetDBValue(CCBuildSQL($this->CountSQL, $this->Where, ""), $this);
        else
            $this->RecordsCount = "CCS not counted";
        $this->query($this->OptimizeSQL(CCBuildSQL($this->SQL, $this->Where, $this->Order)));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect", $this->Parent);
    }
//End Open Method

//SetValues Method @2-7F3F31CF
    function SetValues()
    {
        $this->email_template_id->SetDBValue(trim($this->f("email_templates_email_template_id")));
        $this->email_template_subject->SetDBValue($this->f("email_templates_lang_email_template_subject"));
        $this->email_template_type->SetDBValue($this->f("email_template_type"));
        $this->email_template_desc->SetDBValue($this->f("email_templates_lang_email_template_desc"));
        $this->email_template_from->SetDBValue($this->f("email_template_from"));
    }
//End SetValues Method

} //End email_templatesDataSource Class @2-FCB6E20C

//Initialize Page @1-A7C06953
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
$TemplateFileName = "email_templates.html";
$BlockToParse = "main";
$TemplateEncoding = "UTF-8";
$PathToRoot = "../";
//End Initialize Page

//Authenticate User @1-132EF5B6
CCSecurityRedirect("100", "");
//End Authenticate User

//Initialize Objects @1-3B94E2E6
$DBcalendar = new clsDBcalendar();
$MainPage->Connections["calendar"] = & $DBcalendar;

// Controls
$header = & new clsheader("", "header", $MainPage);
$header->Initialize();
$email_templates = & new clsGridemail_templates("", $MainPage);
$MainPage->header = & $header;
$MainPage->email_templates = & $email_templates;
$email_templates->Initialize();

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

//Execute Components @1-D0D9375A
$header->Operations();
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
