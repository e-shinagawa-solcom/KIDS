

//@-------------------------------------------------------------------------------------------------------------------
/**
* ファイル概要 : [登録・詳細・削除・無効化]処理ボタンテーブルモジュール
*
*
*
* 備考 : 商品・受注・発注・売上・仕入の検索結果画面からの各処理時に
*        書き出すテーブルを定義している。
*
* @package k.i.d.s.
* @license http://www.wiseknot.co.jp/
* @copyright Copyright &copy; 2004, Wiseknot, Inc.
* @author Ryosuke Tomita <r-tomita@wiseknot.co.jp>
* @access public
* @version 0.1
*/ 
//--------------------------------------------------------------------------------------------------------------------





















//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : 日本語英語切替用グローバル変数
*
* 対象 : [登録・詳細・削除・無効化]用テンプレート
*
* @param [g_lngCode] : [数値型] . 親iframeから取得
*/
//--------------------------------------------------------------------------------------------------------------------
// var g_lngCode = parent.lngLanguageCode.value; // [lngLanguageCode]値取得-------------------------------------------
var g_lngCode = 1















//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : 各処理ボタンテーブルセットの定義
*
* 解説 : 各[登録確認・詳細確認・削除確認・無効化確認]用の
*        テーブルセット(日本語・英語)を定義する。
*
* 対象 : [登録・詳細・削除・無効化]用テンプレート
*
* 外部関数 : [fncProcessingOutputModule] 処理ボタンテーブル書き出しモジュール
*
* @param [registTableJ]    : [文字列型] . 登録確認用(日本語)
* @param [registTableE]    : [文字列型] . 登録確認用(英語)
* @param [deleteTableJ]    : [文字列型] . 削除確認用(日本語)
* @param [deleteTableE]    : [文字列型] . 削除確認用(英語)
* @param [detailTableJ]    : [文字列型] . 詳細確認用(日本語)
* @param [detailTableE]    : [文字列型] . 詳細確認用(英語)
* @param [invalidTable01J] : [文字列型] . 無効化処理用パターン１(日本語)
* @param [invalidTable01E] : [文字列型] . 無効化処理用パターン１(英語)
* @param [invalidTable02J] : [文字列型] . 無効化処理用パターン２(日本語)
* @param [invalidTable02E] : [文字列型] . 無効化処理用パターン２(英語)
* @param [invalidTable03J] : [文字列型] . 無効化処理用パターン３(日本語)
* @param [invalidTable03E] : [文字列型] . 無効化処理用パターン３(英語)
* @param [invalidTable04J] : [文字列型] . 無効化処理用パターン４(日本語)
* @param [invalidTable04E] : [文字列型] . 無効化処理用パターン４(英語)
*/
//--------------------------------------------------------------------------------------------------------------------
// 登録確認用(日本語)
var registTableJ = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>下記の内容で登録しますか？</td></tr><tr><td bgcolor="#f1f1f1"><span id="regist_proc"><a href="#" onclick="fncClose();return false;"><img id="close_btn" onfocus="CloseJOn( this );" onblur="CloseJOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="#" onclick="document.getElementById('+'\'regist_proc\''+').outerText='+'\'登録処理中...\''+'; window.form1.submit();return false;"><img id="regist_btn" onfocus="GrayRegistJOn( this );" onblur="GrayRegistJOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="GrayRegistJOn(this)" onmouseout="GrayRegistJOff(this);fncAlphaOff( this );" src="/img/type01/cmn/querybt/regist_gray_off_ja_bt.gif" width="72" height="20" border="0" alt="登録"><a></span></td></tr></table>';
// 登録確認用(英語)
var registTableE = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>Does it register from the following contents?</td></tr><tr><td bgcolor="#f1f1f1"><span id="regist_proc"><a href="#" onclick="fncClose;return false;"><img id="close_btn" onfocus="CloseEOn( this );" onblur="CloseEOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="CloseEOn( this );" onmouseout="CloseEOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_en_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<span name=""><a href="#" onclick="document.getElementById('+'\'regist_proc\''+').outerText='+'\'Registering...\''+'; window.form1.submit();return false;"><img id="regist_btn" onfocus="GrayRegistEOff( this );" onblur="GrayRegistEOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="GrayRegistEOn(this);fncAlphaOff( this );" onmouseout="GrayRegistEOff(this);fncAlphaOff( this );" src="/img/type01/cmn/querybt/regist_gray_off_en_bt.gif" width="72" height="20" border="0" alt="REGIST"><a></span></td></tr></table>';


// 削除確認用(日本語)
var deleteTableJ = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>削除しますか？</td></tr><tr><td bgcolor="#f1f1f1"><a href="#" onclick="fncClose();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="CloseJOn( this );" onblur="CloseJOff( this );" onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="#" onclick="window.form1.submit();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="DeleteJOn( this );" onblur="DeleteJOff( this );" onmouseover="DeleteJOn( this );" onmouseout="DeleteJOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/delete_off_ja_bt.gif" width="72" height="20" border="0" alt="DELETE"><a></td></tr></table>';
// 削除確認用(英語)
var deleteTableE = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>Does it delete?</td></tr><tr><td bgcolor="#f1f1f1"><a href="#" onclick="fncClose();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="CloseEOn( this );" onblur="CloseEOff( this );" onmouseover="CloseEOn( this );" onmouseout="CloseEOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_en_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="#" onclick="window.form1.submit();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="DeleteEOn( this );" onblur="DeleteEOff( this );" onmouseover="DeleteEOn( this );" onmouseout="DeleteEOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/delete_off_en_bt.gif" width="72" height="20" border="0" alt="DELETE"><a></td></tr></table>';


// 詳細確認用(日本語)
var detailTableJ = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr><td bgcolor="#f1f1f1"><a href="#" onclick="fncClose();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="CloseJOn( this );" onblur="CloseJOff( this );" onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a></td></tr></table>';
// 詳細確認用(英語)
var detailTableE = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr><td bgcolor="#f1f1f1"><a href="#" onclick="fncClose();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="CloseEOn( this );" onblur="CloseEOff( this );" onmouseover="CloseEOn( this );" onmouseout="CloseEOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_en_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a></td></tr></table>';



// 無効化処理用パターン１ [内容]：無効化処理でデータの入れ替えなど何も発生しない場合
// (日本語)
var invalidTable01J = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>無効化しますか？</td></tr><tr><td bgcolor="#f1f1f1"><a href="#" onclick="fncClose;"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="CloseJOn( this );" onblur="CloseJOff( this );" onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="#" onclick="window.form1.submit();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="fncInvalidBigButton( \'onJ\' , this );" onblur="fncInvalidBigButton( \'offJ\' , this );" onmouseover="fncInvalidBigButton( \'onJ\' , this );" onmouseout="fncInvalidBigButton( \'offJ\' , this );fncAlphaOff( this );" src="/img/type01/cmn/querybt/invalid_big_off_ja_bt.gif" width="72" height="20" border="0" alt="INVALID"><a></td></tr></table>';
// (英語)
var invalidTable01E = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>Does it invalid?</td></tr><tr><td bgcolor="#f1f1f1"><a href="#" onclick="fncClose;"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="CloseEOn( this );" onblur="CloseEOff( this );" onmouseover="CloseEOn( this );" onmouseout="CloseEOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_en_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="#" onclick="window.form1.submit();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="fncInvalidBigButton( \'onE\' , this );" onblur="fncInvalidBigButton( \'offE\' , this );" onmouseover="fncInvalidBigButton( \'onE\' , this );" onmouseout="fncInvalidBigButton( \'offE\' , this );fncAlphaOff( this );" src="/img/type01/cmn/querybt/invalid_big_off_en_bt.gif" width="72" height="20" border="0" alt="INVALID"><a></td></tr></table>';


// 無効化処理用パターン２ [内容]：無効化処理で前の発注が有効になる場合
// (日本語)
var invalidTable02J = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>以前のデータが復活します。無効化しますか？</td></tr><tr><td bgcolor="#f1f1f1"><a href="#" onclick="fncClose();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="CloseJOn( this );" onblur="CloseJOff( this );" onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="#" onclick="window.form1.submit();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="fncInvalidBigButton( \'onJ\' , this );" onblur="fncInvalidBigButton( \'offJ\' , this );" onmouseover="fncInvalidBigButton( \'onJ\' , this );" onmouseout="fncInvalidBigButton( \'offJ\' , this );fncAlphaOff( this );" src="/img/type01/cmn/querybt/invalid_big_off_ja_bt.gif" width="72" height="20" border="0" alt="INVALID"><a></td></tr></table>';
// (英語)
var invalidTable02E = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>Former data revives. Does it invalid?</td></tr><tr><td bgcolor="#f1f1f1"><a href="#" onclick="fncClose();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="CloseEOn( this );" onblur="CloseEOff( this );" onmouseover="CloseEOn( this );" onmouseout="CloseEOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_en_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="#" onclick="window.form1.submit();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="fncInvalidBigButton( \'onE\' , this );" onblur="fncInvalidBigButton( \'offE\' , this );" onmouseover="fncInvalidBigButton( \'onE\' , this );" onmouseout="fncInvalidBigButton( \'offE\' , this );fncAlphaOff( this );" src="/img/type01/cmn/querybt/invalid_big_off_en_bt.gif" width="72" height="20" border="0" alt="INVALID"><a></td></tr></table>';


// 無効化処理用パターン３ [内容]：削除データを無効化することで、データが復活する場合
// (日本語)
var invalidTable03J = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>削除されたデータが復活します。無効化しますか？</td></tr><tr><td bgcolor="#f1f1f1"><a href="#" onclick="fncClose();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="CloseJOn( this );" onblur="CloseJOff( this );" onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="#" onclick="window.form1.submit();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="fncInvalidBigButton( \'onJ\' , this );" onblur="fncInvalidBigButton( \'offJ\' , this );" onmouseover="fncInvalidBigButton( \'onJ\' , this );" onmouseout="fncInvalidBigButton( \'offJ\' , this );fncAlphaOff( this );" src="/img/type01/cmn/querybt/invalid_big_off_ja_bt.gif" width="72" height="20" border="0" alt="INVALID"><a></td></tr></table>';
// (英語)
var invalidTable03E = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>The deleted data revives. Does it invalid?</td></tr><tr><td bgcolor="#f1f1f1"><a href="#" onclick="fncClose();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="CloseEOn( this );" onblur="CloseEOff( this );" onmouseover="CloseEOn( this );" onmouseout="CloseEOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_en_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="#" onclick="window.form1.submit();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="fncInvalidBigButton( \'onE\' , this );" onblur="fncInvalidBigButton( \'offE\' , this );" onmouseover="fncInvalidBigButton( \'onE\' , this );" onmouseout="fncInvalidBigButton( \'offE\' , this );fncAlphaOff( this );" src="/img/type01/cmn/querybt/invalid_big_off_en_bt.gif" width="72" height="20" border="0" alt="INVALID"><a></td></tr></table>';


// 無効化処理用パターン４ [内容]：無効化処理で有効になるデータが無い場合
// (日本語)
var invalidTable04J = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>復活するデータが存在しません。無効化しますか？</td></tr><tr><td bgcolor="#f1f1f1"><a href="#" onclick="fncClose();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="CloseJOn( this );" onblur="CloseJOff( this );" onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="#" onclick="window.form1.submit();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="fncInvalidBigButton( \'onJ\' , this );" onblur="fncInvalidBigButton( \'offJ\' , this );" onmouseover="fncInvalidBigButton( \'onJ\' , this );" onmouseout="fncInvalidBigButton( \'offJ\' , this );fncAlphaOff( this );" src="/img/type01/cmn/querybt/invalid_big_off_ja_bt.gif" width="72" height="20" border="0" alt="INVALID"><a></td></tr></table>';
// (英語)
var invalidTable04E = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>The data to revitalize does not exist. Does it invalid?</td></tr><tr><td bgcolor="#f1f1f1"><a href="#" onclick="fncClose();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="CloseEOn( this );" onblur="CloseEOff( this );" onmouseover="CloseEOn( this );" onmouseout="CloseEOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_en_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="#" onclick="window.form1.submit();"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onfocus="fncInvalidBigButton( \'onE\' , this );" onblur="fncInvalidBigButton( \'offE\' , this );" onmouseover="fncInvalidBigButton( \'onE\' , this );" onmouseout="fncInvalidBigButton( \'offE\' , this );fncAlphaOff( this );" src="/img/type01/cmn/querybt/invalid_big_off_en_bt.gif" width="72" height="20" border="0" alt="INVALID"><a></td></tr></table>';







function fncClose() {
	parent.close();
}












//@-------------------------------------------------------------------------------------------------------------------
/**
* 概要 : 処理ボタンテーブル書き出しモジュール
*
* 解説 : 各処理用テンプレートで使用する[fncChgEtoJ]関数内で使用。
*
* 対象 : 入力フィールド
*
* 外部関数 : [fncChgEtoJ] 呼出先関数。
*            /src/pc/cmn/resultexstr.js
*            /src/po/cmn/resultexstr.js
*            /src/sc/cmn/resultexstr.js
*            /src/so/cmn/resultexstr.js
*            /src/uc/cmn/resultexstr.js
*
* @param [lngCode] : [数値型]   . [0/1]の数値
* @param [strMode] : [文字列型] . 'regist' , 'detail' , 'delete' , 'use' ,
*                                 'Invalid01' , 'Invalid02' , 'Invalid03' , 'Invalid04'
*                                 の処理振分け用の一意の文字列
*
* @event [onload] : body
*/
//--------------------------------------------------------------------------------------------------------------------
function fncProcessingOutputModule( strMode , lngCode )
{

	switch( strMode )
	{

		case 'regist':

			if( lngCode == 0 )
			{
				ControlTitle.innerText = 'REGISTRATION CONFIRM';
				objQuery.innerHTML = registTableE;
			}
			else if( lngCode == 1 )
			{
				ControlTitle.innerText = '登録確認';
				objQuery.innerHTML = registTableJ;
			}

			break;


		case 'renew':

			if( lngCode == 0 )
			{
				ControlTitle.innerText = 'REGISTRATION CONFIRM';
				objQuery.innerHTML = registTableE;
			}
			else if( lngCode == 1 )
			{
				ControlTitle.innerText = '登録確認';
				objQuery.innerHTML = registTableJ;
			}

			break;


		case 'detail':

			if( lngCode == 0 )
			{
				ControlTitle.innerText = 'DETAIL CONFIRM';
				objQuery.innerHTML = detailTableE;
			}
			else if( lngCode == 1 )
			{
				ControlTitle.innerText = '詳細確認';
				objQuery.innerHTML = detailTableJ;
			}

			break;


		case 'delete':

			if( lngCode == 0 )
			{
				ControlTitle.innerText = 'DELETE CONFIRM';
				objQuery.innerHTML = deleteTableE;
			}
			else if( lngCode == 1 )
			{
				ControlTitle.innerText = '削除確認';
				objQuery.innerHTML = deleteTableJ;
			}

			break;


		case 'use':

			if( lngCode == 0 )
			{
				ControlTitle.innerText = 'DATA UNDER USE';
				objQuery.innerHTML = detailTableE;
			}
			else if( lngCode == 1 )
			{
				ControlTitle.innerText = 'データ使用中';
				objQuery.innerHTML = detailTableJ;
			}

			break;


		case 'Invalid01':

			if( lngCode == 0 )
			{
				ControlTitle.innerText = 'INVALID CONFIRM';
				objQuery.innerHTML = invalidTable01E;
			}
			else if( lngCode == 1 )
			{
				ControlTitle.innerText = '無効化確認';
				objQuery.innerHTML = invalidTable01J;
			}

			break;


		case  'Invalid02':

			if( lngCode == 0 )
			{
				ControlTitle.innerText = 'INVALID CONFIRM';
				objQuery.innerHTML = invalidTable02E;
			}
			else if( lngCode == 1 )
			{
				ControlTitle.innerText = '無効化確認';
				objQuery.innerHTML = invalidTable02J;
			}

			break;


		case 'Invalid03':

			if( lngCode == 0 )
			{
				ControlTitle.innerText = 'INVALID CONFIRM';
				objQuery.innerHTML = invalidTable03E;
			}
			else if( lngCode == 1 )
			{
				ControlTitle.innerText = '無効化確認';
				objQuery.innerHTML = invalidTable03J;
			}

			break;


		case 'Invalid04':

			if( lngCode == 0 )
			{
				ControlTitle.innerText = 'INVALID CONFIRM';
				objQuery.innerHTML = invalidTable04E;
			}
			else if( lngCode == 1 )
			{
				ControlTitle.innerText = '無効化確認';
				objQuery.innerHTML = invalidTable04J;
			}

			break;


		default:
			break;

	}

	return false;
}