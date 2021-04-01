<?php
//Include Common Files @1-23C1BC4C
define("RelativePath", "..");
define("PathToCurrentPage", "/admin/");
define("FileName", "index.php");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
//End Include Common Files

//Include Page implementation @2-47CFCC1A
include_once(RelativePath . "/admin/header.php");
//End Include Page implementation

class clsGridusers { //users class @4-0CB76799

//Variables @4-9B81A17B

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
    var $Sorter_user_email;
    var $Sorter_user_date_add;
//End Variables

//Class_Initialize Event @4-4A55CADF
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
        $this->user_login->Page = "users_activate.php";
        $this->user_first_name = & new clsControl(ccsLabel, "user_first_name", "user_first_name", ccsText, "", CCGetRequestParam("user_first_name", ccsGet, NULL), $this);
        $this->user_last_name = & new clsControl(ccsLabel, "user_last_name", "user_last_name", ccsText, "", CCGetRequestParam("user_last_name", ccsGet, NULL), $this);
        $this->user_email = & new clsControl(ccsLabel, "user_email", "user_email", ccsText, "", CCGetRequestParam("user_email", ccsGet, NULL), $this);
        $this->user_date_add = & new clsControl(ccsLabel, "user_date_add", "user_date_add", ccsDate, $DefaultDateFormat, CCGetRequestParam("user_date_add", ccsGet, NULL), $this);
        $this->users_TotalRecords = & new clsControl(ccsLabel, "users_TotalRecords", "users_TotalRecords", ccsText, "", CCGetRequestParam("users_TotalRecords", ccsGet, NULL), $this);
        $this->Sorter_user_id = & new clsSorter($this->ComponentName, "Sorter_user_id", $FileName, $this);
        $this->Sorter_user_login = & new clsSorter($this->ComponentName, "Sorter_user_login", $FileName, $this);
        $this->Sorter_user_first_name = & new clsSorter($this->ComponentName, "Sorter_user_first_name", $FileName, $this);
        $this->Sorter_user_last_name = & new clsSorter($this->ComponentName, "Sorter_user_last_name", $FileName, $this);
        $this->Sorter_user_email = & new clsSorter($this->ComponentName, "Sorter_user_email", $FileName, $this);
        $this->Sorter_user_date_add = & new clsSorter($this->ComponentName, "Sorter_user_date_add", $FileName, $this);
        $this->Navigator = & new clsNavigator($this->ComponentName, "Navigator", $FileName, 10, tpCentered, $this);
    }
//End Class_Initialize Event

//Initialize Method @4-90E704C5
    function Initialize()
    {
        if(!$this->Visible) return;

        $this->DataSource->PageSize = & $this->PageSize;
        $this->DataSource->AbsolutePage = & $this->PageNumber;
        $this->DataSource->SetOrder($this->SorterName, $this->SorterDirection);
    }
//End Initialize Method

//Show Method @4-1E17CD7C
    function Show()
    {
        global $Tpl;
        global $CCSLocales;
        if(!$this->Visible) return;

        $this->RowNumber = 0;

        $this->DataSource->Parameters["expr61"] = 1;

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
            $this->ControlsVisible["user_email"] = $this->user_email->Visible;
            $this->ControlsVisible["user_date_add"] = $this->user_date_add->Visible;
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
                $this->user_email->SetValue($this->DataSource->user_email->GetValue());
                $this->user_date_add->SetValue($this->DataSource->user_date_add->GetValue());
                $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowRow", $this);
                $this->user_id->Show();
                $this->user_login->Show();
                $this->user_first_name->Show();
                $this->user_last_name->Show();
                $this->user_email->Show();
                $this->user_date_add->Show();
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
        $this->Sorter_user_email->Show();
        $this->Sorter_user_date_add->Show();
        $this->Navigator->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->DataSource->close();
    }
//End Show Method

//GetErrors Method @4-A08BA84D
    function GetErrors()
    {
        $errors = "";
        $errors = ComposeStrings($errors, $this->user_id->Errors->ToString());
        $errors = ComposeStrings($errors, $this->user_login->Errors->ToString());
        $errors = ComposeStrings($errors, $this->user_first_name->Errors->ToString());
        $errors = ComposeStrings($errors, $this->user_last_name->Errors->ToString());
        $errors = ComposeStrings($errors, $this->user_email->Errors->ToString());
        $errors = ComposeStrings($errors, $this->user_date_add->Errors->ToString());
        $errors = ComposeStrings($errors, $this->Errors->ToString());
        $errors = ComposeStrings($errors, $this->DataSource->Errors->ToString());
        return $errors;
    }
//End GetErrors Method

} //End users Class @4-FCB6E20C

class clsusersDataSource extends clsDBcalendar {  //usersDataSource Class @4-1B89833B

//DataSource Variables @4-C3BC9EE8
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
    var $user_email;
    var $user_date_add;
//End DataSource Variables

//DataSourceClass_Initialize Event @4-4C8AB532
    function clsusersDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "Grid users";
        $this->Initialize();
        $this->user_id = new clsField("user_id", ccsInteger, "");
        $this->user_login = new clsField("user_login", ccsText, "");
        $this->user_first_name = new clsField("user_first_name", ccsText, "");
        $this->user_last_name = new clsField("user_last_name", ccsText, "");
        $this->user_email = new clsField("user_email", ccsText, "");
        $this->user_date_add = new clsField("user_date_add", ccsDate, array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"));

    }
//End DataSourceClass_Initialize Event

//SetOrder Method @4-B03AA932
    function SetOrder($SorterName, $SorterDirection)
    {
        $this->Order = "user_id";
        $this->Order = CCGetOrder($this->Order, $SorterName, $SorterDirection, 
            array("Sorter_user_id" => array("user_id", ""), 
            "Sorter_user_login" => array("user_login", ""), 
            "Sorter_user_first_name" => array("user_first_name", ""), 
            "Sorter_user_last_name" => array("user_last_name", ""), 
            "Sorter_user_email" => array("user_email", ""), 
            "Sorter_user_date_add" => array("user_date_add", "")));
    }
//End SetOrder Method

//Prepare Method @4-15DD12DC
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "expr61", ccsInteger, "", "", $this->Parameters["expr61"], "", false);
        $this->wp->Criterion[1] = $this->wp->Operation(opEqual, "user_level", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsInteger),false);
        $this->Where = 
             $this->wp->Criterion[1];
    }
//End Prepare Method

//Open Method @4-414C2D04
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->CountSQL = "SELECT COUNT(*)\n\n" .
        "FROM users";
        $this->SQL = "SELECT user_id, user_login, user_email, user_first_name, user_last_name, user_date_add \n\n" .
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

//SetValues Method @4-6BFFCCB3
    function SetValues()
    {
        $this->user_id->SetDBValue(trim($this->f("user_id")));
        $this->user_login->SetDBValue($this->f("user_login"));
        $this->user_first_name->SetDBValue($this->f("user_first_name"));
        $this->user_last_name->SetDBValue($this->f("user_last_name"));
        $this->user_email->SetDBValue($this->f("user_email"));
        $this->user_date_add->SetDBValue(trim($this->f("user_date_add")));
    }
//End SetValues Method

} //End usersDataSource Class @4-FCB6E20C

//Initialize Page @1-452535A1
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
$TemplateFileName = "index.html";
$BlockToParse = "main";
$TemplateEncoding = "UTF-8";
$PathToRoot = "../";
//End Initialize Page

//Authenticate User @1-132EF5B6
CCSecurityRedirect("100", "");
//End Authenticate User

//Include events file @1-7D9DFCA7
include("./index_events.php");
//End Include events file

//Initialize Objects @1-91773414
$DBcalendar = new clsDBcalendar();
$MainPage->Connections["calendar"] = & $DBcalendar;

// Controls
$header = & new clsheader("", "header", $MainPage);
$header->Initialize();
$users = & new clsGridusers("", $MainPage);
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

//Execute Components @1-D0D9375A
$header->Operations();
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
