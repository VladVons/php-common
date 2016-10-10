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

class tMacros_Replace extends tMacros_BaseClass
{
	const	cString	= 0, 
			cSearch = 1, 
			cReplace= 2;
	const	cSyntax	= "String|Search|Replace";
	const	cDescr	= <<<BAR
href="http://jdv-soft.com/index.php?PN=Help&PS=ProjSimpleSiteCreator/Macros_Replace"
BAR;


	public function InitDefParam()
	{
	}	

	
	public function Build()
	{
		//print("S:" . $this->TArrParam[self::cSearch] . ", R:" . $this->TArrParam[self::cReplace]);

		if (!$this->CheckParam()) return;

		$this->InitDefParam();

		$TArrSearch = new TArray();
		$TArrSearch->Split($this->TArrParam[self::cSearch], "\n");
		$TArrReplace = new TArray();
		$TArrReplace->Split($this->TArrParam[self::cReplace], "\n");
		//print("S:" . $this->TArrParam[self::cSearch] . ", R:" . $this->TArrParam[self::cReplace]);
		
		if ($this->TArrParam[self::cString] == "") {
			// get PHP buffer, replace there string, write it back
			$CurBuffer = ob_get_contents();
			ob_end_clean();	
			$NewBuffer = TStr::ReplaceReg($CurBuffer, $TArrSearch->ArrData,	$TArrReplace->ArrData);
			print($NewBuffer);
			$this->Data = " ";	
		}else{
			$this->Data = TStr::ReplaceReg($this->TArrParam[self::cString],	$TArrSearch->ArrData, $TArrReplace->ArrData);
		}	
		return $this->Data;	
	}
}
?>
