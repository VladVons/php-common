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

include_once("Array.php");
include_once("String.php");
include_once("Sys.php");



class TDir
/////////////////////////////////////////////////////
{
	const cFile = 1, cDir = 2, cBoth = 3; 
 
	protected $SubDir, $Type, $FileIncl, $FileExcl;
	public $TArrData, $DirName, $Path;

	function __construct($aDirName)
	{
		if (!TFS::IsDir($aDirName)) {
			//trigger_error("Dir not found $aDirName", E_USER_NOTICE);
			//Debug(sprintf('%s->%s->%s(%s)', basename(__file__), __class__, __function__, implode(',', func_get_args())));
		}	

		$this->TArrData	= new TArray();
		$this->DirName	= $aDirName;
		$this->Path		= true;
	}

 
	function GetDirName()
	{
		$Pos1 = TStr::PosR($this->DirName, "/"); 
		if ($Pos1 == -1) {
			return $this->DirName;
		}else{
			return TStr::Sub($this->DirName, $Pos1 + 1); 
		}  
	}
 
 
	protected function GetFilesRecurs($aDirPath, $aDepth)
	{
		$DirHandle = @opendir($aDirPath);
		if ($DirHandle) {
			while(false !== ($File = readdir($DirHandle))) {
				if ($File != "." && $File != "..") {
					$FileName = $aDirPath . "/" . $File;
					if (TFS::IsDir($FileName) && $this->SubDir && $aDepth > 0) {
						$this->GetFilesRecurs($FileName, $aDepth - 1);
					}

					if (($this->Type == self::cFile && !TFS::IsDir($FileName)) || 
						($this->Type == self::cDir && TFS::IsDir($FileName))  || 
						($this->Type == self::cBoth) ) 
					{
						if (($this->FileIncl == "" ||  preg_match("/$this->FileIncl/i", $FileName)) &&
							($this->FileExcl == "" || !preg_match("/$this->FileExcl/i", $FileName))) 
						{
							$this->TArrData->AddItemToEnd($this->Path ? $FileName : $File);
						}
					}
				}
			}
			closedir($DirHandle);
		}
	}

 
	function GetFiles($aSubDir = false, $aType = TDir::cFile, $aFileIncl = "", $aFileExcl = "", $aDepth = 999)
	{
		// $aType values: 1 only files, 2 only Dirs, 3 both
		$this->SubDir   = $aSubDir;
		$this->Type     = $aType;
		$this->FileIncl = TStr::ToLower($aFileIncl);
		$this->FileExcl = TStr::ToLower($aFileExcl);
		$this->TArrData->Clear();
		$this->GetFilesRecurs($this->DirName, $aDepth);
		return $this->TArrData->SortByValue();
	}

 
	function GetLastDir($aDirPath, $aSubDir = true)
	{
		do {
			$ArrDirs = $this->GetFiles($aDirPath, false, self::cDir);
			if ($ArrDirs->GetCount() == 0)  break; 
   	
			$ArrDirs = $ArrDirs->SortByValue(false);
			$ArrDirs->Reset();
			list($No, $FullPath) = $ArrDirs->Each();
			$FileName = TFS::GetFileName($FullPath);
			$aDirPath .= "/". $FileName;
		} while ($aSubDir);
  
		return $aDirPath;
	}

 
	function SearchFile($aFileName, $aPrefix = "/", $aDepth = 99)
	{
		$Len = TStr::Length($this->DirName);
		for ($i = $Len + 1; $i >= 0; $i--) {
			if (($i < $Len && $this->DirName[$i] == "/") || ($i == $Len)) {
				$FilePath = TStr::Sub($this->DirName, 0, $i) . $aPrefix . $aFileName;
				if ($aDepth-- == 0) {
					break;
				}elseif (TFS::FileExists($FilePath)) {
					return $FilePath;
				}
			}
		}
		return "";
	}


	function SearchFiles($aFileName, $aPrefix = "/")
	{
		$Len = TStr::Length($this->DirName);
		for ($i = 0; $i <= $Len; $i++) {
			if (($i < $Len && $this->DirName[$i] == "/") || ($i == $Len)) {
				$FilePath = TStr::Sub($this->DirName, 0, $i) . $aPrefix . $aFileName;
				if (TFS::FileExists($FilePath)) {
					$this->TArrData->AddItemToEnd($FilePath);
				}
			}	
		}
	
		return $this->TArrData;
	}
}
?>
