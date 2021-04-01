<?php
//Include Common Files @1-99D479BE
define("RelativePath", ".");
define("PathToCurrentPage", "/");
define("FileName", "event_view.php");
include(RelativePath . "/Common.php");
include(RelativePath . "/Template.php");
include(RelativePath . "/Sorter.php");
include(RelativePath . "/Navigator.php");
//End Include Common Files

//Include Page implementation @2-8EACA429
include_once(RelativePath . "/header.php");
//End Include Page implementation

//Include Page implementation @4-D3FCB384
include_once(RelativePath . "/vertical_menu.php");
//End Include Page implementation

class clsGrideventGrid { //eventGrid class @5-FA43114E

//Variables @5-663D5B8C

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
//End Variables

//Class_Initialize Event @5-8A80514D
    function clsGrideventGrid($RelativePath, & $Parent)
    {
        global $FileName;
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->ComponentName = "eventGrid";
        $this->Visible = True;
        $this->Parent = & $Parent;
        $this->RelativePath = $RelativePath;
        $this->Errors = new clsErrors();
        $this->ErrorBlock = "Grid eventGrid";
        $this->DataSource = new clseventGridDataSource($this);
        $this->ds = & $this->DataSource;
        $this->PageSize = CCGetParam($this->ComponentName . "PageSize", "");
        if(!is_numeric($this->PageSize) || !strlen($this->PageSize))
            $this->PageSize = 1;
        else
            $this->PageSize = intval($this->PageSize);
        if ($this->PageSize > 100)
            $this->PageSize = 100;
        if($this->PageSize == 0)
            $this->Errors->addError("<p>Form: Grid " . $this->ComponentName . "<br>Error: (CCS06) Invalid page size.</p>");
        $this->PageNumber = intval(CCGetParam($this->ComponentName . "Page", 1));
        if ($this->PageNumber <= 0) $this->PageNumber = 1;

        $this->event_date = & new clsControl(ccsLabel, "event_date", "event_date", ccsDate, array("LongDate"), CCGetRequestParam("event_date", ccsGet, NULL), $this);
        $this->event_time = & new clsControl(ccsLabel, "event_time", "event_time", ccsDate, array("ShortTime"), CCGetRequestParam("event_time", ccsGet, NULL), $this);
        $this->event_time_end = & new clsControl(ccsLabel, "event_time_end", "event_time_end", ccsDate, array("ShortTime"), CCGetRequestParam("event_time_end", ccsGet, NULL), $this);
        $this->category_id = & new clsControl(ccsLabel, "category_id", "category_id", ccsText, "", CCGetRequestParam("category_id", ccsGet, NULL), $this);
        $this->user_id = & new clsControl(ccsLabel, "user_id", "user_id", ccsText, "", CCGetRequestParam("user_id", ccsGet, NULL), $this);
        $this->event_desc = & new clsControl(ccsLabel, "event_desc", "event_desc", ccsMemo, "", CCGetRequestParam("event_desc", ccsGet, NULL), $this);
        $this->PanelLocation = & new clsPanel("PanelLocation", $this);
        $this->LabelLocation = & new clsControl(ccsLabel, "LabelLocation", "LabelLocation", ccsText, "", CCGetRequestParam("LabelLocation", ccsGet, NULL), $this);
        $this->event_Location = & new clsControl(ccsLabel, "event_Location", "event_Location", ccsText, "", CCGetRequestParam("event_Location", ccsGet, NULL), $this);
        $this->PanelCost = & new clsPanel("PanelCost", $this);
        $this->LabelCost = & new clsControl(ccsLabel, "LabelCost", "LabelCost", ccsText, "", CCGetRequestParam("LabelCost", ccsGet, NULL), $this);
        $this->event_Cost = & new clsControl(ccsLabel, "event_Cost", "event_Cost", ccsText, "", CCGetRequestParam("event_Cost", ccsGet, NULL), $this);
        $this->PanelURL = & new clsPanel("PanelURL", $this);
        $this->LabelURL = & new clsControl(ccsLabel, "LabelURL", "LabelURL", ccsText, "", CCGetRequestParam("LabelURL", ccsGet, NULL), $this);
        $this->event_URL = & new clsControl(ccsLink, "event_URL", "event_URL", ccsText, "", CCGetRequestParam("event_URL", ccsGet, NULL), $this);
        $this->PanelTextBox1 = & new clsPanel("PanelTextBox1", $this);
        $this->LabelTextBox1 = & new clsControl(ccsLabel, "LabelTextBox1", "LabelTextBox1", ccsText, "", CCGetRequestParam("LabelTextBox1", ccsGet, NULL), $this);
        $this->event_TextBox1 = & new clsControl(ccsLabel, "event_TextBox1", "event_TextBox1", ccsText, "", CCGetRequestParam("event_TextBox1", ccsGet, NULL), $this);
        $this->PanelTextBox2 = & new clsPanel("PanelTextBox2", $this);
        $this->LabelTextBox2 = & new clsControl(ccsLabel, "LabelTextBox2", "LabelTextBox2", ccsText, "", CCGetRequestParam("LabelTextBox2", ccsGet, NULL), $this);
        $this->event_TextBox2 = & new clsControl(ccsLabel, "event_TextBox2", "event_TextBox2", ccsText, "", CCGetRequestParam("event_TextBox2", ccsGet, NULL), $this);
        $this->PanelTextBox3 = & new clsPanel("PanelTextBox3", $this);
        $this->LabelTextBox3 = & new clsControl(ccsLabel, "LabelTextBox3", "LabelTextBox3", ccsText, "", CCGetRequestParam("LabelTextBox3", ccsGet, NULL), $this);
        $this->event_TextBox3 = & new clsControl(ccsLabel, "event_TextBox3", "event_TextBox3", ccsText, "", CCGetRequestParam("event_TextBox3", ccsGet, NULL), $this);
        $this->PanelTextArea1 = & new clsPanel("PanelTextArea1", $this);
        $this->LabelTextArea1 = & new clsControl(ccsLabel, "LabelTextArea1", "LabelTextArea1", ccsText, "", CCGetRequestParam("LabelTextArea1", ccsGet, NULL), $this);
        $this->event_TextArea1 = & new clsControl(ccsLabel, "event_TextArea1", "event_TextArea1", ccsText, "", CCGetRequestParam("event_TextArea1", ccsGet, NULL), $this);
        $this->PanelTextArea2 = & new clsPanel("PanelTextArea2", $this);
        $this->LabelTextArea2 = & new clsControl(ccsLabel, "LabelTextArea2", "LabelTextArea2", ccsText, "", CCGetRequestParam("LabelTextArea2", ccsGet, NULL), $this);
        $this->event_TextArea2 = & new clsControl(ccsLabel, "event_TextArea2", "event_TextArea2", ccsText, "", CCGetRequestParam("event_TextArea2", ccsGet, NULL), $this);
        $this->PanelTextArea3 = & new clsPanel("PanelTextArea3", $this);
        $this->LabelTextArea3 = & new clsControl(ccsLabel, "LabelTextArea3", "LabelTextArea3", ccsText, "", CCGetRequestParam("LabelTextArea3", ccsGet, NULL), $this);
        $this->event_TextArea3 = & new clsControl(ccsLabel, "event_TextArea3", "event_TextArea3", ccsText, "", CCGetRequestParam("event_TextArea3", ccsGet, NULL), $this);
        $this->PanelCheckBox1 = & new clsPanel("PanelCheckBox1", $this);
        $this->LabelCheckBox1 = & new clsControl(ccsLabel, "LabelCheckBox1", "LabelCheckBox1", ccsText, "", CCGetRequestParam("LabelCheckBox1", ccsGet, NULL), $this);
        $this->event_CheckBox1 = & new clsControl(ccsLabel, "event_CheckBox1", "event_CheckBox1", ccsBoolean, $CCSLocales->GetFormatInfo("BooleanFormat"), CCGetRequestParam("event_CheckBox1", ccsGet, NULL), $this);
        $this->PanelCheckBox2 = & new clsPanel("PanelCheckBox2", $this);
        $this->LabelCheckBox2 = & new clsControl(ccsLabel, "LabelCheckBox2", "LabelCheckBox2", ccsText, "", CCGetRequestParam("LabelCheckBox2", ccsGet, NULL), $this);
        $this->event_CheckBox2 = & new clsControl(ccsLabel, "event_CheckBox2", "event_CheckBox2", ccsBoolean, $CCSLocales->GetFormatInfo("BooleanFormat"), CCGetRequestParam("event_CheckBox2", ccsGet, NULL), $this);
        $this->PanelCheckBox3 = & new clsPanel("PanelCheckBox3", $this);
        $this->LabelCheckBox3 = & new clsControl(ccsLabel, "LabelCheckBox3", "LabelCheckBox3", ccsText, "", CCGetRequestParam("LabelCheckBox3", ccsGet, NULL), $this);
        $this->event_CheckBox3 = & new clsControl(ccsLabel, "event_CheckBox3", "event_CheckBox3", ccsBoolean, $CCSLocales->GetFormatInfo("BooleanFormat"), CCGetRequestParam("event_CheckBox3", ccsGet, NULL), $this);
        $this->event_title = & new clsControl(ccsLabel, "event_title", "event_title", ccsText, "", CCGetRequestParam("event_title", ccsGet, NULL), $this);
        $this->edit = & new clsPanel("edit", $this);
        $this->edit_event = & new clsControl(ccsLink, "edit_event", "edit_event", ccsText, "", CCGetRequestParam("edit_event", ccsGet, NULL), $this);
        $this->edit_event->Parameters = CCAddParam($this->edit_event->Parameters, "event_id", CCGetFromGet("event_id", NULL));
        $this->edit_event->Page = "events.php";
        $this->PanelLocation->AddComponent("LabelLocation", $this->LabelLocation);
        $this->PanelLocation->AddComponent("event_Location", $this->event_Location);
        $this->PanelCost->AddComponent("LabelCost", $this->LabelCost);
        $this->PanelCost->AddComponent("event_Cost", $this->event_Cost);
        $this->PanelURL->AddComponent("LabelURL", $this->LabelURL);
        $this->PanelURL->AddComponent("event_URL", $this->event_URL);
        $this->PanelTextBox1->AddComponent("LabelTextBox1", $this->LabelTextBox1);
        $this->PanelTextBox1->AddComponent("event_TextBox1", $this->event_TextBox1);
        $this->PanelTextBox2->AddComponent("LabelTextBox2", $this->LabelTextBox2);
        $this->PanelTextBox2->AddComponent("event_TextBox2", $this->event_TextBox2);
        $this->PanelTextBox3->AddComponent("LabelTextBox3", $this->LabelTextBox3);
        $this->PanelTextBox3->AddComponent("event_TextBox3", $this->event_TextBox3);
        $this->PanelTextArea1->AddComponent("LabelTextArea1", $this->LabelTextArea1);
        $this->PanelTextArea1->AddComponent("event_TextArea1", $this->event_TextArea1);
        $this->PanelTextArea2->AddComponent("LabelTextArea2", $this->LabelTextArea2);
        $this->PanelTextArea2->AddComponent("event_TextArea2", $this->event_TextArea2);
        $this->PanelTextArea3->AddComponent("LabelTextArea3", $this->LabelTextArea3);
        $this->PanelTextArea3->AddComponent("event_TextArea3", $this->event_TextArea3);
        $this->PanelCheckBox1->AddComponent("LabelCheckBox1", $this->LabelCheckBox1);
        $this->PanelCheckBox1->AddComponent("event_CheckBox1", $this->event_CheckBox1);
        $this->PanelCheckBox2->AddComponent("LabelCheckBox2", $this->LabelCheckBox2);
        $this->PanelCheckBox2->AddComponent("event_CheckBox2", $this->event_CheckBox2);
        $this->PanelCheckBox3->AddComponent("LabelCheckBox3", $this->LabelCheckBox3);
        $this->PanelCheckBox3->AddComponent("event_CheckBox3", $this->event_CheckBox3);
        $this->edit->AddComponent("edit_event", $this->edit_event);
    }
//End Class_Initialize Event

//Initialize Method @5-90E704C5
    function Initialize()
    {
        if(!$this->Visible) return;

        $this->DataSource->PageSize = & $this->PageSize;
        $this->DataSource->AbsolutePage = & $this->PageNumber;
        $this->DataSource->SetOrder($this->SorterName, $this->SorterDirection);
    }
//End Initialize Method

//Show Method @5-1654D9F3
    function Show()
    {
        global $Tpl;
        global $CCSLocales;
        if(!$this->Visible) return;

        $this->RowNumber = 0;

        $this->DataSource->Parameters["urlevent_id"] = CCGetFromGet("event_id", NULL);
        $this->DataSource->Parameters["seslocale"] = CCGetSession("locale", NULL);
        $this->DataSource->Parameters["urlevents_category_id"] = CCGetFromGet("events_category_id", NULL);

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
            $this->ControlsVisible["event_date"] = $this->event_date->Visible;
            $this->ControlsVisible["event_time"] = $this->event_time->Visible;
            $this->ControlsVisible["event_time_end"] = $this->event_time_end->Visible;
            $this->ControlsVisible["category_id"] = $this->category_id->Visible;
            $this->ControlsVisible["user_id"] = $this->user_id->Visible;
            $this->ControlsVisible["event_desc"] = $this->event_desc->Visible;
            $this->ControlsVisible["PanelLocation"] = $this->PanelLocation->Visible;
            $this->ControlsVisible["LabelLocation"] = $this->LabelLocation->Visible;
            $this->ControlsVisible["event_Location"] = $this->event_Location->Visible;
            $this->ControlsVisible["PanelCost"] = $this->PanelCost->Visible;
            $this->ControlsVisible["LabelCost"] = $this->LabelCost->Visible;
            $this->ControlsVisible["event_Cost"] = $this->event_Cost->Visible;
            $this->ControlsVisible["PanelURL"] = $this->PanelURL->Visible;
            $this->ControlsVisible["LabelURL"] = $this->LabelURL->Visible;
            $this->ControlsVisible["event_URL"] = $this->event_URL->Visible;
            $this->ControlsVisible["PanelTextBox1"] = $this->PanelTextBox1->Visible;
            $this->ControlsVisible["LabelTextBox1"] = $this->LabelTextBox1->Visible;
            $this->ControlsVisible["event_TextBox1"] = $this->event_TextBox1->Visible;
            $this->ControlsVisible["PanelTextBox2"] = $this->PanelTextBox2->Visible;
            $this->ControlsVisible["LabelTextBox2"] = $this->LabelTextBox2->Visible;
            $this->ControlsVisible["event_TextBox2"] = $this->event_TextBox2->Visible;
            $this->ControlsVisible["PanelTextBox3"] = $this->PanelTextBox3->Visible;
            $this->ControlsVisible["LabelTextBox3"] = $this->LabelTextBox3->Visible;
            $this->ControlsVisible["event_TextBox3"] = $this->event_TextBox3->Visible;
            $this->ControlsVisible["PanelTextArea1"] = $this->PanelTextArea1->Visible;
            $this->ControlsVisible["LabelTextArea1"] = $this->LabelTextArea1->Visible;
            $this->ControlsVisible["event_TextArea1"] = $this->event_TextArea1->Visible;
            $this->ControlsVisible["PanelTextArea2"] = $this->PanelTextArea2->Visible;
            $this->ControlsVisible["LabelTextArea2"] = $this->LabelTextArea2->Visible;
            $this->ControlsVisible["event_TextArea2"] = $this->event_TextArea2->Visible;
            $this->ControlsVisible["PanelTextArea3"] = $this->PanelTextArea3->Visible;
            $this->ControlsVisible["LabelTextArea3"] = $this->LabelTextArea3->Visible;
            $this->ControlsVisible["event_TextArea3"] = $this->event_TextArea3->Visible;
            $this->ControlsVisible["PanelCheckBox1"] = $this->PanelCheckBox1->Visible;
            $this->ControlsVisible["LabelCheckBox1"] = $this->LabelCheckBox1->Visible;
            $this->ControlsVisible["event_CheckBox1"] = $this->event_CheckBox1->Visible;
            $this->ControlsVisible["PanelCheckBox2"] = $this->PanelCheckBox2->Visible;
            $this->ControlsVisible["LabelCheckBox2"] = $this->LabelCheckBox2->Visible;
            $this->ControlsVisible["event_CheckBox2"] = $this->event_CheckBox2->Visible;
            $this->ControlsVisible["PanelCheckBox3"] = $this->PanelCheckBox3->Visible;
            $this->ControlsVisible["LabelCheckBox3"] = $this->LabelCheckBox3->Visible;
            $this->ControlsVisible["event_CheckBox3"] = $this->event_CheckBox3->Visible;
            do {
                $this->RowNumber++;
                $this->DataSource->SetValues();
                $Tpl->block_path = $ParentPath . "/" . $GridBlock . "/Row";
                $this->event_date->SetValue($this->DataSource->event_date->GetValue());
                $this->event_time->SetValue($this->DataSource->event_time->GetValue());
                $this->event_time_end->SetValue($this->DataSource->event_time_end->GetValue());
                $this->category_id->SetValue($this->DataSource->category_id->GetValue());
                $this->user_id->SetValue($this->DataSource->user_id->GetValue());
                $this->event_desc->SetValue($this->DataSource->event_desc->GetValue());
                $this->event_Location->SetValue($this->DataSource->event_Location->GetValue());
                $this->event_Cost->SetValue($this->DataSource->event_Cost->GetValue());
                $this->event_URL->SetValue($this->DataSource->event_URL->GetValue());
                $this->event_URL->Page = $this->DataSource->f("event_url");
                $this->event_TextBox1->SetValue($this->DataSource->event_TextBox1->GetValue());
                $this->event_TextBox2->SetValue($this->DataSource->event_TextBox2->GetValue());
                $this->event_TextBox3->SetValue($this->DataSource->event_TextBox3->GetValue());
                $this->event_TextArea1->SetValue($this->DataSource->event_TextArea1->GetValue());
                $this->event_TextArea2->SetValue($this->DataSource->event_TextArea2->GetValue());
                $this->event_TextArea3->SetValue($this->DataSource->event_TextArea3->GetValue());
                $this->event_CheckBox1->SetValue($this->DataSource->event_CheckBox1->GetValue());
                $this->event_CheckBox2->SetValue($this->DataSource->event_CheckBox2->GetValue());
                $this->event_CheckBox3->SetValue($this->DataSource->event_CheckBox3->GetValue());
                $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeShowRow", $this);
                $this->event_date->Show();
                $this->event_time->Show();
                $this->event_time_end->Show();
                $this->category_id->Show();
                $this->user_id->Show();
                $this->event_desc->Show();
                $this->PanelLocation->Show();
                $this->PanelCost->Show();
                $this->PanelURL->Show();
                $this->PanelTextBox1->Show();
                $this->PanelTextBox2->Show();
                $this->PanelTextBox3->Show();
                $this->PanelTextArea1->Show();
                $this->PanelTextArea2->Show();
                $this->PanelTextArea3->Show();
                $this->PanelCheckBox1->Show();
                $this->PanelCheckBox2->Show();
                $this->PanelCheckBox3->Show();
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
        $this->event_title->SetValue($this->DataSource->event_title->GetValue());
        $this->event_title->Show();
        $this->edit->Show();
        $this->edit_event->Show();
        $Tpl->parse();
        $Tpl->block_path = $ParentPath;
        $this->DataSource->close();
    }
//End Show Method

//GetErrors Method @5-71EE8BC5
    function GetErrors()
    {
        $errors = "";
        $errors = ComposeStrings($errors, $this->event_date->Errors->ToString());
        $errors = ComposeStrings($errors, $this->event_time->Errors->ToString());
        $errors = ComposeStrings($errors, $this->event_time_end->Errors->ToString());
        $errors = ComposeStrings($errors, $this->category_id->Errors->ToString());
        $errors = ComposeStrings($errors, $this->user_id->Errors->ToString());
        $errors = ComposeStrings($errors, $this->event_desc->Errors->ToString());
        $errors = ComposeStrings($errors, $this->LabelLocation->Errors->ToString());
        $errors = ComposeStrings($errors, $this->event_Location->Errors->ToString());
        $errors = ComposeStrings($errors, $this->LabelCost->Errors->ToString());
        $errors = ComposeStrings($errors, $this->event_Cost->Errors->ToString());
        $errors = ComposeStrings($errors, $this->LabelURL->Errors->ToString());
        $errors = ComposeStrings($errors, $this->event_URL->Errors->ToString());
        $errors = ComposeStrings($errors, $this->LabelTextBox1->Errors->ToString());
        $errors = ComposeStrings($errors, $this->event_TextBox1->Errors->ToString());
        $errors = ComposeStrings($errors, $this->LabelTextBox2->Errors->ToString());
        $errors = ComposeStrings($errors, $this->event_TextBox2->Errors->ToString());
        $errors = ComposeStrings($errors, $this->LabelTextBox3->Errors->ToString());
        $errors = ComposeStrings($errors, $this->event_TextBox3->Errors->ToString());
        $errors = ComposeStrings($errors, $this->LabelTextArea1->Errors->ToString());
        $errors = ComposeStrings($errors, $this->event_TextArea1->Errors->ToString());
        $errors = ComposeStrings($errors, $this->LabelTextArea2->Errors->ToString());
        $errors = ComposeStrings($errors, $this->event_TextArea2->Errors->ToString());
        $errors = ComposeStrings($errors, $this->LabelTextArea3->Errors->ToString());
        $errors = ComposeStrings($errors, $this->event_TextArea3->Errors->ToString());
        $errors = ComposeStrings($errors, $this->LabelCheckBox1->Errors->ToString());
        $errors = ComposeStrings($errors, $this->event_CheckBox1->Errors->ToString());
        $errors = ComposeStrings($errors, $this->LabelCheckBox2->Errors->ToString());
        $errors = ComposeStrings($errors, $this->event_CheckBox2->Errors->ToString());
        $errors = ComposeStrings($errors, $this->LabelCheckBox3->Errors->ToString());
        $errors = ComposeStrings($errors, $this->event_CheckBox3->Errors->ToString());
        $errors = ComposeStrings($errors, $this->Errors->ToString());
        $errors = ComposeStrings($errors, $this->DataSource->Errors->ToString());
        return $errors;
    }
//End GetErrors Method

} //End eventGrid Class @5-FCB6E20C

class clseventGridDataSource extends clsDBcalendar {  //eventGridDataSource Class @5-3C6984DC

//DataSource Variables @5-F263C099
    var $Parent = "";
    var $CCSEvents = "";
    var $CCSEventResult;
    var $ErrorBlock;
    var $CmdExecution;

    var $CountSQL;
    var $wp;


    // Datasource fields
    var $event_title;
    var $event_date;
    var $event_time;
    var $event_time_end;
    var $category_id;
    var $user_id;
    var $event_desc;
    var $event_Location;
    var $event_Cost;
    var $event_URL;
    var $event_TextBox1;
    var $event_TextBox2;
    var $event_TextBox3;
    var $event_TextArea1;
    var $event_TextArea2;
    var $event_TextArea3;
    var $event_CheckBox1;
    var $event_CheckBox2;
    var $event_CheckBox3;
//End DataSource Variables

//DataSourceClass_Initialize Event @5-9021CE3F
    function clseventGridDataSource(& $Parent)
    {
        $this->Parent = & $Parent;
        $this->ErrorBlock = "Grid eventGrid";
        $this->Initialize();
        $this->event_title = new clsField("event_title", ccsText, "");
        $this->event_date = new clsField("event_date", ccsDate, array("yyyy", "-", "mm", "-", "dd"));
        $this->event_time = new clsField("event_time", ccsDate, array("HH", ":", "nn", ":", "ss"));
        $this->event_time_end = new clsField("event_time_end", ccsDate, array("HH", ":", "nn", ":", "ss"));
        $this->category_id = new clsField("category_id", ccsText, "");
        $this->user_id = new clsField("user_id", ccsText, "");
        $this->event_desc = new clsField("event_desc", ccsMemo, "");
        $this->event_Location = new clsField("event_Location", ccsText, "");
        $this->event_Cost = new clsField("event_Cost", ccsText, "");
        $this->event_URL = new clsField("event_URL", ccsText, "");
        $this->event_TextBox1 = new clsField("event_TextBox1", ccsText, "");
        $this->event_TextBox2 = new clsField("event_TextBox2", ccsText, "");
        $this->event_TextBox3 = new clsField("event_TextBox3", ccsText, "");
        $this->event_TextArea1 = new clsField("event_TextArea1", ccsText, "");
        $this->event_TextArea2 = new clsField("event_TextArea2", ccsText, "");
        $this->event_TextArea3 = new clsField("event_TextArea3", ccsText, "");
        $this->event_CheckBox1 = new clsField("event_CheckBox1", ccsBoolean, array(1, 0, ""));
        $this->event_CheckBox2 = new clsField("event_CheckBox2", ccsBoolean, array(1, 0, ""));
        $this->event_CheckBox3 = new clsField("event_CheckBox3", ccsBoolean, array(1, 0, ""));

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

//Prepare Method @5-E3F340D5
    function Prepare()
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->wp = new clsSQLParameters($this->ErrorBlock);
        $this->wp->AddParameter("1", "urlevent_id", ccsInteger, "", "", $this->Parameters["urlevent_id"], "", true);
        $this->wp->AddParameter("2", "seslocale", ccsText, "", "", $this->Parameters["seslocale"], "", false);
        $this->wp->AddParameter("3", "urlevents_category_id", ccsInteger, "", "", $this->Parameters["urlevents_category_id"], "", true);
        $this->wp->Criterion[1] = $this->wp->Operation(opEqual, "events.event_id", $this->wp->GetDBValue("1"), $this->ToSQL($this->wp->GetDBValue("1"), ccsInteger),true);
        $this->wp->Criterion[2] = $this->wp->Operation(opEqual, "categories_langs.language_id", $this->wp->GetDBValue("2"), $this->ToSQL($this->wp->GetDBValue("2"), ccsText),false);
        $this->wp->Criterion[3] = $this->wp->Operation(opIsNull, "events.category_id", $this->wp->GetDBValue("3"), $this->ToSQL($this->wp->GetDBValue("3"), ccsInteger),true);
        $this->Where = $this->wp->opAND(
             false, 
             $this->wp->Criterion[1], $this->wp->opOR(
             true, 
             $this->wp->Criterion[2], 
             $this->wp->Criterion[3]));
    }
//End Prepare Method

//Open Method @5-6CFDBA91
    function Open()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeBuildSelect", $this->Parent);
        $this->CountSQL = "SELECT COUNT(*)\n\n" .
        "FROM (events LEFT JOIN users ON\n\n" .
        "events.user_id = users.user_id) LEFT JOIN categories_langs ON\n\n" .
        "events.category_id = categories_langs.category_id";
        $this->SQL = "SELECT events.*, user_login, user_last_name, user_first_name, category_name \n\n" .
        "FROM (events LEFT JOIN users ON\n\n" .
        "events.user_id = users.user_id) LEFT JOIN categories_langs ON\n\n" .
        "events.category_id = categories_langs.category_id {SQL_Where} {SQL_OrderBy}";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeExecuteSelect", $this->Parent);
        if ($this->CountSQL) 
            $this->RecordsCount = CCGetDBValue(CCBuildSQL($this->CountSQL, $this->Where, ""), $this);
        else
            $this->RecordsCount = "CCS not counted";
        $this->query($this->OptimizeSQL(CCBuildSQL($this->SQL, $this->Where, $this->Order)));
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterExecuteSelect", $this->Parent);
    }
//End Open Method

//SetValues Method @5-D780A013
    function SetValues()
    {
        $this->event_title->SetDBValue($this->f("event_title"));
        $this->event_date->SetDBValue(trim($this->f("event_date")));
        $this->event_time->SetDBValue(trim($this->f("event_time")));
        $this->event_time_end->SetDBValue(trim($this->f("event_time_end")));
        $this->category_id->SetDBValue($this->f("category_name"));
        $this->user_id->SetDBValue($this->f("user_login"));
        $this->event_desc->SetDBValue($this->f("event_desc"));
        $this->event_Location->SetDBValue($this->f("event_location"));
        $this->event_Cost->SetDBValue($this->f("event_cost"));
        $this->event_URL->SetDBValue($this->f("event_url"));
        $this->event_TextBox1->SetDBValue($this->f("custom_TextBox1"));
        $this->event_TextBox2->SetDBValue($this->f("custom_TextBox2"));
        $this->event_TextBox3->SetDBValue($this->f("custom_TextBox3"));
        $this->event_TextArea1->SetDBValue($this->f("custom_TextArea1"));
        $this->event_TextArea2->SetDBValue($this->f("custom_TextArea2"));
        $this->event_TextArea3->SetDBValue($this->f("custom_TextArea3"));
        $this->event_CheckBox1->SetDBValue(trim($this->f("custom_CheckBox1")));
        $this->event_CheckBox2->SetDBValue(trim($this->f("custom_CheckBox2")));
        $this->event_CheckBox3->SetDBValue(trim($this->f("custom_CheckBox3")));
    }
//End SetValues Method

} //End eventGridDataSource Class @5-FCB6E20C



//Include Page implementation @3-EBA5EA16
include_once(RelativePath . "/footer.php");
//End Include Page implementation

//Initialize Page @1-80A48E1C
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
$TemplateFileName = "event_view.html";
$BlockToParse = "main";
$TemplateEncoding = "UTF-8";
$PathToRoot = "./";
//End Initialize Page

//Include events file @1-B6CC3771
include("./event_view_events.php");
//End Include events file

//Initialize Objects @1-89FCFA7E
$DBcalendar = new clsDBcalendar();
$MainPage->Connections["calendar"] = & $DBcalendar;

// Controls
$header = & new clsheader("", "header", $MainPage);
$header->Initialize();
$vertical_menu = & new clsvertical_menu("", "vertical_menu", $MainPage);
$vertical_menu->Initialize();
$eventGrid = & new clsGrideventGrid("", $MainPage);
$footer = & new clsfooter("", "footer", $MainPage);
$footer->Initialize();
$MainPage->header = & $header;
$MainPage->vertical_menu = & $vertical_menu;
$MainPage->eventGrid = & $eventGrid;
$MainPage->footer = & $footer;
$eventGrid->Initialize();

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

//Execute Components @1-F751105C
$header->Operations();
$vertical_menu->Operations();
$footer->Operations();
//End Execute Components

//Go to destination page @1-6459D7FE
if($Redirect)
{
    $CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
    $DBcalendar->close();
    header("Location: " . $Redirect);
    $header->Class_Terminate();
    unset($header);
    $vertical_menu->Class_Terminate();
    unset($vertical_menu);
    unset($eventGrid);
    $footer->Class_Terminate();
    unset($footer);
    unset($Tpl);
    exit;
}
//End Go to destination page

//Show Page @1-B0AD4C6B
$header->Show();
$vertical_menu->Show();
$eventGrid->Show();
$footer->Show();
$Tpl->block_path = "";
$Tpl->Parse($BlockToParse, false);
$main_block = $Tpl->GetVar($BlockToParse);
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeOutput", $MainPage);
if ($CCSEventResult) echo $main_block;
//End Show Page

//Unload Page @1-BDEF98AB
$CCSEventResult = CCGetEvent($CCSEvents, "BeforeUnload", $MainPage);
$DBcalendar->close();
$header->Class_Terminate();
unset($header);
$vertical_menu->Class_Terminate();
unset($vertical_menu);
unset($eventGrid);
$footer->Class_Terminate();
unset($footer);
unset($Tpl);
//End Unload Page


?>
