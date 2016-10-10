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

//include_once("Common.php");
class TStr
///////////////////////////////////////////////////////////////////////////////
{
	static public function Length($aValue, $aTrim = false) 
	{
		if ($aTrim) {
			return strlen(trim($aValue));
		}else{
			return strlen($aValue);
		}	 
	}

	
	static public function Trim($aValue, $aChars = " \t\n\r\0\x0B") 
	{
		return trim($aValue, $aChars);
	}

 
	static public function TrimRight($aValue) 
	{
		return rtrim($aValue);
	}

 
	static public function TrimLeft($aValue) 
	{
		return ltrim($aValue);
	}	
 
 
	static public function TrimInside($aValue) 
	{
		return TStr::ReplaceReg($aValue, "/ +/", " ");
	}
	
 
	static public function Sub($aValue, $aStart, $aLen = NULL) 
	{
		if ($aLen === NULL) {
			return substr($aValue, $aStart);
		}else{
			return substr($aValue, $aStart, $aLen);
		}
	}
 

	static public function nCmp($aStr1, $aStr2, $aCnt) 
	{
		return strncmp($aStr1, $aStr2, $aCnt);
	}

 
	static public function Cmp($aStr1, $aStr2) 
	{	
		return strncmp($aStr1, $aStr2, min(strlen($aStr1), strlen($aStr2)));
	}

 
	static public function Pos($aValue, $aFind, $aOfst = 0) 
	{
		$Pos1 = strpos($aValue, $aFind, $aOfst);
		return ($Pos1 === false ? -1 : $Pos1);
	}

 
	static public function PosR($aValue, $aFind, $aOfst = 0) 
	{
		$Pos1 = strrpos($aValue, $aFind, $aOfst);
		return ($Pos1 === false ? -1 : $Pos1);
	}

 
	static public function SubPosR($aValue, $aFind, $aShift = 0) 
	{
		$Result = "";
		$Pos = TStr::PosR($aValue, $aFind);
		if ($Pos != -1) {
			return TStr::Sub($aValue, $Pos + $aShift, TStr::Length($aValue)); 
		}
		return $Result;
	}

 
	static public function PosCnt($aValue, $aFind, $aCnt) 
	{
		for ($i = 0 ; $i < $aCnt; $i++) {
			$Pos1 = TStr::Pos($aValue, $aFind, $Pos1 + 1);
			if ($Pos1 == -1) {
				break;
			}
		}
		return $Pos1;
	}
 

	static public function SubCount($aValue, $aFind, $aOfst = 0) 
	{
		return substr_count($aValue, $aFind, $aOfst);
	}

 
	static public function ToLower($aValue) 
	{
		return strtolower($aValue);
	}


	static public function ToUpper($aValue) 
	{
		return strtoupper($aValue);
	}
 
 
	static public function Left($aValue, $aIdx) 
	{
		return substr($aValue, 0, $aIdx);
	}
 

	static public function LeftEnd($aValue, $aStrEnd) 
	{
		return substr($aValue, 0, TStr::Length($aValue) - TStr::Length($aStrEnd));
	}

 
	static public function LeftWords($aValue, $aLimit) 
	{	
		$Arr1 = preg_split("/[ ]+/", $aValue , $aLimit + 1);
		return implode(" ", array_slice($Arr1, 0, $aLimit));
	}

 
	static public function Right($aValue, $aIdx) 
	{
		$Len = TStr::Length($aValue);
		return substr($aValue, $Len - $aIdx, $aIdx);
	}
	
 
	static public function ReplaceReg($aValue, $aSearch, $aReplace) 
	{
		return preg_replace($aSearch, $aReplace, $aValue);
	}

 
	static public function Replace($aValue, $aSearch, $aReplace) 
	{
		return str_replace($aSearch, $aReplace, $aValue);
	}

 
	static public function Reverse($aValue) 
	{
		return strrev($aValue);
	}

 
	static public function Pad($aValue, $aLen, $aPadStr, $aMode = STR_PAD_LEFT) 
	{
		return str_pad ($aValue, $aLen, $aPadStr, $aMode);
	}
 
 
	static public function Repeat($aValue, $aLen) 
	{
		return str_repeat ($aValue, $aLen);
	}

 
	static public function ExpandR($aValue, $aSearch, $aReplace) 
	{
		$Pos1 = TStr::PosR($aValue, $aSearch);
		if ($Pos1 != -1) {
			return TStr::Sub($aValue, 0, $Pos1) . $aReplace . TStr::Sub($aValue, $Pos1);
		}else{
			return $aValue;
		}		
	}	

	
	static function GetURL($aString)
	{
		$Result = "";
		$Cnt = preg_match_all("/(href|src)=\"?'?([^ \"']+)/i", $aString, $Arr);
		if ($Cnt != 0) {
			for ($i = 0; $i < count($Arr[2]); $i++) {
				$Result .= $Arr[2][$i] . " ";
			}
		}	
		return $Result;
	}


	static function BrainShuffle($aString)
	{
		$Len = strlen($aString) - 1;
		for ($i = 1; $i < $Len; $i++) {
			$Idx = rand(1, $Len -1);
			
			$Tmp = $aString[$i];
			$aString[$i] = $aString[$Idx];
			$aString[$Idx] = $Tmp;
		}
		return $aString;
	}


	static public function EnQuotation($aString) 
	{
		return str_replace(array("'", '"'), array("`", '`'), $aString);
	}


	static public function TranCyrToLat($aString)
	{
	    $Arr1  = array('а', 'б', 'в', 'г', 'д', 'е', 'ё', 'ж', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ч', 'ш', 'щ', 'ъ', 'ы', 'ь', 'э', 'ю', 'я', 'і', 'є', 'ї',
		           'А', 'Б', 'В', 'Г', 'Д', 'Е', 'Е', 'Ж', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ч', 'Ш', 'Щ', 'Ъ', 'Ы', 'Ь', 'Э', 'Ю', 'Я', 'i', 'ye','yi');
	    $Arr2  = array('a', 'b', 'v', 'g', 'd', 'e', 'io','zh','z', 'i', 'y', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h' ,'ts','ch','sh','sc','a', 'i', 'y', 'e', 'yu','ya','І', 'Є', 'Ї',
			   'A', 'B', 'V', 'G', 'D', 'E', 'Io','Zh','Z', 'I', 'Y', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F' ,'H' ,'Ts','Ch','Sh','Sc','A' ,'I' ,'Y' ,'E', 'Yu','Ya','I', 'Ye','Yi');
	    return TStr::Replace($aString, $Arr1, $Arr2);
	}

        
	static public function TranPunktToUrl($aString)
        {
          $Arr1 = array('!', '*', '(', ')', ';', ':', '@', '&', '=', '+', '$', ',', '/', '?', '%', '#', '[', ']', '"', "'", ' ');
          $Arr2 = array('_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_', '_');
	  return TStr::Replace($aString, $Arr1, $Arr2);
	}


	static public function StripNonPrintable($aString)
        {
	    //return preg_replace( '/[^[:print:]]/', ' ',$aString);
	    //return preg_replace("/[\x01-\x08\x0B\x0C\x0E-\x1F\x80-\x9F]/u", " ", $aString);

	    $Arr1 = array("\x00", "\x01", "\x02", "\x03", "\x04", "\x05", "\x06", "\x07", "\x08", "\x0B", "\x0C", "\x0E", "\x0F", "\x10", "\x11", "\x12", "\x13", "\x14", "\x15", "\x16", "\x17", "\x18", "\x19", "\x1A");
	    $Arr2 = array(" ",    " ",    " ",    " ",    " ",    " ",    " ",    " ",    " ",    " ",    " ",    " ",    " ",    " ",    " ",    " ",    " ",    " ",    " ",    " ",    " ",    " ",    " ",    " ");
	    return TStr::Replace($aString, $Arr1, $Arr2);
	}
}


class TString
///////////////////////////////////////////////////////////////////////////////
{
	public $Data;


	function __construct($aValue = "")
	{
		$this->Data = $aValue;  
	}
	
	
	function __toString()
	{
		return $this->Data;
	}	
	
	
	public function Length($aTrim = false) 
	{
		return TStr::Length($this->Data, $aTrim);
	}


	public function Trim() 
	{
		return new TString(TStr::Trim($this->Data));
	}

 
	public function TrimRight() 
	{
		return new TString(TStr::TrimRight($this->Data));
	}

 
	public function TrimLeft() 
	{
		return new TString(TStr::TrimLeft($this->Data));
	}	
 
 
	static public function TrimInside() 
	{
		return new TString(TStr::TrimInside($this->Data));
	}


	public function Sub($aStart, $aLen = NULL) 
	{
		return new TString(TStr::Sub($this->Data, $aStart, $aLen));
	}


	public function nCmp(TString $aTStr, $aCnt) 
	{
		return TStr::nCmp($this->Data, $aTStr->Data, $aCnt);
	}

 
	public function Cmp(TString $aTStr) 
	{	
		return TStr::nCmp($this->Data, $aTStr->Data, min($this->Length(), $aTStr->Length()));
	}


	public function Pos(TString $aFind, $aOfst = 0) 
	{
		return TStr::Pos($this->Data, $aFind->Data, $aOfst);
	}

 
	public function PosR(TString $aFind, $aOfst = 0) 
	{
		return TStr::PosR($this->Data, $aFind->Data, $aOfst);
	}

	
	public function SubPosR(TString $aFind, $aCnt = 1) 
	{
		return new TString(TStr::SubPosR($this->Data, $aFind->Data, $aCnt));
	}

 
	public function PosCnt(TString $aFind, $aCnt) 
	{
		return TStr::PosCnt($this->Data, $aFind->Data, $aCnt);
	}
 

	public function SubCount(TString $aFind, $aOfst = 0) 
	{
		return TStr::SubCount($this->Data, $aFind->Data, $aOfst);
	}

 
	public function ToLower() 
	{
		return new TString(TStr::ToLower($this->Data));
	}


	public function ToUpper() 
	{
		return new TString(TStr::ToUpper($this->Data));
	}
 
 
	public function Left($aIdx) 
	{
		return new TString(TStr::Left($this->Data, 0, $aIdx));
	}
 
 
	public function LeftWords($aLimit) 
	{	
		return new TString(TStr::LeftWords($this->Data, $aLimit));
	}

 
	public function Right($aIdx) 
	{
		return new TString(TStr::Right($this->Data, $aIdx));
	}
	
 
	public function ReplaceReg(TString $aSearch, TString $aReplace) 
	{
		return new TString(TStr::ReplaceReg($this->Data, $aSearch->Data, $aReplace->Data));
	}

 
	public function Replace(TString $aSearch, TString $aReplace) 
	{
		return new TString(TStr::Replace($this->Data, $aSearch->Data, $aReplace->Data));
	}

 
	public function Reverse() 
	{
		return new TString(TStr::Reverse($this->Data));
	}

 
	public function Pad($aLen, TString $aPadStr, $aMode = STR_PAD_LEFT) 
	{
		return new TString(TStr::Pad($this->Data, $aLen, $aPadStr->Data, $aMode));
	}
 
 
	static public function Repeat($aLen) 
	{
		return new TString(TStr::Repeat($this->Data, $aLen));
	}

 
	static public function ExpandR(TString $aSearch, TString $aReplace) 
	{
		return new TString(TStr::ExpandR($this->Data, $aSearch->Data, $aReplace->Data));
	}	
}
?>