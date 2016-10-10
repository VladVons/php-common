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
$ModulesNeed = array("gd", "session");
reset($ModulesNeed);
while (list($Label, $Value) = each($ModulesNeed)) {
	if (!extension_loaded($Value)) {
		print("PHP extension <b>'$Value'</b> not found. <a href='http://pear.php.net/packages.php'>Install</a> it first<br>"); 
	}
}
$TimerStart = Microtime(true);

// Set compression ON in php.ini {zlib.output_compression = On}
//ob_start("ob_gzhandler", 9);

//Start calculating page size
ob_start();
session_start();

//phpinfo(INFO_VARIABLES);

require_once(_DirCommonLib   . "/Error.php");
require_once(_DirCommonLib   . "/Image.php");
require_once(_DirCommonLib   . "/Cookies.php");
require_once(_DirCommonLib   . "/Mail.php");
require_once(_DirCommonLibEx . "/LangTXT.php");
require_once(_DirCommonLibEx . "/Classes.php");

$aGet     = new TArray($_GET);
$aPost    = new TArray($_POST);

$aPN      = GetUnmarked(urldecode($aGet->GetItem("PN"))); 
$aPS      = GetUnmarked(urldecode($aGet->GetItem("PS"))); 
$aCatalog = urldecode($aGet->GetItem("Catalog"));
$aGroup   = urldecode($aGet->GetItem("Group"));
$aItem    = urldecode($aGet->GetItem("Item"));
$aLang    = urldecode($aGet->GetItem("Lang")); 
$aAction  = urldecode($aGet->GetItem("Action")); 

if (_DebugLevel > 0) Debug(sprintf('%s->%s', basename(__file__), _DirRoot));
if (_DebugLevel > 0) Debug(sprintf('%s->%s', basename(__file__), _DirCommon));

if (IsLinkExternal($aCatalog)) {
	throw new MyException("External link in 'aCatalog' detected: $aCatalog", 1);
}

$TError1 = new TError();
$TError1->Init(_LogFile, _AdminMail, _DebugLevelPHP);
 
$gTLang = new TLang(_DefLang, _DirUser . "/Lang");
if (TStr::Length($aLang) == 0) {
	$aLang = $gTLang->GetLanguage();
}	
$gTLang->SetLanguage($aLang);

$Index_HeadUser = "Index_HeadUser.php"; 
if (TFS::FileExists($Index_HeadUser)) {
	if (_DebugLevel > 0) Debug(sprintf('%s->%s', basename(__file__), $Index_HeadUser));
	include $Index_HeadUser;
}
$gTLang->TParseFile->TPath->AddDir("Common/Lang");
$gTLang->TParseFile->TPath->AddDir("Common/Include");
$gTLang->TParseFile->TPath->AddDir("../Common/Include");
$gTLang->TParseFile->TPath->AddDir("../Common/Page");
$gTLang->TArrDefPage->AddItemToEnd("PageIndex");
$gTLang->TArrDefPage->AddItemToEnd("PageLeft");
$gTLang->LoadDefPages();

setlocale (LC_ALL, $gTLang->GetItem("SysPHPLocale", "uk_UA.CP1251"));

//$gTLang->SetItem("UseCache", _UseCache);
//$CachePage = new TCachePage($_SERVER["REQUEST_URI"]);
//$CacheStr = $CachePage->Read();
//if ($CacheStr != "") {
	//print("FromCacheData: <br>" . $CacheStr);
//}else{

if (TStr::Length($aPN) > 0 && $aPN != "index") {
    $CurPN = $aPN;
}else{
	$CurPN = _StartPN;
}

$CurLang = $gTLang->GetLanguage();
$gTLang->LoadFromFile(_DirCommon . "/Lang/$CurLang/$CurPN.txt");
$gTLang->Parse($CurPN);
//print_r($gTLang->TIniFileEx1->ArrData);
if (TStr::Length($aPS) > 0) {
	if (IsLinkExternal($aPS)) {
		throw new MyException("External link in 'aPS' detected: $aPS", 1);
	}
	$gTLang->ParseSubPage($aPN == _DefPN ? "" : $aPN, $aPS);
}	

if (TFS::FileExists($gTLang->GetDir() . "/$CurPN.php")) {
	$IncludeFileName = $gTLang->GetDir() . "/$CurPN.php";
}elseif (TFS::FileExists("$CurPN.php")) {
 	$IncludeFileName = "$CurPN.php";
}elseif (TFS::FileExists(_DirCommonPage ."/$CurPN.php")) {
 	$IncludeFileName = _DirCommonPage . "/$CurPN.php";
}elseif (TFS::FileExists(_DefPN . ".php")) {
	$IncludeFileName = _DefPN . ".php";
}elseif (TFS::FileExists(_DirCommonPage . "/" . _DefPN . ".php")) {
	$IncludeFileName = _DirCommonPage . "/" . _DefPN . ".php";
}else{
	throw new MyException("Can't open neither: " . 
		$gTLang->GetDir() . "/$CurPN.php, " . 
		"$CurPN.php, " . 
		_DefPN . ".php, " .
		_DirCommonPage . "/" . _DefPN . ".php", 1); 
}
//$gTLang->AddFiles("PHP", RootFileName(__FILE__));
$gTLang->AddFiles("PHP", $IncludeFileName);
if (_DebugLevel > 0) Debug(sprintf('%s->%s', basename(__file__), $IncludeFileName));
?>
