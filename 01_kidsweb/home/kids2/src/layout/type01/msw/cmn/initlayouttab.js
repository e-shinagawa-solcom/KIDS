<!--


var objTabA1 = '<a href="javascript:void(0);"><img name="Dtab" onfocus="DtabOn(this);" onblur="DtabOff(this);" onmouseover="DtabOn(this);" onmouseout="DtabOff(this);" src="' + taba1 + '" width="20" height="178" border="0" alt="DEPT" tabindex="10"></a>';
var objTabA3 = '<img src="' + taba3 + '" width="20" height="178" border="0" alt="DEPT">';

var objTabB1 = '<a href="javascript:void(0);"><img name="Itab" onfocus="ItabOn(this);" onblur="ItabOff(this);" onmouseover="ItabOn(this);" onmouseout="ItabOff(this);" src="' + tabb1 + '" width="20" height="178" border="0" alt="IN CHARGE NAME" tabindex="5"></a>';
var objTabB3 = '<img src="' + tabb3 + '" width="20" height="178" border="0" alt="IN CHARGE NAME">';



var ViobjTabA1 = '<a href="javascript:void(0);"><img name="viDtab" onfocus="viDtabOn(this);" onblur="viDtabOff(this);" onmouseover="viDtabOn(this);" onmouseout="viDtabOff(this);" src="' + vitaba1 + '" width="20" height="178" border="0" alt="VENDOR" tabindex="10"></a>';
var ViobjTabA3 = '<img src="' + vitaba3 + '" width="20" height="178" border="0" alt="VENDOR">';

var ViobjTabB1 = '<a href="javascript:void(0);"><img name="viItab" onfocus="viItabOn(this);" onblur="viItabOff(this);" onmouseover="viItabOn(this);" onmouseout="viItabOff(this);" src="' + vitabb1 + '" width="20" height="178" border="0" alt="IN CHARGE NAME" tabindex="5"></a>';
var ViobjTabB3 = '<img src="' + vitabb3 + '" width="20" height="178" border="0" alt="IN CHARGE NAME">';




function initLayoutTab()
{
	if( typeof(TabD) != 'undefined' )
	{
		TabD.innerHTML = objTabA3;
	}

	if( typeof(TabI) != 'undefined' )
	{
		TabI.innerHTML = objTabB1;
	}

	if( typeof(viTabD) != 'undefined' )
	{
		viTabD.innerHTML = ViobjTabA3;
	}

	if( typeof(viTabI) != 'undefined' )
	{
		viTabI.innerHTML = ViobjTabB1;
	}
}


//-->