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

class tMacros_Captcha extends tMacros_BaseClass
{
	const	cText	= 0, 
			cHandler= 1;
	const	cSyntax = "Text|[Handler]";
	const	cDescr	= <<<BAR
Draws graphic captcha
href="http://jdv-soft.com/index.php?PN=Help&PS=ProjSimpleSiteCreator/Macros_Captcha"
BAR;

					 
	public function InitDefParam()
	{
		switch($this->TArrParam->GetCount()) {
			case 1: $this->SetDefParam("Handler", '<img src="Actions.php?Action=NoSpam&Mode=Txt&Text=%s">');
		}
	}	
					 
	
	public function Build()
	{
		if (!$this->CheckParam()) return;

		$this->InitDefParam();

		$this->Data = sprintf($this->TArrParam[self::cHandler], $this->TArrParam[self::cText]);
		return $this->Data;
	}
}
?>
