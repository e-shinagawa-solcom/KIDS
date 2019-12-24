

var KidsLogoImg = '<img src="/img/type01/cmn/kids_logo_center-v3.gif" width=182" height="26" border="0" alt="Kuwagata Integrated Dealing System">';


var loginBt1 = '<a href="#"><img name="loginbutton" onfocus="LoginOn(this);" onblur="LoginOff(this);" onmouseover="LoginOn(this);" onmouseout="LoginOff(this);fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + login1 + '" width="82" height="24" border="0" alt="LOGIN" tabindex="3"></a>';

var darkcloseBtJ1 = '<a href="#"><img onmouseover="DarkCloseJOn(this);" onmouseout="DarkCloseJOff(this);fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + darkclose1J + '" width="72" height="20" border="0" alt=" ƒ§∏§Î"></a>';

var darkcloseBtE1 = '<a href="#"><img onmouseover="DarkCloseEOn_Gold(this);" onmouseout="DarkCloseEOff_Gold(this);fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + darkclose1E_Gold + '" width="72" height="20" border="0" alt="CLOSE"></a>';


var ralowBt1 ='<a href="#"><img onmouseover="fncDarkRAllowButton( \'on\' , this );" onmouseout="fncDarkRAllowButton( \'off\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + ralowbt1 + '" width="19" height="19" border="0"></a>';


///// CSS VALUE /////
var fcolor = '#000000'; //π‡Ã‹•’•©•Û•»•´•È°º
//var segcolor = '#72828b'; //π‡Ã‹«ÿ∑ øß e8f0f1
var segcolor = '#FFD700'; //π‡Ã‹«ÿ∑ øß gold
var segbody = '#d6d0b1'; //INPUT A BODY «ÿ∑ øß
//var brcolor01 = '#cdcdcd #72828b #cdcdcd #cdcdcd'; //π‡Ã‹±¶∂ı§≠BORDER 798787
//var brcolor02 = '#cdcdcd #cdcdcd #cdcdcd #72828b'; //π‡Ã‹∫∏∂ı§≠BORDER
var brcolor03 = '#798787 #e8f0f1 #798787 #e8f0f1'; //π‡Ã‹∫∏±¶∂ı§≠BORDER
var brcolor01 = '#cdcdcd #FFD700 #cdcdcd #cdcdcd'; //π‡Ã‹±¶∂ı§≠BORDER 798787
var brcolor02 = '#cdcdcd #cdcdcd #cdcdcd #FFD700'; //π‡Ã‹∫∏∂ı§≠BORDER

//var bodycolor = '#72828b';
var bodycolor = '#FFD700';


function initLayoutLogin()
{
	KidsLogo.innerHTML =KidsLogoImg;
	Backs.style.background = bodycolor;

	if (typeof(LoginButton)!='undefined')
	{
		LoginButton.innerHTML = loginBt1;
	}

	if (typeof(CloseButton)!='undefined')
	{
		CloseButton.innerHTML = darkcloseBtE1;
	}

	if (typeof(rAllowButton)!='undefined')
	{
		rAllowButton.innerHTML = ralowBt1;
	}

	sUID.style.color = fcolor;
	sUID.style.borderColor = brcolor01;
	sUID.style.background = segcolor;

	sPASSWD.style.color = fcolor;
	sPASSWD.style.borderColor = brcolor01;
	sPASSWD.style.background = segcolor;

	sUIDVars.style.color = fcolor;
	sUIDVars.style.borderColor = brcolor02;
	sUIDVars.style.background = segcolor;

	sPASSWDVars.style.color = fcolor;
	sPASSWDVars.style.borderColor = brcolor02;
	sPASSWDVars.style.background = segcolor;

	strReminder.borderColor = brcolor01;
	strReminder.style.background = segcolor;

	rAllowButton.style.borderColor = '#cdcdcd';
	rAllowButton.style.background = segcolor;


	document.all.strUserID.focus();

	return false;
}