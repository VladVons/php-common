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
<form name="Form2" method="post"
	action="index.php?PN=MailUs&Action=ContactsMail">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="45%" align="left" valign="top"><?php $gTLang->ShowItem("PageTextTop"); ?></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td align="center" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td align="left" valign="top"><?php $gTLang->ShowItem("ContactInfo"); ?></td>
            <td valign="top"><?php $gTLang->ShowItem("MapCode"); ?></td>
          </tr>
        </table></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td align="center" valign="top"><input name="Message" type="submit"	class="Submit1" value="<?php $gTLang->ShowItem("Write message"); ?> "></td>
	</tr>
</table>
</form>

