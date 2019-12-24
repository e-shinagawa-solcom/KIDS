

var KidsLogoImg = '<img src="/img/type01/cmn/kids_logo_center-v3.gif" width=182" height="26" border="0" alt="Kuwagata Integrated Dealing System">';

var EnterBt1 = '<a href="#"><img name="enterbutton" onfocus="fncEnterButton( \'on\' , this );" onblur="fncEnterButton( \'off\' , this );" onmouseover="fncEnterButton( \'on\' , this );" onmouseout="fncEnterButton( \'off\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + enterbt1 + '" width="72" height="20" border="0" alt="ENTER" tabindex="1"></a>';

/*
var ReloadBt1 = '<a href="#"><img name="enterbutton" onfocus="fncReloadButton( \'on\' , this );" onblur="fncReloadButton( \'off\' , this );" onmouseover="fncReloadButton( \'on\' , this );" onmouseout="fncReloadButton( \'off\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + reloadbt1 + '" width="72" height="20" border="0" alt="RELOAD" tabindex="1"></a>';
*/

//var bodycolor = '#72828b';
var bodycolor = '#FFD700';
var TopMargin = '230';



function initLayout()
{

	Backs.style.background   = bodycolor;
	EnterSet.style.marginTop = TopMargin;

	KidsLogo.innerHTML    = KidsLogoImg;

	if( typeof(EnterButton) != 'undefiend' )
	{
		EnterButton.innerHTML = EnterBt1;
		//EnterButton.outerHTML = '<span id="EnterButton" onclick="GoLogin();fncChangeAction();return false;">' + EnterBt1 + '</span>';
		//document.all.enterbutton.focus();
	}

	return false;
}



/*
function fncChangeAction()
{
	EnterButton.outerHTML = '<span id="EnterButton" onclick="location.reload();return false;">' + ReloadBt1 + '</span>';
	return false;
}
*/