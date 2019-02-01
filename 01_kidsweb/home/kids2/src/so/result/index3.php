<?php

// ----------------------------------------------------------------------------
/**
*       受注管理  削除
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
*         ・指定受注番号データの削除処理
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------



// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "so/cmn/lib_sos.php");
require (SRC_ROOT . "so/cmn/lib_sos1.php");
require (SRC_ROOT . "so/cmn/column.php");

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
if ( !$aryData["lngReceiveNo"] )
{
	fncOutputError ( 9061, DEF_ERROR, "データ異常です。", TRUE, "", $objDB );
}

// 文字列チェック
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryCheck["lngReceiveNo"]	  = "null:number(0,10)";
// $aryResult = fncAllCheck( $aryData, $aryCheck );
// fncPutStringCheckError( $aryResult, $objDB );

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ログインユーザーコードの取得
$lngInputUserCode = $objAuth->UserCode;


// 権限確認
// 502 受注管理（受注検索）
if ( !fncCheckAuthority( DEF_FUNCTION_SO2, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}
// 506 受注管理（受注削除）
if ( !fncCheckAuthority( DEF_FUNCTION_SO6, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}



//-------------------------------------------------------------------------
// ■「製品」にログインユーザーが属しているかチェック
//-------------------------------------------------------------------------
$strFncFlag = "SO";
$blnCheck = fncCheckInChargeProduct( $aryData["lngReceiveNo"], $lngInputUserCode, $strFncFlag, $objDB );

// ユーザーが対象製品に属していない場合
if( !$blnCheck )
{
	fncOutputError( 9060, DEF_WARNING, "", TRUE, "", $objDB );
}




// 削除対象の受注NOの受注情報取得
$strQuery = fncGetReceiveHeadNoToInfoSQL ( $aryData["lngReceiveNo"] );

list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
if ( $lngResultNum == 1 )
{
	$aryReceiveResult = $objDB->fetchArray( $lngResultID, 0 );
}
else
{
	fncOutputError( 503, DEF_ERROR, "データが異常です", TRUE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}
$objDB->freeResult( $lngResultID );

// var_dump( $aryData );
// exit;

////////////////////////////////////////////////////////
////////////////////// 削除確認処理 ////////////////////
////////////////////////////////////////////////////////
// 売上データの確認
$strReceiveCode = $aryReceiveResult["strreceivecode2"];
$aryCode = fncGetDeleteCodeToMaster ( $strReceiveCode, 1, $objDB );
if ( $aryCode )
{
	$lngSalesCount = count($aryCode);
}
else
{
	$lngSalesCount = 0;
}







////////////////////////////////////////////////////////
////////////////////// 削除処理実行 ////////////////////
////////////////////////////////////////////////////////
// 該当受注Ｎｏを指定している売上データが存在しなければ
if ( $aryData["strSubmit"] == "submit" and $lngSalesCount == 0 )
{
	// 該当受注の状態が「締め済」の状態であれば
	if ( $aryReceiveResult["lngreceivestatuscode"] == DEF_RECEIVE_CLOSED )
	{
		fncOutputError( 404, DEF_WARNING, "", TRUE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
	}


	// トランザクション開始
	$objDB->transactionBegin();

// 2004.03.24 suzukaze update start
	// 同じ受注Noを指定しているデータをロックする
	$strQuery = "SELECT strReceiveCode FROM m_Receive WHERE strReceiveCode = '" . $strReceiveCode . "' FOR UPDATE ";
	if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
	{
		fncOutputError ( 403, DEF_FATAL, "削除対象受注データのロック処理に失敗しました。", TRUE, "", $objDB );
	}
	$objDB->freeResult( $lngResultID );
// 2004.03.24 suzukaze update end

	// m_receiveのシーケンスを取得
	$sequence_m_receive = fncGetSequence( 'm_Receive.lngReceiveNo', $objDB );

	// 最小リビジョン番号の取得
	$strReceiveCode = $aryReceiveResult["strreceivecode2"];
	$strRevisionGetQuery = "SELECT MIN(lngRevisionNo) as minrevision FROM m_Receive WHERE strReceiveCode = '" . $strReceiveCode . "'";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strRevisionGetQuery, $objDB );
	if ( $lngResultNum )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$lngMinRevisionNo = $objResult->minrevision;
		if ( $lngMinRevisionNo > 0 )
		{
			$lngMinRevisionNo = 0;
		}
	}
	else
	{
		$lngMinRevisionNo = 0;
	}
	$objDB->freeResult( $lngResultID );
	$lngMinRevisionNo--;

	// 最大リバイズコードの取得
	$strReviseGetQuery = "SELECT MAX(strReviseCode) as maxrevise FROM m_Receive WHERE strReceiveCode = '" . $strReceiveCode . "' AND bytInvalidFlag = FALSE";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strReviseGetQuery, $objDB );
	if ( $lngResultNum )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$strMaxReviseCode = $objResult->maxrevise;
	}
	else
	{
		$strMaxReviseCode = "00";
	}
	$objDB->freeResult( $lngResultID );

	$aryQuery[] = "INSERT INTO m_receive (lngReceiveNo, lngRevisionNo, strReviseCode, ";	// 受注NO、リビジョン番号、リバイズコード
	$aryQuery[] = "strReceiveCode, lngInputUserCode, bytInvalidFlag, dtmInsertDate";		// 受注コード、入力者コード、無効フラグ、登録日
	$aryQuery[] = ") values (";
	$aryQuery[] = $sequence_m_receive . ", ";		// 1:受注番号
	$aryQuery[] = $lngMinRevisionNo . ", ";			// 2:リビジョン番号
	$aryQuery[] = "'" . $strMaxReviseCode . "', ";	// 3:リバイスコード
	$aryQuery[] = "'" . $strReceiveCode . "', ";	// 4:受注コード．
	$aryQuery[] = $objAuth->UserCode . ", ";		// 5:入力者コード
	$aryQuery[] = "false, ";						// 6:無効フラグ
	$aryQuery[] = "now()";							// 7:登録日
	$aryQuery[] = ")";

	unset($strQuery);
	$strQuery = implode("\n", $aryQuery );

	if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
	{
		fncOutputError ( 9051, DEF_FATAL, "削除処理に伴うマスタ処理失敗", TRUE, "", $objDB );
	}
	$objDB->freeResult( $lngResultID );

// 2004.03.24 suzukaze update start
	// 同じ受注Noを指定しているデータの受注コードを define にて指定している文字を付与して再利用できるように更新する
	$strNewReceiveCode = DEF_RECEIVE_DEL_START . $strReceiveCode . DEF_RECEIVE_DEL_END;
	$strQuery = "UPDATE m_Receive SET strReceiveCode = '" . $strNewReceiveCode . "' WHERE strReceiveCode = '" . $strReceiveCode . "'";
	if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
	{
		fncOutputError ( 9051, DEF_FATAL, "削除処理に伴う受注Noの変名処理に失敗しました。", TRUE, "", $objDB );
	}
	$objDB->freeResult( $lngResultID );
// 2004.03.24 suzukaze update end

	// トランザクションコミット
	$objDB->transactionCommit();

	// 削除確認画面の表示
	$aryDeleteData = $aryReceiveResult;
	$aryDeleteData["strAction"] = "/so/search/index.php?strSessionID=";
	$aryDeleteData["strSessionID"] = $aryData["strSessionID"];

	$aryDeleteData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "so/finish/remove_parts.tmpl" );

	// テンプレート生成
	$objTemplate->replace( $aryDeleteData );
	$objTemplate->complete();

	// HTML出力
	echo $objTemplate->strTemplate;

	$objDB->close();

	return true;
}

////////////////////////////////////////////////////////
////////////////////// 削除できない ////////////////////
////////////////////////////////////////////////////////
if ( $lngSalesCount )
{
	// 置換用文字列の設定
	for( $i = 0; $i < $lngSalesCount; $i++ )
	{
		$aryDetailData["strFuncType"] = "売上管理";
		$aryDetailData["strCode"] = $aryCode[$i]["lngsearchno"];

		// テンプレート読み込み
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "error/use/parts_detail.tmpl" );

		// テンプレート生成
		$objTemplate->replace( $aryDetailData );
		$objTemplate->complete();
		
		// HTML出力
		$aryDetail[] = $objTemplate->strTemplate;
	}

	$aryData["strMessageDetail"] = implode ("\n", $aryDetail );
	$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "error/use/parts.tmpl" );

	// テンプレート生成
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

	// HTML出力
	echo $objTemplate->strTemplate;

	$objDB->close();

	return true;

}


////////////////////////////////////////////////////////
//////////////////// 削除確認画面表示 //////////////////
////////////////////////////////////////////////////////
// 該当受注の状態が「申請中」の状態であれば
if ( $aryReceiveResult["lngreceivestatuscode"] == DEF_RECEIVE_APPLICATE )
{
	fncOutputError( 406, DEF_WARNING, "", TRUE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}

// 該当受注の状態が「締め済」の状態であれば
if ( $aryReceiveResult["lngreceivestatuscode"] == DEF_RECEIVE_CLOSED )
{
	fncOutputError( 404, DEF_WARNING, "", TRUE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}



// 取得データの調整
// レートタイプ
if ( $aryReceiveResult["lngmonetaryratecode"] and $aryReceiveResult["lngmonetaryratecode"] != DEF_MONETARY_YEN )
{
	$aryReceiveResult["strmonetaryratename"] = fncGetMasterValue(m_monetaryrateclass, lngMonetaryRateCode, strMonetaryRateName, $aryReceiveResult["lngmonetaryratecode"], '', $objDB);
}

$aryNewResult = fncSetReceiveHeadTabelData ( $aryReceiveResult );

// 言語の設定
if ( isset($aryData["lngLanguageCode"]) and  $aryData["lngLanguageCode"] == 0 )
{
	$aryTytle = $aryTableTytleEng;
}
else
{
	$aryTytle = $aryTableTytle;
}

// カラム名の設定
$aryHeadColumnNames = fncSetReceiveTabelName ( $aryTableViewHead, $aryTytle );
// カラム名の設定
$aryDetailColumnNames = fncSetReceiveTabelName ( $aryTableViewDetail, $aryTytle );

////////// 明細行の取得 ////////////////////

// 指定受注番号の受注明細データ取得用SQL文の作成
$strQuery = fncGetReceiveDetailNoToInfoSQL ( $aryData["lngReceiveNo"] );

// 明細データの取得
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

if ( $lngResultNum )
{
	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$aryReceiveDetailResult[] = $objDB->fetchArray( $lngResultID, $i );
	}
}
else
{
	$strMessage = fncOutputError( 503, DEF_WARNING, "受注番号に対する明細が存在しません", FALSE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}
$objDB->freeResult( $lngResultID );

for ( $i = 0; $i < count($aryReceiveDetailResult); $i++)
{
	$aryNewDetailResult[$i] = fncSetReceiveDetailTabelData ( $aryReceiveDetailResult[$i], $aryNewResult );

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "so/result/parts_detail.tmpl" );

	// テンプレート生成
	$objTemplate->replace( $aryDetailColumnNames );
	$objTemplate->replace( $aryNewDetailResult[$i] );
	$objTemplate->complete();

	// HTML出力
	$aryDetailTable[] = $objTemplate->strTemplate;
}

$aryNewResult["strSessionID"] = $aryData["strSessionID"];
if ( count($aryReceiveDetailResult) )
{
	$aryNewResult["strDetailTable"] = implode ("\n", $aryDetailTable );
}

$aryNewResult["strAction"] = "index3.php";
$aryNewResult["strSubmit"] = "submit";
$aryNewResult["strMode"] = "delete";

// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "so/result/parts2.tmpl" );

// テンプレート生成
$objTemplate->replace( $aryNewResult );
$objTemplate->replace( $aryHeadColumnNames );
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;


$objDB->close();
return true;

?>