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

class TError
{
 protected static $MyThis;
 protected $EMailTo, $ErrFile, $ErrLevel, $PrevErrHanler;
 
 function Init($aErrFile, $aEMailTo, $aErrLevel)
 {
  $this->ErrFile  = $aErrFile;
  $this->EMailTo  = $aEMailTo;
  $this->ErrLevel = $aErrLevel;

  TError::$MyThis = $this; 
  $this->PrevErrHanler = set_error_handler("TError::CallBackHandler");
 }
 
 static function CallBackHandler($aErrNo, $aErrMsg, $aFileName, $aLineNum, $aVars) 
 {
	TError::$MyThis->Handler($aErrNo, $aErrMsg, $aFileName, $aLineNum, $aVars); 
 }
 
 function Handler($aErrNo, $aErrMsg, $aFileName, $aLineNum, $aVars) 
 {
  if (! ($aErrNo & $this->ErrLevel) ) return;

 $ErrType = array (
     E_ERROR              => 'Error',
     E_WARNING            => 'Warning',
     E_PARSE              => 'Parsing Error',
     E_NOTICE             => 'Notice',
     E_CORE_ERROR         => 'Core Error',
     E_CORE_WARNING       => 'Core Warning',
     E_COMPILE_ERROR      => 'Compile Error',
     E_COMPILE_WARNING    => 'Compile Warning',
     E_USER_ERROR         => 'User Error',
     E_USER_WARNING       => 'User Warning',
     E_USER_NOTICE        => 'User Notice',
	 E_STRICT             => 'Runtime Notice',
     E_RECOVERABLE_ERROR  => 'Catchable Fatal Error'
     );
 
  $ErrStr = "Date    : " . date("Y-m-d H:i:s") . "\r\n" .
	        "ErrType : " . $ErrType[$aErrNo] . "\r\n" .
	        "ErrMsg  : " . $aErrMsg . "\r\n" .
	        "File    : " . $aFileName . "\r\n" .
		    "Line    : " . $aLineNum . "\r\n\r\n";
  error_log($ErrStr, 3, $this->ErrFile);
  //var_dump(debug_backtrace());
  //mail($this->EMailTo, "PHP Error at " . $_SERVER["SERVER_NAME"],  $ErrStr);
  }
}
?>