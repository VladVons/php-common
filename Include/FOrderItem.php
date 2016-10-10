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

 $Link1="index.php?PN=ItemInfo&Group=" . $Value["Group"] . "&Item=" . $Value["Item"];
?>
<tr align="left">
 <td><?php print($i); ?></td>
 <td><a href="<?php print($Link1); ?>"><?php $gTRefWare->ShowItemInfo("Name"); ?></a></td>
 <td><input type="text" name="<?php print("_Order_Cnt_$i"); ?>" size="1" maxlength="3" value="<?php print($Value["Cnt"]); ?>"></td>
 <td><?php print($Price); ?></td>
 <td><?php print($Value["Cnt"] * $Price); ?></td>
</tr>
