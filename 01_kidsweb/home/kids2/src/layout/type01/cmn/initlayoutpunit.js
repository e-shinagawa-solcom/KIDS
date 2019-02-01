<!--


///// PACKING UNIT BUTTON IMAGE /////
var punitbt1 = '<a href="javascript:void(0);"><img onfocus="PunitOn(this);" onblur="PunitOff(this);" onmouseover="PunitOn(this);" onmouseout="PunitOff(this);fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + punit1 + '" width="19" height="19" border="0" tabindex="' + NumPunitTab + '"></a>';

var punitbt3 = '<a href="javascript:void(0);"><img src="' + punit3 + '" width="19" height="19" border="0" tabindex="' + NumPunitTab + '"></a>';



function initLayoutPunit()
{
	if (typeof(PunitBt)!='undefined')
	{
		///// PACKING UNIT BUTTON IMAGE /////
		PunitBt.innerHTML = punitbt1;
	}
	return false;
}


//-->