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

class tMacros_NearPage extends tMacros_FileClass
{
	const	cMode	= 0, 
			cDirName= 1;
	const	cSyntax = "Mode|[DirName]";
	const	cDescr	= <<<BAR
href="http://jdv-soft.com/index.php?PN=Help&PS=ProjSimpleSiteCreator/Macros_NearPage"
BAR;


	public function InitDefParam()
	{
		switch($this->TArrParam->GetCount()) {
			case 1: $this->SetDefParam("DirName", $this->TParseFile->TPath->TDir->DirName);
		}
	}	

	
	public function Build()
	{
		global	$gTLang;

		if (!$this->CheckParam()) return;

		$this->InitDefParam();

		$CurFile = $this->TParseFile->GetFileName();
		$FileInfo = TFS::GetFileInfo($CurFile);
		$DirName = $this->SearchFile($this->TArrParam[self::cDirName]);
		if ($DirName != "") {
			$TArray1 = $this->GetGalleryFiles($DirName, ".txt$");
			if ($TArray1->GetCount() > 1 && $TArray1->SearchEx($CurFile) !== "") {
				$Mode = $this->TArrParam[self::cMode];
				if (TStr::Pos(",Next,Prev,End,Reset,", $Mode) == -1) {
					$this->LogError(sprintf("%s: '%s'", $this->GetItem("Invalid mode"), $Mode));
				}else{
					if ($TArray1->$Mode()) {
						$FileInfo = TFS::GetFileInfo($TArray1->Current());
					}
				}
			}	
		}
		
		$this->Data = TStr::Replace($FileInfo->GetItem("BaseFileName"), $gTLang->GetDir() . "/", "");
		return $this->Data;	
	}


	public function AsFile($aFileName)
	{
	}
	
}
?>
