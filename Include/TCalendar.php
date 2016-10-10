<?php
/*------------------------------------.
Simple Site Creator PHP script.
.
Copyright (C) 2006-2009 Volodymyr Vons, VladVons@mail.ru.
Copyright (C) 2009 JDV-Soft Inc, http://www.jdv-soft.com.
.
Donations: http://jdv-soft.com?PN=Donation.php.
Support:   http://jdv-soft.com?PN=ProjSimpleSiteCreator.
.
This program is free software and distributed in the hope that it will be useful,.
but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or .
FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details..
.
You can redistribute it and/or modify it under the terms of the GNU General Public License .
as published by the Free Software Foundation; either version 2 of the License, or.
(at your option) any later version..
------------------------------------*/

  //$aYear  = $this->GetItem("TCalendar_Year");
  //$aMonth = $this->GetItem("TCalendar_Month");
  
  $aParam = $this->CurParam;
  if ($aParam->GetCount() < 3) $aParam->AddItemToEnd(Date("Y")); 
  if ($aParam->GetCount() < 4) $aParam->AddItemToEnd(Date("m")); 
  if ($aParam->GetCount() < 5) $aParam->AddItemToEnd(""); 
  $aYear   = $this->CurParam->GetItem(2);
  $aMonth  = $this->CurParam->GetItem(3);
  $aEvents = $this->CurParam->GetItem(4);

  $FirstDay = mktime(0, 0, 0, $aMonth, 1, $aYear);
  $DOW = Date("N", $FirstDay);
  $Days = GetDaysInMonth($aYear, $aMonth);
  $ArrDOW = Array("Ïí","Âò","Ñð","×ò","Ïò","Ñá","Íä");
  $Arr1	 = new TArray();
  for ($i = 0; $i < 7; $i++) {
	$Str1 = sprintf("<TD %s>%s</TD>", ($i == 6 ? 'class="RedBold"' : ""), $ArrDOW[$i]);
	$Arr1->AddItem("D" . $i, $Str1);
  }
	
  for ($i = 1; $i < $DOW; $i++) {
		$Arr1->AddItem("F" . $i, "<TD>&nbsp;</TD>");
  }
  
  //print_r($this->);
  $Month = TStr::Pad($aMonth, 2, "0");
  for ($i = 1; $i <= $Days; $i++) {
	$Day = TStr::Pad($i, 2, "0");
	$FileName = "News/$aYear/$Month$Day.txt";
	if (TFS::FileExists($FileName)) {
		$Str1 = THTML::Href("index.php?PN=Calendar&Catalog=$FileName", $i, "", "");
	}else{
		$Str1 = $i;
	}
	$Arr1->AddItem("T" . $i, "  <TD>$Str1</TD>");
  }
  
  $Table = new TTable(7, 6, 'width="100%"');
  $Table->Build($Arr1, true);
?>

<table border="1" cellpadding="0" cellspacing="0" bgcolor="#CCCCCC">
  <tr>
	<td><?php print("$aYear, $aMonth"); ?></td>
  </tr>
  <tr>
	<td><?php $Table->PrintOut(); ?></td>
  </tr>
 </table>
