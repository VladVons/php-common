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

class tMacros_Box1 extends tMacros_BaseClass
{
	const	cTitle	= 0, 
			cBody	= 1, 
			cAlign 	= 2, 
			cWidth	= 3;
	const	cSyntax = "Title|Body|[Align]|[Width]";
	const	cDescr	= <<<BAR
href="http://jdv-soft.com/index.php?PN=Help&PS=ProjSimpleSiteCreator/Macros_Box"
BAR;
	
	const cTable = <<<BAR
<div class="title_box">%s</div>
<div class="border_box">
	%s
</div>
BAR;

					 
	public function InitDefParam()
	{
		switch($this->TArrParam->GetCount()) {
			case 2: $this->SetDefParam("Align", "Center");
			case 3: $this->SetDefParam("Width", "100%");
		}
	}	

	
	public function Build()
	{
		if (!$this->CheckParam()) return;

		$this->InitDefParam();
		
		$this->Data = sprintf(TStr::Replace(self::cTable, "\n", ""), 
				$this->TArrParam[self::cWidth],
				$this->TArrParam[self::cTitle],
				$this->TArrParam[self::cAlign],
				$this->TArrParam[self::cBody]);
		return $this->Data;	
	}
}
?>
