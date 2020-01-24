<!--


//-----------------------------------------------------------------------------------------
// 解説 : ロゴイメージ定義
//-----------------------------------------------------------------------------------------
var pictKidsLogo = '<img src="/img/staff/kids_logo_staff.gif" width=182" height=38" border="0">';



//-----------------------------------------------------------------------------------------
// 解説 : ユーザーイメージ定義
//-----------------------------------------------------------------------------------------
var imgSaito    = '<img src="/img/staff/pict_saito.gif" width="63" height="58" border="0">';
var imgSuzukaze = '<img src="/img/staff/pict_suzukaze.gif" width="63" height="58" border="0">';
var imgWatanabe = '<img src="/img/staff/pict_watanabe.gif" width="63" height="58" border="0">';
var imgChiba    = '<img src="/img/staff/pict_chiba.gif" width="63" height="58" border="0">';
var imgTetsuka  = '<img src="/img/staff/pict_tetsuka.gif" width="63" height="58" border="0">';
var imgTomita1   = '<a href="#" onclick="fncBeepOn();return false;"><img src="/img/staff/pict_tomita.gif" width="63" height="58" border="0"></a>';
var imgTomita2   = '<img src="/img/staff/pict_tomita2.gif" width="63" height="58" border="0">';



//-----------------------------------------------------------------------------------------
// 解説 : イメージ書出し処理関数
//-----------------------------------------------------------------------------------------
function fncStaff()
{

	// ロゴイメージ書出し
	pictLogo.innerHTML = pictKidsLogo;

	// ユーザーイメージ書出し
	pictSaito.innerHTML    = imgSaito;
	pictSuzukaze.innerHTML = imgSuzukaze;
	pictWatanabe.innerHTML = imgWatanabe;
	pictChiba.innerHTML    = imgChiba;
	pictTetsuka.innerHTML  = imgTetsuka;
	pictTomita.innerHTML   = imgTomita1;

	return false;

}


//-->