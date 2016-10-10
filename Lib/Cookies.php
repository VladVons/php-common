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

class TCookies
/////////////////////////////////////////////////////
{
 function SetItem($aLabel, $aValue, $aDays = 30)
 {
  $Seconds = 60*60*24*$aDays;
  setcookie($aLabel, $aValue, time() + $Seconds, "/");
 }

 function SetArray($aArray, $aDays)
 {
  $aArray->Reset();
  while (list($Label, $Value) = $aArray->Each()) {
     $this->SetItem($Label, $Value, $aDays);
  }
 }

 function GetItem($aLabel)
 {
  return $_COOKIE[$aLabel];
 }

 function ShowItem($aLabel)
 {
  Show($this->GetItem($aLabel));
 }
}
?>
