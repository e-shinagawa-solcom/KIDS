<?
	/** 
	*	����ե� ��������
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

	// �����ɤ߹���
	include_once('conf.inc');

	// �饤�֥���ɤ߹���
	require (LIB_FILE);
	require (SRC_ROOT . "wf/cmn/lib_wf.php");

	// DB��³
	$objDB   = new clsDB();
	$objAuth = new clsAuth();
	$objDB->open( "", "", "", "" );

	$aryData   = $_GET;

	$aryParts = fncStringToArray ( $_COOKIE["WorkflowSearch"], "&", ":" );

	//$aryParts = array_merge ( $_GET, $_COOKIE );

	// ʸ��������å�
	$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );

	// ���å�����ǧ
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	// ���³�ǧ
	if ( !fncCheckAuthority( DEF_FUNCTION_WF2, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", FALSE, "", $objDB );
	}
	if ( fncCheckAuthority( DEF_FUNCTION_WF3, $objAuth ) )
	{
		$bytCancellFlag = TRUE;
	}



	// HIDDEN��������
	$aryParts["strHiddenForm"] = "
	<input type=\"hidden\" name=\"strSessionID\" value=\"" . $aryData["strSessionID"] . "\">
	<input type=\"hidden\" name=\"lngFunctionCode\" value=\"" . DEF_FUNCTION_WF2 . "\">
	";

	// ��ǧ�Լ���
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
	// lngWorkflowStatusCode SELECT��������
	$aryParts["workflowStatusCodeMenu"] = "
	<option value=\"\"></option>
	<option value=\"" . DEF_STATUS_ORDER . "\">������</option>
	<option value=\"" . DEF_STATUS_APPROVE . "\">��ǧ</option>
	<option value=\"" . DEF_STATUS_DENIAL . "\">��ǧ</option>
	";
	*/

	$strFCBuff = '<option value=""></option>'."\n";
	while( list($strKey, $strValue) = each($aryFunctionCode) )
	{
	   $strFCBuff .=  '<option value="'.$strKey.'">'.$strValue.'</option>'."\n";
	}
	$aryParts["selectFunctionCodeMenu"] = $strFCBuff;
/*
	// lngSelectFunctionCode SELECT��������
	$aryParts["selectFunctionCodeMenu"] = "
	<option value=\"\"></option>
	<option value=\"" . DEF_FUNCTION_PO0 . "\">ȯ��</option>
	<option value=\"" . DEF_FUNCTION_E0 . "\">���Ѹ���</option>
	";
*/
	// ����ե�����
	if ( $bytCancellFlag )
	{
		$aryParts["workflowStatusCodeMenu"] = fncGetCheckBoxObject( "m_workflowstatus", "lngworkflowstatuscode", "strworkflowstatusname", "lngWorkflowStatusCode[]", 'where lngworkflowstatuscode in (1,4,10,99)', $objDB );
	//	$aryParts["workflowStatusCodeMenu"] .= "<option value=\"" . DEF_STATUS_CANCELL . "\">�������</option>\n";
	}
	else
	{
		$aryParts["workflowStatusCodeMenu"] = fncGetCheckBoxObject( "m_workflowstatus", "lngworkflowstatuscode", "strworkflowstatusname", "lngWorkflowStatusCode[]", 'where lngworkflowstatuscode in (1,4,10)', $objDB );
	}

	// �إ�ץ���ѵ�ǽ�����ɤ򥻥å�
	$aryParts["lngFunctionCode"] = DEF_FUNCTION_WF2;

	// HTML����

	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "wf/search/search.tmpl" );
	$objTemplate->replace( $aryParts );
	$objTemplate->complete();
	echo $objTemplate->strTemplate;
	
?>
