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
require_once("Index_Head.php");
 
global $gTLang;

if ($aGet->GetItem("Action") == "Skip") {
	$TDir1 = new TDir($aCatalog);
	$TArray1 = $TDir1->GetFiles(false, TDir::cFile, "(.jpg$|.jpeg$|.png$)");
	$IsSkip = false;
	if ($TArray1->GetCount() > 1 && $TArray1->SearchEx($aItem) !== "") {
		if ($aPost->GetItem("_ImageView_Begin") != "") {
			$IsSkip = $TArray1->Reset();
		}elseif ($aPost->GetItem("_ImageView_Prev") != "") {
			$IsSkip = $TArray1->Prev();
		}elseif ($aPost->GetItem("_ImageView_Next") != "") {
			$IsSkip = $TArray1->Next();
		}elseif ($aPost->GetItem("_ImageView_End") != "" ) {
			$IsSkip = $TArray1->End();
		}
	}
	
	$FileInfo = TFS::GetFileInfo($TArray1->Current());
	$FileName = ($IsSkip !== false ? $FileInfo->GetItem("BaseName") : $aItem);
	header("Location: index.php?PN=ImageView&Catalog=$aCatalog&Item=$FileName");

	exit;
}
 
$FileName = $aCatalog . "/" . $aItem;
if (!TFS::FileExists($FileName)) {
	printf("%s: %s", $gTLang->GetItem("File doesn't exists"), $FileName);
	exit;
} 

$TImage1 = new TImageEx();
$TImage1->CreateFromFile($FileName);
$ImgWidth = $TImage1->GetWidth();
if ($ImgWidth > _ImageWidth) {
	$ImgWidth = _ImageWidth;
}
 
$gTLang->LoadFromFile($aCatalog . "/" . Index.txt);
$gTLang->AddFiles("Image", $FileName);
?>

<form action="<?php print("index.php?PN=ImageView&Catalog=$aCatalog&Item=$aItem&Action=Skip"); ?>" method="post" name="form2" id="form2">
 <table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center">
        <input name="_ImageView_Begin"   type="submit" class="submit1" value="&lt;&lt;--">
        <input name="_ImageView_Prev"    type="submit" class="submit1" value="&lt;--">
        <input name="_ImageView_Next"    type="submit" class="submit1" value="--&gt;">
        <input name="_ImageView_End"     type="submit" class="submit1" value="--&gt;&gt;">
	</td>
  </tr>
  <tr align="center" valign="middle">
    <td colspan="3"><?php printf('<a href="%s"><img src=%s width="800" border="1"></a>', $FileName, $FileName, $ImgWidth); ?></td>
  </tr>
  <tr>
    <td align="center"><b><?php $gTLang->ShowItem($aItem); ?></b></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td align="center">
        <input name="_ImageView_Begin"   type="submit" class="submit1" value="&lt;&lt;--">
        <input name="_ImageView_Prev"    type="submit" class="submit1" value="&lt;--">
        <input name="_ImageView_Next"    type="submit" class="submit1" value="--&gt;">
        <input name="_ImageView_End"     type="submit" class="submit1" value="--&gt;&gt;">
	</td>
 </table>
</form>
