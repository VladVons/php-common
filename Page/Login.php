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

$BtnName = $_SESSION["PN_Login_User"] == "" ? "Login" : "Logout";

if ($aAction == "Init") {
	$_SESSION["PN_Login_URI"] = $_SERVER["HTTP_REFERER"];
}elseif ($aAction == "Logout") {
	$_SESSION["PN_Login_OK"]	 = false;
	$_SESSION["PN_Login_User"]	 = "";
	$_SESSION["PN_Login_Rights"] = "";
}elseif ($aAction == "Login" && $aPost->GetItem("_FLogin_Login") != "") {
	$Cookies = new TCookies();
    $aPost->Reset();
	while (list($Label, $Value) = $aPost->Each()) {
		$Cookies->SetItem($Label, true);
	}

        $Login    = trim($aPost->GetItem("_FLogin_Login"));
        $Password = trim($aPost->GetItem("_FLogin_Password"));
	if ( ($Login == _CMS_Login) && ($Password == _CMS_Password) ) {
		$_SESSION["PN_Login_OK"]	 = true;
		$_SESSION["PN_Login_User"]	 = $Login;
		$_SESSION["PN_Login_Rights"]     = 1;
		header(sprintf("Location: %s", $_SESSION["PN_Login_URI"]));
	}else{
		printf("<b>%s !</b>", $gTLang->GetItem("Wrong password or user name"));
	}
}	

$Form_FLogin = new TArray($_COOKIE);
$Form_FLogin->SetItem("BtnName", $BtnName);
$Form_FLogin->SetItem("User", $_SESSION["PN_Login_User"]);	
?>
<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
	 <td align="left" valign="top"><?php $gTLang->ShowItem("PageTextTop"); ?></td>
  </tr>
  <tr>
    <td align="center" valign="top">
		<form name="FLogin" method="post" action="<?php printf("index.php?PN=Login&Action=%s", $BtnName); ?>">
		   <?php 
 			include  _DirCommonForm . "/FLogin.php"; 
			?>
		</form>
	</td>
  </tr>
</table>
