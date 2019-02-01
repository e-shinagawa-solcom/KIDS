<?

// ----------------------------------------------------------------------------
/**
*       ����ե� �Ʒ�����¹Բ���
*
*
*       @package    K.I.D.S.
*       @license    http://www.kuwagata.co.jp/
*       @copyright  KUWAGATA CO., LTD.
*       @author     K.I.D.S. Groups <info@kids-groups.com>
*       @access     public
*       @version    2.00
*
*
*       ��������
*
*		lib_wf.php�ˤ��ɤ߹��९�������̤��뤿��ν���������(���ܤ�DEF_FUNCTION_WF6)
*		confirm.php -> lngActionFunctionCode -> action.php ����������
*	
*		ɽ������Ʒ�ε�ǽ������(DEF_FUNCTION)(�����500:ȯ������Τ�)
*		confirm.php -> lngSelectFunctionCode -> action.php
*	
*		�������ܥ���(DEF_STATUS_ORDER, DEF_STATUS_DENIAL, DEF_STATUS_CANCELL)
*		confirm.php -> lngTransactionCode    -> action.php
*
*       ��������
*
*/
// ----------------------------------------------------------------------------


	// �����ɤ߹���
	require('conf.inc');

	// �饤�֥���ɤ߹���
	require (LIB_FILE);
	require (SRC_ROOT . "wf/cmn/lib_wf.php");
	require (LIB_DEBUGFILE);
	require ( CLS_TABLETEMP_FILE ); // Temporary DB Object
	require ( LIB_ROOT . "tabletemp/excel2temp.php" );

	// DB��³
	$objDB   = new clsDB();
	$objAuth = new clsAuth();
	$objDB->open( "", "", "", "" );

	// GET�ǡ�������
	$aryData = $_GET;


	// ������ΰƷ�Τ߽�������ǽ�ʤ��ᡢ���ֿ֡�����פ򸡺����Ȥ��ƶ���
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


	// ���å�����ǧ
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	// ���³�ǧ
	if ( !fncCheckAuthority( DEF_FUNCTION_WF6, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}

	// ʸ��������å�
	$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
	//echo getArrayTable( $aryData, "TABLE" );exit;
	fncPutStringCheckError( $aryCheckResult, $objDB );

	// ���̼����Ϥ�URL����(���å����ID���ڡ������Ƹ������)
	$strURL = fncGetURL( $aryData );

	// ����ե�����
	// �Ʒ��ɤ߹��ߡ��������ܺپ������������ؿ�
	list ( $lngResultID, $lngResultNum, $strErrorMessage ) = getWorkflowQuery( $objAuth->UserCode, $aryData, $objDB );

	if ( !$lngResultNum )
	{
	// ���ξ��֤��оݰƷ郎���Ĥ���ʤ�������¾�Υ桼������������¹Ԥ���
	// ��¾�Υ桼�����ν����ˤ�ꡢ�оݰƷ�ϡֿ�����פǤϤʤ��ʤ�ޤ������פΥ�å�������ɽ������
		fncOutputError ( 803, DEF_WARNING, "", TRUE, "", $objDB );
	}

	$objResult = $objDB->fetchObject( $lngResultID, 0 );

	//////////////////////////////////////////////////////////////////////////
	// �¹Խ���
	//////////////////////////////////////////////////////////////////////////

	// lngWorkflowSubCode���󥯥����
	$lngWorkflowSubCode = $objResult->lngworkflowsubcode + 1;


	// �ȥ�󥶥�����󳫻�
	$objDB->transactionBegin();

	// �ơ��֥��å�
	list ( $lngResultID, $lngResultNum ) = fncQuery( "LOCK TABLE t_Workflow IN EXCLUSIVE MODE", $objDB );
	list ( $lngResultID, $lngResultNum ) = fncQuery( "LOCK TABLE m_Workflow IN EXCLUSIVE MODE", $objDB );
	list ( $lngResultID, $lngResultNum ) = fncQuery( "LOCK TABLE m_Order IN EXCLUSIVE MODE", $objDB );



	// ������ �ޤ��� ��ǧ ����
	// ��ǧ�Ԥ�������桼������Ʊ��
	if ( ( $objResult->tstatuscode == DEF_STATUS_ORDER || $objResult->tstatuscode == DEF_STATUS_DENIAL ) && $objResult->lnginchargecode == $objAuth->UserCode )
	{
		////////////////////////////////////////////////
		// ��������ǧ
		////////////////////////////////////////////////
		list ( $arySendMailAddress, $aryParts["strStatusName"] ) = fncAction( $aryData["lngWorkflowCode"]
																, $lngWorkflowSubCode
																, $objResult->lngworkfloworderno
																, $objResult->ostatuscode
																, $objResult->lngfunctioncode
																, $objResult->strworkflowname
																, $aryData[strNote]
																, $objResult->lnglimitdays
																, $aryData["lngTransactionCode"]
																, $objResult->strworkflowkeycode
																, $objResult->strrecognitionmail
																, $objResult->strinputmail
																, $objResult->bytrecognitionmailflag
																, $objResult->bytinputmailflag
																, $objAuth->UserDisplayName
																, "ApprovalUser"
																, $objResult->lnginchargecode
																, $objAuth->UserCode
																, $objDB );
	}


	// ������ ����
	// ���ϼԤ�������桼������Ʊ��
	elseif ( $objResult->tstatuscode == DEF_STATUS_ORDER && $objResult->lnginputusercode == $objAuth->UserCode )
	{
		////////////////////////////////////////////////
		// �������
		////////////////////////////////////////////////
		list ( $arySendMailAddress, $aryParts["strStatusName"] ) = fncAction( $aryData["lngWorkflowCode"]
																, $lngWorkflowSubCode
																, $objResult->lngworkfloworderno
																, $objResult->ostatuscode
																, $objResult->lngfunctioncode
																, $objResult->strworkflowname
																, $aryData[strNote]
																, $objResult->lnglimitdays
																, $aryData["lngTransactionCode"]
																, $objResult->strworkflowkeycode
																, $objResult->strrecognitionmail
																, $objResult->strinputmail
																, $objResult->bytrecognitionmailflag
																, $objResult->bytinputmailflag
																, $objAuth->UserDisplayName
																, "InputUser"
																, $objResult->lnginchargecode
																, $objAuth->UserCode
																, $objDB );
	}

	// ������ ����
	// ������桼�����Υ���ե����֡㸽�ߤν��֤Ǥ���
	// ���ϡֿ�����áפ�ɽ��
	elseif ( $objResult->tstatuscode == DEF_STATUS_ORDER )
	{
		// �桼���������ɤ������ե���������ɤȽ���ֹ�����
		list ( $aryWorkflowOrderCode, $aryWorkflowOrderNo ) = fncGetArrayData( $objAuth->UserCode, 0, $objDB );

		// ������桼�����Υ���ե������ֹ椬
		// ɽ������Ʒ���ֹ��꾮�������
		// ���ϡֿ�����áפ�ɽ��
		for ( $j = 0; $j < count ( $aryWorkflowOrderCode ); $j++ )
		{
			if ( $aryWorkflowOrderCode[$j] == $objResult->lngworkflowordercode && $aryWorkflowOrderNo[$j] < $objResult->lngworkfloworderno )
			{
				////////////////////////////////////////////////
				// �������
				////////////////////////////////////////////////
				list ( $arySendMailAddress, $aryParts["strStatusName"] ) = fncAction( $aryData["lngWorkflowCode"]
																, $lngWorkflowSubCode
																, $aryWorkflowOrderNo[$j]
																, $objResult->ostatuscode
																, $objResult->lngfunctioncode
																, $objResult->strworkflowname
																, $aryData[strNote]
																, $objResult->lnglimitdays
																, $aryData["lngTransactionCode"]
																, $objResult->strworkflowkeycode
																, $objResult->strrecognitionmail
																, $objResult->strinputmail
																, $objResult->bytrecognitionmailflag
																, $objResult->bytinputmailflag
																, $objAuth->UserDisplayName
																, "ApprovalUser"
																, $objResult->lnginchargecode
																, $objAuth->UserCode
																, $objDB );
				break;
			}
		}
	}

	// �ȥ�󥶥�����󥳥ߥå�
	$objDB->transactionCommit();

//	$aryParts["strSessionID"]    &= $aryData["strSessionID"];
	$aryParts["strSessionID"]    = $aryData["strSessionID"];
	$aryParts["strWorkflowName"]  = $objResult->strworkflowname;
	if ( count ( $arySendMailAddress ) > 0 )
	{
		$aryParts["strMailAddress"] = "[" . join ( ", ", $arySendMailAddress ) . "]����" . $aryParts["strStatusName"] . "�᡼����������ޤ�����";
	}


	//////////////////////////////////////////////////////////////////////////
	// ��̼��������Ͻ���
	//////////////////////////////////////////////////////////////////////////
	$objTemplate = new clsTemplate();

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate->getTemplate( "wf/regist/finish.tmpl" );

	// �ƥ�ץ졼������
	$objTemplate->replace( $aryParts );
	$objTemplate->complete();

//fncDebug('wf.txt', $objTemplate->strTemplate, __FILE__, __LINE__);

	// HTML����
	echo $objTemplate->strTemplate;


	$objDB->close();


	//////////////////////////////////////////////////////////////////////////////
	// �����ؿ�
	//////////////////////////////////////////////////////////////////////////////
	/**
	* �����ؿ�
	*
	*	�����ؿ�
	*
	*	@param  Long   $lngWorkflowCode        ���򤷤��Ʒ�Υ���ե�������
	*	@param  Long   $lngWorkflowSubCode     ���򤷤��Ʒ�Υ���ե����֥�����
	*	@param  Long   $lngWorkflowOrderNo     ���򤷤��Ʒ�Υ���ե����������
	*	@param  Long   $lngWorkflowStatusCode  ���򤷤��Ʒ�Υ���ե����֥�����
	*                                          1:��ǧ�� 2:�ǽ���ǧ��
	*	@param  Long   $lngFunctionCode        ��ǽ������(EX.500ȯ�����)
	*	@param  String $strWorkflowName        ����ե�̾
	*	@param  String $strNote                ����(�᡼��˽񤭹���)
	*	@param  Long   $lngLimitDays           ������
	*	@param  Long   $lngTransactionCode     ���򤷤��Ʒ�ε�ǽ������
	*                                          ��������ǧ���������
	*	@param  String $strWorkflowKeyCode     ����������(�Ƶ�ǽ�Υ�������)
	*	@param  String $strRecognitionMail     ��ǧ�ԤΥ᡼�륢�ɥ쥹
	*	@param  String $strInputMail           �����ԤΥ᡼�륢�ɥ쥹
	*	@param  String $bytRecognitionMailFlag ��ǧ�ԤΥ᡼���ۿ����ĥե饰
	*	@param  String $bytInputMailFlag       �����ԤΥ᡼���ۿ����ĥե饰
	*	@param  String $strUserDisplayName     ������桼������ɽ��̾
	*	@param  String $strActionUser          ������¹Ԥ��Ƥ���桼�����ξ���
	*                                          ��ǧ��:ApprovalUser
	*                                          ������:InputUser
	*	@param  Long   $lngInChargeCode        ��ǧ�ԥ�����
	*	@param  Long   $lngUserCode            ������桼����������
	*	@param  Object $objDB                  DB���֥�������
	*	@return Array  $arySendMailAddress     ���������顼��å�����
	*	        String $strStatusName          ��������
	*	@access public
	*/
	function fncAction( $lngWorkflowCode
						, $lngWorkflowSubCode
						, $lngWorkflowOrderNo
						, $lngWorkflowStatusCode
						, $lngFunctionCode
						, $strWorkflowName
						, $strNote
						, $lngLimitDays
						, $lngTransactionCode
						, $strWorkflowKeyCode
						, $strRecognitionMail
						, $strInputMail
						, $bytRecognitionMailFlag
						, $bytInputMailFlag
						, $strUserDisplayName
						, $strActionUser
						, $lngInChargeCode
						, $lngUserCode
						, $objDB )
	{
		$aryData["strWorkflowName"]    = $strWorkflowName;
		$aryData["strNote"]            = $strNote;
		$aryData["strUserDisplayName"] = $strUserDisplayName;
		$aryData["strURL"] = LOGIN_URL;
		$aryQuery = array ();

		$strCommitStatusCode = "null";	// �����ơ��֥�Υ��ơ�����������

		// �ǽ���ǧ����ǧ����������Ѥ˥�����桼�����Υ᡼�륢�ɥ쥹�����
		$strQuery = "SELECT strMailAddress FROM m_User WHERE lngUserCode = " . $lngUserCode;

		list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

		if ( $lngResultNum > 0 )
		{
			$objResult = $objDB->fetchObject( $lngResultID, 0 );
			$objDB->freeResult( $lngResultID );

			// �ǥե�����ͤȤ��������ԥ��ɥ쥹�������桼�����Υ᡼�륢�ɥ쥹�Ȥ���
			// �ʤ����������澵ǧ�����ξ��Ͽ����Ԥξ���Ǿ�񤭤����
			$strFromMailAddress = $objResult->strmailaddress;
		}

		//////////////////////////////////////////////////////////////////
		// ���澵ǧ����(lngStatusCode = DEF_APPROVER)
		//////////////////////////////////////////////////////////////////
		if ( $lngTransactionCode == DEF_STATUS_ORDER 
			&& $lngWorkflowStatusCode == DEF_APPROVER 
			&& $lngInChargeCode == $lngUserCode )
		{
			// lngWorkflowOrderNo���󥯥����
			$lngWorkflowOrderNo++;

			$aryQuery[0] = "INSERT INTO t_Workflow " .
	                       "VALUES ( $lngWorkflowCode, $lngWorkflowSubCode," .
	                       " $lngWorkflowOrderNo, " . DEF_STATUS_ORDER . "," .
	                       " '$strNote', now()," .
	                       " now() + ( interval '$lngLimitDays day' ) )";

			// ����ե������ɤ��鼡�ξ�ǧ�ԤΥ᡼�륢�ɥ쥹�Ȥ��ε��ĥե饰�����
			$strQuery = "SELECT u.strMailAddress, u.bytMailTransmitFlag " .
	                    "FROM m_WorkflowOrder o, m_Workflow m, m_User u " .
	                    "WHERE m.lngWorkflowCode = $lngWorkflowCode" .
	                    " AND o.lngWorkflowOrderNo = $lngWorkflowOrderNo" .
	                    " AND u.bytmailtransmitflag = TRUE" .
	                    " AND o.bytWorkflowOrderDisplayFlag = TRUE" .
	                    " AND o.lngWorkflowOrderCode = m.lngWorkflowOrderCode" .
	                    " AND o.lngInChargeCode = u.lngUserCode";

			list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

			if ( $lngResultNum > 0 )
			{
				$objResult = $objDB->fetchObject( $lngResultID, 0 );
				$objDB->freeResult( $lngResultID );

				// ���ξ�ǧ�Ԥ˥᡼��
				$aryMailTransmit[0] = FALSE;
				if ( $objResult->strmailaddress && $objResult->bytmailtransmitflag == "t" )
				{
					$aryMailTransmit[0] = TRUE;
					$aryMailAddress[0]  = $objResult->strmailaddress;

					// ���澵ǧ�����ξ�硢�᡼����ʸ�˵��ܤ����̾�Ͽ����ԡ����ϼԡ�̾�Ȥ���
					// �ޤ����᡼��������ԤϿ����ԡ����ϼԡˤΥ᡼�륢�ɥ쥹�Ȥ���
					$strQuery = "SELECT m.lngApplicantUserCode as lngApplicantUserCode, "
						. "m.lngInputUserCode as lngInputUserCode, "
						. "au.strUserDisplayName as strApplicantUserDisplayName, "
						. "au.strMailAddress as strApplicantUserMailAddress, " 
						. "iu.strUserDisplayName as strInputUserDisplayName, " 
						. "iu.strMailAddress as strInputUserMailAddress " 
						. "FROM m_Workflow m " 
						. "LEFT JOIN m_User au ON m.lngApplicantUserCode = au.lngUserCode " 
						. "LEFT JOIN m_User iu ON m.lngInputUserCode = iu.lngUserCode " 
						. "WHERE m.lngWorkflowCode = " . $lngWorkflowCode;

					list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

					if ( $lngResultNum > 0 )
					{
						$objResult = $objDB->fetchObject( $lngResultID, 0 );
						$objDB->freeResult( $lngResultID );

						// �����Ԥ����ϼԤξ�����ǧ����
						if ( $objResult->lngapplicantusercode != $objResult->lnginputusercode )
						// �����Ԥ����ϼԤ��㤦��硢���ϼԾ������Ѥ���
						{
							// $aryData �����ꤵ��Ƥ������Ƥ���
							$aryData["strUserDisplayName"] = $objResult->strinputuserdisplayname;
							// �᡼��������Ԥ����ϼԤΥ᡼�륢�ɥ쥹������
							$strFromMailAddress = $objResult->strinputusermailaddress;
						}
						else
						// �����Ԥ����ϼԤ�Ʊ����硢�����Ծ������Ѥ���
						{
							// $aryData �����ꤵ��Ƥ������Ƥ���
							$aryData["strUserDisplayName"] = $objResult->strapplicantuserdisplayname;
							// �᡼��������Ԥ����ԤΥ᡼�륢�ɥ쥹������
							$strFromMailAddress = $objResult->strapplicantusermailaddress;
						}
					}

					list ( $arySubject[0], $aryBody[0] ) = fncGetMailMessage( 807, $aryData, $objDB );
				}
			}
			$strStatusName = "��ǧ";
		}

		//////////////////////////////////////////////////////////////////
		// �ǽ���ǧ����(lngStatusCode = DEF_FINAL_APPROVER)
		//////////////////////////////////////////////////////////////////
		elseif ( $lngTransactionCode == DEF_STATUS_ORDER 
				&& $lngWorkflowStatusCode == DEF_FINAL_APPROVER 
				&& $lngInChargeCode == $lngUserCode )
		{
			// ����ե��ơ��֥�ˡ־�ǧ�פȤ����ɲ�
			$aryQuery[0] = "INSERT INTO t_Workflow " .
	                       "VALUES ( $lngWorkflowCode, $lngWorkflowSubCode," .
	                       " $lngWorkflowOrderNo, " . DEF_STATUS_APPROVE . "," .
	                       " '$strNote', now()," .
	                       " NULL )";

			// ����ե��ޥ������Ρִ�λ���פ򹹿�
			$aryQuery[1] = "UPDATE m_Workflow " .
	                       "SET dtmEndDate = now() " .
	                       "WHERE lngWorkflowCode = $lngWorkflowCode";

			// �����������ơ��֥�ι������ơ����������
			switch ( $lngFunctionCode )
			{
				case DEF_FUNCTION_P1:	// ���ʥޥ�����֥ޥ�������׾��֤ˤ���
					$strCommitStatusCode = DEF_PRODUCT_NORMAL;
					break;
				case DEF_FUNCTION_SO1:	// ����ޥ�������ּ���׾��֤ˤ���
					$strCommitStatusCode = DEF_RECEIVE_ORDER;
					break;
				case DEF_FUNCTION_PO1:	// ȯ��ޥ��������ȯ��׾��֤ˤ���
					$strCommitStatusCode = DEF_ORDER_ORDER;
					break;
				case DEF_FUNCTION_SC1:	// ���ޥ��������Ǽ����׾��֤ˤ���
					$strCommitStatusCode = DEF_ORDER_DELIVER;
					break;
				case DEF_FUNCTION_SO1:	// �����ޥ��������Ǽ����׾��֤ˤ���
					$strCommitStatusCode = DEF_STOCK_DELIVER;
					break;
				case DEF_FUNCTION_E1:	// ���Ѹ����������Ф������ե�
					$strCommitStatusCode = DEF_ESTIMATE_APPROVE;
					break;
			}

			// �б�����ǡ���������å�����
			switch ( $lngFunctionCode )
			{
				case DEF_FUNCTION_SO1:	// ���������ǡ����Υ����å���
					$arySql = array();
					$arySql[] = "select count(*) as count";
					$arySql[] = "from";
					$arySql[] = "	m_sales ms";
					$arySql[] = "		left join t_salesdetail tsd on tsd.lngsalesno = ms.lngsalesno";
					$arySql[] = "where";
					$arySql[] = "tsd.lngreceiveno in ";
					$arySql[] = "(";
					$arySql[] = "	select ms1.lngreceiveno";
					$arySql[] = "	from";
					$arySql[] = "		m_receive ms1";
					$arySql[] = "	where";
					$arySql[] = "		ms1.strreceivecode = (select strreceivecode from m_receive where lngreceiveno = $strWorkflowKeyCode)";
					$arySql[] = ")";
					$arySql[] = "and ms.bytinvalidflag = false";
					$arySql[] = "AND ms.lngRevisionNo = (";
					$arySql[] = "	SELECT MAX( s1.lngRevisionNo ) FROM m_Sales s1 WHERE s1.bytInvalidFlag = false and s1.strSalesCode = ms.strSalesCode)";
					$arySql[] = "	AND 0 <= (";
					$arySql[] = "		SELECT MIN( s2.lngRevisionNo ) FROM m_Sales s2 WHERE s2.bytInvalidFlag = false and s2.strSalesCode = ms.strSalesCode )";

					$strQuery = implode("\n", $arySql);
					// �ģ��䤤��碌
					list( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

					if ( $lngResultNum == 1 )
					{
						$objResult	= $objDB->fetchObject( $lngResultID, 0 );
						// ��İʾ塢���ǡ���������С���Ǽ����פȤ���
						if( 1 <= (int)$objResult->count)
						{
							$strCommitStatusCode = DEF_RECEIVE_DELIVER;
						}
					}
					break;

				case DEF_FUNCTION_PO1:	// ȯ���ʻ����ǡ����Υ����å���
					$arySql = array();
					$arySql[] = "select count(*) as count";
					$arySql[] = "from";
					$arySql[] = "	m_stock ms";
					//--		left join t_stockdetail tsd on tsd.lngstockno = ms.lngstockno
					$arySql[] = "where";
					$arySql[] = "ms.lngorderno in ";
					$arySql[] = "(";
					$arySql[] = "	select mo1.lngorderno";
					$arySql[] = "	from";
					$arySql[] = "		m_order mo1";
					$arySql[] = "	where";
					$arySql[] = "		mo1.strordercode = (select strordercode from m_order where lngorderno = $strWorkflowKeyCode)";
					$arySql[] = ")";
					$arySql[] = "and ms.bytinvalidflag = false";
					$arySql[] = "AND ms.lngRevisionNo = (";
					$arySql[] = "	SELECT MAX( s1.lngRevisionNo ) FROM m_stock s1 WHERE s1.bytInvalidFlag = false and s1.strStockCode = ms.strStockCode)";
					$arySql[] = "	AND 0 <= (";
					$arySql[] = "		SELECT MIN( s2.lngRevisionNo ) FROM m_stock s2 WHERE s2.bytInvalidFlag = false and s2.strStockCode = ms.strStockCode )";

					$strQuery = implode("\n", $arySql);
					// �ģ��䤤��碌
					list( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

					if ( $lngResultNum == 1 )
					{
						$objResult	= $objDB->fetchObject( $lngResultID, 0 );
						// ��İʾ塢�����ǡ���������С���Ǽ����פȤ���
						if( 1 <= (int)$objResult->count)
						{
							$strCommitStatusCode = DEF_ORDER_DELIVER;
						}
					}
					break;

				case DEF_FUNCTION_E1:	// ���Ѹ����������Ф������ե�
					// Excel���饢�åץ��ɤ��줿�ե���������������å�
					$arySql = array();
					$arySql[] = "select me.lngtempno as lngtempno";
					$arySql[] = "from";
					$arySql[] = "m_estimate me";
					$arySql[] = "where";
					$arySql[] = "me.lngrevisionno = (select max(lngrevisionno) from m_estimate where lngestimateno = me.lngestimateno)";
					$arySql[] = "and me.lngtempno is not null";
					$arySql[] = "and me.lngestimateno=".$strWorkflowKeyCode;

					$strQuery = implode("\n", $arySql);
					// �ģ��䤤��碌
					list( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
					// lngTempNo ��¸�ߤ��ǧ
					if( $lngResultNum != 1 )
					{
						break;
					}
					// lngTempNo �����
					$objResult = $objDB->fetchObject( $lngResultID, 0 );
					$lngTempNo = trim($objResult->lngtempno);
					if(!is_numeric($lngTempNo))
					{
						break;
					}

					// �ƥ�ݥ��ơ��֥�ξ�����Ѥ��ƾ��ʥޥ�������
					if( !fncTemp2ProductUpdate( $objDB, $lngTempNo) )
					{
						fncOutputError ( 9101, DEF_WARNING, "", TRUE, "", $objDB );
					}

					// �оݸ��Ѹ����ơ��֥��lngTempNo��ä�
					if( !fncDeleteEstimateTempNo( $objDB, $strWorkflowKeyCode ) )
					{
						fncOutputError ( 9101, DEF_WARNING, "", TRUE, "", $objDB );
					}
					break;
			}

//fncDebug('action.txt', $strQuery, __FILE__, __LINE__);
//fncDebug('action.txt', $objResult->count, __FILE__, __LINE__);

/*2011 12 9 kou and
ȯ���ǽ���ǧ��å����������ʥ������ɲ�*/

			if($lngFunctionCode == DEF_FUNCTION_PO1)
			{
				$strQuery = "SELECT distinct mp.strProductCode as strProductCode" .
     						",mp.strProductName as strProductName " .
						" FROM m_Product mp,t_OrderDetail tod " .
						" WHERE tod.strProductCode = mp.strProductCode " .
						" AND tod.lngOrderNo = $strWorkflowKeyCode " ;
					list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

					if ( $lngResultNum > 0 )
					{
						$objResult = $objDB->fetchObject( $lngResultID, 0 );
						$objDB->freeResult( $lngResultID );

						$strProductCode = $objResult->strproductcode;
						$strProductName = $objResult->strproductname;
					}

				$aryData["strWorkflowName"]    = "���ʥ����ɡ�[ ". $strProductCode . "]\n ����̾�Ρ�". $strProductName ."\n ". $strWorkflowName;
			
			}


//fncDebug('action.txt', $objResult->strproductcode, $objResult->strproductname __FILE__, __LINE__);
/*
		$aryParts["strMailAddress"] = "[" . join ( ", ", $arySendMailAddress ) . "]����" . $aryParts["strStatusName"] . "�᡼����������ޤ�����";
2011 12 9 kou end
*/
			// �����Ԥ˥᡼��
			$aryMailTransmit[0] = FALSE;
			if ( $bytInputMailFlag == "t" )
			{
				$aryMailTransmit[0] = TRUE;
				$aryMailAddress[0]  = $strInputMail;
				list ( $arySubject[0], $aryBody[0] ) = fncGetMailMessage( 808, $aryData, $objDB );
			}
			$strStatusName = "�ǽ���ǧ";
		}

		//////////////////////////////////////////////////////////////////
		// ��ǧ����
		//////////////////////////////////////////////////////////////////
		elseif ( $lngTransactionCode == DEF_STATUS_DENIAL && $lngInChargeCode == $lngUserCode )
		{

			// �����������ơ��֥�ι������ơ����������
			switch ( $lngFunctionCode )
			{
				case DEF_FUNCTION_P1:	// ���ʥޥ�����֥ޥ�������׾��֤ˤ���
					$strCommitStatusCode = DEF_PRODUCT_NORMAL;
					break;
				// ���ʥޥ����ʳ���NULL
			}

			// ����ե��ơ��֥�ˡ���ǧ�פȤ����ɲ�
			$aryQuery[0] = "INSERT INTO t_Workflow " .
	                       "VALUES ( $lngWorkflowCode, $lngWorkflowSubCode," .
	                       " $lngWorkflowOrderNo, " . DEF_STATUS_DENIAL . "," .
	                       " '$strNote', now()," .
	                       " NULL )";

			// ����ե��ޥ������Ρִ�λ���פ򹹿�
			$aryQuery[1] = "UPDATE m_Workflow " .
	                       "SET dtmEndDate = now() " .
	                       "WHERE lngWorkflowCode = $lngWorkflowCode";

			// �����Ԥ˥᡼��
			$aryMailTransmit[0] = FALSE;
			if ( $bytInputMailFlag == "t" )
			{
				$aryMailTransmit[0] = TRUE;
				$aryMailAddress[0]  = $strInputMail;
				list ( $arySubject[0], $aryBody[0] ) = fncGetMailMessage( 809, $aryData, $objDB );
			}
			$strStatusName = "��ǧ";
		}

		//////////////////////////////////////////////////////////////////
		// ������ý���
		//////////////////////////////////////////////////////////////////
		elseif ( $lngTransactionCode == DEF_STATUS_CANCELL )
		{

			// �����������ơ��֥�ι������ơ����������
			switch ( $lngFunctionCode )
			{
				case DEF_FUNCTION_P1:	// ���ʥޥ�����֥ޥ�������׾��֤ˤ���
					$strCommitStatusCode = DEF_PRODUCT_NORMAL;
					break;
				// ���ʥޥ����ʳ���NULL
			}

			// ����ե��ơ��֥�ˡֿ�����áפȤ����ɲ�
			$aryQuery[0] = "INSERT INTO t_Workflow " .
	                       "VALUES ( $lngWorkflowCode, $lngWorkflowSubCode," .
	                       " $lngWorkflowOrderNo, " . DEF_STATUS_CANCELL . "," .
	                       " '$strNote', now(), NULL )";

			// ����ե��ޥ������Ρִ�λ���פ򹹿�
			$aryQuery[1] = "UPDATE m_Workflow " .
	                       "SET dtmEndDate = now() " .
	                       "WHERE lngWorkflowCode = $lngWorkflowCode";

			// ���ߤξ�ǧ�Ԥ˥᡼��
			$aryMailTransmit[0] = FALSE;
			if ( $bytRecognitionMailFlag == "t" )
			{
				$aryMailTransmit[0] = TRUE;
				$aryMailAddress[0]  = $strRecognitionMail;
				list ( $arySubject[0], $aryBody[0] ) = fncGetMailMessage( 810, $aryData, $objDB );
			}

			// ������桼��������ǧ�Ԥ��ä����
			if ( $strActionUser == "ApprovalUser" )
			{
				// �����Ԥ˥᡼��
				$aryMailTransmit[1] = FALSE;
				if ( $bytInputMailFlag == "t" )
				{
					$aryMailTransmit[1] = TRUE;
					$aryMailAddress[1]  = $strInputMail;
					list ( $arySubject[1], $aryBody[1] ) = fncGetMailMessage( 810, $aryData, $objDB );
				}
			}
			$strStatusName = "���";
		}

		//
		// �ǽ���ǧ����ǧ��������ý����ξ��Τߡ������������ơ��֥�Υ��ơ������򹹿�����
		//	$strCommitStatusCode �����ꤵ��ʤ���硢���ơ�������null�ǹ���
		//
		if(    ($lngTransactionCode == DEF_STATUS_ORDER  && $lngWorkflowStatusCode == DEF_FINAL_APPROVER )
			|| ($lngTransactionCode == DEF_STATUS_DENIAL && $lngInChargeCode == $lngUserCode)
			|| ($lngTransactionCode == DEF_STATUS_CANCELL)
			)
		{
			$arySqlLine = array();
			
			switch ( $lngFunctionCode )
			{
				case DEF_FUNCTION_P1:	// ���ʥޥ�����֥ޥ�������׾��֤ˤ���
					$arySqlLine[] = "UPDATE m_Product ";
					$arySqlLine[] = "SET lngProductStatusCode = " . $strCommitStatusCode . ",";
					$arySqlLine[] = "dtmUpdateDate = now()";
					$arySqlLine[] = " WHERE lngProductNo = $strWorkflowKeyCode";
					$arySqlLine[] = " AND bytInvalidFlag = FALSE";
					break;
				case DEF_FUNCTION_SO1:	// ����ޥ�������֡����׾��֤ˤ��롡�ʼ���Ǽ�����
					$arySqlLine[] = "UPDATE m_Receive ";
					$arySqlLine[] = "SET lngReceiveStatusCode = " . $strCommitStatusCode . ",";
					$arySqlLine[] = "dtmInsertDate = now()";
					$arySqlLine[] = " WHERE lngReceiveNo = $strWorkflowKeyCode";
					$arySqlLine[] = " AND lngRevisionNo > -1";
					$arySqlLine[] = " AND bytInvalidFlag = FALSE";
					break;
				case DEF_FUNCTION_PO1:	// ȯ��ޥ�������֡����׾��֤ˤ��롡��ȯ��Ǽ�����
					$arySqlLine[] = "UPDATE m_Order ";
					$arySqlLine[] = "SET lngOrderStatusCode = " . $strCommitStatusCode . ",";
					$arySqlLine[] = "dtmInsertDate = now()";
					$arySqlLine[] = " WHERE lngOrderNo = $strWorkflowKeyCode";
					$arySqlLine[] = " AND lngRevisionNo > -1";
					$arySqlLine[] = " AND bytInvalidFlag = FALSE";
					break;
				case DEF_FUNCTION_SC1:	// ���ޥ��������Ǽ����׾��֤ˤ���
					$arySqlLine[] = "UPDATE m_Order ";
					$arySqlLine[] = "SET lngOrderStatusCode = " . $strCommitStatusCode . ",";
					$arySqlLine[] = "dtmInsertDate = now()";
					$arySqlLine[] = " WHERE lngOrderNo = $strWorkflowKeyCode";
					$arySqlLine[] = " AND lngRevisionNo > -1";
					$arySqlLine[] = " AND bytInvalidFlag = FALSE";
					break;
				case DEF_FUNCTION_PC1:	// �����ޥ��������Ǽ����׾��֤ˤ���
					$arySqlLine[] = "UPDATE m_Stock ";
					$arySqlLine[] = "SET lngStockStatusCode = " . $strCommitStatusCode . ",";
					$arySqlLine[] = "dtmInsertDate = now()";
					$arySqlLine[] = " WHERE lngStockNo = $strWorkflowKeyCode";
					$arySqlLine[] = " AND lngRevisionNo > -1";
					$arySqlLine[] = " AND bytInvalidFlag = FALSE";
					break;
				case DEF_FUNCTION_E1:	// ���Ѹ����������Ф������ե�
					$arySqlLine[] = "UPDATE m_Estimate ";
					$arySqlLine[] = "SET lngEstimateStatusCode = " . $strCommitStatusCode . ",";
					$arySqlLine[] = "dtmInsertDate = now()";
					$arySqlLine[] = " WHERE lngEstimateNo = $strWorkflowKeyCode";
					$arySqlLine[] = " AND lngRevisionNo > -1";
					$arySqlLine[] = " AND bytInvalidFlag = FALSE";
					break;
			}
			// arySqlLine ���
			if( !empty($arySqlLine) )
			{
				$aryQuery[2] = implode("\n", $arySqlLine);
			}
		}

		// ������¹�
		foreach ( $aryQuery as $strQuery )
		{
			//echo "������¹�$strQuery\n";
			if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
			{
				fncOutputError ( 802, DEF_FATAL, "�������ԡ�", TRUE, "", $objDB );
			}

			$objDB->freeResult( $lngResultID );
		}

		// �᡼������
		for ( $i = 0; $i < count ( $aryMailTransmit ); $i++ )
		{
			$strAdminMailAddress = fncGetCommonFunction( "adminmailaddress", "m_adminfunction", $objDB );
			$arySendMailAddress[] = $aryMailAddress[$i];
			
			$bytMailSendFlag = fncSendMail( $aryMailAddress[$i], $arySubject[$i], $aryBody[$i], "From: $strFromMailAddress\nReturn-Path: " . $strAdminMailAddress . "\n" );
			
			if ( !$aryMailTransmit[$i] || !$aryMailAddress[$i] || !bytMailSendFlag )
			{
				$arySendMailAddress[] = fncOutputError ( 9053, DEF_WARNING, "�᡼���������ԡ�", FALSE, "", $objDB );
			}
		}
		return Array ( $arySendMailAddress, $strStatusName );
	}

	return TRUE;
?>
