<?php

// ----------------------------------------------------------------------------
/**
*       受注管理  検索画面
*
*
*       @package    K.I.D.S.
*       @license    http://www.kuwagata.co.jp/
*       @copyright  KUWAGATA CO., LTD.
*       @author     K.I.D.S. Groups <info@kids-groups.com>
*       @access     public
*       @version    2.00
*
*
*       処理概要
*         ・検索画面表示処理
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------



// 設定の読み込み
include_once ( "conf.inc" );

// ライブラリ読み込み
require ( LIB_FILE );

$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

//////////////////////////////////////////////////////////////////////////
// POST(一部GET)データ取得
//////////////////////////////////////////////////////////////////////////
if ( $_POST )
{
	$aryData = $_POST;
}
elseif ( $_GET )
{
	$aryData = $_GET;
}

setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

// 文字列チェック
$aryCheck["strSessionID"]   = "null:numenglish(32,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 402 受注管理（受注検索）
if ( !fncCheckAuthority( DEF_FUNCTION_SO2, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}
// 403 受注管理（受注検索　管理モード）
if ( fncCheckAuthority( DEF_FUNCTION_SO3, $objAuth ) )
{
	$aryData["AdminSet_visibility"] = 'style="visibility: visible"';
}
else
{
	$aryData["AdminSet_visibility"] = 'style="visibility: hidden"';
}
// 404 受注管理（詳細表示）
if ( fncCheckAuthority( DEF_FUNCTION_SO4, $objAuth ) )
{
	$aryData["btnDetail_visibility"] = 'style="visibility: visible"';
	$aryData["btnDetailVisible"] = "checked";
}
else
{
	$aryData["btnDetail_visibility"] = 'style="visibility: hidden"';
	$aryData["btnDetailVisible"] = "";
}
// 405 受注管理（確定）
if ( fncCheckAuthority( DEF_FUNCTION_SO4, $objAuth ) )
{
	$aryData["btnDecide_visibility"] = 'style="visibility: visible"';
	$aryData["btnDecideVisible"] = "checked";
}
else
{
	$aryData["btnDecide_visibility"] = 'style="visibility: hidden"';
	$aryData["btnDecideVisible"] = "";
}
// 406 受注管理（確定取消）
if ( fncCheckAuthority( DEF_FUNCTION_SO5, $objAuth ) )
{
	$aryData["btnCancel_visibility"] = 'style="visibility: visible"';
	$aryData["btnCancelVisible"] = "checked";
}
else
{
	$aryData["btnCancel_visibility"] = 'style="visibility: hidden"';
	$aryData["btnCancelVisible"] = "";
}

// 受注ステータス
// $aryData["lngReceiveStatusCode"] 	= fncGetCheckBoxObject( "m_receivestatus", "lngreceivestatuscode", "strreceivestatusname", "lngReceiveStatusCode[]", 'where lngReceiveStatusCode not in (1)', $objDB );
$aryData["lngReceiveStatusCode"] 	= fncGetCheckBoxObject( "m_receivestatus", "lngreceivestatuscode", "strreceivestatusname", "lngReceiveStatusCode[]", '', $objDB );


// 売上区分
$aryData["lngSalesClassCode"] = fncGetPulldown( "m_salesclass", "lngsalesclasscode", "lngsalesclasscode, strsalesclassname", 1, '', $objDB );

//　プルダウンリストの取得に失敗した場合エラー表示
if ( !$aryData["lngReceiveStatusCode"] or !$aryData["lngSalesClassCode"] )
{
    fncOutputError ( 9055, DEF_WARNING, "システム管理者にお問い合わせ下さい。", TRUE, "", $objDB );
}

// ヘルプ対応
$aryData["lngFunctionCode"] = DEF_FUNCTION_SO2;

// テンプレート読み込み
echo fncGetReplacedHtmlWithBase("search/base_search.html", "so/search/so_search.html", $aryData ,$objAuth );

$objDB->close();

return true;

?>

