<!--


///// TAB IMAGE /////
var objtabA1 = '<a href="javascript:void(0);" onclick="ShowInputA();autoFocus1();"><img onfocus="TabAOn(this);" onblur="TabAOff(this);" onmouseover="TabAOn(this);" onmouseout="TabAOff(this);" src="' + tabA1 + '" width="24" height="272" border="0" alt="HEADER"></a>';
var objtabA3 = '<img src="' + tabA3 + '" width="24" height="272" border="0" alt="HEADER" tabindex="' + TabNumA + '">';
var objtabB1 = '<a href="javascript:void(0);" onclick="ShowInputB();autoFocus2();window.DLwin.fncDtHtml();"><img onfocus="TabBOn(this);" onblur="TabBOff(this);" onmouseover="TabBOn(this);" onmouseout="TabBOff(this);" src="' + tabB1 + '" width="24" height="272" border="0" alt="DETAIL" tabindex="' + TabNumB + '"></a>';
var objtabB3 = '<img src="' + tabB3 + '" width="24" height="272" border="0" alt="DETAIL">';


if( typeof(PTabNumA) != 'undefined' ||  typeof(PTabNumB) != 'undefined' )
{
	///// [PRODUCTS] TAB IMAGE /////
	var objPtabA1 = '<a href="javascript:void(0);" onclick="ShowInputA();autoFocus1();"><img onfocus="PTabAOn(this);" onblur="PTabAOff(this);fncDefaultTabindex( document.all.lngFactoryCode );" onmouseover="PTabAOn(this);" onmouseout="PTabAOff(this);" src="' + ptabA1 + '" width="24" height="272" border="0" alt="REGISTRATION A" tabindex="' + PTabNumA + '"></a>';
	var objPtabA3 = '<img src="' + ptabA3 + '" width="24" height="272" border="0" alt="REGISTRATION A">';
	var objPtabB1 = '<a href="javascript:void(0);" onclick="ShowInputB();autoFocus2();"><img onfocus="PTabBOn(this);" onblur="PTabBOff(this);fncDefaultTabindex( document.all.strProductName );" onmouseover="PTabBOn(this);" onmouseout="PTabBOff(this);" src="' + ptabB1 + '" width="24" height="272" border="0" alt="REGISTRATION B" tabindex="' + PTabNumB + '"></a>';
	var objPtabB3 = '<img src="' + ptabB3 + '" width="24" height="272" border="0" alt="REGISTRATION B">';
}



function initLayoutTab()
{

	//// OBJECT JUDGE /////
	if (typeof(TabA)!='undefined')
	{
		TabA.innerHTML = objtabA3;
	}

	if (typeof(TabB)!='undefined')
	{
		TabB.innerHTML = objtabB1;
	}

	if (typeof(PTabA)!='undefined')
	{
		PTabA.innerHTML = objPtabA3;
	}

	if (typeof(PTabB)!='undefined')
	{
		PTabB.innerHTML = objPtabB1;
	}

	return false;
}


//-->