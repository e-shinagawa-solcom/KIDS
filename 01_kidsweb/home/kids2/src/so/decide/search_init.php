<?php

// ----------------------------------------------------------------------------
/**
*       受注管理  確定条件検索画面
*
*       処理概要
*         ・確定対象検索画面表示処理
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
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// 売上分類
$aryData["lngSalesDivisionCode"] = fncGetPulldown( "m_salesdivision", "lngsalesdivisioncode", "lngsalesdivisioncode, strsalesdivisionname", 1, '', $objDB );

// // 売上区分
// $aryData["lngSalesClassCode"] = fncGetPulldown( "m_salesclass", "lngsalesclasscode", "lngsalesclasscode, strsalesclassname", 1, '', $objDB );


// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "so/decide/so_search.html" );
$objTemplate->replace($aryData);


// テンプレート生成
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;


$objDB->close();
return true;

?>

