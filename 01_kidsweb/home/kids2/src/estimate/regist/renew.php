<?
/** 
*	見積原価管理 データ入力画面（更新画面用）
*
*	@package   KIDS
*	@copyright Copyright &copy; 2004, AntsBizShare 
*	@author    Kenji Chiba
*	@access    public
*	@version   1.00
*
*/
// 新規登録
// index.php -> strSessionID           -> renew.php
// index.php -> lngFunctionCode        -> renew.php
//
// 修正
// result/index.php -> strSessionID		-> renew.php
// result/index.php -> lngFunctionCode	-> renew.php
// result/index.php -> lngEstimateNo	-> renew.php
//
// 確認画面からの戻り
// confirm.php -> strSessionID           -> renew.php
// confirm.php -> lngFunctionCode        -> renew.php
// confirm.php -> lngEstimateNo			-> renew.php見積原価番号
// confirm.php -> strProductCode		-> renew.php製品コード
// confirm.php -> aryDitail[仕入科目][明細行][lngStockSubjectCode]	-> renew.php
// confirm.php -> aryDitail[仕入科目][明細行][lngStockItemCode]		-> renew.php
// confirm.php -> aryDitail[仕入科目][明細行][bytPayOffTargetFlag]	-> renew.php
// confirm.php -> aryDitail[仕入科目][明細行][lngCustomerCode]		-> renew.php
// confirm.php -> aryDitail[仕入科目][明細行][bytPercentInputFlag]	-> renew.php
// confirm.php -> aryDitail[仕入科目][明細行][lngProductQuantity]	-> renew.php
// confirm.php -> aryDitail[仕入科目][明細行][curProductRate]		-> renew.php
// confirm.php -> aryDitail[仕入科目][明細行][curProductPrice]		-> renew.php
// confirm.php -> aryDitail[仕入科目][明細行][curSubTotalPrice]		-> renew.php
// confirm.php -> aryDitail[仕入科目][明細行][strNote]				-> renew.php
// confirm.php -> aryDitail[仕入科目][明細行][lngMonetaryUnitCode]	-> renew.php
// confirm.php -> aryDitail[仕入科目][明細行][curSubTotalPriceJP]	-> renew.php
// confirm.php -> aryDitail[仕入科目][明細行][curConversionRate]	-> renew.php
//
// 確認へ
// renew.php -> strSessionID			-> confirm.php
// renew.php -> lngFunctionCode		-> confirm.php
// renew.php -> lngEstimateNo		-> confirm.php見積原価番号
// renew.php -> strProductCode		-> confirm.php製品コード
// renew.php -> bytDecisionFlag		-> confirm.php決定フラグ
// renew.php -> lngWorkflowOrderCode	-> confirm.php承認ルート
// renew.php -> aryDitail[仕入科目][明細行][lngStockSubjectCode]	-> confirm.php
// renew.php -> aryDitail[仕入科目][明細行][lngStockItemCode]	-> confirm.php
// renew.php -> aryDitail[仕入科目][明細行][bytPayOffTargetFlag]	-> confirm.php
// renew.php -> aryDitail[仕入科目][明細行][lngCustomerCode]		-> confirm.php
// renew.php -> aryDitail[仕入科目][明細行][bytPercentInputFlag]	-> confirm.php
// renew.php -> aryDitail[仕入科目][明細行][lngProductQuantity]	-> confirm.php
// renew.php -> aryDitail[仕入科目][明細行][curProductRate]		-> confirm.php
// renew.php -> aryDitail[仕入科目][明細行][curProductPrice]		-> confirm.php
// renew.php -> aryDitail[仕入科目][明細行][curSubTotalPrice]	-> confirm.php
// renew.php -> aryDitail[仕入科目][明細行][strNote]				-> confirm.php
// renew.php -> aryDitail[仕入科目][明細行][lngMonetaryUnitCode]	-> confirm.php
// renew.php -> aryDitail[仕入科目][明細行][curSubTotalPriceJP]	-> confirm.php
// renew.php -> aryDitail[仕入科目][明細行][curConversionRate]	-> confirm.php
//
// 仮保存へ
// renew.php -> strSessionID			-> action.php
// renew.php -> lngFunctionCode		-> action.php
// renew.php -> lngEstimateNo		-> action.php見積原価番号
// renew.php -> strProductCode		-> action.php製品コード
// renew.php -> bytDecisionFlag		-> action.php決定フラグ
// renew.php -> lngWorkflowOrderCode	-> action.php承認ルート
// renew.php -> aryDitail[仕入科目][明細行][lngStockSubjectCode]	-> action.php
// renew.php -> aryDitail[仕入科目][明細行][lngStockItemCode]	-> action.php
// renew.php -> aryDitail[仕入科目][明細行][bytPayOffTargetFlag]	-> action.php
// renew.php -> aryDitail[仕入科目][明細行][lngCustomerCode]		-> action.php
// renew.php -> aryDitail[仕入科目][明細行][bytPercentInputFlag]	-> action.php
// renew.php -> aryDitail[仕入科目][明細行][lngProductQuantity]	-> action.php
// renew.php -> aryDitail[仕入科目][明細行][curProductRate]		-> action.php
// renew.php -> aryDitail[仕入科目][明細行][curProductPrice]		-> action.php
// renew.php -> aryDitail[仕入科目][明細行][curSubTotalPrice]	-> action.php
// renew.php -> aryDitail[仕入科目][明細行][strNote]				-> action.php
// renew.php -> aryDitail[仕入科目][明細行][lngMonetaryUnitCode]	-> action.php
// renew.php -> aryDitail[仕入科目][明細行][curSubTotalPriceJP]	-> action.php
// renew.php -> aryDitail[仕入科目][明細行][curConversionRate]	-> action.php





	// 設定読み込み
	include_once('conf.inc');
	require( LIB_DEBUGFILE );

	// ライブラリ読み込み
	require (LIB_FILE);
	require (SRC_ROOT . "estimate/cmn/lib_e.php");

	// 承認ルートプルダウン生成に必要
	require(SRC_ROOT."po/cmn/lib_po.php");


	require ( CLS_TABLETEMP_FILE );	// Temporary DB Object
	require ( LIB_ROOT . "tabletemp/excel2temp.php" );


	// DB接続
	$objDB   = new clsDB();
	$objAuth = new clsAuth();
	$objDB->open( "", "", "", "" );

	// GETデータ取得
	if ( $_GET )
	{
		$aryData = $_GET;
	}
	else
	{
		$aryData = $_POST;
	}
	$aryDetail = $aryData["aryDitail"];



		// Temp配列
		$g_aryTemp	= $aryData;

fncDebug( 'estimate_regist_renew.txt', $aryData, __FILE__, __LINE__);



	unset ( $aryData["aryDitail"] );


	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	$lngUserCode = $objAuth->UserCode;

	// 権限確認
	// 登録の場合
	if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E1 && fncCheckAuthority( DEF_FUNCTION_E1, $objAuth ) )
	{
	}

	// 修正の場合
	elseif ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E3 && fncCheckAuthority( DEF_FUNCTION_E3, $objAuth ) )
	{
		$aryCheck["lngEstimateNo"] = "null:number(0,32767)";
	}

	// それ以外
	else
	{
		fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}



	$aryCheck["strSessionID"]           = "null:numenglish(32,32)";
	$aryCheck["lngFunctionCode"]        = "null:number(" . DEF_FUNCTION_E1 . "," . DEF_FUNCTION_E3 . ")";


	// 文字列チェック
	$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryCheckResult, $objDB );



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



	// 入力ミスによる戻り以外の場合、エラー表示を非表示に設定
	if ( !preg_match ( "/confirm\.php/", $_SERVER["HTTP_REFERER"] ) )
	{
		$aryData["strProductCode_Error"] = "visibility:hidden;";
	}

//fncDebug( 'estimate_regist_renew.txt', $_SERVER["HTTP_REFERER"], __FILE__, __LINE__);


	/*---------------------------------------------------------------------------*/
	// 修正では必要ない

/*
	// 製品コード指定処理
	if ( $aryData["strMode"] == "onchange" and $aryData["strProductCode"] != "" )
	{
		// 製品コードが設定された状態で反映ボタンが押された場合、製品情報を設定する
		$aryProduct = fncGetProduct( $aryData["strProductCode"], $objDB, $lngUserCode );
		if ( $aryProduct == FALSE )
		{
			// 入力された製品コードが存在しない場合、エラー内容をヘッダ部に表示する設定を行う
			$strErrorMessage = fncOutputError ( 1504, DEF_WARNING, "", FALSE, "", $objDB );

			// メッセージ表示箇所にメッセージを設定
			$aryData["strHeaderErrorMessage"] = $strErrorMessage;
		}
		else
		{
			// 指定製品コードにて既に見積情報が作成されていないかどうかのチェック

			// 見積原価データ取得
			$aryEstimate = fncGetEstimateToProductCode( $aryData["strProductCode"], $objDB );
			if ( $aryEstimate != FALSE )
			{
				// 指定の製品コードに対して見積もりが存在する場合は、エラー内容をヘッダ部に表示する設定を行う
				$strErrorMessage = fncOutputError ( 1501, DEF_WARNING, "", FALSE, "", $objDB );

				// メッセージ表示箇所にメッセージを設定
				$aryData["strHeaderErrorMessage"] = $strErrorMessage;
			}

			// 配列のマージ処理
			$aryData = array_merge( $aryData, $aryProduct );

			// 見積原価デフォルト明細データ取得
			$aryDetail = fncGetEstimateDefaultValue( $aryData["lngProductionQuantity"], $aryData["curProductPrice"], $aryRate, $objDB );
		}

		unset( $aryProduct );

		// 明細HIDDEN文字列取得
		list ( $aryDetail, $aryCalculated, $aryHiddenString ) = fncGetEstimateDetailHtml( $aryDetail, "estimate/regist/plan_detail.tmpl", $objDB );
		if ( is_array($aryHiddenString) )
		{
			$aryData["strDetailData"] = join ( "", $aryHiddenString );
		}

		$aryData["strMode"] = "";

//fncDebug( 'temp_renew.txt', $aryData, __FILE__, __LINE__);
	}
	else if ( $aryData["strProductCode"] != "" )
	{
		// 製品情報取得
		//$aryData = array_merge( $aryData, fncGetProduct( $aryData["strProductCode"], $objDB, $lngUserCode ) );
	}
*/
	/*---------------------------------------------------------------------------*/





// 2004.10.05 suzukaze update start
// 登録、仮保存ボタン押下時のリロードおよび確認、実行画面飛び処理
if ( $aryData["strActionName"] != "" )
{
	// 入力チェックを行う
	// チェック内容

	// 登録時更新時共通
	// 製品コードが指定されているかどうか？
	// 製品コードは正常なものなのかどうか
	// 製品コードが指定されていれば納価が登録フォームに設定されているかどうか

	// 登録時
	// 指定されている製品コードにて見積情報が設定されていないかどうか→されていればエラー

	$lngErrorCount = 0;


	// 製品コードが存在しない場合、エラー
	if ( $aryData["strProductCode"] == "" )
	{
		$lngErrorCount++;
		$aryData["strProductCode_Error"]         = "visibility:visible;";
		$aryData["strProductCode_Error_Message"] = fncOutputError ( 1505, DEF_WARNING, "", FALSE, "", $objDB );
	}
	// 納価および生産予定数が設定されていなければ製品情報が反映されていないとみなす
	else if ( $aryData["curProductPrice_hidden"] == "" and $aryData["lngProductionQuantity_hidden"] == "" )
	{
		$lngErrorCount++;
		$aryData["strProductCode_Error"]         = "visibility:visible;";
		$aryData["strProductCode_Error_Message"] = fncOutputError ( 1506, DEF_WARNING, "", FALSE, "", $objDB );
	}
	else
	{
		// 製品コード情報を取得する
		$aryProduct = fncGetProduct( $aryData["strProductCode"], $objDB, $lngUserCode );
		if ( $aryProduct == FALSE )
		{
			$lngErrorCount++;
			$aryData["strProductCode_Error"]         = "visibility:visible;";
			$aryData["strProductCode_Error_Message"] = fncOutputError ( 1504, DEF_WARNING, "", FALSE, "", $objDB );
		}
	}

	// 登録にて製品コードが指定されている場合のみ
	if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E1 and $aryData["strProductCode"] != "" )
	{
		// 製品情報が存在する場合は製品使用可能かどうかのチェック
		$aryEstimate = fncGetEstimateToProductCode( $aryData["strProductCode"], $objDB );
		if ( $aryEstimate != FALSE )
		{
			$lngErrorCount++;
			// 指定の製品コードに対して見積もりが存在する場合は、エラー内容をヘッダ部に表示する設定を行う
			$aryData["strHeaderErrorMessage"] = fncOutputError ( 1501, DEF_WARNING, "", FALSE, "", $objDB );
		}
	}


	// 修正の場合、修正権限チェック
	if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E3 )
	{
		$aryEstimateData = fncGetEstimate( $aryData["lngEstimateNo"], $objDB );


		// コメント（バッファ）取得
		$strBuffRemark	= $aryEstimateData["strRemark"];


//fncDebug( 'temp_renew.txt', $aryData, __FILE__, __LINE__);

		// (ログインユーザーが入力したものかつ仮保存状態)以外のもの、
		// または、申請中のものは、修正不可としてエラー出力
		if ( !( ( $aryEstimateData["bytDecisionFlag"] == "f" && $aryEstimateData["lngInputUserCode"] == $objAuth->UserCode ) || $aryEstimateData["lngEstimateStatusCode"] != DEF_ESTIMATE_APPLICATE ) )
		{
			$lngErrorCount++;
			// 指定の製品コードに対して見積もりが存在する場合は、エラー内容をヘッダ部に表示する設定を行う
			$aryData["strHeaderErrorMessage"] = fncOutputError ( 1503, DEF_WARNING, "", FALSE, "", $objDB );
		}
	}


//fncDebug( 'es_renew.txt', $aryEstimateData, __FILE__, __LINE__);

	if ( $lngErrorCount == 0 )
	{
///////////////////////////////////////
//////////// 登録確認処理 /////////////
///////////////////////////////////////

		if ( $aryData["strActionName"] == "regist" )
		{
			// 製品情報取得
			$aryEstimateData = fncGetProduct( $aryData["strProductCode"], $objDB, $lngUserCode );

//fncDebug( 'temp_renew.txt', $aryEstimateData, __FILE__, __LINE__);

			// テンポラリ処理の場合
			if( $aryEstimateData["blnTempFlag"] )
			{
				// 標準割合取得
				$aryEstimateData["curStandardRate"] = fncGetEstimateDefault( $objDB );

				// 社内USドルレート取得
				$aryEstimateData["curConversionRate"] = fncGetUSConversionRate( $aryEstimateData["dtmInsertDate"], $objDB );

				// Excel標準割合取得
//				$aryEstimateData["curStandardRate"]		= $aryEstimateData["curStandardRate"];
				// Excel社内USドルレート取得
//				$aryEstimateData["curConversionRate"]	= $aryEstimateData["curConversionRate"];
			}
			else
			{
				// 標準割合取得
				$aryEstimateData["curStandardRate"] = fncGetEstimateDefault( $objDB );

				// 社内USドルレート取得
				$aryEstimateData["curConversionRate"] = fncGetUSConversionRate( $aryEstimateData["dtmInsertDate"], $objDB );
			}




//レート表示がおかしいの至急対応			
			$aryEstimateData["dtmInsertDate"] = date("Y-m-d");




			// 見積原価計算明細HTML出力文字列取得

			list ( $aryEstimateDetail, $aryCalculated, $aryHiddenString ) = fncGetEstimateDetailHtml( $aryDetail, "estimate/regist/plan_detail.tmpl", $objDB );

			// 計算結果を見積原価配列に組み込む
			$aryEstimateData = array_merge ( $aryEstimateData, $aryCalculated );

			unset ( $aryDetail );
			unset ( $aryCalculated );

			// 計算結果を取得
			$aryEstimateData = fncGetEstimateCalculate( $aryEstimateData );

//fncDebug( 'temp_renew.txt', $aryEstimateData, __FILE__, __LINE__);

			// 確認ではなかったら、送信・もどるボタン表示
			if ( $aryData["strActionName"] != "confirm" )
			{
				$aryData["bytReturnFlag"] = "true";

				$aryHiddenString[] = getArrayTable( fncToHTMLString( $aryData ), "HIDDEN" );

				$strHiddenString = join( "", $aryHiddenString );
				unset ( $aryHiddenString );

				$aryForm[] = "<form name=frmAction action=\"action.php\" method=POST>\n";
				$aryForm[] = $strHiddenString;

				// テンポラリフラグ
				if( $aryEstimateData["blnTempFlag"] )
				{
					// テンポラリフラグ
					$aryForm[] = "<input type=\"hidden\" name=\"blnTempFlag\"	value=\"" .$aryEstimateData["blnTempFlag"]. "\" />\n";
					// コメント
					$aryForm[] = "<input type=\"hidden\" name=\"strRemark\"	value=\"" .$strBuffRemark. "\" />\n";

					// Excel標準割合
//					$aryForm[] = "<input type=\"hidden\" name=\"curStandardRate\"	value=\"" .$aryEstimateData["curStandardRate"]. "\" />\n";
					// Excel社内USドルレート
//					$aryForm[] = "<input type=\"hidden\" name=\"curConversionRate\"	value=\"" .$aryEstimateData["curConversionRate"]. "\" />\n";
				}

				$aryForm[] = "</form>\n";


				$aryForm[] = "<form name=frmEdit action=\"renew.php\" method=POST>\n";
				$aryForm[] = $strHiddenString;

				// テンポラリフラグ
				if( $aryEstimateData["blnTempFlag"] )
				{
					// テンポラリフラグ
					$aryForm[] = "<input type=\"hidden\" name=\"blnTempFlag\"	value=\"" .$aryEstimateData["blnTempFlag"]. "\" />\n";
					// コメント
					$aryForm[] = "<input type=\"hidden\" name=\"strRemark\"	value=\"" .$strBuffRemark. "\" />\n";

					// Excel標準割合
//					$aryForm[] = "<input type=\"hidden\" name=\"curStandardRate\"	value=\"" .$aryEstimateData["curStandardRate"]. "\" />\n";
					// Excel社内USドルレート
//					$aryForm[] = "<input type=\"hidden\" name=\"curConversionRate\"	value=\"" .$aryEstimateData["curConversionRate"]. "\" />\n";
				}

				$aryForm[] = "<input type=hidden name=bytReturnFlag value=true>\n";
				$aryForm[] = "</form>\n";

				$aryEstimateData["FORM"] = join ( "", $aryForm );
				unset ( $aryForm );
			}

			unset ( $strHiddenString );

			$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

			if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E1 or $aryData["lngFunctionCode"] == DEF_FUNCTION_E3 )
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


			if ( $aryData["strActionName"] != "confirm" )
			{
				$aryData["strScrollType"] = "ScrollAuto";
			}
			else
			{
				$aryData["strScrollType"] = "ScrollHidden";
			}


		 	// カンマ処理
			$aryEstimateData	= fncGetCommaNumber( $aryEstimateData );
//fncDebug( 'temp_renew.txt', $aryEstimateData, __FILE__, __LINE__);
			$objTemplate = new clsTemplate();
			$objTemplate->getTemplate( "estimate/regist/plan_base.tmpl" );


			$aryData["lngSelfLoginUserCode"] = $lngUserCode; // 入力者コード

//fncDebug( 'es_renew.txt', $aryEstimateData, __FILE__, __LINE__);


			// コメント
			$aryData["strRemarkDisp"]	= nl2br($strBuffRemark);


			// 置き換え
			$objTemplate->replace( $aryData );

		//echo getArrayTable( $aryData, "TABLE" );exit;
			$objTemplate->replace( $aryEstimateData );
			$objTemplate->replace( $aryEstimateDetail );

			$objTemplate->complete();
			echo $objTemplate->strTemplate;

			unset ( $aryEstimateData );
			unset ( $aryEstimateDetail );
			unset ( $aryData );

			$objDB->close();

			return TRUE;

		}

///////////////////////////////////////
//////////// 仮登録処理 ///////////////
///////////////////////////////////////

		else if ( $aryData["strActionName"] == "temporary" )
		{

			/////////////////////////////////////////////////////////////
			// 成形に必要なデータの取得
			/////////////////////////////////////////////////////////////
			// 会社表示コードをキーとする会社コード連想配列を取得
			$aryCompanyCode = fncGetMasterValue( "m_Company", "strCompanyDisplayCode", "lngCompanyCode", "Array", "", $objDB );

			$aryMonetaryUnitCode = Array ( "\\" => DEF_MONETARY_YEN, "$" => DEF_MONETARY_USD, "HKD" => DEF_MONETARY_HKD );

			// 通貨レート配列生成
			$aryRate = fncGetMonetaryRate( $objDB );
			$aryRate[DEF_MONETARY_YEN] = 1;

			$objDB->transactionBegin();

			// リビジョン番号
			// 修正の場合同じ製品コードの見積原価に対してリビジョン番号の最大値を取得する
		/////   リビジョン番号を現在の最大値をとるように修正する　その際にSELECT FOR UPDATEを使用して、同じ仕入に対してロック状態にする
			$strLockQuery = "SELECT lngRevisionNo FROM m_Estimate WHERE strProductCode = '" . $aryData["strProductCode"] . "' FOR UPDATE";

			// ロッククエリーの実行
			list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );

			$lngMaxRevision = 0;
			if ( $lngLockResultNum )
			{
				for ( $i = 0; $i < $lngLockResultNum; $i++ )
				{
					$objRevision = $objDB->fetchObject( $lngLockResultID, $i );
					if ( $lngMaxRevision < $objRevision->lngrevisionno )
					{
						$lngMaxRevision = $objRevision->lngrevisionno;
					}
				}
				$lngRevisionNo = $lngMaxRevision + 1;
			}
			else
			{
				$lngRevisionNo = $lngMaxRevision;
			}
			$objDB->freeResult( $lngLockResultID );


			/////////////////////////////////////////////////////////////
			// 登録対象データ成形と取得
			/////////////////////////////////////////////////////////////
			// 製品情報取得
			$aryEstimateData = fncGetProduct( $aryData["strProductCode"], $objDB, $lngUserCode );

			// 標準割合取得
			$aryEstimateData["curStandardRate"] = fncGetEstimateDefault( $objDB );

			// 見積原価計算明細HTML出力文字列取得
			list ( $aryEstimateDetail, $aryCalculated, $aryHiddenString ) = fncGetEstimateDetailHtml( $aryDetail, "estimate/regist/plan_detail.tmpl", $objDB );

			// 計算結果を見積原価配列に組み込む

			$aryEstimateData = array_merge ( $aryEstimateData, $aryCalculated );

			unset ( $aryEstimateDetail );
			unset ( $aryCalculated );
			unset ( $aryHiddenString );

			// 計算結果を取得
			$aryEstimateData = fncGetEstimateCalculate( $aryEstimateData );

			// 仮保存チェック
			$bytDecisionFlag = "FALSE";
			$lngEstimateStatusCode = DEF_ESTIMATE_TEMPORARY;


			/////////////////////////////////////////////////////////////
			// 見積原価関連クエリ生成開始
			/////////////////////////////////////////////////////////////
			// 見積原価マスタ登録クエリ生成
			$aryEstimateQuery[] = "INSERT INTO m_Estimate VALUES ( " . $aryData["lngEstimateNo"];
			$aryEstimateQuery[] = $lngRevisionNo;
			$aryEstimateQuery[] = "'" . $aryData["strProductCode"] . "'";
			$aryEstimateQuery[] = $bytDecisionFlag;
			$aryEstimateQuery[] = $lngEstimateStatusCode;
			$aryEstimateQuery[] = $aryEstimateData["curFixedCost"];
			$aryEstimateQuery[] = $aryEstimateData["curMemberCost"];
			$aryEstimateQuery[] = $aryEstimateData["curTargetProfit"];
			$aryEstimateQuery[] = $aryEstimateData["curManufacturingCost"];
			$aryEstimateQuery[] = $aryEstimateData["curAmountOfSales"];
			$aryEstimateQuery[] = $aryEstimateData["curProfitOnSales"];
			$aryEstimateQuery[] = $objAuth->UserCode;
			$aryEstimateQuery[] = "FALSE";
			$aryEstimateQuery[] = "NOW()";
			$aryEstimateQuery[] = $aryEstimateData["lngProductionQuantity"] . ")";
			$aryQuery[] = join ( ", ", $aryEstimateQuery );
			unset ( $aryEstimateQuery );


			// 登録・修正の場合、見積原価詳細・ワークフローに関するクエリ生成
			if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E1 || $aryData["lngFunctionCode"] == DEF_FUNCTION_E3 )
			{
				// 見積原価明細番号インクリメント
				$lngEstimateDetailNo++;

				// 見積原価詳細登録クエリ生成
				for ( $i = 0; $i < 8; $i++ )
				{
					for ( $j = 0; $j < count ( $aryDetail[$i] ); $j++ )
					{
						// 通貨レートコード 2：社内固定
						$aryDetail[$i][$j]["lngMonetaryRateCode"] = 2;

						// 表示用ソートキーNULL固定
						$aryDetail[$i][$j]["lngSortKey"] = "NULL";

						// パーセント入力フラグがOffの場合、計画率をNULLに設定
						if ( $aryDetail[$i][$j]["bytPercentInputFlag"] != "true" )
						{
							$aryDetail[$i][$j]["curProductRate"] = "NULL";
						}

						$aryRowsValues = $aryDetail[$i][$j];

			// 2004.10.05 suzukaze update start
						// 仕入先情報が指定されていなければNULLに設定
						if ( !is_numeric( $aryCompanyCode[$aryRowsValues["lngCustomerCompanyCode"]] ) )
						{
							$aryCompanyCode[$aryRowsValues["lngCustomerCompanyCode"]] = "NULL";
						}
						// 計画率の場合単価項目はNULLに設定する
						if ( !is_numeric( $aryRowsValues["curProductPrice"] ) or ($aryRowsValues["curProductPrice"] == "" ) )
						{
							$aryRowsValues["curProductPrice"] = "NULL";
						}
			// 2004.10.05 suzukaze update end

						// 通貨単位コード
						if ( !is_numeric( $aryMonetaryUnitCode[$aryRowsValues["lngMonetaryUnitCode"]] ) or $aryMonetaryUnitCode[$aryRowsValues["lngMonetaryUnitCode"]] == "" )
						{
							$aryMonetaryUnitCode[$aryRowsValues["lngMonetaryUnitCode"]] = 1;
						}
						if ( !is_numeric( $aryRate[$aryMonetaryUnitCode[$aryRowsValues["lngMonetaryUnitCode"]]] ) or $aryRate[$aryMonetaryUnitCode[$aryRowsValues["lngMonetaryUnitCode"]]] == "" )
						{
							$aryMonetaryUnitCode[$aryRowsValues["lngMonetaryUnitCode"]] = 1.000000;
						}

						$aryEstimateQuery[] = "INSERT INTO t_EstimateDetail VALUES ( " . $aryData["lngEstimateNo"];
						$aryEstimateQuery[] = $lngEstimateDetailNo;
						$aryEstimateQuery[] = $lngRevisionNo;
						$aryEstimateQuery[] = $aryRowsValues["lngStockSubjectCode"];
						$aryEstimateQuery[] = $aryRowsValues["lngStockItemCode"];
						$aryEstimateQuery[] = $aryCompanyCode[$aryRowsValues["lngCustomerCompanyCode"]];
						$aryEstimateQuery[] = $aryRowsValues["bytPayOffTargetFlag"];
						$aryEstimateQuery[] = $aryRowsValues["bytPercentInputFlag"];
						$aryEstimateQuery[] = $aryMonetaryUnitCode[$aryRowsValues["lngMonetaryUnitCode"]];
						$aryEstimateQuery[] = $aryRowsValues["lngMonetaryRateCode"];
						$aryEstimateQuery[] = $aryRate[$aryMonetaryUnitCode[$aryRowsValues["lngMonetaryUnitCode"]]];
						$aryEstimateQuery[] = $aryRowsValues["lngProductQuantity"];
						$aryEstimateQuery[] = $aryRowsValues["curProductPrice"];
						$aryEstimateQuery[] = $aryRowsValues["curProductRate"];
						$aryEstimateQuery[] = $aryRowsValues["curSubTotalPrice"];
						$aryEstimateQuery[] = "'" . $aryRowsValues["strNote"] . "'";
						$aryEstimateQuery[] = $aryRowsValues["lngSortKey"] . ")";

						$aryQuery[] = join ( ", ", $aryEstimateQuery );
						unset ( $aryEstimateQuery );
						unset ( $aryRowsValues );

						// 見積原価明細番号インクリメント
						$lngEstimateDetailNo++;
					}
				}
			}

			unset ( $aryEstimateData );
			unset ( $aryCompanyCode );
			unset ( $aryMonetaryUnitCode );
			unset ( $aryRate );
			unset ( $bytDecisionFlag );
			unset ( $aryDetail );
			unset ( $lngEstimateDetailNo );


			//////////////////////////////////////////////////////////////////////////
			// クエリ実行(見積原価追加)
			//////////////////////////////////////////////////////////////////////////
			for ( $i = 0; $i < count ( $aryQuery ); $i++ )
			{
			//	echo "<p>$aryQuery[$i]</p>\n";
				list ( $lngResultID, $lngResultNum ) = fncQuery( $aryQuery[$i], $objDB );
			}

			$objDB->transactionCommit();


			//////////////////////////////////////////////////////////////////////////
			// 結果取得、出力処理
			//////////////////////////////////////////////////////////////////////////


			$aryData["lngInputUserCode"] = $lngUserCode; // 入力者コード

			if ( $lngEstimateStatusCode == DEF_ESTIMATE_TEMPORARY )
			{
				$aryData["lngSaveType"] = 1;
			}
			else
			{
				$aryData["lngSaveType"] = 0;
			}

			// 帳票出力表示切替
			// 削除以外にて帳票出力ボタンを表示する
			if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E4 )
			{
				$aryData["PreviewVisible"] = "hidden";
			}
			else
			{
				$aryData["PreviewVisible"] = "visible";
				$aryData["strPreviewAction"] = "../../list/result/frameset.php?strSessionID=" . $aryData["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_ESTIMATE . "&strReportKeyCode=" . $lngEstimateDetailNo . "&bytCopyFlag=TRUE";
			}

			// 見積原価情報の場合
			if( $aryData["lngFunctionCode"] == DEF_FUNCTION_E1 )
			{
				// 成功時戻り先のアドレス指定
				$aryData["strAction"] = "/estimate/regist/renew.php?lngFunctionCode=" . DEF_FUNCTION_E1 ."&strSessionID=";

				$objTemplate = new clsTemplate();
				$objTemplate->getTemplate( "estimate/regist/finish.tmpl" );
				header("Content-type: text/plain; charset=EUC-JP");
				$objTemplate->replace( $aryData );
				$objTemplate->complete();
				echo $objTemplate->strTemplate;
			}
			// 見積原価修正・削除の場合
			elseif( $aryData["lngFunctionCode"] == DEF_FUNCTION_E3 or $aryData["lngFunctionCode"] == DEF_FUNCTION_E4 )
			{
				// 成功時戻り先のアドレス指定 （意味無し。削除予定）
				$aryData["strAction"] = "/estimate/search/index.php?lngFunctionCode=" . $aryData["lngFunctionCode"] ."&strSessionID=";

				$objTemplate = new clsTemplate();
				$objTemplate->getTemplate( "estimate/regist/finish.tmpl" );
				header("Content-type: text/plain; charset=EUC-JP");
				$objTemplate->replace( $aryData );
				$objTemplate->complete();
				echo $objTemplate->strTemplate;
			}

			unset ( $lngEstimateStatusCode );
			$objDB->close();

			return TRUE;

		}
	}
	else
	{
		$aryData["strProcess"] = "regist";
	}
}







	// 権限グループコード(ユーザー以下)チェック
	$blnAG = fncCheckUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );

	// 「ユーザー」以下の場合
	if( $blnAG )
	{
		// 承認ルート存在チェック
		$blnWF = fncCheckWorkFlowRoot( $lngUserCode, $aryData["strSessionID"], $objDB );

		// 承認ルートが存在しない場合
		if( !$blnWF )
		{
			fncOutputError( 9060, DEF_WARNING, "", TRUE, "", $objDB );
		}
	}





// 2004.09.27 suzukaze update start
$aryData["ProductSubmit"] = "";
$aryData["strProcess"]    = "regist";

// 修正の場合は製品コードは編集不可能とする
$aryData["strProductCode_Editable"] = "contenteditable=\"false\"";

// 通貨コード->通貨記号(JAVASCRIPT使用)
$aryMonetaryUnit = Array ( DEF_MONETARY_YEN => "\\", DEF_MONETARY_USD => "$", DEF_MONETARY_HKD => "HKD" );

// 通貨レート配列生成
$aryRate = fncGetMonetaryRate( $objDB );
$aryRate[DEF_MONETARY_YEN] = 1;


// 通貨レート配列からHIDDEN生成
$aryMonetaryUnitData = Array();
$aryKeys = array_keys ( $aryRate );
foreach ( $aryKeys as $strKey )
{
	$aryMonetaryUnitData[] = "<input type='hidden' name='lngMonetaryUnitCode[" . $aryMonetaryUnit[$strKey] . "]' value='" . $aryRate[$strKey] . "' >\n";
}
unset ( $aryKeys );
unset ( $strKey );


// 登録かつもどりの場合
if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E1 && $aryData["strProductCode"] != "" )
{
	// 製品情報取得
	$aryData = array_merge( $aryData, fncGetProduct( $aryData["strProductCode"], $objDB, $lngUserCode ) );

	// 明細HIDDEN文字列取得
	list ( $aryDetail, $aryCalculated, $aryHiddenString ) = fncGetEstimateDetailHtml( $aryDetail, "estimate/regist/plan_detail.tmpl", $objDB );
	if ( is_Array( $aryHiddenString ) )
	{
		$aryData["strDetailData"] = join ( "", $aryHiddenString );
	}

// 2004.10.02 suzukaze update start
	$aryData["strPageCondition"] = "regist";
// 2004.10.02 suzukaze update end
}

// 修正の場合
elseif ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E3 )
{
	// 見積原価データ取得
	$aryData = array_merge ( $aryData, fncGetEstimate( $aryData["lngEstimateNo"], $objDB ) );

	// ((ログインユーザーが入力したものかつ仮保存状態のもの)、
	// または、申請中以外のもの)以外は、修正不可としてエラー出力
	if ( !( ( $aryData["bytDecisionFlag"] == "f" && $aryData["lngInputUserCode"] == $objAuth->UserCode ) || $aryData["lngEstimateStatusCode"] != DEF_ESTIMATE_APPLICATE ) )
	{
		fncOutputError ( 1503, DEF_WARNING, "", TRUE, "", $objDB );
	}

	// 戻りで無い場合、明細データ取得
	if ( $aryData["bytReturnFlag"] != "true" )
	{
		// 見積原価明細データ取得
		$aryDetail = fncGetEstimateDetailRenew( $aryData["lngEstimateNo"], $aryRate, $aryData["lngProductionQuantity"], $aryData["lngOldProductionQuantity"], $aryData["curProductPrice"], $aryData["curRetailPrice"], $objDB );
		unset ( $aryCalculated );
	}

//fncDebug( 'es_renew.txt', $aryDetail, __FILE__, __LINE__);


	// 明細HIDDEN文字列取得
	list ( $aryDetail, $aryCalculated, $aryHiddenString ) = fncGetEstimateDetailHtml( $aryDetail, "estimate/regist/plan_detail.tmpl", $objDB );

	$aryData["strDetailData"] = join ( "", $aryHiddenString );

	$aryData["strPageCondition"] = "renew";

	$aryData["RENEW"] = TRUE;
}

// カンマ処理
$aryData = fncGetCommaNumber( $aryData );


unset ( $aryDetail );
unset ( $aryCalculated );
unset ( $aryHiddenString );


// 通貨レートHIDDEN生成
$aryData["strMonetaryUnitData"] = join ( "", $aryMonetaryUnitData );
unset ( $aryMonetaryUnitData );



	// 権限グループコードの取得
	$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );

	// 承認ルートの生成
	// 「マネージャー」以上の場合
	if( $lngAuthorityGroupCode <= DEF_DIRECT_REGIST_AUTHORITY_CODE )
	{
		$aryData["lngWorkflowOrderCode"] = '<option value="0">承認なし</option>';
	}
	else
	{
		$aryData["lngWorkflowOrderCode"] = fncWorkFlow( $lngUserCode, $objDB , $aryData["lngWorkflowOrderCode"] );
	}



unset ( $lngResultID );
unset ( $lngResultNum );
unset ( $aryMonetaryUnit );


// 2004.09.29 suzukaze update start
// 入力内容のチェック
if( $aryData["strProcess"] == "check" )
{
	// チェック内容

	// 登録時更新時共通
	// 製品コードが指定されているかどうか？
	// 製品コードは正常なものなのかどうか
	// 製品コードが指定されていれば納価が登録フォームに設定されているかどうか

	// 登録時
	// 指定されている製品コードにて見積情報が設定されていないかどうか→されていればエラー

	$lngErrorCount = 0;

	if ( $aryData["strProductCode"] == "" )
	{
		$lngErrorCount++;
		$aryData["strProductCode_Error"]         = "visibility:visible;";
		$aryData["strProductCode_Error_Message"] = fncOutputError ( 1505, DEF_WARNING, "", FALSE, "", $objDB );
	}
	// 納価および生産予定数が設定されていなければ製品情報が反映されていないとみなす
	else if ( $aryData["curProductPrice"] == "" and $aryData["lngProductionQuantity"] == "" )
	{
		$lngErrorCount++;
		$aryData["strProductCode_Error"]         = "visibility:visible;";
		$aryData["strProductCode_Error_Message"] = fncOutputError ( 1506, DEF_WARNING, "", FALSE, "", $objDB );
	}
	else
	{
		// 製品コード情報を取得する
		$aryProduct = fncGetProduct( $aryData["strProductCode"], $objDB, $lngUserCode );
		if ( $aryProduct == FALSE )
		{
			$lngErrorCount++;
			$aryData["strProductCode_Error"]         = "visibility:visible;";
			$aryData["strProductCode_Error_Message"] = fncOutputError ( 1504, DEF_WARNING, "", FALSE, "", $objDB );
		}
	}

	// 登録にて製品コードが指定されている場合のみ
	if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E1 and $aryData["strProductCode"] != "" )
	{
		// 製品情報が存在する場合は製品使用可能かどうかのチェック
		$aryEstimate = fncGetEstimateToProductCode( $aryData["strProductCode"], $objDB );

		if ( $aryEstimate != FALSE )
		{
			$lngErrorCount++;
			// 指定の製品コードに対して見積もりが存在する場合は、エラー内容をヘッダ部に表示する設定を行う
			$aryData["strHeaderErrorMessage"] = fncOutputError ( 1501, DEF_WARNING, "", FALSE, "", $objDB );
		}
	}

	if ( $lngErrorCount == 0 )
	{
		$aryData["strProcess"] = "confirm";

		$aryData["lngRegistConfirm"] = 1;
	}
	else
	{
		$aryData["strProcess"] = "regist";
	}
}

// URLセット
$aryData["filename"] = "renew.php";


$aryData["strActionFile"] = "renew.php";


	$aryData["lngSelfLoginUserCode"] = $lngUserCode; // 入力者コード


//fncDebug( 'es_renew.txt', $aryData, __FILE__, __LINE__);

//////////////////////////////////////////////////////////////////////////
// 結果取得、出力処理
//////////////////////////////////////////////////////////////////////////
//echo getArrayTable( $aryData, "TABLE" );exit;
echo fncGetReplacedHtml( "estimate/regist/parts.tmpl", $aryData, $objAuth );


$objDB->close();
unset ( $aryData );
unset ( $objAuth );
unset ( $objDB );


return TRUE;
?>
