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

require_once(_DirCommonLib . "/Array.php");
require_once(_DirCommonLib . "/MySQL.php");
require_once(_DirCommonLib . "/Sys.php");
require_once(_DirCommonLib . "/String.php");
require_once(_DirCommonLibEx . "/Misc/IceCat.php");
require_once(_DirCommonLib3rd . "/simple_html_dom.php");


class TDB_Export_Base
{
	protected $TSQL, $DbPrefix;
	protected $TArrVendor, $TArrWare, $TArrDelPrefix, $TArrCompare;

	function __construct(TMySQL $aTSQL, $aDbPrefix = "")
	{
		$this->TSQL = $aTSQL;
		$this->DbPrefix = $aDbPrefix;
	}


	protected function ReadUrl($aUrl)
	{
		return file_get_contents(str_replace(" ", "%20", $aUrl), false, NULL);
	}		


	protected function SaveUrl($aUrl, $aFileName)
	{
		return file_put_contents($aFileName, $this->ReadUrl($aUrl));
	}		


	protected function GetStringBR($aString)
	{
		return str_replace(";", "<br/>", $aString);
	}		


	protected function GetEndUserPrice($aPrice)
	{
		return $aPrice;
		
		if ($aPrice < 1) 
			return $aPrice * 1.7;
		elseif ($aPrice < 10)
			return $aPrice * 1.5;
		elseif ($aPrice < 100)
			return $aPrice * 1.25;
		elseif ($aPrice < 500)
			return $aPrice * 1.15;
		elseif ($aPrice < 1000)
			return $aPrice * 1.10;
		elseif ($aPrice < 2000)
			return $aPrice * 1.08;
		elseif ($aPrice < 5000)
			return $aPrice * 1.05;
		else
			return $aPrice * 1.04;
	}

};


class TDB_Export_JDV extends TDB_Export_Base
{
	public function Clear()
	{
		$this->TSQL->Query("DELETE FROM Ref_Ware");
		$this->TSQL->Query("DELETE FROM Sys_CategoryItem");
		$this->TSQL->Query("DELETE FROM Sys_Category");
		$this->TSQL->Query("DELETE FROM Ref_Price");
		$this->TSQL->Query("DELETE FROM Ref_Vendor");
		
		$this->TSQL->Query("UPDATE Ref_Ware SET IsPublic = 0;");
	}


	public function Init()
	{
		$this->ArrInitWare();
		$this->ArrInitVendor();
		//$this->ArrDelPrefix();
	}


	public function IceCat($aTIceCat, $aMaxCnt = 100000)
	{
		$TSQL2 = clone $this->TSQL;

		$QueryStr	= <<< BAR
		SELECT 
			Ref_Ware.ID AS ID,
			Ref_Ware.ProdCode AS PID,
			Ref_Vendor.Name AS Vendor 
		FROM 
			Ref_Ware
			LEFT JOIN Ref_Vendor ON Ref_Vendor.ID = Ref_Ware.Vendor_ID
		WHERE
			Ref_Ware.IceCode = 0 AND 
			Ref_Ware.ProdCode > '' AND 
			Ref_Vendor.Name NOT IN ('', 'NoName');
BAR;
		$Cnt = $FoundCnt = 0;
		$aTSQL->Query($QueryStr);
		//printf("Count: %d <br>\n", $aTSQL->NumRows());
		while ($aTSQL->FetchAssoc() && $Cnt++ < $aMaxCnt) {
			$QueryStr = "";
			$WareID = $aTSQL->GetItem("ID");
			$ErrCode = $aTIceCat->LoadByPID($aTSQL->GetItem("PID"), $aTSQL->GetItem("Vendor"));
			if ($ErrCode > 0) {
				//print("IceCat found: $WareID <br>");
				$FoundCnt++;
				$ArrFileInfo = TFS::GetFileInfo($aTIceCat->TArrData["Image"]);
				$DstFile = "User/Image/Ware/" . BaseName($aTIceCat->TArrData["Image"]);	
				$aTIceCat->SaveUrl($aTIceCat->TArrData["Image"], $DstFile);

				$QueryStr = sprintf("UPDATE Ref_Ware 
											SET Name = '%s', 
												IceCode = %d, 
												Image = '%s', 
												Description = '%s', 
												BarCode = '%s', 
												DateRelease = '%s'
											WHERE ID = $WareID;", 
						$aTSQL->EscStr($aTIceCat->TArrData["Name"]),
						$aTIceCat->TArrData["ID"],
						$ArrFileInfo["BaseName"],
						$aTSQL->EscStr($this->GetStringBR($aTIceCat->TArrData["DescrTab"])),
						$aTIceCat->TArrData["EAN"],
						$aTIceCat->TArrData["DateRelease"]
						);
				$TSQL2->Query($QueryStr);
			}else
			if ($ErrCode == -1) {
				//print("IceCat not found: $WareID <br>");
				$QueryStr = "UPDATE Ref_Ware SET IceCode = -1 WHERE ID = $WareID;";	
				$TSQL2->Query($QueryStr);
			}

			$PerCent = ($FoundCnt + 1) / ($Cnt + 1) * 100; 
			printf("%d, %d (%d), ID: %d, %s, IceID: %d<br>\n", 
				$Cnt, $FoundCnt, $PerCent, $WareID, $aTIceCat->GetLastUrl(), $aTIceCat->TArrData["ID"]); 
		}
		$aTSQL->FreeResult();
	}


	private function ArrInitVendor()
	{
		$this->TArrVendor = new TArray();
		$this->TSQL->Query("SELECT ID, Name FROM Ref_Vendor");
		while ($this->TSQL->FetchAssoc()) {
			$this->TArrVendor->SetItem($this->TSQL->GetItem("Name"), $this->TSQL->GetItem("ID"));
		}
		$this->TSQL->FreeResult();
	}


	private function ArrInitWare()
	{
		$this->TArrWare = new TArray();
		$this->TSQL->Query("SELECT ID FROM Ref_Ware");
		while ($this->TSQL->FetchAssoc()) {
			$this->TArrWare->SetItem($this->TSQL->GetItem("ID"), "");
		}
		$this->TSQL->FreeResult();
	}


	private function ArrDelPrefix()
	{
		$this->TArrDelPrefix = Array();
		$this->TSQL->Query("SELECT Find FROM Sys_Replace WHERE Type = 1");
		while ($this->TSQL->FetchAssoc()) {
			$this->TArrDelPrefix[] = $this->TSQL->GetItem("Find");
		}
		$this->TSQL->FreeResult();
	}


	public function QuerySetCategory($aParentID, $aID, $aName)
	{
		$QueryStr = sprintf("INSERT INTO Sys_Category (ID, ParentID, Table_ID, Name) VALUES (%d, %d, 1, '%s');", 
						$aID, 
						$aParentID, 
						$this->TSQL->EscStr($aName));
		$this->TSQL->Query($QueryStr);
	}


	public function QuerySetItem($aParentID, $aTArrData)
	{
		$Item = $aTArrData;
		$Vendor = $Item["Vendor"];
		if ($this->TArrVendor->KeyExists($Vendor)) {
			$VendorID = $this->TArrVendor[$Vendor];	
		}else{
			//Print("Vendor not found: $Vendor <br>");
			$QueryStr = sprintf("INSERT INTO Ref_Vendor (Name) VALUES ('%s');",
							$this->TSQL->EscStr($Item["Vendor"]));
			$this->TSQL->Query($QueryStr);
			$this->TSQL->FreeResult(); 
			
			$VendorID = $this->TSQL->InsertID();
			$this->TArrVendor[$Vendor] = $VendorID;
		}

		$ImageFile = "";
		if ($Item["Image"] != "") {
			$DstFile = "User/Image/Ware/" . BaseName($Item["Image"]);	
			if (!TFS::FileExists($DstFile)) {
				try {
					$Data = $this->ReadUrl($Item["Image"]);
					if ($Data !== false) {
						printf("%s <br>\n", $Item["Image"]);
						$this->SaveUrl($Item["Image"], $DstFile);
						$ImageFile = BaseName($Item["Image"]);
					}
				} catch (Exception $e) {
					//	
				}
			}else{
				$ImageFile = BaseName($Item["Image"]);
			}
		}

		$WareID = (int) $Item["ID"];
		if ($this->TArrWare->KeyExists($WareID)) {
			//Print("Found: $WareID <br>");
			$QueryStr = sprintf("UPDATE Ref_Ware SET IsPublic = %d WHERE ID = %d;",
								$Item["Filter"],
								$WareID
								);	
		}else{
			//Print("Ware not found: $WareID <br>");
			$ReplCnt = 1;
			$Name = trim(str_replace($this->TArrDelPrefix, "", $Item["Name"], $ReplCnt));

			$QueryStr = sprintf("INSERT INTO Ref_Ware (ID, Name, Vendor_ID, ProdCode, Warranty, IsPublic, Image, Description, Name_Full) 
									VALUES (%d, '%s', %d, '%s', '%s', %d, '%s', '%s', 's');",
							$WareID,
							$this->TSQL->EscStr($Name),
							$VendorID,
							$Item["PID"],
							$Item["Warranty"],
							$Item["Filter"],
							$ImageFile,
							$this->TSQL->EscStr($this->GetStringBR($Item["Descr"])),
							$Item["Name"]
							);
		}
		$this->TSQL->Query($QueryStr);
		$this->TSQL->FreeResult();

		// Sys_CategoryItem
		$QueryStr = sprintf("INSERT INTO Sys_CategoryItem (Category_ID, Item_ID, Table_ID) VALUES (%d, %d, 1);",
						$aParentID,
						$WareID);
		$this->TSQL->Query($QueryStr);
		$this->TSQL->FreeResult(); 

		$PriceType = 1; // EndUser price
		$QueryStr = sprintf("INSERT INTO Ref_Price (DateQueue, Ware_ID, PriceType_ID, Value) VALUES(Now(), %d, %d, %s);",
						$WareID,
						$PriceType,
						$this->GetEndUserPrice($Item["Price1"] * 8.15)
						);
		$this->TSQL->Query($QueryStr);
		$this->TSQL->FreeResult(); 

		$PriceType = 3; // Incoming price
		$QueryStr = sprintf("INSERT INTO Ref_Price (DateQueue, Ware_ID, PriceType_ID, Value) VALUES(Now(), %d, %d, %s);",
						$WareID,
						$PriceType,
						$Item["Price1"]);
		$this->TSQL->Query($QueryStr);
		$this->TSQL->FreeResult(); 
	}
};



class TDB_Export_OpenCart extends TDB_Export_Base
//-----------------------------------------------------------------------------
{
	private $LangID, $Provider, $ImageDir;

	function __construct(TMySQL $aTSQL, $aDbPrefix)
	{
		parent::__construct($aTSQL, $aDbPrefix); 
		$this->ImageDir = "data";
	}


	private function ClearCategory()
	{
		$this->TSQL->Query("DELETE FROM {$this->DbPrefix}category");
		$this->TSQL->Query("DELETE FROM {$this->DbPrefix}category_description");
		$this->TSQL->Query("DELETE FROM {$this->DbPrefix}category_to_store");
	}


	private function ClearVendor()
	{
		$this->TSQL->Query("DELETE FROM {$this->DbPrefix}manufacturer");
		$this->TSQL->Query("DELETE FROM {$this->DbPrefix}manufacturer_to_store");
	}

	
	private function ClearProduct()
	{
		//$this->TSQL->Query("DELETE FROM {$this->DbPrefix}product");
		$this->TSQL->Query("DELETE FROM {$this->DbPrefix}product_description");
		$this->TSQL->Query("DELETE FROM {$this->DbPrefix}product_to_category");
		$this->TSQL->Query("DELETE FROM {$this->DbPrefix}product_to_store");
		$this->TSQL->Query("UPDATE {$this->DbPrefix}product SET status = 0;");
		//
		$this->TSQL->Query("DELETE FROM {$this->DbPrefix}product_discount");
		$this->TSQL->Query("DELETE FROM {$this->DbPrefix}product_option_value");
		$this->TSQL->Query("DELETE FROM {$this->DbPrefix}product_related");
		$this->TSQL->Query("DELETE FROM {$this->DbPrefix}product_reward");
		$this->TSQL->Query("DELETE FROM {$this->DbPrefix}product_special");
		$this->TSQL->Query("DELETE FROM {$this->DbPrefix}product_image");
	}
	

	public function Clear()
	{
		$this->ClearProduct();
		$this->ClearCategory();
		$this->ClearVendor();
	}


	public function Init($aLang, $aProvider)
	{
		$this->LangID   = $aLang;
		$this->Provider = $aProvider; 

		$this->ArrInitWare();
		$this->ArrInitVendor();
		//$this->ArrDelPrefix();
		//$this->ArrCompare();
	}


	private function ArrInitVendor()
	{
		$this->TArrVendor = new TArray();
		$this->TSQL->Query("SELECT manufacturer_id AS ID, Name FROM {$this->DbPrefix}manufacturer");
		while ($this->TSQL->FetchAssoc()) {
			$this->TArrVendor->SetItem($this->TSQL->GetItem("Name"), $this->TSQL->GetItem("ID"));
		}
		$this->TSQL->FreeResult();
	}


	private function ArrInitWare()
	{
		$this->TArrWare = new TArray();
		$this->TSQL->Query("SELECT product_id AS ID FROM {$this->DbPrefix}product");
		while ($this->TSQL->FetchAssoc()) {
			$this->TArrWare->SetItem($this->TSQL->GetItem("ID"), "");
		}
		$this->TSQL->FreeResult();
	}


	private function ArrDelPrefix()
	{
		$this->TArrDelPrefix = Array();
		$this->TSQL->Query("SELECT Find FROM Sys_Replace WHERE Type = 1");
		while ($this->TSQL->FetchAssoc()) {
			$this->TArrDelPrefix[] = $this->TSQL->GetItem("Find");
		}
		$this->TSQL->FreeResult();
	}


	private function ArrCompare()
	{
		$this->TArrCompare = new TArray();
		$this->TSQL->Query("SELECT product_id FROM Sys_Compare WHERE Url like '%rozetka.com.ua%'");
		while ($this->TSQL->FetchAssoc()) {
			$this->TArrCompare[] = $this->TSQL->GetItem("product_id");
		}
		$this->TSQL->FreeResult();
	}


	private function GetEndPrice($aPrice1, $aPrice2)
	{
		if ((int) $aPrice2 != 0 && $aPrice1 != $aPrice2) {
			return $aPrice2;
		}else{		
			return $this->GetEndUserPrice($aPrice1);
		}
	}


	public function SearchProduct($aName)
	{
		// http://webstat.ws/st.php?st=92&gr=1
		// http://www.rsdn.ru/forum/db/3612733.hot
		// http://linuxgazette.net/164/sephton.html
/*
		$QueryStr = sprintf("SELECT	*, MATCH(Name) AGAINST ('%s') AS Score
							FROM Sys_Compare
							WHERE Avail = 1 AND	MATCH (Name) AGAINST ('%s')
							HAVING Score > 8
							ORDER BY Score DESC
							LIMIT 1", $aStr);
*/
		$QueryStr = sprintf("SELECT	*, levenshtein_ratio(Name, '%s') AS Score
							FROM Sys_Compare
							WHERE MATCH (Name) AGAINST ('%s')
							HAVING Score > 0.5
							ORDER BY Score DESC
							LIMIT 10", $aName);
		$this->TSQL->Query($QueryStr);
		$this->TSQL->FreeResult(); 

		if ($this->TSQL->GetCount() > 0 && $this->TSQL->TArrData["Avail"] == 1) {
			return $this->TSQL->TArrData;	
		}else{
			return new TArray();
		}
	}


	public function QueryGetProduct()
	{
		$QueryStr = "SELECT 
						product.product_id AS ID,
						product.price0 AS Price,
						product_description.name AS Name, 
						manufacturer.name AS Manufacture,
						category_description.name AS Category
					FROM 
						{$this->DbPrefix}product AS product
						INNER JOIN {$this->DbPrefix}product_description AS product_description 
						  ON product_description.product_id = product.product_id And product_description.language_id = 1  
						INNER JOIN {$this->DbPrefix}product_to_category AS product_to_category
						  ON product_to_category.product_id = product_description.product_id
						INNER JOIN {$this->DbPrefix}category_description AS category_description 
						  ON category_description.category_id = product_to_category.category_id
						LEFT  JOIN {$this->DbPrefix}manufacturer AS manufacturer
						  ON product.manufacturer_id = manufacturer.manufacturer_id
					WHERE 
						product.status = 1  
					LIMIT 
						100 OFFSET 10";
		$this->TSQL->Query($QueryStr);
		$this->TSQL->FreeResult();
		return $this->TSQL->TArrData;	
	}

	
	public function QuerySetVendor($aName)
	{
		if ($this->TArrVendor->KeyExists($aName)) {
			$VendorID = $this->TArrVendor[$aName];	
		}else{
			$QueryStr = sprintf("INSERT INTO {$this->DbPrefix}manufacturer (Name) VALUES ('%s');",
							$this->TSQL->EscStr($aName));
			$this->TSQL->Query($QueryStr);
			$this->TSQL->FreeResult(); 
			
			$VendorID = $this->TSQL->InsertID();
			$this->TArrVendor[$aName] = $VendorID;
		}	
		
		return $VendorID;
	}	

	
	public function QuerySetCategory($aParentID, $aID, $aName)
	{
		$QueryStr = sprintf("INSERT INTO {$this->DbPrefix}category (category_id, parent_id, status, date_added) VALUES (%d, %d, 1, Now());", 
						$aID, 
						$aParentID
						);
		$this->TSQL->Query($QueryStr);

		$QueryStr = sprintf("INSERT INTO {$this->DbPrefix}category_description (category_id, name, language_id) VALUES (%d, '%s', %d);", 
						$aID, 
						$this->TSQL->EscStr($aName),
						$this->LangID
						);
		$this->TSQL->Query($QueryStr);

		$QueryStr = sprintf("INSERT INTO {$this->DbPrefix}category_to_store (category_id, store_id) VALUES (%d, %d);", 
						$aID, 
						0
						);
		$this->TSQL->Query($QueryStr);
	}


	public function QuerySetItem($aParentID, $aTArrData)
	{
		$Item = $aTArrData;
		$VendorID = $this->QuerySetVendor($Item["Vendor"]);
		
		$ImagePath = $this->ImageDir . "/store_" . $this->Provider . "/";
		$ImageFile = "";
		if ($Item["Image"] != "") {
			$DstFile = "../image/" . $ImagePath . BaseName($Item["Image"]);	
			if (!TFS::FileExists($DstFile)) {
				try {
					$Data = $this->ReadUrl($Item["Image"]);
					if ($Data !== false) {
						printf("%s <br>\n", $Item["Image"]);
						$this->SaveUrl($Item["Image"], $DstFile);
						$ImageFile = $ImagePath . BaseName($Item["Image"]);
					}
				} catch (Exception $e) {
					//	
				}
			}else{
				$ImageFile = $ImagePath . BaseName($Item["Image"]);
			}
		}


		$WareID   = (int) $Item["ID"];
		$ReplCnt = 1;
		$Name = trim(str_replace($this->TArrDelPrefix, "", $Item["Name"], $ReplCnt));
		if ($this->TArrWare->KeyExists($WareID)) {
			//Printf("Found ID: %s, price: %s, status: %s<br>\n", $WareID, $Item["Price1"], $Item["Filter"]);
			$QueryStr = sprintf("UPDATE {$this->DbPrefix}product SET status = %d, price = %f, price0 = %f WHERE product_id = %d;",
								$Item["Filter"],
								$this->GetEndPrice($Item["Price1"], $Item["Price2"]),
								$Item["Price1"],
								$WareID
								);	
		}else{
			//Printf("Found ID: %s, price: %s, status: %s<br>\n", $WareID, $Item["Price1"], $Item["Filter"]);
			$QueryStr = sprintf("INSERT INTO {$this->DbPrefix}product (product_id, manufacturer_id, model, mpn, ean, status, image, price, price0, quantity, date_added) 
									VALUES (%d, %d, '%s', '%s', '%s', %d, '%s', %f, %f, %d, Now());",
							$WareID,
							$VendorID,
							$this->TSQL->EscStr($Item["Model"]),
							$this->TSQL->EscStr($Item["PID"]),
							$Item["EAN"],
							$Item["Filter"],
							$ImageFile,
							$this->GetEndPrice($Item["Price1"], $Item["Price2"]),
							$Item["Price1"],
							$Item["Quantity"]
							);
		}
		$this->TSQL->Query($QueryStr);
		$this->TSQL->FreeResult();

		$QueryStr = sprintf("INSERT INTO {$this->DbPrefix}product_to_store (product_id, store_id) VALUES (%d, %d);", 
				$WareID, 
				0
				);
		$this->TSQL->Query($QueryStr);
		$this->TSQL->FreeResult();

		$QueryStr = sprintf("INSERT INTO {$this->DbPrefix}product_description (product_id, name, description, language_id) 
									VALUES (%d, '%s', '%s', %d);",
							$WareID,
							$this->TSQL->EscStr($Name),
							$this->TSQL->EscStr($this->GetStringBR($Item["Descr"])),
							$this->LangID
							);
		$this->TSQL->Query($QueryStr);
		$this->TSQL->FreeResult();


		$QueryStr = sprintf("INSERT INTO {$this->DbPrefix}product_to_category (product_id, category_id) VALUES (%d, %d);",
						$WareID,
						$aParentID);
		$this->TSQL->Query($QueryStr);
		$this->TSQL->FreeResult(); 
	}


	public function AttachImagasFromFolder($aName)
	{
	   $Cnt = 0;
       $Dir = new TDir($aName);
       $ArrFiles = $Dir->GetFiles(false, TDir::cFile, "gif|jpg|jpeg|png");
       $ArrFiles->Reset();
       while (list(, $Value) = $ArrFiles->Each()) {
         $PathInfo = pathinfo($Value);
		 $ImageID = $PathInfo["filename"];  
		 if (is_numeric($ImageID)) {
		   $Cnt++;
           $QueryStr = sprintf("UPDATE {$this->DbPrefix}product 
                                SET image = '%s' 
                                WHERE product_id = '%d' AND image = ''",
								  $this->ImageDir . "/" . $PathInfo["basename"],
								  $ImageID
								);	
		   $this->TSQL->Query($QueryStr);
		 }  
       }
       printf("Оброблено $Cnt зоображень формату 999999.jpg<br>", $Cnt);
    }


	public function GetRozetkaData($aUrl)
	{
		$Result = new TArray();

		// http://xdan.ru/Uchimsya-parsit-saity-s-bibliotekoi-PHP-Simple-HTML-DOM-Parser.html
		$html = file_get_html($aUrl);

		$DivCode = $html->find('div.pp-code', 0);
		$Result["Code"] = TStr::SubPosR($DivCode->plaintext, "&nbsp;", TStr::Length("&nbsp;"));

		$DivUsd = $html->find('div.pp-usd', 0);
		$Result["Price_USD"] = (float) TStr::Replace(TStr::SubPosR($DivUsd->plaintext, "&nbsp;", TStr::Length("&nbsp;")), " ", "");

		$html->clear();
		unset($html);

		printf("Url: %s, Code: %s, Price: %s<br>\n", $aUrl, $Result["Code"], $Result["Price_USD"]);
		return $Result;
	}	


	public function QuerySetItemCompare($aTArrData)
	{
		$Item = $aTArrData;

		$ArrRozetka = $this->GetRozetkaData($Item["Url"]);
		if ($ArrRozetka["Price_USD"] > 0) {
			$DateSync = Date("Y-m-d H:i:s");
		}else{
			$DateSync = "0000-00-00";
		}

 		if ($this->TArrCompare->KeyExists((int) $Item["ProductID"])) {
			$QueryStr = sprintf("UPDATE Sys_Compare Code = %d, Url = %d, Price = %f, Image = '%s', Descr = '%s', DateSync = '%s'  WHERE product_id = %d, BotId = 0;",
								$Item["Code"],
								$Item["Url"],
								$ArrRozetka["Price_USD"],
								$ArrRozetka["Image"],
								$ArrRozetka["Description"],
								$DateSync,
								$Item["ProductID"]
								);	
		}else{
			$QueryStr = sprintf("INSERT INTO Sys_Compare (product_id, Code, Url, Price, Image, Descr, DateSync, BotId) VALUES (%d, %d, '%s', %f, '%s', '%s', '%s', 0);", 
								$Item["ProductID"],
								$Item["Code"],
								$Item["Url"],
								$ArrRozetka["Price_USD"],
								$ArrRozetka["Image"],
								$ArrRozetka["Description"],
								$DateSync,
								$Item["ProductID"]
						);
		}
		print("<br>\n$QueryStr<br>\n");
		$this->TSQL->Query($QueryStr);
		$this->TSQL->FreeResult();
	}
};


?>
