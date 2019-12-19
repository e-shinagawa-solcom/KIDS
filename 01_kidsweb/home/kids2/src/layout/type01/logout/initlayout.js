<!--


var KidsLogoImg = '<img src="/img/type01/cmn/kids_logo_center_gold.gif" width=182" height="26" border="0" alt="Kuwagata Integrated Dealing System">';


var LogoutBt1 = '<a href="#"><img name="logoutbutton" onfocus="fncDarkLogoutButton( \'on\' , this );" onblur="fncDarkLogoutButton( \'off\' , this );" onmouseover="fncDarkLogoutButton( \'on\' , this );" onmouseout="fncDarkLogoutButton( \'off\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + darklogout1 + '" width="72" height="20" border="0" alt="LOGOUT" tabindex="2"></a>';

var BackBt1 = '<a href="#"><img name="backbutton" onfocus="fncDarkBackButton( \'on\' , this );" onblur="fncDarkBackButton( \'off\' , this );" onmouseover="fncDarkBackButton( \'on\' , this );" onmouseout="fncDarkBackButton( \'off\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + darkback1 + '" width="72" height="20" border="0" alt="BACK" tabindex="1"></a>';



//var bodycolor = '#72828b';
var bodycolor = '#FFD700';
var TopMargin = '230';



function initLayout()
{

	Backs.style.background    = bodycolor;
	LogoutSet.style.marginTop = TopMargin;

	KidsLogo.innerHTML =KidsLogoImg;


	if( typeof(LogoutButton) != 'undefiend' )
	{
		LogoutButton.innerHTML = LogoutBt1;
		BackButton.innerHTML   = BackBt1;
		//document.all.logoutbutton.focus();
	}

	return false;
}


/*
window.document.onkeydown=fncEnterKeyDown;

function fncEnterKeyDown( e )
{
	if( window.event.keyCode == 13 ||
		window.event.keyCode == 14 )
	{
		fncAlphaOn( document.all.enterbutton );

		window.frmLogout.submit();
	}
}
*/

//-->