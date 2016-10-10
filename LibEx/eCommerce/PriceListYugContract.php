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
class TPriceListYugContract extends TPriceListBase
{
	private $OnStockOK;
	
	
	function __construct()
	{
		parent::__construct(); 
		$this->OnStockOK = iconv("cp1251", "UTF-8", "есть");
	}


	protected function ItemFilter()
	{
		return ($this->TArrData["Status"] == 1); 
	}


	
	protected function ItemParse($aData)
	{
		$this->TArrData->Clear();

		$this->TArrData["ID"]          		= (int)    $aData["id"];
		$this->TArrData["CategoryName"]		= (string) $aData["cat"];
		$this->TArrData["PCategoryName"]	= (string) $aData["cat_top"];
		$this->TArrData["Vendor"]      		= (string) $aData["brand"];
		$this->TArrData["Name"]        		= (string) $aData["name_rus"];
		$this->TArrData["Status"]       	= (int)    $aData["status"];
		$this->TArrData["Quantity"]    		= (string) $aData["qty"];
		$this->TArrData["Model"]       		= (string) $aData["artikul"];
		$this->TArrData["Price1"]      		= (float)  $aData["price_uah"];
		$this->TArrData["Price2"]      		= (float)  $aData["price_uah"];
		$this->TArrData["Warranty"]    		= (string) $aData["guarant"];
		$this->TArrData["Image"]       		= (string) $aData["photo"];
		$this->TArrData["Descr"]       		= (string) $aData->descr;
		$this->TArrData["EAN"]         		= (string) $aData["barcode"];
		$this->TArrData["Filter"]      		= $this->ItemFilter();
		
		return $this->TArrData;
	}


	private function ProductParse($aTDbExp, $aItem)
        {
	    $Result = 0;
	    $TArrCategory = $aTDbExp->GetCategoriesAll();

	    foreach($aItem as $Child) {
		$Item = $this->ItemParse($Child);
		//printf("Dbg. PCategoryName: %s, CategoryName: %s, Product:%s\n", $Item["PCategoryName"], $Item["CategoryName"], $Item["Name"]);

		if ($this->ItemFilter()) {
		    $CategoryName  = $Item["CategoryName"];
		    $PCategoryName = $Item["PCategoryName"];


		    if ($TArrCategory->KeyExists($PCategoryName)) {
			$PCategoryID = $TArrCategory->GetItem($PCategoryName);
		    }else{
			$PCategoryID = 0;
			$CategoryID  = ++$CategoryCnt;
			$TArrCategory->SetItem($PCategoryName, $CategoryID);
			$aTDbExp->AddCategory($PCategoryID, $CategoryID, $PCategoryName);
		    }

		    if ($TArrCategory->KeyExists($CategoryName)) {
			$CategoryID = $TArrCategory->GetItem($CategoryName);
		    }else{
		    	$CategoryID = ++$CategoryCnt;
			$TArrCategory->SetItem($CategoryName, $CategoryID);
			$aTDbExp->AddCategory($PCategoryID, $CategoryID, $CategoryName);
			//printf("Dbg: PCategoryID: %d, PCategoryName%s\n", $PCategoryID, $CategoryName);
		    }
    
		    $Item["CategoryID"] = $CategoryID;
		    $aTDbExp->AddProduct($Item);

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

	    $CntProducts = $this->ProductParse($aTDbExp, $this->Xml);
	    $aTDbExp->CreateCategoryPath();

	    $Str = sprintf("Products: %d; Info: (%s)", $CntProducts, $aTDbExp->GetInfo());    
	    LogStr("Import.log", $Str);
	}
};

?>
