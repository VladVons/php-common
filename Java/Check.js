<!--
function ObjAlert(aObj, aMessage)
//-------------------------------
{
 alert(aMessage);
 aObj.focus();
}

function FormSelectUnique(aForm)
//-------------------------------
{
 for (var i = 0; i < aForm.length; i++) {
    var Type = aForm[i].type.toLowerCase();
    if (Type.indexOf("select") != -1) {
       SelectUnique(aForm, i, 3);
    }
 }
}

function SelectUnique(aForm, aIdx, aIdxMax)
//-------------------------------
{
 var Obj        = aForm[aIdx];
 var NameSplit  = Obj.name.split('_'); // name format is is _Table_Field_Idx;

 if (Obj.selectedIndex > 0 && NameSplit.length == 4) {
    for (var i = 1; i <= aIdxMax; i++) {
        if (parseInt(NameSplit[3]) != i) {
           var NewNameIdx = "_" + NameSplit[1] + "_" + NameSplit[2] + "_" + i;
           var NewObj     = aForm[NewNameIdx];
           if (NewObj && NewObj[NewObj.selectedIndex].text == Obj[Obj.selectedIndex].text) {
              NewObj.selectedIndex = 0;
           }
        } 
    }
 }
}

function SelectValidByIndex(aForm, aName, aIdx)
//-------------------------------
{
 var Obj = aForm.elements[aName];
 if (Obj.selectedIndex == aIdx) {
     ObjAlert(Obj, Obj[Obj.selectedIndex].text);
     return false;
 }else{
    return true;
 }
}

function TextCheckLength(aForm, aArray, aMinLength, aErrMsg)
//-------------------------------
{
 for (var i = 0; i < aArray.length; i++) {
     var Obj = aForm.elements[aArray[i]];
     if (Obj && Obj.value.length <= aMinLength) {
        ObjAlert(Obj, aErrMsg + " - " + aMinLength);
        return false;
     }
 }
 return true;
}


function TextCheckFilter(aForm, aArray, aArrayWords, aErrMsg)
//-------------------------------
{
 for (var i = 0; i < aArray.length; i++) {
     var Obj = aForm.elements[aArray[i]];
     if (Obj) {
        var String1 = Obj.value.toLowerCase();
        for (var x = 0; x < aArrayWords.length; x++) {
            if (String1.indexOf(aArrayWords[x].toLowerCase(), 0) > 0) {
               ObjAlert(Obj, aErrMsg + " - " + aArrayWords[x]);
               return false;
            }
        }
     }
 }
 return true;
}


function TextCompare(aForm, aName1, aName2, aErrMsg)
//-------------------------------
{
 var Obj = aForm.elements[aName1];
 var Result = (Obj.value == aForm.elements[aName2].value);
 if (!Result) {
    ObjAlert(Obj, aErrMsg);
 }
 return Result;
}


function TextIsEmail(aForm, aName, aErrMsg)
//-------------------------------
{
 var Result = false;
 
 var Obj = aForm.elements[aName];
 if (Obj) {
    var Pos1 = Obj.value.indexOf("@");
    var Pos2 = Obj.value.lastIndexOf(".");
    if (Pos1 > 0 && Pos2 > Pos1 + 1) {
        Result = true;
    }else{
        ObjAlert(Obj, aErrMsg);
    }
 }
 return Result;
}
//-->
