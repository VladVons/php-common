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

// OS Commerce handler

require_once(_DirCommonLib . "/MySQL.php");
require_once(_DirCommonLibEx . "/DB/RefBase.php");



abstract class TRefBase_SQL extends TRefBase
/////////////////////////////////////////////////////
{
	Const cInsert = 0, cUpdate = 1;

	protected $TSQL, $NameDB, $SortOrder, $TArrXlatItem, $TArrXlatGroup;
	
	abstract protected function GetQueryItemSelect($aWhere = "");
	abstract protected function GetQueryGroupSelect($aWhere = ""); 
	

	function __construct(TMySQL $aTSQL, TLang $aTLang)
	{
		parent::__construct($aTLang); 

		$this->TSQL	         = $aTSQL;
		$this->SortOrder     = $this->GetQuerySortOrder();
		$this->TArrXlatItem  = $this->GetXlat($this->GetQueryItemSelect());
		$this->TArrXlatGroup = $this->GetXlat($this->GetQueryGroupSelect());
	}


	public function GetQueryFieldID()
	{
		return $this->TArrXlatItem->GetItem($this->FieldID);
	}


	protected function GetQuerySortOrder()
	{
		return ($this->SortAsc ? "ASC" : "DESC");
	}


	protected function GetXlat($aQuery)
	{
		$Result = new TArray();	
		if (preg_match_all("/(.*) AS (\w*)/i", $aQuery, $OutArr) !== false) {
			for($i = 0; $i < sizeof($OutArr[0]); $i++) {
				$Result->SetItem(TStr::Trim($OutArr[2][$i]), TStr::Trim($OutArr[1][$i]));
			}
		}
		
		return $Result;
	}


	protected function GetQueryXlat($aMode, $aNameDB, TArray $aTArray)
	{
		$aTArray = $aTArray->StripTags();

		$End    = ",";
		$Fields = "";
		$Values = "";
		$aTArray->Reset();
		while (list($Label, $Value) = $aTArray->Each()) {
			if ($this->TArrXlatItem->KeyExists($Label) && $Label != $this->FieldID) {
				$FieldDB = $this->TArrXlatItem->GetItem($Label);
				if (TStr::Length($FieldDB) > 2 && TStr::Pos($FieldDB, "(") == -1) {
					$Field = TStr::Replace($FieldDB, $aNameDB . ".", "");
					switch ($aMode) {
						case self::cInsert:	
							$Fields .= $Field . $End;
							$Values .= "'" . $Value . "'" . $End;
							break;
						case self::cUpdate:	
							$Fields .= $Field . "=" . "'" . $Value . "'" . $End;
							break;
					}	
				}
			}	
		}

		switch ($aMode) {
			case self::cInsert:	
				$Result = sprintf("INSERT INTO $aNameDB (%s) VALUES (%s)", 
								TStr::Sub($Fields, 0, -TStr::Length($End)),
								TStr::Sub($Values, 0, -TStr::Length($End)));
					break;
			case self::cUpdate:	
				$Result = sprintf("UPDATE $aNameDB SET %s", TStr::Sub($Fields, 0, -TStr::Length($End)));
					break;
			default:
				$this->Error(sprintf('%s(%s). Unknowm mode: $aMode', __function__, implode(',', func_get_args())));
		}	
		
		return $Result;	 
	}
	
	
	protected function GetIDsQuery($aQuery)
	{
		$Result = new TArray();
		$this->TSQL->Query($aQuery);
		while ($this->TSQL->FetchAssoc()) {
			$Result->AddItemToEnd($this->TSQL->GetItem($this->FieldID));
		}

		return $Result;
	}


	protected function SetCurItemQuery($aQuery)
	{
		$this->TSQL->Query($aQuery);
		$Result = $this->TSQL->FetchAssoc();
		if ($Result) {
			$this->Record->TArrData = $this->TSQL->TArrData;
			$this->CurID = $this->TSQL->TArrData->GetItem($this->FieldID);
		}	
		
		return $Result;
	}


	public function SetCurItem($aID)
	{
		if (Empty($aID)) { 
			return false;
		}	
		
		if ($this->IsGroup($aID)) {
			$QueryStr = $this->GetQueryGroupSelect(sprintf("AND %s='$aID'", $this->TArrXlatGroup->GetItem($this->FieldID)));
		}else{
			$QueryStr = $this->GetQueryItemSelect(sprintf("AND %s='$aID'", $this->GetQueryFieldID()));
		}
			
		return $this->SetCurItemQuery($QueryStr);
	}

	
	public function FilterItems($aField, $aSearch)
	{
		if (!$this->TArrXlatItem->KeyExists($aField)) {
				$this->Error(sprintf('%s(%s). Field not found: $aField', __function__, implode(',', func_get_args())));
		}	

		$FieldDB   = $this->TArrXlatItem->GetItem($aField);
		$Condition = "";

		$Token = new TToken(" ");
		$Token->LoadFromString(TStr::ToLower($aSearch));
		$Token->TArrData->DeleteEmpty();
		if ($Token->TArrData->GetCount() > 0) {
			$Token->TArrData->Reset();
			while (list($Lebel, $Value) = $Token->TArrData->Each()) {
				$Condition .= "AND LOWER($FieldDB) LIKE '%$Value%' ";
			}
		}  

		$QueryStr  = $this->GetQueryItemSelect($Condition);
		return $this->GetIDsQuery($QueryStr);
	}
	

	public function GetCount()
	{
		return $this->TSQL->GetCount($this->GetName());
	}


	public function FindItem($aID)
	{
		return (bool) $this->TSQL->GetCount($this->GetName(), $this->GetQueryFieldID() . "='$aID'");
	}


	public function AddItem()
	{
		$QueryStr = $this->GetQueryXlat(self::cInsert, $this->GetName(), $this->Record->TArrData); 
		if ($this->TSQL->Query($QueryStr)) {
			$LastID = $this->TSQL->InsertID();
			$this->Record->TArrData->SetItem($this->GetQueryFieldID(), $LastID);
			$this->CurID = $LastID;
		}
		$this->TSQL->FreeResult(); 
	}


	public function UpdateItem()
	{
		$QueryStr = $this->GetQueryXlat(self::cUpdate, $this->GetName(), $this->Record->TArrData) . 
					sprintf(" WHERE %s='%s'", $this->GetQueryFieldID(), $this->CurID); 
		$this->TSQL->Query($QueryStr);
		$this->TSQL->FreeResult(); 
	}


	public function DeleteItem()
	{
		$QueryStr = sprintf("DELETE FROM %s WHERE %s='%s'", $this->GetName(), $this->GetQueryFieldID(), $this->CurID);
		$this->TSQL->Query($QueryStr);
		$this->TSQL->FreeResult(); 
	}


	public function TreeNode($aFormatJS)
	{
		$QueryStr = $this->GetQueryGroupSelect();  
		$this->TSQL->Query($QueryStr);
		$Array = new TArray();  
		while ($this->TSQL->FetchAssoc()) {
			$ID   = $this->TSQL->GetItem($this->FieldID);
			$Name = TStr::EnQuotation($this->TSQL->GetItem("Name"));
			$Link = sprintf($aFormatJS, $ID, $this->TSQL->GetItem("ParentID"), $Name, $ID, $ID);
			$Array->AddItemToEnd($Link);
		}
  
		return $Array;
	}
}
?>
