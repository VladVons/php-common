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

require_once("MySQL.php");
require_once("Array.php");



class TArraySQLBase extends TArray
/////////////////////////////////////////////////////
{
	protected $TSQL;
 

	function __construct($aTSQL)
	{
		parent::__construct();
		$this->TSQL = $aTSQL;
	}


	public function Debug($aValue)
	{
		$this->TSQL->Debug = $aValue;
	}


	public function Query($aString)
	{
		$this->TSQL->Query($aString);
	}


	public function FreeResult()
	{
		$this->TSQL->FreeResult();
	}
 

	public function Compare($aTArray)
	{
		$Result = 0;
		$aTArray->Reset();
		while (list($Field, $Value) = $aTArray->Each()) {
			$SQLValue = $this->GetItem($Field);
			if ($SQLValue != $Value) {
				$Result++;
			}  
		}
		
		return $Result;
	} 


	public function BuildFromRow($aRowNo)
	{
		$this->Clear();

		$Rows = $this->TSQL->NumRows();
		if ($Rows > 0 && $aRowNo <= $Rows) {
			$this->TSQL->DataSeek($aRowNo);
			$this->TSQL->FetchAssoc();

			$Fields = $this->TSQL->NumFields();
			for ($i = 0; $i < $Fields; $i++) {
				$FieldName  = $this->TSQL->FieldName($i);
				$FieldValue = $this->TSQL->GetItem($FieldName);
				$this->AddItem($FieldName, $FieldValue);
			}
		}
	}
 

	public function BuildFromRowGroup()
	{
		$TArrayResult = new TArray();
		$NumRows = $this->TSQL->NumRows();
		for ($i = 0; $i < $NumRows; $i++) {
			$this->BuildFromRow($i);
			$TArrayResult->AddItem($i, $this);
		}
		
		return $TArrayResult;
	} 


	public function InsertOne($aTable, $aTArray)
	{
		$aTArray = $aTArray->StripTags();
		$String = sprintf("INSERT INTO %s (%s) VALUES (%s)", $aTable, $aTArray->ImplodeLabel(", "), $aTArray->ImplodeValue(", "));
		$this->TSQL->Query($String);
		$this->TSQL->FreeResult(); 
	}


	public function InsertGroup($aTable, $aTArray)
	{
		if ($aTArray->GetCount() == 0) return;
  
		$TArray1 = $aTArray->GetItemByIndex(0);
		$SQLFields = $TArray1->ImplodeLabel(", ");

		$SQLValues = "";
		$aTArray->Reset();
		while (list($Field, $TArray1) = $aTArray->Each()) {
			$TArray1 = $TArray1->StripTags();
			$SQLValue = $TArray1->ImplodeValue(", ");
			$SQLValues .= ($SQLValues == "" ? "" : ", ") . "(" . $SQLValue . ")";
		}
		
		$String = sprintf("INSERT INTO %s (%s) VALUES %s", $aTable, $SQLFields, $SQLValues);
		$this->TSQL->Query($String);
		$this->TSQL->FreeResult(); 
	}


	public function Update($aTable, $aTArray, $aWhere)
	{
		$aTArray = $aTArray->StripTags();
		$String = sprintf("UPDATE %s SET %s %s", $aTable, $aTArray->Implode(", ", "'"), $aWhere);
		$this->Query($String);
		$this->FreeResult(); 
	}
}



class TArraySQL extends TArraySQLBase
/////////////////////////////////////////////////////
{
	function __construct($aTSQL)
	{
		parent::__construct($aTSQL);
	}


	public function BuildOne($aLabel, $aValue)
	{
		$this->Clear();
		while ($this->TSQL->FetchAssoc()) {
			$this->AddItem($this->TSQL->GetItem($aLabel), $this->TSQL->GetItem($aValue));
		}
	}
 

	public function BuildGroup($aLabel, $aValue, $aGroup)
	{
		$this->Clear();
		$Rows = $this->TSQL->NumRows();
		for ($i = 0; $i < $Rows;) {
			$this->TSQL->DataSeek($i);
			$this->TSQL->FetchAssoc();
			$Group = $this->TSQL->GetItem($aGroup);
	
			$TArray_1 = new TArray();
			for (; $i < $Rows && $Group == $this->TSQL->GetItem($aGroup); $i++) {
				$Label = $this->TSQL->GetItem($aLabel);
				$Value = $this->TSQL->GetItem($aValue);
				$TArray_1->AddItem($Label, $Value);
	
				$this->TSQL->FetchAssoc();
			}
			$this->AddItem($Group, $TArray_1);
		}
	}

	
	public function Build($aString, $aLabel, $aValue)
	{
		$this->TSQL->Query($aString);
		$this->BuildOne($aLabel, $aValue);
		$this->TSQL->FreeResult();
	}
}

?>