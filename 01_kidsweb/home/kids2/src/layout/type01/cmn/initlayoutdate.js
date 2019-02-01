<!--


///// DATE BT IMAGE /////
var datebuttonA = '<a href="javascript:void(0);"><img id="DateAImg" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="DateOn(this);" onblur="DateOff(this);" onmouseover="DateOn(this);" onmouseout="DateOff(this);fncAlphaOff( this );" src="' + datebt1 + '" width="19" height="19" border="0" alt="DATE" tabindex="' + NumDateTabA + '"></a>';

var datebuttonA3 = '<a href="javascript:void(0);"><img id="DateAImg" src="' + datebt3 + '" width="19" height="19" border="0" alt="DATE" tabindex="' + NumDateTabA + '"></a>';


var datebuttonB = '<a href="javascript:void(0);"><img id="DateBImg" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="DateOn(this);" onblur="DateOff(this);" onmouseover="DateOn(this);" onmouseout="DateOff(this);fncAlphaOff( this );" src="' + datebt1 + '" width="19" height="19" border="0" alt="DATE" tabindex="' + NumDateTabB + '"></a>';

var datebuttonB3 = '<a href="javascript:void(0);"><img id="DateBImg" src="' + datebt3 + '" width="19" height="19" border="0" alt="DATE" tabindex="' + NumDateTabB + '"></a>';


var datebuttonC = '<a href="javascript:void(0);"><img id="DateCImg" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="DateOn(this);" onblur="DateOff(this);" onmouseover="DateOn(this);" onmouseout="DateOff(this);fncAlphaOff( this );" src="' + datebt1 + '" width="19" height="19" border="0" alt="DATE" tabindex="' + NumDateTabC + '"></a>';

var datebuttonC3 = '<a href="javascript:void(0);"><img id="DateCImg" src="' + datebt3 + '" width="19" height="19" border="0" alt="DATE" tabindex="' + NumDateTabC + '"></a>';


function initLayoutDate()
{
	//// OBJECT JUDGE /////
	if (typeof(DateBtA)!='undefined')
	{
		DateBtA.innerHTML = datebuttonA;
	}

	if (typeof(DateBtB)!='undefined')
	{
		DateBtB.innerHTML = datebuttonB;
	}

	if (typeof(DateBtC)!='undefined')
	{
		DateBtC.innerHTML = datebuttonC;
	}

	return false;
}


//-->