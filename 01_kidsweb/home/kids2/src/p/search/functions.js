<!--


///// RESET CHECKBOX COUNT /////
function CheckResetCnt()
{
	window.Pwin.CheckAll1.innerHTML = offBt;
	window.Pwin.checkcount1 = 0;

	window.Pwin.CheckAll2.innerHTML = offBt;
	window.Pwin.checkcount2 = 0;
}







////////////////////////// AUTO FOCUS //////////////////////////
function autoFocus1()
{
	//document.all.strProductName.focus();
	return false;
}

function autoFocus2()
{
	//document.all.lngFactoryCode.focus();
	return false;
}




///////// KICK PWIN ///////////
function KickPwin()
{
	window.Pwin.ChgEtoJ();

	return false;
}





////////////////////////// ENTER KEY //////////////////////////
/*
currentFNo = 0;
function nextForm()
{
	if (event.keyCode == 13)
	{
		currentFNo++;
		currentFNo %= document.PPP1.elements.length;
		document.PPP1[currentFNo].focus();
	}
}
window.document.onkeydown = nextForm;
*/


//-->