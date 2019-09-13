<?php

// ----------------------------------------------------------------------------
/**
*       納品書詳細
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
*         ・指定納品伝票番号データの詳細表示処理
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
	require( LIB_DEBUGFILE );
	
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

	// 文字列チェック
	$aryCheck["strSessionID"] = "null:numenglish(32,32)";
	$aryCheck["lngSlipNo"]	  = "null:number(0,10)";

	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	// 権限確認
	// 602 売上管理（売上検索）
	if ( !fncCheckAuthority( DEF_FUNCTION_SC2, $objAuth ) )
	{
		fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}
	// 604 売上管理（詳細表示）
	if ( !fncCheckAuthority( DEF_FUNCTION_SC4, $objAuth ) )
	{
		fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	//詳細画面の表示
	$lngSlipNo = $aryData["lngSlipNo"];

	// 指定納品伝票番号の納品書データ取得用SQL文の作成
	$strQuery = fncGetSlipHeadNoToInfoSQL ( $lngSlipNo );

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
			fncOutputError( 603, DEF_ERROR, "該当データの取得に失敗しました", TRUE, "../sc/search2/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
	}
	else
	{
		fncOutputError( 603, DEF_ERROR, "データが異常です", TRUE, "../sc/search2/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
	}

	$objDB->freeResult( $lngResultID );

	// 取得データを表示用に整形
	$aryNewResult = fncSetSlipHeadTableData ( $aryResult );

//fncDebug('sc_result_index2.txt', $aryNewResult, __FILE__, __LINE__);

	// ヘッダ部のカラム名の設定（キーの頭に"CN"を付与する）
	$aryHeadColumnNames_CN = fncAddColumnNameArrayKeyToCN ( $aryHeadColumnNames );
	// 詳細部のカラム名の設定（キーの頭に"CN"を付与する）
	$aryDetailColumnNames_CN = fncAddColumnNameArrayKeyToCN ( $aryDetailColumnNames );

	////////// 明細行の取得 ////////////////////

	// 指定納品伝票番号の売上明細データ取得用SQL文の作成
	$strQuery = fncGetSlipDetailNoToInfoSQL ( $lngSlipNo );

	// 明細データの取得
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$aryDetailResult[] = $objDB->fetchArray( $lngResultID, $i );
		}
	}
	else
	{
		$strMessage = fncOutputError( 603, DEF_WARNING, "納品伝票番号に対する明細情報が見つかりません。", FALSE, "../sc/search2/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
	}

	$objDB->freeResult( $lngResultID );

	for ( $i = 0; $i < count($aryDetailResult); $i++)
	{
		$aryNewDetailResult[$i] = fncSetSlipDetailTableData ( $aryDetailResult[$i], $aryNewResult );

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

	$aryNewResult["strDetailTable"] = implode ("\n", $aryDetailTable );

	$aryNewResult["strAction"] = "index2.php";
	$aryNewResult["strMode"] = "detail";

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

?>