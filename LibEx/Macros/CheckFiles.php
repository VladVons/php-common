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

class tMacros_CheckFiles extends tMacros_BaseClass
{
	const	cMask	= 0, 
			cFilter = 1;
	const	cSyntax = "[Mask][Filter]";
	const	cDescr	= <<<BAR
Shows help
href="http://jdv-soft.com/index.php?PN=Help&PS=ProjSimpleSiteCreator/Macros_CheckFiles"
BAR;

	public function InitDefParam()
	{
		switch($this->TArrParam->GetCount()) {
			case 0: $this->SetDefParam("Mask", ".txt$");
			case 1: $this->SetDefParam("Filter", ".txt");
		}
	}	
	
	
	public function Build()
	{
		if (!$this->CheckParam()) return;

		$this->InitDefParam();
	
		$TDir1 = new TDir(".");
		$ArrFiles = $TDir1->GetFiles(true, $TDir1::cFile, $this->TArrParam[self::cMask]);
		$ArrFiles->Reset();
		while (list($No, $FullPath) = $ArrFiles->Each()) {
			$this->TParseFile->LoadFromFile($FullPath);
		}

		$TArrResult = $this->TParseFile->TArrFiles->GetAllFiles();
		$this->Data = $TArrResult->GrepValue($this->TArrParam[self::cFilter])->PadEx(TArray::cRight, "\n")->GetContext(TArray::cRight);
		return $this->Data;	
	}
}
?>
