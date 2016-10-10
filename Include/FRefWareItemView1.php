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

$Link1   = "&Group=" . urlencode($aGroup) . "&Item=" . urlencode($WareItem->GetField("ID"));
$ClassType = ($Items % 2 == 0 ? 'class="TableColor_Gray"' : ""); 
?>
<table width="100%"  border="1" cellpadding="0" cellspacing="0">
  <tr>
    <td><table width="100%"  border="0" cellspacing="0" cellpadding="0">
      <tr <?php print($ClassType); ?>>
        <td width="7%"><?php $WareItem->ShowField("ID"); ?></td>
        <td width="70%"><?php $WareItem->ShowField("Name"); ?></td>
        <td width="2%">&nbsp;</td>
        <td width="10%"  class="TableColor_Title"><?php $WareItem->ShowField("Price"); ?></td>
        <td width="8%"><?php $gTLang->ShowItem("OrderIt"); ?></td>
        <td width="3%" align="right"><a href="<?php Show("index.php?PN=Order" . $Link1); ?>"><img src="Images/Buy.gif" width="24" height="16" border="0"></a></td>
      </tr>
    </table></td>
  </tr>
</table>
