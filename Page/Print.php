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
require_once("_Config.php");
require_once(_DirCommonPage ."/Index_Head.php");

global $gTLang;

$PrintLink = sprintf('<a rel="nofollow" onclick="window.print(); return false;">%s</a>', $gTLang->GetItem("Print"));
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="<?php print($gTLang->GetLanguage()); ?>">

<head>
<title><?php $gTLang->ShowItem("HeadTitle"); ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=<?php $gTLang->ShowItem("HeadMetaCharset", "windows-1251"); ?>">
<meta name="robots"        content="<?php $gTLang->ShowItem("HeadMetaRobots", "noindex,nofollow"); ?>">
<meta name="author"        content="JDV-Soft, eMail:VladVons@mail.ru; ICQ 114979538">
<meta name="generator"     content="JDV SiteCreator">

<link rel="stylesheet" type="text/css" href="<?php $gTLang->ShowItem("PageStylePrint"); ?>">
</head>

<body>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left"><?php printf("%s<br>%s: %s (%s)", date("Y/m/d H:i"), $PrintLink, $gTLang->GetItem("PageTitle"), THTML::Href($_SERVER["HTTP_REFERER"], "http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"])); ?></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="left">
		<!-- <?php print("$IncludeFileName Begin"); ?>!-->
		<?php include $IncludeFileName; ?>
		<!-- <?php print("$IncludeFileName End"); ?>!-->
	</td>
  </tr>
  <tr>
    <td align="left">&nbsp;</td>
  </tr>
</table>
</body>
</html>
