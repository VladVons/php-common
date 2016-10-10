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



class TRefManufacture_OSC extends TRefBase_SQL
/////////////////////////////////////////////////////
{
	protected function GetQueryItemSelect($aWhere = "")
	{
		$QueryStr	= <<< BAR
		SELECT
			manufacturers.manufacturers_id AS $this->FieldID,
			manufacturers.manufacturers_image AS Image,
			manufacturers.manufacturers_name AS Name,
			manufacturers_info.manufacturers_url AS URL
		FROM 
			manufacturers,
			manufacturers_info
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
		return "Manufacture";
	}


	protected function GetName()
	{
		return "manufacturers";
	}

	
	public function IsGroup($aID)
	{
		return false;
	}
}
?>