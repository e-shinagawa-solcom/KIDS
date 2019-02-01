<?php

// ----------------------------------------------------------------------------
/**
*       ���ʴ���  �ܺ�
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
*         �����꾦���ֹ�ǡ����ξܺ�ɽ������
*
*       ��������
*
*/
// ----------------------------------------------------------------------------



	// �����ɤ߹���
	include_once('conf.inc');

	// �饤�֥���ɤ߹���
	require (LIB_FILE);
	require (LIB_ROOT . "clscache.php" );
	require (LIB_ROOT . "libdebug.php" );
	require (SRC_ROOT . "p/cmn/lib_ps1.php");
	require (SRC_ROOT . "p/cmn/column.php");

	// DB��³
	$objDB   = new clsDB();
	$objAuth = new clsAuth();
	$objCache = new clsCache();
	$objDB->open( "", "", "", "" );

	//////////////////////////////////////////////////////////////////////////
	// GET�ǡ�������
	//////////////////////////////////////////////////////////////////////////
	if ( $_GET )
	{
		$aryData = $_GET;
	}
	else if ( $_POST )
	{
		$aryData = $_POST;
	}

	// ʸ��������å�
	$aryCheck["strSessionID"] = "null:numenglish(32,32)";
	$aryCheck["lngProductNo"] = "null:number(0,10)";
	// $aryResult = fncAllCheck( $aryData, $aryCheck );
	// fncPutStringCheckError( $aryResult, $objDB );

	// ���å�����ǧ
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	// ���³�ǧ
	// 302 ���ʴ����ʾ��ʸ�����
	if ( !fncCheckAuthority( DEF_FUNCTION_P2, $objAuth ) )
	{
		fncOutputError ( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}
	// 304 ���ʴ����ʾܺ�ɽ����
	if ( !fncCheckAuthority( DEF_FUNCTION_P4, $objAuth ) )
	{
		fncOutputError ( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}

	//�ܺٲ��̤�ɽ��

	$lngProductNo = $aryData["lngProductNo"];

	// ���꾦���ֹ�ξ��ʥǡ���������SQLʸ�κ���
	$strQuery = fncGetProductNoToInfoSQL ( $lngProductNo );

	// �ܺ٥ǡ����μ���
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		if ( $lngResultNum == 1 )
		{
			$aryResult = $objDB->fetchArray( $lngResultID, 0 );
		}
		else
		{
			fncOutputError( 303, DEF_ERROR, "�����ǡ����μ����˼��Ԥ��ޤ���", TRUE, "../p/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}
	}
	else
	{
		fncOutputError( 303, DEF_ERROR, "�ǡ������۾�Ǥ�", TRUE, "../p/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
	}

	$objDB->freeResult( $lngResultID );

//fncDebug("lib_ps.txt", $aryResult, __FILE__, __LINE__);

	// �����ǡ�����Ĵ��
	$aryNewResult = fncSetProductTableData ( $aryResult, $objDB, $objCache );

	// ���������
	if ( isset($aryData["lngLanguageCode"]) and  $aryData["lngLanguageCode"] == 0 )
	{
		$aryTytle = $aryTableTytleEng;
	}
	else
	{
		$aryTytle = $aryTableTytle;
	}

	// �����̾������
	$aryColumnNames = fncSetProductTabelName ( $aryTableView, $aryTytle );

	$aryNewResult["strAction"] = "index2.php";
	$aryNewResult["strMode"] = "detail";

fncDebug("lib_ps.txt", $aryColumnNames, __FILE__, __LINE__);


	// Ģɼ�����б�
	// ɽ���оݤ�����ǡ����ξ��ϥץ�ӥ塼�ܥ����ɽ�����ʤ�
	// �ʤ����¤���äƤʤ�����ץ�ӥ塼�ܥ����ɽ�����ʤ�
	if ( !$aryResult["bytInvalidFlag"] and fncCheckAuthority( DEF_FUNCTION_LO1, $objAuth ) && $aryResult["lngproductstatuscode"] != DEF_PRODUCT_APPLICATE )
	{
		$aryNewResult["strPreviewAction"] = "../../list/result/frameset.php?strSessionID=" . $aryData["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_PRODUCT . "&strReportKeyCode=" . $lngProductNo . "&bytCopyFlag=TRUE";

		$aryNewResult["listview"] = 'visible';
	}
	else
	{
		$aryNewResult["listview"] = 'hidden';
	}



	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "p/result/parts2.tmpl" );

	// �ƥ�ץ졼������
	$objTemplate->replace( $aryNewResult );
	$objTemplate->replace( $aryHeadColumnNames );
	$objTemplate->complete();

	// HTML����
	echo $objTemplate->strTemplate;


	$objDB->close();

	$objCache->Release();

	return true;

?>
