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

class tMacros_MAudio extends tMacros_FileClass
{
	const	cFileName	= 0, 
			cSkin		= 1, 
			cWidth		= 2;
	const	cSyntax		= "FileName|[Skin]|[Width]";
	const	cMask		= "(.mp3$|.wav$)";
	const	cDescr		= <<<BAR
href="http://jdv-soft.com/index.php?PN=Help&PS=ProjSimpleSiteCreator/Macros_MAudio"
BAR;

					 

	public function InitDefParam()
	{
		switch($this->TArrParam->GetCount()) {
			case 1: $this->SetDefParam("Skin", "uppodStyleA1.txt");
			case 2: $this->SetDefParam("Width", 300);
		}
	}	
					 
					 
	public function Build()
	{
		return $this->BuildFile($this->TArrParam[self::cFileName]);
	}


	public function AsFile($aFileName)
	{
		return THTML::MAudio($aFileName, 
							$this->TArrParam[self::cSkin], 
							$this->TArrParam[self::cWidth]);
	}
}
?>
