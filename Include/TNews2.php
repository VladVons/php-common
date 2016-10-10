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
global $gTLang;

// callback. '$this' points to TMacro_Include 

$CurFile	 = $gTLang->GetItem("Gallery_File");
$ArrFileInfo = TFS::GetFileInfo($CurFile);
$CurFileName = $ArrFileInfo->GetItem("BaseFileName");
  
$gTLang->TParseFile->Settings->SetBR("true");
$gTLang->LoadFromFile($CurFile);
$gTLang->TParseFile->Settings->SetBR("false");

//$NewsDate = $this->GetItem("NewsDate");
//$NewsDays = $this->GetItem("NewsDays");
//$Today = Date("Y-m-d");
$aWidth = ($gTLang->GetItem("TNews2_Width") == "TNews2_Width" ? "100%" : $gTLang->GetItem("TNews2_Width"));
$aHeight= ($gTLang->GetItem("TNews2_Height") == "TNews2_Height" ? "" : $gTLang->GetItem("TNews2_Height"));

$NewsPage  = ($gTLang->GetItem("NewsPage") == "NewsPage" ? "Default" : $gTLang->GetItem("NewsPage"));
$Link	   = sprintf("index.php?PN=%s&PS=%s", $NewsPage, $CurFileName);
$NewsBody  = TStr::LeftWords($gTLang->GetItem("PageTextTop"), 100);
?>

<table <?php printf('width="%s" height="%s"', $aWidth, $aHeight); ?> border="1" class="TableShadow">
 <tr>
   <td class="TableColor_Title CursorHand" onclick="GoToPage('<?php print($Link); ?>');"><?php $gTLang->ShowItem("NewsTheme"); ?></td>
 </tr>
 <tr>
   <td class="Pad"><?php printf('%s <a href="%s">%s ...</a>', CrToBr($NewsBody), $Link, $gTLang->GetItem("Detailed")); ?></td>
 </tr>
</table>
<br>
