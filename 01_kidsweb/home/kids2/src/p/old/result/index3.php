<?php

// ----------------------------------------------------------------------------
/**
*       商品管理  削除
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
*         ・指定商品番号データの削除処理
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------



// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (LIB_ROOT . "clscache.php" );
require (SRC_ROOT . "p/cmn/lib_ps.php");
require (SRC_ROOT . "p/cmn/lib_ps1.php");
require (SRC_ROOT . "p/cmn/column.php");

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
if ( !isset($aryData["lngProductNo"]) )
{
	fncOutputError ( 9061, DEF_ERROR, "データ異常です。", TRUE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}

// 文字列チェック
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryCheck["lngProductNo"]	  = "null:number(0,10)";
// $aryResult = fncAllCheck( $aryData, $aryCheck );
// fncPutStringCheckError( $aryResult, $objDB );

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ログインユーザーコードの取得
$lngInputUserCode = $objAuth->UserCode;



// 権限確認
// 302 商品管理（商品検索）
if ( !fncCheckAuthority( DEF_FUNCTION_P2, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}
// 307 商品管理（商品削除）
if ( !fncCheckAuthority( DEF_FUNCTION_P7, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}



//-------------------------------------------------------------------------
// ■「製品」にログインユーザーが属しているかチェック
//-------------------------------------------------------------------------
$strFncFlag = "P";
$blnCheck = fncCheckInChargeProduct( $aryData["lngProductNo"], $lngInputUserCode, $strFncFlag, $objDB );

// ユーザーが対象製品に属していない場合
if( !$blnCheck )
{
	fncOutputError( 9060, DEF_WARNING, "", TRUE, "", $objDB );
}




// 削除対象の製品コードの商品情報取得
$strQuery = fncGetProductNoToInfoSQL ( $aryData["lngProductNo"] );

list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
if ( $lngResultNum == 1 )
{
	$aryProductResult = $objDB->fetchArray( $lngResultID, 0 );
}
else
{
	fncOutputError( 9061, DEF_ERROR, "", TRUE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}
$objDB->freeResult( $lngResultID );

// var_dump( $aryData );
// exit;

////////////////////////////////////////////////////////
////////////////////// 削除確認処理 ////////////////////
////////////////////////////////////////////////////////
$strProductCode = $aryProductResult["strproductcode"];
$aryDeta["strMessageDetail"] = "";
// 削除対象を使用しているデータの確認
// 受注
$aryCode = fncGetDeleteCodeToMaster ( $strProductCode, 1, $objDB );
if ( $aryCode )
{
	$lngReceiveCount = count($aryCode);
	// 置換用文字列の設定
	for( $i = 0; $i < $lngReceiveCount; $i++ )
	{
		$aryDetailData["strFuncType"] = "受注管理";
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
}

// 発注
unset ( $aryCode );
unset ( $aryDetail );
$aryCode = fncGetDeleteCodeToMaster ( $strProductCode, 2, $objDB );
if ( $aryCode )
{
	$lngOrderCount = count($aryCode);
	// 置換用文字列の設定
	for( $i = 0; $i < $lngOrderCount; $i++ )
	{
		$aryDetailData["strFuncType"] = "発注管理";
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

	$aryData["strMessageDetail"] .= implode ("\n", $aryDetail );
}

// 売上
unset ( $aryCode );
unset ( $aryDetail );
$aryCode = fncGetDeleteCodeToMaster ( $strProductCode, 3, $objDB );
if ( $aryCode )
{
	$lngSalesCount = count($aryCode);
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

	$aryData["strMessageDetail"] .= implode ("\n", $aryDetail );
}

// 仕入
unset ( $aryCode );
unset ( $aryDetail );
$aryCode = fncGetDeleteCodeToMaster ( $strProductCode, 4, $objDB );
if ( $aryCode )
{
	$lngStockCount = count($aryCode);
	// 置換用文字列の設定
	for( $i = 0; $i < $lngStockCount; $i++ )
	{
		$aryDetailData["strFuncType"] = "仕入管理";
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

	$aryData["strMessageDetail"] .= implode ("\n", $aryDetail );
}

////////////////////////////////////////////////////////
////////////////////// 削除処理実行 ////////////////////
////////////////////////////////////////////////////////
// 削除確認ＯＫならば
if ( $aryData["strSubmit"] == "submit" and $aryData["strMessageDetail"] == "" )
{
	// トランザクション開始
	$objDB->transactionBegin();

	// 更新行の行レベルロック
	$strQuery = "SELECT lngProductNo FROM m_Product WHERE lngProductNo = " . $aryData["lngProductNo"] . " FOR UPDATE";

	if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
	{
		fncOutputError ( 9061, DEF_ERROR, "ロック処理に失敗しました。", TRUE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
	}
	$objDB->freeResult( $lngResultID );

	// 更新行のUPDATE
	$strQuery = "UPDATE m_Product SET bytInvalidFlag = true WHERE lngProductNo = " . $aryData["lngProductNo"];

	if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
	{
		fncOutputError ( 9061, DEF_ERROR, "削除処理に失敗しました。", TRUE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
	}
	$objDB->freeResult( $lngResultID );

	// コミット処理
	$objDB->transactionCommit();

	// 削除確認画面の表示
	$aryDeleteData = $aryProductResult;
	$aryDeleteData["strAction"] = "/p/search/index.php?strSessionID=";
	$aryDeleteData["strSessionID"] = $aryData["strSessionID"];

	$aryDeleteData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "p/finish/remove_parts.tmpl" );

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
if ( $aryData["strMessageDetail"] != "" )
{
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
$objCache = new clsCache();


// 指定商品番号の商品データ取得用SQL文の作成
$strQuery = fncGetProductNoToInfoSQL ( $aryData["lngProductNo"] );

// 詳細データの取得
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

if ( $lngResultNum )
{
	if ( $lngResultNum == 1 )
	{
		$aryResult = $objDB->fetchArray( $lngResultID, 0 );
	}
	else
	{
		fncOutputError( 303, DEF_ERROR, "該当データの取得に失敗しました", TRUE, "../p/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
	}
}
else
{
	fncOutputError( 303, DEF_ERROR, "データが異常です", TRUE, "../p/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}

$objDB->freeResult( $lngResultID );

// 取得データの調整
$aryNewResult = fncSetProductTableData ( $aryResult, $objDB, $objCache );




// 該当商品の状態が「申請中」の状態であれば
if ( $aryNewResult["lngproductstatuscode"] == DEF_PRODUCT_APPLICATE )
{
	fncOutputError( 308, DEF_WARNING, "", TRUE, "../p/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
}





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
$aryColumnNames = fncSetProductTabelName ( $aryTableView, $aryTytle );

$aryNewResult["strAction"] = "index3.php";
$aryNewResult["strSessionID"] = $aryData["strSessionID"];
$aryNewResult["strSubmit"] = "submit";
$aryNewResult["strMode"] = "delete";

// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "p/result/parts2.tmpl" );

// テンプレート生成
$objTemplate->replace( $aryNewResult );
$objTemplate->replace( $aryHeadColumnNames );
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;


$objDB->close();

$objCache->Release();

return true;

?>