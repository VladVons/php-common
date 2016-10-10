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

require_once("Common.php");
require_once("Token.php");


class TMySQLBase
/////////////////////////////////////////////////////
{
 protected $Connect, $Result, $Database;
 public $TArrData, $Debug;

	function __construct($aHostName, $aUserName, $aPassword, $aDatabase)
	{
		if ($aHostName == "") {
			$this->Error('Host name is empty');
		}	

		if ($aUserName == "") {
			$this->Error('User name is empty');
		}	

		$this->Database = $aDatabase;

		
		$this->Connect = mysql_pconnect($aHostName, $aUserName, $aPassword ) or $this->Error(sprintf('%s (%s)', __function__, $aHostName));
		mysql_select_db($this->Database, $this->Connect) or $this->Error(sprintf('%s(%s)', __function__, implode(',', func_get_args())));
		
    
		//$this->Connect = new mysqli($aHostName, $aUserName, $aPassword, $aDatabase) or $this->Error(sprintf('%s (%s)', __function__, $aHostName));
                //$this->Connect->set_charset("utf8");
                //$this->Connect->query("SET SQL_MODE = ''");
  

		$this->Debug   = false;
		$this->Result  = NULL;
		$this->TArrData = new TArray();
	}


	function __destruct()
	{
		$this->FreeResult();
	}


	public function TSQLClone($aTSQL)
	{
		$this->Connect = $aTSQL->Connect;
		$this->Result  = NULL;
	}


	public function GetDB()
	{
		return $this->Database;
	}


	public function Query($aString)
	{
		if ($this->Debug) {
			Show($aString);
		}else{	
		    $this->FreeResult();
		    $Result = $this->Result = mysql_query($aString, $this->Connect) or $this->Error(sprintf('%s(%s)', __function__, implode(',', func_get_args())));
		    return $Result;
		}
	}	


	public function FetchAssoc()
	{
		$Arr = mysql_fetch_assoc($this->Result);
		if ($Arr == false) {
			$this->TArrData->Clear();
			return false;
		}else{
			$this->TArrData->ArrData = $Arr;
			return true;
		}
	}


	public function AffectedRows()
	{
		return mysql_affected_rows();
	}


	public function NumFields()
	{
		return mysql_numfields($this->Result);
	}


	public function FieldName($aIndex)
	{
		return mysql_fieldname($this->Result, $aIndex);
	}


	public function FreeResult()
	{
		if ($this->Result != NULL) {
			@mysql_free_result($this->Result);
			$this->Result = NULL;
		}
	}


	public function NumRows()
	{
		return mysql_num_rows($this->Result);
	}


	public function DataSeek($aIndex)
	{
		return mysql_data_seek($this->Result, $aIndex);
	}


	public function Error($aFunc)
	{
		Error(sprintf('%s->%s->%s. MySQL: %s', basename(__file__), __class__, $aFunc, mysql_error()));
	}


	public function InsertID()
	{
		return mysql_insert_id();
	}
}



class TMySQL extends TMySQLBase
/////////////////////////////////////////////////////
{
	public function EscStr($aString)
	{
		return mysql_real_escape_string($aString);
	}


	public function GetItem($aName)
	{
		return $this->TArrData->GetItem($aName);
	}


	public function GetCount($aTable, $aWhere = "1")
	{
		$String =	"SELECT 
						COUNT(*) AS Count
					FROM   
						$aTable
					WHERE  
						$aWhere";
		$this->Query($String);
		$this->FetchAssoc();
		$this->FreeResult();
		return $this->GetItem("Count");
	}


	public function InsertArray($aTable, $aTArray)
	{
		$aTArray = $aTArray->StripTags();
		$QueryStr = sprintf("INSERT INTO %s (%s) VALUES (%s)", $aTable, 
				$aTArray->ImplodeEx(TArray::cLeft, ", ", ""), 
				$aTArray->ImplodeEx(TArray::cRight, ", ", "'"));
		$this->Query($QueryStr);
		$this->FreeResult(); 
	}


	public function GetUpdateStr($aTable, $aTArray, $aTail)
	{
		$StrSet = "";

		$aTArray->Reset();
		while (list($Label, $Value) = $aTArray->Each()) {
		    switch (gettype($Value)) {
			case "string":
			    if (trim($Value) != "")
			      $StrSet .= sprintf("%s='%s', ", $Label, $this->EscStr($Value));
	    		    break;
			case "integer":
			      $StrSet .= sprintf("%s=%d, ", $Label, $Value);
	    		    break;
			case "double":
			      $StrSet .= sprintf("%s=%f, ", $Label, $Value);
	    		    break;
			default:
			    Error("unknown type $Label, $Value");  
		    } 
		}
		
		return "UPDATE $aTable SET " . rtrim($StrSet, ", ") . " " . $aTail; 
	}

	public function Injection($aGET)
	{
		$TArrGet = new TArray($aGET);
		$TArrGet = $TArrGet->UrlEncode();

		$TArrDeny = new TArray(array("select", "delete", "update", "insert", "join", "union",
								"from", "where", "order by", "having",
								"like", ")", "(", "*", "-", "+"));
		$TArrDeny->Reset();
		while (list(, $Value) = $TArrDeny->Each()) {
			$Key = $TArrGet->SearchEx($Value);
			if ($Key != "") {
				Error("SQL injection '$Value' detected in URL key '$Key'");
			}
		}
	}


	public function QueryText($aValue)
	{
		$Token1 = new TToken("\n");
		$Token1->LoadFromString($aValue);

		$Result  = 0;
		$String1 = "";
		$Count = $Token1->TArrData->GetCount();
		for ($i = 0; $i < $Count; $i++) {
			$Line = TStr::Trim($Token1->TArrData->GetItem($i));
			$Len = TStr::Length($Line);
			if ($Line[0] == '#') {
				continue;
			}elseif ($Line[$Len - 1] == ';') {
				$Result++;
				$String1 = $String1 . " " . TStr::Sub($Line, 0, $Len - 1);
				$this->Query($String1);
				$String1 = "";
			}else{
				$String1 = $String1 . " " . $Line;
			}
		}

		if (TStr::Trim($String1) != "") {
			$this->Query($String1);
		}
	
		return $Result;
	}


	public function QueryFile($aFileName)
	{
		$TFile1 = new TFile();
		$TFile1->Open($aFileName, "r");
		$this->QueryText($TFile1->Read());
		$TFile1->Close();
	}

}

?>