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

// OS Commerce handler

require_once(_DirCommonLib . "/MySQL.php");
require_once(_DirCommonLibEx . "/DB/RefBase_SQL.php");



class TRefCustomer_OSC extends TRefBase_SQL
/////////////////////////////////////////////////////
{
	function __construct(TSQL $aTSQL, TLang $aTLang)
	{
		parent::__construct($aTSQL, $aTLang);
		//$aTSQL->Debug = true;
	}

	
	protected function GetQueryItemSelect($aWhere = "")
	{
		$QueryStr	= <<< BAR
		SELECT
			customers.customers_id AS $this->FieldID,
			"" AS Image,
			customers.customers_firstname AS FirstName,
			customers.customers_lastname AS LastName,
			CONCAT(customers.customers_firstname, " ", customers.customers_lastname) AS Name,
			customers.customers_email_address AS EMail, 
			customers.customers_dob AS DOB,
			customers.customers_telephone AS Phone,
			customers.customers_password AS Password
		FROM 
			customers
		WHERE
			1
			$aWhere
		ORDER BY 
			$this->SortField $this->SortOrder
BAR;
		
		return $QueryStr;
	}

	
	protected function GetQueryGroupSelect($aWhere = "")
	{
		return "";	
	}

	
	public function GetAlias()
	{
		return "Customer";
	}


	protected function GetName()
	{
		return "customers";
	}

	
	public function IsGroup($aID)
	{
		return false;
	}
	

	public function Validate($aLogin, $aPassword = "")
	{
		$Condition = sprintf("AND LOWER(customers.customers_email_address)='%s'", TStr::ToLower($aLogin));
		if ($aPassword != "") {
			$Condition .=  " AND customers.customers_password='$aPassword'";
		}
		$QueryStr = $this->GetQueryItemSelect($Condition);
		
		return $this->SetCurItemQuery($QueryStr);
	}


	public function AddItem()
	{
		$Login = $this->Record->GetField("EMail");
		$Result = $this->Validate($Login);
		if (!$Result) {
			parent::AddItem();
		}
		
		return $Result;
	}
}
?>