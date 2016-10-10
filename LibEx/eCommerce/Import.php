<?php
/*
chat site
# https://livehelperchat.com/support-project-4c.html
# https://consultsystems.ru/help/code/opencart/
*/

$EOL='\n';

require_once("Config.php");
require_once (_DirCommonLibEx . "/eCommerce/DbExpOpenCart.php");
require_once (_DirCommonLibEx . "/eCommerce/" . _PriceProvider . ".php");


class TImport
{
  private $TSQL1, $TDbExp;


  function __construct()
  {
    $this->ConnectDB();
    $this->InitDbExp(_Lang);
  }


  function ConnectDB()
  {
    $this->TSQL1 = new TMySQL(_DB_HostName, _DB_UserName, _DB_Password, _DB_DataBase);
    $this->TSQL1->Query("SET CHARACTER SET UTF8");
    $this->TSQL1->Query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'");
  }


  function InitDbExp($aLang)
  {
    $this->TDbExp = new TDbExpOpenCart($this->TSQL1, _DB_Prefix);
    $this->TDbExp->Init($aLang, _DirImage);
    printf("%s<br>\n", $this->TDbExp->GetInfo());
  }


  function ImportImages()
  {
    $this->TDbExp->AttachImagasFromFolder();
  }


  function GetCategoryTree()
  {
      $doc = $this->TDbExp->GetCategoryTree();
      $doc->Save("CategoryExtraChargeNew.xml");	
  } 


  function ImportFile($aFile)
  {
    $this->TDbExp->MetaKeyProduct = 'купити, опис, ціна, гарантія, доставка, тернопіль';

    if (file_exists($aFile)) {
      $PriceListClass = "T" . _PriceProvider; 	
      $PriceList = new $PriceListClass();
      $PriceList->LoadFile($aFile, true);

      if (TFS::FileExists("CategoryExtraCharge.xml")) {	
        $PriceList->LoadFileCharge("CategoryExtraCharge.xml");	
      }	 	

      //$this->TDbExp->Clear();
      $this->TDbExp->TRefProduct->ClearRelated();
      $PriceList->DbExp($this->TDbExp);

      //$this->TDbExp->HideEmptyCategory();	
    }else{
      printf("Error: File not found %s !<br>", $aFile);
    }
  }

 
  function ShowNoImage()
  {
    $ArrItems = $this->TDbExp->TRefProduct->GetNoImage();
    $ArrItems->Reset();
    while (list($Label, $Value) = $ArrItems->Each()) {
      printf("%s, %s<br>", $Label, $Value);
    }
    
    printf("Count: %d<br>", $ArrItems->GetCount());
  }
};
?>
