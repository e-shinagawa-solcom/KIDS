
//----------------------------------------------------------------------
// 解説 : ローカル変数定義
//----------------------------------------------------------------------
// [閉じる]ボタン定義
var closeBtJ = '<a href="#"><img name="querybt" onfocus="CloseJOn( this );" onblur="CloseJOff( this );" onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0" tabindex="1"></a>';
var closeBtE = '<a href="#"><img name="querybt" onfocus="CloseEOn( this );" onblur="CloseEOff( this );" onmouseover="CloseEOn( this );" onmouseout="CloseEOff( this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_en_bt.gif" width="72" height="20" border="0" tabindex="1"></a>';

// [戻る]ボタン定義
var backBtJ = '<a href="#"><img name="querybt" onfocus="GrayBackJOn( this );" onblur="GrayBackJOff( this );" onmouseover="GrayBackJOn( this );" onmouseout="GrayBackJOff( this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="/img/type01/cmn/querybt/back_gray_off_ja_bt.gif" width="72" height="20" border="0" tabindex="1"></a>';
var backBtE = '<a href="#"><img name="querybt" onfocus="GrayBackEOn( this );" onblur="GrayBackEOff( this );" onmouseover="GrayBackEOn( this );" onmouseout="GrayBackEOff( this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="/img/type01/cmn/querybt/back_gray_off_en_bt.gif" width="72" height="20" border="0" tabindex="1"></a>';






//----------------------------------------------------------------------
// 解説 : クエリーボタン書き出し関数
//----------------------------------------------------------------------
function fncObjQuery( lngLanguageCode , lngEventCode )
{

	// [閉じる]ボタン
	if( lngEventCode == 0 )
	{
		switch( lngLanguageCode )
		{
			case 0: // 英語
				objQuery.innerHTML = closeBtE;
				break;

			case 1: // 日本語
				objQuery.innerHTML = closeBtJ;
				break;

			default:
				break;
		}
	}

	// [戻る]ボタン
	else if( lngEventCode == 1 )
	{
		switch( lngLanguageCode )
		{
			case 0: // 英語
				objQuery.innerHTML = backBtE;
				break;

			case 1: // 日本語
				objQuery.innerHTML = backBtJ;
				break;

			default:
				break;
		}
	}

	return false;
}





//----------------------------------------------------------------------
// 解説 : ボタンイベント処理関数
//----------------------------------------------------------------------
function fncClickEvent( strEvent )
{
	if( strEvent != 'close' )
	{
		location.href = strEvent;
	}
	else if( strEvent == 'close' )
	{
		window.parent.close();
	}

	fncAlphaOn( document.all.querybt );
	return false;
}





//----------------------------------------------------------------------
// 解説 : 初期レイアウト処理関数
//----------------------------------------------------------------------
var TopMargin = '230';

function initLayout()
{
	ErrorSet.style.marginTop = TopMargin;
	return false;
}