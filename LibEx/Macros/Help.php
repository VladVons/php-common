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

class tMacros_Help extends tMacros_BaseClass
{
	const	cMask = 0, 
			cType = 1;
	const	cSyntax = "[Mask]";
	const	cDescr	= <<<BAR
Shows help
href="http://jdv-soft.com/index.php?PN=Help&PS=ProjSimpleSiteCreator/Macros_Help"
BAR;

	public function InitDefParam()
	{
		switch($this->TArrParam->GetCount()) {
			case 0: $this->SetDefParam("Mask", ".php$");
		}
	}	
	
	
	public function Build()
	{
		if (!$this->CheckParam()) return;

		$this->InitDefParam();
	
		$this->Data = "";
		$TDir1 = new TDir($this->TParseFile->Settings->MacrosDir);
		$ArrFiles = $TDir1->GetFiles(false, $TDir1::cFile, $this->TArrParam[self::cMask], "BaseClass");
		$ArrFiles->Reset();
		while (list($No, $FullPath) = $ArrFiles->Each()) {
			$FileInfo = TFS::GetFileInfo($FullPath);
			$MacrosName = $FileInfo->GetItem("FileName");
			$Macros	= $this->TParseFile->LoadMacros($MacrosName);
			if ($Macros !== "") {
				$this->Data .= sprintf("%s: <b>%s</b>\n%s: %s\n%s: %s\n%s: %s\n\n", 
					$this->GetItem("Macros"),
					$MacrosName, 
					$this->GetItem("Path"),
					RootFileName($FullPath), 
					$this->GetItem("Syntax"),
					$Macros->GetConst("cSyntax"), 
					$this->GetItem("Description"),
					$Macros->GetHelp("cDescr"));
			}
		}

		return $this->Data;	
	}
}
?>
