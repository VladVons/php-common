<?php
/*------------------------------------.
Simple Site Creator PHP script.
.
Copyright (C) 2006-2009 Volodymyr Vons, VladVons@mail.ru.
Copyright (C) 2009 JDV-Soft Inc, http://www.jdv-soft.com.
.
Donations: http://jdv-soft.com?PN=Donation.php.
Support:   http://jdv-soft.com?PN=ProjSimpleSiteCreator.
.
This program is free software and distributed in the hope that it will be useful,.
but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or .
FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details..
.
You can redistribute it and/or modify it under the terms of the GNU General Public License .
as published by the Free Software Foundation; either version 2 of the License, or.
(at your option) any later version..
------------------------------------*/

//entry from TFileBase callback. So '$this' points to TFileBase

$CurFile	 = $this->GetItem("GalleryFile");
$ArrFileInfo = TFS::GetFileInfo($CurFile);
$CurFileName = $ArrFileInfo->GetItem("BaseFileName");
  
//$this->TokenMacros("Set|BR|true");
//$this->LoadFromFile($CurFile);
//$this->TokenMacros("Set|BR|false");

$NewsClass = $this->GetItem("NewsClass");
$NewsPage  = ($this->GetItem("NewsPage") == "NewsPage" ? "Default" : $this->GetItem("NewsPage"));
$Link	   = sprintf("index.php?PN=%s&PS=%s", $NewsPage, $CurFileName);
$NewsBody  = $this->GetItem("PageTextTop");
?>

<table width="100%" border="1" cellspacing="0" cellpadding="0">
  <tr class="<?php print($NewsClass); ?> CursorHand">
    <td>
	 <table width="100%" border="0" cellpadding="0" cellspacing="0" onclick="GoToPage('<?php print($Link); ?>');">
      <tr>
        <td colspan="3">&nbsp;</td>
      </tr>
      <tr>
        <td width="113"><?php $this->ShowItem("NewsDate"); ?></td>
        <td width="446" class="TextBold"><?php $this->ShowItem("NewsTheme") ?></td>
        <td width="191"><?php $this->ShowItem("NewsAuthor"); ?></td>
      </tr>
      <tr>
        <td colspan="3">&nbsp;</td>
      </tr>
      <tr>
        <td colspan="3"><?php printf('%s <a href="%s">%s ...</a>', CrToBr($NewsBody), $Link, $this->GetItem("Detailed")); ?></td>
      </tr>
      <tr>
        <td colspan="3">&nbsp;</td>
      </tr>
    </table></td>
  </tr>
</table>
<BR>
