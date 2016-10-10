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

function GetNearPage($aFileName, $aTArray, $aMethod)
{
	global $gTLang;

	$Result = $gTLang->TParseFile->TArrData->GetItem("Head" . $aMethod);
	if($Result == "") {
		if ($aTArray->GetCount() > 1 && $aTArray->SearchEx($aFileName) !== "") {
			if ($aTArray->$aMethod()) {
				$FileCur = $aTArray->Current();
				$TParseFile1 =  new tParseFile();
				$TParseFile1->TPath = $gTLang->TParseFile->TPath;
				$TParseFile1->Settings->ParseLineEnd = 3;				
				$TParseFile1->LoadFromFile($FileCur);
				$FileInfo = TFS::GetFileInfo($FileCur);
				$Result = THTML::SubPage($FileInfo->GetItem("FileName"), $TParseFile1->TPath->TDir->GetDirName(), $TParseFile1->GetItem("HeadC"));
			}
		}
	}	
	return $Result;
}

//if (TStr::Length($aPS) > 0 && !$gTLang->ParseSubPage($aPN, $aPS)) {
	//throw new MyException(sprintf("%s $aPS", $gTLang->GetItem("Page not found")), 1);
//}

$PS			= TStr::Replace($aPS, $aPN . "/", "");
$FileName	= $gTLang->GetDir() . "/" . $aPN . "/" . $PS . ".txt";
$TDir1		= new TDir(TFS::GetDirName($FileName));
$TArray1	= $TDir1->GetFiles(false, TDir::cFile);
$HeadPrev	= GetNearPage($FileName, $TArray1, "Prev"); 
$HeadNext	= GetNearPage($FileName, $TArray1, "Next"); 
?>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
		<td width="100%" align="left" valign="top">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="29%" align="left"><?php $gTLang->ShowItem("Previous"); ?></td>
				<td width="38%" align="center" class="TextBold"><?php $gTLang->ShowItem("ProjC"); ?></td>
				<td width="33%" align="right"><?php $gTLang->ShowItem("Next"); ?></td>
			</tr>
			<tr>
				<td align="left"><?php print($HeadPrev); ?></td>
				<td align="center" class="TextBold"><?php $gTLang->ShowItem("HeadC"); ?></td>
				<td align="right"><?php print($HeadNext); ?></td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td>
		<hr size="2">
		</td>
	</tr>
	<tr>
		<td align="left" valign="top">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td><!-- <?php print("$aPS SubPage Begin"); ?>!--> 
				<?php $gTLang->ShowItem("BodyL"); ?>
				<!-- <?php print("$aPS SubPage End"); ?>!--></td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
		</table>
		</td>
	</tr>
	<tr>
		<td align="left" valign="top">
		<hr size="2">
		</td>
	</tr>
	<tr>
		<td align="left" valign="top">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="29%" align="left" class="TextBold"><?php print($HeadPrev); ?></td>
				<td width="38%" align="center" class="TextBold"><?php $gTLang->ShowItem("HeadC"); ?></td>
				<td width="33%" align="right" class="TextBold"><?php print($HeadNext); ?></td>
			</tr>
		</table>
		</td>
	</tr>
</table>
