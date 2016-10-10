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

require_once(_DirCommonLibEx . "/ParseFile.php");
 
 
class TFileXVL extends tParseFile
/////////////////////////////////////////////////////
{
 public $Rows, $CurRow;
 protected $TToken1, $ArrayCB;

 
 function __construct()
 {
  parent::__construct();
  $this->TToken1  = new TToken("\n");
 }
 
 
 function GetTok()
 {
  return $this->TToken1;
 }
 
 
 function LoadFromFile($aFileName)
 {
  $this->CurFileName = $aFileName;
  $this->Rows = $this->TToken1->LoadFromFile($aFileName);
 }

 
 function Search($aRow, $aValue)
 {
  for ($i = $aRow ; $i < $this->Rows; $i++) {
      if (TStr::Pos($this->TToken1->TArrData->GetItem($i), $aValue) > 0) {
	      return $i;
	  }
  }  
  return -1;
 }


 function Filter($aRow, $aValue)
 {
  for ($i = 0 ; $i < $this->Rows; $i++) {
      if (TStr::Pos($this->TToken1->TArrData->GetItem($i), $aValue) > 0) {
	      return $i;
	  }
  }  
  return -1;
 }


 protected function GetItemsRecurs($aParent, $aDepth, &$aTArray)
 {
  while ($this->CurRow++ < $this->Rows) {
    $Line1 = TStr::Trim($this->TToken1->TArrData->GetItem($this->CurRow));
    if ($Line1 == "") continue;

	if (TStr::Pos($Line1, "{") != -1) {
	   $this->GetItemsRecurs($this->CurRow, $aDepth + 1, $aTArray);
	}elseif (TStr::Pos($Line1, "}") != -1) {
		return;
	}else{
	   $aTArray->AddItemToEnd($Line1);
	}
  }
 }
 
 
 function GetItems($aGroup)
 {
  $TArray1 = new TArray();
  
  if ($aGroup == "") {
	  $this->CurRow = -1;
      $this->GetItemsRecurs(0, 0, $TArray1);
  }else{
     $Row = $this->Search(0, $aGroup);
     if ($Row != -1) {
	    $this->CurRow = $Row;
        $this->GetItemsRecurs(0, 0, $TArray1);
     }
  }
 
  return $TArray1;
 }

}
?>
