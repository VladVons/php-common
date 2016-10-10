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

 require_once("_Config.php");
 require_once(_DirCommonPage ."/Index_Head.php");

 global $gTLang;

 $Lines2 = $gTLang->Parse("Actions");

 if ($aAction != "NoSpam") {
	//print("Action" . $aAction . "<BR>");
	//$TArray1 = new TArray($_POST);
	//$TArray1->Show();
	//$gTLang->Show();
	//error_reporting(0);
	//Debug($_POST["body"]);
 }

 if ($aAction == "") {
    printf("%s", $gTLang->GetItem("Action is empty"));
 }elseif ($aAction == "MailTest") {
    MailTest();
 }elseif ($aAction == "NoSpam") {
    NoSpam(new TNoSpam(), $aGet->GetItem("Mode"));
 }elseif ($aAction == "OrderMail") {
    OrderMail($_POST, $gTLang, new TMail(), new TMoney($gTLang->GetLanguage()), new TNoSpam());
 }elseif ($aAction == "OrderList") {
    OrderList($_POST, $TLang);
 }elseif ($aAction == "PriceListSubscribe") {
    PriceListSubscribe($_POST, $gTLang, new TMail(), new TNoSpam());
 }elseif ($aAction == "FotoViewSkip") {
    FotoViewSkip($_POST, $gTLang);
 }elseif ($aAction == "ContactsMail") {
	//print_r($_POST); print_r($_GET); die();
    ContactsMail($_POST, $gTLang, new TNoSpam());
 }elseif ($aAction == "SendSMS") {
    SendSMS($_POST, $gTLang);
 }elseif ($aAction == "SearchItem") {
    SearchItem($_POST, $gTLang);
 }elseif ($aAction == "ImportData") {
    ImportData($_POST, $gTLang);
 }elseif ($aAction == "Support") {
    Support($_POST, $gTLang);
 }elseif ($aAction == "ReportError") {
    ReportError($_POST, $gTLang);
 }elseif ($aAction == "LastVersion") {
    LastVersion($_POST, $gTLang);
 }elseif ($aAction == "SetLang") {
    SetLang($_Get, $gTLang);
 }elseif ($aAction == "phpinfo") {
    phpinfo();
 }else{
	$Err = sprintf("%s:$aAction\n\n%s", $gTLang->GetItem("Unknown àction"), $gTLang->GetItem("ReportError"));
    Show($Err);
 }
 exit;



function ImportData($aForm, $aTLang)
////////////////////////////////////////////////////////////////////////////
{
 $TSQL1 = new TMySQL(_DB_HostName, _DB_UserName, _DB_Password);
 $TSQL1->SelectDB(_DB_DataBase);
 $TSQL1->QueryFile(_DirUser . "/Catalog/FileOsc.txt");
 header("Location: index.php?PN=Message&MsgID=Saved&MsgURL=index.php");
}


function NoSpam($aTNoSpam, $aMode)
////////////////////////////////////////////////////////////////////////////
{
 if ($aMode == "Dig") {
	$aTNoSpam->ShowImageReceive();
 }else
 if ($aMode == "Txt") {
	$aTNoSpam->ShowImage(0xFFFFFF, $_GET["Text"], 0);
 }
}


function CheckIt($aTLang, $aTNoSpam, $aTArray)
////////////////////////////////////////////////////////////////////////////
{
 printf("Debug:%s, SV:%s", $aTNoSpam->GetValue("Number"), $aTArray->GetItem("_NoSpamValue_Chk"));

 $ErrMsg = "";
 $TCheckForm1 = new TCheckForm($aTLang);
 $TCheckForm1->Load($aTArray);
 if ($TCheckForm1->IsLength() != "" || $TCheckForm1->IsFilter() != "") {
	$ErrMsg = $TCheckForm1->GetLastError();
 }else
 if ($aTNoSpam->GetValue("Number") != $aTArray->GetItem("_NoSpamValue_Chk")) {
    $ErrMsg = $aTLang->GetItem("Confirmation code is wrong");
 }

 if ($ErrMsg != "") {
    $TReport1 = new TReport(_LogFile);
    $TReport1->Log("CheckIt, " . $ErrMsg);

    $ErrMsg = urlencode($ErrMsg);
    header("Location: index.php?PN=Message&MsgID=Error&MsgURL=Back&MsgStr=$ErrMsg");
    exit;
 }
}



function CheckAndSaveCookies($aTLang, &$aTArray, $aTNoSpam)
////////////////////////////////////////////////////////////////////////////
{
 $aTArray = $aTArray->StripTags();
 //$aTArray->Show(); Die();
 $Cookies1 = new TCookies();
 $Cookies1->SetArray($aTArray, 7);

 $TArray1 = $aTArray->GrepLabel("_Chk");
 CheckIt($aTLang, $aTNoSpam, $TArray1);
}


function MailTest()
////////////////////////////////////////////////////////////////////////////
{
 $TMail1 = new TMail();
 $TMail1->MICharset	= "windows-1251";
 $TMail1->MIToAddress   = "VladVons@gmail.com";
 //$TMail1->MIFromAddress = "VladVons@oster.com.ua";
 $TMail1->MIFromAddress = "Tolik@bereka-radio.com.ua";
 $TMail1->MISubject     = "Test from oster.com.ua";
 $TMail1->MIMessage     = "Test from oster.com.ua";
 Printf("MailTest: %d, To: %s, Time: %s", $TMail1->Send(), $TMail1->MIToAddress, date("Y-m-d H:i:s"));
}


function MailIt($aTLang, $aAdr, $aSubj, $aBody)
////////////////////////////////////////////////////////////////////////////
{
 $TMail1 = new TMail();
 $TMail1->MICharset		= $aTLang->GetItem("HeadMetaCharset");
 $TMail1->MIToAddress   = $aAdr;
 $TMail1->MICCAddress   = $aTLang->GetItem("Email_To");
 $TMail1->MIBCCAddress  = $aTLang->GetItem("Email_CC");
 $TMail1->MIFromAddress = $aTLang->GetItem("Email_To");
 $TMail1->MISubject     = $aTLang->GetItem($aSubj);
 $TMail1->MIMessage     = $aBody . "\n\n" . $aTLang->GetItem("EMailFooter") . "\n";
 $TMail1->Send();

 //print("Mail:" . $TMail1->MICCAddress); die();
 $TReport1 = new TReport(_LogFile);
 $TReport1->Log("$aSubj, $aBody");
}


function SendSMS($aForm, $aTLang)
////////////////////////////////////////////////////////////////////////////
{
  $Macros = $aTLang->TParseFile->LoadMacros("SmsSend");

  //$Macros->SetParam("cPhonesNo",	$aForm["PhonesNo"]);
  //$Macros->SetParam("cMessage",	$aForm["Message"]);
  //$Macros->SetParam("cDescription",$aForm["AlphaName"]);
  //$Macros->SetParam("cAlphaName",	$aForm["AlphaName"]);
  //$Macros->SetParam("cProvider",	$aForm["Provider"]);
  //$Macros->SetParam("cLogin",		$aForm["Login"]);
  //$Macros->SetParam("cPassword",	$aForm["Password"]);

  $Macros->SetParams(
	$aForm["PhonesNo"] . "|" . 
	$aForm["Message"] . "|" . 
	$aForm["Description"] . "|" .
	$aForm["AlphaName"] . "|" .
	$aForm["Provider"] . "|" .
	$aForm["Login"] . "|" .
	$aForm["Password"]
	);
  
  Show($Macros->Build());
}


function OrderListSave($aForm)
////////////////////////////////////////////////////////////////////////////
{
 $i = 0;
 $AOrder = $_SESSION["Order"];
 reset($AOrder);
 while (list($Label, $Value) = each($AOrder)) {
     $i = $i+1;
     $_SESSION["Order"][$Label]["Cnt"] = strip_tags($aForm["_Order_Cnt_" . $i]);
 }
}


function OrderList($aForm, $aTLang)
////////////////////////////////////////////////////////////////////////////
{
 if (isset($aForm["Clear"])) {
    unset($_SESSION["Order"]);
    header("Location: index.php?PN=Order");
 }elseif (isset($aForm["Save"])) {
    OrderListSave($aForm);
    header("Location: index.php?PN=Message&MsgID=Saved&MsgURL=index.php?PN=Order");
 }elseif (isset($aForm["Order"])) {
    OrderListSave($aForm);
    header("Location: index.php?PN=MailUs&Action=Order");
 }
}


function OrderMail($aForm, $aTLang, $aMail, $aTMoney, $aTNoSpam)
////////////////////////////////////////////////////////////////////////////
{
 $TArray1 = new TArray($aForm);
 CheckAndSaveCookies($aTLang, $TArray1, $aTNoSpam);

 $i = 0; $Sum = 0;
 $Articles = "";
 $AOrder = $_SESSION["Order"];
 reset($AOrder);
 while (list($Group, $Value) = each($AOrder)) {
     list($Item) = each($Value);
     $i = $i+1;
     $AItem  = $_SESSION["Order"][$Group][$Item];
     $ICnt   = $AItem["Cnt"];
     $IPrice = $AItem["Price"];
     $Sum    = $Sum + ($ICnt*$IPrice);
     $Articles .= "N $i\t" .
                  "$Group-$Item\t" .
                  $aTLang->GetItem("Quantity") . " $ICnt\t" .
                  $aTLang->GetItem("Price")    . " $IPrice\t" .
                  $aTLang->GetItem("Sum")      . " " . $ICnt*$IPrice . "\n";
 }

 $MailBody =
   $aTLang->GetItem("Supplier") . ":\n" .
   $aTLang->GetItem("SupplierInfo") . "\n" .
   "\n---\n" .
   $aTLang->GetItem("Customer")    . ":\n" .
   $aTLang->GetItem("Full name")    . ": " . $TArray1->GetItem("_MailUs_FullName_Chk")    . "\n" .
   $aTLang->GetItem("Company name") . ": " . $TArray1->GetItem("_MailUs_CompanyName_Chk") . "\n" .
   $aTLang->GetItem("Phone")       . ": " . $TArray1->GetItem("_MailUs_Phone_Chk")       . "\n" .
   $aTLang->GetItem("eMail")       . ": " . $TArray1->GetItem("_MailUs_EMail_Chk")       . "\n" .
   $aTLang->GetItem("Message")     . ": " . $TArray1->GetItem("_MailUs_Message_Chk")     . "\n" .
   $aTLang->GetItem("Address") 	   . ": " . $TArray1->GetItem("_MailUs_Address_Chk")     . "\n" .
   "\n---\n" .
   $aTLang->GetItem("Date") . ": " . date($aTLang->GetItem("Sys_DateFormat", "Y/m/d h:i:s")) . "\n" .
   $Articles . "\n\n" .
   $aTLang->GetItem("Total") . " ($Sum)" . $aTMoney->Num2Str($Sum) . "\n";

 MailIt($aTLang, $TArray1->GetItem("_MailUs_EMail_Chk"), $aTLang->GetItem("Order"), $MailBody);
 header("Location: index.php?PN=Message&MsgID=OrderReceived&MsgURL=index.php");
}


function PriceListSubscribe($aForm, $aTLang, $aMail, $aTNoSpam)
////////////////////////////////////////////////////////////////////////////
{
 $TArray1 = new TArray($aForm);
 CheckAndSaveCookies($aTLang, $TArray1, $aTNoSpam);

 $MailBody =
   $aTLang->GetItem("FullName")    . ": " . $TArray1->GetItem("_MailUs_FullName_Chk") . "\n" .
   $aTLang->GetItem("eMail")       . ": " . $TArray1->GetItem("_MailUs_EMail_Chk")    . "\n";
 MailIt($aTLang, $TArray1->GetItem("_MailUs_EMail_Chk"), "Subscribe", $MailBody);

 header("Location: index.php?PN=Message&MsgID=DataReceived&MsgURL=index.php");
}



function ContactsMail($aForm, $aTLang, $aTNoSpam)
////////////////////////////////////////////////////////////////////////////
{
 $TArray1 = new TArray($aForm);
 CheckAndSaveCookies($aTLang, $TArray1, $aTNoSpam);

 $MailBody =
   $aTLang->GetItem("Full name")    . ": " . $TArray1->GetItem("_MailUs_FullName_Chk")    . "\n" .
   $aTLang->GetItem("Company name") . ": " . $TArray1->GetItem("_MailUs_CompanyName_Chk") . "\n" .
   $aTLang->GetItem("Phone")        . ": " . $TArray1->GetItem("_MailUs_Phone_Chk")       . "\n" .
   $aTLang->GetItem("eMail")        . ": " . $TArray1->GetItem("_MailUs_EMail_Chk")       . "\n" .
   $aTLang->GetItem("Message")      . ": " . $TArray1->GetItem("_MailUs_Message_Chk")     . "\n" .
   $aTLang->GetItem("Address") 	    . ": " . $TArray1->GetItem("_MailUs_Address_Chk")     . "\n";
 MailIt($aTLang, $TArray1->GetItem("_MailUs_EMail_Chk"), $TArray1->GetItem("_MailUs_Subject_Chk"), $MailBody);

 header("Location: index.php?PN=Message&MsgID=DataReceived&MsgURL=index.php");
}



function FotoViewSkip($aForm, $aTLang)
////////////////////////////////////////////////////////////////////////////
{
 $DirName  = $aForm["_FotoView_Path"] . "/" . $aForm["_FotoView_Catalog"];
 $TDir1 = new TDir($DirName);
 $TArray1 = $TDir1->GetFiles(false, TDir::cFile, ".jpg");
 $TArray1->Show();
 if ($TArray1->GetCount() > 1 && $TArray1->SearchEx($aForm["_FotoView_Item"]) !== "") {
    if (isset($aForm["_FotoView_Begin"])) {
       $IsSkip = $TArray1->Reset();
    }else
	if (isset($aForm["_FotoView_Prev"])) {
       $IsSkip = $TArray1->Prev();
    }else
    if (isset($aForm["_FotoView_Next"])) {
       $IsSkip = $TArray1->Next();
    }else
    if (isset($aForm["_FotoView_End"])) {
       $IsSkip = $TArray1->End();
    }

    if ($IsSkip !== false) {
       $Value = $TArray1->Current();
       header("Location: index.php?PN=FotoView&Catalog=" . $aForm["_FotoView_Catalog"] . "&Item=" . $Value);
       exit;
    }
 }
 header("Location: index.php?PN=Message&MsgID=DirBrowsed&MsgURL=Back");
}


////////////////////////////////////////////////////////////////////////////
function SearchItem($aForm, &$aTLang)
{
 $String1 = $aForm["_Search_Text_Chk"];
 if ($String1 == "") {
    header("Location: index.php?PN=Message&MsgID=String%20is%20empty&MsgURL=Back");
 }else{
    $String2 = $aForm["_Search_Menu"];
    header("Location: index.php?PN=Shop&Action=Search&Field=$String2&Item=$String1");
 }
}

function ReportError($aForm, $aTLang)
////////////////////////////////////////////////////////////////////////////
{
 // Url: http://jdv-soft.com/Actions.php?Action=ReportError
 // Params:

 $aFrom 	= $aForm["from"];
 $aSubject	= $aForm["subject"];
 $aBody		= $aForm["body"];
 $aHash		= $aForm["hash"];
 $aProj		= $aForm["proj"];
 $aVersion	= $aForm["version"];
 $aBuild	= $aForm["build"];

 if (md5("11") == $aHash) {
	$TMail1 = new TMail();

	//$TMail1->MIToAddress   = $aFrom;
	//$TMail1->MIFromAddress = $aTLang->GetItem("Email_Support");
	//$TMail1->MISubject     = "$aSubject ($aProj, $aVersion, $aBuild) $aHashOk";
	//$TMail1->MIMessage     = $aTLang->GetItem("ThanksForReport") . "\n\n" . $aTLang->GetItem("EMailFooter") . "\n";
	//$TMail1->MIMessage		= TStr::Replace($TMail1->MIMessage, "\r", "\r\n");
	//$TMail1->Send();

	$TMail1->MIToAddress   = $aTLang->GetItem("Email_Support");
	$TMail1->MIFromAddress = $aFrom;
	$TMail1->MISubject     = "$aSubject ($aProj, $aVersion, $aBuild) $aHashOk";
	$TMail1->MIMessage     = $aBody;
	$TMail1->Send();

	// return succsess to sender
	print("0");
 }
}


function Support($aForm, $aTLang)
////////////////////////////////////////////////////////////////////////////
{
 $Proj = $_GET["Proj"];
 $Mode    = $_GET["Mode"];
 if ($Proj == "FrontOffice") {
	if ($Mode == "Forum") {
		$Section = "Ext/forum/viewforum.php?f=19";
	}elseif ($Mode == "FAQ") {
		$Section = "Ext/forum/viewforum.php?f=28";
	}elseif ($Mode == "Suggestion") {
		$Section = "Ext/forum/viewforum.php?f=23";
	}elseif ($Mode == "Bug") {
		$Section = "Ext/bug";
	}else{
	    Show("Unknown Mode: $Mode");
	}
 }elseif ($Proj == "DBFView") {
	if ($Mode == "Forum") {
		$Section = "Ext/forum/viewforum.php?f=26";
	}elseif ($Mode == "FAQ") {
		$Section = "Ext/forum/viewforum.php?f=29";
	}elseif ($Mode == "Suggestion") {
		$Section = "Ext/forum/viewforum.php?f=27";
	}elseif ($Mode == "Bug") {
		$Section = "Ext/bug";
	}else{
	    Show("Unknown Mode: $Mode");
	}
 }else{
    Show("Unknown Project: $Proj");
 }

 $Url = "http://" . $_SERVER['HTTP_HOST'] . "/" . $Section;
 header("Location: $Url");
}


function LastVersion($aForm, $aTLang)
////////////////////////////////////////////////////////////////////////////
{
 $Proj = $_GET["Proj"];
 $aTLang->ShowItem("LastVersion_$Proj");
}


function SetLang($aForm, $aTLang)
////////////////////////////////////////////////////////////////////////////
{
 $aTLang->SetLanguage($aForm["Lang"]);
 header("Location: index.php");
}
?>
