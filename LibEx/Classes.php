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
require_once(_DirCommonLib . "/Dir.php");


function GetDaysInMonth($aYear, $aMonth)
{
	return $aMonth == 2 ? ($aYear % 4 ? 28 : ($aYear % 100 ? 29 : ($aYear % 400 ? 28 : 29))) : (($aMonth - 1) % 7 % 2 ? 30 : 31);
} 

function InclFile($aFileName)
{
	if (TFS::FileExists($aFileName)) {
		print($aFileName);
	}else{
		throw new MyException("Can't include file: $aFileName", 1);
	}
}


function GetShortSize($aSize)
{
	if ($aSize > pow(10, 9)) {
		return Round($aSize / pow(10, 9), 1) . "Gb";	
	}elseif ($aSize > pow(10, 6)) {
		return Round($aSize / pow(10, 6), 1) . "Mb";	
	}else{
		return Round($aSize / pow(10, 3), 1) . "Kb";
	} 
}	


function GetThumb($aFileIn, $aFileOut, $aSize)
{
 $ArrFI = TFS::GetFileInfo($aFileIn);
 if (TFS::FileExists($aFileOut)) {
    //$Image1 = new TImage();
	//$Image1->CreateFromFile($aFileOut);
	//if ($aSize == Max($Image1->GetHeight(), $Image1->GetWidth())) {
		$FT = abs($ArrFI->GetItem("LastMod") - TFS::GetLastModTime($aFileOut));
		if ($FT < 60*1) {
			return;
		}
	//}
    TFS::Delete($aFileOut);
 }else{
	$DirName = TFS::GetDirName($aFileOut);
	if (! TFS::FileExists($DirName)) {
		TFS::MkDirs($DirName);
	}	
 }
   
 $IsThumb = false;
 $Ext = $ArrFI->GetItem("Extension");
 if (TStr::Pos("jpg,jpeg,png", TStr::ToLower($Ext)) != -1) {
    $ImageEx1 = new TImageEx();
    $IsThumb = $ImageEx1->ReCreate($aFileIn, $aFileOut, $aSize);
 }else
 if (TStr::Pos("flv,swf", TStr::ToLower($Ext)) != -1) {
    $TMpeg1 = new TMpeg();
    $TMpeg1->Create($aFileIn);
    $IsThumb = $TMpeg1->CreateThumb($aFileOut, $aSize);
 }else{
    //throw new MyException("Unknown extension: '$Ext'", 1);
 }

 if ($IsThumb) {
	TFS::Touch($aFileOut, $ArrFI->GetItem("LastMod"));
 }else{
	Show("Cant create thumb $aFileOut");	
 }
}


function GetThumbDef($aFileName, $aSize = "")
{
	$aSize = ($aSize == "" ? _ThumbWidth : $aSize);

	list($Width, $Height) = getimagesize($aFileName);
	$FileThumb = _DirThumb . "/" . TStr::ExpandR($aFileName, ".", "_" . $aSize);
	GetThumb($aFileName, $FileThumb, $aSize);
	return $FileThumb;
}


function CyrToLat($aString)
{
 $StrFind    = "йцукенгшщзхњф≥ывапролджЇ€чсмитьбю…÷” ≈Ќ√Ўў«’ѓ‘≤џ¬јѕ–ќЋƒ∆™я„—ћ»“№Ѕё";
 $StrReplace = "yzukengsszhifiivaproldgejcsmit'buYZUKENGSSZHIFIIVAPROLDGEJCSMIT'BU";
 
 $Result = "";
 for($i = 0; $i < TStr::Length($aString); $i++) {
    $c = $aString[$i];
     switch ($c) {
        case 'ж': $c = "zh";  break;
        case '∆': $c = "Zh";  break;
         
        case '€': $c = "ja";  break;
        case 'я': $c = "Ja";  break;
     
        case 'ю': $c = "yu";  break;
        case 'ё': $c = "Yu";  break;

        case 'ч': $c = "ch";  break;
        case '„': $c = "Ch";  break;
         
        case 'ш': $c = "sh";  break;
        case 'Ў': $c = "Sh";  break;
        
        case 'щ': $c = "sch"; break;
        case 'ў': $c = "Sch"; break;
    
         default:
           $pos = TStr::Pos($StrFind, $c);
           if ($pos != -1) {
                 $c = $StrReplace[$pos];
                                            }    
     }
     $Result .= $c; 
 }
 return $Result;
}


function CryptInt($aValue, $aCrypt)
{
 $Key = 13;
 if ($aCrypt) {
     $Int    = TStr::Reverse(dechex($aValue * $Key));
	 $Result = $Int . "_" . dechex(CRC32($Int));
 }else{
     list($Int, $CRC) = split ('_', $aValue);
	 if ($CRC != dechex(CRC32($Int))) {
         Error("Invalid checksum");
	 }else{
         $Result = hexdec(TStr::Reverse($Int)) / $Key;
	 } 
 }
 return $Result;
}


function CheckVar($aVar, $aURL)
{
 if (!isset($aVar)) {
   //header("Location: $aURL");    
   //exit;
 }
}


function GetRandomIntString($aStart, $aEnd, $aLength)
{
 $Result = "";
 for($i = 0; $i < $aLength; $i++) {
    $Result .= chr(rand(ord($aStart), ord($aEnd)));
 }
 return $Result; 
}


function ShowRandPict($aPathIn, $aPathOut, $aCount, $aLink, $aDelim = "&nbsp;")
{
  $Dir1  = new TDir($aPathIn);
  $TArray1 = $Dir1->GetFiles(true, 1, ".jpg");
  //$TArray1->Shuffle(); 
  if ($aCount != -1) {
    $TArray1 = $TArray1->Slice(0, $aCount);
  }
  
  $Size = 140;
  $TArray1->Reset();
  while (list($Label, $Value) = $TArray1->Each()) {
	  $FileOut = $aPathOut . "/" . TStr::ExpandR($Label, ".", "_" . $Size); 	
      GetThumb($Label, $FileOut, $Size);
	  printf($aLink . $aDelim, $FileOut);
  }
}

 
function GetLongStringLink($aString, $aMaxLines, $aLink, $aMore)
{
  $MaxChars = -1;
  
  $TArray1 = new TArray();
  $Lines = $TArray1->Split($aString, "\r|<br>|<BR>");
  if ($Lines > $aMaxLines) {
     $TArray1 = $TArray1->Slice(0, $aMaxLines);
	 $MaxChars = TStr::Length($TArray1->Implode(", "));
  }else{
	 if (TStr::Length($aString) > $aMaxLines * 80) {
	    $MaxChars = $aMaxLines * 80;
	 }
  }
  
  if ($MaxChars != -1) {
     $THref1 = new THref();
     $THref1->BuildOne($aLink, $aMore);
     $aString = TStr::Sub($aString, 0, $MaxChars) . "... (" . $THref1->GetPrintOut() . ")";
  }
  return $aString;
}


 
 function MenuActive($aString1, $aString2, $aString3)
 {
  $Class = "a1";
  if ($aString1 == $aString2) {
     printf('<td class="%s"><li>%s</li></td>', $Class, $aString3);  
  }else{
     printf('<td><a href="index.php?PN=%s" class="%s">%s</a></td>', $aString2, $Class, $aString3);	 
  }
 } 


 function FilterArray($aLabel, $aValue, &$aData)
 {
  if (is_array($aValue)) {
     $aData[3] = $aLabel; 
  }else{
     $aData[1]->LoadFromString($aValue);
     if ($aData[1]->GetItem($aData[2]) != "") {
        $Value = sprintf('<a href="index.php?PN=ItemInfo&Dir=%s&Group=%s&Item=%s"><img src="%s" border="0"></a>', 
                         $aData[4], $aData[3], $aLabel, 
                        "Temp/Thumb/User/Shop/Shop1/Images/PPC_TMobile_MDA1_CarSet.jpg");
        $aData[0]->AddItem($aLabel, $Value);
     }
  }
 }


 
class TTableMenu extends TTable
/////////////////////////////////////////////////////
{
 protected $TLang1, $TArray1, $PolarHor;

 function __construct($aTLang, $aPolarHor = true, $aActiveStr = "")
 {
  parent::__construct();
  
  $this->TLang1   = $aTLang;
  $this->TArray1  = new TArray();
  $this->PolarHor = $aPolarHor;
 }
 
 
 function AddItem($aName, $aLink, $aActive = "")
 {
  if ($aName == $aActive) {
     $Class = 'class="TableColor_Title"';
   }
  $this->TArray1->AddItem($aName, sprintf("<a href=%s>%s</a>", $aLink, $this->TLang1->GetItem($aName)));
 }

 
 function Show()
 {
  if ($this->PolarHor == true) {
     $this->Height   = 1;
     $this->Width    = $this->TArray1->GetCount();
  }else{
     $this->Height   = $this->TArray1->GetCount();
     $this->Width    = 1;
  }
  $this->Build($this->TArray1);
  $this->PrintOut();
 }
}
 

 
class TCheckForm
/////////////////////////////////////////////////////
{
	protected $TLang1, $TArray1, $LastError;

	function __construct($aTLang)
	{
		$this->TLang1   = $aTLang;
		$this->TArray1 = new TArray();
	}

 
	function Load($aTArray)
	{
		$this->TArray1 = $aTArray;
		$this->LastError = "";
	}
 
 
	function GetLastError()
	{
		return $this->LastError;
	}
 
 
	function IsLength()
	{
		$this->LastError = "";
		$MinLen = $this->TLang1->GetItem("Sys_CheckMinLengthValue", 4);
		$this->TArray1->Reset();
		while (list($Label, $Value) = $this->TArray1->Each()) {
			$Len = TStr::Length($Value, true);
			if ($Len < $MinLen) {
				$this->LastError = $this->TLang1->GetItem("String length is wrong") . " $MinLen ($Label->$Value)";
				break; 
			}
		}
		
		return $this->LastError;
	}


	function IsFilter()
	{
		$this->LastError = "";
		$TToken1 = new TToken("[ ,]");
		$TToken1->LoadFromStringPReg($this->TLang1->GetItem("Sys_CheckSpamWordsValue"));
		$TToken1->TArrData->DeleteEmpty();
  
		$this->TArray1->Reset();
		while (list($Label1, $Value1) = $this->TArray1->Each()) {
			$TToken1->TArrData->Reset();
			while (list($Label2, $Value2) = $TToken1->TArrData->Each()) {
				if (TStr::Pos(TStr::ToLower($Value1), TStr::ToLower($Value2)) != -1) {
					$this->LastError = $this->TLang1->GetItem("These words are in ban list") . " $Value1 ($Label2->$Value2)";
					break;
				}
			}
		}
		return $this->LastError;
	}
}


class TReport
/////////////////////////////////////////////////////
{
	protected $FileName;
 
 
	function __construct($aFileName)
	{
		$this->FileName = $aFileName;
	}

 
	function WriteToFile($aString)
	{
		$TFile1 = new TFile(); 
		$TFile1->Open($this->FileName, "a");
		$TFile1->Write($aString);
		$TFile1->Close();
	}

 
	function Log($aString)
	{
		if (_DebugLevel > 0) Show($aString, true);
  
		$Order = array("\r\n", "\n", "\r");
		$this->WriteToFile(date("Y/m/d h:i:s") . "\t" . 
						$_SERVER["REMOTE_ADDR"] . "\t" .
						TStr::Replace($aString, $Order, "\t") . "\n");
	}
} 


class TNoSpam
/////////////////////////////////////////////////////
{
	function __construct($aBkg=0xCCCCCC)
	{
		$this->SetValue("BkgColor", $aBkg);
	}

 
	protected function GetRandomIntString($aStart, $aEnd, $aLength)
	{
		$Result = "";
		for($i = 0; $i < $aLength; $i++) {
			$Result .= chr(rand(ord($aStart), ord($aEnd)));
		}
		return $Result; 
	}


	public function GetValue($aName)
	{
		return $_SESSION["SN_Spam_$aName"];
	}

 
	protected function SetValue($aName, $aValue)
	{
		$_SESSION["SN_Spam_$aName"] = $aValue;
	}

 
	protected function MakeImage($aBkgColor, $aString, $aNoice)
	{
		$aFont = 5;
		$Width  = TStr::Length($aString) * imagefontwidth($aFont);
		$Height = imagefontheight($aFont);
  
		$TImage1 = new TImage();
		$TImage1->Create($Width+2, $Height+2);	
		$TImage1->FilledRectangle(0, 0, $Width+2, $Height+2, $aBkgColor); 
		$TImage1->String($aFont, 1, 1, $aString, 0x000000);
 
		// Noise
		for ($i = 0; $i < $aNoice; $i++) {
			$TImage1->Line(rand(0, $Width), rand(0, $Height), rand(0, $Width), rand(0, $Height), 0); 	
		}
		$TImage1->ImgJpeg("");
	}
 
 
	public function ShowImageSend($aLink)
	{
		$Value = $this->GetRandomIntString(1, 9, 5);
		$this->SetValue("Number", $Value);
		printf('<input type="text" name="_NoSpamValue_Chk">'. "&nbsp;");
		printf('<img src="%s">', $aLink);
	}
 
 
	public function ShowImage($aBkgColor, $aString, $aNoice)
	{
		header('content-type: image/jpeg');	
		$this->MakeImage($aBkgColor, $aString, $aNoice);
	}

 
	public function ShowImageReceive()
	{
		$this->ShowImage($this->GetValue("BkgColor"), $this->GetValue("Number"), 3);
	}
}


class TUrl
/////////////////////////////////////////////////////
{
 function GetPath()
 {
  return $_SESSION["CurDir"];
 }

 function GetTopPath()
 {
  return $_SESSION["TopDir"];
 }

 function CmpLevel($aValue = "") 
 {
   if ($aValue == "") $aValue = $this->GetPath();
   return TStr::SubCount($aValue, "/") - TStr::SubCount($this->GetTopPath(), "/");
 }

 function SetPath($aValue)
 {
  if (TFS::FileExists($aValue)) {
      $_SESSION["CurDir"] = $aValue;
  }
 }

 function ChangePath($aValue)
 {
  if ($aValue == "..") {
    $Path1 = TStr::Sub($this->GetPath(), 0, TStr::PosR($this->GetPath(), "/"));
    if ($this->CmpLevel($Path1) >= 0) {
       $this->SetPath($Path1);
    }
  }else
  if ($aValue != "") {
       $this->SetPath($this->GetPath() . "/" . $aValue);
  } 
 }

 function InitPath($aValue)
 {
  $this->SetPath($aValue);
  $_SESSION["TopDir"] = $aValue;
 }
}


class TNumToStr
/////////////////////////////////////////////////////
{
	protected $_1_2, $_1_19, $des, $hang, $namerub, $nametho, $namemil;
	
	
	function __construct($aLang)
	{
		$this->SetLang($aLang);
	}
 
	public function SetLang($aLang)
	{
		if ($aLang == "en") {
			$this->_1_2		= array("", "one ", "two ");
			$this->_1_19	= array("", "one ", "two ", "three ", "four ", "five ", "six ", "seven ", "eight ", "nine ", "ten ",
								"eleven ", "twelfth ", "тринадцать ", "четырнадцать ", "п€тнадцать ", "шестнадцать ", "семнадцать ", "восемнадцать ", "дев€тнадцать "); 
			$this->des		= array("", "", "двадцать ", "тридцать ", "сорок ", "п€тьдес€т ", "шестьдес€т ", "семьдес€т ", "восемдес€т ", "дев€носто "); 
			$this->hang 	= array("", "сто ", "двести ", "триста ", "четыреста ", "п€тьсот ", "шестьсот ", "семьсот ", "восемьсот ", "дев€тьсот "); 

			$this->namerub	= array("", "гривна", "гривны ", "гривен "); 
			$this->nametho	= array("", "тыс€ча ", "тыс€чи ", "тыс€ч "); 
			$this->namemil	= array("", "миллион ", "миллиона ", "миллионов "); 
		}elseif ($aLang == "ru") {
			$this->_1_2		= array("", "одна ", "две ");
			$this->_1_19	= array("", "один ", "два ", "три ", "четыре ", "п€ть ", "шесть ", "семь ", "восемь ", "дев€ть ", "дес€ть ",
								"одиннацать ", "двенадцать ", "тринадцать ", "четырнадцать ", "п€тнадцать ", "шестнадцать ", "семнадцать ", "восемнадцать ", "дев€тнадцать "); 
			$this->des		= array("", "", "двадцать ", "тридцать ", "сорок ", "п€тьдес€т ", "шестьдес€т ", "семьдес€т ", "восемдес€т ", "дев€носто "); 
			$this->hang 	= array("", "сто ", "двести ", "триста ", "четыреста ", "п€тьсот ", "шестьсот ", "семьсот ", "восемьсот ", "дев€тьсот "); 

			$this->namerub	= array("", "гривна", "гривны ", "гривен "); 
			$this->nametho	= array("", "тыс€ча ", "тыс€чи ", "тыс€ч "); 
			$this->namemil	= array("", "миллион ", "миллиона ", "миллионов "); 
		}elseif ($aLang == "ua"){
			$this->_1_2		= array("", "одна ", "дв≥ ");
			$this->_1_19	= array("", "один ", "два ", "три ", "чотири ", "п€ть ", "ш≥сть ", "с≥м ", "в≥с≥м ", "дев€ть ", "дес€ть ", 
								"одинадц€ть ", "дванадц€ть ", "тринадц€ть ", "чотирнадц€ть ", "п€тнадцать ", "ш≥стнадц€ть ", "с≥мнадц€ть ", "в≥с≥мнадц€ть ", "дев€тнадц€ть "); 
			$this->des		= array("", "", "двадц€ть ", "тридц€ть ", "сорок ", "п€тьдес€ть ", "ш≥стьдес€ть ", "с≥мдес€ть ", "в≥с≥мдес€ть ", "дев€носто "); 

			$this->hang		= array("", "сто ", "дв≥сти ", "триста ", "чотириста ", "п€тьсот ", "ш≥стьсот ", "с≥мсот ", "в≥с≥мсот ", "дев€тьсот "); 
			$this->namerub	= array("", "гривн€ ", "гривн≥ ", "гривень "); 
			$this->nametho	= array("", "тис€ча ", "тис€ч≥ ", "тис€ч "); 
			$this->namemil	= array("", "м≥л≥он ", "м≥л≥она ", "м≥л≥он≥в "); 
		}
	}


	protected function Semantic($i, &$words, &$fem, $f)
	{ 
		$words = ""; 
		$fl = 0; 
		if ($i >= 100) { 
			$jkl = intval($i / 100); 
			$words .= $this->hang[$jkl]; 
			$i %= 100; 
		} 
		if ($i >= 20){ 
			$jkl = intval($i / 10); 
			$words .= $this->des[$jkl]; 
			$i %= 10; 
			$fl = 1; 
		} 
 
		switch ($i) { 
			case 1: $fem=1; break; 
			case 2: 
			case 3: 
			case 4: $fem=2; break; 
			default: $fem=3; break; 
		} 
 
		if ($i) { 
			if ($i < 3 && $f > 0) { 
				if ($f >= 2 ) { 
					$words .= $this->_1_19[$i]; 
				}else{ 
					$words .= $this->_1_2[$i]; 
				} 
			}else{ 
				$words .= $this->_1_19[$i]; 
			} 
		} 
	} 


	public function Convert($L) 
	{ 
		$s   = " "; 
		$s1  = " "; 
		$s2  = " "; 
		$kop = intval((($L * 100) - intval($L) * 100 )); 
		$L = intval($L); 

		if ($L >= 1000000) { 
			$many = 0; 
			$this->Semantic(intval($L / 1000000), $s1, $many, 2); 
			$s .= $s1 . $this->namemil[$many]; 
			$L %= 1000000; 
			if ($L == 0) { 
				//$s .= "грн. "; 
			} 
		} 

		if ($L >= 1000) { 
			$many = 0; 
			$this->Semantic(intval($L / 1000), $s1, $many, 1); 
			$s .= $s1 . $this->nametho[$many]; 
			$L %= 1000; 
			if ($L == 0){ 
				$s .= "грн. "; 
			} 
		} 

		if ($L != 0) { 
			$many = 0; 
			$this->Semantic($L, $s1, $many, 0); 
			$s .= $s1 . $this->namerub[$many]; 
		} 

		$s .= $kop; 
		return $s; 
	} 
}
?>