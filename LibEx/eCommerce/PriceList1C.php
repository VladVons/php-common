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

require_once("PriceListBase.php");


//-----------------------------------------------------------------------------
class TPriceList1C extends TPriceListBase
{
        private function GetProductImage($aCode)
	{
		return "";
	}


        protected function ItemFilter()
	{
	    //return ($this->TArrData["Quantity"] > 0 /* $this->TArrData["Quantity"] > 0 */);
	    return true;
	}


	protected function ItemParse($aData)
	{
		$this->TArrData->Clear();

		$this->TArrData["CategoryID"] = (string) $aData->CategoryID;
		$this->TArrData["Model"]      = (string) $aData->Articul;
                $this->TArrData["ID"]         = (int)    $aData->Code;
		$this->TArrData["Name"]       = (string) $aData->Name;
		$this->TArrData["Price1"]     = (float)  $aData->PriceIn;
		$this->TArrData["Price2"]     = (float)  $aData->PriceOut;
		$this->TArrData["Quantity"]   = (float)  $aData->Quantity;
		$this->TArrData["Deleted"]    = (int)    $aData->Deleted;
		$this->TArrData["Filter"]     = $this->ItemFilter();

		return $this->TArrData;
	}


	private function CategoryParse($aTDbExp, $aItem)
	{
	    DbgLevel(2, _DebugLevel, basename(__FILE__) . "->" . __FUNCTION__ . "(" . __LINE__ .")");

	    $Result = 0;

    	    foreach($aItem->Children() as $Child) {
		//printf("Dbg: ID: %d, Name: %s\n", $Child["ID"], $Child);
		$aTDbExp->AddCategory((int) $Child["ParentID"], (int) $Child["ID"], (string) $Child);

		$Result++;
		if ($Result % 1000 == 0)
		    DbgLevel(4, _DebugLevel, basename(__FILE__) . "->" . __FUNCTION__ . "(" . __LINE__ .")", sprintf("%d, Categiry ID: %d", $Result, $Child["ID"]));
	    }

	    return  $Result;
        }


	private function ProductParse($aTDbExp, $aItem)
	{
	    DbgLevel(2, _DebugLevel, basename(__FILE__) . "->" . __FUNCTION__ . "(" . __LINE__ .")");

	    $Result = 0;

	    $Time = microtime(true);
	    foreach($aItem->Children() as $Child) {
		$Result++;
		if ($Result % 1000 == 0) {
		    DbgLevel(1, _DebugLevel, basename(__FILE__) . "->" . __FUNCTION__ . "(" . __LINE__ .")", sprintf("%d, Time: %d", $Result, microtime(true) - $Time));
		    $Time = microtime(true);
		}
	    
	    	$ArrItem = $this->ItemParse($Child);
		if ($this->ItemFilter()) {
	    	    $aTDbExp->AddProduct($ArrItem);
		}
	    }

	    return $Result;
        }


	public function DbExp($aTDbExp)
	{
	    DbgLevel(2, _DebugLevel, basename(__FILE__) . "->" . __FUNCTION__ . "(" . __LINE__ .")");

	    $Time = microtime(true);
	    $aTDbExp->InitRef();

	    $CntCategories = $this->CategoryParse($aTDbExp, $this->Xml->Catalog);
	    $aTDbExp->CreateCategoryPath();

	    $CntProducts = $this->ProductParse($aTDbExp, $this->Xml->Items);
	    $aTDbExp->AttachImagasFromFolder();

	    $Str = sprintf("Categories: %d, Products: %d; Info: (%s), Time: %d", $CntCategories, $CntProducts, $aTDbExp->GetInfo(),  microtime(true) - $Time);
	    LogStr("Import.log", $Str);
	}
};

?>
