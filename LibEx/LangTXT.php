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

require_once(_DirCommonLib . "/Control.php");
require_once(_DirCommonLibEx . "/ParseFile.php");


class TLang
/////////////////////////////////////////////////////
{
 protected $Language, $DirLanguage, $TDir, $PageName, $SubPageName;
 public $TParseFile, $TArrDefPages;

	function __construct($aDefLanguage, $aDirLanguage)
	{
		$this->TArrDefPage	= new TArray();
		$this->Language		= $aDefLanguage;
		$this->DirLanguage	= $aDirLanguage;
		$this->TDir		= new TDir($aDirLanguage . "/" . $aDefLanguage);
		$this->TParseFile	= new tParseFile();
		$this->PageName = $this->SubPageName = "";
	}

 
	public function AddPath($aDirName)
	{
		if ($aDirName != "" && TFS::FileExists($aDirName)) {
			$this->TParseFile->TPath->AddDir($aDirName);
		}	
	}

 
	public function LoadFromFile($aFileName)
	{
		if (_DebugLevel > 1) Debug(sprintf('%s->%s->%s(%s)', basename(__file__), __class__, __function__, implode(',', func_get_args())));
		
		$Result = 0;
		if (TFS::FileExists($aFileName)) {
			$this->TParseFile->LoadFromFile($aFileName);
			$Result = 1;
		}	
	
		return $Result;	
	}

 
	protected function GetPageFiles($aPageName, $aDirName = "", $aDepth = 3)
	{
		$DirName = ($aDirName == "" ? $this->TDir->DirName : $this->TDir->DirName . "/" . $aDirName);
		$TDir1 = new TDir($DirName . "/" . $aPageName);
		$TDir1->SearchFiles($aPageName . ".txt", "/", $aDepth);
		//$TDir1->SearchFiles($aPageName . ".xml", "/", $aDepth);
		return $TDir1->TArrData;
	}

 
	public function LoadDefPages($aPageName = "")
	{
		$Result = 0;
		$this->TArrDefPage->Reset();
		while (list($No, $DefPageName) = $this->TArrDefPage->Each()) {
			$ArrFiles = $this->GetPageFiles($DefPageName, $aPageName, 3);
			$Result += $this->LoadFilesFromArray($ArrFiles);
		}

		return $Result; 	
	}
 
 
	protected function LoadFilesFromArray($aArrFiles)
	{
		$Result = 0;
		$aArrFiles->Reset();
		while (list($No, $FilePath) = $aArrFiles->Each()) {
			$Result += $this->LoadFromFile($FilePath);
		}

		return $Result;
	}
 

	public function Parse($aPageName)
	{
		if (_DebugLevel > 1) Debug(sprintf('%s->%s->%s(%s)', basename(__file__), __class__, __function__, implode(',', func_get_args())));
		
		$Result = 0;
		if (TStr::Length($aPageName) > 0 ) {
			$this->PageName = $aPageName;
			$ArrFiles = $this->GetPageFiles($aPageName, "", 3);
			$Result = $this->LoadFilesFromArray($ArrFiles);
		}	

		return $Result;
	}


	public function ParseSubPage($aPageName, $aSubPageName)
	{
		if (_DebugLevel > 1) Debug(sprintf('%s->%s->%s(%s)', basename(__file__), __class__, __function__, implode(',', func_get_args())));
	
		if ($aPageName == "") {
			$PageName = $aSubPageName;
		}else{
			if (TStr::Pos($aSubPageName, $aPageName) == 0) {
				$PageName = $aSubPageName;
			}else{
				$PageName = $aPageName . "/" . $aSubPageName;
			}	
		}	
		
		if (TStr::Cmp($PageName, $this->TDir->DirName) == 0) {
			$PageName = TStr::Sub($PageName, TStr::Length($this->TDir->DirName) + 1);
		}

		$Result = $this->LoadDefPages($PageName);

		$ArrFiles = $this->GetPageFiles($PageName, "", 2);
		$Result += $this->LoadFilesFromArray($ArrFiles);

		return $Result;
	}

 
	public function AddFiles($aType, $aFileName)
	{
		$Type = (TFS::FileExists($aFileName) ? $aType : "Error");
		$this->TParseFile->TArrFiles->AddFile($Type, $aFileName);
	}


	public function GetFiles($aType)
	{
		return $this->TParseFile->TArrFiles->GetFiles($aType);
	}

 
	public function GetItem($aItem, $aDefault = "_NotSet_")
	{
		if (_DebugLevel > 1) Debug(sprintf('%s->%s->%s(%s)', basename(__file__), __class__, __function__, implode(',', func_get_args())));

                $Value = $this->TParseFile->GetItem($aItem, $aDefault);
                if (Defined("_IConvTo")) {
                      //return IConv("windows-1251", _IConvTo, $Value);
		      return $Value;
                }else{
		      return $Value;
                }
	}

 
	public function ShowItem($aItem, $aDefault = "_NotSet_")
	{
		Show($this->GetItem($aItem, $aDefault));
	}

 
	public function SetItem($aItem, $aValue)
	{
		if (_DebugLevel > 1) Debug(sprintf('%s->%s->%s(%s)', basename(__file__), __class__, __function__, implode(',', func_get_args())));
		$this->TParseFile->TArrData->SetItem($aItem, $aValue); 
	}

 
	public function ShowItemWithImage($aItem, $aFileName, $aAlign = "Left")
	{
		Show('<img src="' . $this->GetImage($aFileName) . '" align="' . $aAlign . '">' . $this->GetItem($aItem));
	}

 
	public function GetDir()
	{
		return $this->TDir->DirName;
	}


	protected function SearchFile($aFileName, $aPrefix)
	{
		return $this->TDir->SearchFile($aFileName, $aPrefix);
	}	
 
 
	public function GetLanguageGeoIP()
	{
		$Result = _DefLang;
		if (preg_match("/Googlebot|Yahoo|SheenBot|Twiceler|msnbot|Yandex|Rambler/", $_SERVER["HTTP_USER_AGENT"]) > 0) {
			//$Result = "bot";
		}else{
			if (_LangFromGeoIP && extension_loaded("geoip")) {
				$TArray = new TArray();
				$TArray ->AddItem("ru", "az,by,ee,kg,kz,lt,lv,md,ru,uz,ua");
				$TArray ->AddItem("de", "de,at,lu,ch,be");

				$IP = $_SERVER["REMOTE_ADDR"];
				$GeoIP = geoip_record_by_name($IP);
				// country list http://www.maxmind.com/app/iso3166
				$Language = $TArray ->SearchEx($GeoIP["country_code"]);
				if ($Language != "") {
					$Result = $Language;
				}
			}	
		}
		return $Result;	
	} 

 
	public function GetLanguages()
	{
		$Dir1 = new TDir($this->DirLanguage);
		$Dir1->Path = false;
		return $Dir1->GetFiles(false, TDir::cDir, "\/[a-z][a-z]$"); // Only 2 letters dirs
	}
 
 
	public function GetLanguage()
	{
		$ArrLang = $this->GetLanguages();
		if ($ArrLang->GetCount() > 1) {
			$Language = GetVar($_SESSION["SN_Lang"]);
			if ($Language == "") {
				$Language = GetVar($_COOKIE["CN_Lang"]);
				if ($Language == "") {
					$Language = $this->GetLanguageGeoIP();
				}	
			}
		}else{			
			$ArrLang->Reset();
			list($No, $Language) = $ArrLang->Each();
		}	
	
		return TStr::ToLower($Language);
	}


	public function SetLanguage($aLang)
	{
		if (_DebugLevel > 1) Debug(sprintf('%s->%s->%s(%s)', basename(__file__), __class__, __function__, implode(',', func_get_args())));

		$TArray1 = $this->GetLanguages();
		if (isset($aLang) && $TArray1->Search($aLang) !== false) {
			$_SESSION["SN_Lang"] = $aLang;
			setcookie("CN_Lang", $aLang, time() + (60*60*24*30*12)); // Valid 12 Monthes
		}else{
			$aLang = $this->GetLanguage();
		}
		$this->SetItem("Lang", $aLang);
		$this->Language = $aLang;
		$Dir = $this->DirLanguage . "/" . $this->Language;
		$this->TDir->DirName = $Dir;
	}


	public function ShowLanguages($aClass, $aSeparator = "<br>")
	{
		$TArray1 = $this->GetLanguages();
		if ($TArray1->GetCount() > 1) {
			$Result = "";
			$TArray1->Reset();
			while (list($No, $Language) = $TArray1->Each()) {	
				$Result .= THTML::Href("index.php?Lang=$Language", $this->GetItem($Language), $Language, $aClass) . $aSeparator;
			}
			print(TStr::LeftEnd($Result, $aSeparator));
		}
	}


	public function Show()
	{
		$this->TParseFile->TArrData->Show();
	}
}
?>