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
 global $gTLang;

 $aMsgID   = $aGet->GetItem("MsgID");
 $aMsgStr  = urldecode($aGet->GetItem("MsgStr"));
 $aMsgUrl  = $aGet->GetItem("MsgURL") == "Back" ? $_SERVER['HTTP_REFERER'] : urldecode($aGet->GetItem("MsgURL"));
?>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td align="center"><?php $gTLang->ShowItem($aMsgID); ?></td>
  </tr>
  <tr>
    <td align="center"><?php Show($aMsgStr); ?></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="center">
    <form name="form1" method="post" action="<?php Show($aMsgUrl); ?>">
      <input type="submit" name="Submit" value="OK" class="submit1">
    </form></td>
  </tr>
</table>
