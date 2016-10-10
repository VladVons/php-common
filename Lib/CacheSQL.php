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


abstract class TCacheSQLBase
/////////////////////////////////////////////////////
{
    private $MaxSize;
    protected $TSQL, $TArray, $Table;

    abstract public function GetQuery();

    function __construct($aTSQL, $aTable) 
    {
	$this->TSQL  = $aTSQL;
	$this->Table = $aTable;

	$this->TArray = new TArray();
	$this->MaxSize = 1000;
    }

    function __destruct()
    {
	$this->Flush();
    }

    function SetSize($aValue) 
    {
	$this->Flush();
	$this->MaxSize = $aValue;
    }

    function Add($aValue)
    {
	$this->TArray->AddItemToEnd($aValue);
	if ($this->TArray->GetCount() >= $this->MaxSize) {
	    $this->Flush();
	}
    }

    function Flush()
    {
	if ($this->TArray->GetCount() > 0) {
	    $Query = $this->GetQuery();
	    //printf("%s <br>\n", $Query);
	    $this->TArray->Clear();
	    $this->TSQL->Query($Query);
	    $this->TSQL->FreeResult();
	}
    }
}


class TCacheSQLInsert extends TCacheSQLBase
/////////////////////////////////////////////////////
{
    protected $Fields;

    function __construct($aTSQL, $aTable, $aFields)
    {
	parent::__construct($aTSQL, $aTable);
	$this->Fields = $aFields;
    }

    function GetQuery()
    {
	$Values = "(" . $this->TArray->Implode("),(") . ")";
	return sprintf("INSERT INTO %s (%s) VALUES %s", $this->Table, $this->Fields, $Values);
    }
}

class TCacheSQLInsertDuplicate extends TCacheSQLInsert
/////////////////////////////////////////////////////
{
    protected $FieldsDup;

    function __construct($aTSQL, $aTable, $aFields, $aFieldsDup)
    {
	parent::__construct($aTSQL, $aTable, $aFields);
	$this->FieldsDup = $aFieldsDup;
    }

    function GetQuery()
    {
	return sprintf("%s ON DUPLICATE KEY UPDATE %s", parent::GetQuery(), $this->FieldsDup);
    }
}

?>
