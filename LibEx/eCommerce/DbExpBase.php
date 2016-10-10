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
require_once(_DirCommonLib . "/Dir.php");
require_once(_DirCommonLib . "/String.php");
//require_once(_DirCommonLib3rd . "/simple_html_dom.php");


abstract class TRefBase
//-----------------------------------------------------------------------------
{
    protected	$TSQL, $DbPrefix, $Parent;
    public	$TArrData;

    abstract public function Init();
    abstract public function Clear();
    abstract public function AddArr($aItem);


    function __construct($aParent)
    {
	$this->TSQL     = $aParent->TSQL; 
	$this->DbPrefix = $aParent->DbPrefix;
	$this->Parent   = $aParent;

        $this->TArrData = new TArray();
    }

    
    protected function ArrInit($aSQL, $aLabel, $aValue) 
    {
	$this->TArrData = $this->ArrGet($aSQL, $aLabel, $aValue);
    }


    protected function ArrGet($aSQL, $aLabel, $aValue) 
    {
	$ArrResult = new TArray();

        $this->TSQL->Query($aSQL);
        while ($this->TSQL->FetchAssoc()) {
                $ArrResult->SetItem($this->TSQL->GetItem($aLabel), $this->TSQL->GetItem($aValue));
        }

        $this->TSQL->FreeResult();
	return $ArrResult;
    }
};


abstract class TDbExpBase
//-----------------------------------------------------------------------------
{
	public    $TSQL, $DbPrefix;
	public	  $MetaKeyProduct, $ProviderName;

        abstract protected function GetCategories($aPID);
        abstract protected function GetProducts($aPID);


	function __construct(TMySQL $aTSQL, $aDbPrefix = "")
	{
		$this->TSQL     = $aTSQL;
		$this->DbPrefix = $aDbPrefix;
	}


        private function GetCategoryTreeRecurs($aPID, $aLevel, $aNode)
	{
	    //printf("dbg: %s<br>\n", __FUNCTION__);

	    $ArrItems = $this->GetCategories($aPID);
    	    $ArrItems->Reset();
            while (list($Label, $Value) = $ArrItems->Each()) {
		//printf("\nDbg: Level: $aLevel, PID: $aPID, ID: $Label, Name: $Value\n");   
		$Elem = $aNode->addChild("Category");
		$Elem["Value"] = 0;
		$Elem["ID"]    = $Label;
		$Elem["Name"]  = $Value;

		$this->GetCategoryTreeRecurs($Label, $aLevel + 1, $Elem);

		//$ArrProd = $this->GetProducts($Label);
		//$ArrProd->Show();          
	    }
        }

	
	public function GetCategoryTree()
	{
	    //printf("dbg: %s<br>\n", __FUNCTION__);

	    $doc = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><root/>');
	    $this->GetCategoryTreeRecurs(0, 0, $doc);
	    $xml = $doc->asXML();
	    
	    // SimpleXMLElement cant format output. So use trick
	    $dom = new DOMDocument();
	    $dom->preserveWhiteSpace = false;
	    $dom->formatOutput = true;
	    $dom->loadXML($xml);
	    $dom->saveXML();
    
	    return $dom;
	}


	protected function ReadUrl($aUrl)
	{
	    return @file_get_contents(str_replace(" ", "%20", $aUrl), false, NULL);
	}


	protected function SaveUrl($aUrl, $aFileName)
	{
	    return @file_put_contents($aFileName, $this->ReadUrl($aUrl));
	}


        protected function GetStrSeo($aString)
        {
	    $Tmp = TStr::TranPunktToUrl($aString);
	    $Tmp = TStr::TranCyrToLat($Tmp);
	    $Tmp = TStr::Trim($Tmp, "_ \t\n\r\0\x0B");
	    return $Tmp;
        }


	public function GetDbPrefix()
	{
	    return $this->DbPrefix;
	}


	public function GetSQL()
	{
	    return $this->TSQL;
	}
};

?>
