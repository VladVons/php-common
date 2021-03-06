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

include_once("String.php");
include_once("Common.php");
include_once("Stream.php");


class TExecAPI
/////////////////////////////////////////////////////
{
 protected $Passw, $PathSudo, $PathScript, $DirOS;

 function __construct($aPathSudo, $aPathScript, $aDirOS)
 {
  $this->PathSudo   = $aPathSudo;
  $this->PathScript = $aPathScript;
  $this->DirOS      = $aDirOS;
 }

 function Run($aScript)
 {
  if ($aScript != "") {
     $SubScript = "$this->DirOS/$aScript";
  }
  $ExecStr = $this->PathSudo . " " . $this->PathScript . " " . $SubScript;
  $Pipe1 = new TPipe();
  $Pipe1->Open("$ExecStr", 'r');
  $Array1 = $Pipe1->GetsA();
  return $Array1->Trim(TArray::cRight);
 }
}
?>