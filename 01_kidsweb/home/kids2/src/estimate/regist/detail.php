<?
/** 
*	見積原価管理 詳細表示画面
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2004, AntsBizShare 
*	@author    Kenji Chiba
*	@access    public
*	@version   1.00
*
*/
// index.php -> strSessionID	 -> ditail.php
// index.php -> lngEstimateNo	 -> ditail.php


	// 設定読み込み
	include_once('conf.inc');
	require_once( LIB_DEBUGFILE );


	// ライブラリ読み込み
	require (LIB_FILE);
	require (SRC_ROOT . "estimate/cmn/lib_e.php");

	// DB接続
	$objDB   = new clsDB();
	$objAuth = new clsAuth();
	$objDB->open( "", "", "", "" );

	// GETデータ取得
	$aryData = $_GET;

fncDebug( 'bbb.txt', $aryData, __FILE__, __LINE__);
exit;


	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

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

	// 見積原価HTML出力データ取得
	$aryEstimateData = fncGetEstimate( $aryData["lngEstimateNo"], $objDB );

	// 通貨レート配列生成
	$aryRate = fncGetMonetaryRate( $objDB );
	$aryRate[DEF_MONETARY_YEN] = 1;


		$aryDetail = fncGetEstimateDetail( $aryData["lngEstimateNo"], $aryData, $objDB );

		list ( $aryEstimateDetailStock, $aryCalculated ) = fncGetEstimateDetailHtml( $aryDetail, "estimate/regist/plan_detail.tmpl", $objDB );
		unset ( $aryCalculated );

		// 明細（売上）の生成
//		list($aryEstimateDetailSales, $curFixedCostSales) = fncGetEstimateDetail_Sales_Html( $aryDetail, "estimate/regist/plan_detail_sales.tmpl", $objDB );

//		$aryEstimateDetail	= array_merge( $aryEstimateDetailStock, $aryEstimateDetailSales );


	// 標準割合取得
	$aryEstimateData["curStandardRate"] = fncGetEstimateDefault( $objDB );

	// 社内USドルレート取得
	$aryEstimateData["curConversionRate"] = fncGetUSConversionRate( $aryEstimateData["dtmInsertDate"], $objDB );

	// 計算結果を取得
	$aryEstimateData = fncGetEstimateCalculate( $aryEstimateData );

	$aryEstimateData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];



	// ベーステンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "estimate/regist/plan_base.tmpl" );

	// ベーステンプレート生成
	$objTemplate->replace( $aryEstimateData );
	$objTemplate->replace( $aryEstimateDetail );
	$objTemplate->complete();

	// HTML出力
	echo $objTemplate->strTemplate;
	//echo getArrayTable( $baseData, "TABLE" )

	$objDB->close();


	return TRUE;
?>
