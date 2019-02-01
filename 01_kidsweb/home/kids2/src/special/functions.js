
//------------------------------------------------------------
// 解説 : [TD]背景色変更関数
//------------------------------------------------------------
function fncTdColorChange( strMode , obj )
{

	var defaultcolor = '#0b509f'; // デフォルト色 /* 6d8aab */
	var overcolor    = '#6d8aab'; // ロールオーバー色
	var downcolor    = '#ea8555'; // マウスダウン色

	switch( strMode )
	{
		case 'off':
			obj.style.background = defaultcolor;
			break;

		case 'on':
			obj.style.background = overcolor;
			break;

		case 'down':
			obj.style.background = downcolor;
			break;

		default:
			break;
	}

	return false;
}



//------------------------------------------------------------
// 解説 : [特殊文字]をクリップボードにコピーする関数
//
// ﾊﾟﾗﾒｰﾀｰ : strSpecialChar   , 特殊文字取得引数
//           objSpecialBuffer , バッファ用オブジェクト(hidden)
//------------------------------------------------------------
function fncSpecialCharCopy( strSpecialChar )
{
	// 特殊文字をバッファに格納
	objSpecialBuffer.value = strSpecialChar;

	// バッファのテキストレンジを取得
	var objSpecial = objSpecialBuffer.createTextRange();

	// コピーする領域の設定
	objSpecial.moveStart( 'character' , 0 );
	objSpecial.moveEnd( 'character' );

	//alert(objSpecial);

	// クリップボードにコピー
	objSpecial.execCommand("copy");

	// バッファの初期化
	objSpecialBuffer.value = '';

	return false;
}