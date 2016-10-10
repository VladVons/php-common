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

require_once("Stream.php");

class TToken
/////////////////////////////////////////////////////
{
 protected $Delimiter;
 public $TArrData;


 function __construct($aValue = "\t")
 {
  $this->TArrData = new TArray();
  $this->Delimiter = $aValue;
 }


 function SetDelimiter($aValue)
 {
  $this->Delimiter = $aValue;
 }


 function LoadFromString($aValue)
 {
	$this->TArrData->Explode($aValue, $this->Delimiter);
	return $this->TArrData->GetCount();
 }

 
 function LoadFromStringPReg($aValue)
 {
	$this->TArrData->Split($aValue, $this->Delimiter);
	return $this->TArrData->GetCount();
 }


 function LoadFromFileText($aFileName)
 {
  if (!TFS::FileExists($aFileName)) throw new MyException("File not exists: '$aFileName'", 1);
  
  $this->TArrData = new TArray(file($aFileName));
  return $this->TArrData->GetCount();
 }
 
 
 function LoadFromFile($aFileName)
 {
  if (!TFS::FileExists($aFileName)) throw new MyException("File not exists: $aFileName", 1);
  
  $TFile1 = new TFile();
  $TFile1->Open($aFileName, "r");
  return $this->LoadFromString($TFile1->Read());
 }


 function SaveToString()
 {
  return $this->TArrData->Implode($this->Delimiter);
 }


 function GetItem($aIdx)
 {
  if ($this->TArrData->GetCount() > $aIdx) {
     return $this->TArrData->GetItem($aIdx);
  }else{
     throw new MyException("Index out of bound: $aIdx", 1);
  }
 }


 function SetItem($aIdx, $aValue)
 {
  if ($this->TArrData->GetCount() < $aIdx) {
     $this->TArrData->Pad($aIdx);
  }
  $this->TArrData->SetItem($aIdx, $aValue);
 }
}
?>
