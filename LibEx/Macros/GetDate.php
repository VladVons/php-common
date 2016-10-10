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

class tMacros_GetDate extends tMacros_BaseClass
{
	const	cFormat	= 0;
	const	cSyntax = "[DateFormat]";
	const	cDescr	= <<<BAR
Gets data according to 'DateFormat'

href="http://jdv-soft.com/index.php?PN=Help&PS=ProjSimpleSiteCreator/Macros_GetDate"
BAR;


	public function InitDefParam()
	{
		switch($this->TArrParam->GetCount()) {
			case 0: $this->SetDefParam("Format", "Y-m-d");
		}
	}	
	
	
	public function Build()
	{
		if (!$this->CheckParam()) return;

		$this->InitDefParam();

		$this->Data = date($this->TArrParam[self::cFormat]);
		return $this->Data;
	}
}
?>
