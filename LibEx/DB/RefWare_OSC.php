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




class TRefWare_OSC extends TRefBase_SQL
/////////////////////////////////////////////////////
{
	protected function GetQueryItemSelect($aWhere = "")
	{
		$QueryStr	= <<< BAR
		SELECT 
			products.products_id AS $this->FieldID,
			products.products_image AS Image,
			round(products.products_price, 2) AS Price, 
			products_description.products_name AS Name, 
			products_description.products_description AS Details, 
			if (specials.status, specials.specials_new_products_price, NULL) AS PriceS,
			manufacturers.manufacturers_name AS Manufacture, 
			products_to_categories.categories_id AS ParentID
		FROM 
			products
			INNER JOIN products_description ON products.products_id = products_description.products_id And products_description.language_id = 1  
			inner join products_to_categories ON products_description.products_id = products_to_categories.products_id
			LEFT JOIN specials ON products.products_id = specials.products_id 
			LEFT JOIN manufacturers ON products.manufacturers_id = manufacturers.manufacturers_id
		WHERE 
			products.products_status = 1  
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
			categories.categories_id AS $this->FieldID,
			categories.parent_id AS ParentID, 
			categories.categories_image AS Image,
			categories_description.categories_name AS Name
		FROM
			categories,
			categories_description
		WHERE
			categories.categories_id = categories_description.categories_id AND
			categories_description.language_id  = 1
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
		return (bool) $this->TSQL->GetCount("categories", "categories_id = $aID");
	}


	public function GetSpecials()
	{
		$QueryStr = $this->GetQueryItemSelect("AND specials.status=1");
		return $this->GetIDsQuery($QueryStr);
	}

	
	public function GetGroupItems($aID)
	{
		$this->TSQL->Query("Select sel_all_sub_categories_id($aID) as MyResult");
		$this->TSQL->FetchAssoc();
		$SubGroups = $aID . "," . $this->TSQL->TArrData->GetItem("MyResult"); 
  

		$QueryStr = $this->GetQueryItemSelect("AND products_to_categories.categories_id IN ($SubGroups)");
		return $this->GetIDsQuery($QueryStr);
	}


	public function LogItem($aID)
	{
		$QueryStr = <<< BAR
		UPDATE 
			products_description 
        SET    
			products_viewed = products_viewed + 1 
		WHERE  
			products_id = '$aID' AND language_id = '1'
BAR;

		$this->TSQL->Query($QueryStr);
		$this->TSQL->FreeResult(); 
	}

}
?>