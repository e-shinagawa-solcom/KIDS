<!--



////////////////////////// TODAY DATE //////////////////////////
function TodayDate()
{
	document.all.demo_date.value =YYMMDD ;
	document.all.ddate.value = YYMMDD ;

	return false;
}






////////////////////////// AUTO FOCUS //////////////////////////
function autoFocus1()
{
	document.all.dtmOrderAppDate.focus();
	return false;
}

function autoFocus2()
{
	document.all.strProductCode.focus();
	return false;
}





// CRCリスト再表示・非表示
function fncReViewCRCList()
{
	var crcFlag    = document.all.crcflag.value;

	if( crcFlag == '1' )
	{
		document.all.CRCList.style.visibility = 'visible';
	}
	else
	{
		document.all.CRCList.style.visibility = 'hidden';
	}
}



////////////////////////// SHOW-HIDE SEGMENTS //////////////////////////
function ShowInputA() { //PUSH INPUT A TAB


	//-------------------------------------------------------------------------
	// v2 tomita update
	//-------------------------------------------------------------------------
	fncResetDINView( 0 );

	fncReViewCRCList();
	//-------------------------------------------------------------------------



	document.all.InputA.style.visibility = 'visible' ;
	document.all.InputB.style.visibility = 'hidden' ;
	document.all.InputC.style.visibility = 'hidden' ;

	TabB.innerHTML = objtabB1;
	TabA.innerHTML = objtabA3;



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

	document.all.CRCList.style.visibility = 'hidden';
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