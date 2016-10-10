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

<table width="100%"  border="1" cellpadding="0" cellspacing="0" bordercolor="#CCCCCC">
  <tr>
    <td height="37" bordercolor="#000000"><table width="100%"  border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td width="24%" align="center" valign="middle"><table width="155" height="120"  border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td align="center" valign="middle"><a href="<?php Show("index.php?PN=$ItemInfoPage" . $Link1); ?>"><img src="<?php $WareItem->ShowField("Thumb"); ?>" alt="<?php $WareItem->GetField("Name"); ?>" border="0"></a></td>
          </tr>
        </table>
		</td>
        <td width="76%" valign="top">
		 <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td>
              <table width="100%"  border="0" cellspacing="1" cellpadding="0">
                <tr>
                  <td width="10%" height="18" align="left" class="TableColor_Title"><?php $WareItem->ShowField("ID"); ?></td>
                  <td width="62%" align="left" class="TableColor_Title"><?php $WareItem->ShowField("Name"); ?></td>
                  <td width="11%" align="center" class="TableColor_Title"><?php printf("%s %s", $Price, $gTLang->GetItem("MoneyAbr")); ?></td>
                  <td width="5%" align="right" class="TableColor_Title"><a href="<?php Show("index.php?PN=Order" . $Link1); ?>"><img src="<?php $gTLang->ShowItem("PageImg_Buy"); ?>" width="24" height="16" border="0"></a></td>
                </tr>
            </table></td>
          </tr>
          <tr>
            <td><?php Show($Details); ?></td>
          </tr>
        </table></td>
      </tr>
     </table>
	</td>
  </tr>
</table>
