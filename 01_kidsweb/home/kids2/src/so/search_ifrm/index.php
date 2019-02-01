<?php

// ----------------------------------------------------------------------------
/**
*       受注管理  検索項目画面 ( Inline Frame )
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
*         ・検索項目画面表示処理 ( Inline Frame )
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
$aryData = $_REQUEST;


// 文字列チェック
$aryCheck["strSessionID"]   = "null:numenglish(32,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限チェック
// 402 受注管理（受注検索）
if ( !fncCheckAuthority( DEF_FUNCTION_SO2, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}
// 403 受注管理（受注検索　管理モード）
if ( fncCheckAuthority( DEF_FUNCTION_SO3, $objAuth ) )
{
	$aryData["AdminSet_visibility"] = "visible";
	// 407 受注管理（無効化）
	if ( fncCheckAuthority( DEF_FUNCTION_SO7, $objAuth ) )
	{
		$aryData["btnInvalid_visibility"] = "visible";
		$aryData["btnInvalidVisible"] = "disabled";
	}
	else
	{
		$aryData["btnInvalid_visibility"] = "hidden";
		$aryData["btnInvalidVisible"] = "disabled";
	}
}
else
{
	$aryData["AdminSet_visibility"] = "hidden";
	$aryData["btnInvalid_visibility"] = "hidden";
	$aryData["btnInvalidVisible"] = "";
}
// 404 受注管理（詳細表示）
if ( fncCheckAuthority( DEF_FUNCTION_SO4, $objAuth ) )
{
	$aryData["btnDetail_visibility"] = "visible";
	$aryData["btnDetailVisible"] = "checked";
}
else
{
	$aryData["btnDetail_visibility"] = "hidden";
	$aryData["btnDetailVisible"] = "";
}
// 405 受注管理（修正）
if ( fncCheckAuthority( DEF_FUNCTION_SO5, $objAuth ) )
{
	$aryData["btnFix_visibility"] = "visible";
	$aryData["btnFixVisible"] = "checked";
}
else
{
	$aryData["btnFix_visibility"] = "hidden";
	$aryData["btnFixVisible"] = "";
}
// 406 受注管理（削除）
if ( fncCheckAuthority( DEF_FUNCTION_SO6, $objAuth ) )
{
	$aryData["btnDelete_visibility"] = "visible";
	$aryData["btnDeleteVisible"] = "checked";
}
else
{
	$aryData["btnDelete_visibility"] = "hidden";
	$aryData["btnDeleteVisible"] = "";
}


// 文字列チェック
$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// チェックボックスメニュー
// 受注状態
$aryData["lngReceiveStatusCode"] 	= fncGetCheckBoxObject( "m_receivestatus", "lngreceivestatuscode", "strreceivestatusname", "lngReceiveStatusCode[]", 'where lngReceiveStatusCode not in (1)', $objDB );
// ワークフロー状態
$aryData["lngWorkFlowStatusCode"] 	= fncGetCheckBoxObject( "m_workflowstatus", "lngworkflowstatuscode", "strworkflowstatusname", "lngWorkFlowStatusCode[]", 'where lngworkflowstatuscode not in (0,2,3)', $objDB );



// 売上区分
$aryData["lngSalesClassCode"]		= fncGetPulldown( "m_SalesClass", "lngsalesclasscode", "strsalesclassname", 0, '', $objDB );

//　プルダウンリストの取得に失敗した場合エラー表示
if ( !$aryData["lngReceiveStatusCode"] or !$aryData["lngSalesClassCode"] )
{
	fncOutputError ( 9055, DEF_WARNING, "システム管理者にお問い合わせ下さい。", TRUE, "", $objDB );
}

// クッキーの設定
if( $_COOKIE["ReceiveSearch"] )
{
	$aryCookie = fncStringToArray ( $_COOKIE["ReceiveSearch"], "&", ":" );
	while ( list ($strKeys, $strValues ) = each ( $aryCookie ) )
	{
		$aryData[$strKeys] = $strValues;
	}
}

// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "so/search_ifrm/parts.tmpl" );

// テンプレート生成
$objTemplate->replace( $aryData );
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;

$objDB->close();

return true;

?>

