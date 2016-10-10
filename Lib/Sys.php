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

class TFS
/////////////////////////////////////////////////////
{
 static function Copy($aNameOld, $aNameNew) 
 {
  return @copy($aNameOld, $aNameNew);
 }

 static function Rename($aNameOld, $aNameNew) 
 {
  return @rename($aNameOld, $aNameNew);
 }

 static function IsDir($aName) 
 {
  return @is_dir($aName);
 }

 static function IsFile($aName) 
 {
  return @is_file($aName);
 }

 static function FileExists($aName) 
 {
  return @file_exists($aName);
 }
 
 static function GetLastModTime($aName) 
 {
  return @filemtime($aName);
 }

 static function GetFileName($aPath, $aSuffix = "") 
 {
  return @basename($aPath, $aSuffix);
 }

 static function GetDirName($aPath) 
 {
  return @dirname($aPath);
 }
 
 static function MkDir($aPath) 
 {
  return @mkdir($aPath);
 }
 
 static function DeleteDir($aPath) 
 {
  return @rmdir($aPath);
 }
  
 static function Touch($aName, $aTime = "") 
 {
  return @touch($aName, $aTime);
 }

 static function Delete($aName) 
 {
  return @unlink($aName);
 }

 static function GetFileSize($aName) 
 {
  return @filesize($aName);
 }

 static function GetFullPath($aName)
 {
  return @realpath($aName);
 }

 static function GetFileInfo($aFileName)
 {
  $PathInfo = pathinfo($aFileName);
  
  $Array1 = new TArray();
  $Array1->AddItem("DirName",   $PathInfo["dirname"]);
  $Array1->AddItem("BaseName",  $PathInfo["basename"]);
  $Array1->AddItem("FileName",  $PathInfo["filename"]);
  $Array1->AddItem("BaseFileName",  $PathInfo["dirname"] . "/" . $PathInfo["filename"]);
  $Array1->AddItem("Extension", $PathInfo["extension"]);  
  $Array1->AddItem("Size", TFS::IsDir($aFileName) ? -1 : TFS::GetFileSize($aFileName));
  $Array1->AddItem("Date", date("Y/m/d", TFS::GetLastModTime($aFileName)));
  $Array1->AddItem("LastMod", TFS::GetLastModTime($aFileName));
  $Array1->AddItem("Original", $aFileName);
  return $Array1;
 }
 
 static function MkDirs($aPath)
 {
  if (TFS::IsDir($aPath)) return true;

  $DirName = TFS::GetDirName($aPath);
  if (!TFS::MkDirs($DirName)) {
     return false;
  }
  return TFS::MkDir($aPath);
 }
 
 
}

?>