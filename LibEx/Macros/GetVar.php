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

class tMacros_GetVar extends tMacros_BaseClass
{
	const	cName	= 0,
			cItem	= 1;
	const	cSyntax = "Name|Item";
	const	cDescr	= <<<BAR
Gets PHP 'Item' from such 'Name': _GET, _POST, _SERVER, _ENV, _COOKIE, _REQUEST
Example: GetVar|_SERVER|SERVER_NAME
href="http://jdv-soft.com/index.php?PN=Help&PS=ProjSimpleSiteCreator/Macros_GetVar"
BAR;

	
	public function InitDefParam()
	{
	}

	
	public function Build()
	{
		if (!$this->CheckParam()) return;

		$this->InitDefParam();

		$this->Data = "";	
		$Name = $this->TArrParam[self::cName];
		$Item = $this->TArrParam[self::cItem];
		switch(TStr::ToLower($Name)) {
			case 	"_get":		$this->Data = $_GET[$Item]; 	break;
			case 	"_post":	$this->Data = $_POST[$Item];	break;
			case 	"_server":	$this->Data = $_SERVER[$Item];	break;
			case 	"_env":		$this->Data = $_ENV[$Item];		break;
			case 	"_cookie":	$this->Data = $_COOKIE[$Item];	break;
			case 	"_request":	$this->Data = $_REQUEST[$Item];	break;
			
			default:	$this->LogError(sprintf("Unknown variable '%s'", $Name));
		}
		return $this->Data;	
	}
}
?>
