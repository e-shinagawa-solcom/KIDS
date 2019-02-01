<?php

// ----------------------------------------------------------------------------
/**
*       ������  ��Ͽ����
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
*         �������Ͽ���̤�ɽ��
*         �����ϥ��顼�����å�
*         ����Ͽ�ܥ��󲡲��塢��Ͽ��ǧ���̤�
*
*       ��������
*
*/
// ----------------------------------------------------------------------------



	//-------------------------------------------------------------------------
	// �� �饤�֥��ե������ɹ�
	//-------------------------------------------------------------------------
	include( 'conf.inc' );
	require( LIB_FILE );
	require( LIB_ROOT."libcalc.php" );
	require( SRC_ROOT."po/cmn/lib_po.php" );
	require( SRC_ROOT."pc/cmn/lib_pc.php" );
	require( SRC_ROOT."sc/cmn/lib_sc.php" );
	require( SRC_ROOT."sc/cmn/lib_scp.php" );
	require( LIB_DEBUGFILE );



	//-------------------------------------------------------------------------
	// �� ���֥�����������
	//-------------------------------------------------------------------------
	$objDB   = new clsDB();   // DB���֥�������
	$objAuth = new clsAuth(); // ǧ�ڽ������֥�������


	//-------------------------------------------------------------------------
	// �� �ѥ�᡼������
	//-------------------------------------------------------------------------
	$aryData["strSessionID"]    = $_REQUEST["strSessionID"];   // ���å����ID
	$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"]; // ���쥳����

	$strGetDataMode = $_POST["strGetDataMode"]; // �ǡ����������ƥ⡼��
	$strProcMode    = $_POST["strProcMode"];    // �����⡼��
	$dtmNowDate     = date( 'Y/m/d', time() );  // ��������


	//-------------------------------------------------------------------------
	// �� DB�����ץ�
	//-------------------------------------------------------------------------
	$objDB->open( "", "", "", "" );


	//-------------------------------------------------------------------------
	// �� ����ʸ�����͡����å���󡦸��¥����å�
	//-------------------------------------------------------------------------
	// ʸ��������å�
	$aryCheck["strSessionID"] = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );

	// ���å�����ǧ
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	$lngUserCode  = $objAuth->UserCode;
	$lngUserGroup = $objAuth->AuthorityGroupCode;


	// 600 ������
	if( !fncCheckAuthority( DEF_FUNCTION_SC0, $objAuth ) )
	{
	        fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
	}

	// 601 �������������Ͽ��
	if( fncCheckAuthority( DEF_FUNCTION_SCO1, $objAuth ) )
	{
		$aryData["strRegistURL"]   = "regist/index.php?strSessionID=" . $aryData["strSessionID"];
	}


	// 610 �������ʹ��ɲá��Ժ����
	if( !fncCheckAuthority( DEF_FUNCTION_SC10, $objAuth ) )
	{
		$aryData["adddelrowview"] = 'hidden';
	}




	//-------------------------------------------------------------------------
	// �� ���ϥ����å�
	//-------------------------------------------------------------------------
	if( $strProcMode == "check")
	{
		// ľ��Ͽ�ե饰�μ���
		$lngDirectRegistFlag = $_POST["lngDirectRegistFlag"];


		// ���ٹԤ����
		for( $i = 0; $i < count( $_POST ); $i++ )
		{
			list( $strKeys, $strValues ) = each ( $_POST );

			if($strKeys != "aryPoDitail")
			{
				$aryData[$strKeys] = $strValues;
			}
		}


		//-----------------------------------------------------------
		// �إå������ܥ����å�
		//-----------------------------------------------------------
		list( $aryData, $bytErrorFlag ) = fncCheckData_sc( $aryData, "header", $objDB );

		// ���ʥ����ɤ��տ魯�뾦�ʤ�¸�ߤ��뤫
		for( $i=0; $i < count( $_POST["aryPoDitail"] ); $i++ )
		{
			// ���ʥ����ɣ��������������б�
			$strProductCode = fncGetMasterValue( "m_product", "strproductcode", "strproductcode",  $_POST["aryPoDitail"][$i]["strProductCode"] . ":str",'',$objDB );

			if( !$strProductCode )
			{
				$aryDetailErrorMessage[] = fncOutputError ( 303, "", "", FALSE, "", $objDB );
			}
		}

		// ���顼������Ȥμ���
		$errorCount = ( $bytErrorFlag != "" ) ? 1 : 0;


		//-----------------------------------------------------------
		// ���ٹԤΥ����å�
		//-----------------------------------------------------------
		if( count( $_POST["aryPoDitail"] ) > 0 )
		{
			for( $i = 0; $i < count( $_POST["aryPoDitail"] ); $i++ )
			{
				list( $aryDetailCheck[], $bytErrorFlag2[] ) = fncCheckData_sc( $_POST["aryPoDitail"][$i], "detail", $objDB );
			}
		}

		for( $i=0; $i < count( $bytErrorFlag2 ); $i++ )
		{
			// ���ٹԤΥ��顼�ؿ�
			if( $bytErrorFlag2[$i] == "true" )
			{
				$aryDetailErrorMessage[] = fncDetailError( $bytErrorFlag2 );
			}
		}

		//-----------------------------------------------------------
		// ����No.��¸�ߤ��ʤ����
		//-----------------------------------------------------------
		if( $aryData["strReceiveCode"] == "" )
		{
			// ���ܱߤ��Ѵ�
			$lngPrice = $aryData["curAllTotalPrice"] * $aryData["curConversionRate"];

			// 608 ������(����NO����ꤷ�ʤ���Ͽ����ǽ���ɤ���)
			if( !fncCheckAuthority( DEF_FUNCTION_SC8, $objAuth ) )
			{
				$aryDetailErrorMessage[] = fncOutputError( 607, "", "", FALSE, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}
			else
			{
				// �����ۤ���°ʾ�Ǥ�����Ͽ��ǽ�ʸ��¤���äƤ��ʤ��ʤ�
				if( $lngPrice > DEF_MONEY_NO_CODE and !fncCheckAuthority( DEF_FUNCTION_SC9, $objAuth ) )
				{
					$aryDetailErrorMessage[] = fncOutputError( 704, "", "", FALSE, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
				}
			}

/* *v2*
			if( $lngUserGroup == 5 )					// �桼��
			{
				$aryDetailErrorMessage[] = fncOutputError ( 9060, "", "", FALSE, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}
			elseif( $lngUserGroup == 4 )				// �ޥ͡����㡼
			{
				// 5���ޤ���Ͽ��ǽ
				if( $lngPrice > DEF_MONEY_MANAGER )
				{
					//$strDetailErrorMessage .= "���θ��¤Ǥ�".DEF_MONEY_MANAGER."�߰ʾ�Τ�Τ���Ͽ���뤳�Ȥ��Ǥ��ޤ���";
					$aryDetailErrorMessage[] = fncOutputError ( 704, "", "", FALSE, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
				}
			}
			else										// �ǥ��쥯����
			{
				// 20���ޤ���Ͽ��ǽ
				if( $lngPrice > DEF_MONEY_DIRECTOR )
				{
					// $strDetailErrorMessage .= "���θ��¤Ǥ�".DEF_MONEY_DIRECTOR."�߰ʾ�Τ�Τ���Ͽ���뤳�Ȥ��Ǥ��ޤ���";
					$aryDetailErrorMessage[] = fncOutputError ( 704, "", "", FALSE, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
				}
			}
*/
		}


		if( !is_array( $_POST["aryPoDitail"] ))
		{
			$aryDetailErrorMessage[] = fncOutputError ( 9001, "", "", FALSE, "", $objDB );
		}


		//-----------------------------------------------------------
		// ����No.��¸�ߤ�����
		//-----------------------------------------------------------
		if( strcmp( $_POST["strReceiveCode"], "") != 0 )
		{
			//---------------------------------------------
			// ���ζ�ۤȿ��̤�������ζ�ۤȿ��̤���äƤ��ʤ��������å�
			//---------------------------------------------
			$aryData["lngReceiveNo"] = $_POST["lngReceiveNo"];

			if( $_POST["strReceiveCode"] == "" )
			{
				//-----------------------------------------
				// DB -> SELECT : m_Receive
				//-----------------------------------------
				$strQuery = "SELECT strReceiveCode FROM m_Receive WHERE lngReceiveNo = " . $aryData["lngReceiveNo"];

				if ( isset( $lngResultID ) )
				{
					$objDB->freeResult( $lngResultID );
				}
				list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
				if ( $lngResultNum )
				{
					$objResult = $objDB->fetchObject( $lngResultID, 0 );
					$strReceiveCode = $objResult->strreceivecode;
				}
				else
				{
					// ȯ�����ɼ�������
					fncOutputError ( 9051, DEF_ERROR, "�������ɼ�������", TRUE, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
				}

				// ���ID�����
				$objDB->freeResult( $lngResultID );
			}
			else
			{
				$strReceiveCode = $_POST["strReceiveCode"];
			}

			//-------------------------------------------------------
			// DB -> SELECT : m_Receive
			//-------------------------------------------------------
			$strQuery = "SELECT lngMonetaryUnitCode FROM m_Receive WHERE lngReceiveNo = " . $aryData["lngReceiveNo"];

			if( isset( $lngResultID ) )
			{
				$objDB->freeResult( $lngResultID );
			}

			list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

			if( $lngResultNum )
			{
				$objResult = $objDB->fetchObject( $lngResultID, 0 );
				$lngReceiveMonetaryUnitCode = $objResult->lngmonetaryunitcode;
			}
			else
			{
				// �̲ߥ졼�ȼ�������
				fncOutputError ( 9051, DEF_ERROR, "�̲ߥ졼�ȼ�������", TRUE, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}

			// ���ID�����
			$objDB->freeResult( $lngResultID );


			//-------------------------------------------------------
			// DB -> SELECT : t_receivedetail
			//-------------------------------------------------------
			$aryQuery   = array();
			$aryQuery[] = "SELECT ";
			$aryQuery[] = "lngreceiveno, ";									// 1:�����ֹ�
			$aryQuery[] = "lngreceivedetailno as lngOrderDetailNo, ";		// 2:���������ֹ�
			$aryQuery[] = "lngrevisionno, ";								// 3:��ӥ�����ֹ�
			$aryQuery[] = "strproductcode, ";								// 4:���ʥ�����
			$aryQuery[] = "lngsalesclasscode, ";							// 5:����ʬ������
			$aryQuery[] = "dtmdeliverydate, ";								// 6:Ǽ����
			$aryQuery[] = "lngconversionclasscode, ";						// 7:������ʬ������ / 1��ñ�̷׾�/ 2���ٻ�ñ�̷׾�
			$aryQuery[] = "curproductprice, ";								// 8:���ʲ���
			$aryQuery[] = "lngproductquantity, ";							// 9:���ʿ���
			$aryQuery[] = "lngproductunitcode, ";							// 10:����ñ�̥�����
			$aryQuery[] = "lngtaxclasscode, ";								// 11:�����Ƕ�ʬ������
			$aryQuery[] = "lngtaxcode, ";									// 12:�����ǥ�����
			$aryQuery[] = "curtaxprice, ";									// 13:�����Ƕ��
			$aryQuery[] = "cursubtotalprice, ";								// 14:���׶��
			$aryQuery[] = "strnote as strdetailnote ";						// 15:���� 
			$aryQuery[] = "FROM t_receivedetail ";
			$aryQuery[] = "WHERE ";
			$aryQuery[] = "lngreceiveno = ".$aryData["lngReceiveNo"];
			$aryQuery[] = " ORDER BY lngSortKey ASC";

			$strQuery = "";
			$strQuery = implode("\n", $aryQuery );

			list( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

			if( $lngResultNum )
			{
				for ( $i = 0; $i < $lngResultNum; $i++ )
				{
					$aryReceiveDetail[] = $objDB->fetchArray( $lngResultID, $i );
				}
			}
			else
			{
				// ���ٹԤ�¸�ߤ��ʤ����۾�ǡ���
				fncOutputError ( 9051, DEF_ERROR, "���ٹԼ�������", TRUE, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}

			// ���ID�����
			$objDB->freeResult( $lngResultID );

			// �̲ߤ��ͤ򥳡��ɤ��ѹ�
			if( $_POST["lngMonetaryUnitCode"] != "" )
			{
				$_POST["lngMonetaryUnitCode"] = ( $_POST["lngMonetaryUnitCode"] == "\\" ) ? "\\\\" : $_POST["lngMonetaryUnitCode"];

				$lngSalesMonetaryUnitCode = fncGetMasterValue( "m_monetaryunit", "strmonetaryunitsign", "lngmonetaryunitcode", $_POST["lngMonetaryUnitCode"] . ":str", '', $objDB );
			}
			else
			{
				// ȯ�����ɼ�������
				fncOutputError ( 9061, DEF_ERROR, "�̲�ñ�̤μ�������", TRUE, "sc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
			}

			// ��������ü���������ڼΤ�
			$lngCalcCode = DEF_CALC_KIRISUTE;

			//---------------------------------------------
			// ����İʾ���������ꤷ�Ƥ��ʤ����ɤ����Υ����å�
			//---------------------------------------------
			$lngResult = fncGetStatusSalesRemains( $aryData["lngReceiveNo"], $_POST["aryPoDitail"], $lngReceiveMonetaryUnitCode, $lngSalesMonetaryUnitCode, "", $lngCalcCode, $objDB );

			switch( $lngResult )
			{
				// �������μ�������
				case 0:
					fncOutputError ( 9061, DEF_ERROR, "���ꤵ�줿����Ϻ������ޤ�����", TRUE, "sc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
					break;

				// �������١�������پ���μ�������
				case 1:
					fncOutputError ( 9061, DEF_ERROR, "����������μ����˼��Ԥ��ޤ�����", TRUE, "sc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
					break;

				// ����Ĥΰʾ�λ��������ꤵ��Ƥ���
				case 99:
					fncOutputError ( 604, DEF_ERROR, "", TRUE, "sc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
					break;

				// ���ꤵ��Ƥ����������ϻ�������
				case 50:
					break;
			}
		}

		// ���ٹԤ�¸�ߤ��ʤ����
		if( !is_array( $_POST["aryPoDitail"] ))
		{
			$aryDetailErrorMessage[] = fncOutputError ( 9001, "", "", FALSE, "", $objDB );
		}


		//�إå����ͤ��ü�ʸ���Ѵ�
		$aryData["strNote"] = fncHTMLSpecialChars( $aryData["strNote"] );


		//-----------------------------------------------------------
		// ���ϥ��顼
		//-----------------------------------------------------------
		if( is_array( $aryDetailErrorMessage ) || $errorCount != 0 )
		{
			$aryData = fncChangeData3( $aryData, $objDB );

			// �ץ�������˥塼������
			// �̲�
			$aryData["lngMonetaryUnitCode"] 		= fncPulldownMenu( 0, $aryData["lngMonetaryUnitCode"], '', $objDB );
			// �졼�ȥ�����
			$aryData["lngMonetaryRateCode"]			= fncPulldownMenu( 1, $aryData["lngMonetaryRateCode"], '', $objDB );
			// ��ʧ���
			$aryData["lngPayConditionCode"]			= fncPulldownMenu( 2, $aryData["lngPayConditionCode"], '', $objDB );
			// ����ñ��
			$aryData["lngProductUnitCode_gs"]		= fncPulldownMenu( 7, 0, '', $objDB );
			// �ٻ�ñ��
			$aryData["lngProductUnitCode_ps"]		= fncPulldownMenu( 8, 0, '', $objDB );
			// ����ʬ
			$aryData["lngSalesClassCode"]			= fncPulldownMenu( 10, $aryData["lngSalesClassCode"], '', $objDB );


			// ����ե�ɽ������ɽ������
			$aryData["visibleWF"] = "hidden";
			// ��ǧ�롼��
			// *v2 ����ե��ʤ�* $aryData["lngWorkflowOrderCode"] = fncWorkFlow( $lngUserCode , $objDB , $aryData["lngWorkflowOrderCode"] );


			// ���ٹԤ�hidden�ͤ��Ѵ�����
			if(is_array( $_POST["aryPoDitail"] ) ) 
			{
				$aryData["strDetailHidden"] = fncDetailHidden_sc( $_POST["aryPoDitail"] ,"insert", $objDB );
			}
			if( is_array( $aryDetailErrorMessage ) )
			{
				$aryData["strErrorMessage"] = implode(" : ", $aryDetailErrorMessage );
			}

//fncDebug( 'lib_sc.txt', $aryData["strDetailHidden"], __FILE__, __LINE__);

			$aryData["strGetDataMode"]          = "none";
			$aryData["strProcMode"]             = "check";
			$aryData["ReceiveSubmit"]           = 'fncOrderSubmit( document.HSO.strCustomerReceiveCode.value );';
			$aryData["ReceiveSubmit2"]          = 'fncOrderSubmit2( document.HSO.strProductCodeOpen.value );';
			$aryData["strCustomerReceiveDis"]   = '';
			$aryData["strProductCodeOpenDis"]   = '';
			//$aryData["strReceiveCode_Editable"] = "";
			$aryData["lngCalcCode"]             = DEF_CALC_KIRISUTE;
			$aryData["strPageCondition"]        = "regist";


			// CRC�ꥹ��
			$aryData["crcflag"] = '0';
			$aryData["crcview"] = 'hidden';


			if( is_array( $_POST["aryPoDitail"] ) )
			{
				$aryData["MonetaryUnitDisabled"] = " disabled";
				$aryData["lngRegistConfirm"]     = 0;
			}

			$aryData["lngSelfLoginUserCode"] = $lngUserCode; // ���ϼԥ�����


			// ľ��Ͽ�ե饰
			$aryData["lngDirectRegistFlag"] = $lngDirectRegistFlag;


			// ���¥��롼�ץ����ɤ��֥ޥ͡����㡼�װʲ��ξ��
			if( $lngUserGroup >= DEF_DIRECT_REGIST_AUTHORITY_CODE )
			{
				$aryData["lngAuthorityGroupFlag"] = 0;			// ���¥��롼�ץ����ɥե饰
				$aryData["blnContentEditable"]    = 'false';	// ���֥�������������
				$aryData["blnBtnEditable"]        = 'return;';	// �ܥ���������
			}
			else
			{
				$aryData["lngAuthorityGroupFlag"] = 1;			// ���¥��롼�ץ����ɥե饰
				$aryData["blnContentEditable"]    = 'true';		// ���֥�������������
				$aryData["blnBtnEditable"]        = '';			// �ܥ���������
			}



			$objDB->close();
			$objDB->freeResult( $lngResultID );


			echo fncGetReplacedHtml( "sc/regist/parts.tmpl", $aryData ,$objAuth);

			return true;
		}
		//-----------------------------------------------------------
		// ��ǧ����ɽ��
		//-----------------------------------------------------------
		if( $errorCount == 0 )
		{
			// ����ե�ɽ������ɽ������
			$aryData["visibleWF"] = "hidden";

			// �ץ�������˥塼������
			// �̲�
			$aryData["lngMonetaryUnitCode"]   = fncPulldownMenu( 0, $aryData["lngMonetaryUnitCode"], '', $objDB );
			// �졼�ȥ�����
			$aryData["lngMonetaryRateCode"]   = fncPulldownMenu( 1, $aryData["lngMonetaryRateCode"], '', $objDB );
			// ��ʧ���
			$aryData["lngPayConditionCode"]   = fncPulldownMenu( 2, $aryData["lngPayConditionCode"], '', $objDB );
			// ����ñ��
			$aryData["lngProductUnitCode_gs"] = fncPulldownMenu( 7, 0, '', $objDB );
			// �ٻ�ñ��
			$aryData["lngProductUnitCode_ps"] = fncPulldownMenu( 8, 0, '', $objDB );
			// ����ʬ
			$aryData["lngSalesClassCode"]     = fncPulldownMenu( 10, $aryData["lngSalesClassCode"], '', $objDB );
			// ��ǧ�롼��
			$aryData["lngWorkflowOrderCode"]  = fncWorkFlow( $lngUserCode , $objDB , $aryData["lngWorkflowOrderCode"] );

			// ���ٹԤ�hidden�ͤ��Ѵ�����
			$aryData["strDetailHidden"] = fncDetailHidden_sc( $_POST["aryPoDitail"] ,"insert" ,$objDB );

			$aryData["strBodyOnload"]           = "";
			$aryData["strGetDataMode"]          = "none";
			$aryData["strProcMode"]             = "check";

			$aryData["ReceiveSubmit"]           = 'fncOrderSubmit( document.HSO.strCustomerReceiveCode.value );';
			$aryData["ReceiveSubmit2"]          = 'fncOrderSubmit2( document.HSO.strProductCodeOpen.value );';
			$aryData["strCustomerReceiveDis"]   = '';
			$aryData["strProductCodeOpenDis"]   = '';

			$aryData["MonetaryUnitDisabled"]    = " disabled";
			//$aryData["strReceiveCode_Editable"] = "";
			$aryData["lngCalcCode"]             = DEF_CALC_KIRISUTE;
			$aryData["strPageCondition"]        = "regist";
			$aryData["lngRegistConfirm"]        = 1;


			// CRC�ꥹ��
			$aryData["crcflag"] = '0';
			$aryData["crcview"] = 'hidden';


			$aryData["lngSelfLoginUserCode"] = $lngUserCode; // ���ϼԥ�����


			// ľ��Ͽ�ե饰
			$aryData["lngDirectRegistFlag"] = $lngDirectRegistFlag;


			// ���¥��롼�ץ����ɤ��֥ޥ͡����㡼�װʲ��ξ��
			if( $lngUserGroup >= DEF_DIRECT_REGIST_AUTHORITY_CODE )
			{
				$aryData["lngAuthorityGroupFlag"] = 0;			// ���¥��롼�ץ����ɥե饰
				$aryData["blnContentEditable"]    = 'false';	// ���֥�������������
				$aryData["blnBtnEditable"]        = 'return;';	// �ܥ���������
			}
			else
			{
				$aryData["lngAuthorityGroupFlag"] = 1;			// ���¥��롼�ץ����ɥե饰
				$aryData["blnContentEditable"]    = 'true';		// ���֥�������������
				$aryData["blnBtnEditable"]        = '';			// �ܥ���������
			}


			$objDB->close();


			$aryData["strurl"]       = "/sc/confirm/index.php?strSessionID=".$aryData["strSessionID"];
			$aryData["strActionURL"] = "index.php";


			// �ƥ�ץ졼���ɤ߹���
			$objDB->freeResult( $lngResultID );
			echo fncGetReplacedHtml( "/sc/regist/parts.tmpl", $aryData, $objAuth );
			return true;
		}
	}





	//-------------------------------------------------------------------------
	// �� ����ǡ����ΰ�������
	//-------------------------------------------------------------------------
	if( $strProcMode == "onchange" )
	{
		//-----------------------------------------------------------
		// �ܵҼ����ֹ椫���������
		//-----------------------------------------------------------
		if( $strGetDataMode == "customer" )
		{
			$strCustomerReceiveCode = $_POST["strCustomerReceiveCode"];

			//---------------------------------------------
			// DB -> SELECT : m_Receive
			//---------------------------------------------
			$aryQuery   = array();
			$aryQuery[] = "SELECT distinct";
			$aryQuery[] = "	r.lngReceiveNo";
			$aryQuery[] = "	,r.lngRevisionNo";
			$aryQuery[] = "	,r.strCustomerReceiveCode";
			$aryQuery[] = "	,r.strReceiveCode";
			$aryQuery[] = "	,r.strReviseCode";
			$aryQuery[] = "	,r.lngCustomerCompanyCode as lngCustomerCode";
			$aryQuery[] = "	,r.lngReceiveStatusCode as lngSalesStatusCode";
			$aryQuery[] = "	,r.lngMonetaryUnitCode";
			$aryQuery[] = "	,r.lngMonetaryRateCode";
			$aryQuery[] = "	,r.curConversionRate";
			$aryQuery[] = "	,r.strNote";
			$aryQuery[] = "	,r.lngreceivestatuscode";
			$aryQuery[] = "FROM";
			$aryQuery[] = "	m_Receive r ";
			$aryQuery[] = "		LEFT JOIN t_receivedetail tr";
			$aryQuery[] = " 	ON r.lngreceiveno = tr.lngreceiveno";
			$aryQuery[] = "WHERE r.strCustomerReceiveCode = '". $strCustomerReceiveCode . "'";
			$aryQuery[] = " AND ( r.lngreceivestatuscode = 2 or r.lngreceivestatuscode = 3 )";
			$aryQuery[] = " AND ( r.strcustomerreceivecode is not null and r.strcustomerreceivecode != '' )";
			$aryQuery[] = "	AND r.bytInvalidFlag = FALSE";
			$aryQuery[] = "	AND r.lngRevisionNo >= 0";
			$aryQuery[] = "	AND r.lngRevisionNo = (";
			$aryQuery[] = "		SELECT MAX( r2.lngRevisionNo ) FROM m_Receive r2 WHERE r2.strReceiveCode = r.strReceiveCode";
			$aryQuery[] = "		AND r2.strReviseCode = ( ";
			$aryQuery[] = "		SELECT MAX( r3.strReviseCode ) FROM m_Receive r3 WHERE r3.strReceiveCode = r2.strReceiveCode )";
			$aryQuery[] = "	)";
			$aryQuery[] = "	AND 0 <= (";
			$aryQuery[] = "		SELECT MIN( r4.lngRevisionNo ) FROM m_Receive r4 WHERE r4.bytInvalidFlag = false AND r4.strReceiveCode = r.strReceiveCode";
			$aryQuery[] = "	)";
			$aryQuery[] = "ORDER BY r.lngReceiveNo";

			$strQuery = implode( "\n", $aryQuery );

//fncDebug('lib_scp.txt', $strQuery, __FILE__, __LINE__);
		}

		//-----------------------------------------------------------
		// ���ʥ����ɤ����������
		//-----------------------------------------------------------
		if( $strGetDataMode == "product" )
		{
			$strProductCodeOpen = $_POST["strProductCodeOpen"];

			// Ʊ��θܵҼ����ֹ��¸�ߥ����å� (�Ŀ������)
			$aryQuery   = array();
			$aryQuery[] = "SELECT DISTINCT";
			$aryQuery[] = "	mr.strcustomerreceivecode";
			$aryQuery[] = "FROM";
			$aryQuery[] = " m_receive mr";
			$aryQuery[] = "		LEFT JOIN t_receivedetail tr";
			$aryQuery[] = " 	ON tr.lngreceiveno = mr.lngreceiveno";
			$aryQuery[] = "WHERE";
			$aryQuery[] = " tr.strproductcode = '" . $strProductCodeOpen . "'";
			$aryQuery[] = " AND mr.bytinvalidflag = false";
			$aryQuery[] = " AND ( mr.lngreceivestatuscode = 2 or mr.lngreceivestatuscode = 3 )";
			$aryQuery[] = " AND ( mr.strcustomerreceivecode is not null and mr.strcustomerreceivecode != '' )";
			$aryQuery[] = " AND mr.lngrevisionno >= 0";
			$aryQuery[] = " AND mr.lngrevisionno = (";
			$aryQuery[] = "  SELECT";
			$aryQuery[] = "   max( mr2.lngrevisionno )";
			$aryQuery[] = "  FROM";
			$aryQuery[] = "   m_receive mr2";
			$aryQuery[] = "  WHERE";
			$aryQuery[] = "   mr2.strReceiveCode = mr.strReceiveCode";
			$aryQuery[] = "   AND mr2.strrevisecode = (";
			$aryQuery[] = "    SELECT";
			$aryQuery[] = "     max( mr3.strrevisecode )";
			$aryQuery[] = "    FROM";
			$aryQuery[] = "     m_receive mr3";
			$aryQuery[] = "    WHERE";
			$aryQuery[] = "     mr3.strReceiveCode = mr2.strReceiveCode";
			$aryQuery[] = "   )";
			$aryQuery[] = " )";
			$aryQuery[] = " AND 0 <= (";
			$aryQuery[] = "  SELECT";
			$aryQuery[] = "   min( mr4.lngrevisionno )";
			$aryQuery[] = "  FROM";
			$aryQuery[] = "   m_receive mr4";
			$aryQuery[] = "  WHERE";
			$aryQuery[] = "   mr4.bytinvalidflag = false";
			$aryQuery[] = "   AND mr4.strReceiveCode = mr.strReceiveCode";
			$aryQuery[] = " )";

			$strQuery = "";
			$strQuery = implode( "\n", $aryQuery );

			// ������¹�
			list( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
			
			if( $lngResultNum )
			{
			//	for( $i = 0; $i < $lngResultNum; $i++ )
			//	{
			//		$objResult[$i] = $objDB->fetchArray( $lngResultID, $i );
			//	}
				//$strCRC = $objResult->strcustomerreceivecode;

				// ������ȼ���
				$lngCount = (int)$lngResultNum;
			}
			else
			{
				// ������ȼ�������
				fncOutputError( 416, DEF_WARNING, "", TRUE, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}

//var_dump( $lngCount ); exit();

			//---------------------------------------------
			// �ܵҼ����ֹ椬���ĤΤߤξ��
			//---------------------------------------------
			if( $lngCount == 1 )
			{
				// �ܵҼ����ֹ�μ���
				$aryQuery   = array();
				$aryQuery[] = "SELECT DISTINCT";
				$aryQuery[] = " mr.strcustomerreceivecode";
				$aryQuery[] = "FROM";
				$aryQuery[] = " m_receive mr";
				$aryQuery[] = "		LEFT JOIN t_receivedetail tr";
				$aryQuery[] = " 	ON tr.lngreceiveno = mr.lngreceiveno";
				$aryQuery[] = "WHERE";
				$aryQuery[] = " tr.strproductcode = '" . $strProductCodeOpen . "'";
				$aryQuery[] = " AND mr.bytinvalidflag = false";
				$aryQuery[] = " AND ( mr.lngreceivestatuscode = 2 or mr.lngreceivestatuscode = 3 )";
				$aryQuery[] = " AND ( mr.strcustomerreceivecode is not null and mr.strcustomerreceivecode != '' )";
				$aryQuery[] = " AND mr.lngrevisionno >= 0";
				$aryQuery[] = " AND mr.lngrevisionno = (";
				$aryQuery[] = "  SELECT";
				$aryQuery[] = "   max( mr2.lngrevisionno )";
				$aryQuery[] = "  FROM";
				$aryQuery[] = "   m_receive mr2";
				$aryQuery[] = "  WHERE";
				$aryQuery[] = "   mr2.strReceiveCode = mr.strReceiveCode";
				$aryQuery[] = "   AND mr2.strrevisecode = (";
				$aryQuery[] = "    SELECT";
				$aryQuery[] = "     max( mr3.strrevisecode )";
				$aryQuery[] = "    FROM";
				$aryQuery[] = "     m_receive mr3";
				$aryQuery[] = "    WHERE";
				$aryQuery[] = "     mr3.strReceiveCode = mr2.strReceiveCode";
				$aryQuery[] = "   )";
				$aryQuery[] = " )";
				$aryQuery[] = " AND 0 <= (";
				$aryQuery[] = "  SELECT";
				$aryQuery[] = "   min( mr4.lngrevisionno )";
				$aryQuery[] = "  FROM";
				$aryQuery[] = "   m_receive mr4";
				$aryQuery[] = "  WHERE";
				$aryQuery[] = "   mr4.bytinvalidflag = false";
				$aryQuery[] = "   AND mr4.strReceiveCode = mr.strReceiveCode";
				$aryQuery[] = " )";

				$strQuery = "";
				$strQuery = implode( "\n", $aryQuery );
//	fncDebug('lib_scp.txt', $strQuery, __FILE__, __LINE__);

				// ������¹�
				list( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

				if( $lngResultNum )
				{
					$objResult = $objDB->fetchObject( $lngResultID, 0 );
					$strCRC = $objResult->strcustomerreceivecode;
				}
				else
				{
					// �ܵҼ����ֹ��������
					fncOutputError( 412, DEF_WARNING, "", TRUE, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
				}

				// �ܵҼ����ֹ����
				$strCustomerReceiveCode = $strCRC;



				//---------------------------------------------
				// DB -> SELECT : m_Receive
				//---------------------------------------------
				$aryQuery   = array();
				$aryQuery[] = "SELECT distinct";
				$aryQuery[] = "	r.lngReceiveNo";
				$aryQuery[] = "	,r.lngRevisionNo";
				$aryQuery[] = "	,r.strCustomerReceiveCode";
				$aryQuery[] = "	,r.strReceiveCode";
				$aryQuery[] = "	,r.strReviseCode";
				$aryQuery[] = "	,r.lngCustomerCompanyCode as lngCustomerCode";
				$aryQuery[] = "	,r.lngReceiveStatusCode as lngSalesStatusCode";
				$aryQuery[] = "	,r.lngMonetaryUnitCode";
				$aryQuery[] = "	,r.lngMonetaryRateCode";
				$aryQuery[] = "	,r.curConversionRate";
				$aryQuery[] = "	,r.strNote";
				$aryQuery[] = "FROM";
				$aryQuery[] = "	m_Receive r ";
				$aryQuery[] = "		LEFT JOIN t_receivedetail tr";
				$aryQuery[] = " 	ON r.lngreceiveno = tr.lngreceiveno";
				$aryQuery[] = "WHERE r.strCustomerReceiveCode = '". $strCustomerReceiveCode . "'";
				$aryQuery[] = " AND ( r.lngreceivestatuscode = 2 or r.lngreceivestatuscode = 3 )";
				$aryQuery[] = " AND ( r.strcustomerreceivecode is not null and r.strcustomerreceivecode != '' )";
				$aryQuery[] = "	AND r.bytInvalidFlag = FALSE";
				$aryQuery[] = "	AND r.lngRevisionNo >= 0";
				$aryQuery[] = "	AND r.lngRevisionNo = (";
				$aryQuery[] = "		SELECT MAX( r2.lngRevisionNo ) FROM m_Receive r2 WHERE r2.strReceiveCode = r.strReceiveCode";
				$aryQuery[] = "		AND r2.strReviseCode = ( ";
				$aryQuery[] = "		SELECT MAX( r3.strReviseCode ) FROM m_Receive r3 WHERE r3.strReceiveCode = r2.strReceiveCode )";
				$aryQuery[] = "	)";
				$aryQuery[] = "	AND 0 <= (";
				$aryQuery[] = "		SELECT MIN( r4.lngRevisionNo ) FROM m_Receive r4 WHERE r4.bytInvalidFlag = false AND r4.strReceiveCode = r.strReceiveCode";
				$aryQuery[] = "	)";
				$aryQuery[] = "ORDER BY r.lngReceiveNo";

/*
				$aryQuery   = array();
				$aryQuery[] = "SELECT ";
				$aryQuery[] = "r.lngReceiveNo, ";										// 1:�����ֹ�
				$aryQuery[] = "r.lngRevisionNo, ";										// 2:��ӥ�����ֹ�
				$aryQuery[] = "r.strCustomerReceiveCode, ";								// �ܵҼ����ֹ�
				$aryQuery[] = "r.strReceiveCode, ";										// 3:��������
				$aryQuery[] = "r.strReviseCode, ";										// 4:��Х���������
				$aryQuery[] = "r.lngCustomerCompanyCode as lngCustomerCode, ";			// 6:�ܵ�
				$aryQuery[] = "r.lngReceiveStatusCode as lngSalesStatusCode, ";			// 9:������֥�����
				$aryQuery[] = "r.lngMonetaryUnitCode, ";								// 10:�̲�ñ�̥�����
				$aryQuery[] = "r.lngMonetaryRateCode, ";								// 11:�̲ߥ졼�ȥ�����
				$aryQuery[] = "r.curConversionRate, ";									// 12:�����졼��
				$aryQuery[] = "r.strNote ";												// 14:����
				$aryQuery[] = "FROM m_Receive r ";
				$aryQuery[] = "WHERE r.strCustomerReceiveCode = '". $strCustomerReceiveCode . "' ";
				$aryQuery[] = "AND r.bytInvalidFlag = FALSE ";
				$aryQuery[] = "AND r.lngRevisionNo >= 0 ";
				$aryQuery[] = "AND r.lngRevisionNo = ( ";
				$aryQuery[] = "SELECT MAX( r2.lngRevisionNo ) FROM m_Receive r2 WHERE r2.strReceiveCode = r.strReceiveCode  ";
				$aryQuery[] = "AND r2.strReviseCode = ( ";
				$aryQuery[] = "SELECT MAX( r3.strReviseCode ) FROM m_Receive r3 WHERE r3.strReceiveCode = r2.strReceiveCode ) ) ";
				$aryQuery[] = "AND 0 <= ( ";
				$aryQuery[] = "SELECT MIN( r4.lngRevisionNo ) FROM m_Receive r4 WHERE r4.bytInvalidFlag = false AND r4.strReceiveCode = r.strReceiveCode ) ";
				$aryQuery[] = "ORDER BY r.lngReceiveNo";
*/

				$strQuery = "";
				$strQuery = implode( "\n", $aryQuery );
			}
			//---------------------------------------------
			// �ܵҼ����ֹ椬ʣ��¸�ߤ�����
			//---------------------------------------------
			else if( $lngCount > 1 )
			{
				// �ܵҼ����ֹ�μ���
				$aryQuery   = array();
				$aryQuery[] = "SELECT";
				$aryQuery[] = " mr.strcustomerreceivecode,";
				$aryQuery[] = " to_char( tr.dtmdeliverydate, 'YYYY/mm/dd' ) as dtmdeliverydate,";
				$aryQuery[] = " tr.lngreceivedetailno";
				$aryQuery[] = "FROM";
				$aryQuery[] = " m_receive mr";
				$aryQuery[] = "LEFT JOIN";
				$aryQuery[] = " t_receivedetail tr";
				$aryQuery[] = " ON mr.lngreceiveno = tr.lngreceiveno";
				$aryQuery[] = "WHERE";
				$aryQuery[] = "mr.lngreceiveno in ( select tr1.lngreceiveno from t_receivedetail tr1 where tr1.strproductcode = '" . $strProductCodeOpen . "' )";
				//$aryQuery[] = " tr.strproductcode = '" . $strProductCodeOpen . "'";
				$aryQuery[] = " AND mr.bytinvalidflag = false";
				$aryQuery[] = " AND ( mr.lngreceivestatuscode = 2 or mr.lngreceivestatuscode = 3 )";
				$aryQuery[] = " AND ( mr.strcustomerreceivecode is not null and mr.strcustomerreceivecode != '' )";
				$aryQuery[] = " AND mr.lngrevisionno >= 0";
				$aryQuery[] = " AND mr.lngrevisionno = (";
				$aryQuery[] = "  SELECT";
				$aryQuery[] = "   max( mr2.lngrevisionno )";
				$aryQuery[] = "  FROM";
				$aryQuery[] = "   m_receive mr2";
				$aryQuery[] = "  WHERE";
				$aryQuery[] = "   mr2.strReceiveCode = mr.strReceiveCode";
				$aryQuery[] = "   AND mr2.strrevisecode = (";
				$aryQuery[] = "    SELECT";
				$aryQuery[] = "     max( mr3.strrevisecode )";
				$aryQuery[] = "    FROM";
				$aryQuery[] = "     m_receive mr3";
				$aryQuery[] = "    WHERE";
				$aryQuery[] = "     mr3.strReceiveCode = mr2.strReceiveCode";
				$aryQuery[] = "   )";
				$aryQuery[] = " )";
				$aryQuery[] = " AND 0 <= (";
				$aryQuery[] = "  SELECT";
				$aryQuery[] = "   min( mr4.lngrevisionno )";
				$aryQuery[] = "  FROM";
				$aryQuery[] = "   m_receive mr4";
				$aryQuery[] = "  WHERE";
				$aryQuery[] = "   mr4.bytinvalidflag = false";
				$aryQuery[] = "   AND mr4.strReceiveCode = mr.strReceiveCode";
				$aryQuery[] = " )";
				$aryQuery[] = "ORDER BY";
				$aryQuery[] = " dtmdeliverydate";

				$strQuery = "";
				$strQuery = implode( "\n", $aryQuery );

				// ������¹�
				list( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

				// �ǡ����μ���
				for( $i = 0; $i < $lngResultNum; $i++ )
				{
					$aryCRCBase[$i] = $objDB->fetchArray( $lngResultID, $i );
				}


				// ���ȥǡ����Υ�����ȼ���
				$lngDBCnt = count( $aryCRCBase );

				// �ǡ�������ѥ����󥿤ν����
				$lngCreateCnt = 0;


				// �ǡ��������
				for( $i = 0; $i < $lngDBCnt; $i++ )
				{

					$aryCRC[$lngCreateCnt] = $aryCRCBase[$i];

					$blnReplaceFlag = false;
					for( $j = 0; $j < count( $aryCRC ); $j++ )
					{
						if( $aryCRC[$j]["strcustomerreceivecode"] == $aryCRCBase[$i]["strcustomerreceivecode"] )
						{
							// ��ǧ�оݤ����󤬺Ǹ�Ǥ�̵���ʰ�����������֤���������ˤ��֤������ե饰��True�ˤ���
							if( $j != $lngCreateCnt )
							{
								$blnReplaceFlag = true;
							}

							if( !is_array( $aryCRC[$j]["dtmdeliverydate"] ) )
							{
								$aryCRC[$j]["dtmdeliverydate"] = array();
							}
							else
							{
								$lngCreateCnt--;
							}

							$aryCRC[$j]["dtmdeliverydate"][] = $aryCRCBase[$i]["lngreceivedetailno"] . ". " . $aryCRCBase[$i]["dtmdeliverydate"];

							break;
						}
						
					}


					$lngCreateCnt++;
				}


				// ���פ����Ǥ������
				if( $blnReplaceFlag )
				{
					array_pop( $aryCRC );
				}

//fncDebug( 'index.txt', $aryCRCBase, __FILE__, __LINE__);


				// Ǽ����ʣ��¸�ߤ���ǡ�����ʸ������
				for( $i = 0; $i < count( $aryCRC ); $i++ )
				{
					if( is_array( $aryCRC[$i]["dtmdeliverydate"] ) )
					{
						$aryCRC[$i]["dtmdeliverydate"] = implode( "<br>", $aryCRC[$i]["dtmdeliverydate"] );
					}
				}

				// �ǥХå�
				//fncDebug( 'sc_debug.txt', $aryCRC, __FILE__, __LINE__);


				// ��ʣ���Ƥ���ܵҼ����ֹ��ɽ��
				$lngLangCode = $aryData["lngLanguageCode"];
				$aryCRCList  = array();

				$aryCRCList[] = '<table id="CRCTable" width="905" cellpadding="4" cellspacing="1" border="0">';

				if( $lngLangCode == 0 )
				{
					$aryCRCList[] = '<tr id="CRCHeader"><td id="excrctext" colspan="3" align="center">Two or more customer order numbers exist. Please select it from the following.</td></tr>';
					$aryCRCList[] = '<tr id="CRCColumn"><td>&nbsp;</td><td id="excrc" align="center">Customer order No.</td><td id="exdeli" align="center">Delivery date</td></tr>';
				}
				else
				{
					$aryCRCList[] = '<tr id="CRCHeader"><td id="excrctext" colspan="3" align="center">�ܵҼ����ֹ椬ʣ��¸�ߤ��Ƥ��ޤ�������������򤷤Ƥ���������</td></tr>';
					$aryCRCList[] = '<tr id="CRCColumn"><td>&nbsp;</td><td id="excrc" align="center">�ܵҼ����ֹ�</td><td id="exdeli" align="center">Ǽ��</td></tr>';
				}


				$crc = 1;

				for( $i = 0; $i < count( $aryCRC ); $i++ )
				{
					$aryCRCList[] = '<tr id="CRCData">';
					$aryCRCList[] = '<td align="right">' . $crc . '</td>';
					$aryCRCList[] = '<td><a href="#" onclick="fncOrderSubmit( \'' . $aryCRC[$i]["strcustomerreceivecode"] . '\' );">' . $aryCRC[$i]["strcustomerreceivecode"] . '</a></td>';
					$aryCRCList[] = '<td>' . $aryCRC[$i]["dtmdeliverydate"] . '</td>';
					$aryCRCList[] = '</tr>';

					$crc = $crc + 1;
				}

				$aryCRCList[] = '</table>';

				$strCRCList = "";
				$strCRCList = implode( "\n", $aryCRCList );





				// �ץ�������˥塼������
				// �̲�
				$aryData["lngMonetaryUnitCode"]   = fncPulldownMenu( 0, "\\", '', $objDB );
				// �졼�ȥ�����
				$aryData["lngMonetaryRateCode"]   = fncPulldownMenu( 1, 0, '', $objDB );
				// ��ʧ���
				$aryData["lngPayConditionCode"]   = fncPulldownMenu( 2, 0, '', $objDB );
				// ����ʬ
				$aryData["lngSalesClassCode"]     = fncPulldownMenu( 10, $aryData["lngSalesClassCode"], '', $objDB );
				// ����ñ��
				$aryData["lngProductUnitCode_gs"] = fncPulldownMenu( 7, 0, '', $objDB );
				// �ٻ�ñ��
				$aryData["lngProductUnitCode_ps"] = fncPulldownMenu( 8, 0, '', $objDB );



				// ����ե�ɽ������ɽ������
				$aryData["visibleWF"] = "hidden";
				// ��ǧ�롼��
				// *v2 ����ե��ʤ�* $aryData["lngWorkflowOrderCode"]  = fncWorkFlow( $lngUserCode , $objDB , "" );



				$aryData["strGetDataMode"]        = "none";
				$aryData["strProcMode"]           = "check";
				$aryData["curConversionRate"]     = "1.000000";
				$aryData["strSessionID"]          = $aryData[ "strSessionID" ];
				$aryData["ReceiveSubmit"]         = 'fncOrderSubmit( document.HSO.strCustomerReceiveCode.value );';
				$aryData["ReceiveSubmit2"]        = 'fncOrderSubmit2( document.HSO.strProductCodeOpen.value );';
				$aryData["strProductCodeOpen"]    = $strProductCodeOpen;
				$aryData["strCustomerReceiveDis"] = '';
				$aryData["strProductCodeOpenDis"] = '';

				$aryData["lngCalcCode"]           = DEF_CALC_KIRISUTE;
				$aryData["strPageCondition"]      = "regist";
				$aryData["dtmOrderAppDate"]       = $dtmNowDate;
				$aryData["lngRegistConfirm"]      = 0;


				// CRC�ꥹ��
				$aryData["crcflag"] = '1';
				$aryData["crcview"] = 'visible';
				$aryData["crclist"] = $strCRCList;


				// 610 �������ʹ��ɲá��Ժ����
				if( !fncCheckAuthority( DEF_FUNCTION_SC10, $objAuth ) )
				{
					$aryData["adddelrowview"] = 'hidden';
				}

				$aryData["lngSelfLoginUserCode"] = $lngUserCode; // ���ϼԥ�����


				// ľ��Ͽ�ե饰
				$aryData["lngDirectRegistFlag"] = 1;


				// ���¥��롼�ץ����ɤ��֥ޥ͡����㡼�װʲ��ξ��
				if( $lngUserGroup >= DEF_DIRECT_REGIST_AUTHORITY_CODE )
				{
					$aryData["lngAuthorityGroupFlag"] = 0;			// ���¥��롼�ץ����ɥե饰
					$aryData["blnContentEditable"]    = 'false';	// ���֥�������������
					$aryData["blnBtnEditable"]        = 'return;';	// �ܥ���������
				}
				else
				{
					$aryData["lngAuthorityGroupFlag"] = 1;			// ���¥��롼�ץ����ɥե饰
					$aryData["blnContentEditable"]    = 'true';		// ���֥�������������
					$aryData["blnBtnEditable"]        = '';			// �ܥ���������
				}


				$objDB->close();
				$objDB->freeResult( $lngResultID );

				echo fncGetReplacedHtml( "sc/regist/parts.tmpl", $aryData ,$objAuth );

				return true;
			}
			//---------------------------------------------
			// ����ǡ�����¸�ߤ��ʤ����
			//---------------------------------------------
			else
			{
				fncOutputError( 403, DEF_WARNING, "(".$strCustomerReceiveCode.")", TRUE, "sc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
			}
		}



		//---------------------------------------------------------------------
		// �ܵҼ����ֹ椫��������� �� �ǡ�����¸�ߵڤӡ�������֤Υ����å�
		//---------------------------------------------------------------------
		// ���ID�����
		if( isset( $lngResultID ) ) $objDB->freeResult( $lngResultID );

		// ������¹�
		list( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

		$aryReceive = array();

		// ���ꤵ�줿����¸�ߤ�����
		if( $lngResultNum )
		{
			for( $i = 0; $i < $lngResultNum; $i++ )
			{
				// �ǡ��������
				$aryReceive[$i] = $objDB->fetchArray( $lngResultID, $i );
			}
		}
		// ���ꤵ�줿������֤��ֿ����桦Ǽ�ʺѤߡפ����ޤ��ϥǡ�����¸�ߤ��ʤ����
		else
		{
			//---------------------------------------------
			// DB -> SELECT : m_Receive
			//---------------------------------------------
			$aryQuery   = array();
			$aryQuery[] = "SELECT distinct";
			$aryQuery[] = "	r.lngReceiveNo";
			$aryQuery[] = "	,r.lngRevisionNo";
			$aryQuery[] = "	,r.strCustomerReceiveCode";
			$aryQuery[] = "	,r.strReceiveCode";
			$aryQuery[] = "	,r.strReviseCode";
			$aryQuery[] = "	,r.lngCustomerCompanyCode as lngCustomerCode";
			$aryQuery[] = "	,r.lngReceiveStatusCode as lngSalesStatusCode";
			$aryQuery[] = "	,r.lngMonetaryUnitCode";
			$aryQuery[] = "	,r.lngMonetaryRateCode";
			$aryQuery[] = "	,r.curConversionRate";
			$aryQuery[] = "	,r.strNote";
			$aryQuery[] = "	,r.lngreceivestatuscode";
			$aryQuery[] = "FROM";
			$aryQuery[] = "	m_Receive r ";
			$aryQuery[] = "		LEFT JOIN t_receivedetail tr";
			$aryQuery[] = " 	ON r.lngreceiveno = tr.lngreceiveno";
			$aryQuery[] = "WHERE r.strCustomerReceiveCode = '". $strCustomerReceiveCode . "'";
			//$aryQuery[] = " AND ( r.lngreceivestatuscode = 2 or r.lngreceivestatuscode = 3 )";
			$aryQuery[] = " AND ( r.strcustomerreceivecode is not null and r.strcustomerreceivecode != '' )";
			$aryQuery[] = "	AND r.bytInvalidFlag = FALSE";
			$aryQuery[] = "	AND r.lngRevisionNo >= 0";
			$aryQuery[] = "	AND r.lngRevisionNo = (";
			$aryQuery[] = "		SELECT MAX( r2.lngRevisionNo ) FROM m_Receive r2 WHERE r2.strReceiveCode = r.strReceiveCode";
			$aryQuery[] = "		AND r2.strReviseCode = ( ";
			$aryQuery[] = "		SELECT MAX( r3.strReviseCode ) FROM m_Receive r3 WHERE r3.strReceiveCode = r2.strReceiveCode )";
			$aryQuery[] = "	)";
			$aryQuery[] = "	AND 0 <= (";
			$aryQuery[] = "		SELECT MIN( r4.lngRevisionNo ) FROM m_Receive r4 WHERE r4.bytInvalidFlag = false AND r4.strReceiveCode = r.strReceiveCode";
			$aryQuery[] = "	)";
			$aryQuery[] = "ORDER BY r.lngReceiveNo";

			$strQuery = implode( "\n", $aryQuery );

			// ������¹�
			list( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

			// ���ꤵ�줿����¸�ߤ�����
			if( $lngResultNum )
			{
				// �����桦Ǽ�ʺ�
				fncOutputError( 417, DEF_WARNING, "(".$strCustomerReceiveCode.")", TRUE, "sc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
			}
			// �����������ǡ�����¸�ߤ��ʤ����
			else
			{
				fncOutputError( 403, DEF_WARNING, "(".$strCustomerReceiveCode.")", TRUE, "sc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
			}
		}

		// ���ID�����
		$objDB->freeResult( $lngResultID );


		// �ǥХå�
//fncDebug( 'lib_scp.txt', $aryReceive, __FILE__, __LINE__);


		//-----------------------------------------------------------
		// ������֤Υ����å����ü�ʸ���Ѵ�
		//-----------------------------------------------------------
		for( $i = 0; $i < count($aryReceive); $i++ )
		{
			// ������ξ��
			if( $aryReceive[$i]["lngsalesstatuscode"] == DEF_RECEIVE_PREORDER )
			{
				fncOutputError( 410, DEF_WARNING, "", TRUE, "sc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
			}

			// ������ξ��
			if( $aryReceive[$i]["lngsalesstatuscode"] == DEF_RECEIVE_APPLICATE )
			{
				fncOutputError( 406, DEF_WARNING, "", TRUE, "sc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
			}

			// Ǽ�ʺѤξ��
			if( $aryReceive[$i]["lngsalesstatuscode"] == DEF_RECEIVE_END )
			{
				//fncOutputError( 415, DEF_WARNING, "", TRUE, "sc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
			}

			// ��ǧ���������ä��ξ��
			if( $aryReceive[$i]["lngsalesstatuscode"] == "" || $aryReceive[$i]["lngsalesstatuscode"] == "null" )
			{
				fncOutputError( 414, DEF_WARNING, "", TRUE, "sc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
			}

			// ��������ü�ʸ���Ѵ�
			$aryReceive[$i]["strnote"] =  fncHTMLSpecialChars( $aryReceive[$i]["strnote"] );
		}



		//-----------------------------------------------------------
		// DB -> SELECT : t_receivedetail
		//-----------------------------------------------------------
		$aryReceiveDetail = array();
		// ���ٹԤμ���
		for( $i = 0; $i < count($aryReceive); $i++ )
		{
			$aryQuery   = array();
			$aryQuery[] = "SELECT ";
			$aryQuery[] = "	lngreceiveno";
			$aryQuery[] = "	,lngreceivedetailno as lngorderdetailno";
			$aryQuery[] = "	,lngreceivedetailno as lngreceivedetailno";
			$aryQuery[] = "	,lngrevisionno";
			$aryQuery[] = "	,strproductcode";
			$aryQuery[] = "	,lngsalesclasscode";
			$aryQuery[] = "	,To_char( dtmdeliverydate,'YYYY/mm/dd') as dtmdeliverydate";
			$aryQuery[] = "	,lngconversionclasscode";
			$aryQuery[] = "	,curproductprice";
			$aryQuery[] = "	,lngproductquantity";
			$aryQuery[] = "	,lngproductunitcode";
			$aryQuery[] = "	,lngtaxclasscode";
			$aryQuery[] = "	,lngtaxcode";
			$aryQuery[] = "	,curtaxprice";
			$aryQuery[] = "	,cursubtotalprice";
			$aryQuery[] = "	,strnote as strdetailnote";
			$aryQuery[] = "FROM";
			$aryQuery[] = "	t_receivedetail";
			$aryQuery[] = "WHERE";
			$aryQuery[] = "	lngreceiveno = " . $aryReceive[$i]["lngreceiveno"];
			$aryQuery[] = "ORDER BY lngSortKey ASC";

			$strQuery = "";
			$strQuery = implode( "\n", $aryQuery );

//fncDebug( 'lib_scp.txt', $strQuery, __FILE__, __LINE__);
			// ������¹�
			list( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

			if( $lngResultNum )
			{
				$aryBuff = array();
				for( $lngTR = 0; $lngTR < $lngResultNum; $lngTR++ )
				{
					// �ǡ��������
					$aryBuff[$lngTR] = $objDB->fetchArray( $lngResultID, $lngTR );
				
					// Ǽ�������ܤ�ɽ���������ѹ�����
					$aryBuff[$lngTR]["dtmdeliverydate"] = str_replace( "-", "/", $aryBuff[$lngTR]["dtmdeliverydate"] );

					// ��������ֹ�Υ��󥯥����
					$aryBuff[$lngTR]["lngorderdetailno"] = $lngTR + 1;
				}
				
				$aryReceiveDetail[$i] = $aryBuff;
			}
			else
			{
				fncOutputError( 403, DEF_WARNING, "(".$strCustomerReceiveCode.")", TRUE, "sc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
			}

			// ���ID�����
			$objDB->freeResult( $lngResultID );
		}


		// ����Ĥ����ݤ�ü�������λ�����ˡ���������
		//  ------> ����ʳ��Ǥ� �������==>�ڼΤƽ����Ȥ���
		$lngCalcMode = DEF_CALC_KIRISUTE;

		// �׾��������ꤵ��Ƥ�����ϡ����Ϥ��줿�׾���������Ѥ�
		if( $_POST["dtmOrderAppDate"] != "" )
		{
			$dtmOrderAppDate = $_POST["dtmOrderAppDate"];
		}
		else
		// �׾����Ϻ��������դ�Ŭ�Ѥ���
		{
			$dtmOrderAppDate = $dtmNowDate;
		}



		//-----------------------------------------------------------
		// ���������Ŀ������
		//-----------------------------------------------------------
		$aryRemainsDetail = array();
		for( $i = 0; $i < count($aryReceive); $i++ )
		{
			$aryRemainsDetail[$i] = fncGetSalesRemains( $aryReceive[$i]["lngreceiveno"], "", $lngCalcMode, $objDB );

		}

//fncDebug( 'lib_scp.txt', $aryRemainsDetail, __FILE__, __LINE__);

		// �����ѥ�����Ȥν����
		$lngPlusCnt = 0;
		for( $i = 0; $i < count( $aryRemainsDetail ); $i++ )
		{
			if( $aryRemainsDetail[$i] == 1 )
			{
				fncOutputError( 9051, DEF_ERROR, "", TRUE, "sc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
			}
			else
			{
				//---------------------------------------------
				// ����Ŀ��η׾�ñ������������ñ�̤ˤ��碌��
				//---------------------------------------------
//fncDebug( 'lib_scp.txt', $aryRemainsDetail[$i], __FILE__, __LINE__);
				$aryRemainsDetail_New = fncSetConversionSalesRemains( $aryRemainsDetail[$i], $aryReceiveDetail[$i], $aryReceive[$i]["lngmonetaryunitcode"], $lngCalcMode, $dtmOrderAppDate, $objDB );
//fncDebug( 'lib_scp.txt', $aryRemainsDetail_New, __FILE__, __LINE__);

				if( $aryRemainsDetail_New == 1 )
				{
					fncOutputError( 9051, DEF_ERROR, "", TRUE, "sc/regist/index.php?strSessionID=" . $_POST["strSessionID"], $objDB );
				}

				// ���ٹԤ�hidden�ͤ��Ѵ�����
				$strBuff .= "\n" . fncDetailHidden_sc( $aryRemainsDetail_New, "detail", $objDB, $lngPlusCnt );

//fncDebug( 'lib_scp.txt', $lngPlusCnt, __FILE__, __LINE__);
			}
		}

		//-----------------------------------------------------------
		// �ǡ����ΰܹ�
		//-----------------------------------------------------------
		$aryData = fncChangeData2( $aryReceive[0] , $objDB );
		$aryData["strDetailHidden"] = $strBuff;



		// �׾�����aryData������
		$aryData["dtmOrderAppDate"] = $dtmOrderAppDate;

		// �졼�ȥ�����
		// �̲ߤ����ܰʳ��ʤ�졼�ȥ����פ��TTM�פˤ���
		if( $aryData["lngmonetaryunitcode"] != 1 )
		{
			$aryData["lngmonetaryratecode"] = fncPulldownMenu( 1, 2, '', $objDB );
//			$aryData["lngmonetaryratecode"] = fncPulldownMenu( 1, 1, '', $objDB );
		}
		else
		{
			$aryData["lngmonetaryratecode"] = fncPulldownMenu( 1, $aryData["lngmonetaryratecode"], '', $objDB );
		}

		// �ץ�������˥塼������
		// �̲�
		$lngMonetaryUnit                  = fncGetMasterValue( "m_monetaryunit", "lngmonetaryunitcode", "strmonetaryunitsign", $aryData["lngmonetaryunitcode"],'', $objDB );
		$aryData["lngmonetaryunitcode"]   = fncPulldownMenu( 0, $lngMonetaryUnit, '', $objDB );
		// ����
		$strSalesStatus = fncGetMasterValue( "m_salesstatus", "lngsalesstatuscode", "strsalesstatusname", $aryData["lngsalesstatuscode"],'', $objDB );
		$aryData["strsalsestatus_dis"]    = $strSalesStatus;
		// ����ñ��
		$aryData["lngProductUnitCode_gs"] = fncPulldownMenu( 7, 0, '', $objDB );
		// �ٻ�ñ��
		$aryData["lngProductUnitCode_ps"] = fncPulldownMenu( 8, 0, '', $objDB );
		// ����ʬ
		$aryData["lngSalesClassCode"]     = fncPulldownMenu( 10, $aryData["lngSalesClassCode"], '', $objDB );



		//---------------------------------------------------------------
		// �����֤μ���
		//---------------------------------------------------------------
		if( strcmp( $aryData["lngsalesstatuscode"], "" ) != 0 )
		{
			$strSalseStatus = fncGetMasterValue( "m_salesstatus", "lngsalesstatuscode", "strsalesstatusname", $aryData["lngsalesstatuscode"],'', $objDB );
			$aryData["lngSalseStatusCode_Display"] = $strSalseStatus;
			$aryData["lngSalseStatusCode"]         = $aryData["lngsalesstatuscode"];
		}



		// ��ǧ�롼�Ȥμ���
		$lngWorkflowOrderCode = fncGetMasterValue( "m_workflow", "strworkflowkeycode", "lngworkflowordercode", $aryData["lngreceiveno"].":str", '', $objDB );
		// ��ǧ�롼��
		// *v2 ����ե��ʤ�* $aryData["lngWorkflowOrderCode"] = fncWorkFlow( $lngUserCode , $objDB , $lngWorkflowOrderCode );


		// ����ե�ɽ������ɽ������
		$aryData["visibleWF"] = "hidden";


		// �׾��������ꤵ��Ƥ�����ϡ����Ϥ��줿�׾���������Ѥ�
		if( $_POST["dtmOrderAppDate"] != "" )
		{
			$aryData["dtmOrderAppDate"] = $_POST["dtmOrderAppDate"];
		}
		else
		// �׾����Ϻ��������դ�Ŭ�Ѥ���
		{
			$aryData["dtmOrderAppDate"] = $dtmNowDate;
		}


		// ���ʥ����ɤ������
		// ���ʥ����ɤ���������Ƥξ��
		if( $strGetDataMode == "product" )
		{
			$aryData["strProductCodeOpen"] = $strProductCodeOpen;
		}
		// �ܵҼ����ֹ椫��������Ƥξ��
		else
		{
			$aryData["strProductCodeOpen"] = $aryReceiveDetail[0]["strproductcode"];
		}


		$aryData["strGetDataMode"]          = "none";
		$aryData["strProcMode"]             = "check";
		$aryData["ReceiveSubmit"]           = 'fncOrderSubmit( document.HSO.strCustomerReceiveCode.value );';
		$aryData["ReceiveSubmit2"]          = 'fncOrderSubmit2( document.HSO.strProductCodeOpen.value );';
		$aryData["strCustomerReceiveDis"]   = '';
		$aryData["strProductCodeOpenDis"]   = '';
		$aryData["MonetaryUnitDisabled"]    = " disabled";
		$aryData["strSessionID"]            = $_POST["strSessionID"];
		$aryData["lngRegistConfirm"]        = 0;
		$aryData["lngCalcCode"]             = DEF_CALC_KIRISUTE;
		$aryData["strPageCondition"]        = "regist";


		$aryData["crcflag"] = '0';
		$aryData["crcview"] = 'hidden';


		// 610 �������ʹ��ɲá��Ժ����
		if( !fncCheckAuthority( DEF_FUNCTION_SC10, $objAuth ) )
		{
			$aryData["adddelrowview"] = 'hidden';
		}


		$aryData["lngSelfLoginUserCode"] = $lngUserCode; // ���ϼԥ�����


		// ľ��Ͽ�ե饰
		$aryData["lngDirectRegistFlag"] = 0;


		// ���¥��롼�ץ����ɤ��֥ޥ͡����㡼�װʲ��ξ��
		if( $lngUserGroup >= DEF_DIRECT_REGIST_AUTHORITY_CODE )
		{
			$aryData["lngAuthorityGroupFlag"] = 0;			// ���¥��롼�ץ����ɥե饰
			$aryData["blnContentEditable"]    = 'false';	// ���֥�������������
			$aryData["blnBtnEditable"]        = 'return;';	// �ܥ���������
		}
		else
		{
			$aryData["lngAuthorityGroupFlag"] = 1;			// ���¥��롼�ץ����ɥե饰
			$aryData["blnContentEditable"]    = 'true';		// ���֥�������������
			$aryData["blnBtnEditable"]        = '';			// �ܥ���������
		}


		$objDB->close();
		$objDB->freeResult( $lngResultID );

		//require( LIB_DEBUGFILE );
		//fncDebug( 'lib_sc.txt', $aryData, __FILE__, __LINE__);



		echo fncGetReplacedHtml( "sc/regist/parts.tmpl", $aryData ,$objAuth );

		return true;
	}






	//-------------------------------------------------------------------------
	// �� ���ɽ�� -> ���������
	//-------------------------------------------------------------------------
	// �ץ�������˥塼������
	// �̲�
	$aryData["lngMonetaryUnitCode"]   = fncPulldownMenu( 0, "\\", '', $objDB );
	// �졼�ȥ�����
	$aryData["lngMonetaryRateCode"]   = fncPulldownMenu( 1, 0, '', $objDB );
	// ��ʧ���
	$aryData["lngPayConditionCode"]   = fncPulldownMenu( 2, 0, '', $objDB );
	// ����ʬ
	$aryData["lngSalesClassCode"]     = fncPulldownMenu( 10, $aryData["lngSalesClassCode"], '', $objDB );
	// ����ñ��
	$aryData["lngProductUnitCode_gs"] = fncPulldownMenu( 7, 0, '', $objDB );
	// �ٻ�ñ��
	$aryData["lngProductUnitCode_ps"] = fncPulldownMenu( 8, 0, '', $objDB );



	// ����ե�ɽ������ɽ������
	$aryData["visibleWF"] = "hidden";
	// ��ǧ�롼��
	// *v2 ����ե��ʤ�* $aryData["lngWorkflowOrderCode"]  = fncWorkFlow( $lngUserCode , $objDB , "" );



	$aryData["strGetDataMode"]              = "none";
	$aryData["strProcMode"]                 = "check";
	$aryData["curConversionRate"]           = "1.000000";
	$aryData["strSessionID"]                = $aryData[ "strSessionID" ];
	$aryData["ReceiveSubmit"]               = 'fncOrderSubmit( document.HSO.strCustomerReceiveCode.value );';
	$aryData["ReceiveSubmit2"]              = 'fncOrderSubmit2( document.HSO.strProductCodeOpen.value );';
	$aryData["strCustomerReceiveDis"]       = '';
	$aryData["strProductCodeOpenDis"]       = '';
	//$aryData["strReceiveCode_Editable"]     = '';

	$aryData["lngCalcCode"]                 = DEF_CALC_KIRISUTE;
	$aryData["strPageCondition"]            = "regist";
	$aryData["dtmOrderAppDate"]             = $dtmNowDate;
	$aryData["lngRegistConfirm"]            = 0;


	// CRC�ꥹ��
	$aryData["crcflag"] = '0';
	$aryData["crcview"] = 'hidden';


	$aryData["lngSelfLoginUserCode"] = $lngUserCode; // ���ϼԥ�����


	// ľ��Ͽ�ե饰
	$aryData["lngDirectRegistFlag"] = 1;



	// ���¥��롼�ץ����ɤ��֥ޥ͡����㡼�װʲ��ξ��
	if( $lngUserGroup >= DEF_DIRECT_REGIST_AUTHORITY_CODE )
	{
		$aryData["lngAuthorityGroupFlag"] = 0;			// ���¥��롼�ץ����ɥե饰
		$aryData["blnContentEditable"]    = 'false';	// ���֥�������������
		$aryData["blnBtnEditable"]        = 'return;';	// �ܥ���������
	}
	else
	{
		$aryData["lngAuthorityGroupFlag"] = 1;			// ���¥��롼�ץ����ɥե饰
		$aryData["blnContentEditable"]    = 'true';		// ���֥�������������
		$aryData["blnBtnEditable"]        = '';			// �ܥ���������
	}



	$objDB->close();
	$objDB->freeResult( $lngResultID );


	echo fncGetReplacedHtml( "sc/regist/parts.tmpl", $aryData ,$objAuth);

	return true;

?>
