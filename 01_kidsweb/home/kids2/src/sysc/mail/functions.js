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
	document.all.demo_date.focus();
	return false;
}

function autoFocus2()
{
	document.all.gcode.focus();
	return false;
}










////////////////////////// SHOW-HIDE SEGMENTS //////////////////////////
function ShowInputA() { //PUSH INPUT A TAB

	document.all.InputA.style.visibility = 'visible' ;
	document.all.InputB.style.visibility = 'hidden' ;
	document.all.InputC.style.visibility = 'hidden' ;

	TabB.innerHTML = objtabB1;
	TabA.innerHTML = objtabA3;

	return false;
}


function ShowInputB() { //PUSH INPUT B TAB

	document.all.InputA.style.visibility = 'hidden' ;
	document.all.InputB.style.visibility = 'visible' ;
	document.all.InputC.style.visibility = 'visible' ;

	TabB.innerHTML = objtabB3;
	TabA.innerHTML = objtabA1;

	return false;
}
















////////////////////////// PACKING UNIT WINDOW OPEN //////////////////////////
function GoPUNIT() {
pw = window.open('/punit/preload.html', 'pWin','width=500,height=500,status=yes,scrollbars=yes,directories=no,menubar=no,resizable=yes,location=no,toolbar=no,left=0,top=0');

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