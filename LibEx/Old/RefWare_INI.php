<?php
/*------------------------------------
Simple Site Creator PHP script

Copyright (C) 2006-2009 Volodymyr Vons, VladVons@mail.ru
Copyright (C) 2009 JDV-Soft Inc, http://www.jdv-soft.com

Donations: http://jdv-soft.com?PN=Donation.php
Support:   http://jdv-soft.com?PN=ProjSimpleSiteCreator

This program is free software and distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or 
FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.

You can redistribute it and/or modify it under the terms of the GNU General Public License 
as published by the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
------------------------------------*/

require_once(_DirCommonLibEx . "/LangTXT.php");


class TPathEx extends TPath
/////////////////////////////////////////////////////
{
 protected $TLang1, $CurrG, $CurrI, $CurrITok, $Language;


 function __construct($aPath, TLang $aTLang)
 {
  parent::__construct($aPath);
  $this->TLang1   = $aTLang;
  $this->Language = $aTLang->GetLanguage();
 }

 
 function ArrayToLinks($aTArray, $aLink)
 {
  $TArray2 = new TArray();
  $aTArray->Reset();
  while (list($Label, $Value) = $aTArray->Each()) {
     $TArray2->AddItem($Value, $aLink . "&Dir=$this->Path" . "&Group=" . urlencode($Value));
  }

  $THref1 = new THref();
  $THref1->SetSeparator("\n");
  $THref1->Build($TArray2);
  return $THref1->GetPrintOut();
 }


 function GetThumb($aFileIn, $aSize = 140)
 {
  $FileOut = _DirThumb . "/" . TStr::ExpandR($aFileIn, ".", "_" . $aSize);
  GetThumb($aFileIn, $FileOut, $aSize);
  return $FileOut;
 }


 function GetItem($aItem)
 {
  return $this->GetItemFromSection($aItem, $this->Language);
 }

}



class TShopINI extends TPathEx
/////////////////////////////////////////////////////
{
 protected $AXlat, $CurGroup;

 function __construct($aPath, $aTLang, $aAXlat)
 {
  parent::__construct($aPath, $aTLang);
  $this->ParseFile("$aPath/Description_$this->Language.txt");
  $this->AXlat = $aAXlat;
 }


 function RootToLinks($aLink)
 {
  $TArray2 = new TArray();
  $TArray1 = $this->TIniFileEx1->GetSections();
  while (list($Label, $Value) = $TArray1->Each()) {
       $TArray2->AddItem($Label, $Value);
  }

  return $this->ArrayToLinks($TArray2->SortByValue(), $aLink);
 }


 function Filter($aIdx)
 {
  $Array1 = array();
  $Array1[0] = new TArray();
  $Array1[1] = new TToken();
  $Array1[2] = $aIdx;
  $Array1[3] = "";
  $Array1[4] = $this;
  $this->TIniFileEx1->Walk(CallBack_TDirInfo_Filter1, $Array1);
  return $Array1[0];
 }


 function SetCurGroup($aValue)
 {
  $this->CurrG = $aValue;
 }


 function SetCurItem($aValue)
 {
  $this->CurrI = $aValue;
  $String1 = $this->TIniFileEx1->GetItemFromSection($this->CurrI, $this->CurrG);
  $this->CurrITok = new TToken();
  $this->CurrITok->LoadFromString($String1);
 }


 function GetItemInfoByIdx($aIdx)
 {
  return $this->CurrITok->GetItem($aIdx);
 }


 function GetXlatValue($aValue)
 {
  if ($this->AXlat->Search($aValue) !== false) {
     $Idx = $this->AXlat->GetItem($aValue);
     return $this->GetItemInfoByIdx($Idx);
  }else{
     throw new MyException("Item not found: $aValue", 1);
  }
 }


 function GetItemInfo($aValue)
 {
  if ($aValue == "Name") {
     return $this->CurrI;

  }elseif ($aValue == "Images") {
     $Image = $this->GetXlatValue("Image");
     $Path = "$this->Path/Images/$Image";
     $Dir1 = new TDir(TFS::GetDirName($Path));
     if ($Image != "" && TFS::FileExists($Path)) { 
        $Dir1->GetFiles(false, 1, TFS::GetFileName($Path, ".jpg"));
     }
     return $Dir1->TArrData;

  }elseif ($aValue == "Thumb"){
     $Image = $this->GetXlatValue("Image");
     $Path  = "$this->Path/Images/$Image";
     if ($Image != "" && TFS::FileExists($Path)) {
        return $this->GetThumb($Path);
     }else{
        return $this->TLang1->GetItem("Sys_Path_NoFoto");
     }

  }else{
     return $this->GetXlatValue($aValue);
  }
 }


 function GetGroupInfo($aValue)
 {
  if ($aValue == "Name") {
     return $this->CurrG;
  }elseif ($aValue == "Items") {
     return $this->TIniFileEx1->GetItems($this->CurrG);
  }elseif ($aValue == "Thumb") {
     return $this->TLang1->GetItem("Sys_Path_NoFotoGroup");
  }else{
     Error("GetGroupInfo(). Item not found: " . $aValue);
  }
 }


 function ShowItemInfo($aItem)
 {
  Show($this->GetItemInfo($aItem));
 }


 function ShowGroupInfo($aItem)
 {
  Show($this->GetGroupInfo($aItem));
 }
}

?>