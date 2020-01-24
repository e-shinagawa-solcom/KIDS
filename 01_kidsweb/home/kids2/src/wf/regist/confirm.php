<?
/** 
*	ワークフロー 案件処理実行画面
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*	更新履歴
*	2004.04.19	処理実行画面から発注詳細表示時にセッションエラーを表示するバグの修正
*	2004.04.20	処理対象の案件が見つからなかった場合のメッセージを変更
*				見つからない状況＝他のユーザーが処理を行った
*
*/
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
// lib_wf.phpにて読み込むクエリを区別するための処理コード(基本はDEF_FUNCTION_WF6)
// edit.php -> lngActionFunctionCode -> confirm.php 処理コード
//
// 表示する案件の機能コード(DEF_FUNCTION)(初期は500:発注管理のみ)
// edit.php -> lngSelectFunctionCode -> confirm.php
//
// 押したボタン(DEF_STATUS_ORDER, DEF_STATUS_DENIAL, DEF_STATUS_CANCELL)
// edit.php -> lngTransactionCode    -> confirm.php

// 案件処理実行へ
// confirm.php -> strSessionID          -> action.php
// confirm.php -> lngFunctionCode       -> action.php
// confirm.php -> lngWorkflowStatusCode -> action.php
// confirm.php -> lngApplicantUserCode  -> action.php
// confirm.php -> lngInputUserCode      -> action.php
// confirm.php -> dtmStartDateFrom      -> action.php
// confirm.php -> dtmStartDateTo        -> action.php
// confirm.php -> dtmEndDateFrom        -> action.php
// confirm.php -> dtmEndDateTo          -> action.php
// confirm.php -> lngInChargeCode       -> action.php
// confirm.php -> lngWorkflowCode       -> action.php
// confirm.php -> strNote               -> action.php
//
// lib_wf.phpにて読み込むクエリを区別するための処理コード(基本はDEF_FUNCTION_WF6)
// confirm.php -> lngActionFunctionCode -> action.php 処理コード
//
// 表示する案件の機能コード(DEF_FUNCTION)(初期は500:発注管理のみ)
// confirm.php -> lngSelectFunctionCode -> action.php
//
// 押したボタン(DEF_STATUS_ORDER, DEF_STATUS_DENIAL, DEF_STATUS_CANCELL)
// confirm.php -> lngTransactionCode    -> action.php

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
$aryCheck["lngWorkflowStatusCode"] = "number(" . DEF_STATUS_VOID . "," . DEF_STATUS_DENIAL . ")";
$aryCheck["lngApplicantUserCode"]  = "number(0,32767)";
$aryCheck["lngInputUserCode"]      = "number(0,32767)";
$aryCheck["dtmStartDateFrom"]      = "date(/)";
$aryCheck["dtmStartDateTo"]        = "date(/)";
$aryCheck["dtmEndDateFrom"]        = "date(/)";
$aryCheck["dtmEndDateTo"]          = "date(/)";
$aryCheck["lngInChargeCode"]       = "number(0,32767)";
$aryCheck["lngPage"]               = "number(0,1000)";
$aryCheck["lngWorkflowCode"]       = "number(0,2147483647)";
$aryCheck["lngActionFunctionCode"] = "number(0,32767)";
$aryCheck["lngSelectFunctionCode"] = "number(0,32767)";
$aryCheck["lngTransactionCode"]    = "number(0,32767)";
$aryCheck["strNote"]               = "length(0,300)";

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

//////////////////////////////////////////////////////////////////////////
// 確認
//////////////////////////////////////////////////////////////////////////

// 申請中 または 否認 かつ
// 承認者がログインユーザーと同じ
if ( ( $objResult->tstatuscode == DEF_STATUS_ORDER || $objResult->tstatuscode == DEF_STATUS_DENIAL ) && $objResult->lnginchargecode == $objAuth->UserCode )
{
	////////////////////////////////////////////////
	// 申請・否認
	////////////////////////////////////////////////
	if ( $aryData["lngActionFunctionCode"] == DEF_STATUS_APPROVE )
	{
		//echo "承認処理";
	}
	elseif ( $aryData["lngActionFunctionCode"] == DEF_STATUS_DENIAL )
	{
		//echo "否認処理";
	}
}


// 申請中 かつ
// 入力者がログインユーザーと同じ
elseif ( $objResult->tstatuscode == DEF_STATUS_ORDER && $objResult->lnginputusercode == $objAuth->UserCode )
{
	////////////////////////////////////////////////
	// 申請取消
	////////////////////////////////////////////////
	//echo "取消処理";
}

// 申請中 かつ
// ログインユーザーのワークフロー順番＜現在の順番である
// 場合は「申請取消」を表示
elseif ( $objResult->tstatuscode == DEF_STATUS_ORDER )
{
	// ユーザーコードからワークフロー順序コードと順序番号を取得
	list ( $aryWorkflowOrderCode, $aryWorkflowOrderNo ) = fncGetArrayData( $objAuth->UserCode, 0, $objDB );

	// ログインユーザーのワークフロー順番番号が
	// 表示する案件の番号より小さい場合
	// 場合は「申請取消」を表示
	for ( $j = 0; $j < count ( $aryWorkflowOrderCode ); $j++ )
	{
		if ( $aryWorkflowOrderCode[$j] == $objResult->lngworkflowordercode && $aryWorkflowOrderNo[$j] < $objResult->lngworkfloworderno )
		{
			////////////////////////////////////////////////
			// 申請取消
			////////////////////////////////////////////////
			//echo "取消処理";
			break;
		}
	}
}

/*
//
// 発注・ワークフローの場合
//
if( $objResult->lngfunctioncode == DEF_FUNCTION_PO1 )
{
	// 発注にて指定している製品コードの取得処理
	$strProductCodeQuery = "SELECT od.strProductCode as strProductCode FROM t_OrderDetail od WHERE od.lngOrderNo = " . $objResult->strworkflowkeycode;

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
		$aryParts["strWorkflowName"]   = "<td class=\"Segs\" onClick=\"javascript:fncShowWfDialogCommon('/po/result/index2.php?strSessionID=" . $aryData["strSessionID"] . "&lngOrderNo=" . $objResult->strworkflowkeycode . "' , window.form1 , 'ResultIframeWf' , 'YES' , " . $_COOKIE["lngLanguageCode"] . " , 'detail', 505, 679, 6, 30 );\"><a class=wfA href=\"/estimate/result/detail.php?strSessionID=" . $aryData["strSessionID"] . "&lngEstimateNo=" . $lngEstimateNo . "\" target=_blank>" . $objResult->strworkflowname . "</a></td>";
	}
}
//
// 見積原価のワークフローの場合
//
elseif( $objResult->lngfunctioncode == DEF_FUNCTION_E1 )
{
	// 見積原価のワークフローの場合、見積原価情報内容のウィンドウを開く処理
	$aryParts["strWorkflowName"]   = "<td class=\"Segs\"><a class=wfA href=\"/estimate/result/detail.php?strSessionID=" . $aryData["strSessionID"] . "&lngEstimateNo=" . $objResult->strworkflowkeycode . "\" target=_blank>" . $objResult->strworkflowname . "</a></td>";
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


$aryParts["dtmStartDate"]          =& $objResult->dtmstartdate;
// $aryParts["strWorkflowName"]       =& $objResult->strworkflowname;
$aryParts["strApplicantName"]      =& $objResult->strapplicantname;
$aryParts["strInputName"]          =& $objResult->strinputname;
$aryParts["strRecognitionName"]    =& $objResult->strrecognitionname;
$aryParts["dtmLimitDate"]          =& $objResult->dtmlimitdate;
$aryParts["dtmEndDate"]            =& $objResult->dtmenddate;

// 2004.04.19 suzukaze update start
$aryParts["strWorkflowKeyCode"]    =& $objResult->strworkflowkeycode;
// 2004.04.19 suzukaze update end

$aryParts["lngStatusCode"]         =& $aryWorkflowStatus[$objResult->tstatuscode];
$aryParts["lngActionFunctionCode"] =& $aryData["lngActionFunctionCode"];
$aryParts["lngTransactionCode"]    =& $aryData["lngTransactionCode"];
$aryParts["lngWorkflowCode"]       =& $aryData["lngWorkflowCode"];
$aryParts["lngFunctionCode"]       =& $aryData["lngFunctionCode"];

$aryParts["strProcessAction"]      = "#";

//$aryParts["strURL"] = "action.php?$strURL&lngActionFunctionCode=$aryData[lngActionFunctionCode]&lngTransactionCode=$aryData[lngTransactionCode]&lngWorkflowCode=$aryData[lngWorkflowCode]";
//$aryParts["strProcessAction"] = "action.php?$strURL&lngActionFunctionCode=$aryData[lngActionFunctionCode]&lngTransactionCode=$aryData[lngTransactionCode]&lngWorkflowCode=$aryData[lngWorkflowCode]&strNote=' + document.form1.strNote.value + '";


$aryParts["strMode"]         =  "confirm";
$aryParts["lngLanguageCode"] =& $_COOKIE["lngLanguageCode"];
$aryParts["strSessionID"]    =& $aryData["strSessionID"];



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



$objDB->freeResult( $lngResultID );

$objDB->close();


return TRUE;
?>
