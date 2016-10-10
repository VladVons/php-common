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
$CurFile = $this->GetItem("Gallery_File");

//$this->TokenMacros("Set|SetBR|true");
//$this->LoadFromFile($CurFile);
//$this->TokenMacros("Set|SetBR|false");

$ArrFile = TFS::GetFileInfo($CurFile);
$PS = UrlCode($ArrFile->GetItem("DirName") . "/" . $ArrFile->GetItem("FileName"), "RawEncode");
$Link = THTML::Page("Default&PS=$PS", $ArrFile->GetItem("FileName"));
//$this->SetItem("PageTitle", $ArrFile->GetItem("FileName"));
?>

<table width="750" border="0" cellpadding="0" cellspacing="0">
  <tr>
	<td scope="row"><?php print($Link); ?></td>
  </tr>
</table>
