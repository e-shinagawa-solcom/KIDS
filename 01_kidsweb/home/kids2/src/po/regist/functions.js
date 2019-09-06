<!--

/*
var WinCnt = 0;

function fncOpenWFRoot()
{
	if( WinCnt == 0 )
	{
		WFRootFrame.style.visibility = 'visible';
		WFrootBt.innerHTML = darkgrayOpenBt3;
		WinCnt = 1;
	}
	else if( WinCnt == 1 )
	{
		WFRootFrame.style.visibility = 'hidden';
		WFrootBt.innerHTML = darkgrayOpenBt1;
		WinCnt = 0;
	}

	return false;
}

function fncRedirectOpen()
{
	if( WinCnt == 1 )
	{
		fncOpenWFRoot();
	}
}
*/


////////// OPEN-CLOSE //////////
var countClick=0;

function ShowTaxStatus(obj1)
{
	if (countClick==0)
	{
		TaxStatus.style.visibility = 'visible';
		obj1.innerHTML = showTaxbt3;
		countClick++;
		//document.all.revise.focus();
	}
	else if (countClick==1)
	{
		TaxStatus.style.visibility = 'hidden';
		obj1.innerHTML = showTaxbt1;
		countClick=0;
	}
	return false;
}







////////////////////////// AUTO FOCUS //////////////////////////
function autoFocus1() {
  document.all.dtmExpirationDate.focus();
}

function autoFocus2() {
  document.all.strProductCode.focus();
}












////////////////////////// SHOW-HIDE SEGMENTS //////////////////////////
function ShowInputA() { //PUSH INPUT A TAB


	//-------------------------------------------------------------------------
	// v2 tomita update
	//-------------------------------------------------------------------------
	fncResetDINView( 0 );
	//-------------------------------------------------------------------------



	document.all.InputA.style.visibility = 'visible' ;
	document.all.InputB.style.visibility = 'hidden' ;
	document.all.InputC.style.visibility = 'hidden' ;

	TabB.innerHTML = objtabB1;
	TabA.innerHTML = objtabA3;



	///// TAX WINDOW /////
	TaxStatus.style.visibility = 'hidden';

	if (countClick == 1)
	{
		ShowTaxStatus(document.all.TaxBt);
	}



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


function ShowInputB() { //PUSH INPUT B TAB


	//-------------------------------------------------------------------------
	// v2 tomita update
	//-------------------------------------------------------------------------
	fncResetDINView( 1 );
	//-------------------------------------------------------------------------



	document.all.InputA.style.visibility = 'hidden' ;
	document.all.InputB.style.visibility = 'visible' ;
	document.all.InputC.style.visibility = 'visible' ;

	TabB.innerHTML = objtabB3;
	TabA.innerHTML = objtabA1;



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



	return false;
}







////////////////////////// PACKING UNIT WINDOW OPEN //////////////////////////
function GoPUNIT() {
pw = window.open('/punit/preload.html', 'pWin','width=500,height=500,status=yes,scrollbars=yes,directories=no,menubar=no,resizable=yes,location=no,toolbar=no,left=0,top=0');

return false;
}












////////////////////////// KICK DETAIL LIST WINDOW (Exchange) //////////////////////////
function KickDLwin() {
	window.DLwin.ExchangeStrDL();
	return false;
}










////////////////////////// CLEAR VARS //////////////////////////
function VarsAClear() {
	document.all.HSO.reset();

	ErrMeg.style.visibility = 'hidden' ;
	ERmark.style.visibility = 'hidden' ;

	return false;
}

function VarsBClear() {
	document.all.DSO.reset();

	ErrMeg.style.visibility = 'hidden' ;
	ERmark.style.visibility = 'hidden' ;

	return false;
}







//-->