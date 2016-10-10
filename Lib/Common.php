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

Tips:
http://www.interface.ru/home.asp?artId=21133
------------------------------------*/

define("_VerNum", "3.7.0");
define("_Version", "JDV-Soft. SSC. Ver " . _VerNum);
define("_ErrLog", "Error " . __FILE__ . "(" . __LINE__ . ") " . __CLASS__ . "->" . __FUNCTION__);
define("_Dbg", "sprintf('%s %s::%s(%s)', basename(__FILE__), __CLASS__, __FUNCTION__, implode(',', func_get_args()))");
define("_Dbg2", __FILE__ . " ". __CLASS__ . " " . __FUNCTION__);


function CrToBr($aString)
{
	return str_replace("\n", "<br>\n", $aString);
}


function Show($aString, $aBold = false)
{
	//$aString = CrToBr($aString . "\n");
	$aString = CrToBr($aString);
	if ($aBold) {
		print_r("<strong>$aString</strong>");
	}else{
		print_r($aString);
	}
}


function DbgLevel($aLevel, $aMaxLevel, $aFunc, $aMsg="")
{
  if ($aLevel < $aMaxLevel)
    printf("%s: %s <br>\n", $aFunc, $aMsg);
}


function LogStr($aFile, $aString, $aShow = true)
{
  $Data = sprintf("%s, %s<br>\n", date("Y-m-d H:i:s"), $aString);       
  file_put_contents($aFile, $Data, FILE_APPEND);

  if ($aShow)
    print($Data);
}


function Debug($aObject, $aToFile = false)
{
	if (is_array($aObject)) {
		$String1 = implode("\r\n", $aObject) . "\n";
	}else{
		$String1 = $aObject . "\n";
	}
	$String1 = "Debug:" . $String1;

	if ($aToFile) {
		$FileName = _DirTemp . "/Debug.txt";
		$fHandle = fopen($FileName, "a") or die("can't write to file $FileName");
		fwrite($fHandle, $String1);
		fclose($fHandle);
	}else{
		Show($String1);
	}
}


function _d2($aObject, $aToFile = false)
{
	return Debug($aObject, $aToFile);
}


function _d($aObject)
{
    print("dbg:\n");

    if (is_array($aObject)) {
	print_r($aObject);
    }else{        
	print($aObject);
    }

    die();
}


function Error($aString = "")
{
	Show("\r" . "ERROR: " . $aString . "\r");
	Debug($aString, 0, true);
	die();
}


function SaveToFile(&$aObj, $aFileName) {
	$HFile = fopen($aFileName, "w");
	if ($HFile !== false) {
		$Str1 = serialize($aObj);
		fwrite ($HFile, $Str1);
		return fclose($HFile);
	}
	return false;
}


function LoadFromFile(&$aObj, $aFileName) {
	$HFile = fopen($aFileName, "r");
	if ($HFile !== false) {
		$Str1 = fread($HFile, filesize($aFileName));
		$aObj = unserialize($Str1);
		return fclose($HFile);
	}
	return false;
}


function GetVar($aVar)
{
	return (isset($aVar) ? $aVar : "");
}


function GetQuotedValue($aString, $aLabel)
{
	$Pos1 = TStr::Pos($aString, $aLabel);
	if ($Pos1 != -1) {
		$Pos1 += TStr::Length($aLabel);
		$Pos2 = TStr::Pos($aString, TStr::Right($aString, 1), $Pos1);
		if ($Pos2 != -1) {
			return TStr::Sub($aString, $Pos1, $Pos2 - $Pos1 - 1);
		}
	}
	return $aString;
}

function GetUnmarked($aString)
{
	$Pos1 = TStr::Pos($aString, "#");
	if ($Pos1 == -1) {
		return $aString;
	}else{
		return TStr::Left($aString, $Pos1);
	}
}

function GetStatLink($aLink)
{
 $Cnt = preg_match_all("/[\?|\&](.*)=(.*)/", $aLink, $NotUsed);
 if ($Cnt == 1) {
	$Result = preg_replace("/index\.php\?PN=(.*)/", "index-PN-$1.html", $aLink);
	$Result = $aLink;
 }else{
	$Result = $aLink;
 }
 return $Result;
}


function ShowStatLink($aLink)
{
 print(GetStatLink($aLink));
}


function IsLinkExternal($aPath)
{
 return preg_match("/(http\:|https\:|www\.|[0-9]+\.[0-9]+\.[0-9]+\.[0-9])/i", $aPath);
}


function IsPicture($aFileName)
{
 return preg_match("/[.](jpg|png|gif)$/i", $aFileName);
}


function UrlCode($aStr, $aMode)
{
	if ($aMode == "RawEncode") {
		return implode("/", array_map("rawurlencode", explode("/", $aStr)));
	}else{
		Error("Unknown mode '$aMode' in UrlCode()");
	}
}


function HttpFileName($aFileName)
{
	if (IsLinkExternal($aFileName)) {
		return $aFileName;
	}else{
		$ScriptDir = TFS::GetDirName($_SERVER["SCRIPT_NAME"]);
		return "http://" . $_SERVER["HTTP_HOST"] . ($ScriptDir == "/" ? "/" : $ScriptDir . "/") . $aFileName;
	}
}


function HttpSaveURI($aLink = "")
{
	if ($aLink == "") {
		$_SESSION["REQUEST_URI"] = $_SERVER["REQUEST_URI"];
	}else{
		$_SESSION["REQUEST_URI"] = $aLink;
	}
}


function HttpGoURI()
{
	$URI = $_SESSION["REQUEST_URI"];
	if (!Empty($URI)) {
		header("Location: $URI");
	}	
}


function RootFileName($aFileName)
{
	$Pos = TStr::Pos($aFileName, $_SERVER["DOCUMENT_ROOT"]);
	if ($Pos != -1) {
		return TStr::Sub($aFileName, $Pos + TStr::Length($_SERVER["DOCUMENT_ROOT"]));
	}
	return $aFileName;
}


function IConvert($aStr)
{
	return iconv("cp1251", "koi8-u", $aStr);
}


function Guest($aStr)
{
	return 1;
}


class MyException extends Exception
{
 function __construct($aMsg, $aCode)
 {
  parent::__construct($aMsg, $aCode);
 }

 function __toString() {
  $errorMsg = "Date: " . date("Y-m-d h:i:s") . "\t" .
			  "Message: " . $this->GetMessage() . "\t" .
			  "File: "  . $this->getFile() . "\t" .
			  "Line: "  . $this->getLIne() . "\t" .
			  "Code: "  . $this->code . "\t" .
			  "Agent: " . $_SERVER['HTTP_USER_AGENT'] . "\n";

  error_log($errorMsg, 3, _LogFile);

  $HtmlMsg = "<br>\n" . str_replace("\t", "<br>\n", $errorMsg) . "<br>\n";
  return $HtmlMsg;
 }
}

?>