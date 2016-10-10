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

class tMacros_Table extends tMacros_BaseClass
{
	const	cItem	= 0, 
			cCols	= 1, 
			cTable	= 2, 
			cTr 	= 3, 
			cTd		= 4,
			cDelim	= 5;
	const	cSyntax = "Item|[Cols]|[Table][Tr][Td][Delim]";
	const	cDescr		= <<<BAR
href="http://jdv-soft.com/index.php?PN=Help&PS=ProjSimpleSiteCreator/Macros_Table"
BAR;
	
	
	public function InitDefParam()
	{
		switch($this->TArrParam->GetCount()) {
			case 1: $this->SetDefParam("Cols", 1);
			case 2: $this->SetDefParam("Table", "border='1'");
			case 3: $this->SetDefParam("Tr", "valign='top'");
			case 4: $this->SetDefParam("Td", "valign='top'");
			case 5: $this->SetDefParam("Delim", "[|\n]");
		}
	}

	
	public function Build()
	{
		if (!$this->CheckParam()) return;

		$this->InitDefParam();

		$this->Data = "";	
		$Cols = $this->TArrParam[self::cCols];
		$Token1 = new TToken($this->TArrParam[self::cDelim]);
		$Cnt = $Token1->LoadFromStringPReg($this->GetItem($this->TArrParam[self::cItem]));
		$Table1 = new TTable($Cols, $Cnt / $Cols, $this->TArrParam[self::cTable]);
		//$Table1->SetTR($this->pTableTrTop, $this->pTableTr0, $this->pTableTr1); 
		//$Table1->SetTD($this->pTableTd->Slice(2, 0)); 
		$Table1->Build($Token1->TArrData);
		$this->Data = $Table1->GetPrintOut();

		return $this->Data;	
	}
}
?>
