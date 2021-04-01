<?php

//Include Page implementation @10-D3FCB384
include_once(RelativePath . "/vertical_menu.php");
//End Include Page implementation

class clsprofile_menu { //profile_menu class @1-08ED3E4F

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

//Class_Initialize Event @1-6A0F233B
    function clsprofile_menu($RelativePath, $ComponentName, & $Parent)
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->ComponentName = $ComponentName;
        $this->RelativePath = $RelativePath;
        $this->Visible = true;
        $this->Parent = & $Parent;
        $this->Visible = (CCSecurityAccessCheck("10") == "success");
        $this->FileName = "profile_menu.php";
        $this->Redirect = "";
        $this->TemplateFileName = "profile_menu.html";
        $this->BlockToParse = "main";
        $this->TemplateEncoding = "UTF-8";
    }
//End Class_Initialize Event

//Class_Terminate Event @1-2BE74C5F
    function Class_Terminate()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeUnload", $this);
        $this->vertical_menu->Class_Terminate();
        unset($this->vertical_menu);
    }
//End Class_Terminate Event

//BindEvents Method @1-77CB969E
    function BindEvents()
    {
        $this->my_events->CCSEvents["BeforeShow"] = "profile_menu_my_events_BeforeShow";
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

//Initialize Method @1-94B8317B
    function Initialize()
    {
        global $FileName;
        global $CCSLocales;
        if(!$this->Visible)
            return "";

        // Create Components
        $this->profile_main = & new clsControl(ccsLink, "profile_main", "profile_main", ccsText, "", CCGetRequestParam("profile_main", ccsGet, NULL), $this);
        $this->profile_main->Page = $this->RelativePath . "profile.php";
        $this->profile_chpass = & new clsControl(ccsLink, "profile_chpass", "profile_chpass", ccsText, "", CCGetRequestParam("profile_chpass", ccsGet, NULL), $this);
        $this->profile_chpass->Page = $this->RelativePath . "change_password.php";
        $this->my_events = & new clsControl(ccsLink, "my_events", "my_events", ccsText, "", CCGetRequestParam("my_events", ccsGet, NULL), $this);
        $this->my_events->Page = $this->RelativePath . "profile_events.php";
        $this->vertical_menu = & new clsvertical_menu($this->RelativePath, "vertical_menu", $this);
        $this->vertical_menu->Initialize();
        $this->BindEvents();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnInitializeView", $this);
    }
//End Initialize Method

//Show Method @1-ED7E9448
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
        $this->vertical_menu->Show();
        $this->profile_main->Show();
        $this->profile_chpass->Show();
        $this->my_events->Show();
        $Tpl->Parse();
        $Tpl->block_path = $block_path;
        $Tpl->SetVar($this->ComponentName, $Tpl->GetVar($this->ComponentName));
    }
//End Show Method

} //End profile_menu Class @1-FCB6E20C

//Include Event File @1-847208EF
include(RelativePath . "/profile_menu_events.php");
//End Include Event File


?>
