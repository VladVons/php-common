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

require_once("DbExpBase.php");
require_once (_DirCommonLib . "/CacheSQL.php");

define('_DirImageRoot',  '../image');



class TRefVendor extends TRefBase
//-----------------------------------------------------------------------------
{
	public function Clear()
	{
	    $this->TSQL->Query("DELETE FROM {$this->DbPrefix}manufacturer");
	    $this->TSQL->Query("DELETE FROM {$this->DbPrefix}manufacturer_to_store");
	}

	
        public function Init()
        {
	    $this->ArrInit("SELECT manufacturer_id AS ID, Name FROM {$this->DbPrefix}manufacturer", "Name", "ID");
	}


	public function AddArr($aItem)
	{
	    return $this->Add($aItem["Vendor"]);
	}


	private function Add($aName)
	{
	    //printf("dbg: %s<br>\n", __FUNCTION__);

	    if ($this->TArrData->KeyExists($aName)) {
		$ResultID = $this->TArrData[$aName];
	    }else{
		$QueryStr = sprintf("INSERT INTO {$this->DbPrefix}manufacturer (Name) VALUES ('%s')", $this->TSQL->EscStr($aName));
		$this->TSQL->Query($QueryStr);
		$this->TSQL->FreeResult();

		$ResultID = $this->TSQL->InsertID();
		$this->TArrData[$aName] = $ResultID;

        	$QueryStr = sprintf("INSERT INTO {$this->DbPrefix}manufacturer_to_store (manufacturer_id, store_id) VALUES (%d, %d)", $ResultID, 0);
    		$this->TSQL->Query($QueryStr);
        	$this->TSQL->FreeResult();
	    }

	    return $ResultID;
	}
};



class TRefProduct extends TRefBase
//-----------------------------------------------------------------------------
{
	private $CI_product_to_category, $CI_product_to_store, $CI_url_alias, $CID_product;

	function __construct($aParent)
	{
	    parent::__construct($aParent);

	    $this->CI_product_to_category = new TCacheSQLInsert($this->TSQL, "{$this->DbPrefix}product_to_category", "product_id, category_id");
	    $this->CI_product_to_store    = new TCacheSQLInsert($this->TSQL, "{$this->DbPrefix}product_to_store",    "product_id, store_id");
	    $this->CI_url_alias           = new TCacheSQLInsert($this->TSQL, "{$this->DbPrefix}url_alias",           "query, keyword");

	    $this->CID_product            = new TCacheSQLInsertDuplicate($this->TSQL, "{$this->DbPrefix}product",
						"product_id, manufacturer_id, model, status, image, price, price0, quantity, date_added", 
						"manufacturer_id=VALUES(manufacturer_id), model=VALUES(model), status=VALUES(status), price=VALUES(price), price0=VALUES(price0), quantity=VALUES(quantity)");
	}

        public function ClearBase()
	{
            $this->TSQL->Query("DELETE FROM {$this->DbPrefix}product");
            $this->TSQL->Query("DELETE FROM {$this->DbPrefix}product_image");
	}


        public function ClearRelated()
	{
	    $this->TSQL->Query("DELETE FROM {$this->DbPrefix}product_to_category");
	    $this->TSQL->Query("DELETE FROM {$this->DbPrefix}product_to_store");
	    $this->TSQL->Query("DELETE FROM {$this->DbPrefix}url_alias WHERE query LIKE 'product_id=%'");

	    //$this->TSQL->Query("DELETE FROM {$this->DbPrefix}product_discount");
	    //$this->TSQL->Query("DELETE FROM {$this->DbPrefix}product_option_value");
	    //$this->TSQL->Query("DELETE FROM {$this->DbPrefix}product_related");
	    //$this->TSQL->Query("DELETE FROM {$this->DbPrefix}product_reward");
	    //$this->TSQL->Query("DELETE FROM {$this->DbPrefix}product_special");
	}


	public function Clear()
	{
	    //$this->TSQL->ClearProductBase();
	    $this->TSQL->ClearRelated();
	}

	
        public function Init()
        {
	    $this->ClearRelated();

	    $this->TSQL->Query("UPDATE {$this->DbPrefix}product SET status = 0");

	    $QueryStr = "SELECT product_id AS ID FROM {$this->DbPrefix}product"; 
	    $this->ArrInit($QueryStr, "ID", "");
	}


	public function AddArr($aItem)
	{
    	    $this->Add($aItem["ID"], $aItem["CategoryID"], $aItem["Name"], $aItem["Model"], $aItem["Filter"], $aItem["Price1"], $aItem["Price2"], 
			$aItem["Quantity"], $aItem["VendorID"], $aItem["ImageFS"]);
	}

	public function Add($aID, $aPID, $aName, $aModel, $aStatus, $aPriceIn, $aPriceOut, $aQuantity, $aVendorID, $aImage)
	{
	    $Fields = sprintf("%d, %d, '%s', %d, '%s', %f, %f, %d, NOW()",
						$aID,
						$aVendorID,
						$this->TSQL->EscStr($aModel),
						$aStatus,
						$aImage,
						$aPriceOut,
						$aPriceIn,
						$aQuantity);

	    $this->CID_product->Add($Fields);

	    $this->AddRelated($aID, $aPID);
	}

	public function Add_Slow($aID, $aPID, $aName, $aModel, $aStatus, $aPriceIn, $aPriceOut, $aQuantity, $aVendorID, $aImage)
	{	
	    $Dbg = sprintf("ID: $aID, PID: $aPID, Name: $aName, Mode: $aModel, Status: $aStatus, PriceIn: $aPriceIn, PriceOut: $aPriceOut, Quantity: $aQuantity, VendorID: $aVendorID, Image:$aImage");

	    if ($this->TArrData->KeyExists($aID)) {
		DbgLevel(3, _DebugLevel, basename(__FILE__) . "->" . __FUNCTION__ . "(" . __LINE__ .")", "UPDATE " . $Dbg);
		$QueryStr = sprintf("UPDATE {$this->DbPrefix}product SET manufacturer_id = %d, model = '%s', status = %d, price = %f, price0 = %f, quantity = %d WHERE product_id = %d",
						$aVendorID,
                                                $this->TSQL->EscStr($aModel),
						$aStatus,
						$aPriceOut,
						$aPriceIn,
					        $aQuantity,
						$aID);
		$this->TSQL->Query($QueryStr);
		//$this->Debug($aID, $QueryStr);
	    }else{
		DbgLevel(3, _DebugLevel, basename(__FILE__) . "->" . __FUNCTION__ . "(" . __LINE__ .")", "INSERT " . $Dbg);

		$this->TArrData[$aID] = "";

		$QueryStr = sprintf("INSERT INTO {$this->DbPrefix}product (product_id, manufacturer_id, model, status, image, price, price0, quantity, date_added)
								VALUES (%d, %d, '%s', %d, '%s', %f, %f, %d, Now())",
						$aID,
						$aVendorID,
						$this->TSQL->EscStr($aModel),
						$aStatus,
						$aImage,
						$aPriceOut,
						$aPriceIn,
						$aQuantity);
		$this->TSQL->Query($QueryStr);
		//$this->Debug($aID, "INSERT");

	    }

	    $this->AddRelated($aID, $aPID);
	}


	private function AddRelated($aID, $aPID)
	{
	    //$QueryStr = sprintf("INSERT INTO {$this->DbPrefix}product_to_category (product_id, category_id) VALUES (%d, %d)", $aID, $aPID);
	    //$this->TSQL->Query($QueryStr);
	    $this->CI_product_to_category->Add(sprintf("%d, %d", $aID, $aPID));

	    //$QueryStr = sprintf("INSERT INTO {$this->DbPrefix}product_to_store (product_id, store_id) VALUES (%d, %d)", $aID, 0);
	    //$this->TSQL->Query($QueryStr);
	    $this->CI_product_to_store->Add(sprintf("%d, %d", $aID, 0));

	    //$QueryStr = sprintf("INSERT INTO {$this->DbPrefix}url_alias (query, keyword) VALUES ('product_id=%d', 'p%s.html')", $aID, $aID);
	    //$this->TSQL->Query($QueryStr);
	    $this->CI_url_alias->Add(sprintf("'product_id=%d', 'p%s.html'", $aID, $aID));
	}


	public function GetItems($aPID) 
	{
	    $QueryStr = "
		SELECT     t1.product_id AS ID, t2.name AS Name
		FROM       {$this->DbPrefix}product t1
		LEFT JOIN  {$this->DbPrefix}product_description t2 ON (t1.product_id = t2.product_id)
		LEFT JOIN  {$this->DbPrefix}product_to_category t3 ON (t1.product_id = t3.product_id)
		WHERE      t3.category_id = $aPID AND t2.language_id = $this->LangID
		ORDER BY   t2.name";

		return $this->ArrGet($QueryStr, "ID", "Name");
	}

       
	public function GetNoImage()
        {
	    $QueryStr = "
		SELECT      DATE(t1.date_added) DateAdd, t1.product_id as ID, t2.name as Name  
		FROM        {$this->DbPrefix}product t1
		LEFT JOIN   {$this->DbPrefix}product_description t2 ON (t1.product_id = t2.product_id)
		WHERE       t1.image = '' 
		ORDER BY    t1.date_added DESC";

		return $this->ArrGet($QueryStr, "ID", "Name");
	}


	public function AttachImagasFromFolder()
	{
	    DbgLevel(2, _DebugLevel, basename(__FILE__) . "->" . __FUNCTION__ . "(" . __LINE__ .")");

	    $Result = 0;

	    $CID_product = new TCacheSQLInsertDuplicate($this->TSQL, "{$this->DbPrefix}product",
						"product_id, image",
						"image=VALUES(image)");
	

	    $ImageFS = _DirImageRoot . "/" . $this->Parent->DirImage;
	    $Dir = new TDir($ImageFS);
	    $ArrFiles = $Dir->GetFiles(false, TDir::cFile, "gif|jpg|jpeg|png");	
	    $ArrFiles->Reset();
	    while (list(, $Value) = $ArrFiles->Each()) {
		$PathInfo = pathinfo($Value);
		$ImageID = $PathInfo["filename"];
		if (is_numeric($ImageID)) {
		    $Result++;
		    //$QueryStr = sprintf("UPDATE {$this->DbPrefix}product SET image = '%s' WHERE product_id = %d AND image = ''",
			//	 $this->Parent->DirImage . "/" . $PathInfo["basename"],
			//	 $ImageID);
		    //$this->TSQL->Query($QueryStr);

		    // AND image = '' ???
		    $Fields = sprintf("%d, '%s'",
			$ImageID,
			$this->Parent->DirImage . "/" . $PathInfo["basename"]);
	
		    $CID_product->Add($Fields);
		}
	    }

    	    return $Result;
	}
};


class TRefProductDescr extends TRefBase
//-----------------------------------------------------------------------------
{
	private $CID_product_description;

	function __construct($aParent)
	{
	    parent::__construct($aParent);

	    $this->CID_product_description = new TCacheSQLInsertDuplicate($this->TSQL, "{$this->DbPrefix}product_description",
						"product_id, language_id, name, description, meta_keyword, meta_title, meta_description, tag",
						"name=VALUES(name), meta_keyword=VALUES(meta_keyword), meta_title=VALUES(meta_title), meta_description=VALUES(meta_description)");
	}

	public function Clear()
	{
	    $this->TSQL->Query(sprintf("DELETE FROM {$this->DbPrefix}product_description WHERE language_id = %d", $this->Parent->LangID));
	}

	
        public function Init()
        {
	    $QueryStr = sprintf("SELECT product_id AS ID FROM {$this->DbPrefix}product_description WHERE language_id = %d", $this->Parent->LangID);
	    $this->ArrInit($QueryStr, "ID", "");
	}


        public function AddArr($aItem)
	{
	    $this->Add((int)$aItem["ID"], (string)$aItem["Name"], (string)$aItem["Model"], (string)$aItem["Descr"]);
	}

	

        private function Add($aID, $aName, $aModel, $aDescr)
	{
	    $Fields = sprintf("%d, %d, '%s', '%s', '%s', '%s', '%s', '%s'",
					$aID,
					$this->Parent->LangID,
					$this->TSQL->EscStr($aName),
					$this->TSQL->EscStr(TStr::Replace($aDescr, ";", "<br/>")),
					$aModel . " " . $this->Parent->MetaKeyProduct,
					$aModel . " " . $this->Parent->MetaKeyProduct,
					$this->TSQL->EscStr($aName),
					"");

	    $this->CID_product_description->Add($Fields);
	}


        private function Add_Slow($aID, $aName, $aModel, $aDescr)
	{
	    //printf("dbg: %s, %d<br>\n", __FUNCTION__, $aID);

	    if ($this->TArrData->KeyExists($aID)) {
		$ArrRec = new TArray();
		$ArrRec["name"]			= $aName;
		$ArrRec["description"]		= TStr::Replace($aDescr, ";", "<br/>");
		$ArrRec["meta_keyword"]		= $aModel . " " . $this->Parent->MetaKeyProduct;
		$ArrRec["meta_title"]		= $ArrRec["meta_keyword"];
		$ArrRec["meta_description"]	= $aName;
		$QueryStr = $this->TSQL->GetUpdateStr("{$this->DbPrefix}product_description", $ArrRec, sprintf("WHERE product_id = %d AND language_id = %d", $aID, $this->Parent->LangID));
	    }else{
		$this->TArrData[$aID] = "";

		$QueryStr = sprintf("INSERT INTO {$this->DbPrefix}product_description (product_id, language_id, name, description, meta_keyword, meta_title, meta_description)
								VALUES (%d, %d, '%s', '%s', '%s', '%s', '%s')",
					$aID,
					$this->Parent->LangID,
					$this->TSQL->EscStr($aName),
					$this->TSQL->EscStr(TStr::Replace($aDescr, ";", "<br/>")),
					$MetaTitle,
					$MetaTitle,
					$this->TSQL->EscStr($aName));
	    }
	    $this->TSQL->Query($QueryStr);
	}
};



class TRefCategory extends TRefBase
//-----------------------------------------------------------------------------
{
	private $TCacheSQLIns;

	function __construct($aParent)
        {
            parent::__construct($aParent);
        }
            
   
	public function Clear()
	{
	    $this->TSQL->Query("DELETE FROM {$this->DbPrefix}category");
	    $this->TSQL->Query("DELETE FROM {$this->DbPrefix}category_description");
	    $this->TSQL->Query("DELETE FROM {$this->DbPrefix}category_to_store");
	    $this->TSQL->Query("DELETE FROM {$this->DbPrefix}category_to_layout");

	    // CreatePath()
	    //$this->TSQL->Query("DELETE FROM {$this->DbPrefix}category_path");

	    $this->TSQL->Query("DELETE FROM {$this->DbPrefix}url_alias WHERE query LIKE 'category_id=%'");
	}

	
        public function Init()
        {
	  $QueryStr = sprintf("
		SELECT     t1.category_id AS ID, t2.name AS Name
		FROM       {$this->DbPrefix}category t1
		LEFT JOIN  {$this->DbPrefix}category_description t2 ON (t1.category_id = t2.category_id)", 
		    $this->Parent->LangID);

		$this->ArrInit($QueryStr, "ID", "Name");
	}


	public function AddArr($aItem)
	{
	     //$this->Add($aItem["PID"], $aItem["ID"], $aItem["Name"]);
	}


	public function Add($aPID, $aID, $aName)
	{
	    DbgLevel(3, _DebugLevel, basename(__FILE__) . "->" . __FUNCTION__ . "(" . __LINE__ .")", "PID: $aPID, ID: $aID, Name: $aName");

	    if ($this->TArrData->KeyExists($aID)) {
		//
	    }else{
		$this->TArrData[$aID] = "";

		$QueryStr = sprintf("INSERT INTO {$this->DbPrefix}category (category_id, parent_id, status, date_added) VALUES (%d, %d, 1, Now())", $aID, $aPID);
		$this->TSQL->Query($QueryStr);

		$QueryStr = sprintf("INSERT INTO {$this->DbPrefix}category_description (category_id, name, language_id) VALUES (%d, '%s', %d)",	
					$aID, 
					$this->TSQL->EscStr($aName),
					$this->Parent->LangID);
		$this->TSQL->Query($QueryStr);

		$QueryStr = sprintf("INSERT INTO {$this->DbPrefix}category_to_store (category_id, store_id) VALUES (%d, %d)", $aID, 0);
		$this->TSQL->Query($QueryStr);
		
		$QueryStr = sprintf("INSERT INTO {$this->DbPrefix}category_to_layout (category_id, store_id, layout_id) VALUES (%d, %d, %d)", $aID, 0,	0);
		$this->TSQL->Query($QueryStr);

		$QueryStr = sprintf("INSERT INTO {$this->DbPrefix}url_alias (query, keyword) VALUES ('category_id=%d', 'c%s')",	$aID, $aID);
		$this->TSQL->Query($QueryStr);
	    }
	}

    
	private function GetItemsQuery($aWhere)
	{
	  $QueryStr = sprintf("
		SELECT     t1.category_id AS ID, t2.name AS Name
		FROM       {$this->DbPrefix}category t1
		LEFT JOIN  {$this->DbPrefix}category_description t2 ON (t1.category_id = t2.category_id)
		WHERE      t2.language_id = %d %s
		ORDER BY   t2.name", $this->Parent->LangID, $aWhere);

	    return $QueryStr;
        }


        public function GetItems($aPID)
	{
	    $QueryStr = $this->GetItemsQuery("AND t1.parent_id = $aPID");    
	    return $this->ArrGet($QueryStr, "ID", "Name");
        }

        
	public function GetItemsAll()
	{
	    $QueryStr = $this->GetItemsQuery("");		
	    return $this->ArrGet($QueryStr, "Name", "ID");
    	}


	public function HideEmpty()
	{
	    //printf("dbg: %s<br>\n", __FUNCTION__);
	    
	    $QueryStr =
    		"UPDATE 
    		    {$this->DbPrefix}category T1
    		LEFT JOIN  
    		    {$this->DbPrefix}product_to_category T2 ON (T1.category_id = T2.category_id)
		LEFT JOIN 
    		    {$this->DbPrefix}category T4 ON (T1.category_id = T4.parent_id)
    		SET 
    		    T1.status = 0
    		WHERE 
    		    T2.category_id IS NULL AND T4.category_id IS NULL";

		$this->TSQL->Query($QueryStr);
		//$this->TSQL->FreeResult();

		// OpenCart2 doesnt hide categories with status = 0. So delete
		$this->TSQL->Query("DELETE FROM {$this->DbPrefix}category WHERE status = 0");
		$this->TSQL->FreeResult();
	}


	private function CreatePathRecurs($aPID, &$aChain)
	{
	    DbgLevel(3, _DebugLevel, basename(__FILE__) . "->" . __FUNCTION__ . "(" . __LINE__ .")", $aPID);

            //printf("Dbg: %s, PID: %d, Level: %d<br>\n", __FUNCTION__, $aPID, $aLevel);

	    $aChain->Push($aPID);

	    $aChain->Reset();
	    while (list($No, $ID) = $aChain->Each()) {
		// skip top level
		if ($No > 0) {
		    //$QueryStr = sprintf("INSERT INTO {$this->DbPrefix}category_path (category_id, path_id, level) VALUES (%d, %d, %d);", $aPID, $ID, $No - 1);
		    //$this->TSQL->Query($QueryStr);
		    $QueryStr = sprintf("%d, %d, %d", $aPID, $ID, $No - 1);
		    $this->TCacheSQLIns->Add($QueryStr);
		}
	    }

            $ArrItems = $this->GetItems($aPID);
            $ArrItems->Reset();
            while (list($Label, ) = $ArrItems->Each()) {
                $this->CreatePathRecurs($Label, $aChain);
            }

	    $aChain->Pop();
        }


	public function CreatePath()
        {
	    DbgLevel(2, _DebugLevel, basename(__FILE__) . "->" . __FUNCTION__ . "(" . __LINE__ .")");

	    $this->TSQL->Query("DELETE FROM {$this->DbPrefix}category_path");

	    $this->TCacheSQLIns = new TCacheSQLInsert($this->TSQL, "{$this->DbPrefix}category_path", "category_id, path_id, level");
	    $this->CreatePathRecurs(0, new TArray());
	    $this->TCacheSQLIns->Flush();
        }
};


        
class TDbExpOpenCart extends TDbExpBase
//-----------------------------------------------------------------------------
{
	private $TRefProductDescr, $TRefCategory, $TRefVendor;
	public  $LangID, $DirImage, $TRefProduct;


	function __construct(TMySQL $aTSQL, $aDbPrefix="_oc")
	{
	    //$aTSQL->Debug = true;
    	    parent::__construct($aTSQL, $aDbPrefix);

	    $this->TRefProduct		= new TRefProduct($this);
	    $this->TRefProductDescr	= new TRefProductDescr($this);
	    $this->TRefCategory		= new TRefCategory($this);
	    $this->TRefVendor		= new TRefVendor($this);
	}


	public function GetInfo()
	{
	    $CntCategory     = $this->TSQL->GetCount(" {$this->DbPrefix}category");
	    $CntProduct      = $this->TSQL->GetCount(" {$this->DbPrefix}product");
	    $CntImages       = $this->TSQL->GetCount(" {$this->DbPrefix}product", "image <> ''");
	    $CntProductDescr = $this->TSQL->GetCount(" {$this->DbPrefix}product_description", "description <> ''");

	    $PerCent = $CntImages / $CntProduct * 100;

	    return sprintf("Categories: %d; Products: %d; Descriptions: %d; Images: %d; Percent %01.2f;", $CntCategory, $CntProduct, $CntProductDescr, $CntImages, $PerCent);
	}


	protected function GetProducts($aPID)
	{
	    return $this->TRefProduct->GetItems($aPID);
	}


	protected function GetCategories($aPID)
	{
	    return $this->TRefCategory->GetItems($aPID);
	}


	public function GetCategoriesAll()
	{
	    return $this->TRefCategory->GetItemsAll();
	}


	public function CreateCategoryPath()
	{
	    return $this->TRefCategory->CreatePath();
	}


	private function GetLangIdByCode($aCode)
	{
	    $QueryStr = sprintf("SELECT language_id AS ID FROM {$this->DbPrefix}language WHERE code = '%s'", $aCode);
	    $this->TSQL->Query($QueryStr);
	    $this->TSQL->FetchAssoc();
	    $this->TSQL->FreeResult();
            return $this->TSQL->GetItem("ID");
	}


	public function Clear()
	{
	    $this->TRefProduct->Clear();
	    $this->TRefProduct->ClearBase();
	    $this->TRefProductDescr->Clear();
	    $this->TRefCategory->Clear();
	    $this->TRefVendor->Clear();
	}


	public function InitRef()
	{
	    $this->TRefProduct->Init();
	    $this->TRefProductDescr->Init();
	    $this->TRefCategory->Init();
	    $this->TRefVendor->Init();
	}


	public function Init($aLang, $aDirImage)
	{
	    $this->LangID = $this->GetLangIdByCode($aLang);
	    if ($this->LangID == "") {
    		Error("Unknown language: $aLang");
	    }

    	    $this->DirImage = $aDirImage;
    	    TFS::MkDir(_DirImageRoot . "/" . $this->DirImage, 0777, true);
	}


	private function GrabImage($aUrl)
	{
		$Result = "";

		if ($aUrl == "")
		    return $Result;
		
		$ArrFile = TFS::GetFileInfo($aUrl);
		$Dir     = $this->DirImage . "/" . TStr::Sub($ArrFile["FileName"], -2, 2);
		$DirFS   = _DirImageRoot . "/" . $Dir;
		TFS::MkDir($DirFS);

		$FileSQL = $Dir   . "/" . $ArrFile["BaseName"];
		$FileFS  = $DirFS . "/". $ArrFile["BaseName"];
		//printf("dbg-a: %s %s %s", $aUrl, $FileFS, $FileSQL); die();

		if (TFS::FileExists($FileFS)) {
		    $Result = $FileSQL;
		}else{    
		    //printf("dbg: %s %s %s<br>\n", $aUrl, $FileFS, $FileSQL);
		    try {
			$Data = $this->ReadUrl($aUrl);
			if ($Data !== false) {
				printf("%s <br>\n", $aUrl);
				$this->SaveUrl($aUrl, $FileFS );
				$Result = $FileSQL;
			}
		    } catch (Exception $e) { 
		        $Result = ""; 
		    }
		}

		return $Result;
	}


	public function AddCategory($aPID, $aID, $aName)
	{
	    $this->TRefCategory->Add($aPID, $aID, $aName);
	}


	public function AddProduct($aItem)
	{
	    //printf("dbg: %s, %s<br>\n", __FUNCTION__, $aItem["Image"]);

	    $aItem["ImageFS"]  = $this->GrabImage($aItem["Image"]);
	    $aItem["VendorID"] = $this->TRefVendor->AddArr($aItem);

	    $this->TRefProduct->AddArr($aItem);
	    $this->TRefProductDescr->AddArr($aItem);
	}


	public function AttachImagasFromFolder()
	{
	    $Result = $this->TRefProduct->AttachImagasFromFolder();
            printf("images found in %s: %d<br>\n", $this->DirImage, $Result);
	}


	public function SetAdminPassw($aPassw)
	{
	    $this->TSQL->Query(sprintf("UPDATE {$this->DbPrefix}user SET password=md5('%s') WHERE user_id = 1", $aPassw));
	}
};

?>
