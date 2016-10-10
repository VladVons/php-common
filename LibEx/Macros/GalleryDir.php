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

class tMacros_GalleryDir extends tMacros_FileClass
{
	const	cMacrosName	= 0, 
			cDirName	= 1, 
			cFileMask	= 2, 
			cCols		= 3, 
			cItems		= 4, 
			cHandler	= 5;
	const	cSyntax		= "MacrosName|DirName|[FileMask]|[Cols]|[Items]";
	const	cDescr		= <<<BAR
href="http://jdv-soft.com/index.php?PN=Help&PS=ProjSimpleSiteCreator/Macros_GalleryDir"
BAR;
	

	public function InitDefParam()
	{
		switch($this->TArrParam->GetCount()) {
			case 2: $this->SetDefParam("Mask", ".jpg$");
			case 3: $this->SetDefParam("Cols", 3);
			case 4: $this->SetDefParam("Items", 25);
		}
	}	
	
	
	public function Build()
	{
		if (!$this->CheckParam()) return;

		$this->InitDefParam();

		$this->Data = "";
		$Handler	= "";
		$MacrosName = $this->TArrParam[self::cMacrosName];
		if (TStr::Pos($MacrosName, ":") != -1) {
			$Token1 = new TToken(":");
			$Token1->LoadFromString($MacrosName);
			$MacrosName = $Token1->GetItem(0);	
			$Handler	= $Token1->GetItem(1);
		}
		$this->SetDefParam("Handler", $Handler);
		
		$Macros = $this->TParseFile->LoadMacros($MacrosName);
		if ($Macros === "") {
			$ErrMsg = sprintf("Cant create macros '%s'", $this->TArrParam[self::cMacrosName]);
			$this->LogError($ErrMsg);
			return $this->Data;
		}else{
			$Mask = $Macros->GetConst("cMask");
			if ($Mask !== "") {
				//$this->TArrParam[self::cFileMask] = $Macros->GetConst("cMask");
			}	
		}	
		$Macros->Owner = $this;
		$this->Data = $this->Parse_AsFilesDir($Macros, 
							$this->TArrParam[self::cDirName], 
							$this->TArrParam[self::cFileMask], 
							$this->TArrParam[self::cCols], 
							$this->TArrParam[self::cItems]);
		return $this->Data;		
	}	

	public function AsFile($aFileName) 
	{
	}	
}
?>
