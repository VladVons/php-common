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

class TMail
/////////////////////////////////////////////////////
{
 Public $MIFromName, $MIFromAddress, $MIToName, $MIToAddress, $MICCAddress, $MIBCCAddress, $MIMessage, $MISubject, $MICharset;

 function TMail()
 {
  $this->Clear();
 }

 function Clear()
 {
  $this->MIFromName		= "";
  $this->MIFromAddress	= "";
  $this->MIToName		= "";
  $this->MIToAddress	= "";
  $this->MICCAddress	= "";
  $this->MIBCCAddress	= "";
  $this->MIMessage		= "";
  $this->MISubject		= "";
  $this->MICharset		= "windows-1251";		
 }

 function Send()
 {
  $Header = "Return-path: <$this->MIFromAddress>\r\n" .
            "From: $this->MIFromName <$this->MIFromAddress>\r\n" .
            "Reply-To: $this->MIFromName <$this->MIFromAddress>\r\n" .
            ($this->MICCAddress  == "" ? "" : "CC: <$this->MICCAddress>\r\n") .
            ($this->MIBCCAddress == "" ? "" : "BCC: <$this->MIBCCAddress>\r\n") .
            "Sender: $this->MIFromName <$this->MIFromAddress>\r\n" .
            "X-Mailer: PHP/" . phpversion() . "\r\n" .
            "MIME-Version: 1.0\r\n" .
            "Content-type: text/plain; charset=$this->MICharset\r\n";
  $Result = mail($this->MIToAddress, $this->MISubject, $this->MIMessage, $Header);
  return $Result;
 }
}

?>
