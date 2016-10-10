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

class tMacros_GetItems extends tMacros_BaseClass
{
	const	cFilter	= 0,
			cMode	= 1,
			cInvert = 2;
	const	cSyntax	= "[Filter]|[Mode]|[Invert]";
	const	cDescr	= <<<BAR
Gets 'Filter' from class 'TParseFile	
href="http://jdv-soft.com/index.php?PN=Help&PS=ProjSimpleSiteCreator/Macros_GetItems"
BAR;

	
	public function InitDefParam()
	{
		switch($this->TArrParam->GetCount()) {
			case 0: $this->SetDefParam("Filter", "");
			case 1: $this->SetDefParam("Mode", "cRight");
			case 2: $this->SetDefParam("Invert", false);
		}
	}
	
	
	public function Build()
	{
		if (!$this->CheckParam()) return;

		$this->InitDefParam();
		
		if ($this->TArrParam[self::cFilter] == "") {
			$TArray1 = $this->TParseFile->TArrData;
		}else{
			$TArray1 = $this->TParseFile->TArrData->GrepLabel($this->TArrParam[self::cFilter], $this->TArrParam[self::cInvert]);
		}
		$Mode = constant("TArray::" . $this->TArrParam[self::cMode]);
		$this->Data = $TArray1->PadEx(TArray::cRight, "\n")->GetContext($Mode);
		return $this->Data;	
	}
}
?>
