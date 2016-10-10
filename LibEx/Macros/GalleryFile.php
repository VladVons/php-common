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

class tMacros_GalleryFile extends tMacros_FileClass
{
	const	cMacrosName = 0, 
			cFileName	= 1, 
			cCols		= 2, 
			cItems		= 3;
	const	cSyntax		= "MacrosName|FileName|[Cols]|[Items]";
	const	cDescr		= <<<BAR
href="http://jdv-soft.com/index.php?PN=Help&PS=ProjSimpleSiteCreator/Macros_GalleryFile"
BAR;
	

	public function InitDefParam()
	{
		switch($this->TArrParam->GetCount()) {
			case 2: $this->SetDefParam("Cols", 3);
			case 3: $this->SetDefParam("Items", 25);
		}
	}	
	
	
	public function Build()
	{
		if (!$this->CheckParam()) return;

		$this->InitDefParam();
		$this->Data = "";	
		$Macros = $this->TParseFile->LoadMacros($this->TArrParam[self::cMacrosName]);
		if ($Macros === "") {
			$ErrMsg = sprintf("Cant createLoadMacros macros '%s'", $this->TArrParam[self::cMacrosName]);
			$this->LogError($ErrMsg);
			return $this->Data;
		}else{
			$FileName = $this->SearchFile($this->TArrParam[self::cFileName]);
			if ($FileName != "") { 
				$Token1 = new TToken("\n");
				$Token1->LoadFromFile($FileName);
				$Token1->TArrData->DeleteEmpty();
				$this->Data = $this->Parse_AsFiles($Macros, 
							$Token1->TArrData, 
							$this->TArrParam[self::cCols], 
							$this->TArrParam[self::cItems]);
			}	
		}	

		return $this->Data;		
	}	
	
	
	public function AsFile($aFileName) 
	{
	}	
}
?>
