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
global $gTLang, $TSQL1;

require_once(_DirCommonLibEx . "/DB/RefCustomer_OSC.php");

$RefCustomer = new TRefCustomer_OSC($TSQL1, $gTLang);

$ID = $RefCustomer->GetSessionID();
if ($aAction != "New" && Empty($ID)) {
	header("Location: index.php?PN=RefCustomerLogin&Action=Init");
	exit();
}

$Customer = $RefCustomer->GetRecord();
if ($aPost->GetCount() > 0) {
	$Customer->SetFields($aPost, "_CustomerInfo_");

	if ($aAction == "New") {
		$RefCustomer->AddItem();
	}else
	if ($aAction == "Update") {
		$RefCustomer->SetCurItem($ID);
		$RefCustomer->UpdateItem();
	}
}
?>
<form name="RefCustomerItemInfo" method="post" action="<?php printf("index.php?PN=RefCustomerItemInfo&Action=$aAction"); ?>">
 <table width="100%" border="0" align=center cellpadding="0" cellspacing="0" class="TableShadow">
    <tr>
      <td><?php $gTLang->ShowItem("First name"); ?>&nbsp;</td>
      <td><input name="_CustomerInfo_FirstName" type="text"  value="<?php $Customer->ShowField("FirstName"); ?>"></td>
    </tr>
    <tr>
      <td><?php $gTLang->ShowItem("Last name"); ?>&nbsp;</td>
      <td><input name="_CustomerInfo_LastName" type="text"  value="<?php $Customer->ShowField("LastName"); ?>"></td>
    </tr>
    <tr>
      <td><?php $gTLang->ShowItem("EMail"); ?>&nbsp;</td>
      <td><input name="_CustomerInfo_EMail" type="text"  value="<?php $Customer->ShowField("EMail"); ?>"></td>
    </tr>
    <tr>
      <td><?php $gTLang->ShowItem("Phone"); ?>&nbsp;</td>
      <td><input name="_CustomerInfo_Phone" type="text"  value="<?php $Customer->ShowField("Phone"); ?>"></td>
    </tr>
    <tr>
      <td><?php $gTLang->ShowItem("Address"); ?>&nbsp;</td>
      <td><input name="_CustomerInfo_Address" type="text"  value="<?php $Customer->ShowField("Address"); ?>"></td>
    </tr>
    <tr>
      <td><?php $gTLang->ShowItem("Password"); ?></td>
      <td><input name="_CustomerInfo_Password" type="password"  value="<?php $Customer->ShowField("Password"); ?>"></td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td><input name="OK" type="submit" value="OK" /></td>
    </tr>
 </table>
</form>
