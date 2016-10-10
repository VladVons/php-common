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

class tMacros_Image extends tMacros_FileClass
{
	const	cFileName	= 0, 
			cText		= 1, 
			cAlign		= 2, 
			cWidth		= 3,
			cLink		= 4;
	const	cSyntax		= "FileName|[Text]|[Align]|[Width]|[Link]";
	const	cMask		= "(.jpg$|.png$|.gif$|.bmp$)";
	const	cDescr		= <<<BAR
href="http://jdv-soft.com/index.php?PN=Help&PS=ProjSimpleSiteCreator/Macros_Image"
BAR;

					 

	public function InitDefParam()
	{
		switch($this->TArrParam->GetCount()) {
			case 1: $this->TArrParam[self::cText] = $this->TArrParam[self::cFileName];
			case 2: $this->SetDefParam("Align", "left");
			case 3: $this->SetDefParam("Width", 200);
		}
	}	

	
	public function Build()
	{
		if (!$this->CheckParam()) return;

		$this->InitDefParam();

		$this->Data = $this->Parse_File($this->TArrParam[self::cFileName]);
		return $this->Data;
	}


	public function AsFile($aFileName)
	{
		return THTML::Image($aFileName, 
			$this->TArrParam[self::cText], 
			$this->TArrParam[self::cAlign], 
			$this->	TArrParam[self::cWidth], 
			$this->TArrParam[self::cLink]);
	}
}
?>
