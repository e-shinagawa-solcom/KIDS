<?
/** 
*	見積原価管理 詳細表示画面
*
*	@package   KIDS
*	@copyright Copyright (c) 2004, kuwagata 
*	@author    Kenji Chiba
*	@editor    Kazushi Saito 2009.08.30
*	@access    public
*	@version   1.00
*
*/
// index.php -> strSessionID	 -> ditail.php
// index.php -> lngEstimateNo	 -> ditail.php

	// 設定読み込み
	require ('conf.inc');
	require ( LIB_DEBUGFILE );

	// ライブラリ読み込み
	require (LIB_FILE);
	require (SRC_ROOT . "estimate/cmn/lib_e.php");

	require ( CLS_TABLETEMP_FILE );	// Temporary DB Object
	require ( LIB_ROOT . "tabletemp/excel2temp.php" );



	// DB接続
	$objDB   = new clsDB();
	$objAuth = new clsAuth();
	$objDB->open( "", "", "", "" );

	// GETデータ取得
	$aryData = $_GET;

	fncDebug( 'estimate_result_detail_01.txt', $aryData, __FILE__, __LINE__);


	$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	// ログインユーザーコードの取得
	$lngInputUserCode = $objAuth->UserCode;


	// 権限確認
	if ( !fncCheckAuthority( DEF_FUNCTION_E4, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}





	// 文字列チェック
	$aryCheck["strSessionID"]	 = "null:numenglish(32,32)";
	$aryCheck["lngEstimateNo"]	 = "null:number(0,2147483647)";

	$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryCheckResult, $objDB );

	if ( join ( "", $aryCheckResult ) )
	{
		fncOutputError ( 1502, DEF_WARNING, "", TRUE, "", $objDB );
	}

	// 共通受け渡しURL生成(セッションID、ページ、各検索条件)
	$strURL = fncGetURL( $aryData );






	// 通貨レート配列生成
	$aryRate = fncGetMonetaryRate( $objDB );
	$aryRate[DEF_MONETARY_YEN] = 1;


	// 見積原価HTML出力データ取得
	$aryEstimateData = fncGetEstimate( $aryData["lngEstimateNo"], $objDB );


	//fncDebug( 'es_detail.txt', $aryEstimateData, __FILE__, __LINE__);


	// 状態が「申請中」の場合
	if( $aryEstimateData["lngEstimateStatusCode"] == DEF_ESTIMATE_APPLICATE )
	{
		//fncOutputError( 1509, DEF_WARNING, "", TRUE, "", $objDB );
	}
	// 状態が「否認」の場合
	if( $aryEstimateData["lngEstimateStatusCode"] == DEF_ESTIMATE_DENIAL )
	{
		fncOutputError( 1510, DEF_WARNING, "", TRUE, "", $objDB );
	}





	$aryDetail = fncGetEstimateDetail( $aryData["lngEstimateNo"], $aryRate, $objDB );

fncDebug( 'estimate_result_detail.txt', $aryDetail, __FILE__, __LINE__);

	list ( $aryEstimateDetail, $aryCalculated, $aryHiddenString ) = fncGetEstimateDetailHtml( $aryDetail, "estimate/regist/plan_detail.tmpl", $objDB );

	// added start k.saito 2005.1.28
	// 固定費小計が取得されていない為、緊急対策で設定
	//$aryEstimateData["curFixedCostSubtotal"] = $aryCalculated["curFixedCostSubtotal"];
	// added end

fncDebug( 'estimate_result_detail.txt', $aryDetail, __FILE__, __LINE__);

	// 計算結果を見積原価配列に組み込む
	$aryEstimateData = array_merge ( $aryEstimateData, $aryCalculated );

	// 明細（売上）の生成
	list($aryEstimateDetailSales, $curFixedCostSales) = fncGetEstimateDetail_Sales_Html( $aryDetail, "estimate/regist/plan_detail_sales.tmpl", $objDB );

	$aryEstimateDetail = array_merge ( $aryEstimateDetail, $aryEstimateDetailSales );
	$aryEstimateData["curFixedCostSales"]	= $curFixedCostSales;	// 1:固定費売上の合計

//		$aryHiddenString = array_merge ( $aryHiddenString, $aryHiddenStringSales );

fncDebug( 'estimate_result_detail_sales.txt', $aryEstimateDetailSales, __FILE__, __LINE__);

	unset ( $aryCalculated );
	unset ( $aryHiddenString );
	unset ( $aryRate );



	// テンポラリフラグ有効
	if( $aryEstimateData["blnTempFlag"] )
	{
		// 標準割合取得
		$aryEstimateData["curStandardRate"]		= fncGetEstimateDefault( $objDB );

		// 社内USドルレート取得
		$aryEstimateData["curConversionRate"]	= fncGetUSConversionRate( $aryEstimateData["dtmInsertDate"], $objDB );

		// Excel標準割合取得
//		$aryEstimateData["curStandardRate"]		= $aryEstimateData["curStandardRate"];
		// Excel社内USドルレート取得
//		$aryEstimateData["curConversionRate"]	= $aryEstimateData["curConversionRate"];
	}
	else
	{
		// 標準割合取得
		$aryEstimateData["curStandardRate"]		= fncGetEstimateDefault( $objDB );

		// 社内USドルレート取得
		$aryEstimateData["curConversionRate"]	= fncGetUSConversionRate( $aryEstimateData["dtmInsertDate"], $objDB );
	}



	// 計算結果を取得
	$aryEstimateData = fncGetEstimateCalculate( $aryEstimateData );

	// カンマ処理
	$aryEstimateData	= fncGetCommaNumber( $aryEstimateData );


	// ベーステンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "estimate/regist/plan_base.tmpl" );

	$aryData["strMessageJs"] = "<script type=\"text/javascript\" language=\"javascript\" src=\"/estimate/regist/detail_exstr.js\"></script>";


		// コメント
		$aryData["strRemarkDisp"]	= nl2br($aryEstimateData["strRemark"]);


	// ベーステンプレート生成
	$objTemplate->replace( $aryData );
	$objTemplate->replace( $aryEstimateData );
	$objTemplate->replace( $aryEstimateDetail );
	$objTemplate->complete();

	// HTML出力
	echo $objTemplate->strTemplate;
	//echo getArrayTable( $baseData, "TABLE" )

fncDebug( 'estimate_result_detail_01.txt', $objTemplate->strTemplate, __FILE__, __LINE__);


	$objDB->close();


	return TRUE;
?>
