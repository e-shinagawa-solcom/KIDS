<?
/** 
*	見積原価管理 データ入力画面
*
*	@package   KIDS
*	@copyright Copyright &copy; 2004, AntsBizShare 
*	@author    Kenji Chiba
*	@access    public
*	@version   1.00
*
*/
// 新規登録
// index.php -> strSessionID           -> edit.php
// index.php -> lngFunctionCode        -> edit.php
//
// 修正
// result/index.php -> strSessionID		-> edit.php
// result/index.php -> lngFunctionCode	-> edit.php
// result/index.php -> lngEstimateNo	-> edit.php
//
// 確認画面からの戻り
// confirm.php -> strSessionID           -> edit.php
// confirm.php -> lngFunctionCode        -> edit.php
// confirm.php -> lngEstimateNo			-> edit.php見積原価番号
// confirm.php -> strProductCode		-> edit.php製品コード
// confirm.php -> aryDitail[仕入科目][明細行][lngStockSubjectCode]	-> edit.php
// confirm.php -> aryDitail[仕入科目][明細行][lngStockItemCode]		-> edit.php
// confirm.php -> aryDitail[仕入科目][明細行][bytPayOffTargetFlag]	-> edit.php
// confirm.php -> aryDitail[仕入科目][明細行][lngCustomerCode]		-> edit.php
// confirm.php -> aryDitail[仕入科目][明細行][bytPercentInputFlag]	-> edit.php
// confirm.php -> aryDitail[仕入科目][明細行][lngProductQuantity]	-> edit.php
// confirm.php -> aryDitail[仕入科目][明細行][curProductRate]		-> edit.php
// confirm.php -> aryDitail[仕入科目][明細行][curProductPrice]		-> edit.php
// confirm.php -> aryDitail[仕入科目][明細行][curSubTotalPrice]		-> edit.php
// confirm.php -> aryDitail[仕入科目][明細行][strNote]				-> edit.php
// confirm.php -> aryDitail[仕入科目][明細行][lngMonetaryUnitCode]	-> edit.php
// confirm.php -> aryDitail[仕入科目][明細行][curSubTotalPriceJP]	-> edit.php
// confirm.php -> aryDitail[仕入科目][明細行][curConversionRate]	-> edit.php
//
// 確認へ
// edit.php -> strSessionID			-> confirm.php
// edit.php -> lngFunctionCode		-> confirm.php
// edit.php -> lngEstimateNo		-> confirm.php見積原価番号
// edit.php -> strProductCode		-> confirm.php製品コード
// edit.php -> bytDecisionFlag		-> confirm.php決定フラグ
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
//
// 仮保存へ
// edit.php -> strSessionID			-> action.php
// edit.php -> lngFunctionCode		-> action.php
// edit.php -> lngEstimateNo		-> action.php見積原価番号
// edit.php -> strProductCode		-> action.php製品コード
// edit.php -> bytDecisionFlag		-> action.php決定フラグ
// edit.php -> lngWorkflowOrderCode	-> action.php承認ルート
// edit.php -> aryDitail[仕入科目][明細行][lngStockSubjectCode]	-> action.php
// edit.php -> aryDitail[仕入科目][明細行][lngStockItemCode]	-> action.php
// edit.php -> aryDitail[仕入科目][明細行][bytPayOffTargetFlag]	-> action.php
// edit.php -> aryDitail[仕入科目][明細行][lngCustomerCode]		-> action.php
// edit.php -> aryDitail[仕入科目][明細行][bytPercentInputFlag]	-> action.php
// edit.php -> aryDitail[仕入科目][明細行][lngProductQuantity]	-> action.php
// edit.php -> aryDitail[仕入科目][明細行][curProductRate]		-> action.php
// edit.php -> aryDitail[仕入科目][明細行][curProductPrice]		-> action.php
// edit.php -> aryDitail[仕入科目][明細行][curSubTotalPrice]	-> action.php
// edit.php -> aryDitail[仕入科目][明細行][strNote]				-> action.php
// edit.php -> aryDitail[仕入科目][明細行][lngMonetaryUnitCode]	-> action.php
// edit.php -> aryDitail[仕入科目][明細行][curSubTotalPriceJP]	-> action.php
// edit.php -> aryDitail[仕入科目][明細行][curConversionRate]	-> action.php

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

$aryDetail = $aryData["aryDitail"];



fncDebug( 'estimate_regist_edit.txt', $aryData["aryDitail"], __FILE__, __LINE__);


	// Temp配列
	$g_aryTemp	= $aryData;

	// ファイルテンポラリフラグ有効の場合、
	// 確認画面へ情報を引き継ぐため、ファイル製品ヘッダ情報を保持
	if( $g_aryTemp["bytTemporaryFlg"] )
	{
//		$aryTempHidden	= array();


// lngCartonQuantity
// lngProductionQuantity


		$aryData["temp_bytTemporaryFlg"]			= "<input type=\"hidden\" name=\"bytTemporaryFlg\" value=\"" .$g_aryTemp["bytTemporaryFlg"]. "\" />";
		$aryData["temp_curStandardRate"]			= "<input type=\"hidden\" name=\"curStandardRate\" value=\"" .$g_aryTemp["curStandardRate"]. "\" />";
		$aryData["temp_curConversionRate"]			= "<input type=\"hidden\" name=\"curConversionRate\" value=\"" .$g_aryTemp["curConversionRate"]. "\" />";

		$aryData["temp_strProductName"]				= "<input type=\"hidden\" name=\"strProductName\" value=\"" .$g_aryTemp["strProductName"]. "\" />";
		$aryData["temp_dtmDeliveryLimitDate"]		= "<input type=\"hidden\" name=\"dtmDeliveryLimitDate\" value=\"" .$g_aryTemp["dtmDeliveryLimitDate"]. "\" />";
		$aryData["temp_strGroupDisplayCode"]		= "<input type=\"hidden\" name=\"strGroupDisplayCode\" value=\"" .$g_aryTemp["strGroupDisplayCode"]. "\" />";
		$aryData["temp_strUserDiplayCode"]			= "<input type=\"hidden\" name=\"strUserDiplayCode\" value=\"" .$g_aryTemp["strUserDiplayCode"]. "\" />";
		$aryData["temp_strUserDisplayName"]			= "<input type=\"hidden\" name=\"strUserDisplayName\" value=\"" .$g_aryTemp["strUserDisplayName"]. "\" />";
		$aryData["temp_curRetailPrice"]				= "<input type=\"hidden\" name=\"curRetailPrice\" value=\"" .$g_aryTemp["curRetailPrice"]. "\" />";

		$aryData["temp_lngCartonQty"]				= "<input type=\"hidden\" name=\"lngCartonQuantity\" value=\"" .$g_aryTemp["lngCartonQuantity"]. "\" />";

		$aryData["temp_lngPlanCartonProduction"]	= "<input type=\"hidden\" name=\"lngPlanCartonProduction\" value=\"" .$g_aryTemp["lngPlanCartonProduction"]. "\" />";

		$aryData["curProductPrice"]					= "<input type=\"hidden\" name=\"curProductPrice\" value=\"" .$g_aryTemp["curProductPrice"]. "\" />";

		$aryData["temp_lngProductionQuantity_hidden"]	= "<input type=\"hidden\" name=\"lngProductionQuantity_hidden\" value=\"" .$g_aryTemp["lngProductionQuantity_hidden"]. "\" />";
		$aryData["temp_curProductPrice_hidden"]			= "<input type=\"hidden\" name=\"curProductPrice_hidden\" value=\"" .$g_aryTemp["curProductPrice_hidden"]. "\" />";


		$aryData["temp_strRemark_hidden"]			= "<input type=\"hidden\" name=\"strRemark\" value=\"" .$g_aryTemp["strRemark"]. "\" />";

		$aryData["RENEW"]	= true;	// 画面表示モード：修正 ※修正処理ではない
	}


//fncDebug( 'es_temp1.txt', $aryData, __FILE__, __LINE__);

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
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "estimate/regist/edit.php?strSessionID=" . $aryData["strSessionID"] . "&lngFunctionCode=" . DEF_FUNCTION_E1 . "&lngRegist=1", $objDB );
}


$aryCheck["strSessionID"]           = "null:numenglish(32,32)";
$aryCheck["lngFunctionCode"]        = "null:number(" . DEF_FUNCTION_E1 . "," . DEF_FUNCTION_E3 . ")";

// 文字列チェック
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );


// 入力ミスによる戻り以外の場合、エラー表示を非表示に設定
if ( !preg_match ( "/confirm\.php/", $_SERVER["HTTP_REFERER"] ) )
{
	$aryData["strProductCode_Error"] = "visibility:hidden;";
}



//fncDebug( 'estimate_regist_edit.txt', $_SERVER["HTTP_REFERER"], __FILE__, __LINE__);


// 製品コード指定処理
if ( $aryData["strMode"] == "onchange" and $aryData["strProductCode"] != "" )
{
	// 製品コードが設定された状態で反映ボタンが押された場合、製品情報を設定する
	$aryProduct = fncGetProduct( $aryData["strProductCode"], $objDB, $lngUserCode );


	// 製品状態チェック -> 「申請中」の場合、処理終了
	if( $aryProduct["lngproductstatuscode"] == DEF_PRODUCT_APPLICATE )
	{
		fncOutputError ( 308, DEF_WARNING, "", TRUE, "estimate/regist/edit.php?strSessionID=" . $aryData["strSessionID"] . "&lngFunctionCode=" . DEF_FUNCTION_E1 . "&lngRegist=1", $objDB );
	}


	// 製品が存在しない場合
	if ( $aryProduct == FALSE )
	{
		// 入力された製品コードが存在しない場合、エラー内容をヘッダ部に表示する設定を行う
		$strErrorMessage = fncOutputError( 1504, DEF_WARNING, "", FALSE, "estimate/regist/edit.php?strSessionID=" . $aryData["strSessionID"] . "&lngFunctionCode=" . DEF_FUNCTION_E1 . "&lngRegist=1", $objDB );

		// メッセージ表示箇所にメッセージを設定
		$aryData["strHeaderErrorMessage"] = $strErrorMessage;
	}
	// 製品が存在する場合
	else
	{
		// 指定製品コードにて既に見積情報が作成されていないかどうかのチェック

		// 見積原価データ取得
		$aryEstimate = fncGetEstimateToProductCode( $aryData["strProductCode"], $objDB );


fncDebug( 'es_edit.txt', $g_aryTemp, __FILE__, __LINE__);

		// 見積原価データが存在する場合
		if ( $aryEstimate != FALSE )
		{
			// ファイルテンポラリ処理以外の場合
			if( !$g_aryTemp["bytTemporaryFlg"] )
			{
				// 指定の製品コードに対して見積もりが存在する場合は、エラー内容をヘッダ部に表示する設定を行う
				$strErrorMessage = fncOutputError ( 1501, DEF_WARNING, "", FALSE, "estimate/regist/edit.php?strSessionID=" . $aryData["strSessionID"] . "&lngFunctionCode=" . DEF_FUNCTION_E1 . "&lngRegist=1", $objDB );

				// メッセージ表示箇所にメッセージを設定
				$aryData["strHeaderErrorMessage"] = $strErrorMessage;
			}
		}


		//========================================================================
		// 050223 by kou 製品の上代、納価、生産予定数を再確認メッセージを出す
		//見積作成された場合はデータを読み込まないように変更
		else
		{
			//製品の上代、納価、生産予定数を再確認メッセージを出す
			$strErrorMessage = fncOutputError ( 1508, DEF_WARNING, "", FALSE, "estimate/regist/edit.php?strSessionID=" . $aryData["strSessionID"] . "&lngFunctionCode=" . DEF_FUNCTION_E1 . "&lngRegist=1", $objDB );

			// メッセージ表示箇所にメッセージを設定
			$aryData["strHeaderErrorMessage"] = $strErrorMessage;

			// 配列のマージ処理
			$aryData = array_merge( $aryData, $aryProduct );


			// 見積原価デフォルト明細データ取得
			$aryDetail = fncGetEstimateDefaultValue( $aryData["lngProductionQuantity"], $aryData["curProductPrice"], $aryRate, $objDB, $aryData["strSessionID"] );
		}
//============================================================================
	}


	unset( $aryProduct );

	// 明細HIDDEN文字列取得
	list ( $aryDetail, $aryCalculated, $aryHiddenString ) = fncGetEstimateDetailHtml( $aryDetail, "estimate/regist/plan_detail.tmpl", $objDB );


//fncDebug( 'es_array.txt', $aryDetail, __FILE__, __LINE__);


	if ( is_array($aryHiddenString) )
	{
		$aryData["strDetailData"] = join ( "", $aryHiddenString );
	}

	$aryData["strMode"] = "";

	$aryData["dtmInsertDate"] = date("Y/m/d");

}
else if ( $aryData["strProductCode"] != "" )
{
	// 製品情報取得
	$aryData = array_merge( $aryData, fncGetProduct( $aryData["strProductCode"], $objDB, $lngUserCode ) );

	$aryData["dtmInsertDate"] = date("Y/m/d");
}


//fncDebug( 'es_temp1.txt', $aryData, __FILE__, __LINE__);


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

	if ( $aryData["strProductCode"] == "" )
	{
		$lngErrorCount++;
		$aryData["strProductCode_Error"]         = "visibility:visible;";
		$aryData["strProductCode_Error_Message"] = fncOutputError ( 1505, DEF_WARNING, "", FALSE, "estimate/regist/edit.php?strSessionID=" . $aryData["strSessionID"] . "&lngFunctionCode=" . DEF_FUNCTION_E1 . "&lngRegist=1", $objDB );
	}
	// 納価および生産予定数が設定されていなければ製品情報が反映されていないとみなす
	else if ( $aryData["curProductPrice_hidden"] == "" and $aryData["lngProductionQuantity_hidden"] == "" )
	{
		$lngErrorCount++;
		$aryData["strProductCode_Error"]         = "visibility:visible;";
		$aryData["strProductCode_Error_Message"] = fncOutputError ( 1506, DEF_WARNING, "", FALSE, "estimate/regist/edit.php?strSessionID=" . $aryData["strSessionID"] . "&lngFunctionCode=" . DEF_FUNCTION_E1 . "&lngRegist=1", $objDB );
	}
	else
	{
		// 製品コード情報を取得する
		$aryProduct = fncGetProduct( $aryData["strProductCode"], $objDB, $lngUserCode );
		if ( $aryProduct == FALSE )
		{
			$lngErrorCount++;
			$aryData["strProductCode_Error"]         = "visibility:visible;";
			$aryData["strProductCode_Error_Message"] = fncOutputError ( 1504, DEF_WARNING, "", FALSE, "estimate/regist/edit.php?strSessionID=" . $aryData["strSessionID"] . "&lngFunctionCode=" . DEF_FUNCTION_E1 . "&lngRegist=1", $objDB );
		}
	}

	// 登録にて製品コードが指定されている場合のみ
	if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E1 and $aryData["strProductCode"] != "" )
	{
		// 製品情報が存在する場合は製品使用可能かどうかのチェック
		$aryEstimate = fncGetEstimateToProductCode( $aryData["strProductCode"], $objDB );

		if ( $aryEstimate != FALSE )
		{
			// ファイルテンポラリ処理以外の場合
			if( !$g_aryTemp["bytTemporaryFlg"] )
			{
				$lngErrorCount++;
				// 指定の製品コードに対して見積もりが存在する場合は、エラー内容をヘッダ部に表示する設定を行う
				$aryData["strHeaderErrorMessage"] = fncOutputError ( 1501, DEF_WARNING, "", FALSE, "estimate/regist/edit.php?strSessionID=" . $aryData["strSessionID"] . "&lngFunctionCode=" . DEF_FUNCTION_E1 . "&lngRegist=1", $objDB );
			}
		}
	}


//fncDebug( 'temp_edit.txt', $aryData, __FILE__, __LINE__);


	// 修正の場合、修正権限チェック
	if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E3 )
	{
		$aryEstimateData = fncGetEstimate( $aryData["lngEstimateNo"], $objDB );

		// (ログインユーザーが入力したものかつ仮保存状態)以外のもの、
		// または、申請中のものは、修正不可としてエラー出力
		if ( !( ( $aryEstimateData["bytDecisionFlag"] == "f" && $aryEstimateData["lngInputUserCode"] == $objAuth->UserCode ) || $aryEstimateData["lngEstimateStatusCode"] != DEF_ESTIMATE_APPLICATE ) )
		{
			$lngErrorCount++;
			// 指定の製品コードに対して見積もりが存在する場合は、エラー内容をヘッダ部に表示する設定を行う
			$aryData["strHeaderErrorMessage"] = fncOutputError ( 1503, DEF_WARNING, "", FALSE, "estimate/regist/edit.php?strSessionID=" . $aryData["strSessionID"] . "&lngFunctionCode=" . DEF_FUNCTION_E1 . "&lngRegist=1", $objDB );
		}
	}



	if ( $lngErrorCount == 0 )
	{
		// 明細HIDDEN文字列取得
		list ( $aryDetail, $aryCalculated, $aryHiddenString ) = fncGetEstimateDetailHtml( $aryDetail, "estimate/regist/plan_detail.tmpl", $objDB );
		if ( is_Array( $aryHiddenString ) )
		{
			$aryData["strDetailData"] = join ( "", $aryHiddenString );
		}

		$aryData["strProcess"] = "confirm";

		$aryData["lngRegistConfirm"] = 1;

		$aryData["strPageCondition"] = "regist";

		// URLセット
		$aryData["filename"] = "confirm.php";



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



// 2004.10.06 tomita update start
		if ( $aryData["strActionName"] == "regist" )
		{
			$aryData["strurl"]        = "/estimate/regist/confirm.php";
			$aryData["strActionName"] = "regist";
		}
		else if ( $aryData["strActionName"] == "temporary" )
		{
			$aryData["strurl"]        = "/estimate/regist/action.php";
			$aryData["strActionName"] = "temporary";
		}
		//$aryData["strurl"] = "/estimate/regist/confirm.php?strSessionID=".$aryData["strSessionID"];
		//echo getArrayTable( $aryData, "TABLE" );exit;
// 2004.10.06 tomita update end

		$aryData["lngSetValue"] = 1;

		$aryData["strActionFile"] = "edit.php";


		$aryData["lngSelfLoginUserCode"] = $lngUserCode; // 入力者コード




		// ファイルテンポラリフラグ有効の場合、
		// 確認画面へ情報を引き継ぐため、ファイル製品ヘッダ情報を保持
		if( $g_aryTemp["bytTemporaryFlg"] )
		{
			$aryData["temp_lngCartonQty"]				= "<input type=\"hidden\" name=\"lngCartonQuantity\" value=\"" .$aryData["lngCartonQuantity"]. "\" />";

//fncDebug( 'es_temp1.txt', $aryData, __FILE__, __LINE__);
		}



		echo fncGetReplacedHtml( "estimate/regist/parts.tmpl", $aryData, $objAuth );

		$objDB->close();

		return true;
	}
	else
	{
		$aryData["strProcess"] = "regist";
	}
}



//fncDebug( 'es_temp1.txt', $aryData, __FILE__, __LINE__);

// 2004.09.27 suzukaze update start
// 製品コードを指定し、反映ボタンが押下された場合の処理を設定
if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E1 )
{
	$aryData["ProductSubmit"] = "fncProductSubmit();";
	$aryData["strProcess"]    = "regist";

	// 登録の場合は製品検索ボタンを使用可能とする
	$aryData["btnMSWBt03_Editable"] = "fncGetObjectName( window.MGwin , strProductCode , strProductName );DisplayerM03( '' , document.all.Mdata03 , window.MGwin.document.all.strProductCode  );ExchangeM03( 0 , window.self );fncFocusType( window.MGwin , 'productsTop' );";

	// ファイルテンポラリ処理の場合
	if( $g_aryTemp["bytTemporaryFlg"] )
	{
		$aryData["ProductSubmit"] = "";

		// 製品コードを編集不可能とする
		$aryData["strProductCode_Editable"] = "contenteditable=\"false\"";
	}
}
else
{
	$aryData["ProductSubmit"] = "";
	$aryData["strProcess"]    = "regist";

	// 修正の場合は製品コードは編集不可能とする
	$aryData["strProductCode_Editable"] = "contenteditable=\"false\"";
}

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
	$aryProduct = fncGetProduct( $aryData["strProductCode"], $objDB, $lngUserCode );
	// 製品情報取得
	$aryData = array_merge( $aryData, is_array($aryProduct) ? $aryProduct : array($aryProduct) );

	// 明細HIDDEN文字列取得
	list ( $aryDetail, $aryCalculated, $aryHiddenString ) = fncGetEstimateDetailHtml( $aryDetail, "estimate/regist/plan_detail.tmpl", $objDB );
	if ( is_Array( $aryHiddenString ) )
	{
		$aryData["strDetailData"] = join ( "", $aryHiddenString );
	}


//fncDebug( 'es_temp1.txt', $aryData, __FILE__, __LINE__);


// 2004.10.02 suzukaze update start
	$aryData["strPageCondition"] = "regist";
// 2004.10.02 suzukaze update end

	$aryData["dtmInsertDate"] = date("Y/m/d");
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
		fncOutputError ( 1503, DEF_WARNING, "", TRUE, "estimate/regist/edit.php?strSessionID=" . $aryData["strSessionID"] . "&lngFunctionCode=" . DEF_FUNCTION_E1 . "&lngRegist=1", $objDB );
	}

	// 戻りで無い場合、明細データ取得
	if ( $aryData["bytReturnFlag"] != "true" )
	{
		// 見積原価明細データ取得
		$aryDetail = fncGetEstimateDetail( $aryData["lngEstimateNo"], $aryRate, $objDB );
		unset ( $aryCalculated );
	}

	// 明細HIDDEN文字列取得
	list ( $aryDetail, $aryCalculated, $aryHiddenString ) = fncGetEstimateDetailHtml( $aryDetail, "estimate/regist/plan_detail.tmpl", $objDB );
	$aryData["strDetailData"] = join ( "", $aryHiddenString );

// 2004.10.02 suzukaze update start
	$aryData["strPageCondition"] = "renew";
// 2004.10.02 suzukaze update end

// 2004.10.04 tomita update start
	$aryData["RENEW"] = TRUE;
// 2004.10.04 tomita update end
}
else
{
	$aryData["dtmInsertDate"] = date("Y/m/d");
}


//fncDebug( 'es_temp1.txt', $aryData, __FILE__, __LINE__);


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
		$aryData["strProductCode_Error_Message"] = fncOutputError ( 1505, DEF_WARNING, "", FALSE, "estimate/regist/edit.php?strSessionID=" . $aryData["strSessionID"] . "&lngFunctionCode=" . DEF_FUNCTION_E1 . "&lngRegist=1", $objDB );
	}
	// 納価および生産予定数が設定されていなければ製品情報が反映されていないとみなす
	else if ( $aryData["curProductPrice"] == "" and $aryData["lngProductionQuantity"] == "" )
	{
		$lngErrorCount++;
		$aryData["strProductCode_Error"]         = "visibility:visible;";
		$aryData["strProductCode_Error_Message"] = fncOutputError ( 1506, DEF_WARNING, "", FALSE, "estimate/regist/edit.php?strSessionID=" . $aryData["strSessionID"] . "&lngFunctionCode=" . DEF_FUNCTION_E1 . "&lngRegist=1", $objDB );
	}
	else
	{
		// 製品コード情報を取得する
		$aryProduct = fncGetProduct( $aryData["strProductCode"], $objDB, $lngUserCode );
		if ( $aryProduct == FALSE )
		{
			$lngErrorCount++;
			$aryData["strProductCode_Error"]         = "visibility:visible;";
			$aryData["strProductCode_Error_Message"] = fncOutputError ( 1504, DEF_WARNING, "", FALSE, "estimate/regist/edit.php?strSessionID=" . $aryData["strSessionID"] . "&lngFunctionCode=" . DEF_FUNCTION_E1 . "&lngRegist=1", $objDB );
		}
	}

	// 登録にて製品コードが指定されている場合のみ
	if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_E1 and $aryData["strProductCode"] != "" )
	{
		// 製品情報が存在する場合は製品使用可能かどうかのチェック
		$aryEstimate = fncGetEstimateToProductCode( $aryData["strProductCode"], $objDB );

		if ( $aryEstimate != FALSE )
		{
			// ファイルテンポラリ処理以外の場合
			if( !$g_aryTemp["bytTemporaryFlg"] )
			{
				$lngErrorCount++;
				// 指定の製品コードに対して見積もりが存在する場合は、エラー内容をヘッダ部に表示する設定を行う
				$aryData["strHeaderErrorMessage"] = fncOutputError ( 1501, DEF_WARNING, "", FALSE, "estimate/regist/edit.php?strSessionID=" . $aryData["strSessionID"] . "&lngFunctionCode=" . DEF_FUNCTION_E1 . "&lngRegist=1", $objDB );
			}
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
$aryData["filename"] = "edit.php";

$aryData["strActionFile"] = "edit.php";


	$aryData["lngSelfLoginUserCode"] = $lngUserCode; // 入力者コード







//fncDebug( 'es_temp1.txt', $aryData, __FILE__, __LINE__);


//////////////////////////////////////////////////////////////////////////
// 結果取得、出力処理
//////////////////////////////////////////////////////////////////////////
//echo getArrayTable( $aryData, "TABLE" );exit;

//fncDebug( 'estimate.txt', fncGetReplacedHtml( "estimate/regist/parts.tmpl", $aryData, $objAuth ), __FILE__, __LINE__);
echo fncGetReplacedHtml( "estimate/regist/parts.tmpl", $aryData, $objAuth );


$objDB->close();
unset ( $aryData );
unset ( $objAuth );
unset ( $objDB );


return TRUE;
?>
