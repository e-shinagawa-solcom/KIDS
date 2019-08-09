<?php

// ----------------------------------------------------------------------------
/**
*       ���ʴ���  �������ܲ��� ( Inline Frame )
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
*         ���������ܲ���ɽ������ ( Inline Frame )
*
*       ��������
*
*/
// ----------------------------------------------------------------------------



	// ������ɤ߹���
	include_once ( "conf.inc" );

	// �饤�֥���ɤ߹���
	require ( LIB_FILE );
	require(SRC_ROOT."po/cmn/lib_po.php");
	require( "libsql.php" );

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

	// ���å�����ǧ
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	// ���³�ǧ
	if ( !fncCheckAuthority( DEF_FUNCTION_P0, $objAuth ) )
	{
		fncOutputError ( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}
	if ( !fncCheckAuthority( DEF_FUNCTION_P2, $objAuth ) )
	{
		fncOutputError ( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}

	// 304 ���ʴ����ʾܺ�ɽ����
	if ( fncCheckAuthority( DEF_FUNCTION_P4, $objAuth ) )
	{
		$aryData["btnDetail_visibility"] = "visible";
		$aryData["btnDetailVisible"] = "checked";
	}
	else
	{
		$aryData["btnDetail_visibility"] = "hidden";
		$aryData["btnDetailVisible"] = "";
	}
	// 306 ���ʴ����ʽ�����
	if ( fncCheckAuthority( DEF_FUNCTION_P6, $objAuth ) )
	{
		$aryData["btnFix_visibility"] = "visible";
		$aryData["btnFixVisible"] = "checked";
	}
	else
	{
		$aryData["btnFix_visibility"] = "hidden";
		$aryData["btnFixVisible"] = "";
	}
	// 306 ���ʴ����ʺ����
	if ( fncCheckAuthority( DEF_FUNCTION_P7, $objAuth ) )
	{
		$aryData["btnDelete_visibility"] = "visible";
		$aryData["btnDeleteVisible"] = "checked";
	}
	else
	{
		$aryData["btnDelete_visibility"] = "hidden";
		$aryData["btnDeleteVisible"] = "";
	}

	// ʸ��������å�
	$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );

	// �ץ�������˥塼
	// ����
	$aryData["lngInChargeGroupCodeSelect"]	= fncGetPulldown( "m_group", "lnggroupcode", "strgroupdisplaycode || ' ' || strgroupdisplayname as strgroupdisplayname", 0,'WHERE bytgroupdisplayflag = true and lngcompanycode in (0,1)', $objDB );
	// ���ƥ��꡼
	$lngUserCode = $objAuth->UserCode;
	$aryData["lngCategoryCode"]				= fncGetPulldownQueryExec( fncSqlqueryCategory2(array(0=>$lngUserCode)), $aryData["lngCategoryCode"], $objDB, 2);
	// ���ʹԾ���
	$aryData["lngGoodsPlanProgressCode"]	= fncGetPulldown( "m_goodsplanprogress", "lnggoodsplanprogresscode", "strgoodsplanprogressname", 0,'', $objDB );
	// �ڻ�
	$aryData["lngCertificateClassCode"]		= fncGetPulldown( "m_CertificateClass", "lngcertificateclasscode", "strcertificateclassname", 0, '', $objDB );
	// �Ǹ���
	$aryData["lngCopyrightCode"]			= fncGetPulldown( "m_copyright", "lngcopyrightcode", "strcopyrightname", 0, '', $objDB );

	// ����ե�����
	$aryData["lngWorkFlowStatusCode"] 	= fncGetCheckBoxObject( "m_workflowstatus", "lngworkflowstatuscode", "strworkflowstatusname", "lngWorkFlowStatusCode[]", 'where lngworkflowstatuscode not in (0,2,3)', $objDB );

	//���ץ������ꥹ�Ȥμ����˼��Ԥ�����票�顼ɽ��
	if ( !$aryData["lngGoodsPlanProgressCode"] or !$aryData["lngCertificateClassCode"] or !$aryData["lngCopyrightCode"] )
	{
		fncOutputError ( 9055, DEF_WARNING, "�����ƥ�����Ԥˤ��䤤��碌��������", TRUE, "", $objDB );
	}

	// ���å���������
	if( $_COOKIE["ProductSearch"] )
	{
		$aryCookie = fncStringToArray ( $_COOKIE["ProductSearch"], "&", ":" );
		while ( list ($strKeys, $strValues ) = each ( $aryCookie ) )
		{
			$aryData[$strKeys] = $strValues;
		}
	}

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "p/search_ifrm/parts.tmpl" );

	// �ƥ�ץ졼������
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

	// HTML����
	echo $objTemplate->strTemplate;

	$objDB->close();

	return true;

?>
