<?php
/** 
*	エラー画面表示　処理
*
*	エラー画面の表示処理
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Keiji Suzukaze <k-suzukaze@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*	処理概要
*	システム的エラーの場合にエラー画面を表示させる
*
*	更新履歴
*
*	2004.02.26	エラー発生時の戻りのアドレスを　LOGIN_URL　を使用しないように修正
*
*
*
*/

// エラー画面表示処理

// 設定の読み込み
include_once ( "conf.inc" );

// ライブラリ読み込み
require ( LIB_FILE );

// GETデータの取得
$aryData["ref"]				= $_GET["ref"];
$aryData["path"]			= $_GET["path"];
$aryData["strMessage"]		= $_GET["strMessage"];
// COOKIEから言語コードを取得
$aryData["lngLanguageCode"] = $_COOKIE[lngLanguageCode];
if ( $aryData["lngLanguageCode"] == "" )
{
	$aryData["lngLanguageCode"] = 0;
}

// テンプレートオブジェクト生成
$objTemplate = new clsTemplate();
// $objTemplate->getTemplate( "error/parts.tmpl" );
$objTemplate->getTemplate( "error/index.html" );

// echo "ref = " . $aryData["ref"] . "<br>";
// echo "path = " . $aryData["path"] . "<br>";

if ( $aryData["ref"] == TOP_URL )
{
	if ( $aryData["path"] != "" )
	{
		$aryData["strEvent"] = TOP_URL . $aryData["path"];
//		$aryData["strEvent"] = "history.back()";
	}
	else
	{
// 2004.02.26 suzukaze update start
		$aryData["strEvent"] = TOP_URL . "login/login.php?value=kids";
// 2004.02.26 suzukaze update end
	}
//	$aryData["strEvent"]     = "javascript:top.location.href='" . LOGIN_URL . "';";
	$aryData["strEventText"] = "戻る";
	$aryData["lngEventCode"] = DEF_ERROR_BACK;
}
else
{
//	$aryData["strEvent"]     = "window.close();";
	$aryData["strEvent"]     = "close";
	$aryData["strEventText"] = "閉じる";
	$aryData["lngEventCode"] = DEF_ERROR_CLOSE;
}

// 置き換え
$objTemplate->replace( $aryData );
$objTemplate->complete();


echo $objTemplate->strTemplate;

?>

<script language=javascript>

function fncShowHidePreload( strMode )
{
	// 表示
	if( strMode == 0 )
	{
		if( typeof(parent.Preload) != 'undefined' )
		{
			parent.Preload.style.visibility = 'visible';
		}
	}

	// 非表示
	else if( strMode == 1 )
	{
		if( typeof(parent.Preload) != 'undefined' )
		{
			parent.Preload.style.visibility = 'hidden';
		}
	}

	return false;
}

if( typeof(fncShowHidePreload) != 'undefined' )
{
	fncShowHidePreload( 1 );
}

</script>

<?

return TRUE;
?>
