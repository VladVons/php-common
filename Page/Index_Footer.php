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

function ExportFilesToSession($aType, $aLang)
{
	$Name = "ParseFiles_$aType";
	$_SESSION[$Name] = "";
	$ArrFiles  = $aLang->GetFiles($aType);
	for ($i = 0; $i < $ArrFiles->GetCount(); $i++) {
		$_SESSION[$Name][$i] = $ArrFiles->GetItem($i);
	}
	return $ArrFiles;
}

if ($CurPN != "Edit") {
	$URI = $_SERVER["REQUEST_URI"];
	$_SESSION["PN_Edit_URI"] = $URI;

	ExportFilesToSession("Image", $gTLang);
	ExportFilesToSession("File", $gTLang);
	ExportFilesToSession("PHP", $gTLang);
	ExportFilesToSession("Link", $gTLang);
	ExportFilesToSession("Error", $gTLang);
	$ArrFilesLang = ExportFilesToSession("Load", $gTLang);

	$FilesCnt = $ArrFilesLang->GetCount();
	if ($FilesCnt > 0) {
		$FullFileName = $ArrFilesLang->GetItem($FilesCnt - 1);
		$ArrFileInfo  = TFS::GetFileInfo($FullFileName);
		$Href = GetStatLink(sprintf("index.php?PN=Edit&Action=Edit&Dir=%s&Src=%s", 
								$ArrFileInfo->GetItem("DirName"), $ArrFileInfo->GetItem("BaseName")));
		$EditLink	= sprintf('<a href="%s" target="_blank">%s</a>', $Href, $gTLang->GetItem("Edit"));
		$UpwardLink	= sprintf('<a href="%s#">%s</a>', $_SERVER["REQUEST_URI"],  $gTLang->GetItem("Upward"));								
		if (_CMS_InfoLink == true) {
			$PageInfo = sprintf('%s: %s; %s: %3.3f; %s: %s',
						$gTLang->GetItem("Modified"), $ArrFileInfo->GetItem("Date"),
						$gTLang->GetItem("Time generation"), Microtime(true) - $TimerStart,
						$gTLang->GetItem("Size"), GetShortSize(ob_get_length()));
		}		
	}
	if (TStr::Pos($URI, "index.php") == -1) {
		$PrintLink = sprintf('<a href="%s" target="_blank">%s</a>', $URI . "Print.php", $gTLang->GetItem("Print"));
	}else{
		$PrintLink = sprintf('<a href="%s" target="_blank">%s</a>', TStr::Replace($URI, "index.php", "Print.php"), $gTLang->GetItem("Print"));
	}	
}
?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
    <td width="21%" align="left">&nbsp;</td>
    <td width="58%" align="center"><?php print(THTML::Href("http://jdv-soft.com/index.php?PN=ProjSimpleSiteCreator", _Version)); ?></td>
    <td width="21%" align="right"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="left"><?php print($EditLink); ?></td>
        <td align="left"><?php print($PrintLink); ?></td>
        <td align="left"><?php print($UpwardLink); ?></td>
      </tr>
    </table></td>
  </tr>
  <tr>
    <td align="center">&nbsp;</td>
    <td align="center"><?php print($PageInfo); ?></td>
    <td align="right">&nbsp;</td>
  </tr>
</table>
<?php
 $ArrFileInfo = TFS::GetFileInfo($IncludeFileName);
 $IncludeFileFooter = $ArrFileInfo->GetItem("BaseFileName") . "_Footer." . $ArrFileInfo->GetItem("Extension");
 if (TFS::FileExists($IncludeFileFooter)) {
    include $IncludeFileFooter;
 }
?>
