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

include_once("String.php");
include_once("Common.php");


// See PHP SPL for ArrayAccess
class TArrayAPI Implements ArrayAccess 
{
	const cAll = 0, cLeft = 1, cRight = 2, cUpper = 3, cLower = 4;  

	public $ArrData;


	function __construct($aArray = array())
	{
		$this->ArrData = $aArray;  
	}

 
	function __toString()
	{
		return $this->Implode(", ");
	}	

	
	function offsetExists($aIdx) 
	{
		return isset($this->ArrData[$aIdx]);
	}

 
	function offsetGet($aIdx) 
	{
		return $this->GetItem($aIdx);
	}
 
 
	function offsetSet($aIdx, $aValue) 
	{
		$this->SetItem($aIdx, $aValue);
	}
 

	function offsetUnset($aIdx) 
	{
		$this->DeleteItem($aIdx);
	}

 
	function AddItem($aLabel, $aValue)
	{
		if (_DebugLevel > 4) Debug(sprintf('%s->%s->%s(%s)', basename(__file__), __class__, __function__, implode(',', func_get_args())));

		if (isset ($this->ArrData[$aLabel])) {
			throw new MyException("Duplicates in array: $aLabel, $aValue", 1);
		}else{
			$this->SetItem($aLabel, $aValue);
		}
	}


	function GetItem($aLabel, $aDefault = "")
	{
		if (_DebugLevel > 4) Debug(sprintf('%s->%s->%s(%s)', basename(__file__), __class__, __function__, implode(',', func_get_args())));

		if (isset($this->ArrData[$aLabel])) {
			return $this->ArrData[$aLabel];
		}else{
			return $aDefault;
		}	
	}


	function SetItem($aLabel, $aValue)
	{
		if (_DebugLevel > 4) Debug(sprintf('%s->%s->%s(%s)', basename(__file__), __class__, __function__, implode(',', func_get_args())));

		$this->ArrData[$aLabel] = $aValue;
	}

 
	function AddItemToEnd($aValue, $Uniq = false)
	{
		if (_DebugLevel > 4) Debug(sprintf('%s->%s->%s(%s)', basename(__file__), __class__, __function__, implode(',', func_get_args())));

		if (($Uniq == false) || ($Uniq == true && $this->ValueExists($aValue) == false)) {
			$this->ArrData[$this->GetCount()] = $aValue;
		}
	}

 
	function DeleteItem($aLabel)
	{
		unset($this->ArrData[$aLabel]);
	}


	function DeleteItemByValue($aValue)
	{
		$Key = $this->Search($aValue);
		if ($Key !== false) {
			$this->DeleteItem($Key);
		}
	}


	function Merge(TArray $aArray)
	{
		return new TArray(array_merge($this->ArrData, $aArray->ArrData));
	}

 
	function Fill($aStart, $aLength, $aValue)
	{
		return new TArray(array_fill($aStart, $aLength, $aValue));
	}


	function Pad($aCount, $aValue = "")
	{
		return new TArray(array_pad($this->ArrData, $aCount, $aValue)); 
	}


	function Slice($aOffset, $aLength)
	{
		if ($aLength == 0) {
			return new TArray(array_slice($this->ArrData, $aOffset));
		}else{
			return new TArray(array_slice($this->ArrData, $aOffset, $aLength));
		}
	}


	function SetCount($aItems)
	{
		$Items = $this->GetCount();
		if ($aItems > $Items) {
			$this->Pad($aItems, "");
		}elseif ($aItems < $Items) {
			$this->Slice(0, $aItems);
		}
	}


	function GetCount()
	{
		return count($this->ArrData);
	}


	function KeyExists($aLabel)
	{
		return array_key_exists($aLabel, $this->ArrData);
	}

 
	function ValueExists($aValue)
	{
		return in_array($aValue, $this->ArrData);
	}


	function Keys()
	{
		return new TArray(array_keys($this->ArrData));
	}


	function ShowItem($aLabel)
	{
		Show($this->GetItem($aLabel));
	}


	function Search($aValue)
	{
		return array_search($aValue, $this->ArrData);
	}


	function Reset()
	{
		return reset($this->ArrData);
	}


	function Each()
	{
		return each($this->ArrData);
	}


	function Current()
	{
		return current($this->ArrData);
	}


	function Next()
	{
		return next($this->ArrData);
	}


	function Prev()
	{
		return prev($this->ArrData);
	}

 
	function End()
	{
		return end($this->ArrData);
	}

 
	function Key()
	{
		return key($this->ArrData);
	}

 
	function Unique()
	{
		return new TArray(array_unique($this->ArrData));
	}


	function Push($aValue)
	{
		return array_push($this->ArrData, $aValue);
	}


	function Pop()
	{
		return array_pop($this->ArrData);
	}


	function Shuffle($aAssoc = true)
	{
		if ($aAssoc) {
			$Keys = array_keys($this->ArrData);
			shuffle($Keys);
			foreach($Keys as $Key) {
				$New[$Key] = $this->ArrData[$Key];
			}
			return new TArray($New);
		}else{
			return new TArray(shuffle($this->ArrData));
		}	  
	}

 
	function Extract($aPrefix = "Ex")
	{
		return extract($this->ArrData, EXTR_PREFIX_ALL, $aPrefix);
	}

 
	function Clear()
	{
		unset($this->ArrData);
		$this->ArrData = array();
	}


	function SortByValue($aAsc = true, $aMode = SORT_LOCALE_STRING)
	{
		$TArray = new TArray($this->ArrData);
		if ($aAsc) {
			asort($TArray->ArrData, $aMode);
		}else{
			rsort($TArray->ArrData, $aMode);
		}
		return $TArray;
	}


	function SortByLabel($aAsc = true, $aMode = SORT_LOCALE_STRING)
	{
		$TArray = new TArray($this->ArrData);
		if ($aAsc) {
			ksort($TArray->ArrData, $aMode);
		}else{
			krsort($TArray->ArrData, $aMode);
		}
		return $TArray;
	}


	function Split($aString, $aSeparators)
	{
		$this->ArrData = preg_split("/" . $aSeparators . "/", $aString);
		return $this->GetCount();
	}

 
	function Explode($aString, $aSeparator)
	{
		$this->ArrData = explode($aSeparator, $aString);
		return $this->GetCount();
	}


	function Implode($aSeparators)
	{
		return Implode($aSeparators, $this->ArrData);
	}
 

	function GrepValue($aString, $aInvert = false)
	{
		if ($aInvert) {
			$ArrData = preg_grep("/$aString/", $this->ArrData, PREG_GREP_INVERT);
		}else{
			$ArrData = preg_grep("/$aString/", $this->ArrData);
			print_r($ArrData);
		}  
		return new TArray($ArrData);
	}
}

 

class TArray extends TArrayAPI
/////////////////////////////////////////////////////
{
	function GetItemByIndex($aIdx)
	{
		if ($aIdx > $this->GetCount()) {
			throw new MyException("Index out of bound $aIdx", 1);
		}else{
			$this->Reset();
			list($Value) = array_values(array_slice($this->ArrData, aIdx, 1));
			return $Value;
		}
	}

 
	function SearchEx($aString)
	{
		$aString = TStr::ToLower($aString);
		$this->Reset();
		do {
			$Value = current ($this->ArrData);
			$Label = key ($this->ArrData);
			if (TStr::Pos(TStr::ToLower($Value), $aString) != -1) {
				return $Label;
			}
		} while ($this->Next());
		
		return "";
 }

 
	function SearchStringAND($aString)
	{
		$Cnt = 0;
		$this->Reset();
		while (list(, $Value) = $this->Each()) {
			if (TStr::Pos($aString, $Value) != -1) {
				$Cnt++;
			}   
		}
		return $Cnt == $this->GetCount();  
	}
 

	function GrepLabel($aString, $aInvert = false)
	{
		$TArray = new TArray();
		$this->Reset();
		while (list($Label, $Value) = $this->Each()) {
			if ((TStr::Pos($Label, $aString) != -1) xor $aInvert) {
				$TArray->AddItem($Label, $Value); 
			}
		}
		return $TArray;
	}

 
	function DeleteEmpty()
	{
		foreach ($this->ArrData as $i => $NotUse) {
			if (empty($this->ArrData[$i])) {
				unset($this->ArrData[$i]);
			}
		} 
	}

 
	function StripTags()
	{
		$TArray = new TArray(); 
		$this->Reset();
		while (list($Label, $Value) = $this->Each()) {
			$TArray->AddItem($Label, strip_tags($Value));
		}
		return $TArray;
	}


	function UrlEncode()
	{
		$TArray = new TArray(); 
		$this->Reset();
		while (list($Label, $Value) = $this->Each()) {
			$TArray->AddItem($Label, urlencode($Value));
		}
		return $TArray;
	}
 

	function Trim($aMode = self::cAll)
	{
		$TArray = new TArray(); 
		$this->Reset();
		while (list($Label, $Value) = $this->Each()) {
			switch ($aMode) {
				case self::cLeft:	
					$Value = TStr::TrimLeft($Value); break;
				case self::cRight:	
					$Value = TStr::TrimRight($Value); break;
				case self::cAll:	
					$Value = TStr::Trim($Value); break;
			}	
			$TArray->AddItem($Label, $Value);
		}
		return $TArray;
	}


	function PadEx($aMode, $aValue)
	{
		$TArray = new TArray(); 
		$this->Reset();
		while (list($Label, $Value) = $this->Each()) {
			switch ($aMode) {
				case self::cLeft:	
					$Value = $aValue . $Value; break;
				case self::cRight:	
					$Value = $Value  . $aValue; break;
			}	
			$TArray->AddItem($Label, $Value);
		}
		return $TArray;
	}
	
	
	function CharCase($aMode)
	{
		$TArray = new TArray(); 
		$this->Reset();
		while (list($Label, $Value) = $this->Each()) {
			switch ($aMode) {
				case self::cUpper:	
					$Value = TStr::ToUpper($Value); break;
				case self::cLower:	
					$Value = TStr::ToLower($Value); break;
			}	
			$TArray->AddItem($Label, $Value);
		}
		return $TArray;
	}

 
	function ImplodeEx($aMode = self::cAll, $aSeparator = "&", $aBracket = "'")
	{
		$Result = "";
		$this->Reset();
		while (list($Label, $Value) = $this->Each()) {
			switch ($aMode) {
				case self::cLeft:	
					$Result .= $aBracket . $Label . $aBracket . $aSeparator; break;
				case self::cRight:	
					$Result .= $aBracket . $Value . $aBracket . $aSeparator; break;
				default: 			
					$Result .= $Label . "=" . $aBracket . $Value . $aBracket . $aSeparator;
			}	
		}
		return TStr::Sub($Result, 0, -TStr::Length($aSeparator));
	}


	function AddItemInc($aBegin, $aEnd, $aStep = 1, $aPad = 1)
	{
		for ($i = $aBegin; $i <= $aEnd; $i += $aStep) {
			$Pad = TStr::Pad($i, $aPad, "0");
			$this->AddItem($Pad, $Pad);
		}
	}

 
	function GetContext($aMode = self::cRight, $aBeginStr = "", $aEndStr = "")
	{
		$Result = "";		
		$this->Reset();
		while (list($Label, $Value) = $this->Each()) {
			switch($aMode) {
				case self::cAll:	
					$Result .= $aBeginStr . $Label . "=" . $Value . $aEndStr; break;
				case self::cLeft:	
					$Result .= $aBeginStr . $Label . $aEndStr; break;	 
				case self::cRight:	
					$Result .= $aBeginStr . $Value . $aEndStr; break;	 
				default: 
					return "Unknown mode in GetContext('" . $aMode . "')";
			}
		}	 
		return $Result;
	}


	function Show($aMode = self::cAll) {
		$Data = $this->PadEx(self::cRight, "\n")->GetContext($aMode);
		Show($Data);
	}
	

	function SaveToFile($aFileName) {
		return SaveToFile($this->ArrData, $aFileName);
	}


	function LoadFromFile($aFileName) {
		return LoadFromFile($this->ArrData, $aFileName);
	}
}
?>