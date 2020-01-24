

//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : キーイベント押下時の処理関数
*
* @event [onkeydown] : window.document
*/
//--------------------------------------------------------------------------------------------------------------------
window.document.onkeydown=fncEnterKeyDown;

function fncEnterKeyDown( e )
{
	if( window.event.keyCode == 13 || window.event.keyCode == 14 )
	{
		// ボタンのアルファ値変更
		fncAlphaOn( document.all.enterbutton );

		// ログイン画面へ
		GoLogin();
	}
}


//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : ENTERボタンオブジェクトの処理関数
*
* 対象 : ボタンオブジェクト
*
* @param [strMode] : [文字列]         . 処理モード文字列
* @param [obj]     : [オブジェクト型] . オブジェクト名
*
* @event [onmouseover] : 対象オブジェクト
* @event [onmouseout]  : 対象オブジェクト
*/
//--------------------------------------------------------------------------------------------------------------------
function fncEnterButton( strMode , obj )
{
	switch( strMode )
	{
		case 'off':
			obj.src = enterbt1;
			break;

		case 'on':
			obj.src = enterbt2;
			break;

		default:
			break;
	}
}


//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : マウスダウン時にオブジェクトのアルファ値を変更する
*
* 対象 : ボタンオブジェクト
*
* @param [objName] : [オブジェクト型] . オブジェクト名
*
* @event [onmousedown] : 対象オブジェクト
*/
//--------------------------------------------------------------------------------------------------------------------
function fncAlphaOn( obj )
{
	obj.style.filter = 'alpha(opacity=50)' ;
}


//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : マウスアップ時にオブジェクトのアルファ値を変更する
*
* 対象 : ボタンオブジェクト
*
* @param [objName] : [オブジェクト型] . オブジェクト名
*
* @event [onmouseup] : 対象オブジェクト
*/
//--------------------------------------------------------------------------------------------------------------------
function fncAlphaOff( obj )
{
	obj.style.filter = 'alpha(opacity=100)' ;
}