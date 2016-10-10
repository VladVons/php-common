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


class TImage
/////////////////////////////////////////////////////
//require 'GD2' lib
{
 protected $Handle;

 function __construct($aHandle = NULL) {
  $this->Handle = $aHandle;
 }

 
 function __destruct() {
  $this->Destroy();
 }
 
 
 function GetHandle()
 {
  return $this->Handle;
 }
 
 function ImgJpeg($aFileName = "", $aQuality = 75)
 {
  return imagejpeg($this->Handle, $aFileName, $aQuality);
 }

 
 function ImgPng($aFileName = "", $aQuality = 7)
 {
  return imagepng($this->Handle, $aFileName, $aQuality);
 }


 function CreateFromFile($aPath)
 {
	$this->Destroy();

	if (!TFS::FileExists($aPath)) {
		throw new MyException("File doesn`t exists: '$aPath'", 1);
	}
 	
	if (preg_match("/(.jpg$|.jpeg$)/i", $aPath)) {
		$this->Handle = @imagecreatefromjpeg($aPath);
	}elseif (preg_match("/.png$/i", $aPath)) {
		$this->Handle = @imagecreatefrompng($aPath);
	}elseif (preg_match("/.gif$/i", $aPath)) {
		$this->Handle = @imagecreatefromgif($aPath);
	}elseif (preg_match("/.bmp$/i", $aPath)) {
		$this->Handle = @imagecreatefrombmp($aPath);
	}else{
		throw new MyException("Unknown image extension in '$aPath'");
	}

	if (!$this->Handle) {
     throw new MyException("Can`t create object from image: '$aPath'", 1);
	}
 }

 
 function Create($aWidth, $aHeight)
 {
  $this->Destroy();
  $this->Handle = $dst_img = imageCreateTrueColor($aWidth, $aHeight); 
  if (!$this->Handle) {
     throw new MyException("Can`t create image: $aWidth x $aHeight", 1);
  }
 }


 function Destroy()
 {
  if ($this->Handle) {
     imagedestroy($this->Handle);
     $this->Handle = NULL;
  }
 }


 function GetHeight()
 {
  return imagesy($this->Handle);
 }


 function GetWidth()
 {
  return imagesx($this->Handle);
 }


 function SaveToFile($aPath, $aQuality = 75)
 {
  if (preg_match("/(.jpg$|.jpeg$)/i", $aPath)) {
	return $this->ImgJpeg($aPath, $aQuality); //$aQuality 0-100
  }elseif (preg_match("/png$/i", $aPath)) {
	return $this->ImgPng($aPath, intval($aQuality / 10)); //$aQuality 0-9
  }else{
	throw new MyException("Unknown image extension in $aPath");
  }
 }

 
 function CopyResized($aTImageOut, $aDstX, $aDstY, $aSrcX, $aSrcY, $aDstW, $aDstH, $aSrcW, $aSrcH)
 {
  imagecopyresized($aTImageOut->Handle, $this->Handle, $aDstX, $aDstY, $aSrcX, $aSrcY, $aDstW, $aDstH, $aSrcW, $aSrcH);
 }
 

 function CopyResampled($aTImageOut, $aDstX, $aDstY, $aSrcX, $aSrcY, $aDstW, $aDstH, $aSrcW, $aSrcH)
 {
  imagecopyresampled($aTImageOut->Handle, $this->Handle, $aDstX, $aDstY, $aSrcX, $aSrcY, $aDstW, $aDstH, $aSrcW, $aSrcH);
 }


 function ColorAllocate($aRed, $aGreen, $aBlue)
 {
  return imagecolorclosest($this->Handle, $aRed, $aGreen, $aBlue);
 }


 function String($aFont, $aX, $aY, $aString, $aColor)
 {
  return imagestring($this->Handle, $aFont, $aX, $aY, $aString, $aColor);
 }
 

 function FilledRectangle($aX1, $aY1, $aX2, $aY2, $aColor)
 {
  return imagefilledrectangle($this->Handle, $aX1, $aY1, $aX2, $aY2, $aColor);
 }


 function Line($aX1, $aY1, $aX2, $aY2, $aColor)
 {
  return imageline($this->Handle, $aX1, $aY1, $aX2, $aY2, $aColor);
 }
}



class TImageEx extends TImage
/////////////////////////////////////////////////////
{

 function __construct($aHandle = NULL) {
  $this->Handle = $aHandle;
 }


 function Scale($aScale)
 {
  $Height = $this->GetHeight();
  $Width  = $this->GetWidth();

  $NewHeight = (int) abs($Height * $aScale);
  $NewWidth  = (int) abs($Width  * $aScale);

  $TImageOut = new TImageEx(); 
  $TImageOut->Create($NewWidth, $NewHeight);
  $this->CopyResampled($TImageOut, 0, 0, 0, 0, $NewWidth, $NewHeight, $Width, $Height);

  return $TImageOut;
 }


 function ScaleTo($aMaxSize)
 {
  $Height = $this->GetHeight();
  $Width  = $this->GetWidth();

  $NewScale = $aMaxSize / max($Height, $Width);
  return $this->Scale($NewScale);
 }


 function ScaleToFile($aMaxSize, $aFileName, $aQuality)
 {
  $TImageOut = $this->ScaleTo($aMaxSize);
  return $TImageOut->SaveToFile($aFileName, $aQuality);
 }


 function ReCreate($aPathIn, $aPathOut, $aMaxSize, $aString = "")
 {
  $TImage_1 = new TImageEx();
  $TImage_1->CreateFromFile($aPathIn);

  if ($aString != "") {
     $Color = $TImage_1->ColorAllocate(200, 200, 200);
     $TImage_1->String(4, 5, 5, $aString, $Color);
  }

  $Width   = $TImage_1->GetWidth();
  $Height  = $TImage_1->GetHeight();

  $MaxSize = max($Width, $Height);
  if ($MaxSize > $aMaxSize) {
     $MaxSize = $aMaxSize;
  }

  $ComprRate = ($Width * $Height) / filesize($aPathIn);
  if ($ComprRate < 9 || $MaxSize == $aMaxSize) {
     return $TImage_1->ScaleToFile($MaxSize, $aPathOut, 65);
  }else{
     return copy($aPathIn, $aPathOut);
  }
 }
}


class TMpegFrame
/////////////////////////////////////////////////////
//require 'ffmpeg' lib
{
 protected $Image;

 function __construct($aHandle = NULL) {
  $this->Handle = $aHandle;
 }

 function Create($aHandle)
 {
  $this->Handle = $aHandle;
 }
 
 function GetWidth()
 {
  return $this->Handle->getWidth();
 }

 function GetHeight()
 {
  return $this->Handle->getHeight();
 }

 function GetImage()
 {
  return new TImageEx($this->Handle->toGDImage());
 }
 
 function GetTime()
 {
  return $this->Handle->getPTS();
 }
}


class TMpeg
/////////////////////////////////////////////////////
//require 'ffmpeg' lib
{
 protected $Image;

 function __construct($aHandle = NULL) {
  $this->Handle = $aHandle;
 }

 function Create($aPath)
 {
  $this->Handle = new ffmpeg_movie($aPath, false);
  if (!$this->Handle) {
     throw new MyException("Can`t create image: $aPath", 1);
   }
 }

 function GetDuration()
 {
  return $this->Handle->getDuration();
 }

 function GetFrameCount()
 {
  return $this->Handle->getFrameCount();
 }
 
 function GetFrameWidth()
 {
  return $this->Handle->getFrameWidth();
 }

 function GetFrameHeight()
 {
  return $this->Handle->getFrameHeight();
 }
 
 function GetFrame($aIdx)
 {
  return new TMpegFrame($this->Handle->getFrame($aIdx));
 }
 
 function CreateThumb($aFileOut, $aScale)
 {
  return $this->GetFrame(1)->GetImage()->ScaleToFile($aScale, $aFileOut, 75);
 }
}
?>