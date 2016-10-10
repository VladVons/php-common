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
require_once(_DirCommonLib . "/Paging.php");

global $gTLang;

$aWidth	= $aGet->GetItem("Width",  _GalleryWidth);
$aHeight= $aGet->GetItem("Height", _GalleryHeight);
$aItems	= $aWidth * $aHeight;

$TDir1 = new TDir($aCatalog);
$TArray1 = $TDir1->GetFiles(true, TDir::cFile, "(.jpg$|.png$)");
if ($TArray1->GetCount() == 0) {
	$gTLang->ShowItem("Directory is empty");
	exit;
}

$Paging1 = new TPaging($aGet->ArrData, $aItems);
$Paging1->Build($TArray1->GetCount());
$TArray1 = $TArray1->Slice($Paging1->GetItemStart(), $Paging1->GetItemLength());

$LangDescr = "";
$TArrTable = new TArray();
$TArray1->Reset();
while (list($No, $FullPath) = $TArray1->Each()) {
	$FileInfo = TFS::GetFileInfo($FullPath);
	$FileName = $FileInfo->GetItem("BaseName");
	$Catalog  = $FileInfo->GetItem("DirName");
	$FileSize = GetShortSize($FileInfo->GetItem("Size"));
	if ($LangDescr != $Catalog) {
		$LangDescr = $Catalog;
		$gTLang->LoadFromFile($LangDescr . "/Index.txt");
	}
	$FileThumb = GetThumbDef($FullPath, _ThumbWidth);
	//print("FullPath:$FullPath, FileName:$FileName, Catalog:$Catalog, FileSize:$FileSize, FileThumb:$FileThumb<br>");

	$Hint = sprintf("%s, $FileSize, %s", $FileName, $FileInfo->GetItem("Date"));
	$Link1 = "index.php?PN=ImageView&Catalog=$Catalog&Item=$FileName";
	$Link2 = sprintf('<p style="float:%s; margin:0px 5px 5px 5px; font-style: italic; text-align: center;">
					<a href="%s"><img src="%s" title="%s" border="1"></a><br>%s</p>',
					"Left", $Link1, $FileThumb, $Hint, $gTLang->GetItem($FileName));
	$TArrTable->SetItem($FullPath, $Link2);
	$gTLang->AddFiles("Image", $FullPath);
}

$Table1 = new TTable($aWidth, $aHeight, 'width="100%" border="0"', 'align="center" valign="middle"');
$Table1->Build($TArrTable);
?>

<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr align="left" valign="top">
    <td><?php $Table1->PrintOut(); ?></td>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="center" class="TextBold"><?php printf("%s %s", $gTLang->GetItem("Pages"), $Paging1->GetPrintOut()); ?></td>
  </tr>
</table>
