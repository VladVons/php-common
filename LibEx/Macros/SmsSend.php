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

class tMacros_SmsSend extends tMacros_BaseClass
{
	const	cPhonesNo	= 0;
	const	cMessage	= 1;
	const	cDescription= 2;
	const	cAlphaName	= 3;
	const	cProvider	= 4;
	const	cLogin		= 5;
	const	cPassword	= 6;
	const	cSyntax		= "PhonesNo|Message|Description|AlphaName|Provider|[Login]|[Password]";
	const	cDescr	= <<<BAR
Send SMS

href="http://jdv-soft.com/index.php?PN=Help&PS=ProjSimpleSiteCreator/Macros_SmsSend"
BAR;

	public function InitDefParam()
	{
	}	
	
	
	public function Build()
	{
		if (!$this->CheckParam()) return;

		$this->InitDefParam();
		
		switch($this->TArrParam[self::cProvider]) {
			case "SmsFly": 
				$this->Data = $this->SendVia_SmsFly(); break;
			case "": 
				$this->Data = "Error: Empty provider"; break;
			default: 
				$this->Data = sprintf("Error: Unknown provider '%s'", $this->TArrParam[self::cProvider]);
		}	

		return $this->Data;	
	}
	
	
	private function SendVia_SmsFly()
	{
		$PatternXML = <<<BAR
<?xml version="1.0" encoding="utf-8"?>
<request>
<operation>SENDSMS</operation>
	<message start_time="%s" end_time="%s" livetime="%s" rate="%s" desc="%s" source="%s">;
		<body>%s</body>
		%s
	</message>
</request>
BAR;
		$TArray1 = new TArray();
		$TArray1->Split($this->TArrParam[self::cPhonesNo], ",");
		$Recipients = $TArray1->GetContext(TArray::cRight, "<recipient>", "</recipient>");
		
		$RequestXML = sprintf($PatternXML,
				Date("Y-m-d H:i:s"), 
				Date("Y-m-d H:i:s", Time() + 3*60*60), 
				4, 
				120,
				iconv("windows-1251", "utf-8", $this->TArrParam[self::cDescription]),
				$this->TArrParam[self::cAlphaName],
				iconv("windows-1251", "utf-8", $this->TArrParam[self::cMessage]),
				$Recipients
				);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_USERPWD , $this->TArrParam[self::cLogin] . ':' . $this->TArrParam[self::cPassword]);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_URL, "http://sms-fly.com/api/api.php");
		curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: text/xml", "Accept: text/xml"));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $RequestXML);
		$Result = curl_exec($ch);
		curl_close($ch);

		return $Result;
	}	
}
?>
