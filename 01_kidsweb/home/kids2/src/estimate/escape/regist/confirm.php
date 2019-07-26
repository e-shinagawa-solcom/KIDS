<?
/** 
*	見積管理 確認画面
*
*	@package   KIDS
*	@copyright Copyright &copy; 2004, AntsBizShare 
*	@author    Kenji Chiba
*	@access    public
*	@version   1.00
*
*	更新履歴
*	2004.04.14	見積が顧客であった場合に、表示・非表示を切り替えられないバグの修正
*
*/
// edit.php -> strSessionID			-> confirm.php
// edit.php -> lngFunctionCode		-> confirm.php
// edit.php -> lngEstimateNo		-> confirm.php見積原価番号
// edit.php -> strProductCode		-> confirm.php製品コード
// edit.php -> strActionName		-> confirm.php実行処理名(confirm or temporary)
// edit.php -> lngWorkflowOrderCode	-> confirm.php承認ルート
// edit.php -> aryDitail[仕入科目][明細行][lngStockSubjectCode]	-> confirm.php
// edit.php -> aryDitail[仕入科目][明細行][lngStockItemCode]	-> confirm.php
// edit.php -> aryDitail[仕入科目][明細行][bytPayOffTargetFlag]	-> confirm.php
// edit.php -> aryDitail[仕入科目][明細行][lngCustomerCode]		-> confirm.php
// edit.php -> aryDitail[仕入科目][明細行][bytPercentInputFlag]	-> confirm.php
// edit.php -> aryDitail[仕入科目][明細行][lngProductQuantity]	-> confirm.php
// edit.php -> aryDitail[仕入科目][明細行][curProductRate]		-> confirm.php
// edit.php -> aryDitail[仕入科目][明細行][curProductPrice]		-> confirm.php
// edit.php -> aryDitail[仕入科目][明細行][curSubTotalPrice]	-> confirm.php
// edit.php -> aryDitail[仕入科目][明細行][strNote]				-> confirm.php
// edit.php -> aryDitail[仕入科目][明細行][lngMonetaryUnitCode]	-> confirm.php
// edit.php -> aryDitail[仕入科目][明細行][curSubTotalPriceJP]	-> confirm.php
// edit.php -> aryDitail[仕入科目][明細行][curConversionRate]	-> confirm.php

// 実行へ
// confirm.php -> strSessionID			-> action.php
// confirm.php -> lngFunctionCode		-> action.php
// confirm.php -> lngEstimateNo			-> action.php見積原価番号
// confirm.php -> strProductCode		-> action.php製品コード
// confirm.php -> strActionName			-> action.php実行処理名(confirm or temporary)
// confirm.php -> lngWorkflowOrderCode	-> action.php承認ルート
// confirm.php -> aryDitail[仕入科目][明細行][lngStockSubjectCode]	-> action.php
// confirm.php -> aryDitail[仕入科目][明細行][lngStockItemCode]		-> action.php
// confirm.php -> aryDitail[仕入科目][明細行][bytPayOffTargetFlag]	-> action.php
// confirm.php -> aryDitail[仕入科目][明細行][lngCustomerCode]		-> action.php
// confirm.php -> aryDitail[仕入科目][明細行][bytPercentInputFlag]	-> action.php
// confirm.php -> aryDitail[仕入科目][明細行][lngProductQuantity]	-> action.php
// confirm.php -> aryDitail[仕入科目][明細行][curProductRate]		-> action.php
// confirm.php -> aryDitail[仕入科目][明細行][curProductPrice]		-> action.php
// confirm.php -> aryDitail[仕入科目][明細行][curSubTotalPrice]		-> action.php
// confirm.php -> aryDitail[仕入科目][明細行][strNote]				-> action.php
// confirm.php -> aryDitail[仕入科目][明細行][lngMonetaryUnitCode]	-> action.php
// confirm.php -> aryDitail[仕入科目][明細行][curSubTotalPriceJP]	-> action.php
// confirm.php -> aryDitail[仕入科目][明細行][curConversionRate]	-> action.php




	// 設定読み込み
	include_once('conf.inc');
	require_once( LIB_DEBUGFILE );



	//echo mb_internal_encoding();
	//mb_http_output( "EUC-JP" );
	//echo mb_http_output();



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
	/*
	if ( $_GET )
	{
		$aryData = $_GET;
	}
	else
	{
		$aryData = $_POST;
	}
	*/
	$aryData = $_REQUEST;

	// 製品コード5桁化に伴って、4桁の見積ファイルがアップロードされた際に
	// 5桁に拡張してマスタ検索を実施する
	if (strlen($aryData["strProductCode"]) == 4)
	{
		$aryData["strProductCode"] = '0'.$aryData["strProductCode"];
	}

		// Temp配列
		$g_aryTemp	= $aryData;

	//fncDebug( 'temp_renew.txt', $aryData, __FILE__, __LINE__);



	$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

	$aryDetail = $aryData["aryDetail"];
	if ( !is_Array( $aryData ) )
	{
		$aryDetail = $aryData["strDetailData"];
	}
	unset ( $aryData["aryDetail"] );
	//echo getArrayTable( $aryDetail[0][1], "TABLE" );exit;


	//fncDebug( 'estimate_regist_confirm.txt', $aryData, __FILE__, __LINE__);

	// ---------------------------------------------------------------------------------------------------------------------------------------

	$aryCheck["strSessionID"]			= "null:numenglish(32,32)";
	$aryCheck["lngFunctionCode"]		= "null:number(" . DEF_FUNCTION_E1 . "," . DEF_FUNCTION_E5 . ")";
	$aryCheck["lngWorkflowOrderCode"]	= "null:number(0,32767)";


	//$aryData["bytInvalidFlag_Error"]     = "visibility:hidden;";

	$lngErrorCount = 0;


	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );
	$lngUserCode = $objAuth->UserCode;

	// 権限確認
	//////////////////////////////////////////////////////////////////////////
	// 見積登録の場合
	//////////////////////////////////////////////////////////////////////////
	if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E1 && fncCheckAuthority( DEF_FUNCTION_E1, $objAuth ) )
	{
		$aryCheck["strProductCode"] = "null:numenglish(1,6)";
	}

	//////////////////////////////////////////////////////////////////////////
	// 見積修正の場合
	//////////////////////////////////////////////////////////////////////////
	elseif ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E3 && fncCheckAuthority( DEF_FUNCTION_E3, $objAuth ) )
	{
		$aryCheck["lngEstimateNo"] = "null:number(1,2147483647)";

	}

	//////////////////////////////////////////////////////////////////////////
	// 見積削除の場合
	//////////////////////////////////////////////////////////////////////////
	elseif ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E4 && fncCheckAuthority( DEF_FUNCTION_E4, $objAuth ) )
	{
		$aryCheck["lngEstimateNo"] = "null:number(1,2147483647)";
		unset ( $aryCheck["lngWorkflowOrderCode"] );
	}

	//////////////////////////////////////////////////////////////////////////
	// それ以外(権限ERROR)
	//////////////////////////////////////////////////////////////////////////
	else
	{
		fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}


	// 文字列チェック
	$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
	//fncPutStringCheckError( $aryCheckResult, $objDB );

	//fncDebug( 'estimate_regist_confirm.txt', $aryCheckResult, __FILE__, __LINE__);



	// ファイルテンポラリ処理以外の場合
	if( !$g_aryTemp["bytTemporaryFlg"] )
	{
		//////////////////////////////////////////////////////////////////////////
		// 登録の場合の既にデータがあるかチェック
		//////////////////////////////////////////////////////////////////////////
		if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E1 )
		{
			if ( $aryData["strProductCode"] == "" )
			{
				fncOutputError ( 1505, DEF_WARNING, "見積原価書を表示できません。", TRUE, "", $objDB );
			}

			list ( $lngResultID, $lngResultNum ) = fncQuery( "SELECT * FROM m_Estimate WHERE strProductCode = '" . $aryData["strProductCode"] . "'", $objDB );

			if ( $lngResultNum > 0 )
			{
				$objDB->freeResult( $lngResultID );
				fncOutputError ( 1501, DEF_WARNING, "既に見積原価の登録のある製品です。", TRUE, "", $objDB );
			}
		}
	}


	//////////////////////////////////////////////////////////////////////////
	// 削除の場合、現状の見積原価データ取得
	//////////////////////////////////////////////////////////////////////////
	if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E4 )
	{
		//-------------------------------------------------------------------------
		// ■「製品」にログインユーザーが属しているかチェック
		//-------------------------------------------------------------------------
		$strFncFlag = "ES";
		$blnCheck = fncCheckInChargeProduct( $aryData["lngEstimateNo"], $lngUserCode, $strFncFlag, $objDB );

		// ユーザーが対象製品に属していない場合
		if( !$blnCheck )
		{
			fncOutputError( 9060, DEF_WARNING, "", TRUE, "", $objDB );
		}



		// 通貨レート配列生成
		$aryRate = fncGetMonetaryRate( $objDB );
		$aryRate[DEF_MONETARY_YEN] = 1;

		// 見積原価HTML出力データ取得
		$aryEstimateData = fncGetEstimate( $aryData["lngEstimateNo"], $objDB );
		$aryDetail = fncGetEstimateDetail( $aryData["lngEstimateNo"], $aryRate, $objDB );


		// コメント（バッファ）取得
		$strBuffRemark	= $aryEstimateData["strRemark"];


	//fncDebug( 'es_delete.txt', $aryEstimateData, __FILE__, __LINE__);

		list ( $aryEstimateDetail, $aryCalculated, $aryHiddenString ) = fncGetEstimateDetailHtml( $aryDetail, "estimate/regist/plan_detail.tmpl", $objDB );


		// 計算結果を見積原価配列に組み込む
		$aryEstimateData = array_merge ( $aryEstimateData, $aryCalculated );


		unset ( $aryDetail );
		unset ( $aryCalculated );

		// (ログインユーザーが入力したものかつ仮保存状態)以外のもの、
		// または、申請中のものは、修正不可としてエラー出力
		if ( !( ( $aryEstimateData["bytDecisionFlag"] == "f" && $aryEstimateData["lngInputUserCode"] == $objAuth->UserCode ) || $aryEstimateData["lngEstimateStatusCode"] != DEF_ESTIMATE_APPLICATE ) )
		{
			fncOutputError ( 1503, DEF_WARNING, "", TRUE, "", $objDB );
		}



		// テンポラリ処理の場合
		if( $aryEstimateData["blnTempFlag"] )
		{
			// 標準割合取得
			$aryEstimateData["curStandardRate"] = fncGetEstimateDefault( $objDB );

			// 社内USドルレート取得
			$aryEstimateData["curConversionRate"] = fncGetUSConversionRate( $aryEstimateData["dtmInsertDate"], $objDB );

			// Excel標準割合取得
	//		$aryEstimateData["curStandardRate"]		= $aryEstimateData["curStandardRate"];
			// Excel社内USドルレート取得
	//		$aryEstimateData["curConversionRate"]	= $aryEstimateData["curConversionRate"];
		}
		// 通常
		else
		{
			// 標準割合取得
			$aryEstimateData["curStandardRate"] = fncGetEstimateDefault( $objDB );

			// 社内USドルレート取得
			$aryEstimateData["curConversionRate"] = fncGetUSConversionRate( $aryEstimateData["dtmInsertDate"], $objDB );
		}


		// 計算結果を取得
		$aryEstimateData = fncGetEstimateCalculate( $aryEstimateData );

	//fncDebug( 'es_delete.txt', $aryEstimateData, __FILE__, __LINE__);


	}
	// --------------------------------------------------------------------------------------------------------------------------------------------------
	// 削除以外でエラーが無ければデータ取得
	// --------------------------------------------------------------------------------------------------------------------------------------------------
	elseif ( $lngErrorCount < 1 )
	{


		// 修正の場合、修正権限チェック
		if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E3 )
		{
			$aryEstimateData = fncGetEstimate( $aryData["lngEstimateNo"], $objDB );

			// (ログインユーザーが入力したものかつ仮保存状態)以外のもの、
			// または、申請中のものは、修正不可としてエラー出力


			// コメント（バッファ）取得
			$strBuffRemark	= $aryEstimateData["strRemark"];


	/*
			if ( !( ( $aryEstimateData["bytDecisionFlag"] == "f" && $aryEstimateData["lngInputUserCode"] == $objAuth->UserCode ) || $aryEstimateData["lngEstimateStatusCode"] != DEF_ESTIMATE_APPLICATE ) )
			{
				fncOutputError ( 1503, DEF_WARNING, "", TRUE, "", $objDB );
			}
			unset ( $aryEstimateData );
	*/
		}


//fncDebug( 'es_renew.txt', $aryEstimateData, __FILE__, __LINE__);



		/*-----------------------------------------------------------------------*/
		// 製品情報取得
		$aryEstimateData = fncGetProduct( $aryData["strProductCode"], $objDB, $lngUserCode );


fncDebug( 'estimate_regist_confirm_03.txt', $aryEstimateData, __FILE__, __LINE__);



//fncDebug( 'temp_renew.txt', $aryEstimateData, __FILE__, __LINE__);


		// 登録、修正時の確認画面では作成日は実行日とする
		$aryEstimateData["dtmInsertDate"]	= date("Y/m/d");


		// テンポラリ処理の場合
		if( $aryEstimateData["blnTempFlag"] )
		{
			// 標準割合取得
			$aryEstimateData["curStandardRate"]	= fncGetEstimateDefault( $objDB );

			// 社内USドルレート取得
			$aryEstimateData["curConversionRate"]	= fncGetUSConversionRate( $aryEstimateData["dtmInsertDate"], $objDB );

			// Excel標準割合取得
	//		$aryEstimateData["curStandardRate"]		= $aryEstimateData["curStandardRate"];
			// Excel社内USドルレート取得
	//		$aryEstimateData["curConversionRate"]	= $aryEstimateData["curConversionRate"];
		}
		// ファイルテンポラリ処理の場合
		else if( $g_aryTemp["bytTemporaryFlg"] )
		{
			// コメント（バッファ）取得
			$strBuffRemark	= $aryEstimateData["strRemark"];


			// 標準割合取得
			$aryEstimateData["curStandardRate"]	= fncGetEstimateDefault( $objDB );

			// 社内USドルレート取得
			$aryEstimateData["curConversionRate"]	= fncGetUSConversionRate( $aryEstimateData["dtmInsertDate"], $objDB );

			// Excel標準割合取得
	//		$aryEstimateData["curStandardRate"]		= $g_aryTemp["curStandardRate"];
			// Excel社内USドルレート取得
	//		$aryEstimateData["curConversionRate"]	= $g_aryTemp["curConversionRate"];
		}
		// 通常
		else
		{
			// 標準割合取得
			$aryEstimateData["curStandardRate"]	= fncGetEstimateDefault( $objDB );

			// 社内USドルレート取得
			$aryEstimateData["curConversionRate"]	= fncGetUSConversionRate( $aryEstimateData["dtmInsertDate"], $objDB );
		}

fncDebug( 'estimate_regist_confirm_03.1.txt', $aryDetail, __FILE__, __LINE__);





	//	// 登録、修正時の確認画面では作成日は実行日とする
	//	$aryEstimateData["dtmInsertDate"]     = date("Y/m/d");

		// 見積原価計算明細HTML出力文字列取得
		list ( $aryEstimateDetail, $aryCalculated, $aryHiddenString ) = fncGetEstimateDetailHtml( $aryDetail, "estimate/regist/plan_detail.tmpl", $objDB );

		// 計算結果を見積原価配列に組み込む
		$aryEstimateData = array_merge ( $aryEstimateData, $aryCalculated );

//fncDebug( 'es_temp.txt', $aryHiddenString, __FILE__, __LINE__);

//		unset ( $aryDetail );
		unset ( $aryCalculated );

		// 明細（売上）の生成
		list($aryEstimateDetailSales, $curFixedCostSales, $aryHiddenStringSales) = fncGetEstimateDetail_Sales_Html( $aryDetail, "estimate/regist/plan_detail_sales.tmpl", $objDB );


		$aryEstimateDetail	= array_merge( $aryEstimateDetail, $aryEstimateDetailSales );
		$aryEstimateData["curFixedCostSales"]	= $curFixedCostSales;	// 1:固定費売上の合計
		$aryHiddenString	= array_merge( is_array($aryHiddenString)?$aryHiddenString:(array)$aryHiddenString, 	$aryHiddenStringSales );

fncDebug( 'estimate_retist_confirm_aryEstimateData.txt', $aryEstimateData, __FILE__, __LINE__);

		// 計算結果を取得
		$aryEstimateData = fncGetEstimateCalculate( $aryEstimateData );

//fncDebug( 'es_temp.txt', $aryEstimateData, __FILE__, __LINE__);
		/*-----------------------------------------------------------------------*/
	}



	// エラー項目表示処理
	list ( $aryData, $bytErrorFlag ) = getArrayErrorVisibility( $aryData, $aryCheckResult, $objDB );
	$lngErrorCount += $bytErrorFlag;


	//////////////////////////////////////////////////////////////////////////
	// 結果取得、出力処理
	//////////////////////////////////////////////////////////////////////////
	// 文字列チェックにエラーがある場合、入力画面に戻る

	//エラーが存在するかつ(確認ボタンによる表示または削除処理)の場合、エラー出力
	//if( $lngErrorCount > 0 && ( $aryData["lngActionCode"] == "confirm" || $aryData["lngFunctionCode"] == DEF_FUNCTION_E4 ) )
	if( $lngErrorCount > 0 && ( $aryData["strActionName"] == "confirm" || $aryData["lngFunctionCode"] == DEF_FUNCTION_E4 ) )
	{
		fncOutputError ( 1502, DEF_WARNING, "", TRUE, "", $objDB );
		exit;


	}
	//エラーがあったら
	elseif( $lngErrorCount > 0 )
	{
		//echo getArrayTable( $aryData, "TABLE" );exit;
		echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=euc-jp\">\n";
		echo "<form action=\"/estimate/regist/edit.php\" method=\"POST\">\n";
		echo getArrayTable( fncToHTMLString( $aryData ), "HIDDEN" );
		echo getArrayTable( fncToHTMLString( $aryEstimateData ), "HIDDEN" );
		echo getArrayTable( fncToHTMLString( $aryEstimateDetail ), "HIDDEN" );
		echo "</form>\n";
		echo "<script language=\"javascript\">document.forms[0].submit();</script>";
	}
	//エラーがなかったら
	else
	{

//fncDebug( 'estimate_regist_confirm_02.txt', $aryData["strActionName"], __FILE__, __LINE__);

		// 確認ではなかったら、送信・もどるボタン表示
		if ( $aryData["strActionName"] != "confirm" )
		{
			$aryData["bytReturnFlag"] = "true";

			$aryHiddenString[] = getArrayTable( fncToHTMLString( $aryData ), "HIDDEN" );

fncDebug( 'estimate_regist_confirm_04.txt', $aryHiddenString, __FILE__, __LINE__);


			$strHiddenString = join( "", $aryHiddenString );
			unset ( $aryHiddenString );

			/*
			$aryForm[] = "<form action=\"action.php\" method=POST>\n";
			$aryForm[] = $strHiddenString;
			$aryForm[] = "<input type=submit value='実行'>\n";
			$aryForm[] = "</form>\n";
			$aryForm[] = "<form action=\"edit.php\" method=POST>\n";
			$aryForm[] = $strHiddenString;
			$aryForm[] = "<input type=hidden name=bytReturnFlag value=true>\n";
			$aryForm[] = "<input type=submit value='もどる'>\n";
			$aryForm[] = "</form>\n";
			*/





			/*---------------------------------------------------------------------
				FORM生成
			---------------------------------------------------------------------*/
			$aryForm[] = "<form name=frmAction action=\"action.php\" method=POST>\n";
			$aryForm[] = $strHiddenString;

			// テンポラリ処理
			if( $aryEstimateData["blnTempFlag"] )
			{
				// テンポラリフラグ
				$aryForm[] = "<input type=\"hidden\" name=\"blnTempFlag\"	value=\"" .$aryEstimateData["blnTempFlag"]. "\" />\n";
				// コメント
				$aryForm[] = "<input type=\"hidden\" name=\"strRemark\"	value=\"" .$strBuffRemark. "\" />\n";
			}
			// ファイルテンポラリ処理
			else if( $aryData["bytTemporaryFlg"] )
			{
				// テンポラリフラグ
	//			$aryForm[] = "<input type=\"hidden\" name=\"bytTemporaryFlg\"	value=\"" .$aryData["bytTemporaryFlg"]. "\" />\n";
				// コメント
	//			$aryForm[] = "<input type=\"hidden\" name=\"strRemark\"	value=\"" .$strBuffRemark. "\" />\n";
				// Excel標準割合
	//			$aryForm[] = "<input type=\"hidden\" name=\"curStandardRate\"	value=\"" .$aryData["curStandardRate"]. "\" />\n";
				// Excel社内USドルレート
	//			$aryForm[] = "<input type=\"hidden\" name=\"curConversionRate\"	value=\"" .$aryData["curConversionRate"]. "\" />\n";
			}

			$aryForm[] = "</form>\n";

			$aryForm[] = "<form name=frmEdit action=\"edit.php\" method=POST>\n";
			$aryForm[] = $strHiddenString;

			// テンポラリ処理
			if( $aryEstimateData["blnTempFlag"] )
			{
				// テンポラリフラグ
				$aryForm[] = "<input type=\"hidden\" name=\"blnTempFlag\"	value=\"" .$aryEstimateData["blnTempFlag"]. "\" />\n";
				// コメント
				$aryForm[] = "<input type=\"hidden\" name=\"strRemark\"	value=\"" .$strBuffRemark. "\" />\n";
			}
			// ファイルテンポラリ処理
			else if( $aryData["bytTemporaryFlg"] )
			{
				// テンポラリフラグ
	//			$aryForm[] = "<input type=\"hidden\" name=\"bytTemporaryFlg\"	value=\"" .$aryData["bytTemporaryFlg"]. "\" />\n";
				// コメント
	//			$aryForm[] = "<input type=\"hidden\" name=\"strRemark\"	value=\"" .$astrBuffRemark. "\" />\n";
				// Excel標準割合
	//			$aryForm[] = "<input type=\"hidden\" name=\"curStandardRate\"	value=\"" .$aryData["curStandardRate"]. "\" />\n";
				// Excel社内USドルレート
	//			$aryForm[] = "<input type=\"hidden\" name=\"curConversionRate\"	value=\"" .$aryData["curConversionRate"]. "\" />\n";
			}

			$aryForm[] = "<input type=hidden name=bytReturnFlag value=true>\n";
			$aryForm[] = "</form>\n";





			$aryEstimateData["FORM"] = join ( "", $aryForm );
			unset ( $aryForm );
		}

		unset ( $strHiddenString );

		$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

		// 削除の場合
		if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E4 )
		{
			$objTemplate = new clsTemplate();
			$objTemplate->getTemplate( "estimate/regist/plan_button_delete.tmpl" );

			$objTemplate->complete();
			$aryData["BUTTON"] = $objTemplate->strTemplate;
			$aryData["strMessageJs"] = "<script type=\"text/javascript\" language=\"javascript\" src=\"/estimate/regist/confirm_delete_exstr.js\"></script>";

			$aryData["strScrollType"] = "ScrollAuto";
		}
		// 登録・修正の場合
		else if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E1 or $aryData["lngFunctionCode"] == DEF_FUNCTION_E3 )
		{
			if ( $aryData["strActionName"] != "confirm" )
			{
				$objTemplate = new clsTemplate();
				$objTemplate->getTemplate( "estimate/regist/plan_button_regist.tmpl" );

				$objTemplate->complete();
				$aryData["BUTTON"] = $objTemplate->strTemplate;
				$aryData["strMessageJs"] = "<script type=\"text/javascript\" language=\"javascript\" src=\"/estimate/regist/confirm_exstr.js\"></script>";
			}
			else
			{
				$aryData["strMessageJs"] = "<script type=\"text/javascript\" language=\"javascript\" src=\"/estimate/regist/detail_exstr.js\"></script>";
			}
		}

	/*
	// 2004.10.05 suzukaze update start
		if ( $aryData["strActionName"] != "confirm" )
		{
			$aryData["strScrollType"] = "ScrollAuto";
		}
		else
		{
			$aryData["strScrollType"] = "ScrollHidden";
		}
	// 2004.10.05 suzukaze update end
	*/


		// コメント <br /> 付加
		$aryData["strRemarkDisp"]	= nl2br($strBuffRemark);

	 	// カンマ処理
		$aryEstimateData	= fncGetCommaNumber( $aryEstimateData );

		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "estimate/regist/plan_base.tmpl" );

		// 置き換え
	// 2004.10.05 tomita update start
		$objTemplate->replace( $aryData );
	// 2004.10.05 tomita update end

	//echo getArrayTable( $aryData, "TABLE" );exit;
		$objTemplate->replace( $aryEstimateData );
		$objTemplate->replace( $aryEstimateDetail );

		$objTemplate->complete();
		echo $objTemplate->strTemplate;

fncDebug( 'estimate_regist_confirm.txt', $objTemplate->strTemplate, __FILE__, __LINE__);

// debug
//fncDebug( 'es_detial.txt', $aryData, __FILE__, __LINE__);
//fncDebug( 'es_post.txt', $_REQUEST, __FILE__, __LINE__);

	}


	unset ( $aryEstimateData );
	unset ( $aryEstimateDetailData );
	unset ( $aryData );
	unset ( $g_aryTemp );


	$objDB->close();


	return TRUE;
?>


