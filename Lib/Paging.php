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

require_once("Control.php");

class TPaging
{
 protected $THref1, $TArrayForm, $StartPage, $ItemsPerPage, $PagesPerGroup, $StartPageName, $Items;

 function __construct($aForm, $aItemsPerPage = 10, $aPagesPerGroup = 10, $aSign = "StartPage")
 {
  $this->THref1 = new THref();
  $this->THref1->SetSeparator(" ");

  $this->ItemsPerPage  = $aItemsPerPage;
  $this->PagesPerGroup = $aPagesPerGroup;

  $this->StartPageName = $aSign;

  $this->TArrayForm = new TArray($aForm);
  $StartPage = $this->TArrayForm->GetItem($this->StartPageName);
  $this->StartPage  = isset($StartPage) ? $StartPage : 0;
 }


 function GetItemStart()
 {
  return $this->StartPage * $this->ItemsPerPage;
 }


 function GetItemEnd()
 {
  $EndItem = $this->GetItemStart() + $this->ItemsPerPage;
  return $EndItem > $this->Items ? $this->Items : $EndItem;
 }


 function GetItemLength()
 {
  return $this->GetItemEnd() - $this->GetItemStart();
 }


 function Build($aItems)
 {
  $this->Items = $aItems;
  if ($aItems <= $this->ItemsPerPage) {
	return 0;
  }
  
  $PageFound = $aItems / $this->ItemsPerPage;
  $TArray1 = new TArray();

  if ($this->StartPage > 0) {
      $PageSkip = $this->StartPage - $this->PagesPerGroup;
      $PageGoTo = $PageSkip > 0 ? $PageSkip : 0;
      $this->TArrayForm->SetItem($this->StartPageName, $PageGoTo);
      $TArray1->AddItem("<<-", "?" . $this->TArrayForm->ImplodeEx(TArray::cAll, "&", ""));
  }

  $PageEnd = $PageFound - $this->StartPage;
  for ($i = 0; $i < $this->PagesPerGroup && $i < $PageEnd; $i++) {
      $PageNo = $this->StartPage + $i + 1;
      $this->TArrayForm->SetItem($this->StartPageName, $PageNo - 1);
      if ($i == 0) {
	     $TArray1->AddItem("[ $PageNo ]", "");
      }else{
         $TArray1->AddItem("[ $PageNo ]", "?" . $this->TArrayForm->ImplodeEx(TArray::cAll, "&", ""));
      }
  }

  if ($PageFound > $this->PagesPerGroup) {
      $this->TArrayForm->SetItem($this->StartPageName, $this->StartPage + 1);
      $TArray1->AddItem("->", "?" . $this->TArrayForm->ImplodeEx(TArray::cAll, "&", ""));
  }

  $this->THref1->Build($TArray1);
  return $TArray1->GetCount();
 }


 function PrintOut()
 {
  $this->THref1->PrintOut();
 }
 
 
 function GetPrintOut()
 {
  return $this->THref1->GetPrintOut();
 }
 
}

?>