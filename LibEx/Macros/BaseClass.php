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

require_once(_DirCommonLib . "/Array.php");
require_once(_DirCommonLib . "/Control.php");
require_once(_DirCommonLib . "/Token.php");
require_once(_DirCommonLib . "/Paging.php");

/*
	$this->ArrSyntax->AddItem("get",	 "Get|Help;Version;Files(File);Server;GeoIP");
	//$this->ArrSyntax->AddItem("set",	 "Set|SetTableTR,SetTableTD");
	//$this->ArrSyntax->AddItem("table",   "Table|Item|Cols|[Params]");
*/
abstract class tMacros_BaseClass
//-----------------------------------------------------------------------------
{
	const cNone	  = "Undefined";
	const cSyntax = self::cNone, cDescr = self::cNone, cMask = self::cNone;
//	const cDescr	= <<<BAR
//HEREDOC Style
//BAR;


	protected $TParseFile;
	public $Data, $TArrParam, $Owner;


	abstract public function Build();
	abstract public function InitDefParam();


	public function __construct(tParseBase $aTParseBase, $aParams = "")
	{
		$this->TParseFile = $aTParseBase;
		$this->TParseFile->AddMacrosClass($this);
		$this->Data		= "";
		$this->Owner	= "";
		$this->SetParams($aParams);
	}


	public function GetItem($aItem, $aDefault = "_NotSet_")
	{
		return $this->TParseFile->GetItem($aItem, $aDefault);
	}


	public function SetItem($aItem, $aValue)
	{
		$this->TParseFile->SetItem($aItem, $aValue);
	}


	public static function GetConst($aName)
    {
		$ConstName = sprintf("%s::%s", get_called_class(), $aName);
        if (defined($ConstName)) {
			return constant($ConstName);
		}else{
			return "";
		}
    }


	public function GetHelp($aType)
	{
		if ($aType == "cDescr") {
			$Result = TStr::ReplaceReg(self::GetConst("cDescr"), '/href=(.*)/i', sprintf('<a href=$1>%s ...</a>', $this->GetItem("More")));
		}elseif ($aType == "cURL") {
			$Result = TStr::GetURL(self::GetConst("cDescr"));
		}else{
			$Result = self::GetConst($aType);
		}
		return $Result;
	}


	public function GetMacrosName()
	{
		return TStr::Replace(get_class($this), $this->TParseFile->Settings->MacrosPrefix, "");
	}


	public function GetDefParam($aItem, $aDefault)
	{
		$ItemName = $this->GetMacrosName() . "_" . $aItem;
		return $this->GetItem($ItemName, $aDefault);
	}


	public function SetDefParam($aItem, $aValue)
	{
		$Value = $this->GetDefParam($aItem, $aValue);
		$this->TArrParam->AddItemToEnd($Value);
	}


	public function SetParams($aString)
	{
		if ($aString == "") {
			$this->TArrParam = new TArray();
		}else{
			$Token1 = new TToken($this->TParseFile->Settings->SignMacrosDelim);
			$Token1->LoadFromString($aString);
			if (TStr::Pos($aString, " ") != -1) {
				$Token1->TArrData = $Token1->TArrData->Trim(TArray::cAll);
			}
			$this->TArrParam = $Token1->TArrData;
		}
	}


	public function SetParam($aName, $aValue)
	{
		$this->TArrParam[$this->GetConst($aName)] = $aValue;
	}
	
	
	public function LogError($aMessage)
	{
		$ErrMsg = sprintf("%s: '%s', %s: '%s'",
							$this->GetItem("Macros"), $this->GetMacrosName(),
							$this->GetItem("Error"), $aMessage);
		$this->TParseFile->LogError($ErrMsg, $this->GetHelp("cURL"));
	}


	protected function CheckParam()
	{
		$Syntax = $this->GetHelp("cSyntax");

		$Result = false;
		if ($Syntax == "") {
			$this->LogError("Syntax is empty");
		}else{
			$ParamCnt = $this->TArrParam->GetCount();
			$ParamMaxCnt = TStr::SubCount($Syntax, $this->TParseFile->Settings->SignMacrosDelim) + 1;
			$ParamMinCnt = $ParamMaxCnt - TStr::SubCount($Syntax, "[");
			if ($ParamCnt < $ParamMinCnt) {
				$ErrMsg = sprintf("Param count in %s < %d. %s: %s",	get_class($this), $ParamMinCnt, $this->GetItem("Syntax"), $Syntax);
				$this->LogError($ErrMsg);
			}elseif ($ParamCnt > $ParamMaxCnt) {
				if (TStr::Right($Syntax, 3) != "...") {
					$ErrMsg = sprintf("Param count in %s > %d. %s: %s",	get_class($this), $ParamMaxCnt, $this->GetItem("Syntax"), $Syntax);
					$this->LogError($ErrMsg);
				}else{
					$Result = true;
				}
			}else{
				$Result = true;
			}
		}

		return $Result;
	}


    public function Show()
	{
		Show($this->Data);
    }


	public function __toString()
    {
        return $this->Data;
    }
}


abstract class tMacros_GalleryClass extends tMacros_BaseClass
//-----------------------------------------------------------------------------
{
	public function SortArray($aArray)
	{
		if ($aArray->GetCount() > 0) {
			if ($this->TParseFile->Settings->GallerySort == "Rnd") {
				$aArray = $aArray->Shuffle();
			}else{
				$aArray = $aArray->SortByValue($this->TParseFile->Settings->GallerySort == "Asc");
			}
			$aArray = $aArray->Slice($this->TParseFile->Settings->GalleryOffset, ($this->TParseFile->Settings->GalleryLength == 0 ? $aArray->GetCount() : $this->TParseFile->Settings->GalleryLength));
		}
		return $aArray;
	}
}


abstract class tMacros_FileClass extends tMacros_GalleryClass
//-----------------------------------------------------------------------------
{
	abstract public function AsFile($aFileName);
	//public function InitDefParam() {}
	//public function Build() {}


	public function BuildFile($aFileName)
	{
		$this->Data = "";
		if ($this->CheckParam()) {
			$this->InitDefParam();

			$this->Data = $this->Parse_File($aFileName);
		}
		return $this->Data;
	}


	public function GetPrefix($aType)
	{
		return (TStr::Pos("Image,Thumb", $aType) != -1 ? "Image" : "File");
	}


	public function SearchFile($aFileName)
	{
		$FileName = TStr::Trim($aFileName);
		if ($FileName != "") {
			$MacrosName = $this->GetMacrosName();
			$Prefix		= $this->GetPrefix($MacrosName);
			$ClassPath	= $this->GetDefParam("Path", "Common" . "/" . $MacrosName);
			if (TFS::FileExists($ClassPath . "/" . $FileName)) {
				$FileName = $ClassPath . "/" . $FileName;
				$this->TParseFile->TArrFiles->AddFile($Prefix, $FileName);
			}else{
				$FileName = $this->TParseFile->TPath->SearchFile($FileName, "/" . $Prefix . "/");
				if ($FileName == "") {
					$ErrMsg = $this->FileNotFound($Prefix, $FileName);
					$this->LogError($ErrMsg);
				}else{
					$this->TParseFile->TArrFiles->AddFile($Prefix, $FileName);
				}
			}
		}
		return $FileName;
	}


	public function FileNotFound($aPrefix, $aFileName)
	{
		$this->TParseFile->TArrFiles->AddFile("Error", $aFileName);
		$ErrMsg = sprintf("File not found: %s. Prefix: %s", $aFileName, $aPrefix);
		$this->LogError($ErrMsg);
	}


	public function Parse_File($aFileName)
	{
		if (TStr::Pos($aFileName, ",") == -1) {
			$Result = $this->Parse_AsFile($aFileName);
		}else{
			$Token1 = new TToken(",");
			$Token1->LoadFromString($aFileName);
			$Result = $this->Parse_AsFiles($this,
						$Token1->TArrData,
						$this->GetDefParam("Cols", 3),
						$this->GetDefParam("Items", 25));
		}
		return $Result;
	}


	public function Parse_AsFile($aFileName)
	{
		$Result = "";
		$FileName = $this->SearchFile($aFileName);
		if ($FileName != "") {
			$Result = $this->AsFile($FileName);
		}
		return $Result;
	}


	public function Parse_AsFiles(tMacros_FileClass $aMacros, TArray $aTArray, $aCols, $aItemsMax)
	{
		$MacrosName = $aMacros->GetMacrosName();
		$Prefix 	= $this->GetPrefix($MacrosName);

		$ItemsCount = $aTArray->GetCount();
		$aTArray 	= $this->SortArray($aTArray);
		$ItemsPerPage = ($aItemsMax == 0 ? $ItemsCount : $aItemsMax);

		$ArrTable = new TArray();
		$Paging1 = new TPaging($_GET, $ItemsPerPage, 10, sprintf("%s-%u", $MacrosName, CRC32($aTArray->Implode(","))));
		$Pages = $Paging1->Build($ItemsCount);
		$aTArray = $aTArray->Slice($Paging1->GetItemStart(), $Paging1->GetItemLength());
		$aTArray->Reset();
		while (list($No, $FullPath) = $aTArray->Each()) {
			$FullPath = $this->SearchFile($FullPath);
			if ($FullPath == "") continue;

			$FileInfo = TFS::GetFileInfo($FullPath);
			$DescrFile = $FileInfo->GetItem("DirName") . "/PageIndex.txt";
			if (TFS::FileExists($DescrFile)) {
				if ($this->TParseFile->TArrFiles->TArrLoad->Search($DescrFile) == false) {
					$MacrosLoad = $this->TParseFile->LoadMacros("IncludeData");
					$MacrosLoad->TArrParam[0] = $DescrFile;
					$MacrosLoad->Build();
				}
			}

			if ($this->TParseFile->Settings->GalleryText == "") {
				$Token1 = new TToken($this->TParseFile->Settings->SignMacrosDelim);
				$BaseName = $FileInfo->GetItem("BaseName");
				$ParamCnt = $Token1->LoadFromString($this->GetItem($BaseName));
				if ($ParamCnt > 0 && $Token1->GetItem(0) == $BaseName) {
					$Token1->SetItem(0, $FileInfo->GetItem("FileName"));
				}
				if ($aMacros::GetConst("cText") !== "") {
					$aMacros->TArrParam[$aMacros::cText] = $Token1->GetItem(0);
				}
			}else{
				$aMacros->TArrParam[$aMacros::cText] = $this->TParseFile->Settings->GalleryText;
			}

			if ($aMacros::GetConst("cFileName") !== "") {
				$aMacros->TArrParam[$aMacros::cFileName] = $FullPath;
			}
			$this->SetItem("Gallery_File", $FullPath);
			$ArrTable->AddItemToEnd($aMacros->AsFile($FullPath));
			if ($MacrosName != "Include") {
				$this->TParseFile->TArrFiles->AddFile($Prefix, $FullPath);
			}
		}
		$Table1 = new TTable($aCols, $ArrTable->GetCount()  / $aCols);
		$Table1->Build($ArrTable);

		return $Table1->GetPrintOut() . ($Pages > 0 ? $this->TParseFile->Settings->BR . THTML::ClearBoth() . $Paging1->GetPrintOut() . $this->TParseFile->Settings->BR : "");
	}


	protected function Parse_AsFilesDir(tMacros_FileClass $aMacros, $aDirName, $aMask, $aCols, $aItemsMax)
	{
		$Result = "";
		if ($this->TParseFile->Settings->GalleryShowDir) {
			$DirName = $this->SearchFile($aDirName);

			$Dir1 = new TDir($DirName);
			$ArrFiles = $Dir1->GetFiles(true, TDir::cFile, $aMask);
			$AllFilesCnt = $ArrFiles->GetCount();

			$ArrFiles = $Dir1->GetFiles(true, TDir::cDir);
			$ArrFiles->AddItemToEnd($DirName);
			$ArrFiles = $ArrFiles->SortByValue(true);
			$ArrFiles->Reset();
			while (list($No, $FullPath) = $ArrFiles->Each()) {
				$ArrFiles2 = $this->GetGalleryFiles($FullPath, $aMask);
				$FilesCnt = $ArrFiles2->GetCount();

				$FileInfo = TFS::GetFileInfo($FullPath);
				$FileName = $FileInfo->GetItem("BaseName");
				if ($FullPath == $DirName) {
					$FileName = sprintf("%s (%d) /", $DirName, $AllFilesCnt);
				}

				$Result .= THTML::Line() . $this->TParseFile->Settings->BR .
					sprintf("<strong>%s (%d)</strong>", $FileName, $FilesCnt) . $this->TParseFile->Settings->BR .
					$this->Parse_AsFiles($aMacros, $ArrFiles2, $aCols, $aItemsMax);
			}
		}else{
			$ArrFiles = $this->GetGalleryFiles($aDirName, $aMask);
			$Result = $this->Parse_AsFiles($aMacros, $ArrFiles, $aCols, $aItemsMax);
		}

		return $Result;
	}


	public function GetGalleryFiles($aDirName, $aMask)
	{
		$Result = new TArray();
		$DirName = $this->TParseFile->TPath->SearchFile($aDirName, "/");
		if ($DirName != "") {
			$SubDirs = $this->TParseFile->Settings->GallerySubDir;
			if ($this->TParseFile->Settings->GalleryShowDir) {
				$SubDirs = false;
			}
			$Dir1 = new TDir($DirName);
			$Result = $Dir1->GetFiles($SubDirs, TDir::cFile, $aMask);
		}
		return $Result;
	}
}


abstract class tMacros_DBClass extends tMacros_GalleryClass
//-----------------------------------------------------------------------------
{

}
?>
