<?php
//Include Common Files @1-142D97AC
define("RelativePath", ".");
define("PathToCurrentPage", "/");
define("FileName", "index.php");
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

//cal_month clsEvent @5-DA3AD7E8
class clsEventcal_month {
    var $_Time;
    var $category_image;
    var $EventTime;
    var $EventTimeEnd;
    var $EventDescription;
    var $_EventDescriptionPage;
    var $_EventDescriptionParameters;

}
//End cal_month clsEvent

class clsCalendarcal_month { //cal_month Class @5-B799D37B

//cal_month Variables @5-784FF09C

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
//End cal_month Variables

//cal_month Class_Initialize Event @5-500AD8D9
    function clsCalendarcal_month($RelativePath, & $Parent) {
        global $CCSLocales;
        global $DefaultDateFormat;
        global $FileName;
        global $Redirect;
        $this->ComponentName = "cal_month";
        $this->Type = "1";
        $this->Visible = True;
        $this->RelativePath = $RelativePath;
        $this->Parent = & $Parent;
        $this->Errors = new clsErrors();
        $CCSForm = CCGetFromGet("ccsForm", "");
        if ($CCSForm == $this->ComponentName) {
            $Redirect = FileName . "?" .  CCGetQueryString("All", array("ccsForm"));
            return;
        }
        $this->DataSource = new clscal_monthDataSource($this);
        $this->ds = & $this->DataSource;
        $this->FirstWeekDay = $CCSLocales->GetFormatInfo("FirstWeekDay");
        $this->MonthsInRow = 1;


        $this->MonthDate = & new clsControl(ccsLabel, "MonthDate", "MonthDate", ccsDate, array("mmmm", " ", "yyyy"), CCGetRequestParam("MonthDate", ccsGet, NULL), $this);
        $this->DayOfWeek = & new clsControl(ccsLabel, "DayOfWeek", "DayOfWeek", ccsDate, array("dddd"), CCGetRequestParam("DayOfWeek", ccsGet, NULL), $this);
        $this->DayNumber = & new clsControl(ccsLink, "DayNumber", "DayNumber", ccsDate, array("d"), CCGetRequestParam("DayNumber", ccsGet, NULL), $this);
        $this->DayNumber->Page = "day.php";
        $this->add_event = & new clsControl(ccsLink, "add_event", "add_event", ccsText, "", CCGetRequestParam("add_event", ccsGet, NULL), $this);
        $this->add_event->Page = "events.php";
        $this->category_image = & new clsControl(ccsImage, "category_image", "category_image", ccsText, "", CCGetRequestParam("category_image", ccsGet, NULL), $this);
        $this->EventTime = & new clsControl(ccsLabel, "EventTime", "EventTime", ccsDate, array("ShortTime"), CCGetRequestParam("EventTime", ccsGet, NULL), $this);
        $this->EventTimeEnd = & new clsControl(ccsLabel, "EventTimeEnd", "EventTimeEnd", ccsDate, array("ShortTime"), CCGetRequestParam("EventTimeEnd", ccsGet, NULL), $this);
        $this->EventDescription = & new clsControl(ccsLink, "EventDescription", "EventDescription", ccsText, "", CCGetRequestParam("EventDescription", ccsGet, NULL), $this);
        $this->EventDescription->Page = "event_view.php";
        $this->go_week = & new clsControl(ccsLink, "go_week", "go_week", ccsText, "", CCGetRequestParam("go_week", ccsGet, NULL), $this);
        $this->go_week->Page = "week.php";
        $this->Navigator = & new clsCalendarNavigator($this->ComponentName, "Navigator", $this->Type, 10, $this);
        $this->CalendarTypes = & new clsPanel("CalendarTypes", $this);
        $this->YearIcon = & new clsControl(ccsLink, "YearIcon", "YearIcon", ccsText, "", CCGetRequestParam("YearIcon", ccsGet, NULL), $this);
        $this->YearIcon->Page = "year.php";
        $this->MonthIcon = & new clsControl(ccsLink, "MonthIcon", "MonthIcon", ccsText, "", CCGetRequestParam("MonthIcon", ccsGet, NULL), $this);
        $this->MonthIcon->Parameters = CCGetQueryString("QueryString", array("ccsForm"));
        $this->MonthIcon->Page = "index.php";
        $this->WeekIcon = & new clsControl(ccsLink, "WeekIcon", "WeekIcon", ccsText, "", CCGetRequestParam("WeekIcon", ccsGet, NULL), $this);
        $this->WeekIcon->Page = "week.php";
        $this->CalendarTypes->AddComponent("YearIcon", $this->YearIcon);
        $this->CalendarTypes->AddComponent("MonthIcon", $this->MonthIcon);
        $this->CalendarTypes->AddComponent("WeekIcon", $this->WeekIcon);
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
//End cal_month Class_Initialize Event

//Initialize Method @5-A4F1CC7C
    function Initialize()
    {
        if(!$this->Visible) return;
        $this->DataSource->SetOrder("", "");
    }
//End Initialize Method

//Show Method @5-6FBDDD10
    function Show () {
        global $Tpl;
        global $CCSLocales;
        global $DefaultDateFormat;
        if(!$this->Visible) return;

        $this->DataSource->Parameters["sescategory"] = CCGetSession("category", NULL);

        $FirstProcessingDate = CCParseDate(CCFormatDate($this->CurrentDate, array("yyyy","-","mm","-01 00:00:00")), array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"));
        $Days = (CCFormatDate($FirstProcessingDate, array("w")) - $this->FirstWeekDay + 6) % 7;
        $FirstShowedDate = CCDateAdd($FirstProcessingDate, "-" . $Days . "day");
        $LastProcessingDate = CCDateAdd($FirstProcessingDate, "1month -1second");
        $Days = ($this->FirstWeekDay - CCFormatDate($LastProcessingDate, array("w")) + 7) % 7;
        $LastShowedDate = CCDateAdd($LastProcessingDate, $Days . "day");
        $MonthsCount = 1;
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeSelect", $this);

        $this->DataSource->Prepare();
        $this->DataSource->Open();

        while ($this->DataSource->next_record()) {
            $DateField = CCParseDate($this->DataSource->f("event_date"), array("yyyy", "-", "mm", "-", "dd"));
            if (!is_array($DateField)) continue;
            if (CCCompareValues($DateField, $FirstShowedDate, ccsDate) >= 0 && CCCompareValues($DateField, $LastShowedDate, ccsDate) <= 0) {
                $this->DataSource->SetValues();
                $Event = new clsEventcal_month();
                $Event->_Time = CCParseDate($this->DataSource->f("event_time"), array("HH", ":", "nn", ":", "ss"));
                $this->DayNumber->SetValue($this->CurrentProcessingDate);
                $this->EventDescription->Parameters = "";
                $this->EventDescription->Parameters = CCAddParam($this->EventDescription->Parameters, "event_id", $this->DataSource->f("event_id"));
                $Event->DayNumber = $this->DataSource->DayNumber->GetValue();
                $Event->add_event = $this->DataSource->add_event->GetValue();
                $Event->category_image = $this->DataSource->category_image->GetValue();
                $Event->EventTime = $this->DataSource->EventTime->GetValue();
                $Event->EventTimeEnd = $this->DataSource->EventTimeEnd->GetValue();
                $Event->EventDescription = $this->DataSource->EventDescription->GetValue();
                $Event->go_week = $this->DataSource->go_week->GetValue();
                $Event->_EventDescriptionPage = $this->EventDescription->Page;
                $Event->_EventDescriptionParameters = $this->EventDescription->Parameters;
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
            $this->NextProcessingDate = CCDateAdd($this->CurrentProcessingDate, "1month");
            $this->PrevProcessingDate = CCDateAdd($this->CurrentProcessingDate, "-1month");
            $Tpl->SetVar("MonthsInRow", $this->MonthsInRow);
            $Tpl->block_path = $ParentPath . "/" . $CalendarBlock;
            $this->MonthDate->SetValue($this->CurrentProcessingDate);
            $this->Navigator->CurrentDate = $this->CurrentDate;
            $this->Navigator->PrevProcessingDate = $this->PrevProcessingDate;
            $this->Navigator->NextProcessingDate = $this->NextProcessingDate;
            $this->MonthDate->Show();
            $this->Navigator->Show();
            $this->CalendarTypes->Show();
            $Tpl->Parse();
        }
        $Tpl->block_path = $ParentPath;
    }
//End Show Method

//cal_month ShowMonth Method @5-875DA233
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
                        $this->EventDescription->Page = $event->_EventDescriptionPage;
                        $this->EventDescription->Parameters = $event->_EventDescriptionParameters;
                        $this->DayNumber->SetValue($event->DayNumber);
                        $this->add_event->SetValue($event->add_event);
                        $this->category_image->SetValue($event->category_image);
                        $this->EventTime->SetValue($event->EventTime);
                        $this->EventTimeEnd->SetValue($event->EventTimeEnd);
                        $this->EventDescription->SetValue($event->EventDescription);
                        $this->go_week->SetValue($event->go_week);
                        $this->category_image->Show();
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
                $this->add_event->Parameters = "";
                $this->add_event->Parameters = CCAddParam($this->add_event->Parameters, "event_date", CCFormatDate($this->CurrentProcessingDate, array("mm", "/", "dd", "/", "yyyy")));
                $this->DayNumber->Show();
                $this->add_event->Show();
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
                $this->go_week->Parameters = "";
                $this->go_week->Parameters = CCAddParam($this->go_week->Parameters, "day", CCFormatDate($this->CurrentProcessingDate, array("yyyy", "-", "mm", "-", "dd")));
                $this->go_week->Show();
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
        $Tpl->Parse("", true);
        $Tpl->block_path = $ParentPath;
    }
//End cal_month ShowMonth Method

//cal_month ProcessNextDate Method @5-67D24A68
    function ProcessNextDate($NewDate) {
        $this->PrevProcessingDate = $this->CurrentProcessingDate;
        $this->CurrentProcessingDate = $this->NextProcessingDate;
        $this->NextProcessingDate = $NewDate;
    }
//End cal_month ProcessNextDate Method

//cal_month SetNow Method @5-231E221D
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
//End cal_month SetNow Method

//cal_month SetCurrentStyle Method @5-1162C70C
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
//End cal_month SetCurrentStyle Method

//cal_month CompareEventTime Method @5-0D15932F
    function CompareEventTime($val1, $val2) {
        $time1 = is_a($val1, "clsEventcal_month") && is_array($val1->_Time) ? $val1->_Time[ccsHour] * 3600 + $val1->_Time[ccsMinute] * 60 + $val1->_Time[ccsSecond] : 0;
        $time2 = is_a($val2, "clsEventcal_month") && is_array($val2->_Time) ? $val2->_Time[ccsHour] * 3600 + $val2->_Time[ccsMinute] * 60 + $val2->_Time[ccsSecond] : 0;
        if ($time1 == $time2)
            return 0;
        return $time1 > $time2 ? 1 : -1;
    }
//End cal_month CompareEventTime Method

} //End cal_month Class @5-FCB6E20C

class clscal_monthDataSource extends clsDBcalendar {  //cal_monthDataSource Class @5-A74108BC

//DataSource Variables @5-133AB85A
    var $Parent = "";
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;
    var $CmdExecution;

    var $wp;


    // Datasource fields
    var $DayNumber;
    var $add_event;
    var $category_image;
    var $EventTime;
    var $EventTimeEnd;
    var $EventDescription;
    var $go_week;
//End DataSource Variables

//DataSourceClass_Initialize Event @5-266840CE
    function clscal_monthDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "";
        $this->Initialize();
        $this->DayNumber = new clsField("DayNumber", ccsDate, array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"));
        $this->add_event = new clsField("add_event", ccsText, "");
        $this->category_image = new clsField("category_image", ccsText, "");
        $this->EventTime = new clsField("EventTime", ccsDate, array("HH", ":", "nn", ":", "ss"));
        $this->EventTimeEnd = new clsField("EventTimeEnd", ccsDate, array("HH", ":", "nn", ":", "ss"));
        $this->EventDescription = new clsField("EventDescription", ccsText, "");
        $this->go_week = new clsField("go_week", ccsText, "");

    }
//End DataSourceClass_Initialize Event

//SetOrder Method @5-6E417178
    function SetOrder($SorterName, $SorterDirection)
    {
        $this->Order = "event_time, event_time_end";
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

//Open Method @5-6FCDBA60
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->SQL = "SELECT category_image, event_id, event_title, event_date, event_time, event_time_end \n\n" .
        "FROM events LEFT JOIN categories ON\n\n" .
        "events.category_id = categories.category_id {SQL_Where} {SQL_OrderBy}";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect", $this->Parent);
        $this->query(CCBuildSQL($this->SQL, $this->Where, $this->Order));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect", $this->Parent);
        $this->MoveToPage($this->AbsolutePage);
    }
//End Open Method

//SetValues Method @5-1FDFB32C
    function SetValues()
    {
        $this->category_image->SetDBValue($this->f("category_image"));
        $this->EventTime->SetDBValue(trim($this->f("event_time")));
        $this->EventTimeEnd->SetDBValue(trim($this->f("event_time_end")));
        $this->EventDescription->SetDBValue($this->f("event_title"));
    }
//End SetValues Method

} //End cal_monthDataSource Class @5-FCB6E20C

//Include Page implementation @3-EBA5EA16
include_once(RelativePath . "/footer.php");
//End Include Page implementation

//Initialize Page @1-2504188A
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
$PathToRoot = "./";
//End Initialize Page

//Include events file @1-7D9DFCA7
include("./index_events.php");
//End Include events file

//Initialize Objects @1-9AB78290
$DBcalendar = new clsDBcalendar();
$MainPage->Connections["calendar"] = & $DBcalendar;

// Controls
$header = & new clsheader("", "header", $MainPage);
$header->Initialize();
$infopanel = & new clsinfopanel("", "infopanel", $MainPage);
$infopanel->Initialize();
$cal_month = & new clsCalendarcal_month("", $MainPage);
$footer = & new clsfooter("", "footer", $MainPage);
$footer->Initialize();
$MainPage->header = & $header;
$MainPage->infopanel = & $infopanel;
$MainPage->cal_month = & $cal_month;
$MainPage->footer = & $footer;
$cal_month->Initialize();

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

//Go to destination page @1-1726FDE1
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
    $DBcalendar->close();
    header("Location: " . $Redirect);
    $header->Class_Terminate();
    unset($header);
    $infopanel->Class_Terminate();
    unset($infopanel);
    unset($cal_month);
    $footer->Class_Terminate();
    unset($footer);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-4FE585C7
$header->Show();
$infopanel->Show();
$cal_month->Show();
$footer->Show();
$Tpl->block_path = "";
$Tpl->Parse($BlockToParse, false);
$main_block = $Tpl->GetVar($BlockToParse);
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeOutput", $MainPage);
if ($CCSEventResult) echo $main_block;
//End Show Page

//Unload Page @1-5F32A2CB
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
$DBcalendar->close();
$header->Class_Terminate();
unset($header);
$infopanel->Class_Terminate();
unset($infopanel);
unset($cal_month);
$footer->Class_Terminate();
unset($footer);
unset($Tpl);
//End Unload Page


?>
