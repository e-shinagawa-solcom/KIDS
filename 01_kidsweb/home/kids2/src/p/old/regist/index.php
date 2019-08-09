<?php

// ----------------------------------------------------------------------------
/**
*       ���ʴ���  ��Ͽ����
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
	require( SRC_ROOT."p/cmn/lib_p3.php" );
	require( SRC_ROOT . "po/cmn/lib_po.php" );
	require( "libsql.php" );


	//-------------------------------------------------------------------------
	// �� ���֥�����������
	//-------------------------------------------------------------------------
	$objDB   = new clsDB();
	$objAuth = new clsAuth();


	//-------------------------------------------------------------------------
	// �� DB�����ץ�
	//-------------------------------------------------------------------------
	$objDB->open("", "", "", "");


	//-------------------------------------------------------------------------
	// �� �ѥ�᡼������
	//-------------------------------------------------------------------------
	$aryData = $_POST;

	$aryData["strSessionID"]    = $_REQUEST["strSessionID"];   // ���å����ID
	$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"]; // ���쥳����


//var_dump($aryData);exit;

	//-------------------------------------------------------------------------
	// �� ����ʸ�����͡����å���󡦸��¥����å�
	//-------------------------------------------------------------------------
	// ʸ��������å�(session)
	$aryCheck["strSessionID"] = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );

	// ���å�����ǧ
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	$lngUserCode = $objAuth->UserCode;


	// 300 ���ʴ���
	if ( !fncCheckAuthority( DEF_FUNCTION_P0, $objAuth ) )
	{
		fncOutputError( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}

	// 301 ���ʴ����ʾ�����Ͽ��
	if ( !fncCheckAuthority( DEF_FUNCTION_P1, $objAuth ) )
	{
		fncOutputError( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}


	//-------------------------------------------------------------------------
	// �� ���ϥ����å�
	//-------------------------------------------------------------------------
	if( strcmp ( $aryData["strProcess"], "" ) != 0)
	{

		if($aryData["strProcess"] == "check" )
		{

			$aryCheck = array();

			$aryCheck["strProductName"]				= "null:length(1,100)";			// 3:����̾��
			$aryCheck["strProductEnglishName"]		= "null:length(1,100)"; 			// 4:����̾��(�Ѹ�)
			$aryCheck["lngInChargeGroupCode"]		= "null:length(1,2)";				// 5:����
			$aryCheck["lngInChargeUserCode"]		= "null:length(1,3)";				// 6:ô����
			$aryCheck["strGoodsCode"]				= "length(1,10)";			// 9:���ʥ����ɡ�null��Ͽ��ǽ��
/*ɬ�ܳ���		$aryCheck["lngCategoryCode"]			= "number(1,999999999):length(1,10)";		// xxxx:���ƥ��꡼������// added by k.saito*/
			$aryCheck["lngCategoryCode"]			= "length(1,10)";
			$aryCheck["strGoodsName"]				= "length(1,80)";			// 10:����̾��
			$aryCheck["lngCustomerCode"]			= "number(0,999999999)";			// 11:�ܵ�
			//$aryCheck["lngCustomerUserCode"]		= "";						// 13:�ܵ�ô���ԥ����� 
			if( strcmp($aryCheck["lngCustomerUserCode"], "") == 0 )
			{
				$aryCheck["strCustomerUserName"]	= "length(1,50)";				// 14:�ܵ�ô����50byte
			}
			$aryCheck["lngPackingUnitCode"]			= "money(1,99)";				// 15:�ٻ�ñ��(int2)
			$aryCheck["lngProductUnitCode"]			= "money(1,99)";				// 16:����ñ��(int2)
			$aryCheck["lngBoxQuantity"]				= "number(0,2147483647)";		// 17:��Ȣ���ޡ�����10byte"   :length(1,10)
			$aryCheck["lngCartonQuantity"]			= "null:number(1,2147483647)";			// 18:�����ȥ�����10byte   :length(1,10)
			$aryCheck["lngProductionQuantity"]		= "null:number(1,2147483647)";			// 19:����ͽ���10byte     :length(1,10)
			$aryCheck["lngProductionUnitCode"]		= "number(1,99):length(1,4)";			// 20:����ͽ�����ñ��4byte
			$aryCheck["lngFirstDeliveryQuantity"]	= "null:number(1,2147483647)";				// 21:���Ǽ�ʿ�10byte   :length(1,10)
			$aryCheck["lngFirstDeliveryUnitCode"]	= "number(1,99):length(0,4)";				// 22:���Ǽ�ʿ���ñ��4byte
			$aryCheck["lngFactoryCode"]				= "length(1,4)";			// 23:��������4byte
			$aryCheck["lngAssemblyFactoryCode"]		= "length(1,4)";	 			// 24:���å���֥깩��4byte
			$aryCheck["lngDeliveryPlaceCode"]		= "length(1,4)";				// 25:Ǽ�ʾ��4byte
			$aryCheck["dtmDeliveryLimitDate"]		= "null:date"; 					// 26:Ǽ�ʴ�����:date
			$aryCheck["curProductPrice"]			= "null:money(0.0001,99999999999999.9999)";		// 27:����
			$aryCheck["curRetailPrice"]				= "null:money(0.0000,99999999999999.9999)";	// 28:����     :length(1,8)
// 2004.06.17 suzukaze update start
//			$aryCheck["lngRoyalty"]					= "null:number(0,999999)";			// 30:�����ƥ��� 
/*ɬ�ܳ���		$aryCheck["lngRoyalty"]					= "null:number(0,100)";				// 30:�����ƥ���*/
 			$aryCheck["lngRoyalty"]					= "number(0,100)";
/*			if(strcmp($aryCheck["lngRoyalty"], "") == 0)
			{
				$aryCheck["lngRoyalty"]					= "0.00";
			}
*/
// 2004.06.17 suzukaze update start
			$aryCheck["lngCertificateClassCode"]	= "null";		 					// 31:�ڻ�

/*ɬ�ܳ���		if( $aryData["lngCopyrightCode"] == 0 && strcmp( $aryData["strCopyrightNote"], "") == 0 )
			{
				$aryCheck["lngCopyrightCode"]		= "number(1,99)"; 					// 32:�Ǹ���
				$aryCheck["strCopyrightNote"]		= "null";						// :�Ǹ�������
			}
*/
			$aryCheck["lngCopyrightCode"]		= "length(1,50)"; 					// 32:�Ǹ���
			$aryCheck["strCopyrightNote"]		= "length(1,200)";						// :�Ǹ�������
			$aryCheck["strCopyrightDisplayPrint"]	= "length(1,100)"; 						// 34:�Ǹ�ɽ��(����ʪ)100
			$aryCheck["lngProductFormCode"]			= "number(1,99,The list has not been selected.):length(1,100)"; 	// 35:���ʷ���100

			if($_COOKIE["lngLanguageCode"])
			{
				$aryCheck["lngProductFormCode"]			= "number(1,99,�ꥹ�Ȥ����򤵤�Ƥ��ޤ���):length(1,100)"; 	// 35:���ʷ���100
			}
			$aryCheck["strProductComposition"]		= "null:number(0,99)";  						// 36:���ʹ���100byte
			$aryCheck["strAssemblyContents"]		= "length(1,100)";  							// 37:���å���֥�����100byte
			$aryCheck["strSpecificationDetails"]	= "length(1,10000)";  							// 38:���;ܺ�300


			// ���顼�ؿ��θƤӽФ� 
			$aryCheckResult = fncAllCheck( $aryData, $aryCheck );

			list ( $aryData, $bytErrorFlag ) = getArrayErrorVisibility( $aryData, $aryCheckResult, $objDB );


			$errorCount = ($bytErrorFlag == "TRUE" ) ? 1 : 0;


			// ���顼��̵���ä��顦��
			if( $errorCount == 0 )
			{
				// ����̾�ʱѸ�ˤ������������ ================================================================================

				$lngInchargeGroupCode = fncGetMasterValue("m_group", "strgroupdisplaycode", "lnggroupcode", $aryData["lngInChargeGroupCode"] . ":str",'',$objDB);

				$aryQueryName[] = "SELECT " ;
				$aryQueryName[] = "strproductcode, ";
				$aryQueryName[] = "strproductenglishname ";
				$aryQueryName[] = "FROM ";
				$aryQueryName[] = "m_product ";
				$aryQueryName[] = "WHERE "; 
				$aryQueryName[] = "strproductenglishname = '".$aryData["strProductEnglishName"]."' AND ";
				$aryQueryName[] = "lnginchargegroupcode = $lngInchargeGroupCode AND ";
				$aryQueryName[] = "bytinvalidflag = false ";
				$aryQueryName[] = "ORDER BY strproductcode";


				$strQueryName = implode("\n", $aryQueryName );
				// echo "$strQueryName<br>";

				if ( !$lngResultID = $objDB->execute( $strQueryName ) )
				{
					fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
					$objDB->close();
					return true;
				}

				$lngResultNum = pg_num_rows( $lngResultID );

				if( $lngResultNum != 0 )
				{
					$aryData["strProductEnglishName_Error"] = "visibility:visible;width:16;";
					$aryData["strProductEnglishName_Error_Message"] = fncOutputError ( 305, "", "", FALSE, "", $objDB );
					$errorCount++;
				}
			}

/*
			// ������ɤ��狼��ʤ�����������Ťͤ�ʤ��Ǿʤ��줿�ߤ����Ǥ���
			if( $errorCount == 0 )
			{

				// ��ǧ����ɽ��
				$aryData["strBodyOnload"] = "";

			}
*/

		}

		// �ץ�������˥塼������ �ͤ����äƤ����� 
		// ���ƥ��꡼
		$aryData["lngCategoryCode"]				= fncGetPulldownQueryExec( fncSqlqueryCategory(array(0=>$lngUserCode)), $aryData["lngCategoryCode"], $objDB);
		// �ٻ�ñ��
		$aryData["lngPackingUnitCode"]			= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryData["lngPackingUnitCode"], "WHERE bytpackingconversionflag=true", $objDB);
		// ����ñ��
		$aryData["lngProductUnitCode"]			= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryData["lngProductUnitCode"], "WHERE bytproductconversionflag=true", $objDB);
		// ����ͽ�����ñ��
		$aryData["lngProductionUnitCode"]		= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryData["lngProductionUnitCode"], '', $objDB);
		// ���Ǽ�ʿ���ñ��
		$aryData["lngFirstDeliveryUnitCode"]		= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryData["lngFirstDeliveryUnitCode"], '', $objDB);
		// �о�ǯ��
		$aryData["lngTargetAgeCode"]			= fncGetPulldown( "m_targetage", "lngTargetAgeCode", "strTargetAgeName", $aryData["lngTargetAgeCode"], '', $objDB);
		// �ڻ� �ơ��֥�ʤ�
		$aryData["lngCertificateClassCode"]		= fncGetPulldown( "m_CertificateClass", "lngcertificateclasscode", "strcertificateclassname", $aryData["lngCertificateClassCode"], '', $objDB);
		// �Ǹ���
		$aryData["lngCopyrightCode"]			= fncGetPulldown( "m_copyright", "lngcopyrightcode", "strcopyrightname", $aryData["lngCopyrightCode"], '', $objDB);
		// ���ʷ��� �ơ��֥�ʤ�
		$aryData["lngProductFormCode"]			= fncGetPulldown( "m_productform","lngproductformcode","strproductformname", $aryData["lngProductFormCode"], '', $objDB);
		// ���ʹԾ���
//		$aryData["lngGoodsPlanProgressCode"]	= fncGetPulldown( "m_goodsplanprogress","lnggoodsplanprogresscode", "strgoodsplanprogressname",1,'', $objDB);
		$aryData["lngGoodsPlanProgressCode"]	= fncGetPulldown( "m_goodsplanprogress","lnggoodsplanprogresscode", "strgoodsplanprogressname", $aryData["lngGoodsPlanProgressCode"], '', $objDB);
//�ץ쥼��ơ���������Ͽ�Ǥ���褦�˽�����050328��by�⡡
		// ���å���֥�����
		if( strcmp( $aryData["lngAssemblyFactoryCode"],"" ) != 0 )
		{
			addslashes( $aryData["lngAssemblyFactoryCode"] );
		}


		// ���;ܺ٤��ü�ʸ���Ѵ�
		//$aryData["strSpecificationDetails"] = fncHTMLSpecialChars( $aryData["strSpecificationDetails"] );
		//============================================================================================
		// ���;ܺ�HIDDEN�ѡ�HIDDEN�������ि���;ʬ�ʥ����ʤɤ��������
		if( strcmp( $aryData["strSpecificationDetails"], "") != 0 )
		{
			$aryData["strSpecificationDetails"] = stripslashes($aryData["strSpecificationDetails"]);
				$aryData["strSpecificationDetails"] = htmlspecialchars($aryData["strSpecificationDetails"], ENT_COMPAT | ENT_HTML401, "ISO-8859-1");
		}



		/**
			���������;ܺٲ����ե�����HIDDEN����
		*/
		if( $aryData["uploadimages"] )
		{
			for( $i = 0; $i < count($aryData["uploadimages"]); $i ++ )
			{
				$aryUploadImagesHidden[]	= "<input type=\"hidden\" name=\"uploadimages[]\" value=\"" .$aryData["uploadimages"][$i]. "\" />\n";
			}

			// �Ƽ����Ѥ�����
			$aryData["re_uploadimages"]	= implode( "\n", $aryUploadImagesHidden );
			$aryData["re_editordir"]	= "<input type=\"hidden\" name=\"strTempImageDir\" value=\"" .$aryData["strTempImageDir"]. "\" />\n";
		}
		else
		{
			$aryData["re_uploadimages"]	= "";
			$aryData["re_editordir"]	= "";
		}



		//=============================================================================================
		if( $errorCount == 0 )
		{
			// ���¥��롼�ץ����ɤμ���
			$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );

			// ��ǧ�롼�Ȥ�����
			// �֥ޥ͡����㡼�װʾ�ξ��
			if( $lngAuthorityGroupCode <= DEF_DIRECT_REGIST_AUTHORITY_CODE )
			{
				$aryData["lngWorkflowOrderCode"] = '<option value="0">��ǧ�ʤ�</option>';
			}
			else
			{
				$aryData["lngWorkflowOrderCode"] = fncWorkFlow( $lngUserCode , $objDB , $aryData["lngWorkflowOrderCode"] );
			}



			$objDB->close();


			// submit�ؿ�
			define( "DEF_EN_MARK", "\\" );
			$aryData["strMonetaryrate"] = DEF_EN_MARK; //�̲ߥޡ���


			$aryData["strBodyOnload"] = "";
			$aryData["lngRegistConfirm"] = 1;
			$aryData["strurl"] = "/p/confirm/index.php?strSessionID=".$aryData["strSessionID"];
			$aryData["strActionURL"] = "index.php";

			// �ƥ�ץ졼���ɤ߹���
			$objDB->freeResult( $lngResultID );
			echo fncGetReplacedHtml( "/p/regist/parts.tmpl", $aryData, $objAuth );
			return true;
		}
		// ���ϥ��顼
		else
		{
			// ���¥��롼�ץ����ɤμ���
			$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );

			// ��ǧ�롼�Ȥ�����
			// �֥ޥ͡����㡼�װʾ�ξ��
			if( $lngAuthorityGroupCode <= DEF_DIRECT_REGIST_AUTHORITY_CODE )
			{
				$aryData["lngWorkflowOrderCode"] = '<option value="0">��ǧ�ʤ�</option>';
			}
			else
			{
				$aryData["lngWorkflowOrderCode"] = fncWorkFlow( $lngUserCode , $objDB , $aryData["lngWorkflowOrderCode"] );
			}


			// submit�ؿ�
			$aryData["lngRegistConfirm"] = 0;
			$aryData["strActionURL"] = "index.php";

			$objDB->close();
			$objDB->freeResult( $lngResultID );
			echo fncGetReplacedHtml( "/p/regist/parts.tmpl", $aryData,$objAuth );
			return true;
		}

	}

	// �ǽ��ɽ����������
	// �ץ�������˥塼������ �ͤ����äƤ����� 
	
	// ���ƥ��꡼
	$aryData["lngCategoryCode"]				= fncGetPulldownQueryExec( fncSqlqueryCategory(array(0=>$lngUserCode)), $aryData["lngCategoryCode"], $objDB);
	// �ٻ�ñ��
	$aryData["lngPackingUnitCode"]			= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", 1, "WHERE bytpackingconversionflag=true", $objDB);
	// ����ñ�� 
	$aryData["lngProductUnitCode"]			= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", 1, "WHERE bytproductconversionflag=true", $objDB);
	// ����ͽ�����ñ��
	$aryData["lngProductionUnitCode"]		= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", 1, '', $objDB);
	// ���Ǽ�ʿ���ñ��
	$aryData["lngFirstDeliveryUnitCode"]		= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", 1, '', $objDB);
	// �о�ǯ��
	$aryData["lngTargetAgeCode"]			= fncGetPulldown( "m_targetage", "lngTargetAgeCode", "strTargetAgeName", 0, '', $objDB);
	// �ڻ桡�ơ��֥�ʤ�
	$aryData["lngCertificateClassCode"]		= fncGetPulldown( "m_CertificateClass", "lngcertificateclasscode", "strcertificateclassname", 0, '', $objDB);
	// �Ǹ���
	$aryData["lngCopyrightCode"]			= fncGetPulldown( "m_copyright", "lngcopyrightcode", "strcopyrightname", 0, '', $objDB);
	// ���ʷ��֡��ơ��֥�ʤ�
	$aryData["lngProductFormCode"]			= fncGetPulldown( "m_productform", "lngproductformcode", "strproductformname", 0, '', $objDB);

	// ���ʹԾ���
	$aryData["lngGoodsPlanProgressCode"]	= fncGetPulldown( "m_goodsplanprogress", "lnggoodsplanprogresscode", "strgoodsplanprogressname", 1, '', $objDB );
	$aryData["strProcess"] = "check";

	// submit�ؿ�
	$aryData["lngRegistConfirm"] = 0;



	// ���¥��롼�ץ����ɤμ���
	$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );

	// ��ǧ�롼�Ȥ�����
	// �֥ޥ͡����㡼�װʾ�ξ��
	if( $lngAuthorityGroupCode <= DEF_DIRECT_REGIST_AUTHORITY_CODE )
	{
		$aryData["lngWorkflowOrderCode"] = '<option value="0">��ǧ�ʤ�</option>';
	}
	else
	{
		$aryData["lngWorkflowOrderCode"] = fncWorkFlow( $lngUserCode , $objDB ,"" );
	}



	// �ե�����URL
	if(strcmp($aryData["strurl"], "") == 0)
	{
		$aryData["strurl"] = "/p/confirm/index.php?strSessionID=".$aryData["strSessionID"];
	}
	$aryData["strActionURL"] = "index.php";



/**
	���;ܺ��귿ʸ��
*/
$aryData["strSpecificationDetails"]	= "�߷׿� : <br />��ư�� : <br />";



	$objDB->close();
	$objDB->freeResult( $lngResultID );

	// �إ���б�
	$aryData["lngFunctionCode"] = DEF_FUNCTION_P1;
	echo fncGetReplacedHtml( "p/regist/parts.tmpl", $aryData ,$objAuth);

	return true;


?>