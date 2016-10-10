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

require_once(_DirCommonLib . "/Dir.php");


abstract class TDbBase
/////////////////////////////////////////////////////
{
	protected $TLang, $Record, $SortField, $SortAsc, $FieldID, $CurID;

	abstract protected function GetName();
	abstract public function GetAlias();
	abstract public function GetCount();

	abstract public function SetCurItem($aID);
	abstract public function AddItem();
	abstract public function DeleteItem();
	abstract public function FindItem($aID);
	abstract public function UpdateItem();
	abstract public function FilterItems($aFieldName, $aSearchStr);

	public function __construct(TLang $aTLang)
	{
		$this->TLang   = $aTLang;
		$this->FieldID = "ID";
		$this->SetSort("Name", true);
	}
 
	
	public function GetRecord()
	{
		return $this->Record;
	}


	public function GetLang()
	{
		return $this->TLang;
	}
	
 
	public function GetImageDir()
	{
		return _DirUser . "/Image/" . $this->GetAlias();
	}


	public function SetSort($aSortField, $aSortAsc = true)
	{
		$this->SortField = $aSortField;
		$this->SortAsc   = $aSortAsc;
	}


	public function GetSessionID()
	{
		return $_SESSION[$this->GetAlias() . $this->FieldID]; 
	}


	public function SetSessionID($aID)
	{
		$_SESSION[$this->GetAlias() . $this->FieldID] = $aID;
	}


	public function Error($aFunc)
	{
		Error(sprintf('%s->%s->%s. MySQL: %s', basename(__file__), __class__, $aFunc, mysql_error()));
	}
}
?>
