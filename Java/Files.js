function FileDelete(aDir, aFileName, aGoLink)
//-------------------------------
{
 if (confirm("Delete file " + aFileName + "?")) {
	document.location = aGoLink + "&Dir=" + aDir + "&Src=" + aFileName;
 }	
}


function FileRename(aDir, aFileName, aGoLink)
//-------------------------------
{
 var NewFileName = prompt("Rename file " + aFileName + "?", aFileName);
 if (NewFileName != null) {
	if (aFileName != NewFileName) {
		document.location = aGoLink + "&Dir=" + aDir + "&Src=" + aFileName + "&Dst=" + NewFileName;
	}else{
		//alert("Files match");
	}
 }		
}


function FileUpload(aDir, aFileName, aGoLink)
//-------------------------------
{
 // http://www.tigir.com/javascript_select.htm
 document.FFS.UploadFile.click();
 //document.FFS.Menu_FS_1.options[2].selected=true;
 document.FFS.Menu_FS_1.value=aDir;
 //document.FFS.UploadFile.value="111";
} 


function GoToPage(aUrl) 
//-------------------------------
{
 document.location.href = aUrl;
 return true;
}
