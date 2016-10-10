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




class TCategPrice
{
	Private $Xml; 
	Public  $TArrData;


	function __construct($aFile)
	{
	    printf("dbg: %s->%s->%s<br>\n", __CLASS__, __FUNCTION__, $aFile);

	    $this->TArrData  = new TArray();
	    $this->Xml	= simplexml_load_file($aFile);
	}


	public function AddItem($aCategID)
	{
	    $Charge = $this->GetCategValue($aCategID, $this->Xml, 0, "Value");
	    $this->TArrData->SetItem($aCategID, $Charge);
	}


	public function GetPrice($aCategID, $aPrice)
	{
	    $Result = -1;

	    if ($this->TArrData->KeyExists($aCategID)) 
		$Result = $this->TArrData[$aCategID];

	    if ($Result < 0) 
		$Result = $this->GetRetailCharge($aPrice);

	    return $aPrice + ($aPrice * $Result / 100);
	}


        // search Dir3 for Value and return 30 
        // <Dir1 Value=10><Dir2 Value=30><Dir3 Value=0></Dir2></Dir1> 
        private function GetCategValue($aID, $aNode, $aPResult, $aField)
        {
            $Result = -1;

            foreach($aNode->Children() as $Node) {
                $Value = $Node[$aField] == 0 ? $aPResult : $Node[$aField];

                if ($Node["ID"] == $aID) {
                    $Result = $Value;
                    break;
                }else{
                    if ($Node->Children() && $Result < 0) {
                        $Result = $this->GetCategValue($aID, $Node, $Value, $aField);
                    }
                }
            }
            
            return $Result;
        }


	protected function GetRetailCharge($aPrice)
	{
	    if ($aPrice < 1) 
		return 70;
	    elseif ($aPrice < 10)
		return 50;
	    elseif ($aPrice < 100)
		return 25;
	    elseif ($aPrice < 500)
		return 15;
	    elseif ($aPrice < 1000)
		return 10;
	    elseif ($aPrice < 2000)
		return 8;
	    elseif ($aPrice < 5000)
		return 5;
	    else
		return 3;
	}
}



abstract class TPriceListBase
{
	private		$TArrVendor, $TArrWare, $TArrDelPrefix;
	protected	$Xml, $TSQL;
	public		$TArrData, $TCategPrice;


	abstract protected function ItemFilter();
	abstract protected function ItemParse($aData);
	//abstract protected function DataParse($aTDbExp);


	function __construct()
	{
		$this->TCategPrice = NULL;		
		$this->TArrData	   = new TArray();
		$this->Xml         = new SimpleXMLElement("<root></root>");
	}


	protected function ReadUrl($aUrl)
	{
		return file_get_contents(TStr::Replace($aUrl, " ", "%20"), false, NULL);
	}


	protected function SaveUrl($aUrl, $aFileName)
	{
		return file_put_contents($aFileName, $this->ReadUrl($aUrl));
	}


	public function LoadFile($aFile, $aStrip = true)
	{
		if ($aStrip) {
		    $String = file_get_contents($aFile);
		    $String = TStr::StripNonPrintable($String);
		    $String = TStr::Replace($String, array('&'), array('/'));
		    $this->Xml = simplexml_load_string($String, "SimpleXMLElement", LIBXML_PARSEHUGE);
		}else{
		    $this->Xml = simplexml_load_file($aFile);
		}
	}


	public function LoadFileCharge($aFile)
	{
		$this->TCategPrice = new TCategPrice($aFile);
	}
};

?>
