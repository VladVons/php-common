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

require_once("Common.php");

class THTML
/////////////////////////////////////////////////////
{
 static function Encode($aUrl)
 {
	if (TStr::Pos($aUrl, "?") == -1 && TStr::Pos($aUrl, "#") == -1 && TStr::Pos($aUrl, "http:") == -1) {
		$aUrl = GetStatLink(UrlCode($aUrl, "RawEncode"));
	}
	return $aUrl;
 }

 
 static function Font($aText, $aColor, $aSize)
 {
	$Color = ($aColor == "" ? "" : sprintf('color="%s"', $aColor));
	$Size  = ($aSize == "" ? ""  : sprintf('size="%s"', $aSize));
	return sprintf('<font %s %s>%s</font>', $Color, $Size, $aText);
 }

 
 static function Href($aHref, $aText = "", $aHint = "", $aClass = "")
 {
	$Text  = ($aText  == "" ? $aHref : $aText);
	$Hint  = ($aHint  == "" ? "" : sprintf('title="%s"', $aHint));
	$Class = ($aClass == "" ? "" : sprintf('class="%s"', $aClass));

	if (IsLinkExternal($aHref)) {
		if (TStr::Pos($aHref, "www.") == 0) {
			$aHref = TStr::Replace($aHref, "www.", "http://www.");
		}	
		$Target = 'target="_blank"';
	}else{	
		$Target = "";
		$aHref = THTML::Encode($aHref);
	}	
	
	return sprintf('<a href="%s" %s %s %s>%s</a>', $aHref, $Hint, $Target, $Class, $Text);
 }

 
 static function GetFileHref($aFileName, $aCaption) 
 {
	if (IsLinkExternal($aFileName)) {
		return THTML::Href($aFileName, $aCaption, "", "");
	}else{	
		$Array1  = TFS::GetFileInfo($aFileName);
		$FileSize = GetShortSize($Array1->GetItem("Size"));
		$Hint = sprintf("%s; $FileSize; %s", TFS::GetFileName($aFileName), $Array1->GetItem("Date"));
		return THTML::Href($aFileName, $aCaption, $Hint, "") . " ($FileSize)";
	}	
 }

 
 static function Button($aHref, $aClass)
 {
	return sprintf('<div class="%s">%s<span></span></div><div style="clear:both;"></div>', $aClass, $aHref);
 }

 
 static function File($aFileName, $aText)
 {
	return THTML::GetFileHref($aFileName, $aText);
 }


 static function Flash($aFileName, $aLink = "")
 {
	list($Width, $Height) = getimagesize($aFileName);
	$Result = sprintf('<object type="application/x-shockwave-flash" data="%s" width="%s" height="%s"><param name="movie" value="%s"><param name="quality" value="high"><param name="scale" value="exactfit"><param name="bgcolor" value="#ffffff"><embed src="%s" width="%s" height="%s" type="application/x-shockwave-flash" play="true" loop="true" menu="true"></embed></object>',
			$aFileName, $Width, $Height, $aFileName, $aFileName, $Width, $Height);
	return $Result;
 }


 static function MPlayList($aFileName) 
 {
	if (TStr::Pos($aFileName, ",") == -1) {
		$Result = "file=" . $aFileName; 
	}else{
		$PlayList = "";
		$Token1 = new TToken(";");
		$Token1->LoadFromString($aFileName);
		$Token1->TArrData->Reset();
		while (list($Label, $Value) = $Token1->TArrData->Each()) {
			$Token2 = new TToken(",");
			$Cnt = $Token2->LoadFromString($Value);
			$Str1  = ($Cnt > 0 ? "'file':'" .     $Token2->GetItem(0) . "'" : "");
			$Str1 .= ($Cnt > 1 ? ",'comment':'" . $Token2->GetItem(1) . "'" : "");
			$Str1 .= ($Cnt > 2 ? ",'poster':'" .  $Token2->GetItem(2) . "'" : "");
			$PlayList .= "{" . $Str1 . "},";
		}
		$Result = "pl={'playlist':[" . TStr::Left($PlayList, TStr::Length($PlayList) - 1) . "]}"; 
	}
	return $Result;
 }
 
 
 static function MAudio($aFileName, $aSkin = "", $aWidth = "")
 {
	$Skin   = (empty($aSkin) ? "m=audio" : "st=" . _DirCommonFlash . "/" . $aSkin);
	$Width  = (empty($aWidth) ? _AudioWidth : $aWidth);
	$Height = 35;
	$aFileName = HttpFileName($aFileName);
	$FileName = IConvert($aFileName);

	$Result=sprintf('<object id="MyUppodA" type="application/x-shockwave-flash" data="%s" width="%s" height="%d"><param name="bgcolor" value="#ffffff" /><param name="allowScriptAccess" value="always" /><param name="movie" value="%s" /><param name="flashvars" value="%s&amp;%s"></object>'
	, _DirCommonFlash . "/uppod.swf", $Width, $Height, _DirCommonFlash . "/uppod.swf", $Skin, THTML::MPlayList($FileName));
	
	return $Result;			
 }
 
 
 static function MVideo($aFileName, $aSkin = "", $aWidth = "")
 {
	$Skin	= (empty($aSkin) ? "m=video" : "st=" . _DirCommonFlash . "/" . $aSkin);
	$Width	= (empty($aWidth) ? _VideoWidth : $aWidth);
	$Height	= IntVal($Width * 3 / 4);
	$aFileName = HttpFileName($aFileName);
	$FileName = IConvert($aFileName);

	$Result = sprintf('<object id="MyUppodV" type="application/x-shockwave-flash" data="%s" width="%s" height="%d"><param name="bgcolor" value="#ffffff" /><param name="allowFullScreen" value="true" /><param name="allowScriptAccess" value="always" /><param name="movie" value="%s" /><param name="flashvars" value="%s&amp;%s"></object>'
	, _DirCommonFlash . "/uppod.swf", $Width, $Height, _DirCommonFlash . "/uppod.swf", $Skin, THTML::MPlayList($FileName));
	
	return $Result;			
 }


 static function MFoto($aFileName, $aSkin = "", $aWidth = "")
 {
	$Skin	= (empty($aSkin) ? "m=photo" : "st=" . _DirCommonFlash . "/" . $aSkin);
	$Width	= (empty($aWidth) ? _VideoWidth : $aWidth);
	$Height	= IntVal($Width * 3 / 4);
	$FileName = IConvert($aFileName);
	
	$Result = sprintf('<object id="MyUppodF" type="application/x-shockwave-flash" data="%s" width="%s" height="%d"><param name="bgcolor" value="#ffffff" /><param name="allowFullScreen" value="true" /><param name="allowScriptAccess" value="always" /><param name="movie" value="%s" /><param name="flashvars" value="%s&amp;%s"></object>'
	, _DirCommonFlash . "/uppod.swf", $Width, $Height, _DirCommonFlash . "/uppod.swf", $Skin, THTML::MPlayList($FileName));
	
	return $Result;			
 }

 
 static function Image($aFileName, $aText = "", $aAlign = "", $aWidth = "", $aLink = "")
 {
	$Text   = ($aText   == "" ? "" : sprintf('title="%s" alt="%s"', $aText, $aText));
	$Align  = ($aAlign  == "" ? "" : sprintf('align="%s"', $aAlign));
	$Width	= ($aWidth   == "" ? "" : sprintf('width="%s"', $aWidth));
	$Link   = ($aLink   == "" ? $aFileName : GetQuotedValue($aLink, 'href="'));

	$Img = sprintf('<img src="%s" %s %s %s style="margin:5px 5px 5px 5px;" border="1">', THTML::Encode($aFileName), $Align, $Text, $Width);
	return THTML::Href($Link, $Img, $aText, "");
 }
	
	
 static function Thumb($aFileName, $aText, $aAlign, $aSize = "", $aLink = "")
 {
	$Link = ($aLink == "" ? $aFileName : GetQuotedValue($aLink, 'href="'));

	list($Width, $Height) = getimagesize($aFileName);
	$TextDim = $aText . " (". $Width ."x" . $Height . ")";

	$FileThumb = GetThumbDef($aFileName, $aSize);

	$Img = sprintf('<img src="%s" alt="%s" style="margin-bottom: 5px;" border="1">', THTML::Encode($FileThumb), $aText);
	$Href = THTML::Href($Link, $Img, $TextDim, "");
	return sprintf('<p style="float:%s; margin:0px 5px 5px 5px; font-style: italic; text-align: center;">%s<br>%s</p>', $aAlign, $Href, $aText);
 }

 
 static function Page($aLink, $aText, $aClass = "")
 {
	if ($aLink[0] == "#") {		
		$Href = sprintf("%s%s", $_SERVER["REQUEST_URI"], $aLink);
	}else{
		$Href = sprintf("index.php?PN=%s", $aLink);
	}							
	return THTML::Href($Href, $aText, "", $aClass);
 }

 
 static function SubPage($aLink, $aDir, $aText, $aClass = "")
 {
	$Pos1 = TStr::PosR($aLink, "/"); 
	if ($Pos1 == -1) {
		$aLink = $aDir . "/" . $aLink;
	}

	parse_str($_SERVER["QUERY_STRING"], $Arr1);
	$Arr1["PS"] = $aLink;
	unset($Arr1["Lang"]);	//??? Apache RewriteEngine=On
	$Query = urldecode(http_build_query($Arr1));

	$Href = sprintf('index.php?%s', $Query);
	return THTML::Href($Href, $aText, "", $aClass);
 }


 static function Line($aSize)
 {
	return sprintf('<hr size="%s">', $aSize);
 }


 static function Mark($aName, $aText, $aClass = "")
 {
 	$Class = ($aClass == "" ? "" : sprintf('class="%s"', $aClass));
	return sprintf('<a name="%s" style="text-decoration:none" title="%s" %s>%s</a>', $aName, $aName, $Class, $aText);
 }

 
 static function ClearBoth()
 {
	return sprintf('<div style="clear:both;"></div>');
 }

 
 static function SpanClass($aName, $aText)
 {
	return sprintf('<span class="%s">%s</span>', $aName, $aText);
 }


 static function Tag($aName, $aText)
 {
	return sprintf('<%s>%s</%s>', $aName, $aText, $aName);
 }

 
 static function Input($aName, $aText = "", $aType = "", $aClass = "")
 {
 	$Text  = ($aText  == "" ? "" : sprintf('value="%s"', $aText));
 	$Type  = ($aType  == "" ? "" : sprintf('type="%s"', $aType));
 	$Class = ($aClass == "" ? "" : sprintf('class="%s"', $aClass));
	$Result = sprintf('<input name="%s" %s %s %s>', $aName, $Text, $Type, $Class);
	return TStr::TrimInside($Result);
 }
}


class TControl
/////////////////////////////////////////////////////
{
 protected $StrName, $StrSeparator, $StrClass, $PrintStr;

 function __construct()
 {
  $this->Init();
 }

 
 function Init()
 {
  $this->StrName      = "";
  $this->StrSeparator = "";
  $this->StrClass     = "";
  $this->Clear();
 }

 
 function GetPrintOut()
 {
  return $this->PrintStr;
 }

 
 function PrintOut()
 {
  print($this->GetPrintOut());
 }

 
 function Clear()
 {
  $this->PrintStr = "";
 }

 
 function SetSeparator($aString)
 {
  $this->StrSeparator = $aString;
 }

 
 function SetName($aString)
 {
  $this->StrName = $aString;
 }

 
 function SetClass($aString)
 {
  $this->StrClass = "class=" . '"' . $aString . '"';
 }
}


class TLabel extends TControl
/////////////////////////////////////////////////////
{
 function BuildOne($aLabel)
 {
  printf("%s%s", $aLabel, $this->StrSeparator);
 }  

 
 function Build($aTArray)
 {
   $aTArray->Reset();
   while (list($Label) = $aTArray->Each()) {
       $this->BuildOne($Label);
   }
 }  
}


class TInput extends TControl
/////////////////////////////////////////////////////
{
 protected $StrType = "";

 function TInput($aName, $aType)
 {
  $this->StrName = $aName;
  $this->StrType = $aType;
 }

 
 function BuildOne($aValue)
 {
  $this->PrintStr .= sprintf('<INPUT name="%s" type="%s" value="%s">%s', $this->StrName, $this->StrType, $aValue, $this->StrSeparator);
 }

 
 function Build($aTArray)
 {
   $aTArray->Reset();
   while (list($Label) = $aTArray->Each()) {
       $this->BuildOne($Label);
   }
 }
}


class THref extends TControl
/////////////////////////////////////////////////////
{
 function BuildOne($aLabel, $aValue, $aUnSelected = "")
 {
  if ($aValue == "" || $aLabel == $aUnSelected) {
     $this->PrintStr .= sprintf('%s %s', $aLabel, $this->StrSeparator);
  }else{
     $this->PrintStr .= sprintf('<a href="%s" %s>%s</a>%s', $aValue, $this->StrClass, $aLabel, $this->StrSeparator);
  }
 }

 
 function Build($aTArray)
 {
   $aTArray->Reset();
   while (list($Label, $Value) = $aTArray->Each()) {
      $this->BuildOne($Label, $Value);
   }
 }
}


class TMenu extends TControl
/////////////////////////////////////////////////////
{
 protected $StrDisabled, $StrSelected;

 function __construct($aName)
 {
  parent::__construct();
  $this->SetName($aName);
  $this->StrDisabled = "";
  $this->StrSelected = "";
 }

 
 function Disabled($aValue)
 {
  if ($aValue) {
     $this->StrDisabled = "disabled";
  }else{
     $this->StrDisabled = "";
  }
 }

 
 function Selected($aValue)
 {
  $this->StrSelected = $aValue;
 }

 
 function Build($aTArray)
 {
  $this->PrintStr .= sprintf("\n<SELECT name='%s' %s %s >\n", $this->StrName, $this->StrDisabled, $this->StrClass);
  $aTArray->Reset();
  while (list($Label, $Value) = $aTArray->Each()) {
       if ($this->StrSelected === $Value || $this->StrSelected === $Label) {
 	       $this->PrintStr .= sprintf("  <option selected value='%s'>%s</option>\n", $Value, $Label);
       }else{
           $this->PrintStr .= sprintf("  <option value='%s'>%s</option>\n", $Value, $Label);
       }
   }
   $this->PrintStr .= sprintf(" </SELECT>\n");
 }
}



class TTable extends TControl
/////////////////////////////////////////////////////
{
 protected $Width, $Height, $StrTable, $StrTrTop, $StrTr0, $StrTr1, $ArrTd;

 
 function __construct($aWidth = 1, $aHeight = 1, $aStrTable = "") {
  $this->Width    = $aWidth;
  $this->Height   = $aHeight;
  $this->StrTable = $aStrTable;
  $this->ArrTd 	  = new TArray(); 
  $this->SetTR();
 }

 
 function SetTR($aTrTop = "", $aTr0 = "", $aTr1 = "") {
  $this->StrTrTop = $aTrTop;
  $this->StrTr0   = $aTr0;
  $this->StrTr1   = $aTr1;
 }
 
 function SetTD($aArrTd) {
  $this->ArrTd = $aArrTd;
 }
  
 
 function Build($aTArray, $aIsTD = false)
 {
  $Height = ($this->Height == -1 ? $aTArray->GetCount() / $this->Width : $this->Height);
  $this->PrintStr = sprintf("<TABLE %s>", $this->StrTable); 

  $aTArray->Reset();
  for ($Y = 0; $Y < $Height; $Y++) {
	if ($Y == 0) {
		$StrTr = $this->StrTrTop;	
	}else{
		$StrTr = ($Y % 2 == 0 ? $this->StrTr0 : $this->StrTr1);
	}
	$this->PrintStr .= sprintf(" <TR %s>", $StrTr);
	for ($X = 0; $X < $this->Width; $X++) {
		list(, $Value) = $aTArray->Each();
			if ($aIsTD) {
				$this->PrintStr .= $Value;
			}else{
				$TD = "";
				if ($this->ArrTd->GetCount() > 0 && $this->ArrTd->GetCount() >= $X) {
					$TD = $this->ArrTd[$X];
				}	
				$this->PrintStr .= sprintf("  <TD valign='top' %s>%s</TD>", $TD, (isset($Value) ? $Value : "&nbsp;")); 
			}	
      } 
      $this->PrintStr .= sprintf(" </TR>");
  } 
  $this->PrintStr .= "</TABLE>";
 }
}

?>