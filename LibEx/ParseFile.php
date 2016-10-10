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
require_once(_DirCommonLib . "/Token.php");
require_once(_DirCommonLib . "/Dir.php");
require_once(_DirCommonLibEx . "/Macros/BaseClass.php");
require_once(_DirCommonLibEx . "/Classes.php");


class TPath
/////////////////////////////////////////////////////
{
	public $TDir, $TArrData;

 	function __construct()
	{
		$this->TDir 	= new TDir("");
		$this->TArrData	= new TArray();
	}

	public function AddDir($aDirName)
	{
		if ($aDirName != "" && TFS::IsDir($aDirName)) {
			$this->TArrData->AddItemToEnd($aDirName, true);
			return true;
		}
	}


	protected function SearchInList($aName)
	{
		$this->TArrData->Reset();
		while (list(, $Value) = $this->TArrData->Each()) {
			$FileName = $Value . "/" . $aName;
			if (TFS::FileExists($FileName)) {
				return $FileName;
			}
		}
		return "";
	}


	public function SearchFile($aName, $aPrefix)
	{
		//$aName = $this->FileCharset($aName);
		if (!IsLinkExternal($aName)) {
			if (TFS::FileExists($aName)) {
				return $aName;
			}else{
				$FileName = $this->SearchInList($aName);
				if ($FileName != "") {
					return $FileName;
				}else{
					return $this->TDir->SearchFile($aName, $aPrefix);
				}
			}
		}else{
			return $aName;
		}
	}
}


class TArrFiles
/////////////////////////////////////////////////////
{
	public $TArrLoad, $TArrImage, $TArrFile, $TArrLink, $TArrPHP, $TArrError;

 	function __construct()
	{
		$this->TArrLoad  = new TArray();
		$this->TArrImage = new TArray();
		$this->TArrFile  = new TArray();
		$this->TArrPHP   = new TArray();
		$this->TArrLink  = new TArray();
		$this->TArrError = new TArray();
	}


	public function AddFile($aType, $aFileName)
	{
		if (_DebugLevel > 3) Debug(sprintf('%s->%s->%s(%s)', basename(__file__), __class__, __function__, implode(',', func_get_args())));

		$ArrName = "TArr" . $aType;
		$this->$ArrName->AddItemToEnd($aFileName, true);
	}


	public function Search($aType, $aFileName)
	{
		$ArrName = "TArr" . $aType;
		return $this->$ArrName->Search($aFileName);
	}


	public function GetFiles($aType)
	{
		$ArrName = "TArr" . $aType;
		return $this->$ArrName;
	}


	public function GetAllFiles()
	{
		$TArrResult = new TArray();
		$TArrVars	= new TArray(get_class_vars(get_class($this)));
		$TArrVars->Reset();
		while (list($VarName, $None) = $TArrVars->Each()) {
			if (TStr::Cmp($VarName, "TArr") == 0) {
				$TArrResult = $TArrResult->Merge($this->$VarName->PadEx(TArray::cLeft, $VarName . "->"));
			}
		}

		return $TArrResult->SortByValue();;
	}

}


/////////////////////////////////////////////////////
class tParseSettings
{
	public	$ParseMacros, $Sections, $MacrosDir, $MacrosPrefix;
	public	$SignCommentOnce, $SignCommentBegin, $SignCommentEnd;
	public	$SignDelim, $SignMacrosBegin, $SignMacrosEnd, $SignMacrosDelim;
	public	$ParseLineBegin, $ParseLineEnd;
	public	$GallerySort, $GalleryOffset, $GalleryLength, $GalleryShowDir, $GallerySubDir, $GalleryText;
	public	$BR, $Stop, $Debug, $OnRead;


	public function __construct()
	{
		$this->SetDefaults();
	}


	public function SetDefaults($aNotUsed = "")
	{
		$this->ParseMacros		= true;
		$this->Sections			= false;
		$this->SignCommentOnce	= "//";
		$this->SignCommentBegin	= "/*";
		$this->SignCommentEnd	= "*/";
		$this->SignDelim		= "=";
		$this->SignMacrosBegin  = "{";
		$this->SignMacrosEnd    = "}";
		$this->SignMacrosDelim	= "|";
		$this->MacrosDir		= TFS::GetDirName(__FILE__) . "/" . "Macros";
		$this->MacrosPrefix		= "tMacros_";
		$this->BR 				= "\n";
		$this->ParseLineBegin	= 0;
		$this->ParseLineEnd		= 0;
		$this->Debug			= false;
		$this->GallerySort		= "Asc";
		$this->GalleryOffset	= 0;
		$this->GalleryLength	= 0;
		$this->GalleryShowDir	= false;
		$this->GallerySubDir	= true;
		$this->GalleryText		= "";
		$this->OnRead			= "";
		$this->Stop				= false;
		return " ";
	}


	public function SetPRE($aValue)
	{
        if ($aValue == "true") {
			$this->BR = "\r";
			$this->SignDelim = "==";
			return "<pre>";
		}else{
			$this->SignDelim = "=";
			$this->BR = "\n";
			return "</pre>";
		}
	}


	public function SetBR($aValue)
	{
		$this->BR = (TStr::ToLower($aValue) == "true" ? "\n" : "\r");
		return " ";
	}


	public function SetAsArray($aTArray)
	{
		$Result = "";
		$Name  = TStr::Trim($aTArray->GetItem(0));
		$Value = $aTArray->GetItem(1);
		$MethodName = "Set" . $Name;
		if (method_exists($this, $MethodName)) {
			$Result = $this->$MethodName($Value);
		}elseif (isset($this->$Name)) {
			$Type = gettype($this->$Name);
			if ($Type == "boolean") {
				$this->$Name = TStr::ToLower($Value) == "true";
			}elseif ($Type == "integer"){
				$this->$Name = intval($Value);
			}else{
				$this->$Name = $Value;
			}
			$Result = " ";
		}
		return $Result;
	}


	public function SetAsString($aValue)
	{
		$Token1 = new TToken($this->SignMacrosDelim);
		$Token1->LoadFromString($aValue);
		return $this->SetAsArray($Token1->TArrData);
	}


	public function Get()
	{
		$TArray = new TArray();
		$ClassVars = get_class_vars(get_class($this));
		foreach ($ClassVars as $Name => $None) {
			$Value = $this->$Name;
			$Type = gettype($Value);
			if ($Type == "boolean") {
				$Value = ($Value ? "true" : "false");
			}elseif ($Type == "integer") {
				$Value = intval($Value);
			}
			$TArray->AddItem($Name, $Value);
		}
		return $TArray->SortByLabel();
	}
}


/////////////////////////////////////////////////////
class tParseBase
{
	private 	$UlOpen;
	protected	$TReport, $TArrMacros, $TArrFunc, $CurMacros, $CurFileName;
	public		$TArrData, $Settings, $TArrFiles, $TPath;


	public function __construct()
	{
		$this->CurFileName	= "";
		$this->UlOpen		= false;
		$this->TArrData		= new TArray();
		$this->TArrMacros	= new TArray();
		$this->TArrFunc		= new TArray();
		$this->TArrFiles	= new TArrFiles();
		$this->TPath		= new TPath();
		$this->TReport		= new TReport(_LogFile);
		$this->Settings		= new tParseSettings();
		//$this->Settings->OnRead = &$this->LoadFromString();
	}


	public function LogError($aMessage, $aUrl = "")
	{
		if (_DebugLevel > 2) Debug(sprintf('%s->%s->%s(%s)', basename(__file__), __class__, __function__, implode(',', func_get_args())));

		$Message = "File " . $this->CurFileName . ", line " . $this->CurLineNo . ", (" . $aMessage . ")";
		$this->TReport->Log($Message);

		if ($aUrl == "") {
			$ErrMsg = sprintf('<label title="%s" class="_Error">{%s}</label>',
							$this->GetItem("Error"), $Message, $this->CurMacros);
		}else{
			$ErrMsg = THTML::Href($aUrl, $this->CurMacros, $Message);
		}
		Show($this->GetItem("Error") . ": " . $ErrMsg . "<br>");

		return $Message;
	}


	public function AddMacrosClass(tMacros_BaseClass $aBaseClass)
	{
		$this->TArrMacros->AddItemToEnd($aBaseClass);
	}


	public function AddMacrosFunc($aMacrosName, $aFuncName)
	{
		$this->TArrFunc->AddItem($aMacrosName, $aFuncName);
	}


	public function ShowMacros()
	{
		$this->TArrMacros->Reset();
		while (list($MacrosNo, $MacrosClass) = $this->TArrMacros->Each()) {
			printf("Name:%s\nSyntax:%s\nValues:%s\n\n", get_class($MacrosName), $MacrosName->Syntax, $MacrosName->TArrParam->ImplodeEx(TArray::cAll, ", ", "'"));
		}
	}


	public function LoadMacros($aMacrosName)
	{
		if (_DebugLevel > 2) Debug(sprintf('%s->%s->%s(%s)', basename(__file__), __class__, __function__, implode(',', func_get_args())));

		$aMacrosName = TStr::Trim($aMacrosName);
		$FileName = $this->Settings->MacrosDir . "/" . $aMacrosName . ".php";
		if (TFS::FileExists($FileName)) {
			require_once($FileName);
			$ClassName = $this->Settings->MacrosPrefix . $aMacrosName;
			$Class1 = new $ClassName($this);
			return $Class1;
		}else{
			$this->LogError("Macros not found: " . RootFileName($FileName));
			return "";
		}
	}


	public function GetItemFromSection($aItem, $aSection)
	{
		return $this->TArrData[$aSection][$aItem];
	}


	public function GetItem($aItem, $aDefault = "_NotSet_")
	{
		if (_DebugLevel > 2) Debug(sprintf('%s->%s->%s(%s)', basename(__file__), __class__, __function__, implode(',', func_get_args())));

		$Default = ($aDefault == "_NotSet_" ? $aItem : $aDefault);
		return $this->TArrData->GetItem($aItem, $Default);
	}


	public function SetItem($aItem, $aValue)
	{
		$this->TArrData->SetItem($aItem, $aValue);
	}


	public function ShowItem($aItem, $aDefault = "")
	{
		Show($this->GetItem($aItem, $aDefault));
	}


	protected function Parse_MacrosProc($aPos, $aDepth)
	{
		if ($this->CurLine[0] == "*") {
			$Value = "";
			if (!$this->UlOpen) {
				$this->UlOpen = true;
				$Value = "<ul>";
			}
			$this->CurLine = $Value . "<li>" . TStr::Sub($this->CurLine, 1);
		}elseif ($this->UlOpen) {
			$this->UlOpen = false;
			$this->CurLine = "</ul>" . $this->CurLine;
		}

		$Len = TStr::Length($this->CurLine);
		if ($aPos < 0 || $aPos >= $Len || $Len < 2) {
			return 0;
		}

		$Result = 0;
		$Pos1 = $aPos;
		$Stop = false;
		do {
			$Value = "";
			$Pos1 = TStr::Pos($this->CurLine, $this->Settings->SignMacrosBegin, $Pos1);
			if ($Pos1 != -1) {
				$Pos2 = TStr::Pos($this->CurLine, $this->Settings->SignMacrosEnd, $Pos1 + 1);
				$Pos3 = TStr::Pos($this->CurLine, $this->Settings->SignMacrosBegin, $Pos1 + 1);
				if ($Pos2 != -1 && $Pos3 != -1 && $Pos3 < $Pos2) {
					$this->Parse_MacrosProc($Pos3, $aDepth + 1);
					$Len = TStr::Length($this->CurLine);
					$Pos2 = TStr::Pos($this->CurLine, $this->Settings->SignMacrosEnd, $Pos1 + 1);
				}
				if ($Pos2 != -1) {
					$this->CurMacros = TStr::Trim(TStr::Sub($this->CurLine, $Pos1 + 1, $Pos2 - $Pos1 - 1));
					$Token1 = new TToken($this->Settings->SignMacrosDelim);
					$Token1->LoadFromString($this->CurMacros);
					if (TStr::Pos($this->CurMacros, " ") != -1) {
						$Token1->TArrData->Trim();
					}
					$MacrosName = $Token1->GetItem(0);
					$Token1->TArrData = $Token1->TArrData->Slice(1, 0);

					$MacrosFunc = $this->TArrFunc->GetItem($MacrosName);
					if ($MacrosFunc != "") {
						$Value = call_user_func($MacrosFunc, $this, $Token1->TArrData);
						//$Value = call_user_func($MacrosFunc, &$this, &$Token1->TArrData);
					}else{
						switch($MacrosName) {
							case "Stop":
								if ($this->Settings->Stop) {
									$Stop = true;
								}
								$Value = " ";
								break;

							case "Break":
								$Value = THTML::ClearBoth();
								break;

							default:
								$MacrosClass = $this->LoadMacros($MacrosName);
								if ($MacrosClass !== "") {
									$MacrosClass->TArrParam = $Token1->TArrData;
									$MacrosClass->Build();
									$Value = $MacrosClass->Data;
								}

						}
					}
				}
			}

			$LenValue = TStr::Length($Value);
			if ($LenValue > 0) {
				if ($this->Settings->Debug) {
					if (TStr::Pos($MacrosName, "Get") != 0) {
						$Value = "<!--Begin_" . $MacrosName . "!-->" . $Value . "<!-- End_" . $MacrosName . "!-->";
						$LenValue = TStr::Length($Value);
					}
				}

				$this->CurLine = TStr::Sub($this->CurLine, 0, $Pos1) . $Value . TStr::Sub($this->CurLine, $Pos2 + 1);
				$Result += $LenValue;
				if ($MacrosName == "MGalleryDir") {
					$Pos1   += $Pos2 + $LenValue + 1;
				}else{
					$Pos1   += $LenValue;
				}
				$Len = TStr::Length($this->CurLine);
				if ($Stop) {
					$Result = -1;
				}
			}elseif ($Pos1 != -1) {
				$Pos1++;
			}
		} while (!$Stop && $Pos1 != -1 && $Pos1 < $Len);

		return $Result;
	}


	public function GetFileName()
	{
		return $this->CurFileName;
	}


}


class tParseFileINI extends tParseBase
/////////////////////////////////////////////////////
{
	private		$Property, $Value, $SectionName, $Commented;
	protected	$CurLine, $CurLineNo;


	public function __construct()
	{
		parent::__construct();

		$this->Commented	= false;
		$this->SectionName	= "";
		$this->Property		= "";
		$this->Value		= "";
	}


	protected function Flash()
	{
		if ($this->Property == "") return;

		$Value =
		TStr::Trim($this->Value);
		if ($this->SectionName == "") {
			$this->TArrData->SetItem($this->Property, $Value);
		}else{
			$this->TArrData[$this->SectionName][$this->Property] = $Value;
		}

		$this->Property = "";
		$this->Value    = "";
	}


	protected function LoadFromToken($aToken, $aStart, $aEnd)
	{
		for ($i = $aStart; $i < $aEnd; $i++) {
			$this->CurLine = TStr::TrimRight($aToken->GetItem($i));
			$this->CurLineNo = $i + 1;
			$LenLine = TStr::Length($this->CurLine);
			if ($LenLine == 0) {
				$this->Value .= $this->Settings->BR;
			}elseif (TStr::Cmp($this->CurLine, $this->Settings->SignCommentBegin) == 0) {
				$this->Commented = true;
			}elseif (TStr::Cmp($this->CurLine, $this->Settings->SignCommentEnd) == 0) {
				$this->Commented = false;
			}elseif (TStr::Cmp($this->CurLine, $this->Settings->SignCommentOnce) != 0 && $this->Commented == false) {
				if ($this->Settings->Sections && $this->CurLine[0] == '[' && $this->CurLine[$LenLine - 1] == ']') {
					$SectionName = TStr::Trim(TStr::Sub($this->CurLine, 1, $LenLine - 2));
					$this->Flash();
					$this->SectionName = $SectionName;
				}else{
					$Pos1 = TStr::Pos($this->CurLine, $this->Settings->SignDelim);
					if ($Pos1 != -1) {
						$Property = TStr::Sub($this->CurLine, 0, $Pos1);
						if (strpbrk($Property, "<>|:/%?&") == false) {
							$this->Flash();
							$this->Property = $Property;
							$this->CurLine = TStr::Sub($this->CurLine, $Pos1 + TStr::Length($this->Settings->SignDelim));
						}
					}

					$BR = $this->Settings->BR;
					if ($this->Settings->ParseMacros) {
						$Result = $this->Parse_MacrosProc(0, 0);
						if (TStr::Length($this->CurLine, true) == 0) {
							$BR = "";
						}
						if ($Result == -1) {
							$this->Value .= $this->CurLine . $BR;
							break;
						}
					}

					$this->Value .= $this->CurLine . $BR;
				}
			}
		}
		$this->Flash();

		return $aEnd - $aStart;
	}


	public function LoadFromString($aString)
	{

		$Token1 = new TToken("\n");
		$Token1->LoadFromString($aString);
		$this->Commented = false;
		$ParseLineEnd = ($this->Settings->ParseLineEnd == 0 ? $Token1->TArrData->GetCount() : min($this->Settings->ParseLineEnd, $Token1->TArrData->GetCount()));
		$Result = $this->LoadFromToken($Token1, $this->Settings->ParseLineBegin, $ParseLineEnd);
		return $Result;
	}
}


class tParseFile extends tParseFileINI
/////////////////////////////////////////////////////
{
	public function LoadFromFile($aFileName)
	{
		if (_DebugLevel > 2) Debug(sprintf('%s->%s->%s(%s)', basename(__file__), __class__, __function__, implode(',', func_get_args())));

		$Result = 0;
		$this->CurFileName = $aFileName;
		if ($this->TArrFiles->Search("Load", $aFileName) === false) {
			$this->TArrFiles->AddFile("Load", $aFileName);

			$ArrFileInfo = TFS::GetFileInfo($aFileName);
			$DirName = $ArrFileInfo->GetItem("DirName");

			$this->TPath->AddDir($DirName);
			$this->TPath->AddDir("$DirName/" . $ArrFileInfo->GetItem("FileName"));
			$this->TPath->AddDir("$DirName/Image");
			$this->TPath->AddDir("$DirName/File");

			$this->TPath->TDir->DirName = $DirName;

			$TFile1 = new TFile();
			$TFile1->Open($aFileName, "r");
			$FileData = $TFile1->Read();
			if (!empty($this->Settings->OnRead)) {
				//call_user_func($this->Settings->OnRead, $aFileName, &$FileData);
				call_user_func($this->Settings->OnRead, $aFileName, $FileData);
			}

			// We call it sometime recursively, so make copy of instance
			$Clone1 = clone $this;
			$Result = $Clone1->LoadFromString($FileData);
			$this->TArrData = $Clone1->TArrData;
			$this->TArrFiles = $Clone1->TArrFiles;
		}else{
			$ErrMsg = sprintf("%s: %s", $this->GetItem("Already loaded"), $aFileName);
			//$this->LogError($ErrMsg);
		}
		return $Result;
	}
}
?>
