<?php

//Sorter Class @0-379E4BB4

class clsSorter
{
  var $ComponentType = "Sorter";
  var $OrderDirection;
  var $OrderColumn;

  var $IsOn;
  var $IsAsc;
  var $TargetName;
  var $SorterName;
  var $FileName;
  var $Visible;

  var $CCSEvents;
  var $CCSEventResult;
  var $Parent;

  function clsSorter($ComponentName, $SorterName, $FileName, & $Parent)
  {
    $this->TargetName = $ComponentName;
    $this->SorterName = $SorterName;
    $this->FileName   = $FileName;
    $this->Visible    = true;
    $this->CCSEvents  = array();
    $this->Parent     = & $Parent;

    $this->OrderColumn = CCGetParam($this->TargetName . "Order", "");
    $this->OrderDirection = CCGetParam($this->TargetName . "Dir", "");
    $this->IsOn = ($this->OrderColumn == $this->SorterName);
    $this->IsAsc = (!strlen($this->OrderDirection) || $this->OrderDirection == "ASC");
  }


  function Show()
  {
    global $Tpl;
    $this->EventResult = CCGetEvent($this->CCSEvents, "BeforeShow", $this);

    if(!$this->Visible) return;

    $QueryString = CCGetQueryString("QueryString", Array($this->TargetName . "Page", "ccsForm"));
    $SorterBlock = "Sorter " . $this->SorterName;
    $AscOnPath = $SorterBlock . "/Asc_On";
    $AscOffPath = $SorterBlock . "/Asc_Off";
    $DescOnPath = $SorterBlock . "/Desc_On";
    $DescOffPath = $SorterBlock . "/Desc_Off";
    $QueryString = CCAddParam($QueryString, $this->TargetName . "Order", $this->SorterName);

    $AscOnExist = $Tpl->BlockExists($AscOnPath);
    $AscOffExist = $Tpl->BlockExists($AscOffPath);
    $DescOnExist = $Tpl->BlockExists($DescOnPath);
    $DescOffExist = $Tpl->BlockExists($DescOffPath);

    if($this->IsOn)
    {
      if($this->IsAsc) 
      {
        $this->OrderDirection = "DESC";
        if($AscOnExist) $Tpl->Parse($AscOnPath, false);
        if($AscOffExist) $Tpl->SetVar($AscOffPath, "");
        if($DescOnExist) $Tpl->SetVar($DescOnPath, "");
        if($DescOffExist)
        {
          $Tpl->SetVar("Desc_URL", $this->FileName . "?" . CCAddParam($QueryString, $this->TargetName . "Dir", "DESC"));
          $Tpl->Parse($DescOffPath, false);
        }
      }
      Else 
      {
        $this->OrderDirection = "ASC";
        if($AscOnExist) $Tpl->SetVar($AscOnPath, "");
        if($AscOffExist) 
        {
          $Tpl->SetVar("Asc_URL", $this->FileName . "?" . CCAddParam($QueryString, $this->TargetName . "Dir", "ASC"));
          $Tpl->Parse($AscOffPath, false);
        }
        if($DescOnExist) $Tpl->Parse($DescOnPath, false);
        if($DescOffExist) $Tpl->SetVar($DescOffPath, "");
      }
    }
    else
    {
      $this->OrderDirection = "ASC";
      if($AscOnExist) $Tpl->SetVar($AscOnPath, "");
      if($AscOffExist) 
      {
        $Tpl->SetVar("Asc_URL", $this->FileName . "?" . CCAddParam($QueryString, $this->TargetName . "Dir", "ASC"));
        $Tpl->Parse($AscOffPath, false);
      }
      if($DescOnExist) $Tpl->SetVar($DescOnPath, "");
      if($DescOffExist) 
      {
        $Tpl->SetVar("Desc_URL", $this->FileName . "?" . CCAddParam($QueryString, $this->TargetName . "Dir", "DESC"));
        $Tpl->Parse($DescOffPath, false);
      }
    }

    $QueryString = CCAddParam($QueryString, $this->TargetName . "Dir", $this->OrderDirection);
    $Tpl->SetVar("Sort_URL", $this->FileName . "?" . $QueryString);
    $Tpl->Parse($SorterBlock, false);
  }


}
//End Sorter Class


?>
