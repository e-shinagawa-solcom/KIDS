<?php

// ----------------------------------------------------------------------------
/**
*       納品書削除
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
*         ・指定納品伝票番号データの削除処理
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------

// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "sc/cmn/lib_scd1.php");
require (SRC_ROOT . "sc/cmn/column_scd.php");
require (LIB_DEBUGFILE);
require_once (LIB_EXCLUSIVEFILE);

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
if ( !$aryData["lngSlipNo"] )
{
	fncOutputError ( 9061, DEF_ERROR, "データ異常です。", TRUE, "", $objDB );
}

// 文字列チェック
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
// TODO:要仕様確認
//$aryCheck["lngSlipNo"]	  = "null:number(0,10)";

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限確認
// 602 売上管理（売上検索）
if ( !fncCheckAuthority( DEF_FUNCTION_SC2, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}
// 606 売上管理（売上削除）
if ( !fncCheckAuthority( DEF_FUNCTION_SC6, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// 削除対象の納品伝票番号の納品情報取得
$strQuery = fncGetSlipHeadNoToInfoSQL ( $aryData["lngSlipNo"], $aryData["lngRevisionNo"] );

list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

if ( $lngResultNum == 1 )
{
	$aryHeadResult = $objDB->fetchArray( $lngResultID, 0 );
}
else
{
	fncOutputError( 603, DEF_ERROR, "データが異常です", TRUE, "../sc/search2/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}
$objDB->freeResult( $lngResultID );

// *****************************************************
//   削除処理実行（Submit時）
// *****************************************************
if( $aryData["strSubmit"] )
{
	
	$lngSalesNo = $aryHeadResult["lngsalesno"];
	$strSlipCode = $aryHeadResult["strslipcode"];
	$lngSlipNo = $aryHeadResult["lngslipno"];
	$strCustomerCode = $aryHeadResult["strcustomercode"];

	// --------------------------------
	//    削除処理
	// --------------------------------
	// トランザクション開始
	$objDB->transactionBegin();

fncDebug("kids2.log", "step-0", __FILE__, __LINE__, "a");
    

	// --------------------------------
	//    削除可能かどうかのチェック
	// --------------------------------
	// 顧客の国が日本で、かつ納品書ヘッダに紐づく請求書明細が存在する場合は削除不可
	if (fncJapaneseInvoiceExists($strCustomerCode, $lngSalesNo, $objDB)){
		MoveToErrorPage("請求書発行済みのため、削除できません");
	}

	// 納品書明細に紐づく受注ステータスが「締済み」の場合は削除不可
	if (fncReceiveStatusIsClosed($aryData["lngSlipNo"], $objDB))
	{
		MoveToErrorPage("締済みのため、削除できません");
	}

	

	if( !lockSlip($aryData["lngSlipNo"], $objDB))
	{
		MoveToErrorPage("他ユーザーが納品書を編集中です。");
	}

	
	if( isSlipModified($aryData["lngSlipNo"], $aryData["lngRevisionNo"], $objDB) )
	{
		MoveToErrorPage("納品書が他ユーザーにより更新または削除されています。");
	}


	// 売上データの削除
	if (!fncDeleteSales($lngSalesNo, $objDB, $objAuth))
	{
		fncOutputError ( 9051, DEF_FATAL, "削除処理に伴う売上マスタ処理失敗", TRUE, "", $objDB );
	}

	// 納品書データの削除
	if (!fncDeleteSlip($lngSlipNo, $objDB, $objAuth))	
	{
		fncOutputError ( 9051, DEF_FATAL, "削除処理に伴う納品書マスタ処理失敗", TRUE, "", $objDB );
	}


	// 納品伝票明細に紐づく受注マスタの受注ステータスを「受注」に更新
	if (!fncUpdateReceiveStatus($aryData["lngSlipNo"], $aryData["lngRevisionNo"], $objDB))
	{
		fncOutputError ( 9051, DEF_FATAL, "削除処理に伴う受注明細テーブル処理失敗", TRUE, "", $objDB );
	}


	// トランザクションコミット
	$objDB->transactionCommit();

	// 削除完了画面の表示
	$aryDeleteData = $aryHeadResult;
	$aryDeleteData["strAction"] = "/sc/search2/index.php?strSessionID=";
	$aryDeleteData["strSessionID"] = $aryData["strSessionID"];

	// 言語コード：日本語
	$aryDeleteData["lngLanguageCode"] = 1;

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "sc/finish2/remove_parts.tmpl" );

	// テンプレート生成
	$objTemplate->replace( $aryDeleteData );
	$objTemplate->complete();

	// HTML出力
	echo $objTemplate->strTemplate;

	$objDB->close();

	return true;
}

// *****************************************************
//   削除確認画面表示（Submit前）
// *****************************************************
// 取得データを表示用に整形
$aryNewResult = fncSetSlipHeadTableData ( $aryHeadResult );

// ヘッダ部のカラム名の設定（キーの頭に"CN"を付与する）
$aryHeadColumnNames_CN = fncAddColumnNameArrayKeyToCN ( $aryHeadColumnNames );

// 詳細部のカラム名の設定（キーの頭に"CN"を付与する）
$aryDetailColumnNames_CN = fncAddColumnNameArrayKeyToCN ( $aryDetailColumnNames );

// 指定売上番号の売上明細データ取得用SQL文の作成
$strQuery = fncGetSlipDetailNoToInfoSQL ( $aryData["lngSlipNo"], $aryData["lngRevisionNo"] );

// 明細データの取得
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
if ( $lngResultNum )
{
	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$arySalesDetailResult[] = $objDB->fetchArray( $lngResultID, $i );
	}
}
else
{
	$strMessage = fncOutputError( 603, DEF_WARNING, "納品伝票番号に対する明細が存在しません", FALSE, "../sc/search2/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}
$objDB->freeResult( $lngResultID );

for ( $i = 0; $i < count($arySalesDetailResult); $i++)
{
	// 明細データを表示用に加工
	$aryNewDetailResult[$i] = fncSetSlipDetailTableData ( $arySalesDetailResult[$i], $aryNewResult );

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "sc/result2/parts_detail.tmpl" );

	// テンプレート生成
	$objTemplate->replace( $aryDetailColumnNames_CN );
	$objTemplate->replace( $aryNewDetailResult[$i] );
	$objTemplate->complete();

	// HTML出力
	$aryDetailTable[] = $objTemplate->strTemplate;
}

$aryNewResult["strSessionID"] = $aryData["strSessionID"];
if ( count($arySalesDetailResult) )
{
	$aryNewResult["strDetailTable"] = implode ("\n", $aryDetailTable );
}

$aryNewResult["strAction"] = "index3.php";
$aryNewResult["strSubmit"] = "submit";
$aryNewResult["strMode"] = "delete";

// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "sc/result2/parts2.tmpl" );

// テンプレート生成
$objTemplate->replace( $aryNewResult );
$objTemplate->replace( $aryHeadColumnNames_CN );
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;

$objDB->close();
return true;

// エラー画面への遷移
function MoveToErrorPage($strMessage){
	
	// 言語コード：日本語
	$aryHtml["lngLanguageCode"] = 1;

	// エラーメッセージの設定
	$aryHtml["strErrorMessage"] = $strMessage;

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "/result/error/parts.tmpl" );
	
	// テンプレート生成
	$objTemplate->replace( $aryHtml );
	$objTemplate->complete();

	// HTML出力
	echo $objTemplate->strTemplate;

	exit;
}

?>