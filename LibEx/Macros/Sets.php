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

class tMacros_Sets extends tMacros_BaseClass
{
	const	cMacros	= 0, 
			cValues = 1;
	const	cSyntax = "Macros|Values";
	const	cDescr	= <<<BAR
href="http://jdv-soft.com/index.php?PN=Help&PS=ProjSimpleSiteCreator/Macros_Sets"
BAR;
	
	
	public function InitDefParam()
	{
	}
	
	
	public function Build()
	{
		if (!$this->CheckParam()) return;

		$this->InitDefParam();

		$MacrosName = $this->TArrParam[self::cMacros];
		$Macros = $this->TParseFile->LoadMacros($MacrosName);
		if ($Macros !== "") {
			$Token1 = new TToken(",");
			$Token1->LoadFromString($this->TArrParam[self::cValues]);
			while (list($No1, $Pair) = $Token1->TArrData->Each()) {
				$Pos1 = TStr::Pos($Pair, "=");
				if ($Pos1 > 1 ) {
					$Macros->TArrParam[0] = TStr::Trim(TStr::Left($Pair, $Pos1));
					$Macros->TArrParam[1] = TStr::Trim(TStr::Sub($Pair, $Pos1 + 1));
					$Macros->Build();
				}
			}
			$this->Data = " ";
		}
		return $this->Data;	
	}
}
?>
