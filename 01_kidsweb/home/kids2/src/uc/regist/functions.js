<!--

////////// OPEN-CLOSE //////////
var countClick=0;

function ShowGoodsPlan(obj1)
{
	if (countClick==0)
	{
		GoodsPlan.style.visibility = 'visible';
		obj1.innerHTML = showGPbt3;
		countClick++;
		//document.all.revise.focus();
	}
	else if (countClick==1)
	{
		GoodsPlan.style.visibility = 'hidden';
		obj1.innerHTML = showGPbt1;
		countClick=0;
	}
	return false;
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
















////////////////////////// SHOW-HIDE SEGMENTS //////////////////////////
function ShowInputA() //PUSH INPUT A TAB
{
	document.all.InputA.style.visibility = 'visible' ;
	document.all.InputB.style.visibility = 'hidden' ;

	PTabA.innerHTML = objPtabA3;
	PTabB.innerHTML = objPtabB1;


	///// ERROR ID /////
	var ErrID1 = ErrInputA.children;
	var ErrID2 = ErrInputB.children;

	for (i = 0 ; i < ErrID1.length ; i++)
	{
		if ( ErrID1[i].style.visibility == 'hidden' && parseInt(ErrID1[i].style.width) > 0 )
		{
			ErrID1[i].style.visibility = 'visible' ;
		}
	}

	for (i = 0 ; i < ErrID2.length ; i++)
	{
		if ( ErrID2[i].style.visibility == 'visible' && parseInt(ErrID2[i].style.width) > 0 )
		{
			ErrID2[i].style.visibility = 'hidden' ;
		}
	}


	return false;
}



function ShowInputB() //PUSH INPUT B TAB
{
	document.all.InputA.style.visibility = 'hidden' ;
	document.all.InputB.style.visibility = 'visible' ;

	PTabA.innerHTML = objPtabA1;
	PTabB.innerHTML = objPtabB3;


	///// ERROR ID /////
	var ErrID1 = ErrInputA.children;
	var ErrID2 = ErrInputB.children;

	for (i = 0 ; i < ErrID1.length ; i++)
	{
		//alert(ErrID1.length + '/' + i);
		if ( ErrID1[i].style.visibility == 'visible' && parseInt(ErrID1[i].style.width) > 0 )
		{
			ErrID1[i].style.visibility = 'hidden' ;
		}
		//alert(parseInt(ErrID1[i].style.width));
	}

	for (i = 0 ; i < ErrID2.length ; i++)
	{
		//alert(ErrID2.length + '/' + i);
		if ( ErrID2[i].style.visibility == 'hidden' && parseInt(ErrID2[i].style.width) > 0 )
		{
			ErrID2[i].style.visibility = 'visible' ;
		}
		//alert(parseInt(ErrID2[i].style.width));
	}



	///// GOODS PLAN WINDOW /////
	GoodsPlan.style.visibility = 'hidden';

	if (countClick == 1)
	{
		ShowGoodsPlan(document.all.GoodsPlanButton);
	}

	return false;
}












////////////////////////// CLEAR VARS //////////////////////////
function VarsAClear() {
	document.all.PPP1.reset();

	ErrMeg.style.visibility = 'hidden' ;
	ERmark.style.visibility = 'hidden' ;

	return false;
}

function VarsBClear() {
	document.all.PPP2.reset();

	ErrMeg.style.visibility = 'hidden' ;
	ERmark.style.visibility = 'hidden' ;

	return false;
}










//-->