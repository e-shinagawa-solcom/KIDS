<?
	/** 
	*	����ե� �Ʒ����ɽ������
	*
	*	@package   KIDS
	*	@license   http://www.wiseknot.co.jp/ 
	*	@copyright Copyright &copy; 2003, Wiseknot 
	*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
	*	@access    public
	*	@version   1.00
	*
	*/
	// -------------------------------------------------------------------------
	// ���̤����ꤲ���ѿ�(�Ϥ�����URL�� lib_wf.php ���� fncGetURL($aryData) �Ǽ�����)
	// *.php -> strSessionID          -> index.php

	// �ɤΥڡ������餭���Τ���Ƚ�̤��뤿��ε�ǽ������
	// *.php -> lngFunctionCode       -> index.php

	// *.php -> lngWorkflowStatusCode        -> index.php
	// *.php -> lngApplicantUserDisplayCode  -> index.php
	// *.php -> lngInputUserDisplayCode      -> index.php
	// *.php -> dtmStartDateFrom             -> index.php
	// *.php -> dtmStartDateTo               -> index.php
	// *.php -> dtmEndDateFrom               -> index.php
	// *.php -> dtmEndDateTo                 -> index.php
	// *.php -> lngInChargeCode              -> index.php

	// ɽ������Ʒ�ε�ǽ������(DEF_FUNCTION)(�����500:ȯ������Τ�)
	// *.php -> lngSelectFunctionCode -> index.php

	// -------------------------------------------------------------------------
	// �Ʒ�������
	// /wf/list/index.php -> strSessionID                       -> index.php
	// /wf/list/index.php -> lngFunctionCode                    -> index.php
	// /wf/list/index.php -> ViewColumn[]                       -> index.php
	// /wf/list/index.php -> SearchColumn[]                     -> index.php
	//
	// ����ɽ������(ViewColumn[]�����)key�Ͽ���Ϣ��value�ϲ���ʸ����
	// /wf/search/search.php -> lngWorkflowStatusCodeVisible       -> index.php
	// /wf/search/search.php -> lngApplicantUserDisplayCodeVisible -> index.php
	// /wf/search/search.php -> lngInputUserCodeVisible            -> index.php
	// /wf/search/search.php -> dtmStartDateVisible                -> index.php
	// /wf/search/search.php -> dtmEndDateVisible                  -> index.php
	// /wf/search/search.php -> lngInChargeCodeVisible             -> index.php
	// /wf/search/search.php -> lngSelectFunctionCodeVisible       -> index.php

	// ����������(SearchColumn[]�����)key�Ͽ���Ϣ��value�ϲ���ʸ����
	// /wf/search/search.php -> lngWorkflowStatusCodeConditions   -> index.php
	// /wf/search/search.php -> lngApplicantUserCodeConditions    -> index.php
	// /wf/search/search.php -> lngInputUserDisplayCodeConditions -> index.php
	// /wf/search/search.php -> dtmStartDateConditions            -> index.php
	// /wf/search/search.php -> dtmEndDateConditions              -> index.php
	// /wf/search/search.php -> lngInChargeCodeConditions         -> index.php
	// /wf/search/search.php -> lngSelectFunctionCodeConditions   -> index.php
	//
	// -------------------------------------------------------------------------
	// �Ʒ︡�����
	// ���̤����ꤲ���ѿ���
	// /wf/search/search.php -> ViewColumn[]                    -> index.php
	// /wf/search/search.php -> SearchColumn[]                  -> index.php
	// /wf/search/search.php -> lngDefaultNumBerofList          -> index.php
	//
	// -------------------------------------------------------------------------
	// �ڡ����ѹ���
	// ���̤����ꤲ���ѿ���
	// index.php -> lngPage                -> index.php
	// index.php -> strSort                -> index.php
	// index.php -> lngDefaultNumBerofList -> index.php
	//
	// -------------------------------------------------------------------------
	// �����Ȥ�
	// ���̤����ꤲ���ѿ���
	// index.php -> lngPage                -> index.php
	// index.php -> strSort                -> index.php
	// index.php -> lngDefaultNumBerofList -> index.php
	//
	// -------------------------------------------------------------------------
	// �ܺ�ɽ����
	// ���̤����ꤲ���ѿ���
	// index.php -> lngWorkflowCode       -> detail.php
	//
	// -------------------------------------------------------------------------
	// ������
	// ���̤����ꤲ���ѿ���
	// index.php -> lngWorkflowCode       -> edit.php

	// lib_wf.php�ˤ��ɤ߹��९�������̤��뤿��ν���������(���ܤ�DEF_FUNCTION_WF6)
	// index.php -> lngActionFunctionCode -> edit.php

	// �����ɤ߹���
	include_once('conf.inc');

	// �饤�֥���ɤ߹���
	require (LIB_FILE);
	require (SRC_ROOT . "wf/cmn/lib_wf.php");
	require( LIB_DEBUGFILE );

	// DB��³
	$objDB   = new clsDB();
	$objAuth = new clsAuth();
	$objDB->open( "", "", "", "" );

	//////////////////////////////////////////////////////////////////////////
	// POST(����GET)�ǡ�������
	//////////////////////////////////////////////////////////////////////////
	if ( $_POST )
	{
		$aryData = $_POST;
	}
	elseif ( $_GET )
	{
		$aryData = $_GET;
	}

	// ����ɽ�����ܼ���
	if ( $lngArrayLength = count ( $aryData["ViewColumn"] ) )
	{
		$aryColumn = $aryData["ViewColumn"];
		for ( $i = 0; $i < $lngArrayLength; $i++ )
		{
			$aryData[$aryColumn[$i]] = 1;
		}
		$aryData["ViewColumn"] = "";
		$aryColumn = "";
	}

	// ���������ܼ���
	if ( $lngArrayLength = count ( $aryData["SearchColumn"] ) )
	{
		$aryColumn = $aryData["SearchColumn"];
		for ( $i = 0; $i < $lngArrayLength; $i++ )
		{
			$aryData[$aryColumn[$i]] = 1;
		}
		$aryData["SearchColumn"] = "";
		$aryColumn = "";
	}
	
	// �����å��ܥå������Ϥ��줿WF���ơ�������ʸ���������
	$aryData["lngWorkflowStatusCode"] = fncGetArrayToWorkflowStatusCode($aryData["lngWorkflowStatusCode"]);

	//////////////////////////////////////////////////////////////////////////
	// ���å���󡢸��³�ǧ
	//////////////////////////////////////////////////////////////////////////
	// ���å�����ǧ
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	// ���³�ǧ
	if ( ( $aryData["lngFunctionCode"] != DEF_FUNCTION_WF1 && $aryData["lngFunctionCode"] != DEF_FUNCTION_WF2 && $aryData["lngFunctionCode"] != DEF_FUNCTION_WF3 ) || !fncCheckAuthority( $aryData["lngFunctionCode"], $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}
	if ( $aryData["lngFunctionCode"] != DEF_FUNCTION_WF1 && fncCheckAuthority( DEF_FUNCTION_WF3, $objAuth ) )
	{
		$aryData["lngFunctionCode"] = DEF_FUNCTION_WF3;
	}


	//////////////////////////////////////////////////////////////////////////
	// ʸ��������å�
	//////////////////////////////////////////////////////////////////////////
	$aryCheck["strSessionID"]           = "null:numenglish(32,32)";
	$aryCheck["lngFunctionCode"]        = "null:number(" . DEF_FUNCTION_WF1 . "," . DEF_FUNCTION_WF3 . ")";
	//$aryCheck["lngWorkflowStatusCode"]  = "number(" . DEF_STATUS_VOID . "," . DEF_STATUS_DENIAL . ")";
	$aryCheck["lngApplicantUserDisplayCode"] = "numenglish(1,3)";
	$aryCheck["lngInputUserDisplayCode"]     = "numenglish(1,3)";
	$aryCheck["dtmStartDateFrom"]       = "date(/)";
	$aryCheck["dtmStartDateTo"]         = "date(/)";
	$aryCheck["dtmEndDateFrom"]         = "date(/)";
	$aryCheck["dtmEndDateTo"]           = "date(/)";
	$aryCheck["lngInChargeCode"]        = "number(0,32767)";
	$aryCheck["lngPage"]                = "number";
	$aryCheck["lngWorkflowCode"]        = "number(0,2147483647)";
	$aryCheck["lngSelectFunctionCode"]  = "number(0,32767)";
	$aryCheck["lngDefaultNumBerofList"] = "number(0,100)";

	// �ڡ����������
	if ( !$aryData["lngPage"] )
	{
		$aryData["lngPage"] = 0;
	}

	// ʸ��������å�
	$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryCheckResult, $objDB );

	// �����Ƚ����
	if ( !$aryData["strSort"] )
	{
		$aryData["strSort"] = "column_7_ASC";
	}

	// ����ե����֤ν����(�ǥե���ȡ�ֿ�����װƷ�)
	if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_WF1 )
	{
		$aryData["lngWorkflowStatusCode"] = DEF_STATUS_ORDER;
	}

	// ���̵�ǽ�ޥ��������ɽ������פ����
	if ( $aryData["lngDefaultNumBerofList"] == "" )
	{
		$aryData["lngDefaultNumBerofList"] = fncGetCommonFunction( "defaultnumberoflist", "m_commonfunction", $objDB );
	}

	// �ֽ����ܥ����ɽ����ǧ�Τ����
	// ������桼�����Υ���ե������ɤ��ֹ�����
	list ( $aryWorkflowOrderCode, $aryWorkflowOrderNo ) = fncGetArrayData( $objAuth->UserCode, 0, $objDB );

	// ����ե�����
	// �Ʒ��ɤ߹��ߡ��������ܺپ������������ؿ�
	list ( $lngResultID, $lngResultNum, $baseData["strErrorMessage"] ) = getWorkflowQuery( $objAuth->UserCode, $aryData, $objDB );

	// ���̼����Ϥ�URL����(���å����ID���ڡ������Ƹ������)
	$strURL = fncGetURL( $aryData );

	// ���å���������쥳���ɤ����
	$partsData["lngLanguageCode"] = $baseData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

	//////////////////////////////////////////////////////////////////////////
	// ��̼��������Ͻ���
	//////////////////////////////////////////////////////////////////////////
	// �ѡ��ĥƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "wf/result/parts.tmpl" );
	$strPartsTemplate = $objTemplate->strTemplate;


	// �ڡ���������
	if ( $aryData["lngDefaultNumBerofList"] == 0 )
	{
		// ����ɽ��
		$lngStartView = 0;
		$lngEndView   = $lngResultNum;
		$baseData["prev_visibility"] = "hidden";
		$baseData["next_visibility"] = "hidden";
	}
	else
	{
		// �ڡ���������
		$baseData["prev"]            = "index.php?$strURL&strSort=$aryData[strSort]&lngDefaultNumBerofList=$aryData[lngDefaultNumBerofList]&lngPage=" . ( $aryData["lngPage"] - 1 );
		$baseData["prev_visibility"] = "visible";
		if ( $lngResultNum )
		{
			$baseData["page"]            = $aryData["lngPage"] + 1 . "/" . ceil ( $lngResultNum / $aryData["lngDefaultNumBerofList"] );
		}
		$baseData["next"]            = "index.php?$strURL&strSort=$aryData[strSort]&lngDefaultNumBerofList=$aryData[lngDefaultNumBerofList]&lngPage=" . ( $aryData["lngPage"] + 1 );
		$baseData["next_visibility"] = "visible";

		// ������̿���ɽ���ڡ�����ɽ�������顢���ɽ���ΰ������
		if ( $lngResultNum - $aryData["lngDefaultNumBerofList"] * $aryData["lngPage"] <= $aryData["lngDefaultNumBerofList"] )
		{
			$lngStartView = $aryData["lngPage"] * $aryData["lngDefaultNumBerofList"];
			$lngEndView   = $lngResultNum;
			$baseData["next_visibility"] = "hidden";
		}
		else
		{
			$lngStartView = $aryData["lngPage"] * $aryData["lngDefaultNumBerofList"];
			$lngEndView   = ( $aryData["lngPage"] + 1 ) * $aryData["lngDefaultNumBerofList"];
		}
		// �ǽ�Υڡ����ξ�硢��prev�פ�ɽ�����ʤ�
		if ( !$aryData["lngPage"] )
		{
			$baseData["prev"] = "";
			$baseData["prev_visibility"] = "hidden";
		}
	}


	// �ơ��֥����̾�ȥ����Ƚ���
	if ( $aryData["lngSelectFunctionCodeVisible"] )
	{
		// ����
		$baseData["column9"] = "<td id=\"WF11\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_9_ASC&lngPage=$aryData[lngPage]&lngDefaultNumBerofList=$aryData[lngDefaultNumBerofList]';\"><a href=\"#\">����</a></td>";
	}
	if ( $aryData["dtmStartDateVisible"] )
	{
		// ������
		$baseData["column1"] = "<td id=\"WF02\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_1_ASC&lngPage=$aryData[lngPage]&lngDefaultNumBerofList=$aryData[lngDefaultNumBerofList]';\"><a href=\"#\">������</a></td>";
	}
	// �Ʒ����
	$baseData["column2"] = "<td id=\"WF03\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_2_ASC&lngPage=$aryData[lngPage]&lngDefaultNumBerofList=$aryData[lngDefaultNumBerofList]';\"><a href=\"#\">�Ʒ����</a></td>";

	if ( $aryData["lngApplicantUserDisplayCodeVisible"] )
	{
		// ������
		$baseData["column3"] = "<td id=\"WF04\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_3_ASC&lngPage=$aryData[lngPage]&lngDefaultNumBerofList=$aryData[lngDefaultNumBerofList]';\"><a href=\"#\">������</a></td>";
	}

	if ( $aryData["lngInputUserDisplayCodeVisible"] )
	{
		// ���ϼ�
		$baseData["column4"] = "<td id=\"WF05\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_4_ASC&lngPage=$aryData[lngPage]&lngDefaultNumBerofList=$aryData[lngDefaultNumBerofList]';\"><a href=\"#\">���ϼ�</a></td>";
	}

	if ( $aryData["lngInChargeCodeVisible"] )
	{
		// ��ǧ��
		$baseData["column5"] = "<td id=\"WF06\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_5_ASC&lngPage=$aryData[lngPage]&lngDefaultNumBerofList=$aryData[lngDefaultNumBerofList]';\"><a href=\"#\">��ǧ��</a></td>";
	}

	// ����
	$baseData["column6"] = "<td id=\"WF07\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_6_ASC&lngPage=$aryData[lngPage]&lngDefaultNumBerofList=$aryData[lngDefaultNumBerofList]';\"><a href=\"#\">����</a></td>";

	if ( $aryData["dtmEndDateVisible"] )
	{
		// ��λ��
		$baseData["column8"] = "<td id=\"WF10\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_8_ASC&lngPage=$aryData[lngPage]&lngDefaultNumBerofList=$aryData[lngDefaultNumBerofList]';\"><a href=\"#\">��λ��</a></td>";
	}

	if ( $aryData["lngWorkflowStatusCodeVisible"] )
	{
		// ����
		$baseData["column7"] = "<td id=\"WF08\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_7_ASC&lngPage=$aryData[lngPage]&lngDefaultNumBerofList=$aryData[lngDefaultNumBerofList]';\"><a href=\"#\">����</a></td>";
	}

	// Ʊ�����ܤΥ����Ȥϵս�ˤ������
	list ( $column, $lngSort, $DESC ) = split ( "_", $aryData["strSort"] );

	if ( $DESC == 'ASC' )
	{
		$baseData["column" . $lngSort] = ereg_replace ( "ASC", "DESC", $baseData["column" . $lngSort] );
	}

	// $lngStartView ���� $lngEndView �����ѡ��ĥƥ�ץ졼�Ȥ�������
	//for ( $i = 0; $i < $lngResultNum; $i++ )
	for ( $i = $lngStartView; $i < $lngEndView; $i++ )
	{
		$objResult = $objDB->fetchObject( $lngResultID, $i );

		// Ϣ��
		$partsData["number"]            = $i + 1;
		// �ܺ�URL
		$partsData["detail"]            = "/wf/result/detail.php?$strURL&lngWorkflowCode=" . $objResult->lngworkflowcode;
		// ����
		if ( $aryData["lngSelectFunctionCodeVisible"] )
		{
			$partsData["lngSelectFunctionCode"]  = "<td nowrap>" . $aryFunctionCode[$objResult->lngfunctioncode] . "</td>";
		}
		// ������
		if ( $aryData["dtmStartDateVisible"] )
		{
			$partsData["dtmStartDate"]  = "<td nowrap>" . $objResult->dtmstartdate . "</td>";
		}
	/*
		//
		// ȯ������ե��ξ��
		//
		if( $objResult->lngfunctioncode == DEF_FUNCTION_PO1 )
		{
			// ȯ��ˤƻ��ꤷ�Ƥ������ʥ����ɤμ�������
			$strProductCodeQuery = "SELECT od.strProductCode as strProductCode FROM t_OrderDetail od WHERE od.lngOrderNo = " . $objResult->strworkflowkeycode;

			// �ͤ�Ȥ� =====================================
			$lngEstimateNo = "";
			list ( $lngResultProductCodeID, $lngResultProductCodeNum ) = fncQuery( $strProductCodeQuery, $objDB );
			if ( $lngResultProductCodeNum )
			{
				$objProductCodeResult = $objDB->fetchObject( $lngResultProductCodeID, 0 );
				$strProductCode = $objProductCodeResult->strproductcode;


				// ���Ѹ����ǡ�������
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

			// ���˻�������ʥ����ɤ��Ф��Ƹ��Ѹ�������¸�ߤ����
			if ( $lngEstimateNo != "" )
			{
				// ȯ�����Ƥȸ��Ѹ��������Υ�����ɥ��򳫤�����
				$partsData["strWorkflowName"]   = "<td onClick=\"javascript:fncShowWfDialogCommon('/po/result/index2.php?strSessionID=" . $aryData["strSessionID"] . "&lngOrderNo=" . $objResult->strworkflowkeycode . "' , window.form1 , 'ResultIframeWf' , 'YES' , " . $_COOKIE["lngLanguageCode"] . " , 'detail', 505, 679, 6, 30 );\"><a class=wfA href=\"/estimate/result/detail.php?strSessionID=" . $aryData["strSessionID"] . "&lngEstimateNo=" . $lngEstimateNo . "\" target=_blank>" . $objResult->strworkflowname . "</a></td>";
			}
		}
		//
		// ���Ѹ���������ե��ξ��
		//
		elseif( $objResult->lngfunctioncode == DEF_FUNCTION_E0 )
		{
			// ���Ѹ����������ƤΥ�����ɥ��򳫤�����
			$partsData["strWorkflowName"]   = "<td><a class=wfA href=\"/estimate/result/detail.php?strSessionID=" . $aryData["strSessionID"] . "&lngEstimateNo=" . $objResult->strworkflowkeycode . "\" target=_blank>" . $objResult->strworkflowname . "</a></td>";
		}

		//
		// �嵭��ȯ��ʸ��Ѹ�����ʻ�ѡˡ����Ѹ������˳������ʤ���¾�Υ���ե��ξ��
		//
		if( empty($partsData["strWorkflowName"]) )
		{
			$partsData["strWorkflowName"]   = "<td onClick=\"javascript:fncShowDialogCommon('".$aryFunctionLink[$objResult->lngfunctioncode]."?strSessionID=" . $aryData["strSessionID"] . "&".$aryWorkflowKeyName[$objResult->lngfunctioncode]."=" . $objResult->strworkflowkeycode . "' , window.form1 , 'ResultIframeCommon' , 'YES' , " . $_COOKIE["lngLanguageCode"] . " , 'detail' );\"><a href=# class=wfA>" . $objResult->strworkflowname . "</a></td>";
		}
	*/
	
		// �Ʒ����ʳƥ���ե����֤���������
		$partsData["strWorkflowName"] = fncGetWorkflowNameLink( $objDB, $objResult, $aryData["strSessionID"]);

		// ������
		if ( $aryData["lngApplicantUserDisplayCodeVisible"] )
		{
			$partsData["strApplicantName"] = "<td nowrap>" . $objResult->strapplicantname . "</td>";
		}
		// ���ϼ�
		if ( $aryData["lngInputUserDisplayCodeVisible"] )
		{
			$partsData["strInputName"]     = "<td nowrap>" . $objResult->strinputname . "</td>";
		}
		// ��ǧ��
		if ( $aryData["lngInChargeCodeVisible"] )
		{
			$partsData["strRecognitionName"]      = "<td nowrap>" . $objResult->strrecognitionname . "</td>";
		}
		// ����
		$partsData["dtmLimitDate"]  = "<td nowrap>" . $objResult->dtmlimitdate . "</td>";

		// ��λ����
		if ( $aryData["dtmEndDateVisible"] )
		{
			$partsData["dtmEndDate"]    = "<td nowrap>" . $objResult->dtmenddate . "</td>";
		}
		// ����
		if ( $aryData["lngWorkflowStatusCodeVisible"] )
		{
			$partsData["status"]        = "<td id=\"W0_%statusCode%_\" nowrap>" . $aryWorkflowStatus[$objResult->tstatuscode] . "</td>";
		}

		// ����URL�������ܥ���βĻ롢�ԲĻ�ե饰�����
		$bytTransactionFlag = 0;

		// ����URL�������ܥ���βĻ롢�ԲĻ�����
		// �ֿ�����פ��ľ�ǧ�Ԥޤ������ϼԤξ��ɽ��
		if ( $objResult->tstatuscode == DEF_STATUS_ORDER && ( $objResult->lnginchargecode == $objAuth->UserCode || $objResult->lnginputusercode == $objAuth->UserCode ) )
		{
			$bytTransactionFlag = 1;

		}
		elseif ( $objResult->tstatuscode == DEF_STATUS_ORDER && count ( $aryWorkflowOrderCode ) )
		{
			// ������桼�����Υ���ե������ֹ椬
			// ɽ������Ʒ���ֹ��꾮�������
			for ( $j = 0; $j < count ( $aryWorkflowOrderCode ); $j++ )
			{
				if ( $aryWorkflowOrderCode[$j] == $objResult->lngworkflowordercode && $aryWorkflowOrderNo[$j] < $objResult->lngworkfloworderno )
				{
					$bytTransactionFlag = 1;
					break;
				}
			}
		}

		if ( $bytTransactionFlag )
		{
			$partsData["edit_visibility"] = "visible";
			$partsData["edit"]            = "/wf/regist/edit.php?$strURL&lngActionFunctionCode=" . DEF_FUNCTION_WF6 . "&lngWorkflowCode=" . $objResult->lngworkflowcode;
		}
		else
		{
			$partsData["edit_visibility"] = "hidden";
			$partsData["edit"]            = "";
		}

		// ��ǧ�����ڤ����(ʸ���ο����Ѥ���)
		$partsData["limitcolor"] = "";

		if ( $objResult->lnglimitdate < 0 )
		{
			$partsData["limitcolor"] = " style=\"color:#ff0000;\"";
		}

		// �ƥ�ץ졼�Ȥ򥳥ԡ������ǡ���Ϣ������Υ���������˼���
		$strParts = $strPartsTemplate;
		$partsDataKeys = array_keys( $partsData );

		// �����ο������֤�����
		foreach ( $partsDataKeys as $key )
		{
			$strParts = preg_replace ( "/_%" . $key . "%_/", "$partsData[$key]", $strParts );
		}


		// �ѡ��ĥƥ�ץ졼������
		$baseData["tabledata"] .= $strParts;
	}

	$objDB->freeResult( $lngResultID );

	$baseData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];
	// �١����ƥ�ץ졼���ɤ߹���
	$objTemplate->getTemplate( "wf/result/base.tmpl" );

	$baseData["HIDDEN"] = getArrayTable( $aryData, "HIDDEN" );

	// �١����ƥ�ץ졼������
	$objTemplate->replace( $baseData );
	$objTemplate->complete();

	// HTML����
	echo $objTemplate->strTemplate;

	$objDB->close();


	return TRUE;
?>
