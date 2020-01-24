<!--
//: ----------------------------------------------------------------------------
//: ファイル概要：
//:               共通関数
//: 備考        ：
//:               
//:
//: 作成日      ：YYYY/MM/MM
//: 作成者      ：** **
//: 修正履歴    ：
//: ----------------------------------------------------------------------------


//@*****************************************************************************
// 概要   ：BEEP SOUND
//******************************************************************************
function beep()
{
	BeepSound.src='/error/lupin.wav';
}


//@*****************************************************************************
// 概要   ：文字列内の文字を全て置き換える
// ﾊﾟﾗﾒｰﾀｰ：strIn,  [String型], 文字列
//          strExp, [String型], 置き換えたい文字列
//          strNew, [String型], 置き換え後の文字列
// 戻り値 ：置き換え後の文字列  [String型]
// 解説   ：1文字以上ある strIn の strExp を全て strNew へ置き換える
//******************************************************************************
function ComfncStringReplace(strIn, strExp, strNew)
{
	var strOut = new String(strIn);
	var i      = 0;

	if( strIn.length == 0 || strIn == "" ) return strOut;
  	while( strOut.search(strExp) != -1 )
	{
	    strOut = strOut.replace( strExp, strNew );
	    i++;
	    if( i >= strIn.length ) break;
	}
    return strOut;
}


//@*****************************************************************************
// 概要   ：文字列中の指定場所へ指定文字をｾｯﾄする
// ﾊﾟﾗﾒｰﾀｰ：sValue, [String],  対象文字列
//          nPnt,   [Number],  分割ﾎﾟｲﾝﾄ
//          sSet,   [String],  ｾｯﾄ文字
//          nFlg,   [Number],  sValue内に存在するsSetの処理 true-初期化する false-残す
//
// 戻り値 ：指定文字がｾｯﾄされた文字列
//
// 解説   ：
//        ：
// 注意   ：ﾗｲﾌﾞﾗﾘ中より呼出されている関数
//            ComfncHyphenFormat, 
//            ComfncYMDFormat
//******************************************************************************
function ComfncSplitSetString( sValue, nPnt, sSet, nFlg )
{
	var sVal = new String(sValue);
	var sIn  = "";
	if( sVal.length == 0 || sVal == "" ) return "";
	if( nFlg )
	{
		//var re  = new RegExp("\\x2d"); // -
		var re  = sSet;
		sIn     = new String(ComfncStringReplace(sVal, re, ""));
	}
	else
	{
		sIn     = sVal;
	}

	var sFront = new String(sIn.substr(0,nPnt));
	var sBack  = new String(sIn.substr(nPnt));

	var sRet   = "";

	if( sFront != "" && sBack != "" )
	{
		sRet = sFront + sSet + sBack;
	}
	else
	{
		sRet = sFront;
	}

	return sRet;
}


//@*****************************************************************************
//* 概要   ：ComfncMoneyFormat
//******************************************************************************
function ComfncMoneyFormat(oTxt)
{
	var s = oTxt.value;

//alert("置き換え対象の文字列->" + oTxt.value );
	var re = new RegExp("\\x2e");
	var sIn = ComfncStringReplace(oTxt.value, re, ",");
//alert("置換えました" + sIn);

	var b = ComfncStringReplace(sIn, ",", "");
	sIn = String(Number(b));
	if( isNaN(b) )
	{
		ErrMeg.style.visibility = 'visible' ;
		ERmark.style.visibility = 'visible' ;
		beep();
	//MegWin.style.visibility = 'visible' ;
		ErrMeg.innerText = '数値を入力して下さい' ;
	//alert("数値を入力して下さい\n" + b);
	//oTxt.focus();
		return false;
	}
	else 
	{
		ErrMeg.style.visibility = 'hidden' ;
		ERmark.style.visibility = 'hidden' ;
	//MegWin.style.visibility = 'hidden' ;
	}

	var nlen = sIn.length;
	var cnt  = 0;
	var ary  = new Array;
	var aryr = new Array;
	var s    = "";

	ary = sIn.split("");
	for( i = (nlen-1); i >= 0; i-- )
	{
	    //if( cnt == 3 || cnt == 6 || cnt == 9) s += ',';
	    if( cnt != 0 && (cnt % 3) == 0 ) s += ',';
	    s += String(ary[i]);
	    cnt++;
	}
	ary = s.split("");
	aryr = ary.reverse();
	s = String(aryr.join(""));
	oTxt.value = s;
}


//@*****************************************************************************
//* 概要   ：日付フォーマット YYYY/MM/DD に変換する
//* ﾊﾟﾗﾒｰﾀｰ：sValue,  [String],  文字列日付（YYYYMMDD)
//*
//* 戻り値 ：YYYY/MM/DD
//*
//* 解説   ：sValue(YYYYMMDD) を YYYY/MM/DD へ変換する
//*        ：
//* 注意   ：指定入力形式外の場合は alertが表示される。
//******************************************************************************
function ComfncYMDFormat(sValue)
{
	if( sValue == "" ) return "";

	var re   = new RegExp("\\x2f");
	var sVal = new String(ComfncStringReplace(sValue, re, ""));

	var sYMD = "";

	if( sVal.length == 8 )
	{
		ErrMeg.style.visibility = 'hidden' ;
		ERmark.style.visibility = 'hidden' ;
	//MegWin.style.visibility = 'hidden' ;
		sYMD = new String(ComfncSplitSetString( sVal, 4, '/', false ));
		sYMD = new String(ComfncSplitSetString( sYMD, 7, '/', false ));
	}
	else
	{
		ErrMeg.style.visibility = 'visible' ;
		ERmark.style.visibility = 'visible' ;
		beep();
	//MegWin.style.visibility = 'visible' ;
		ErrMeg.innerText = '日付は「YYYYMMDD」又は「YYYY/MM/DD」の形式で入力して下さい' ;
	//alert("日付は 'YYYYMMDD' 又は 'YYYY/MM/DD' の形式で入力して下さい");
		sYMD = sValue;
	}
	var dDate = false;
	if( (dDate = ComfncDateValidity(sYMD)) == "" )
	{
		//alert('無効な日付です');
		return sValue;
	}
	else
	{
		sYMD = dDate;
	}
	return sYMD;
}

//-->