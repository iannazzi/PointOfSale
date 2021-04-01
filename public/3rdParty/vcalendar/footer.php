<?php
class clsfooter { //footer class @1-1DA8A4E8

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

//Class_Initialize Event @1-CBB21137
    function clsfooter($RelativePath, $ComponentName, & $Parent)
    {
        global $CCSLocales;
        global $DefaultDateFormat;
        $this->ComponentName = $ComponentName;
        $this->RelativePath = $RelativePath;
        $this->Visible = true;
        $this->Parent = & $Parent;
        $this->FileName = "footer.php";
        $this->Redirect = "";
        $this->TemplateFileName = "footer.html";
        $this->BlockToParse = "main";
        $this->TemplateEncoding = "UTF-8";
    }
//End Class_Initialize Event

//Class_Terminate Event @1-32FD4740
    function Class_Terminate()
    {
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "BeforeUnload", $this);
    }
//End Class_Terminate Event

//BindEvents Method @1-A22F108F
    function BindEvents()
    {
        $this->html_footer->CCSEvents["BeforeShow"] = "footer_html_footer_BeforeShow";
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "AfterInitialize", $this);
    }
//End BindEvents Method

//Operations Method @1-7E2A14CF
    function Operations()
    {
        global $Redirect;
        if(!$this->Visible)
            return "";
    }
//End Operations Method

//Initialize Method @1-A1B06AB9
    function Initialize()
    {
        global $FileName;
        global $CCSLocales;
        if(!$this->Visible)
            return "";

        // Create Components
        $this->html_footer = & new clsControl(ccsLabel, "html_footer", "html_footer", ccsText, "", CCGetRequestParam("html_footer", ccsGet, NULL), $this);
        $this->html_footer->HTML = true;
        $this->BindEvents();
        $this->CCSEventResult = CCGetEvent($this->CCSEvents, "OnInitializeView", $this);
    }
//End Initialize Method

//Show Method @1-D132903F
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
        $this->html_footer->Show();
        $Tpl->Parse();
        $Tpl->block_path = $block_path;
        $Tpl->SetVar($this->ComponentName, $Tpl->GetVar($this->ComponentName));
    }
//End Show Method

} //End footer Class @1-FCB6E20C

//Include Event File @1-7A04916F
include(RelativePath . "/footer_events.php");
//End Include Event File


?>
