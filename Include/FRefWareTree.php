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
global $gTLang, $gTRefWare;

//print_r($gTRefWare);
$TArrayJS = new TArray();
$TArrayJS->AddItemToEnd(sprintf("d = new dTree('d', '%s');", _DirCommonJava . "/dTree/"));
$TArrayJS->AddItemToEnd(sprintf("d.add(0, -1, '%s');", $gTLang->GetItem("Catalog")));
$TArrayTN = $gTRefWare->TreeNode("d.add(%d, %d, '%s', 'index.php?PN=Shop&Group=%s');");
$TArrayJS = $TArrayJS->Merge($TArrayTN);
$TArrayJS->AddItemToEnd("document.write(d);");
?>

<table width="100%"  border="0" cellpadding="0" cellspacing="0">
 <tr>
  <td class="TableColor_Title"><?php $gTLang->ShowItem("Shop"); ?></td>
 </tr>
 <tr>
  <td>&nbsp;</td>
 </tr>
 <tr>
  <td align="left" valign="top">
   <form method="post" action="Actions.php?Action=SearchItem">
     <table width="100%"  border="0" cellpadding="0" cellspacing="0">
      <tr>
        <td align="center"><input type="text" name="_Search_Text_Chk"></td>
      </tr>
      <tr>
        <td align="center">
		  <select name="_Search_Menu">
		    <option value="Name"><?php $gTLang->ShowItem("Name"); ?></option>
		    <option value="Details"><?php $gTLang->ShowItem("Details"); ?></option>
		    <option value="ID"><?php $gTLang->ShowItem("ID"); ?></option>
		    <option value="Manufacture"><?php $gTLang->ShowItem("Manufacture"); ?></option>
		  </select>
		</td>
      </tr>
      <tr>
        <td align="center"><input type="submit" name="submit" value="<?php $gTLang->ShowItem("Search"); ?>"></td>
      </tr>
    </table>
   </form>
  </td>
 </tr>
 <tr>
  <td align="left" valign="top">
   <link rel="stylesheet" href="<?php print(_DirCommonJava . "/dTree/dtree.css"); ?>" type="text/css">
   <script type="text/javascript" src="<?php print(_DirCommonJava . "/dTree/dtree.js"); ?>"></script>
   <script type="text/javascript">
     <!--
     <?php print($TArrayJS->PadEx(TArray::cRight, "\n")->GetContext(TArray::cRight)); ?>
	 //-->
    </script>
  </td>
 </tr>
</table>
