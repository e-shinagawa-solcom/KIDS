<!--


var KidsLogoImg = '<img src="/img/type01/cmn/kids_logo_left_v3.gif" width=182" height="26" border="0" alt="Kuwagata Integrated Dealing System">';
var KidsLogoImgCenter = '<img src="/img/type01/cmn/kids_logo_center.gif" width=182" height="26" border="0" alt="Kuwagata Integrated Dealing System">';

var submitBt1 = '<a href="#"><img name="submitbutton" onfocus="fncSubmitButton( \'on\' , this );" onblur="fncSubmitButton( \'off\' , this );" onmouseover="fncSubmitButton( \'on\' , this );" onmouseout="fncSubmitButton( \'off\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + submit1 + '" width="72" height="20" border="0" alt="LOGIN" tabindex="2"></a>';

var darkcloseBtJ1 = '<a href="#"><img onmouseover="DarkCloseJOn(this);" onmouseout="DarkCloseJOff(this);fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + darkclose1J + '" width="72" height="20" border="0" alt="ÊÄ¤¸¤ë"></a>';

var darkcloseBtE1 = '<a href="#"><img onmouseover="DarkCloseEOn_Gold(this);" onmouseout="DarkCloseEOff_Gold(this);fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + darkclose1E_Gold + '" width="72" height="20" border="0" alt="CLOSE"></a>';



///// CSS VALUE /////
//var fcolor = '#ffffff'; //¹àÌÜ¥Õ¥©¥ó¥È¥«¥é¡¼
//var segcolor = '#72828b'; //¹àÌÜÇØ·Ê¿§ e8f0f1
var fcolor = '#000000'; //¹àÌÜ¥Õ¥©¥ó¥È¥«¥é¡¼
var segcolor = '#FFD700'; //¹àÌÜÇØ·Ê¿§ e8f0f1

var segbody = '#d6d0b1'; //INPUT A BODY ÇØ·Ê¿§

//var brcolor01 = '#cdcdcd #72828b #cdcdcd #cdcdcd'; //¹àÌÜ±¦¶õ¤­BORDER 798787
var brcolor01 = '#000000 #000000 #000000 #000000'; //¹àÌÜ±¦¶õ¤­BORDER 798787
//var brcolor02 = '#cdcdcd #cdcdcd #cdcdcd #72828b'; //¹àÌÜº¸¶õ¤­BORDER
var brcolor02 = '#000000 #000000 #000000 #000000'; //¹àÌÜº¸¶õ¤­BORDER

var brcolor03 = '#798787 #e8f0f1 #798787 #e8f0f1'; //¹àÌÜº¸±¦¶õ¤­BORDER

//var bodycolor = '#72828b';
var bodycolor = '#FFD700';


function initLayout()
{

	Backs.style.background = bodycolor;

	if( typeof(KidsLogo) != 'undefined' )
	{
		KidsLogo.innerHTML =KidsLogoImg;
	}

	if( typeof(KidsLogo2) != 'undefined' )
	{
		KidsLogo2.innerHTML =KidsLogoImg;
	}

	if (typeof(SubmitButton)!='undefined')
	{
		SubmitButton.innerHTML = submitBt1;
	}

	if (typeof(CloseButton)!='undefined')
	{
		CloseButton.innerHTML = darkcloseBtE1;
	}

	if (typeof(CloseButton2)!='undefined')
	{
		CloseButton2.innerHTML = darkcloseBtE1;
	}

	if (typeof(CloseButton3)!='undefined')
	{
		CloseButton3.innerHTML = darkcloseBtE1;
	}

	if (typeof(strEmail)!='undefined')
	{
		strEmail.style.color = fcolor;
		strEmail.style.borderColor = brcolor01;
		strEmail.style.background = segcolor;

		strEmailVars.style.color = fcolor;
		strEmailVars.style.borderColor = brcolor02;
		strEmailVars.style.background = segcolor;

		document.all.strMailAddress.focus();
	}


	return false;
}





//-->