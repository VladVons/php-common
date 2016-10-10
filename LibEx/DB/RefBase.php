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

require_once(_DirCommonLib . "/Common.php");
require_once(_DirCommonLib . "/Dir.php");
require_once(_DirCommonLibEx . "/DB/DbBase.php");


class TRefBaseRecord
/////////////////////////////////////////////////////
{
	protected $TRefBase;
	public $TArrData;	
	

	public function __construct(TRefBase $aTRefBase)
	{
		$this->TRefBase = $aTRefBase;
		$this->TArrData = new TArray();
	}


	public function Error($aFunc, $aMsg)
	{
		Error(sprintf('%s->%s->%s. %s', basename(__file__), __class__, $aFunc, $aMsg));
	}


	public function GetNoImage()
	{
		$PathNoFoto = $this->TRefBase->GetLang()->GetItem("SysPathNoFoto");
		if (TFS::FileExists($PathNoFoto)) {
			return $PathNoFoto;
		}else{
			$this->Error(__function__, "unknown 'SysPathNoFoto'");
		}
	
	}
	

	public function GetImage($aNoFoto = true)
	{
		$Image = $this->TArrData->GetItem("Image");
		$Path  = $this->TRefBase->GetImageDir() . "/" . $Image;
		if ($Image != "" && TFS::FileExists($Path)) { 
			return $Path;
		}else
		if ($aNoFoto) {
			return $this->GetNoImage();
		}else{
			return "";
		}
	}


	public function GetImages()
	{
		$Path = $this->GetImage(false);
		if ($Path == "") { 
			return $this->GetNoImage();
		}else{
			$Dir1 = new TDir(TFS::GetDirName($Path));
			return $Dir1->GetFiles(false, TDir::cFile, TFS::GetFileName($Path, ".jpg"));
		}	
	}	


	public function GetThumb()
	{
		$FileIn = $this->GetImage();
		$FileOut = _DirThumb . "/" . TStr::ExpandR($FileIn, ".", "_" . _ThumbWidth);
		GetThumb($FileIn, $FileOut, _ThumbWidth);
		return $FileOut;
	}
	

	public function GetField($aValue)
	{
		if ($aValue == "Image") {
			return $this->GetImage();
		}elseif ($aValue == "Thumb"){
			return $this->GetThumb();
		}else{
			return $this->TArrData->GetItem($aValue);
		}  
	} 


	public function SetField($aLabel, $aValue)
	{
		$this->TArrData->SetItem($aLabel, $aValue);
	}


	public function SetFields(TArray $aTArray, $aPrefix)
	{
		$aTArray->Reset();
		while (list($Label, $Value) = $aTArray->Each()) {
			if (TStr::Cmp($Label, $aPrefix) == 0) {
				$Key = TStr::Replace($Label, $aPrefix, "");
				$this->TArrData->SetItem($Key, $Value);
			}
		}
	}
	

	public function ShowField($aValue)
	{
		Show($this->GetField($aValue));
	}
}



abstract class TRefBase extends TDbBase
/////////////////////////////////////////////////////
{
	abstract public function IsGroup($aID);


	public function __construct(TLang $aTLang)
	{
		parent::__construct($aTLang); 
		$this->Record = new TRefBaseRecord($this);
	}
}
?>
