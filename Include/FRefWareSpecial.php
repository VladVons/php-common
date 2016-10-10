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
 
 global $gTLang, $gTRefWare;

 $ItemInfoPage = "RefWareItemInfo";

 $aRows		= $gTLang->GetItem("FSpecial_Rows", 1);
 $aCols		= $gTLang->GetItem("FSpecial_Cols", 3);
 $aScroll	= $gTLang->GetItem("FSpecial_Scroll", 3);

 $TArrSpecials = $gTRefWare->GetSpecials();
 if ($TArrSpecials->GetCount() == 0) {
	 return;
 }		  
 
 $TArray2 = new TArray();
 $TArrSpecials->Reset();
 $TArrSpecials = $TArrSpecials->Shuffle();
 while (list($Label, $Value) = $TArrSpecials->Each()) {
	$gTRefWare->SetCurItem($Value);
	$WareItem = $gTRefWare->GetRecord();

	$ID			= $WareItem->GetField("ID");
    $Name		= $WareItem->GetField("Name");
	$Thumb		= $WareItem->GetField("Thumb");
	$Price		= $WareItem->GetField("Price");
	$PriceS		= $WareItem->GetField("PriceS");
	$Category	= $WareItem->GetField("Category");
	$ImagePath	= $gTRefWare->GetImageDir() . "/" . $WareItem->GetField("Image");
	
	$gTLang->AddFiles("Image", $ImagePath);
	$String1 = "<a href=index.php?PN=$ItemInfoPage&Group=$Category&Item=$ID><img src=$Thumb border='1' title='$Name'></a><br><s>$Price</s><br><span class='RedBold'>$PriceS</span>";
    $TArray2->AddItem($Label, $String1);
	if ($TArray2->GetCount() >= $aRows * $aCols) {
		break;
	}
 }
 $Table1 = new TTable($aCols, $TArrSpecials->GetCount() / $aCols, 'width="100%" border="0"', 'align="center" valign="middle"');
 $Table1->Build($TArray2);
 $Specials = $Table1->GetPrintOut();
?>

<table width="100%"  border="0" cellpadding="0" cellspacing="0" bordercolor="#CCCCCC">
  <tr>
    <td align="center" valign="top" class="TableColor_Title"><?php $gTLang->ShowItem("Action"); ?></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="center" valign="top"><?php 
		if ($aScroll == 0) { 
			print($Specials);
		}else{
			printf('<marquee scrollamount="%d" scrolldelay="%d">%s</marquee>', $aScroll, $aScroll, $Specials);
		}	
		?></td>
  </tr>
</table>
