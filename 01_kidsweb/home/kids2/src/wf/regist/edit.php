<?
/** 
*	ワークフロー 案件処理画面
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*	更新履歴
*	2004.04.20	処理対象の案件が見つからなかった場合のメッセージを変更
*				見つからない状況＝他のユーザーが処理を行った
*
*/
// index.php -> strSessionID          -> edit.php
// index.php -> lngFunctionCode       -> edit.php
// index.php -> lngWorkflowStatusCode -> edit.php
// index.php -> lngApplicantUserCode  -> edit.php
// index.php -> lngInputUserCode      -> edit.php
// index.php -> dtmStartDateFrom      -> edit.php
// index.php -> dtmStartDateTo        -> edit.php
// index.php -> dtmEndDateFrom        -> edit.php
// index.php -> dtmEndDateTo          -> edit.php
// index.php -> lngInChargeCode       -> edit.php
// index.php -> lngWorkflowCode       -> edit.php
//
// 表示する案件の機能コード(DEF_FUNCTION)(初期は500:発注管理のみ)
// index.php -> lngSelectFunctionCode -> edit.php
//
// lib_wf.phpにて読み込むクエリを区別するための処理コード(基本はDEF_FUNCTION_WF6)
// index.php -> lngActionFunctionCode -> edit.php
//
// 案件処理実行へ
// edit.php -> strSessionID          -> confirm.php
// edit.php -> lngFunctionCode       -> confirm.php
// edit.php -> lngWorkflowStatusCode -> confirm.php
// edit.php -> lngApplicantUserCode  -> confirm.php
// edit.php -> lngInputUserCode      -> confirm.php
// edit.php -> dtmStartDateFrom      -> confirm.php
// edit.php -> dtmStartDateTo        -> confirm.php
// edit.php -> dtmEndDateFrom        -> confirm.php
// edit.php -> dtmEndDateTo          -> confirm.php
// edit.php -> lngInChargeCode       -> confirm.php
// edit.php -> lngWorkflowCode       -> confirm.php
//
// 表示する案件の機能コード(DEF_FUNCTION)(初期は500:発注管理のみ)
// edit.php -> lngSelectFunctionCode -> confirm.php
//
// lib_wf.phpにて読み込むクエリを区別するための処理コード(基本はDEF_FUNCTION_WF6)
// edit.php -> lngActionFunctionCode -> confirm.php
//
// 押したボタン(DEF_STATUS_ORDER, DEF_STATUS_DENIAL, DEF_STATUS_CANCELL)
// edit.php -> lngTransactionCode    -> confirm.php

// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "wf/cmn/lib_wf.php");
require( LIB_DEBUGFILE );

// DB接続
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

// GETデータ取得
$aryData = $_GET;

// 申請中の案件のみ処理が可能なため、状態「申請中」を検索条件として強制
$aryData["lngWorkflowStatusCodeConditions"] =1;
$aryData["lngWorkflowStatusCode"] = DEF_STATUS_ORDER;

$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
$aryCheck["lngFunctionCode"]       = "null:number(" . DEF_FUNCTION_WF1 . "," . DEF_FUNCTION_WF3 . ")";
$aryCheck["lngApplicantUserCode"]  = "number(0,32767)";
$aryCheck["lngInputUserCode"]      = "number(0,32767)";
$aryCheck["dtmStartDateFrom"]      = "date(/)";
$aryCheck["dtmStartDateTo"]        = "date(/)";
$aryCheck["dtmEndDateFrom"]        = "date(/)";
$aryCheck["dtmEndDateTo"]          = "date(/)";
$aryCheck["lngInChargeCode"]       = "number(0,32767)";
$aryCheck["lngWorkflowCode"]       = "number(0,2147483647)";
$aryCheck["lngActionFunctionCode"] = "number(0,32767)";
$aryCheck["lngSelectFunctionCode"] = "number(0,32767)";


// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_WF6, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}


// 文字列チェック
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );

// 共通受け渡しURL生成(セッションID、ページ、各検索条件)
$strURL = fncGetURL( $aryData );

$aryParts["comment"] = "処理を選択してください。";

// ワークフロー管理
// 案件読み込み、検索、詳細情報取得クエリ関数
list ( $lngResultID, $lngResultNum, $strErrorMessage ) = getWorkflowQuery( $objAuth->UserCode, $aryData, $objDB );

if ( !$lngResultNum )
{
// 2004.04.20 suzukaze update start
// この状態で対象案件が見つからない状況＝他のユーザーが処理を実行した
// 「他のユーザーの処理により、対象案件は「申請中」ではなくなりました。」のメッセージを表示する
	fncOutputError ( 803, DEF_WARNING, "", TRUE, "", $objDB );
// 2004.04.20 suzukaze update end
}

$objResult = $objDB->fetchObject( $lngResultID, 0 );
$aryParts["dtmStartDate"]       = $objResult->dtmstartdate;
//$aryParts["strWorkflowName"]    = $objResult->strworkflowname;
$aryParts["strWorkflowKeyCode"] = $objResult->strworkflowkeycode;
$aryParts["strApplicantName"]   = $objResult->strapplicantname;
$aryParts["strInputName"]       = $objResult->strinputname;
$aryParts["strRecognitionName"] = $objResult->strrecognitionname;
$aryParts["dtmLimitDate"]       = $objResult->dtmlimitdate;
$aryParts["dtmEndDate"]         = $objResult->dtmenddate;
$aryParts["lngStatusCode"]      = $aryWorkflowStatus[$objResult->tstatuscode];

/*
//
// 発注・ワークフローの場合
//
if( $objResult->lngfunctioncode == DEF_FUNCTION_PO1 )
{
	// 発注にて指定している製品コードの取得処理
	$strProductCodeQuery = "SELECT od.strProductCode as strProductCode FROM t_OrderDetail od WHERE od.lngOrderNo = " . $aryParts["strWorkflowKeyCode"];

	// 値をとる =====================================
	$lngEstimateNo = "";
	list ( $lngResultProductCodeID, $lngResultProductCodeNum ) = fncQuery( $strProductCodeQuery, $objDB );
	if ( $lngResultProductCodeNum )
	{
		$objProductCodeResult = $objDB->fetchObject( $lngResultProductCodeID, 0 );
		$strProductCode = $objProductCodeResult->strproductcode;

		// 見積原価データ取得
		$aryEstimateQuery[] = "SELECT e.lngEstimateNo ";
		$aryEstimateQuery[] = "FROM m_Estimate e";
		$aryEstimateQuery[] = "WHERE e.strProductCode = '" . $strProductCode . "'";
		$aryEstimateQuery[] = " AND e.lngRevisionNo = (SELECT MAX(e2.lngRevisionNo) FROM m_Estimate e2 WHERE e.lngEstimateNo = e2.lngEstimateNo)";
		$aryEstimateQuery[] = " AND e.bytDecisionFlag = true ";

		list ( $lngResultEstimateID, $lngResultEstimateNum ) = fncQuery( join ( " ", $aryEstimateQuery ), $objDB );

		if ( $lngResultEstimateNum )
		{
			$objEstimateResult = $objDB->fetchObject( $lngResultEstimateID, 0 );
			$objDB->freeResult( $lngResultEstimateID );
			unset ( $lngResultEstimateID );
			unset ( $lngResultEstimateNum );

			$lngEstimateNo = $objEstimateResult->lngestimateno;
			unset ( $objEstimateResult );
		}
		unset( $aryEstimateQuery );
	}
	$objDB->freeResult( $lngResultProductCodeID );

	// 既に指定の製品コードに対して見積原価情報が存在すれば
	if ( $lngEstimateNo != "" )
	{
		// 発注内容と見積原価双方のウィンドウを開く処理
		$aryParts["strWorkflowName"]   = "<td class=\"Segs\" onClick=\"javascript:fncShowWfDialogCommon('/po/result/index2.php?strSessionID=" . $aryData["strSessionID"] . "&lngOrderNo=" . $aryParts["strWorkflowKeyCode"] . "' , window.form1 , 'ResultIframeWf' , 'YES' , " . $_COOKIE["lngLanguageCode"] . " , 'detail', 505, 679, 6, 30 );\"><a class=wfA href=\"/estimate/result/detail.php?strSessionID=" . $aryData["strSessionID"] . "&lngEstimateNo=" . $lngEstimateNo . "\" target=_blank>" . $aryParts["strWorkflowName"] . "</a></td>";
	}
}
//
// 見積原価のワークフローの場合
//
elseif( $objResult->lngfunctioncode == DEF_FUNCTION_E1 )
{
	// 見積原価のワークフローの場合、見積原価情報内容のウィンドウを開く処理
	$aryParts["strWorkflowName"]   = "<td class=\"Segs\"><a class=wfA href=\"/estimate/result/detail.php?strSessionID=" . $aryData["strSessionID"] . "&lngEstimateNo=" . $aryParts["strWorkflowKeyCode"] . "\" target=_blank>" . $aryParts["strWorkflowName"] . "</a></td>";
}

//
// 上記、発注（見積原価・併用）、見積原価、に該当しない、他のワークフローの場合
//
if( empty($aryParts["strWorkflowName"]) )
{
	$aryParts["strWorkflowName"]   = "<td class=\"Segs\" onClick=\"javascript:fncShowDialogCommon('".$aryFunctionLink[$objResult->lngfunctioncode]."?strSessionID=" . $aryData["strSessionID"] . "&".$aryWorkflowKeyName[$objResult->lngfunctioncode]."=" . $objResult->strworkflowkeycode . "' , window.form1 , 'ResultIframeCommon' , 'YES' , " . $_COOKIE["lngLanguageCode"] . " , 'detail' );\"><a href=# class=wfA>" . $objResult->strworkflowname . "</a></td>";
}
*/

// 案件情報（各ワークフロー状態から生成）
$aryParts["strWorkflowName"] = fncGetWorkflowNameLink( $objDB, $objResult, $aryData["strSessionID"]);




//////////////////////////////////////////////////////////////////////////
// ユーザー別ボタン表示処理
//////////////////////////////////////////////////////////////////////////

// 処理ボタン(承認・否認・申請取り消し)表示確認のための
// ログインユーザーのワークフローコードと番号を取得
list ( $aryWorkflowOrderCode, $aryWorkflowOrderNo ) = fncGetArrayData( $objAuth->UserCode, 0, $objDB );

// 申請中 かつ
// 承認者がログインユーザーと同じ場合は「承認」「否認」を表示
if ( $objResult->tstatuscode == DEF_STATUS_ORDER && $objResult->lnginchargecode == $objAuth->UserCode )
{
	$aryParts["strRecognAction"] = "confirm.php?$strURL&lngWorkflowCode=$aryData[lngWorkflowCode]&lngActionFunctionCode=" . DEF_FUNCTION_WF6 . "&lngTransactionCode=" . DEF_STATUS_ORDER;
	$aryParts["strDenyAction"]   = "confirm.php?$strURL&lngWorkflowCode=$aryData[lngWorkflowCode]&lngActionFunctionCode=" . DEF_FUNCTION_WF6 . "&lngTransactionCode=" . DEF_STATUS_DENIAL;
}


// 申請中 かつ
// 入力者がログインユーザーと同じ
// 場合は「申請取り消し」を表示
elseif ( $objResult->tstatuscode == DEF_STATUS_ORDER && $objResult->lnginputusercode == $objAuth->UserCode )
{
	$flgPutButton = TRUE;
}

// 申請中 かつ
// ログインユーザーの順番＜現在の順番である
// 場合は「申請取り消し」を表示
elseif ( $objResult->tstatuscode == DEF_STATUS_ORDER )
{
	// ログインユーザーのワークフロー順番番号が
	// 表示する案件の番号より小さい場合
	for ( $j = 0; $j < count ( $aryWorkflowOrderCode ); $j++ )
	{
		if ( $aryWorkflowOrderCode[$j] == $objResult->lngworkflowordercode && $aryWorkflowOrderNo[$j] < $objResult->lngworkfloworderno )
		{
			$flgPutButton = TRUE;
			break;
		}
	}
}

if ( $flgPutButton )
{
	$aryParts["strCancelAction"] = "confirm.php?$strURL&lngWorkflowCode=$aryData[lngWorkflowCode]&lngActionFunctionCode=" . DEF_FUNCTION_WF6 . "&lngTransactionCode=" . DEF_STATUS_CANCELL;
}


//////////////////////////////////////////////////////////////////////////
// 結果取得、出力処理
//////////////////////////////////////////////////////////////////////////
//$aryParts["close"] = "<h3><a href=\"javascript:window.close();\">閉じる</a></h3>\n";

/*
foreach ( $aryParts as $strKey )
{
	echo $strKey;
}
*/

// HTML出力
//$aryData["RENEW"] = TRUE;
//echo fncGetReplacedHtml( "p/regist/parts.tmpl", $aryData, $objAuth );

$aryParts["strMode"] = "edit";
$aryParts["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];
$aryParts["strSessionID"] =& $aryData["strSessionID"];







$objTemplate = new clsTemplate();

// テンプレート読み込み
if( $lngFunctionCode == DEF_FUNCTION_E1 )
{
	$objTemplate->getTemplate( "wf/regist/confirm_estimate.tmpl" );
}
else
{
	$objTemplate->getTemplate( "wf/regist/confirm.tmpl" );
}

$objTemplate->replace( $aryParts );
$objTemplate->complete();


echo $objTemplate->strTemplate;


$objDB->close();


return TRUE;
?>
