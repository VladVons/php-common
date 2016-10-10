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
?>

<table width="200"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td class="TableColor_Title"><?php $gTLang->ShowItem("Search on site"); ?></td>
  </tr>
  <tr>
    <td align="left">
	<form action="http://google.com/search" name="f" target="_blank">
	<input type="hidden" name="q" value="site:<?php print("http://" . str_replace("www.", "", $_SERVER['HTTP_HOST'])); ?>">
	<input type="hidden" name="ie" value="<?php $gTLang->ShowItem("HeadMetaCharset", "windows-1251"); ?>">
	<input maxlength="256" size="15" name="q" value="">
	<input type="submit" value="Google" name="btnG" class="Submit1">
	</form>
 	</td>
  </tr>
</table>