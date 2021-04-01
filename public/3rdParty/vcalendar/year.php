<?php
//Include Common Files @1-ECEC7242
define("RelativePath", ".");
define("PathToCurrentPage", "/");
define("FileName", "year.php");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
include_once(RelativePath . "/CalendarNavigator.php");
//End Include Common Files

//Include Page implementation @2-8EACA429
include_once(RelativePath . "/header.php");
//End Include Page implementation

//Include Page implementation @4-A5E85701
include_once(RelativePath . "/infopanel.php");
//End Include Page implementation

//year_events clsEvent @5-2FDBF0F3
class clsEventyear_events {
    var $_Time;
    var $CategoryImage;
    var $EventTime;
    var $EventTimeEnd;
    var $EventDescription;

}
//End year_events clsEvent

class clsCalendaryear_events { //year_events Class @5-7E11B184

//year_events Variables @5-784FF09C

    var $ComponentType = "Calendar";
    var $ComponentName;
    var $Visible;
    var $Errors;
    var $DataSource;
    var $ds;
    var $Type;
    //Calendar variables
    var $CurrentDate;
    var $CurrentProcessingDate;
    var $NextProcessingDate;
    var $PrevProcessingDate;
    var $CalendarStyles = array();
    var $CurrentStyle;
    var $FirstWeekDay;
    var $Now;
    var $IsCurrentMonth;
    var $MonthsInRow;
    var $CCSEvents = array();
    var $CCSEventResult;
    var $Parent;
//End year_events Variables

//year_events Class_Initialize Event @5-7FA39C0D
    function clsCalendaryear_events($RelativePath, & $Parent) {
        global $CCSLocales;
        global $DefaultDateFormat;
        global $FileName;
        global $Redirect;
        $this->ComponentName = "year_events";
        $this->Type = "12";
        $this->Visible = True;
        $this->RelativePath = $RelativePath;
        $this->Parent = & $Parent;
        $this->Errors = new clsErrors();
        $CCSForm = CCGetFromGet("ccsForm", "");
        if ($CCSForm == $this->ComponentName) {
            $Redirect = FileName . "?" .  CCGetQueryString("All", array("ccsForm"));
            return;
        }
        $this->DataSource = new clsyear_eventsDataSource($this);
        $this->ds = & $this->DataSource;
        $this->FirstWeekDay = $CCSLocales->GetFormatInfo("FirstWeekDay");
        $this->MonthsInRow = 4;


        $this->CurYearLabel = & new clsControl(ccsLabel, "CurYearLabel", "CurYearLabel", ccsText, "", CCGetRequestParam("CurYearLabel", ccsGet, NULL), $this);
        $this->MonthDate = & new clsControl(ccsLink, "MonthDate", "MonthDate", ccsDate, array("mmmm"), CCGetRequestParam("MonthDate", ccsGet, NULL), $this);
        $this->MonthDate->Page = "index.php";
        $this->DayOfWeek = & new clsControl(ccsLabel, "DayOfWeek", "DayOfWeek", ccsDate, array("ddd"), CCGetRequestParam("DayOfWeek", ccsGet, NULL), $this);
        $this->GoWeekHeader = & new clsPanel("GoWeekHeader", $this);
        $this->DayNumber = & new clsControl(ccsLink, "DayNumber", "DayNumber", ccsDate, array("d"), CCGetRequestParam("DayNumber", ccsGet, NULL), $this);
        $this->DayNumber->Page = "day.php";
        $this->div_begin = & new clsControl(ccsLabel, "div_begin", "div_begin", ccsText, "", CCGetRequestParam("div_begin", ccsGet, NULL), $this);
        $this->div_begin->HTML = true;
        $this->CategoryImage = & new clsControl(ccsImage, "CategoryImage", "CategoryImage", ccsText, "", CCGetRequestParam("CategoryImage", ccsGet, NULL), $this);
        $this->EventTime = & new clsControl(ccsLabel, "EventTime", "EventTime", ccsDate, array("ShortTime"), CCGetRequestParam("EventTime", ccsGet, NULL), $this);
        $this->EventTimeEnd = & new clsControl(ccsLabel, "EventTimeEnd", "EventTimeEnd", ccsDate, array("ShortTime"), CCGetRequestParam("EventTimeEnd", ccsGet, NULL), $this);
        $this->EventDescription = & new clsControl(ccsLabel, "EventDescription", "EventDescription", ccsText, "", CCGetRequestParam("EventDescription", ccsGet, NULL), $this);
        $this->div_end = & new clsControl(ccsLabel, "div_end", "div_end", ccsText, "", CCGetRequestParam("div_end", ccsGet, NULL), $this);
        $this->div_end->HTML = true;
        $this->GoWeek = & new clsControl(ccsLink, "GoWeek", "GoWeek", ccsText, "", CCGetRequestParam("GoWeek", ccsGet, NULL), $this);
        $this->GoWeek->Page = "week.php";
        $this->Navigator = & new clsCalendarNavigator($this->ComponentName, "Navigator", $this->Type, 10, $this);
        $Now = CCGetDateArray();
        $this->SetNow($Now);
        $this->CalendarStyles["WeekdayName"] = "class=\"CalendarWeekdayName\"";
        $this->CalendarStyles["WeekendName"] = "class=\"CalendarWeekendName\"";
        $this->CalendarStyles["Day"] = "class=\"CalendarDay\"";
        $this->CalendarStyles["Weekend"] = "class=\"CalendarWeekend\"";
        $this->CalendarStyles["Today"] = "class=\"CalendarToday\"";
        $this->CalendarStyles["WeekendToday"] = "class=\"CalendarWeekendToday\"";
        $this->CalendarStyles["OtherMonthDay"] = "class=\"CalendarOtherMonthDay\"";
        $this->CalendarStyles["OtherMonthToday"] = "class=\"CalendarOtherMonthToday\"";
        $this->CalendarStyles["OtherMonthWeekend"] = "class=\"CalendarOtherMonthWeekend\"";
        $this->CalendarStyles["OtherMonthWeekendToday"] = "class=\"CalendarOtherMonthWeekendToday\"";
    }
//End year_events Class_Initialize Event

//Initialize Method @5-A4F1CC7C
    function Initialize()
    {
        if(!$this->Visible) return;
        $this->DataSource->SetOrder("", "");
    }
//End Initialize Method

//Show Method @5-016DFF5B
    function Show () {
        global $Tpl;
        global $CCSLocales;
        global $DefaultDateFormat;
        if(!$this->Visible) return;

        $this->DataSource->Parameters["sescategory"] = CCGetSession("category", NULL);

        $FirstProcessingDate = CCParseDate(CCFormatDate($this->CurrentDate, array("yyyy", "-01-01 00:00:00")), array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"));
        $Days = (CCFormatDate($FirstProcessingDate, array("w")) - $this->FirstWeekDay + 6) % 7;
        $FirstShowedDate = CCDateAdd($FirstProcessingDate, "-" . $Days . "day");
        $LastProcessingDate = CCDateAdd($FirstProcessingDate, "1year -1second");
        $Days = ($this->FirstWeekDay - CCFormatDate($LastProcessingDate, array("w")) + 7) % 7;
        $LastShowedDate = CCDateAdd($LastProcessingDate, $Days . "day");
        $MonthsCount = 12;
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeSelect", $this);

        $this->DataSource->Prepare();
        $this->DataSource->Open();

        while ($this->DataSource->next_record()) {
            $DateField = CCParseDate($this->DataSource->f("event_date"), array("yyyy", "-", "mm", "-", "dd"));
            if (!is_array($DateField)) continue;
            if (CCCompareValues($DateField, $FirstShowedDate, ccsDate) >= 0 && CCCompareValues($DateField, $LastShowedDate, ccsDate) <= 0) {
                $this->DataSource->SetValues();
                $Event = new clsEventyear_events();
                $Event->_Time = CCParseDate($this->DataSource->f("event_time"), array("HH", ":", "nn", ":", "ss"));
                $this->MonthDate->SetValue($this->CurrentProcessingDate);
                $this->DayNumber->SetValue($this->CurrentProcessingDate);
                $Event->MonthDate = $this->DataSource->MonthDate->GetValue();
                $Event->DayNumber = $this->DataSource->DayNumber->GetValue();
                $Event->CategoryImage = $this->DataSource->CategoryImage->GetValue();
                $Event->EventTime = $this->DataSource->EventTime->GetValue();
                $Event->EventTimeEnd = $this->DataSource->EventTimeEnd->GetValue();
                $Event->EventDescription = $this->DataSource->EventDescription->GetValue();
                $Event->GoWeek = $this->DataSource->GoWeek->GetValue();
                $datestr = CCFormatDate($DateField, array("yyyy","mm","dd"));
                if(!isset($this->Events[$datestr])) $this->Events[$datestr] = array();
                $this->Events[$datestr][] = $Event;
            }
        }

        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShow", $this);
        if(!$this->Visible) return;

        $CalendarBlock = "Calendar " . $this->ComponentName;
        $ParentPath = $Tpl->block_path;
        $Tpl->block_path = $ParentPath . "/" . $CalendarBlock;
        $this->Errors->AddErrors($this->DataSource->Errors);
        if($this->Errors->Count()) {
            $Tpl->replaceblock("", $this->Errors->ToString());
            $Tpl->block_path = $ParentPath;
            return;
        } else {
            $month = 0;
            $this->CurrentProcessingDate = $FirstProcessingDate;
            $this->NextProcessingDate = CCDateAdd($this->CurrentProcessingDate, "1month");
            $this->PrevProcessingDate = CCDateAdd($this->CurrentProcessingDate, "-1month");
            $Tpl->block_path = $ParentPath . "/" . $CalendarBlock . "/Month";
            while ($MonthsCount > $month++) {
                $this->ShowMonth();
                if(($MonthsCount != $month) && ($month % $this->MonthsInRow == 0)) {
                    $Tpl->SetVar("MonthsInRow", $this->MonthsInRow);
                    $Tpl->block_path = $ParentPath . "/" . $CalendarBlock;
                    $Tpl->ParseTo("MonthsRowSeparator", true, "Month");
                    $Tpl->block_path = $ParentPath . "/" . $CalendarBlock . "/Month";
                }
                $Tpl->SetBlockVar("Week", "");
                $Tpl->SetBlockVar("Week/Day", "");
                $this->ProcessNextDate(CCDateAdd($this->NextProcessingDate, "+1month"));
            }
            $this->CurrentProcessingDate = $FirstProcessingDate;
            $this->NextProcessingDate = CCDateAdd($this->CurrentProcessingDate, "1year");
            $this->PrevProcessingDate = CCDateAdd($this->CurrentProcessingDate, "-1year");
            $Tpl->SetVar("MonthsInRow", $this->MonthsInRow);
            $Tpl->block_path = $ParentPath . "/" . $CalendarBlock;
            $this->Navigator->CurrentDate = $this->CurrentDate;
            $this->Navigator->PrevProcessingDate = $this->PrevProcessingDate;
            $this->Navigator->NextProcessingDate = $this->NextProcessingDate;
            $this->CurYearLabel->Show();
            $this->Navigator->Show();
            $Tpl->Parse();
        }
        $Tpl->block_path = $ParentPath;
    }
//End Show Method

//year_events ShowMonth Method @5-A8E54CDA
    function ShowMonth () {
        global $Tpl;
        global $CCSLocales;
        global $DefaultDateFormat;
        $ParentPath = $Tpl->block_path;
        $OldCurrentProcessingDate = $this->CurrentProcessingDate;
        $OldNextProcessingDate = $this->NextProcessingDate;
        $OldPrevProcessingDate = $this->PrevProcessingDate;
        $FirstMonthDate = CCParseDate(CCFormatDate($this->CurrentProcessingDate, array("yyyy", "-", "mm","-01 00:00:00")), array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"));
        $LastMonthDate = CCDateAdd($FirstMonthDate, "+1month -1second");
        $Days = (CCFormatDate($FirstMonthDate, array("w")) - $this->FirstWeekDay + 6) % 7;
        $FirstShowedDate = CCDateAdd($FirstMonthDate, "-" . $Days . "day");
        $Days += $LastMonthDate[ccsDay];
        $Days += ($this->FirstWeekDay  - CCFormatDate($LastMonthDate, array("w")) + 7) % 7;
        $this->CurrentProcessingDate =  $FirstShowedDate;
        $this->PrevProcessingDate =  CCDateAdd($FirstShowedDate, "-1day");
        $this->NextProcessingDate =  CCDateAdd($FirstShowedDate, "+1day");
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowMonth", $this);
        $ShowedDays = 0;
        $WeekDay = CCFormatDate($this->CurrentProcessingDate, array("w"));
        while($ShowedDays < $Days) {
            if ($ShowedDays % 7 == 0)
                $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowWeek", $this);
            $this->IsCurrentMonth = $this->CurrentProcessingDate[ccsMonth] == $OldCurrentProcessingDate[ccsMonth];
            $this->SetCurrentStyle("Day", $WeekDay);
            $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowDay", $this);
            if ($this->IsCurrentMonth) {
                $datestr = CCFormatDate($this->CurrentProcessingDate, array("yyyy","mm","dd"));
                $Tpl->block_path = $ParentPath . "/Week/Day/EventRow";
                $Tpl->SetBlockVar("", "");
                if (isset($this->Events[$datestr])) {
                    uasort($this->Events[$datestr], array($this, "CompareEventTime"));
                    foreach ($this->Events[$datestr] as $key=>$event) {
                        $Tpl->block_path = $ParentPath . "/Week/Day/EventRow";
                        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowEvent", $this);
                        $this->MonthDate->SetValue($event->MonthDate);
                        $this->DayNumber->SetValue($event->DayNumber);
                        $this->CategoryImage->SetValue($event->CategoryImage);
                        $this->EventTime->SetValue($event->EventTime);
                        $this->EventTimeEnd->SetValue($event->EventTimeEnd);
                        $this->EventDescription->SetValue($event->EventDescription);
                        $this->GoWeek->SetValue($event->GoWeek);
                        $this->CategoryImage->Show();
                        $this->EventTime->Show();
                        $this->EventTimeEnd->Show();
                        $this->EventDescription->Show();
                        $Tpl->Parse("", true);
                    }
                } else {
                }
                $Tpl->block_path = $ParentPath . "/Week/Day";
                $this->DayNumber->SetValue($this->CurrentProcessingDate);
                $this->DayNumber->Parameters = "";
                $this->DayNumber->Parameters = CCAddParam($this->DayNumber->Parameters, "day", CCFormatDate($this->CurrentProcessingDate, array("yyyy", "-", "mm", "-", "dd")));
                $this->DayNumber->Show();
                $this->div_begin->Show();
                $this->div_end->Show();
                $Tpl->SetVar("Style", $this->CurrentStyle);
                $Tpl->Parse("", true);
            } else {
                $Tpl->block_path = $ParentPath . "/Week/EmptyDay";
                $Tpl->block_path = $ParentPath . "/Week";
                $Tpl->SetVar("Style", $this->CurrentStyle);
                $Tpl->ParseTo("EmptyDay", true, "Day");
            }
            $ShowedDays++;
            if ($ShowedDays and $ShowedDays % 7 == 0) {
                $Tpl->block_path = $ParentPath . "/Week";
                $this->GoWeek->Parameters = "";
                $this->GoWeek->Parameters = CCAddParam($this->GoWeek->Parameters, "day", CCFormatDate($this->CurrentProcessingDate, array("yyyy", "-", "mm", "-", "dd")));
                $this->GoWeek->Show();
                $Tpl->Parse("", true);
                $Tpl->SetBlockVar("Day", "");
            }
            $this->ProcessNextDate(CCDateAdd($this->NextProcessingDate, "+1day"));
            $WeekDay = $WeekDay == 7 ? 1 : $WeekDay + 1;
        }
        $Tpl->block_path = $ParentPath . "/WeekDays";
        $Tpl->SetBlockVar("","");
        $WeekDay = CCFormatDate($this->CurrentProcessingDate, array("w"));
        $ShowedDays = 0;
        $this->CurrentProcessingDate =  $FirstShowedDate;
        $this->PrevProcessingDate =  CCDateAdd($FirstShowedDate, "-1day");
        $this->NextProcessingDate =  CCDateAdd($FirstShowedDate, "+1day");
        while($ShowedDays < 7) {
            $this->DayOfWeek->SetValue($this->CurrentProcessingDate);
            $this->DayOfWeek->Show();
            $this->SetCurrentStyle("WeekDay", $WeekDay);
            $Tpl->SetVar("Style", $this->CurrentStyle);
            $Tpl->Parse("", true);
            $WeekDay = $WeekDay == 7 ? 1 : $WeekDay + 1;
            $this->ProcessNextDate(CCDateAdd($this->NextProcessingDate, "+1day"));
            $ShowedDays++;
        }
        $Tpl->block_path = $ParentPath;
        $this->CurrentProcessingDate = $OldCurrentProcessingDate;
        $this->NextProcessingDate = $OldNextProcessingDate;
        $this->PrevProcessingDate = $OldPrevProcessingDate;
        $this->MonthDate->SetValue($this->CurrentProcessingDate);
        $this->MonthDate->Parameters = "";
        $this->MonthDate->Parameters = CCAddParam($this->MonthDate->Parameters, "cal_monthDate", CCFormatDate($this->CurrentProcessingDate, array("yyyy", "-", "mm")));
        $this->MonthDate->Show();
        $this->GoWeekHeader->Show();
        $Tpl->Parse("", true);
        $Tpl->block_path = $ParentPath;
    }
//End year_events ShowMonth Method

//year_events ProcessNextDate Method @5-67D24A68
    function ProcessNextDate($NewDate) {
        $this->PrevProcessingDate = $this->CurrentProcessingDate;
        $this->CurrentProcessingDate = $this->NextProcessingDate;
        $this->NextProcessingDate = $NewDate;
    }
//End year_events ProcessNextDate Method

//year_events SetNow Method @5-231E221D
    function SetNow ($Now) {
        $this->Now = $Now;
        $this->CurrentDate = $Now;
        if ($FullDate = CCGetFromGet($this->ComponentName . "Date", "")) {
            @list($year,$month) = split("-", $FullDate, 2);
        } else {
            $year = CCGetFromGet($this->ComponentName . "Year", "");
            $month = CCGetFromGet($this->ComponentName . "Month", "");
        }
        if (is_numeric($year) &&  $year >=101 && $year <=9999)
            $this->CurrentDate[ccsYear] = $year;
        if (is_numeric($month) &&  $month >=1 && $month <=12)
            $this->CurrentDate[ccsMonth] = $month;
        $this->CurrentDate[ccsDay] = 1;
    }
//End year_events SetNow Method

//year_events SetCurrentStyle Method @5-1162C70C
    function SetCurrentStyle ($scope, $weekday="") {
        $Result="";
        switch ($scope) {
            case "WeekDay":
                if ($weekday == 1 || $weekday == 7)
                    $Result = "WeekendName";
                else
                    $Result = "WeekdayName";
                break;
            case "Day":
                $IsWeekend = $weekday == 1 || $weekday == 7;
                if (!$this->IsCurrentMonth) {
                    $Result = "OtherMonth" . ($IsWeekend ? "Weekend" : "Day");
                } else {
                    $IsCurrentDay = $this->CurrentProcessingDate[ccsYear] == $this->Now[ccsYear] &&
                        $this->CurrentProcessingDate[ccsMonth] == $this->Now[ccsMonth] &&
                        $this->CurrentProcessingDate[ccsDay] == $this->Now[ccsDay];
                    if($IsCurrentDay)
                        $Result = "Today";
                    if($IsWeekend) 
                        $Result = "Weekend" . $Result;
                    elseif (!$Result) 
                        $Result = "Day";
                }
                break;
        }
        $this->CurrentStyle = isset($this->CalendarStyles[$Result]) ? $this->CalendarStyles[$Result] : "";
    }
//End year_events SetCurrentStyle Method

//year_events CompareEventTime Method @5-2AE3CCE6
    function CompareEventTime($val1, $val2) {
        $time1 = is_a($val1, "clsEventyear_events") && is_array($val1->_Time) ? $val1->_Time[ccsHour] * 3600 + $val1->_Time[ccsMinute] * 60 + $val1->_Time[ccsSecond] : 0;
        $time2 = is_a($val2, "clsEventyear_events") && is_array($val2->_Time) ? $val2->_Time[ccsHour] * 3600 + $val2->_Time[ccsMinute] * 60 + $val2->_Time[ccsSecond] : 0;
        if ($time1 == $time2)
            return 0;
        return $time1 > $time2 ? 1 : -1;
    }
//End year_events CompareEventTime Method

} //End year_events Class @5-FCB6E20C

class clsyear_eventsDataSource extends clsDBcalendar {  //year_eventsDataSource Class @5-3CBAD2F4

//DataSource Variables @5-6BA83D97
    var $Parent = "";
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;
    var $CmdExecution;

    var $wp;


    // Datasource fields
    var $MonthDate;
    var $DayNumber;
    var $CategoryImage;
    var $EventTime;
    var $EventTimeEnd;
    var $EventDescription;
    var $GoWeek;
//End DataSource Variables

//DataSourceClass_Initialize Event @5-73C49B45
    function clsyear_eventsDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "";
        $this->Initialize();
        $this->MonthDate = new clsField("MonthDate", ccsDate, array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"));
        $this->DayNumber = new clsField("DayNumber", ccsDate, array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"));
        $this->CategoryImage = new clsField("CategoryImage", ccsText, "");
        $this->EventTime = new clsField("EventTime", ccsDate, array("HH", ":", "nn", ":", "ss"));
        $this->EventTimeEnd = new clsField("EventTimeEnd", ccsDate, array("HH", ":", "nn", ":", "ss"));
        $this->EventDescription = new clsField("EventDescription", ccsText, "");
        $this->GoWeek = new clsField("GoWeek", ccsText, "");

    }
//End DataSourceClass_Initialize Event

//SetOrder Method @5-9E1383D1
    function SetOrder($SorterName, $SorterDirection)
    {
        $this->Order = "";
        $this->Order = CCGetOrder($this->Order, $SorterName, $SorterDirection, 
            "");
    }
//End SetOrder Method

//Prepare Method @5-10B29740
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "sescategory", ccsInteger, "", "", $this->Parameters["sescategory"], "", false);
        $this->wp->Criterion[1] = $this->wp->Operation(opEqual, "events.category_id", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsInteger),false);
        $this->Where = 
             $this->wp->Criterion[1];
    }
//End Prepare Method

//Open Method @5-D5B4574C
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->SQL = "SELECT category_image, event_time, event_time_end, event_is_public, event_date, event_desc, event_title, event_id \n\n" .
        "FROM events LEFT JOIN categories ON\n\n" .
        "events.category_id = categories.category_id {SQL_Where} {SQL_OrderBy}";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect", $this->Parent);
        $this->query(CCBuildSQL($this->SQL, $this->Where, $this->Order));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect", $this->Parent);
        $this->MoveToPage($this->AbsolutePage);
    }
//End Open Method

//SetValues Method @5-0D9BB318
    function SetValues()
    {
        $this->CategoryImage->SetDBValue($this->f("category_image"));
        $this->EventTime->SetDBValue(trim($this->f("event_time")));
        $this->EventTimeEnd->SetDBValue(trim($this->f("event_time_end")));
        $this->EventDescription->SetDBValue($this->f("event_title"));
    }
//End SetValues Method

} //End year_eventsDataSource Class @5-FCB6E20C

//Include Page implementation @3-EBA5EA16
include_once(RelativePath . "/footer.php");
//End Include Page implementation

//Initialize Page @1-5CE3027F
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
$TemplateFileName = "year.html";
$BlockToParse = "main";
$TemplateEncoding = "UTF-8";
$PathToRoot = "./";
//End Initialize Page

//Include events file @1-77038D9B
include("./year_events.php");
//End Include events file

//Initialize Objects @1-4A223E93
$DBcalendar = new clsDBcalendar();
$MainPage->Connections["calendar"] = & $DBcalendar;

// Controls
$header = & new clsheader("", "header", $MainPage);
$header->Initialize();
$infopanel = & new clsinfopanel("", "infopanel", $MainPage);
$infopanel->Initialize();
$year_events = & new clsCalendaryear_events("", $MainPage);
$footer = & new clsfooter("", "footer", $MainPage);
$footer->Initialize();
$MainPage->header = & $header;
$MainPage->infopanel = & $infopanel;
$MainPage->year_events = & $year_events;
$MainPage->footer = & $footer;
$year_events->Initialize();

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

//Execute Components @1-32C622A9
$header->Operations();
$infopanel->Operations();
$footer->Operations();
//End Execute Components

//Go to destination page @1-7507F630
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
    $DBcalendar->close();
    header("Location: " . $Redirect);
    $header->Class_Terminate();
    unset($header);
    $infopanel->Class_Terminate();
    unset($infopanel);
    unset($year_events);
    $footer->Class_Terminate();
    unset($footer);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-6F777816
$header->Show();
$infopanel->Show();
$year_events->Show();
$footer->Show();
$Tpl->block_path = "";
$Tpl->Parse($BlockToParse, false);
$main_block = $Tpl->GetVar($BlockToParse);
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeOutput", $MainPage);
if ($CCSEventResult) echo $main_block;
//End Show Page

//Unload Page @1-B92261E4
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
$DBcalendar->close();
$header->Class_Terminate();
unset($header);
$infopanel->Class_Terminate();
unset($infopanel);
unset($year_events);
$footer->Class_Terminate();
unset($footer);
unset($Tpl);
//End Unload Page


?>
