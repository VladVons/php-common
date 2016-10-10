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

require_once(_DirCommonLibEx . "/FileBase.php");

 
// SimpleXMLElement is in exteranl PHP extension  'SimpleXML' 
class TXML extends TFileBase
/////////////////////////////////////////////////////
{
 protected $SXE, $FileName;
 
 function __construct($XmlStr = '<root></root>')
 {
  parent::__construct();
  $this->SXE = new SimpleXMLElement($XmlStr);	 
 }
	
 function LoadFromFile($aFileName)
 {
  $this->CurFileName = $aFileName; 
  $this->SXE = simplexml_load_file($aFileName);
 } 	 

 function LoadFromString($aString)
 {
  $this->SXE = simplexml_load_string($aFileName);
 } 	 

 function SaveToFile($aFileName = "")
 {
  $this->SXE->asXML(empty($aFileName) ? $this->FileName : $aFileName);
 } 	 
 
 function Get()
 {
  return $this->SXE;
 } 	 
 
}
?>
