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




class TRefWare_JDV extends TRefBase_SQL
/////////////////////////////////////////////////////
{
	protected function GetQueryItemSelect($aWhere = "")
	{
		// AND Price_B.DateQueue = Max(Price_B.DateQueue)
		$QueryStr	= <<< BAR
		SELECT 
			Ref_Ware.ID AS $this->FieldID,
			Ref_Ware.Image AS Image,
			Ref_Ware.Name AS Name, 
			round(Price_A.Value, 2) AS Price, 
			round(Price_B.Value, 2) AS PriceS,
			Ref_Ware.Description AS Details, 
			Ref_Vendor.Name AS Manufacture, 
			Sys_CategoryItem.ID AS Parent
		FROM 
			Ref_Ware
			INNER JOIN Sys_CategoryItem ON Sys_CategoryItem.Item_ID = Ref_Ware.ID
			LEFT JOIN Ref_Price Price_A ON (Price_A.Ware_ID = Ref_Ware.ID AND Price_A.PriceType_ID = 1)
			LEFT JOIN Ref_Price Price_B ON (Price_B.Ware_ID = Ref_Ware.ID AND Price_B.PriceType_ID = 2) 
			LEFT JOIN Ref_Vendor ON Ref_Vendor.ID = Ref_Ware.Vendor_ID
		WHERE 
			1
			$aWhere
		ORDER BY 
			$this->SortField $this->SortOrder;
BAR;
		return $QueryStr;
	}


	protected function GetQueryGroupSelect($aWhere = "")
	{
		$QueryStr = <<< BAR
		SELECT
			Sys_Category.ID AS $this->FieldID,
			Sys_Category.ParentID AS ParentID, 
			Sys_Category.Image AS Image,
			Sys_Category.Name AS Name
		FROM
			Sys_Category
		WHERE
			1
			$aWhere
		ORDER BY
			$this->SortField $this->SortOrder
BAR;

		return $QueryStr;
	}


	public function GetAlias()
	{
		return "Ware";
	}

  
	protected function GetName()
	{
		return "products";
	}

	
	public function IsGroup($aID)
	{
		return (bool) $this->TSQL->GetCount("Sys_Category", "ID = $aID");
	}


	public function GetSpecials()
	{
		$QueryStr = $this->GetQueryItemSelect("AND Price_B.PriceType_ID = 2 AND Price_B.Value < Price_A.Value");
		Print($QueryStr);
		return $this->GetIDsQuery($QueryStr);
	}

	
	public function GetGroupItems($aID)
	{
		$this->TSQL->Query("Select GetAllSubCatagoryID($aID, 1) as MyResult");
		$this->TSQL->FetchAssoc();
		$SubGroups = $aID . "," . $this->TSQL->TArrData->GetItem("MyResult"); 
  

		$QueryStr = $this->GetQueryItemSelect("AND Sys_CategoryItem.Category_ID IN ($SubGroups)");
		return $this->GetIDsQuery($QueryStr);
	}

	
	public function LogItem($aID)
	{
		$QueryStr = "INSERT INTO Log_Click (Table_ID, Item_ID, Person_ID) VALUES (1, $aID, 1)";
		$this->TSQL->Query($QueryStr);
		$this->TSQL->FreeResult(); 
	}


	public function ItemsForIceCat()
	{
		$QueryStr = $this->GetQueryItemSelect("AND IceCode = 0");
		return $this->GetIDsQuery($QueryStr);	
	}
}
?>