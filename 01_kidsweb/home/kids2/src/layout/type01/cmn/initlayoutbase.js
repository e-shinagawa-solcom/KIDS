


//--------------------------------------------------------
// 特殊文字ボタン
//--------------------------------------------------------
var specialButton1 = '<a href="#"><img onmouseover="fncSpecialButton( \'on\' , this );" onmouseout="fncSpecialButton( \'off\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + specialbt1 + '" width="19" height="19" border="0" alt="EDIT" tabindex=""></a>';
var specialButton3 = '<a href="#"><img src="' + specialbt3 + '" width="19" height="19" border="0" alt="EDIT"></a>';


///// MAIN MENU BUTTON /////
var mainmenuButton1 = '<a href="#"><img onmouseover="MainmenuOn(this);" onmouseout="MainmenuOff(this);fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + mainmenubt1 + '" width="82" height="24" border="0" alt="MAIN MENU" tabindex="-1"></a>';



///// HELP BUTTON /////
var helpButton1 = '<a href="#"><img onmouseover="HelpOn(this);" onmouseout="HelpOff(this);fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + helpbt1 + '" width="19" height="19" border="0" alt="ONLINE HELP" tabindex="-1"></a>';

var helpButton3 = '<img src="' + helpbt3 + '" width="19" height="19" border="0" alt="ONLINE HELP" tabindex="-1">';







///// NAVIGATION /////
var naviButton1 = '<a href="#"><img onclick="NaviOn( this );  fncNaviVisible( Navigations , NaviButton ); return false;" src="' + navibt1 + '" width="18" height="515" border="0" tabindex="-1"></a>';
var naviButton3 = '<a href="#"><img onclick="NaviOff( this ); fncNaviVisible( Navigations , NaviButton ); return false;" src="' + navibt3 + '" width="18" height="515" border="0" tabindex="-1"></a>';







///// OFF ON BUTTON /////
var offBt = '<a href="#"><img onmouseover="OnBt(this);" onmouseout="OffBt(this);" src="' + off + '" width="19" height="19" border="0" tabindex="-1"></a>';
var onBt = '<a href="#"><img src="' + on + '" width="19" height="19" border="0" tabindex="-1"></a>';


///// LAYOUT GRID IMAGE /////
var LayoutGridImg = '<img src="' + gridimg10 + '" width="996" height="689" border="0">';

///// BASE HEADER,FOOTER IMAGE /////
var headerImg = '<img src="' + headimage + '" width="980" height="86" border="0">';
var footerImg = '<img src="' + footimage + '" width="980" height="35" border="0">';


///// LOGOUT BT IMAGE /////
var logoutbt = '<a href="#"><img onmouseover="LogoutOn(this);" onmouseout="LogoutOff(this);fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + logout1 + '" width="82" height="24" border="0" alt="LOGOUT" tabindex="-1"></a>';




///// E TO J BT IMAGE /////
//var etojEbt = '<a href="#" onclick="ChgEtoJ();"><img onmouseover="EtoJ1EOn(this);" onmouseout="EtoJ1EOff(this);fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + etojE1 + '" width="82" height="24" border="0" alt="ENGLISH" tabindex="-1"></a>';
//var etojJbt = '<a href="#" onclick="ChgEtoJ();"><img onmouseover="EtoJ1JOn(this);" onmouseout="EtoJ1JOff(this);fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + etojJ1 + '" width="82" height="24" border="0" alt="JAPANESE" tabindex="-1"></a>';





///// LOGOUT BT IMAGE /////
var pictureSpace = '<img src="' + pictspace + '" width="68" height="68" border="0">';






///// INPUT A,B,C BOTTOM IMAGE /////
var bottom01 = '<img src="' + bottoms1 + '" width="927" height="12" border="0">';
var bottom02 = '<img src="' + bottoms2 + '" width="790" height="12" border="0">';
var bottom03 = '<img src="' + bottoms3 + '" width="949" height="12" border="0">';


///// LAYOUT GRID CSS STYLE /////
var GridVisible = 'hidden';


///// BASE CSS STYLE /////
var baseback = '#72828b'; //BASE BACK COLOR
var bcolor = 'transparent'; //BODY COLOR


function initLayoutBase()
{
	if (typeof(Header)!='undefined')
	{
		Header.innerHTML = headerImg;
	}

	if (typeof(Footer)!='undefined')
	{
		Footer.innerHTML = footerImg;
	}

	if (typeof(LogOutBt)!='undefined')
	{
		LogOutBt.innerHTML = logoutbt;
	}

	if (typeof(EtoJ)!='undefined')
	{
		EtoJ.innerHTML = etojEbt;
	}

	if (typeof(BaseBack)!='undefined')
	{
		BaseBack.style.background = baseback;
	}

	if (typeof(Bodys)!='undefined')
	{
		Bodys.style.background = bcolor;
	}

	if (typeof(NaviButton)!='undefined')
	{
		NaviButton.innerHTML = naviButton1;
	}

	if (typeof(MainMenuButton)!='undefined')
	{
		MainMenuButton.innerHTML = mainmenuButton1;
	}

	if (typeof(PictSpaceBase)!='undefined')
	{
		PictSpaceBase.innerHTML = pictureSpace;
	}

	if( typeof(HelpBt) != 'undefined' )
	{
		HelpBt.innerHTML = helpButton1;
	}

	if( typeof(SpecialBt) != 'undefined' )
	{
		SpecialBt.innerHTML = specialButton1;
	}

	if (typeof(LayoutGrid)!='undefined')
	{
		LayoutGrid.innerHTML = LayoutGridImg;
		LayoutGrid.style.visibility = GridVisible;
	}

	return false;
}



//-->