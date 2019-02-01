<!--


//---------------------------------------------------
// 解説 : タイマー作動後のイメージ入れ替えモジュール
//---------------------------------------------------
function fncModuleChangePict()
{
	pictTomita.innerHTML = imgTomita1;
}


//---------------------------------------------------
// 解説 : タイマー設定モジュール
//---------------------------------------------------
function fncModuleSetTimer( lngTime )
{
	setTimeout( 'fncModuleChangePict()' , lngTime );
}


//---------------------------------------------------
// 解説 : タイマー呼出関数
//---------------------------------------------------
function fncBeepOn()
{

	// サウンド再生
	BeepSound.src='/error/lupin.wav';

	// イメージ入れ替え
	pictTomita.innerHTML = imgTomita2;

	// タイマー起動
	fncModuleSetTimer( 2800 );

	return false;
}


//-->