<?
/** 
*	ワークフロー 詳細情報表示画面
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*	更新履歴
*	2004.04.19	詳細画面から発注詳細表示時にセッションエラーを表示するバグの修正
*
*/
// index.php -> strSessionID          -> detail.php
// index.php -> lngFunctionCode       -> detail.php
// index.php -> lngWorkflowStatusCode -> detail.php
// index.php -> lngApplicantUserCode  -> detail.php
// index.php -> lngInputUserCode      -> detail.php
// index.php -> dtmStartDateFrom      -> detail.php
// index.php -> dtmStartDateTo        -> detail.php
// index.php -> dtmEndDateFrom        -> detail.php
// index.php -> dtmEndDateTo          -> detail.php
// index.php -> lngInChargeCode       -> detail.php
// index.php -> lngWorkflowCode       -> detail.php
// index.php -> lngSelectFunctionCode -> index.php

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

$aryCheck["strSessionID"]            = "null:numenglish(32,32)";
$aryCheck["lngFunctionCode"]         = "null:number(" . DEF_FUNCTION_WF1 . "," . DEF_FUNCTION_WF3 . ")";
//$aryCheck["lngWorkflowStatusCode"]   = "number(" . DEF_STATUS_VOID . "," . DEF_STATUS_DENIAL . ")";
$aryCheck["lngApplicantUserCode"]    = "number(0,32767)";
$aryCheck["lngInputUserCode"]        = "number(0,32767)";
$aryCheck["dtmStartDateFrom"]        = "date(/)";
$aryCheck["dtmStartDateTo"]          = "date(/)";
$aryCheck["dtmEndDateFrom"]          = "date(/)";
$aryCheck["dtmEndDateTo"]            = "date(/)";
$aryCheck["lngInChargeCode"]         = "number(0,32767)";
$aryCheck["lngWorkflowCode"]         = "number(0,2147483647)";
$aryCheck["lngSelectFunctionCode"] = "number(0,32767)";

// チェックボックスで渡されたWFステータスを文字列で設定
$aryData["lngWorkflowStatusCode"] = fncGetArrayToWorkflowStatusCode($aryData["lngWorkflowStatusCode"]);

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_WF4, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}
if ( fncCheckAuthority( DEF_FUNCTION_WF5, $objAuth ) )
{
	$lngFunctionCode = DEF_FUNCTION_WF5;
}


// 文字列チェック
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );

// 共通受け渡しURL生成(セッションID、ページ、各検索条件)
$strURL = fncGetURL( $aryData );

// ワークフロー管理
// 案件読み込み、検索、詳細情報取得クエリ関数
list ( $lngResultID, $lngResultNum, $strErrorMessage ) = getWorkflowQuery( $objAuth->UserCode, $aryData, $objDB );

//////////////////////////////////////////////////////////////////////////
// 結果取得、出力処理
//////////////////////////////////////////////////////////////////////////


$objResult = $objDB->fetchObject( $lngResultID, 0 );

$partsData["dtmStartDate"]       = $objResult->dtmstartdate;
//$partsData["strWorkflowName"]    = $objResult->strworkflowname;
$partsData["strApplicantName"]   = $objResult->strapplicantname;
$partsData["strInputName"]       = $objResult->strinputname;
$partsData["strRecognitionName"] = $objResult->strrecognitionname;
$partsData["dtmLimitDate"]       = $objResult->dtmlimitdate;
$partsData["dtmEndDate"]         = $objResult->dtmenddate;
$partsData["strWorkflowKeyCode"] = $objResult->strworkflowkeycode;
$partsData["lngStatusCode"]      = $aryWorkflowStatus[$objResult->lngworkflowstatuscode];
$lngFunctionCode    = $objResult->lngfunctioncode;

/*
//
// 発注・ワークフローの場合
//
if( $objResult->lngfunctioncode == DEF_FUNCTION_PO1 )
{
	// 発注にて指定している製品コードの取得処理
	$strProductCodeQuery = "SELECT od.strProductCode as strProductCode FROM t_OrderDetail od WHERE od.lngOrderNo = " . $partsData["strWorkflowKeyCode"];

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
		$partsData["strWorkflowName"]   = "<td class=\"Segs\" onClick=\"javascript:fncShowWfDialogCommon('/po/result/index2.php?strSessionID=" . $aryData["strSessionID"] . "&lngOrderNo=" . $partsData["strWorkflowKeyCode"] . "' , window.form1 , 'ResultIframeWf' , 'YES' , " . $_COOKIE["lngLanguageCode"] . " , 'detail', 505, 679, 6, 30 );\"><a class=wfA href=\"/estimate/result/detail.php?strSessionID=" . $aryData["strSessionID"] . "&lngEstimateNo=" . $lngEstimateNo . "\" target=_blank>" . $partsData["strWorkflowName"] . "</a></td>";
	}
}
//
// 見積原価のワークフローの場合
//
elseif( $objResult->lngfunctioncode == DEF_FUNCTION_E1 )
{
	// 見積原価のワークフローの場合、見積原価情報内容のウィンドウを開く処理
	$partsData["strWorkflowName"]   = "<td class=\"Segs\"><a class=wfA href=\"/estimate/result/detail.php?strSessionID=" . $aryData["strSessionID"] . "&lngEstimateNo=" . $partsData["strWorkflowKeyCode"] . "\" target=_blank>" . $partsData["strWorkflowName"] . "</a></td>";
}

//
// 上記、発注（見積原価・併用）、見積原価、に該当しない、他のワークフローの場合
//
if( empty($partsData["strWorkflowName"]) )
{
	$partsData["strWorkflowName"]   = "<td class=\"Segs\" onClick=\"javascript:fncShowDialogCommon('".$aryFunctionLink[$objResult->lngfunctioncode]."?strSessionID=" . $aryData["strSessionID"] . "&".$aryWorkflowKeyName[$objResult->lngfunctioncode]."=" . $objResult->strworkflowkeycode . "' , window.form1 , 'ResultIframeCommon' , 'YES' , " . $_COOKIE["lngLanguageCode"] . " , 'detail' );\"><a href=# class=wfA>" . $objResult->strworkflowname . "</a></td>";
}
*/

// 案件情報（各ワークフロー状態から生成）
$partsData["strWorkflowName"] = fncGetWorkflowNameLink( $objDB, $objResult, $aryData["strSessionID"]);

fncDebug('wf.txt', $partsData["strWorkflowName"], __FILE__, __LINE__);


$objDB->freeResult( $lngResultID );


// ワークフロー順番を取得
$strQuery = "SELECT wfo.lngWorkflowOrderNo, u.strUserDisplayName " .
            "FROM m_Workflow wf, m_WorkflowOrder wfo, m_User u " .
            "WHERE wf.lngWorkflowCode = " . $aryData["lngWorkflowCode"] . " " .
            " AND wfo.lngWorkflowOrderCode = wf.lngWorkflowOrderCode " .
            " AND wfo.lngInChargeCode = u.lngUserCode " .
            "ORDER BY wfo.lngWorkflowOrderNo";

list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
if ( $lngResultNum < 1 )
{
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}
for ( $i = 0; $i < $lngResultNum; $i++ )
{
	$objResult = $objDB->fetchObject( $lngResultID, $i );
	$aryWorkflowOrder[] = "<tr><td class=\"SegColumn\">" . $objResult->lngworkfloworderno . "</td><td class=\"Segs\">" . $objResult->struserdisplayname . "</td></tr>\n";
}

$partsData["strWorkflowOrder"] = join ( "", $aryWorkflowOrder );

$partsData["strMode"] = "detail";
$partsData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];
/*
$partsData["strRecognAction"] = "";
$partsData["strDenyAction"] = "";
$partsData["strCancelAction"] = "";
$partsData["strProcessAction"] = "";
*/


// パーツテンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "wf/result/parts.tmpl" );
$strPartsTemplate = $objTemplate->strTemplate;

// テンプレート読み込み
if( $lngFunctionCode == DEF_FUNCTION_E1 )
{
	$objTemplate->getTemplate( "wf/regist/confirm_estimate.tmpl" );
}
else
{
	$objTemplate->getTemplate( "wf/regist/confirm.tmpl" );
}

// テンプレート生成
$objTemplate->replace( $partsData );
$objTemplate->replace( $aryData );
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;
$objDB->close();

return TRUE;
?>
