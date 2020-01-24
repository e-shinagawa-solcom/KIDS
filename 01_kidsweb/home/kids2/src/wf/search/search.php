<?
	/** 
	*	ワークフロー 検索画面
	*
	*	@package   KIDS
	*	@license   http://www.wiseknot.co.jp/ 
	*	@copyright Copyright &copy; 2003, Wiseknot 
	*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
	*	@access    public
	*	@version   1.00
	*
	*/
	// index.php -> strSessionID    -> index.php
	//

	// 設定読み込み
	include_once('conf.inc');

	// ライブラリ読み込み
	require (LIB_FILE);
	require (SRC_ROOT . "wf/cmn/lib_wf.php");

	// DB接続
	$objDB   = new clsDB();
	$objAuth = new clsAuth();
	$objDB->open( "", "", "", "" );

	$aryData   = $_GET;

	$aryParts = fncStringToArray ( $_COOKIE["WorkflowSearch"], "&", ":" );

	//$aryParts = array_merge ( $_GET, $_COOKIE );

	// 文字列チェック
	$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );

	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	// 権限確認
	if ( !fncCheckAuthority( DEF_FUNCTION_WF2, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", FALSE, "", $objDB );
	}
	if ( fncCheckAuthority( DEF_FUNCTION_WF3, $objAuth ) )
	{
		$bytCancellFlag = TRUE;
	}



	// HIDDENタグ生成
	$aryParts["strHiddenForm"] = "
	<input type=\"hidden\" name=\"strSessionID\" value=\"" . $aryData["strSessionID"] . "\">
	<input type=\"hidden\" name=\"lngFunctionCode\" value=\"" . DEF_FUNCTION_WF2 . "\">
	";

	// 承認者取得
	$strQuery = "SELECT" .
	            " DISTINCT ON ( u.lngUserCode )" .
	            " u.lngUserCode, u.strUserFullName " .
	            "FROM m_Workflow m, m_WorkflowOrder o, m_User u " .
	            "WHERE m.lngWorkflowOrderCode = o.lngWorkflowOrderCode" .
	            " AND o.lngInChargeCode = u.lngUserCode" .
	            " AND " .
	            "(" .
	            "  m.lngInputUserCode = " . $objAuth->UserCode . " " .
	            "   OR o.lngWorkflowOrderCode = " .
	            "  (" .
	            "    SELECT o2.lngWorkflowOrderCode" .
	            "    FROM m_WorkflowOrder o2" .
	            "    WHERE o2.lngInChargeCode = $objAuth->UserCode" .
	            "     AND o.lngWorkflowOrderCode = o2.lngWorkflowOrderCode" .
	            "  )" .
	            ")" .
	            "ORDER BY u.lngUserCode";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		$aryParts["inChargeCodeMenu"] = "	<option value=\"\"></option>\n";
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$objResult = $objDB->fetchObject( $lngResultID, $i );
			$aryParts["inChargeCodeMenu"] .= "	<option value=\"" . $objResult->lngusercode . "\">" . $objResult->struserfullname . "</option>\n";
		}
	}
	$objDB->freeResult( $lngResultID );

	/*
	// lngWorkflowStatusCode SELECTタグ生成
	$aryParts["workflowStatusCodeMenu"] = "
	<option value=\"\"></option>
	<option value=\"" . DEF_STATUS_ORDER . "\">申請中</option>
	<option value=\"" . DEF_STATUS_APPROVE . "\">承認</option>
	<option value=\"" . DEF_STATUS_DENIAL . "\">否認</option>
	";
	*/

	$strFCBuff = '<option value=""></option>'."\n";
	while( list($strKey, $strValue) = each($aryFunctionCode) )
	{
	   $strFCBuff .=  '<option value="'.$strKey.'">'.$strValue.'</option>'."\n";
	}
	$aryParts["selectFunctionCodeMenu"] = $strFCBuff;
/*
	// lngSelectFunctionCode SELECTタグ生成
	$aryParts["selectFunctionCodeMenu"] = "
	<option value=\"\"></option>
	<option value=\"" . DEF_FUNCTION_PO0 . "\">発注</option>
	<option value=\"" . DEF_FUNCTION_E0 . "\">見積原価</option>
	";
*/
	// ワークフロー状態
	if ( $bytCancellFlag )
	{
		$aryParts["workflowStatusCodeMenu"] = fncGetCheckBoxObject( "m_workflowstatus", "lngworkflowstatuscode", "strworkflowstatusname", "lngWorkflowStatusCode[]", 'where lngworkflowstatuscode in (1,4,10,99)', $objDB );
	//	$aryParts["workflowStatusCodeMenu"] .= "<option value=\"" . DEF_STATUS_CANCELL . "\">申請取消</option>\n";
	}
	else
	{
		$aryParts["workflowStatusCodeMenu"] = fncGetCheckBoxObject( "m_workflowstatus", "lngworkflowstatuscode", "strworkflowstatusname", "lngWorkflowStatusCode[]", 'where lngworkflowstatuscode in (1,4,10)', $objDB );
	}

	// ヘルプリンク用機能コードをセット
	$aryParts["lngFunctionCode"] = DEF_FUNCTION_WF2;

	// HTML出力

	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "wf/search/search.tmpl" );
	$objTemplate->replace( $aryParts );
	$objTemplate->complete();
	echo $objTemplate->strTemplate;
	
?>
