<?php

// ----------------------------------------------------------------------------
/**
*       発注管理  詳細
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
*         ・指定発注番号データの詳細表示処理
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------



// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "po/cmn/lib_pos.php");
require (SRC_ROOT . "list/cmn/lib_lo.php");
require (SRC_ROOT . "po/cmn/lib_pos1.php");
require (SRC_ROOT . "po/cmn/column.php");

// DB接続
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

//////////////////////////////////////////////////////////////////////////
// GETデータ取得
//////////////////////////////////////////////////////////////////////////
if ( $_GET )
{
	$aryData = $_GET;
}
else if ( $_POST )
{
	$aryData = $_POST;
}
// 2004.04.19 suzukaze update start
if ( !isset($aryData["lngOrderNo"]) )
{
	fncOutputError ( 9061, DEF_ERROR, "データ異常です。", TRUE, "", $objDB );
}
// 2004.04.19 suzukaze update end

// 文字列チェック
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryCheck["lngOrderNo"]	  = "null:number(0,10)";
// $aryResult = fncAllCheck( $aryData, $aryCheck );
// fncPutStringCheckError( $aryResult, $objDB );

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ログインユーザーコードの取得
$lngInputUserCode = $objAuth->UserCode;


// 権限確認
// 502 発注管理（発注検索）
if ( !fncCheckAuthority( DEF_FUNCTION_PO2, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}
// 504 発注管理（詳細表示）
if ( !fncCheckAuthority( DEF_FUNCTION_PO4, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

//詳細画面の表示

$lngOrderNo = $aryData["lngOrderNo"];

// 指定発注番号の発注データ取得用SQL文の作成
//$strQuery = fncGetPurchaseHeadNoToInfoSQL ( $lngOrderNo );
$aryResult = fncGetPurchaseHeadNoToInfo ( $lngOrderNo, $objDB );

// 詳細データの取得
// list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

// if ( $lngResultNum )
// {
// 	if ( $lngResultNum == 1 )
// 	{
// 		$aryResult = $objDB->fetchArray( $lngResultID, 0 );
// 	}
// 	else
// 	{
// 		fncOutputError( 503, DEF_ERROR, "該当データの取得に失敗しました", TRUE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
// 	}
// }
// else
// {
// 	fncOutputError( 503, DEF_ERROR, "データが異常です", TRUE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
// }

// $objDB->freeResult( $lngResultID );

// 取得データの調整
$aryNewResult = fncSetPurchaseHeadTabelData ( $aryResult );

// 言語の設定
$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];
if ( $aryData["lngLanguageCode"] == 0 )
{
	$aryTytle = $aryTableTytleEng;
}
else
{
	$aryTytle = $aryTableTytle;
}

// カラム名の設定
$aryHeadColumnNames = fncSetPurchaseTabelName ( $aryTableViewHead, $aryTytle );
// カラム名の設定
$aryDetailColumnNames = fncSetPurchaseTabelName ( $aryTableViewDetail, $aryTytle );

////////// 明細行の取得 ////////////////////

// 指定発注番号の発注明細データ取得用SQL文の作成
// $strQuery = fncGetPurchaseDetailNoToInfoSQL ( $lngOrderNo );
$aryDetailResult[] = fncGetPurchaseDetailNoToInfo ( $lngOrderNo, $objDB );

// 明細データの取得
// list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

// if ( $lngResultNum )
// {
// 	for ( $i = 0; $i < $lngResultNum; $i++ )
// 	{
// 		$aryDetailResult[] = $objDB->fetchArray( $lngResultID, $i );
// 	}
// }
// else
// {
// 	fncOutputError( 503, DEF_WARNING, "発注番号に対する明細情報が見つかりません。", TRUE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
// }

// $objDB->freeResult( $lngResultID );

for ( $i = 0; $i < count($aryDetailResult); $i++)
{
	$aryNewDetailResult[$i] = fncSetPurchaseDetailTabelData ( $aryDetailResult[$i], $aryNewResult );

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "po/result/parts_detail.tmpl" );

	// テンプレート生成
	$objTemplate->replace( $aryDetailColumnNames );
	$objTemplate->replace( $aryNewDetailResult[$i] );
	$objTemplate->complete();

	// HTML出力
	$aryDetailTable[] = $objTemplate->strTemplate;
}

$aryNewResult["strDetailTable"] = implode ("\n", $aryDetailTable );

// 帳票出力対応
// 表示対象が削除データ、申請中データの場合はプレビューボタンを表示しない
// また帳票出力権限を持ってない場合もプレビューボタンは表示しない
if ( $aryResult["lngrevisionno"] >= 0 and $aryResult["lngorderstatuscode"] != DEF_ORDER_APPLICATE and fncCheckAuthority( DEF_FUNCTION_LO2, $objAuth ))
{
	// 表示対象が、否認データ、申請取消データの場合はプレビューボタンを表示しない
	// if ( fncCheckApprovalProductOrder( $aryData["lngOrderNo"], $objDB ) )
	// {
	// 	$aryNewResult["strPreviewAction"] = "../../list/result/frameset.php?strSessionID=" . $aryData["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_ORDER . "&strReportKeyCode=" . $aryData["lngOrderNo"] . "&bytCopyFlag=TRUE";

		$aryNewResult["listview"] = 'visible';
	// }
	// else
	// {
		// $aryNewResult["listview"] = 'hidden';
	// }
}
else
{
	$aryNewResult["listview"] = 'hidden';
}




$aryNewResult["strAction"] = "index2.php";
$aryNewResult["strMode"] = "detail";

// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "po/result/parts2.tmpl" );

// テンプレート生成
$objTemplate->replace( $aryNewResult );
$objTemplate->replace( $aryHeadColumnNames );
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;


$objDB->close();
return true;

?>