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
abstract class TPriceListBrainBase extends TPriceListBase
{
	protected $FBrain, $ArrVendors, $ArrCateg, $ArrCategCharge;


	protected function GetProductImage($aCode)
	{
		$URL = "http://brain.com.ua/static/images/prod_img/" . substr($aCode, -2, 1) . "/" . substr($aCode, -1, 1) . "/". $aCode . ".jpg";
		return $URL;
	}


	protected function ItemFilter()
	{
		return $this->TArrData["Stok1"] > 0;
	}
}

//-----------------------------------------------------------------------------
class TPriceListBrain extends TPriceListBrainBase
{
	private $CntCategories;

	protected function ItemParse($aData)
	{
		$this->TArrData->Clear();

                $this->TArrData["CategoryID"]= (string) $aData["CategoryID"];
		$this->TArrData["Vendor"]    = (string) $aData["Vendor"];
		$this->TArrData["Model"]     = (string) $aData["Model"];
		$this->TArrData["EAN"]       = TStr::Trim( (string) $aData["Code"]);
                $this->TArrData["ID"]        = (int)    $aData["ProductID"];
		$this->TArrData["Name"]      = (string) $aData["Name"];
		$this->TArrData["Price1"]    = (float)  $aData["PriceUSD"];
		$this->TArrData["Price2"]    = (float)  $this->TCategPrice->GetPrice($this->TArrData["Category"], $this->TArrData["Price1"]);
		$this->TArrData["Stok1"]     = (int)    $aData["Stock"];
		$this->TArrData["Warranty"]  = (string) $aData["Warranty"];
		$this->TArrData["Descr"]     = (string) $aData["Description"];
		$this->TArrData["Filter"]    = $this->ItemFilter();
		$this->TArrData["Image"]     = $this->GetProductImage($aData["Code"]);
		$this->TArrData["Days"]      = (int)    $aData["DayDelivery"];
                $this->TArrData["Quantity"]  = $this->ItemFilter() ? 1 : 0;

		return $this->TArrData;
	}


	private function CategoryParse($aTDbExp, $aItem, $aPID)
	{
	  foreach($aItem->Children() as $Child) {
	    $ID   = (string) $Child["id"];
	    $Name = (string) $Child["name"];

	    $this->TCategPrice->AddItem($ID);

	    //printf("Dbg1: %s, %s, %s, %s<br>\n", $Child->getName(), $aPID, $ID, $Name);
            $aTDbExp->AddCategory($aPID, $ID, $Name);
            if ($aItem->Children()) {
		$this->CntCategories++;
		$this->CategoryParse($aTDbExp, $Child, $ID);
            }
          }
        }


	private function ProductParse($aTDbExp, $aItem)
	{
	    $Result = 0;
     
	    foreach($aItem->Children() as $Child) {
		$ArrItem = $this->ItemParse($Child);
		if ($this->ItemFilter()) {
		    //printf("ID: %s, PID: %d, EAN: %s, Name: %s, Stock: %d, Price1: %f, Price2: %f<br>\n",  $ArrItem["ID"], $ArrItem["Category"], $ArrItem["EAN"], $ArrItem["Name"], $ArrItem["Stok1"],  $ArrItem["Price1"],  $ArrItem["Price2"]);
		    $aTDbExp->AddProduct($ArrItem);

		    $Result++;
		    if ($Result % 1000 == 0) 
                	printf("dbg: %d<br>\n", $Result);
		}
	    }
	
	  return $Result;    
        }


	public function DbExp($aTDbExp)
	{
	    $aTDbExp->InitRef();

	    $this->CntCategories = 0;    
	    $this->CategoryParse($aTDbExp, $this->Xml->categories, 0);
	    $aTDbExp->CreateCategoryPath();

	    $CntProducts = $this->ProductParse($aTDbExp, $this->Xml->products);
	    
	    $Str = sprintf("Categories: %d, Products: %d; Info: (%s)", $this->CntCategories, $CntProducts, $aTDbExp->GetInfo());   
	    LogStr("Import.log", $Str);
        }
};

?>
