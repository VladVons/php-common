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

 require_once(_DirCommonLib . "/Paging.php");

 global $gTLang, $gTRefWare;

 $gTLang->AddFiles("PHP", $InclName);

 $aShopView   = $aGet->GetItem("Style", _ShopView);
 $aShopHeight = $aGet->GetItem("Items", _ShopHeight);
 $aShopWitdh  = $aGet->GetItem("Width", _ShopWidth); 

 if ($aAction == "Search") {
     $TArray1 = $gTRefWare->FilterItems($aGet->GetItem("Field"), $aItem);
 }else{
    $TArray1 = $gTRefWare->GetGroupItems($aGroup);
 }

 $Items = $TArray1->GetCount();
 if ($Items == 0) {
    $gTLang->ShowItem("NotFound");
	Die();
 }

 $TabWidth  = $aShopWitdh;
 $TabHeight = min($aShopHeight, $Items / $TabWidth);

 $Paging1 = new TPaging($aGet->ArrData, $TabWidth * $TabHeight);
 $Paging1->Build($Items);
 $TArray1 = $TArray1->Slice($Paging1->GetItemStart(), $Paging1->GetItemLength());
 $Pages = sprintf("%s %s | %s %s", $gTLang->GetItem("Pages"), $Paging1->GetPrintOut(), $gTLang->GetItem("Found"), $Items);
 
 $gTRefWare->SetCurItem($aGroup);
 $GroupName = ($aGroup != "" ? $gTRefWare->GetRecord()->GetField("Name") : $aGroup);
?>

<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr align="left" valign="top">
    <td width="7%" rowspan="2"><img src="<?php $gTRefWare->GetRecord()->GetField("Thumb"); ?>" width="64" height="48" border="0"></td>
    <td width="2%" rowspan="2">&nbsp;</td>
    <th width="91%" height="24"><?php printf("<h1>%s</h1>", $GroupName); ?></th>
  </tr>
  <tr align="left" valign="top">
    <td height="18">&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr align="left" valign="top">
    <td colspan="3" class="TableColor_Title"><?php Show($Pages); ?></td>
  <tr align="left" valign="top">
    <td colspan="3">&nbsp;</td>
  <tr align="left" valign="top">
    <td colspan="3"><?php
	 $KeyWords = $GroupName;
	 $TArray1->Reset();
     print("\n<TABLE>\n");
     for ($Y = 0; $Y < $TabHeight; $Y++) {
         print(" <TR align='center' valign='middle'>\n");
         for ($X = 0; $X < $TabWidth; $X++) {
 			 if (! list($Label, $Value) = $TArray1->Each()) break;

			 $gTRefWare->SetCurItem($Value);
			 $KeyWords = $KeyWords . "," . $gTRefWare->GetRecord()->GetField("Name");
             print("  <TD>\n");
			 $IncludeFile = _DirCommonForm . "/" . $aShopView . ".php";
             include $IncludeFile;
			 print("  </TD>\n");
         }
         print(" </TR>\n");
     }
     print("</TABLE>\n");
    ?></td>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td colspan="3" class="TableColor_Title"><?php Show($Pages); ?></td>
  </tr>
</table>
