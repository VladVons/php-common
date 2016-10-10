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

require_once(_DirCommonLibEx . "/FileXVL.php");
require_once(_DirCommonLibEx . "/Catalog.php");


class TCatalogXVL extends TCatalog
/////////////////////////////////////////////////////
{
 protected $FXVL1, $DBPath, $DBName, $CurrG, $CurrI;

 
 function __construct($aDBPath, $aDBName, $aTLang)
 {
  parent::__construct($aTLang);
  $this->FXVL1 = new TFileXVL();

  $this->DBPath = $aDBPath;
  $this->DBName = $aDBName;
  
  $FileName = $this->DBPath . "/" . $this->DBName . "_" . $this->Lang . ".xvl";
  $this->FXVL1->LoadFromFile($FileName);
 }
 


 protected function Locate($aValue, $aTok)
 {
  $Row = $this->FXVL1->Search(0, $aValue);
  if ($Row != -1) {
      $aTok->LoadFromString($this->FXVL1->GetTok()->TArrData->GetItem($Row));
  }
  return $Row;
 }

 
 function SetCurItem($aValue)
 {
  $this->CurrI = $aValue;
  return $this->Locate($aValue, $this->CurrITok);
 }

  
 function SetCurGroup($aValue)
 {
  $this->CurrG = $aValue;
  return $this->Locate($aValue, $this->CurrGTok);
 }


 function ParseItem($aValue)
 {
  return $this->CurrITok->LoadFromString($aValue);  
 }


 
 function GetGroupInfo($aValue)
 {
  if ($aValue == "Items") {
     return $this->FXVL1->GetItems($this->CurrG);
  }elseif ($aValue == "Thumb") {
     return $this->TLang1->GetItem("Sys_Path_NoFotoGroup");
  }else{
     return $this->GetXlatValue($aValue, $this->CurrGTok);
  }
 }

 
 
 function ShowGroupInfo($aValue)
 {
  Show($this->GetGroupInfo($aValue));
 }

 
 
 function ShowItemInfo($aItem)
 {
  Show($this->GetItemInfo($aItem));
 }


 function FilterItems($aFieldName, $aValue)
 {
  $TArray1  = new TArray();
  $TokRec   = new TToken($this->RecDelim);
  
  $TokValue = new TToken(" ");
  $TokValue->LoadFromString(TStr::ToLower($aValue));
  $TokValue->Get()->DeleteEmpty();
  
  $FieldIdx = $this->AXlat->GetItem($aFieldName);
  for ($i = 0 ; $i < $this->Rows; $i++) {
      $Line1 = $this->TToken1->TArrData->GetItem($i);
	  if (TStr::Pos($Line1, "{") == -1) {
	      $TokRec->LoadFromString($Line1);
	      $Field = TStr::ToLower($TokRec->TArrData->GetItem($FieldIdx));
		  if ($TokValue->Get()->SearchStringAND($Field)) {
		     $TArray1->AddItemToEnd($Line1);
		  } 
	  }
  }  
  return $TArray1;
 }

 
 protected function TreeNodeRecurs($aParent, $aDepth, &$aTArray, &$aFormatJS)
 {
  while ($this->FXVL1->CurRow++ < $this->FXVL1->Rows) {
    $Line1 = $this->FXVL1->GetTok()->Get()->GetItem($this->FXVL1->CurRow);
	if (TStr::Pos($Line1, "{") != -1) {
	   $Token1 = new TToken($this->RecDelim);
       $Token1->LoadFromString($Line1);
	   $ID = $this->GetXlatValue($this->FieldID, $Token1);
       $Name = $this->GetXlatValue("Name", $Token1);	   

       $Parent = 0;
	   if ($aDepth > 0) {
          $Line2 = $this->FXVL1->GetTok()->TArrData->GetItem($aParent);
	      $Token1 = new TToken($this->RecDelim);
          $Token1->LoadFromString($Line2);
	      $Parent = $this->GetXlatValue($this->FieldID, $Token1);
	   }  
       $aTArray->AddItemToEnd(sprintf($aFormatJS, $ID, $Parent, $Name, $ID));	   

	   $this->TreeNodeRecurs($this->FXVL1->CurRow, $aDepth + 1, $aTArray, $aFormatJS);
	}elseif (TStr::Pos($Line1, "}") != -1) {
		return;
	}
  }
 }

 
 function TreeNode($aTArray, $aFormatJS)
 {
  $this->FXVL1->CurRow = -1;
  $this->TreeNodeRecurs(0, 0, $aTArray, $aFormatJS);
 }
 
}
?>