
//-------------------------------------------------------
// 解説    : 特定フレーム内コンテンツの印刷関数
// ﾊﾟﾗﾒｰﾀｰ : objName ,  印刷対象フレーム名( parent.obj )
//-------------------------------------------------------
function fncPrintFrame( objName, nextUrl )
{
	objName.focus();
	objName.print();

	if (nextUrl != "") {		
		parent.report.location.href = nextUrl;
	}
	return false;
}



//-------------------------------------------------------
// 適用：「PRINT」ボタン
//-------------------------------------------------------
var printbt1 = '/img/type01/list/print_off_bt.gif';
var printbt2 = '/img/type01/list/print_off_on_bt.gif';

function fncPrintButton( strMode , obj )
{
	switch( strMode )
	{
		case 'off':
			obj.src = printbt1;
			break;

		case 'on':
			obj.src = printbt2;
			break;

		default:
			break;
	}

	return false;
}

//-----------------------------------------------------------
// 解説 : マウスダウン時にオブジェクトのアルファ値を変更する
//-----------------------------------------------------------
function fncAlphaOn( obj )
{
	obj.style.filter = 'alpha(opacity=50)' ;
}

//-----------------------------------------------------------
// 解説 : マウスアップ時にオブジェクトのアルファ値を変更する
//-----------------------------------------------------------
function fncAlphaOff( obj )
{
	obj.style.filter = 'alpha(opacity=100)' ;
}
