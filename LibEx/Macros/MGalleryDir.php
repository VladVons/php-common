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

class tMacros_MGalleryDir extends tMacros_FileClass
{
	const	cMacrosName	= 0, 
			cDirName	= 1, 
			cFileMask	= 2;
	const	cSyntax 	= "MacrosName|DirName|[FileMask]";
	const	cDescr		= <<<BAR
href="http://jdv-soft.com/index.php?PN=Help&PS=ProjSimpleSiteCreator/Macros_MGalleryDir"
BAR;

	
	public function InitDefParam()
	{
		switch($this->TArrParam->GetCount()) {
			case 2: $this->SetDefParam("Mask", ".jpg$");
		}
	}	
	
	
	public function Build()
	{
		if (!$this->CheckParam()) return;

		$this->InitDefParam();

		$this->Data = "";	
		$Macros = $this->TParseFile->LoadMacros($this->TArrParam[self::cMacrosName]);
		if ($Macros === "") {
			$ErrMsg = sprintf("Cant create macros '%s'", $this->TArrParam[self::cMacrosName]);
			$this->LogError($ErrMsg);
			return $this->Data;
		}else{
			$Mask = $Macros->GetConst("cMask");
			if ($Mask !== "") {
				//$this->TArrParam[self::cFileMask] = $this->Macros->GetConst("cMask");
			}	
		}	

		$PlayList	= "";
		$MacrosName = $Macros->GetMacrosName();
		$Prefix		= $this->GetPrefix($MacrosName);
		$ArrFiles = $this->GetGalleryFiles($this->TArrParam[self::cDirName], $this->TArrParam[self::cFileMask]);
		$ArrFiles = $this->SortGalleryArray($ArrFiles);
		$ArrFiles->Reset();
		while (list($No, $FullPath) = $ArrFiles->Each()) {
			$ArrFileInfo = TFS::GetFileInfo($FullPath);
			$FileName = $ArrFileInfo->GetItem("FileName");
			$Str1 = $FullPath . "," . $this->TParseFile->GetItem($FileName);
			if ($this->TArrParam[self::cMacrosName] == "MFoto") {
				$Str1 = $Str1 . "," . GetThumbDef($FullPath);
			}
			$PlayList .= $Str1 . ";";
			$this->TParseFile->TArrFiles->AddFile($Prefix, $FullPath);
		}
		$PlayList = TStr::Left($PlayList, TStr::Length($PlayList) - 1);
		
		if ($Macros::GetConst("cFileName") !== "") {
			$Macros->TArrParam[$Macros::cFileName] = $PlayList;
		}
		if ($this->CheckParam()) {
			$Macros->InitDefParam();
			$this->Data = $Macros->AsFile($PlayList);
		}	
		return $this->Data;		
	}	

	
	public function AsFile($aFileName) 
	{
	}	
}
?>
