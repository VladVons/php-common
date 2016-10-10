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

  
include_once("Sys.php");
include_once("Array.php");


class TStream
/////////////////////////////////////////////////////
{
	protected $Handle;

	function __construct()
	{
		$this->Handle = 0;
	}

 
	function Read($aLength)
	{
		return fread($this->Handle, $aLength);
	}

 
	function Write($aValue, $aLength = -1)
	{
		if ($aLength == -1) {
			return fwrite($this->Handle, $aValue);
		}else{
			return fwrite($this->Handle, $aValue, $aLength);
		}
	}


	function Gets()
	{
		return fgets($this->Handle);
	}


	function GetsA()
	{
		$TArray1 = new TArray();
		while (!$this->EOF()) { 
			$TArray1->AddItemToEnd($this->Gets()); 
		}
		
		return $TArray1;   
	}


	function Puts($aValue)
	{
		return fputs($this->Handle, $aValue);
	}


	function PutsA($aTArray)
	{
		$aTArray->Reset();
		while (list(, $Value) = $aTArray->Each()) {
			$this-Puts($Value);
		}
	}

 
	function Seek($aOffset)
	{
		return fseek($this->Handle, $aOffset, SEEK_SET );
	}


	function EOF()
	{
		return feof($this->Handle);
	}
}


class TFile extends TStream
/////////////////////////////////////////////////////
{
	protected $FileName;

	function __destruct()
	{
		$this->Close();
	}


	function Open($aFileName, $aMode = "r+")
	{
		$Handle1 = fopen($aFileName, $aMode);
		if ($Handle1 != false) {
			$this->Close();  
			$this->Handle    = $Handle1;
			$this->FileName = $aFileName;
		}
		
		return $Handle1;
 }


	function Close()
	{
		if ($this->Handle) {
			fclose($this->Handle);
			$this->Handle = 0;
		}
	}
 

	function OpenTemp() 
	{
		$Handle1 = tmpfile();
		if ($Handle1) {
			$this->Handle = $Handle1;
		}
	}

 
	function Read($aLength = -1)
	{
		if ($aLength = -1) {
			$aLength = $this->GetSize();
		}
		
		return parent::Read($aLength);
	}

 
	function Flush()
	{
		fflush($this->Handle);
	}

 
	function GetInfo()
	{	
		return fstat($this->Handle);
	}


	function GetSize()
	{
		$Array = $this->GetInfo();
		return $Array["size"];
	}
}



class TPipe extends TStream
/////////////////////////////////////////////////////
{
	function __destruct() {
		$this->Close();
	}


	function Open($aCommand, $aMode = "r")
	{
		$Handle1 = popen($aCommand, $aMode);
		if ($Handle1) {
			$this->Close();
			$this->Handle = $Handle1;
		}
		
		return $Handle1;
	}
 
 
	function Close()
	{
		if ($this->Handle) {
			pclose($this->Handle);
			$this->Handle = 0;
		}
	}
}

?>