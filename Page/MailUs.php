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

$aCookie  = new TArray($_COOKIE);
?>

<form name="FormMailUs" method="post" action="<?php print("Actions.php?Action=$aAction&PN=$CurPN"); ?>">
  <SCRIPT LANGUAGE="JavaScript" src="<?php InclFile(_DirCommonJava . "/Check.js"); ?>"></SCRIPT>
  <SCRIPT LANGUAGE="JavaScript">
<!--
function CheckMailUsForm(aForm)
{
 //aForm.submit(); return 1;

 var CheckEMailMsg = "<?php $gTLang->ShowItem("eMail format is wrong"); ?>";
 var CheckMinLengthMsg = "<?php $gTLang->ShowItem("String length is wrong"); ?>";
 var CheckSpamWordsMsg = "<?php $gTLang->ShowItem("These words are in ban list"); ?>";
 var CheckMinLengthValue = "<?php $gTLang->ShowItem("SysCheckMinLengthValue", 4); ?>";

 var Items  = new Array("_MailUs_FullName_Chk", "_MailUs_Phone_Chk", "_MailUs_EMail_Chk", "_MailUs_Address_Chk", "_MailUs_Subject_Chk");
 var Words  = new Array("href", "http", "porn");
 var Result = TextCheckLength(aForm, Items, CheckMinLengthValue, CheckMinLengthMsg) && 
              TextIsEmail(aForm, "_MailUs_EMail_Chk", CheckEMailMsg) && 
			  TextCheckFilter(aForm, Items, Words, CheckSpamWordsMsg);
 if (Result) {
    aForm.submit();
 }
 return Result;
}
//-->
</SCRIPT>
  <table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr align="left" >
      <th colspan="3" align="center"><?php $gTLang->ShowItem("Contact information"); ?></th>
    </tr>
    <tr >
      <th colspan="3">&nbsp;</th>
    </tr>
    <tr align="left">
      <td width="20%"><?php $gTLang->ShowItem("Full name"); ?></td>
      <td width="1%">*</td>
      <td width="79%"><input name="_MailUs_FullName_Chk" type="text" value="<?php $aCookie->ShowItem("_MailUs_FullName_Chk"); ?>"></td>
    </tr>
    <tr align="left">
      <td><?php $gTLang->ShowItem("Company name"); ?></td>
      <td>&nbsp;</td>
      <td><input name="_MailUs_CompanyName" type="text" value="<?php $aCookie->ShowItem("_MailUs_CompanyName"); ?>"></td>
    </tr>
    <tr align="left">
      <td><?php $gTLang->ShowItem("Phone"); ?></td>
      <td>*</td>
      <td><input name="_MailUs_Phone_Chk" type="text" value="<?php $aCookie->ShowItem("_MailUs_Phone_Chk"); ?>"></td>
    </tr>
    <tr align="left">
      <td><?php $gTLang->ShowItem("eMail"); ?></td>
      <td>*</td>
      <td><input name="_MailUs_EMail_Chk" type="text" value="<?php $aCookie->ShowItem("_MailUs_EMail_Chk"); ?>"></td>
    </tr>
    <tr align="left">
      <td><?php $gTLang->ShowItem("Address"); ?></td>
      <td>*</td>
      <td><input name="_MailUs_Address_Chk" type="text" value="<?php $aCookie->ShowItem("_MailUs_Address_Chk"); ?>"></td>
    </tr>
    <tr align="left">
      <td><?php $gTLang->ShowItem("Product name"); ?></td>
      <td>*</td>
      <td><input name="_MailUs_Subject_Chk" type="text" value="<?php $aCookie->ShowItem("_MailUs_Subject_Chk"); ?>"></td>
    </tr>
    <tr align="left">
      <td><?php $gTLang->ShowItem("Message"); ?></td>
      <td>&nbsp;</td>
      <td><textarea name="_MailUs_Message_Chk" cols="35" rows="10"><?php $aCookie->ShowItem("_MailUs_Message_Chk"); ?></textarea></td>
    </tr>
    <tr align="left">
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr align="left" valign="middle" >
      <td><?php $gTLang->ShowItem("Confirmation code"); ?></td>
      <td>*</td>
      <td><?php
	     $TNoSpam1 = new TNoSpam(); 
		 $TNoSpam1->ShowImageSend("Actions.php?Action=NoSpam&Mode=Dig");
        ?></td>
    </tr>
    <tr align="left">
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td>&nbsp;</td>
    </tr>
    <tr align="left">
      <td>&nbsp;</td>
      <td>&nbsp;</td>
      <td><input name="button" type="button" value="OK" class="Submit1" onclick="CheckMailUsForm(this.form)"></td>
    </tr>
  </table>
</form>
