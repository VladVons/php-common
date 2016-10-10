<?php
/*------------------------------------.
Simple Site Creator PHP script.

Copyright (C) 2006-2009 Volodymyr Vons, VladVons@mail.ru.
Copyright (C) 2009 JDV-Soft Inc, http://www.jdv-soft.com.

Donations: http://jdv-soft.com?PN=Donation.php.
Support:   http://jdv-soft.com?PN=ProjSimpleSiteCreator.

This program is free software and distributed in the hope that it will be useful,.
but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or .
FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details..

You can redistribute it and/or modify it under the terms of the GNU General Public License .
as published by the Free Software Foundation; either version 2 of the License, or.
(at your option) any later version..
------------------------------------*/
 global $gTLang, $gTRefWare;

 $Link1   = "&Action=Add" . "&Group=" . urlencode($aGroup) . "&Item=" . urlencode($aItem);

 $gTRefWare->SetCurItem($aGroup);
 $GroupName = ($aGroup != "" ? $gTRefWare->GetRecord()->GetField("Name") : $aGroup);

 $gTRefWare->LogItem($aItem);
 $gTRefWare->SetCurItem($aItem);
 $WareItem = $gTRefWare->GetRecord();
 
 $Price    = $WareItem->GetField("Price");
 $PriceS   = $WareItem->GetField("PriceS");
 $PriceStr = sprintf("%s %s", $Price, $gTLang->GetItem("MoneyAbr"));
 if ($PriceS != "") {
   $PriceS = "<s>$Price</s>&nbsp;<span class='RedBold'>$PriceS</span><br>"; 
 }
 $Details = $PriceS . "<br>" . TStr::Replace($WareItem->GetField("Details"), ";", "<br>");
 
 $MacrosThumb = $gTLang->TParseFile->LoadMacros("Thumb");
 $MacrosThumb->TArrParam[0] = $WareItem->GetImages().Implode(",");
 $MacrosThumb->TArrParam[1] = $WareItem->GetField("Name");
 
 $gTLang->SetItem("ArticleNameData", $WareItem->GetField("Name"));
 ?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr> 
  	<td align="center"><?php printf("<h1>%s</h1>", $GroupName); ?></td>
  </tr>
  <tr> 
  	<td>&nbsp;</td>
  </tr>
  <tr>
    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="center"><?php $gTLang->ShowItem("ID"); ?></td>
        <td align="center"><?php $gTLang->ShowItem("Article name"); ?></td>
        <td align="center"><?php $gTLang->ShowItem("Price"); ?></td>
        <td align="center"><?php $gTLang->ShowItem("Buy"); ?></td>
      </tr>
      <tr>
        <td width="10%" align="center" class="TableColor_Title"><?php $WareItem->ShowField("ID"); ?></td>
        <td width="68%" align="center" class="TableColor_Title"><?php $WareItem->ShowField("Name"); ?></td>
        <td width="12%" align="center" class="TableColor_Title"><?php print($PriceStr); ?></td>
        <td width="10%" align="center" class="TableColor_Title"><a href="<?php Show("index.php?PN=Order" . $Link1); ?>"><img src="Images/Buy.gif" width="24" height="16" border="0"></a></td>
      </tr>
    </table></td>
  </tr>
  <tr><td>&nbsp;</td></tr>
  <tr>
    <td align="left" valign="top"><?php print($Details); ?></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="center"><?php printf("<h1>%s<h1>", $PriceStr); ?></td>
  </tr>
  <tr>
    <td><Hr size="1" NoShad></td>
  </tr>
  <tr>
    <td align="center" valign="top">&nbsp;</td>
  </tr>
  <tr>
    <td align="center" valign="top"><?php print($MacrosThumb->Build()); ?></td>
  </tr>
</table>
