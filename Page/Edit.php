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
 global $gTLang;
// http://www.php.net/manual/en/class.ziparchive.php
 function GetHistoryFile($aDir, $aSrc, $aAction)
 {
	$DirHistory = _DirHistory . "/" . $aDir;
	TFS::MkDirs($DirHistory);
	$ArrFileInfo = TFS::GetFileInfo($aSrc);
	$FileHistory = $DirHistory . "/" . $ArrFileInfo->GetItem("FileName") . "_" .
					Date("Ymd-Hms") . "_" . $_SESSION["PN_Login_User"] . "_" .
					$aAction . "." . $ArrFileInfo->GetItem("Extension");
	$TReport1 = new TReport(_LogFile);
	$TReport1->Log($FileHistory);

	return $FileHistory;
 }


 function LoadFile($aSrc, $aDst)
 {
	$ArrFileInfo = TFS::GetFileInfo($aDst);
	$DstDirName  = $ArrFileInfo->GetItem("DirName");
	$DstFileName = $ArrFileInfo->GetItem("BaseName");

	TFS::MkDirs($DstDirName);

	if (TFS::FileExists($aDst)) {
		$FileHistory = GetHistoryFile($DstDirName, $DstFileName, "Upl");
		TFS::Copy($aDst, $FileHistory);
	}
	$Result = TFS::Rename($aSrc, $aDst);

	if (_OptimizeImage && IsPicture($aDst)) {
		$ImageEx1 = new TImageEx();
		$ImageEx1->ReCreate($aDst, $aDst, _ImageWidth);
	}
	return $Result;
 }


 function ImportFilesFromSession($aLang, $aType, $aArrTable, $aArrMenu)
 {
	for ($i = 0; $i < 8; $i++) {
	    $Str1 = ($i == 1 ? "$aType:" : "<HR>");
	    $aArrTable->AddItemToEnd($Str1);
	}

	$Name = "ParseFiles_$aType";
	$ArrFiles = new TArray($_SESSION[$Name]);
	for ($i = 0; $i < $ArrFiles->GetCount(); $i++) {
		$FullFileName = $ArrFiles->GetItem($i);
		$ArrFileInfo = TFS::GetFileInfo($FullFileName);
		$DirName  = $ArrFileInfo->GetItem("DirName");
		$FileName = $ArrFileInfo->GetItem("BaseName");

		switch($aType) {
			case "PHP":
			case "Load":
				$Link1 = sprintf('<a href="index.php?PN=Edit&Action=Edit&Dir=%s&Src=%s">%s</a>', $DirName, $FileName, $FileName);
				break;
			case "Link":
			case "Error": $Link1 = sprintf("%s", $FullFileName);
				break;
			default:  	  $Link1 = sprintf('<a href="%s">%s</a>', THTML::Encode($FullFileName), $FileName); break;

		}

		$aArrTable->AddItemToEnd($i+1);
		$aArrTable->AddItemToEnd(sprintf('<a href="%s">%s</a>', $DirName, $DirName));
		$aArrTable->AddItemToEnd(sprintf("%s", $Link1));
		$aArrTable->AddItemToEnd(GetShortSize($ArrFileInfo->GetItem("Size")));
		$aArrTable->AddItemToEnd($ArrFileInfo->GetItem("Date"));
		$aArrTable->AddItemToEnd(sprintf("<a href=\"javascript:FileDelete('%s','%s','%s')\" title=\"%s\">Del</a>",
					$DirName, $FileName, "index.php?PN=Edit&Action=Del", $aLang->GetItem("Delete")));
		$aArrTable->AddItemToEnd(sprintf("<a href=\"javascript:FileRename('%s','%s','%s')\" title=\"%s\">Ren</a>",
					$DirName, $FileName, "index.php?PN=Edit&Action=Ren", $aLang->GetItem("Rename")));
		$aArrTable->AddItemToEnd(sprintf("<a href=\"javascript:FileUpload('%s','%s','%s')\" title=\"%s\">Upl</a>",
					$DirName, $FileName, "index.php?PN=Edit&Action=Upl", $aLang->GetItem("Upload")));

		if (!$aArrMenu->KeyExists($DirName)) {
			$aArrMenu->AddItem($DirName, $DirName);
		}
	}
 }

#-----------------------------------
 if (_CMS_Allow == false) {
	$gTLang->ShowItem("Page is not allowed");
	exit;
 }

 $IP = $_SERVER["REMOTE_ADDR"];
 if (!preg_match("/" . _CMS_Hosts . "/i", $IP)) {
	printf("%s: $IP", $gTLang->ShowItem("Host is not allowed"));
	exit;
 }

 $aLoginOK = $_SESSION["PN_Login_OK"];
 if ($aLoginOK) {
	$ReadOnly = "";
	$LoginInfo = sprintf("%s: %s", $gTLang->GetItem("User"),  $_SESSION["PN_Login_User"]);

	$VerInfo = "http://jdv-soft.com/User/File/ProjSimpleSiteCreator/Version.txt";
	$VerStr = @file_get_contents($VerInfo);
	if ($VerStr !== false) {
		$IniFile = new tParseFileINI();
		$IniFile->LoadFromString($VerStr);
		$LastVer = $IniFile->GetItem("Version");
		if ($LastVer != "Version" && _VerNum < $LastVer) {
			printf("<br><b>%s: %s !</b><br>", $gTLang->GetItem("Found new version"), $IniFile->GetItem("Version"));
		}
	}
 }else{
 	$LoginInfo = sprintf('%s. (%s)<br><a href="index.php?PN=Login&Action=Init">%s</a>',
			$gTLang->GetItem("You are guest here"), $IP, $gTLang->GetItem("Register as administrator"));
	if (_CMS_Guest == false) {
		$gTLang->ShowItem("Guest is not allowed");
		exit;
	}
	$ReadOnly = "readonly='yes'";
 }

 $aURI = $_SESSION["PN_Edit_URI"];

 $aDir   = urldecode($aGet->GetItem("Dir"));
 $aSrc   = urldecode($aGet->GetItem("Src"));
 $CurFileName = "$aDir/$aSrc";
 if (!TFS::FileExists($CurFileName)) {
	printf("%s : $CurFileName", $gTLang->GetItem("File doesnt exists"));
	exit;
 }
 $ArrFileInfo = TFS::GetFileInfo($CurFileName);
 $CurFileExt  = $ArrFileInfo->GetItem("Extension");

 $ArrTable = new TArray();
 $ArrTable->AddItemToEnd("N");
 $ArrTable->AddItemToEnd($gTLang->GetItem("Folder"));
 $ArrTable->AddItemToEnd($gTLang->GetItem("File"));
 $ArrTable->AddItemToEnd($gTLang->GetItem("Size"));
 $ArrTable->AddItemToEnd($gTLang->GetItem("Date"));
 $ArrTable->AddItemToEnd("&nbsp;");
 $ArrTable->AddItemToEnd("&nbsp;");
 $ArrTable->AddItemToEnd("&nbsp;");

 $ArrMenu = new TArray();
 $ArrMenu->SetItem("$aDir",			"$aDir");
 $ArrMenu->SetItem("$aDir/Image",   "$aDir/Image");
 $ArrMenu->SetItem("$aDir/File",    "$aDir/File");

 ImportFilesFromSession($gTLang, "PHP",   $ArrTable, $ArrMenu);
 ImportFilesFromSession($gTLang, "Load",  $ArrTable, $ArrMenu);
 ImportFilesFromSession($gTLang, "Image", $ArrTable, $ArrMenu);
 ImportFilesFromSession($gTLang, "File",  $ArrTable, $ArrMenu);
 //ImportFilesFromSession($gTLang, "Link",  $ArrTable, $ArrMenu);
 ImportFilesFromSession($gTLang, "Error", $ArrTable, $ArrMenu);
 $Table1 = new TTable(8, $ArrTable->GetCount()  / 8, "border='0' cellspacing='1' cellpadding='1'");
 $Table1->Build($ArrTable);

 if ($aLoginOK) {
	$FSResult = "";
	$FSField  = "";
	$ResultFile = $CurFileName;

	if ($aAction == "Write") {
		if (TStr::Length($aPost->GetItem("_Edit_CurFile_Read"))  > 0) {
			//$Link = sprintf("index.php?PN=Edit&Action=Edit&Dir=%s&Src=%s", $aPost->GetItem("_Edit_CurFileDir"), $aPost->GetItem("_Edit_CurFileName"));
			//header("Location: $Link");
			$aDir = $aPost->GetItem("_Edit_CurFileDir");
			$aSrc = $aPost->GetItem("_Edit_CurFileName");
			$CurFileName = "$aDir/$aSrc";

			$FSResult = TFS::FileExists($CurFileName);
			$FSField  = "Read";
		}elseif (TStr::Length($aPost->GetItem("_Edit_Text"))  > 0) {
			$EditText = $aPost->GetItem("_Edit_Text");

			$TFile1 = new TFile();
			$TFile1->Open($CurFileName, "r");
			$OldText = $TFile1->Read();
			$TFile1->Close();

			$EditFileName = $aPost->GetItem("_Edit_CurFileDir") . "/" . $aPost->GetItem("_Edit_CurFileName");
			if ($EditText != $OldText || $CurFileName != $EditFileName) {
				$FileHistory = GetHistoryFile($aDir, $aSrc, $aAction);
				$TFile2 = new TFile();
				$TFile2->Open($FileHistory, "w");
				$TFile2->Write($OldText);
				$TFile2->Close();

				$aDir = $aPost->GetItem("_Edit_CurFileDir");
				$aSrc = $aPost->GetItem("_Edit_CurFileName");
				$CurFileName = "$aDir/$aSrc";
				TFS::MkDirs($aDir);

				$TFile1->Open($CurFileName, "w");
				$FSResult = $TFile1->Write($EditText);
				$TFile1->Close();
				$FSField  = "Write";

				$ArrMenu->SetItem("$aDir", "$aDir");
			}
		}
	}elseif ($aAction == "Del") {
		$FileHistory = GetHistoryFile($aDir, $aSrc, $aAction);
		TFS::Copy($CurFileName, $FileHistory);
		$FSResult = TFS::Delete($CurFileName);
		$FSField  = "Delete";
	}elseif ($aAction == "Ren") {
		$FSResult = TFS::Rename($CurFileName, $aGet->GetItem("Dir") . "/" . $aGet->GetItem("Dst"));
		$FSField  = "Rename";
	}elseif ($aAction == "Upl") {
		for ($i = 1; $i <= 3; $i++) {
			$Url = $aPost->GetItem("_Edit_UploadFileUrl_$i");
			if ($Url) {
				$FileStr = file_get_contents($Url);
				if ($FileStr !== false) {
					$TmpFile = tempnam(_DirTemp , "upl");
					$TFile2 = new TFile();
					$TFile2->Open($TmpFile, "w");
					$TFile2->Write($FileStr);
					$TFile2->Close();

					$ArrFileInfo = TFS::GetFileInfo($Url);
					$UrlFileName = $ArrFileInfo->GetItem("BaseName");

					$DirName = $aPost->GetItem("Menu_FS_1");
					$FSResult = LoadFile($TmpFile, $DirName . "/" . $UrlFileName);
				}
			}
			$FSField  = "Upload file URL";
		}

		if ($_FILES["UploadFile"]["name"] != "") {
			$Files = $_FILES["UploadFile"];
			extract($Files, EXTR_PREFIX_ALL, 'uf');
			if (is_uploaded_file($uf_tmp_name)) {
				$DirName = $aPost->GetItem("Menu_FS_1");
				$FSResult = LoadFile($uf_tmp_name, $DirName . "/" .  $Files["name"]);
			}
			$FSField  = "Upload file";

		}elseif ($_FILES["UploadArchive"]["name"] != "") {
			$Files = $_FILES["UploadArchive"];
			extract($Files, EXTR_PREFIX_ALL, 'uf');
			if (is_uploaded_file($uf_tmp_name)) {
				$DirName = $aPost->GetItem("Menu_FS_1");
				$ZIP = new ZipArchive();
				if ($ZIP->open($uf_tmp_name) === true) {
					$ZIP->extractTo($DirName);
					$ZIP->close();
					$FSResult = true;
				}
			}
			$FSField  = "Upload archive";

		}elseif ($_FILES["UploadSQL"]["name"] != "") {
			$Files = $_FILES["UploadSQL"];
			extract($Files, EXTR_PREFIX_ALL, 'uf');
			if (is_uploaded_file($uf_tmp_name)) {
				$Ext = TStr::ToLower(TStr::SubPosR($Files["name"], "."));
				if ($Ext == ".gz") {
					$FileData = "";
					$hGZ = gzopen($uf_tmp_name, "r");
					while(!gzeof($hGZ)) {
						$FileData .= gzread($hGZ, 4096);
					}
					gzclose($hGZ);
				}else{
					if($Ext == ".sql" || $Ext == ".txt") {
						$TFile1 = new TFile();
						$TFile1->Open($uf_tmp_name, "r");
						$FileData = $TFile1->Read();
						$TFile1->Close();
					}
				}
				$ResultFile = $Files["name"];

				$TSQL1 = new TMySQL(_DB_HostName, _DB_UserName, _DB_Password);
				$TSQL1->SelectDB(_DB_DataBase);
				$FSResult = $TSQL1->QueryText($FileData);
				$FSField  = "Upload SQL file";
			}
		}
	}

	if ($FSField != "") {
		printf("<b>%s: %s - %s<br>%s: %s<br></b>",
			$gTLang->GetItem("Action"), $gTLang->GetItem($FSField), (empty($FSResult) ? $gTLang->GetItem("Error") : "OK"),
			$gTLang->GetItem("Path"), $ResultFile);
	}
 }

 $TFile1 = new TFile();
 $TFile1->Open($CurFileName, "r");
 if ($CurFileExt != "txt" && !$aLoginOK) {
	$TextArea = $gTLang->GetItem("You have to be Administrator to see this file");
 }elseif (empty($aSrc)) {
	$TextArea = $gTLang->GetItem("No source file");
 }else{
	$TextArea = $TFile1->Read();

	$ArrFileInfo = TFS::GetFileInfo($CurFileName);
	$HistFName = $ArrFileInfo->GetItem("FileName");
	$HistDName = _DirHistory . "/" . $aDir;
	$Dir1 = new TDir($HistDName);
	$ArrFiles = $Dir1->GetFiles(false, 1, $FileName);
	if ($ArrFiles->GetCount() > 0) {
		$FilterHistory = "$HistDName?P=" . $ArrFileInfo->GetItem("FileName") . "*";
		$LinkHistory = sprintf('<a href="%s">%s</a>', $FilterHistory, $gTLang->GetItem("History"));
	}
 }

 $Menu1 = new TMenu("Menu_FS_1");
 $Menu1->Selected($aDir);
 //$ArrMenu->SortByValue();
 $Menu1->Build($ArrMenu);

 if ($aLoginOK) {
    $TDir2 = new TDir($aDir);
    $ArrFiles2 = $TDir2->GetFiles(false, 1);
	$DataFiles2 = $ArrFiles2->PadEx(TArray::cRight, "\n")->GetContext(_SM_Value_BR);
 }
?>
<script type="text/javascript">
 function IsGuest()
 {
	var LoginOK = <?php print($aLoginOK ? "true" :"false"); ?>;
	var Message = "<?php $gTLang->ShowItem("Guest is not allowed"); ?>";
	if (!LoginOK) {
		alert(Message);
	}
	return LoginOK;
 }
</script>

<table width="100%"  border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td align="left" valign="top">&nbsp;</td>
  </tr>
  <tr>
    <td align="left"><table width="100%" border="1" cellspacing="0" cellpadding="0">
      <tr>
        <td width="72%" align="left" valign="top">
		<table width="100%" border="1" cellspacing="0" cellpadding="0">
          <tr>
            <td colspan="2"><h2><?php print($LoginInfo); ?></h2></td>
          </tr>
          <tr>
    		 <td colspan="2" align="left" valign="top"><?php printf('%s:<a href="%s">%s</a><br>', $gTLang->GetItem("Home"), $aURI, $aURI); ?></td>
  		  </tr>
          <tr>
            <td colspan="2" align="left"><?php print($aDir); ?></td>
          </tr>
		  <tr>
            <td width="82%"><?php  $Table1->PrintOut(); ?></td>
            <td width="18%" align="left" valign="top"><?php Show($DataFiles2); ?></td>
		  </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2">
		     <form name="FFS" method="post" action="<?php print("index.php?PN=Edit&Action=Upl&Dir=$aDir&Src=$aSrc"); ?>" enctype="multipart/form-data">
			   <table width="100%" border="1" cellspacing="0" cellpadding="0">
				<tr>
				  <td rowspan="3" align="left"><?php $gTLang->ShowItem("Upload file URL"); ?></td>
				  <td width="38%" rowspan="5" align="left" valign="top"><?php $Menu1->PrintOut(); ?></td>
				  <td align="left"><input name="_Edit_UploadFileUrl_1" type="text" size="60" <?php print($ReadOnly); ?> />
				  1</td>
				  <td width="3%" rowspan="5" align="left"><input name="Url_OK" type="submit" value="OK" class="Submit1" onclick="IsGuest()" /></td>
				</tr>
				<tr>
				  <td align="left"><input name="_Edit_UploadFileUrl_2" type="text" size="60" <?php print($ReadOnly); ?> />
				    2</td>
				  </tr>
				<tr>
				  <td align="left"><input name="_Edit_UploadFileUrl_3" type="text" size="60" <?php print($ReadOnly); ?> />
				    3</td>
  			  </tr>
				<tr>
					<td width="10%" align="left"><?php $gTLang->ShowItem("Upload file"); ?></td>
					<td width="49%" align="left"><input name="UploadFile" type="file" id="UploadFile" size="60"></td>
				</tr>
				<tr>
					<td align="left"><?php $gTLang->ShowItem("Upload archive"); ?></td>
					<td align="left"><input name="UploadArchive" type="file" id="UploadArchive" size="60"></td>
				</tr>
				<tr>
					<td align="left"><?php $gTLang->ShowItem("Upload SQL script"); ?></td>
					<td align="left">&nbsp;</td>
					<td align="left"><input name="UploadSQL" type="file" id="UploadSQL" size="60"></td>
				</tr>
               </table>
			</form>		   </td>
          </tr>
          <tr>
            <td colspan="2">&nbsp;</td>
          </tr>
        </table></td>
        </tr>
    </table></td>
  </tr>
  <tr>
    <td align="left">&nbsp;</td>
  </tr>
  <tr>
    <td align="left">
     <form name="FEdit" method="post" action="<?php print("index.php?PN=Edit&Action=Write&Dir=$aDir&Src=$aSrc"); ?>">
		<table width="100%" border="0" cellspacing="0" cellpadding="0">
  			<tr>
    			<td align="left" class="TextBold"><table width="100%" border="0" cellpadding="0" cellspacing="0">
                  <tr>
                    <td height="24"><?php print($LinkHistory); ?></td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td width="61%" height="24"><input name="_Edit_CurFileDir" type="text" value="<?php print($aDir); ?>" <?php print($ReadOnly); ?> size="50">&nbsp;<input name="_Edit_CurFileName" type="text" value="<?php print($aSrc); ?>" <?php print($ReadOnly); ?>></td>
                    <td width="39%"><input name="_Edit_CurFile_Write" type="submit" value="<?php $gTLang->ShowItem("Write"); ?>" class="Submit1" onclick="IsGuest()">&nbsp;
                    <input name="_Edit_CurFile_Read" type="submit" value="<?php $gTLang->ShowItem("Read"); ?>" class="Submit1" onclick="IsGuest()"></td>
                  </tr>
                </table></td>
		  	</tr>
      		<tr>
       		  <td align="left"><?php printf('<textarea name="_Edit_Text" cols="105" rows="40" wrap="%s" %s>%s</textarea>', _CMS_WrapEdit, $ReadOnly, $TextArea); ?></td>
      		</tr>
    	</table>
     </form>	</td>
  </tr>
</table>
