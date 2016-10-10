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
$Msg = "";
if ($aAction == "Logout") {
	$RefCustomer->SetSessionID("");
}else
if($aAction == "Login") {
	if ($aPost->GetItem("_FLogin_Login") == "") {
		$Msg = $gTLang->GetItem("Field user is empty");
	}else	
	if ($aPost->GetItem("_FLogin_Password") == "") {
		$Msg = $gTLang->GetItem("Field password is empty");
	}else	
	if ($RefCustomer->Validate($aPost->GetItem("_FLogin_Login"), $aPost->GetItem("_FLogin_Password"))) {
		$Customer = $RefCustomer->GetRecord();
		$RefCustomer->SetSessionID($Customer->GetField("ID"));
		HttpGoURI();
	}else{
		$Msg = sprintf("%s !", $gTLang->GetItem("Wrong password or user name"));
	}
}	

$Form_FLogin = new TArray();
$ID = $RefCustomer->GetSessionID();
if (Empty($ID)) {
	$Form_FLogin->SetItem("BtnName", "Login");
}else{
	$RefCustomer->SetCurItem($ID);
	$Customer = $RefCustomer->GetRecord();
	$Form_FLogin->SetItem("BtnName", "Logout");
	$Form_FLogin->SetItem("User", $Customer->GetField("Name"));	
}
?>
<form name="FLogin" method="post" action="<?php printf("index.php?PN=RefCustomerLogin&Action=%s", $Form_FLogin->GetItem("BtnName")); ?>">   
 <table width="100%" border="0" cellspacing="0" cellpadding="0">
   <tr>
	  <td align="left"><?php $gTLang->ShowItem("PageTextTop"); ?></td>
   </tr>
   <tr>
	  <td align="left"><?php printf("<b>%s</b>", $Msg); ?></td>
   </tr>
   <tr>
	  <td>&nbsp;</td>
   </tr>
   <tr>
     <td align="center"><?php include _DirCommonForm . "/FLogin.php"; ?></td>
  </tr>
 </table>
</form>	 
