<?php
/*------------------------------------.
Simple Site Creator PHP script.
.
Copyright (C) 2006-2009 Volodymyr Vons, VladVons@mail.ru.
Copyright (C) 2009 JDV-Soft Inc, http://www.jdv-soft.com.
.
Donations: http://jdv-soft.com?PN=Donation.php.
Support:   http://jdv-soft.com?PN=ProjSimpleSiteCreator.
.
This program is free software and distributed in the hope that it will be useful,.
but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or .
FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details..
.
You can redistribute it and/or modify it under the terms of the GNU General Public License .
as published by the Free Software Foundation; either version 2 of the License, or.
(at your option) any later version..
------------------------------------*/
global $gTLang;

$Array1 = new TArray($_COOKIE);
?>
<SCRIPT LANGUAGE="JavaScript" src="<?php print(_DirCommonJava . "/Check.js"); ?>"></SCRIPT>
<SCRIPT LANGUAGE="JavaScript">
<!--
function CheckForm(aForm)
{
 var CheckEMailMsg = "<?php $gTLang->ShowItem("CheckEMailMsg"); ?>";
 var CheckMinLengthMsg = "<?php $gTLang->ShowItem("CheckMinLengthMsg"); ?>";
 var CheckSpamWordsMsg = "<?php $gTLang->ShowItem("CheckSpamWordsMsg"); ?>";
 var CheckMinLengthValue = "<?php $gTLang->ShowItem("SysCheckMinLengthValue", 4); ?>";

 var Items  = new Array("_MailUs_EMail_Chk", "_MailUs_FullName_Chk");
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

<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><?php $gTLang->ShowItem("PageTextTop"); ?></td>
  </tr>
  <tr>
    <td height="49">&nbsp;</td>
  </tr>
  <tr>
    <td><form name="form1" method="post" action="Includes/Actions.php?Action=PriceListSubscribe&PN=<?php print($DefPageName); ?>">
        <table width="578" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td colspan="3" class="TableColor_Title"><?php $gTLang->ShowItem("Subscribe EMail"); ?></td>
          </tr>
          <tr>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
          </tr>
          <tr>
            <td align="left"><?php $gTLang->ShowItem("EMail"); ?></td>
            <td align="left">*</td>
            <td align="left"><input type="text" name="_MailUs_EMail_Chk" value="<?php $Array1->ShowItem("_MailUs_EMail_Chk"); ?>"></td>
          </tr>
          <tr>
            <td width="102" align="left"><?php $gTLang->ShowItem("Full name"); ?></td>
            <td width="12" align="left">*</td>
            <td width="464" align="left"><input type="text" name="_MailUs_FullName_Chk" value="<?php $Array1->ShowItem("_MailUs_FullName_Chk"); ?>"></td>
          </tr>
          <tr>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
            <td align="center">&nbsp;</td>
          </tr>
          <tr align="left" valign="middle" >
            <td><?php $gTLang->ShowItem("Confirmation code"); ?></td>
            <td>*</td>
            <td><?php
	     $TNoSpam1 = new TNoSpam(); 
		 $TNoSpam1->ShowImageSend("Includes/Actions.php?Action=NoSpam&Mode=Dig");
        ?></td>
          </tr>
          <tr>
            <td align="left">&nbsp;</td>
            <td align="left">&nbsp;</td>
            <td align="left">&nbsp;</td>
          </tr>
          <tr>
            <td align="left"><?php $gTLang->ShowItem("Subscribe"); ?></td>
            <td align="left">&nbsp;</td>
            <td align="left"><input name="_Subscribe_Action_1" type="radio" value="1" checked></td>
          </tr>
          <tr>
            <td align="left"><?php $gTLang->ShowItem("Unsubscribe"); ?></td>
            <td align="left">&nbsp;</td>
            <td align="left"><input name="_Subscribe_Action_1" type="radio" value="0"></td>
          </tr>
          <tr>
            <td colspan="3" align="center"><input name="button" type="button" value="OK" onClick="CheckForm(this.form)"></td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
          </tr>
        </table>
      </form></td>
  </tr>
</table>
