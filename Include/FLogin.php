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

$BtnName  = $Form_FLogin->GetItem("BtnName");
$Disabled = ($BtnName == "Logout" ? " Disabled" : "");
?>
 <table width="100%" border="0" align=center cellpadding="0" cellspacing="0" class="TableShadow">
    <tr align="left">
      <td width="22%">&nbsp;</td>
      <td width="78%" align="left"><?php printf("<b>%s</b>", $Form_FLogin->GetItem("User")); ?></td>
    </tr>
    <tr align="left">
      <td><?php $gTLang->ShowItem("Login"); ?>&nbsp;</td>
      <td align="left"><input name="_FLogin_Login" type="text"  value="<?php $Form_FLogin->ShowItem("Login"); ?>" <?php print($Disabled); ?>></td>
    </tr>
    <tr align="left">
      <td><?php $gTLang->ShowItem("Password"); ?></td>
      <td align="left"><input name="_FLogin_Password" type="password" value="<?php $Form_FLogin->ShowItem("Password"); ?>" <?php print($Disabled); ?>></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td align="left"><?php printf('<input type="submit" name="_FLogin_Btn%s" value="%s" class="Submit1">', $BtnName, $gTLang->GetItem($BtnName)); ?></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td align="left">&nbsp;</td>
    </tr>
	<tr>
      <td>&nbsp;</td>
      <td align="left"><a href="index.php?PN=RefCustomerItemInfo&Action=New"><?php $gTLang->ShowItem("New user"); ?></a></td>
    </tr>
	<tr>
	  <td>&nbsp;</td>
	  <td align="left"><a href="index.php?PN=RefCustomerItemInfo&Action=NewUser"><?php $gTLang->ShowItem("Forget password"); ?></a></td>
    </tr>
 </table>
