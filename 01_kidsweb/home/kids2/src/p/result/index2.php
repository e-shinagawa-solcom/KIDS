<?php

// ----------------------------------------------------------------------------
/**
*       商品管理  詳細
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
*         ・指定商品番号データの詳細表示処理
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
	require (LIB_ROOT . "libdebug.php" );
	require (SRC_ROOT . "p/cmn/lib_ps1.php");
	require (SRC_ROOT . "p/cmn/column.php");

	// DB接続
	$objDB   = new clsDB();
	$objAuth = new clsAuth();
	$objCache = new clsCache();
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

	// 文字列チェック
	$aryCheck["strSessionID"] = "null:numenglish(32,32)";
	$aryCheck["lngProductNo"] = "null:number(0,10)";
	// $aryResult = fncAllCheck( $aryData, $aryCheck );
	// fncPutStringCheckError( $aryResult, $objDB );

	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	// 権限確認
	// 302 商品管理（商品検索）
	if ( !fncCheckAuthority( DEF_FUNCTION_P2, $objAuth ) )
	{
		fncOutputError ( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}
	// 304 商品管理（詳細表示）
	if ( !fncCheckAuthority( DEF_FUNCTION_P4, $objAuth ) )
	{
		fncOutputError ( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	//詳細画面の表示

	$lngProductNo = $aryData["lngProductNo"];

	// 指定商品番号の商品データ取得用SQL文の作成
	$strQuery = fncGetProductNoToInfoSQL ( $lngProductNo );

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

//fncDebug("lib_ps.txt", $aryResult, __FILE__, __LINE__);

	// 取得データの調整
	$aryNewResult = fncSetProductTableData ( $aryResult, $objDB, $objCache );

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

	$aryNewResult["strAction"] = "index2.php";
	$aryNewResult["strMode"] = "detail";

fncDebug("lib_ps.txt", $aryColumnNames, __FILE__, __LINE__);


	// 帳票出力対応
	// 表示対象が削除データの場合はプレビューボタンを表示しない
	// なお権限を持ってない場合もプレビューボタンを表示しない
	if ( !$aryResult["bytInvalidFlag"] and fncCheckAuthority( DEF_FUNCTION_LO1, $objAuth ) && $aryResult["lngproductstatuscode"] != DEF_PRODUCT_APPLICATE )
	{
		$aryNewResult["strPreviewAction"] = "../../list/result/frameset.php?strSessionID=" . $aryData["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_PRODUCT . "&strReportKeyCode=" . $lngProductNo . "&bytCopyFlag=TRUE";

		$aryNewResult["listview"] = 'visible';
	}
	else
	{
		$aryNewResult["listview"] = 'hidden';
	}



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
