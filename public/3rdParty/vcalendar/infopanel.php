<?php

//InfoCalendar clsEvent @108-65A2120A
class clsEventinfopanelInfoCalendar {
    var $_Time;
    var $category_image;
    var $EventTime;
    var $EventTimeEnd;
    var $EventDescription;

}
//End InfoCalendar clsEvent

class clsCalendarinfopanelInfoCalendar { //InfoCalendar Class @108-B8F036CB

//InfoCalendar Variables @108-784FF09C

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
//End InfoCalendar Variables

//InfoCalendar Class_Initialize Event @108-65DA7770
    function clsCalendarinfopanelInfoCalendar($RelativePath, & $Parent) {
        global $CCSLocales;
        global $DefaultDateFormat;
        global $FileName;
        global $Redirect;
        $this->ComponentName = "InfoCalendar";
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
        $this->DataSource = new clsinfopanelInfoCalendarDataSource($this);
        $this->ds = & $this->DataSource;
        $this->FirstWeekDay = $CCSLocales->GetFormatInfo("FirstWeekDay");
        $this->MonthsInRow = 1;


        $this->MonthDate = & new clsControl(ccsLabel, "MonthDate", "MonthDate", ccsDate, array("mmmm", ", ", "yyyy"), CCGetRequestParam("MonthDate", ccsGet, NULL), $this);
        $this->DayOfWeek = & new clsControl(ccsLabel, "DayOfWeek", "DayOfWeek", ccsDate, array("wi"), CCGetRequestParam("DayOfWeek", ccsGet, NULL), $this);
        $this->GoWeekHeader = & new clsPanel("GoWeekHeader", $this);
        $this->DayNumber = & new clsControl(ccsLink, "DayNumber", "DayNumber", ccsDate, array("d"), CCGetRequestParam("DayNumber", ccsGet, NULL), $this);
        $this->DayNumber->Page = $this->RelativePath . "day.php";
        $this->div_begin = & new clsControl(ccsLabel, "div_begin", "div_begin", ccsText, "", CCGetRequestParam("div_begin", ccsGet, NULL), $this);
        $this->div_begin->HTML = true;
        $this->category_image = & new clsControl(ccsImage, "category_image", "category_image", ccsText, "", CCGetRequestParam("category_image", ccsGet, NULL), $this);
        $this->EventTime = & new clsControl(ccsLabel, "EventTime", "EventTime", ccsDate, array("ShortTime"), CCGetRequestParam("EventTime", ccsGet, NULL), $this);
        $this->EventTimeEnd = & new clsControl(ccsLabel, "EventTimeEnd", "EventTimeEnd", ccsDate, array("ShortTime"), CCGetRequestParam("EventTimeEnd", ccsGet, NULL), $this);
        $this->EventDescription = & new clsControl(ccsLabel, "EventDescription", "EventDescription", ccsText, "", CCGetRequestParam("EventDescription", ccsGet, NULL), $this);
        $this->div_end = & new clsControl(ccsLabel, "div_end", "div_end", ccsText, "", CCGetRequestParam("div_end", ccsGet, NULL), $this);
        $this->div_end->HTML = true;
        $this->GoWeek = & new clsControl(ccsLink, "GoWeek", "GoWeek", ccsText, "", CCGetRequestParam("GoWeek", ccsGet, NULL), $this);
        $this->GoWeek->Page = $this->RelativePath . "week.php";
        $this->InfoNavigator = & new clsPanel("InfoNavigator", $this);
        $this->PrevMonth = & new clsControl(ccsLink, "PrevMonth", "PrevMonth", ccsText, "", CCGetRequestParam("PrevMonth", ccsGet, NULL), $this);
        $this->PrevMonth->Parameters = CCGetQueryString("QueryString", array("InfoCalendarDate", "ccsForm"));
        $this->PrevMonth->Page = "";
        $this->NextMonth = & new clsControl(ccsLink, "NextMonth", "NextMonth", ccsText, "", CCGetRequestParam("NextMonth", ccsGet, NULL), $this);
        $this->NextMonth->Parameters = CCGetQueryString("QueryString", array("InfoCalendarDate", "ccsForm"));
        $this->NextMonth->Page = "";
        $this->InfoNavigator->AddComponent("PrevMonth", $this->PrevMonth);
        $this->InfoNavigator->AddComponent("NextMonth", $this->NextMonth);
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
//End InfoCalendar Class_Initialize Event

//Initialize Method @108-A4F1CC7C
    function Initialize()
    {
        if(!$this->Visible) return;
        $this->DataSource->SetOrder("", "");
    }
//End Initialize Method

//Show Method @108-0D89448B
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
                $Event = new clsEventinfopanelInfoCalendar();
                $Event->_Time = CCParseDate($this->DataSource->f("event_time"), $this->DataSource->DateFormat);
                $this->DayNumber->SetValue($this->CurrentProcessingDate);
                $Event->DayNumber = $this->DataSource->DayNumber->GetValue();
                $Event->category_image = $this->DataSource->category_image->GetValue();
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
            $this->NextProcessingDate = CCDateAdd($this->CurrentProcessingDate, "1month");
            $this->PrevProcessingDate = CCDateAdd($this->CurrentProcessingDate, "-1month");
            $Tpl->SetVar("MonthsInRow", $this->MonthsInRow);
            $Tpl->block_path = $ParentPath . "/" . $CalendarBlock;
            $this->MonthDate->SetValue($this->CurrentProcessingDate);
            $this->MonthDate->Show();
            $this->InfoNavigator->Show();
            $Tpl->Parse();
        }
        $Tpl->block_path = $ParentPath;
    }
//End Show Method

//InfoCalendar ShowMonth Method @108-62CD224D
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
            $datestr = CCFormatDate($this->CurrentProcessingDate, array("yyyy","mm","dd"));
            $Tpl->block_path = $ParentPath . "/Week/Day/EventRow";
            $Tpl->SetBlockVar("", "");
            if (isset($this->Events[$datestr])) {
                uasort($this->Events[$datestr], array($this, "CompareEventTime"));
                foreach ($this->Events[$datestr] as $key=>$event) {
                    $Tpl->block_path = $ParentPath . "/Week/Day/EventRow";
                    $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowEvent", $this);
                    $this->DayNumber->SetValue($event->DayNumber);
                    $this->category_image->SetValue($event->category_image);
                    $this->EventTime->SetValue($event->EventTime);
                    $this->EventTimeEnd->SetValue($event->EventTimeEnd);
                    $this->EventDescription->SetValue($event->EventDescription);
                    $this->GoWeek->SetValue($event->GoWeek);
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
            $this->DayNumber->Show();
            $this->div_begin->Show();
            $this->div_end->Show();
            $Tpl->SetVar("Style", $this->CurrentStyle);
            $Tpl->Parse("", true);
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
        $this->GoWeekHeader->Show();
        $Tpl->Parse("", true);
        $Tpl->block_path = $ParentPath;
    }
//End InfoCalendar ShowMonth Method

//InfoCalendar ProcessNextDate Method @108-67D24A68
    function ProcessNextDate($NewDate) {
        $this->PrevProcessingDate = $this->CurrentProcessingDate;
        $this->CurrentProcessingDate = $this->NextProcessingDate;
        $this->NextProcessingDate = $NewDate;
    }
//End InfoCalendar ProcessNextDate Method

//InfoCalendar SetNow Method @108-231E221D
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
//End InfoCalendar SetNow Method

//InfoCalendar SetCurrentStyle Method @108-FDD58228
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
                $IsCurrentDay = $this->CurrentProcessingDate[ccsYear] == $this->Now[ccsYear] &&
                    $this->CurrentProcessingDate[ccsMonth] == $this->Now[ccsMonth] &&
                    $this->CurrentProcessingDate[ccsDay] == $this->Now[ccsDay];
                if($IsCurrentDay)
                    $Result = "Today";
                if($IsWeekend) 
                    $Result = "Weekend" . $Result;
                elseif (!$Result) 
                    $Result = "Day";
                if (!$this->IsCurrentMonth)
                    $Result = "OtherMonth" . $Result;
                break;
        }
        $this->CurrentStyle = isset($this->CalendarStyles[$Result]) ? $this->CalendarStyles[$Result] : "";
    }
//End InfoCalendar SetCurrentStyle Method

//InfoCalendar CompareEventTime Method @108-6ECE798E
    function CompareEventTime($val1, $val2) {
        $time1 = is_a($val1, "clsEventinfopanelInfoCalendar") && is_array($val1->_Time) ? $val1->_Time[ccsHour] * 3600 + $val1->_Time[ccsMinute] * 60 + $val1->_Time[ccsSecond] : 0;
        $time2 = is_a($val2, "clsEventinfopanelInfoCalendar") && is_array($val2->_Time) ? $val2->_Time[ccsHour] * 3600 + $val2->_Time[ccsMinute] * 60 + $val2->_Time[ccsSecond] : 0;
        if ($time1 == $time2)
            return 0;
        return $time1 > $time2 ? 1 : -1;
    }
//End InfoCalendar CompareEventTime Method

} //End InfoCalendar Class @108-FCB6E20C

class clsinfopanelInfoCalendarDataSource extends clsDBcalendar {  //InfoCalendarDataSource Class @108-6A9ACB6E

//DataSource Variables @108-D9A0AF30
    var $Parent = "";
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;
    var $CmdExecution;

    var $wp;


    // Datasource fields
    var $DayNumber;
    var $category_image;
    var $EventTime;
    var $EventTimeEnd;
    var $EventDescription;
    var $GoWeek;
//End DataSource Variables

//DataSourceClass_Initialize Event @108-BC3779F2
    function clsinfopanelInfoCalendarDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "";
        $this->Initialize();
        $this->DayNumber = new clsField("DayNumber", ccsDate, array("yyyy", "-", "mm", "-", "dd", " ", "HH", ":", "nn", ":", "ss"));
        $this->category_image = new clsField("category_image", ccsText, "");
        $this->EventTime = new clsField("EventTime", ccsDate, array("HH", ":", "nn", ":", "ss"));
        $this->EventTimeEnd = new clsField("EventTimeEnd", ccsDate, array("HH", ":", "nn", ":", "ss"));
        $this->EventDescription = new clsField("EventDescription", ccsText, "");
        $this->GoWeek = new clsField("GoWeek", ccsText, "");

    }
//End DataSourceClass_Initialize Event

//SetOrder Method @108-9E1383D1
    function SetOrder($SorterName, $SorterDirection)
    {
        $this->Order = "";
        $this->Order = CCGetOrder($this->Order, $SorterName, $SorterDirection, 
            "");
    }
//End SetOrder Method

//Prepare Method @108-10B29740
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

//Open Method @108-6642C1E4
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->SQL = "SELECT category_image, event_date, event_time, event_time_end, event_title \n\n" .
        "FROM events LEFT JOIN categories ON\n\n" .
        "events.category_id = categories.category_id {SQL_Where} {SQL_OrderBy}";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect", $this->Parent);
        $this->query(CCBuildSQL($this->SQL, $this->Where, $this->Order));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect", $this->Parent);
        $this->MoveToPage($this->AbsolutePage);
    }
//End Open Method

//SetValues Method @108-1FDFB32C
    function SetValues()
    {
        $this->category_image->SetDBValue($this->f("category_image"));
        $this->EventTime->SetDBValue(trim($this->f("event_time")));
        $this->EventTimeEnd->SetDBValue(trim($this->f("event_time_end")));
        $this->EventDescription->SetDBValue($this->f("event_title"));
    }
//End SetValues Method

} //End InfoCalendarDataSource Class @108-FCB6E20C

//Include Page implementation @168-D3FCB384
include_once(RelativePath . "/vertical_menu.php");
//End Include Page implementation

class clsinfopanel { //infopanel class @1-852FCAF2

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

//Class_Initialize Event @1-FD5777D8
    function clsinfopanel($RelativePath, $ComponentName, & $Parent)
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->ComponentName = $ComponentName;
        $this->RelativePath = $RelativePath;
        $this->Visible = true;
        $this->Parent = & $Parent;
        $this->FileName = "infopanel.php";
        $this->Redirect = "";
        $this->TemplateFileName = "infopanel.html";
        $this->BlockToParse = "main";
        $this->TemplateEncoding = "UTF-8";
    }
//End Class_Initialize Event

//Class_Terminate Event @1-3793EAEB
    function Class_Terminate()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeUnload", $this);
        unset($this->InfoCalendar);
        $this->vertical_menu->Class_Terminate();
        unset($this->vertical_menu);
    }
//End Class_Terminate Event

//BindEvents Method @1-7AF5BA7A
    function BindEvents()
    {
        $this->InfoCalendar->GoWeekHeader->CCSEvents["BeforeShow"] = "infopanel_InfoCalendar_GoWeekHeader_BeforeShow";
        $this->InfoCalendar->category_image->CCSEvents["BeforeShow"] = "infopanel_InfoCalendar_category_image_BeforeShow";
        $this->InfoCalendar->EventTime->CCSEvents["BeforeShow"] = "infopanel_InfoCalendar_EventTime_BeforeShow";
        $this->InfoCalendar->GoWeek->CCSEvents["BeforeShow"] = "infopanel_InfoCalendar_GoWeek_BeforeShow";
        $this->InfoCalendar->InfoNavigator->CCSEvents["BeforeShow"] = "infopanel_InfoCalendar_InfoNavigator_BeforeShow";
        $this->InfoCalendar->CCSEvents["BeforeShowDay"] = "infopanel_InfoCalendar_BeforeShowDay";
        $this->InfoCalendar->ds->CCSEvents["BeforeBuildSelect"] = "infopanel_InfoCalendar_ds_BeforeBuildSelect";
        $this->CCSEvents["AfterInitialize"] = "infopanel_AfterInitialize";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterInitialize", $this);
    }
//End BindEvents Method

//Operations Method @1-18FCC81D
    function Operations()
    {
        global $Redirect;
        if(!$this->Visible)
            return "";
        $this->vertical_menu->Operations();
    }
//End Operations Method

//Initialize Method @1-1169ED2F
    function Initialize()
    {
        global $FileName;
        global $CCSLocales;
        if(!$this->Visible)
            return "";
        $this->DBcalendar = new clsDBcalendar();
        $this->Connections["calendar"] = & $this->DBcalendar;

        // Create Components
        $this->InfoJavaScriptPanel = & new clsPanel("InfoJavaScriptPanel", $this);
        $this->InfoCalendar = & new clsCalendarinfopanelInfoCalendar($this->RelativePath, $this);
        $this->vertical_menu = & new clsvertical_menu($this->RelativePath, "vertical_menu", $this);
        $this->vertical_menu->Initialize();
        $this->InfoCalendar->Initialize();
        $this->BindEvents();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnInitializeView", $this);
    }
//End Initialize Method

//Show Method @1-2104F46F
    function Show()
    {
        global $Tpl;
        global $CCSLocales;
        $block_path = $Tpl->block_path;
        $Tpl->LoadTemplate("/" . $this->TemplateFileName, $this->ComponentName, $this->TemplateEncoding, "remove");
        $Tpl->block_path = $Tpl->block_path . "/" . $this->ComponentName;
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShow", $this);
        if(!$this->Visible) {
            $Tpl->block_path = $block_path;
            $Tpl->SetVar($this->ComponentName, "");
            return "";
        }
        $this->InfoCalendar->Show();
        $this->vertical_menu->Show();
        $this->InfoJavaScriptPanel->Show();
        $Tpl->Parse();
        $Tpl->block_path = $block_path;
        $Tpl->SetVar($this->ComponentName, $Tpl->GetVar($this->ComponentName));
    }
//End Show Method

} //End infopanel Class @1-FCB6E20C

//Include Event File @1-ADE42145
include(RelativePath . "/infopanel_events.php");
//End Include Event File


?>
