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

$WareItem = $gTRefWare->GetRecord();
$Link1   = "&Group=" . urlencode($aGroup) . "&Item=" . $WareItem->GetField("ID");

$Image = $WareItem->GetField("Image");
if (!empty($Image)) {
	$gTLang->AddFiles("Image", $gTRefWare->GetImageDir() . "/" . $Image);
}

$Price  = $WareItem->GetField("Price");
$PriceS = $WareItem->GetField("PriceS");
if ($PriceS != "") {
   $PriceS = "<s>$Price</s>&nbsp;<span class='RedBold'>$PriceS</span><br>";
}

$Details = $WareItem->GetField("Details");
if ($Details == $Value) {
   $Details = "";
}else{
   $Details = GetLongStringLink($Details, 4, $gTLang->GetItem("More"), "index.php?PN=$ItemInfoPage" . $Link1);
}
$Details = $PriceS . "<br>" . $Details;
$ClassType = ($i % 2 == 0 ? 'class="TableColor_Gray"' : ""); 
?>

<table width="180" height="320"  border="1" cellpadding="0" cellspacing="0" bordercolor="#CCCCCC">
  <tr>
    <td align="center" valign="top"><table width="100%" height="100%"  border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td width="24%" align="center" class="TableColor_Title"><?php printf("%s %s", $Price, $gTLang->GetItem("MoneyAbr")); ?></a></td>
      </tr>
      <tr>
        <td align="center"><?php printf("ID: %s", $WareItem->GetField("ID")); ?></td>
      </tr>
      <tr>
        <td align="center" valign="middle" class="TableColor_Title"><a href="<?php Show("index.php?PN=$ItemInfoPage" . $Link1); ?>"><img src="<?php $WareItem->ShowField("Thumb"); ?>" alt="<?php $WareItem->ShowField("Name"); ?>" border="0" /></a></td>
      </tr>
      <tr>
        <td align="left">&nbsp;</td>
      </tr>
      <tr>
        <td align="center"><?php $WareItem->ShowField("Name"); ?></td>
      </tr>
    </table></td>
  </tr>
</table>

