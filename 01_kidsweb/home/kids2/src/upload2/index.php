<?php
/* ************************************************************************* */
/* UPLOAD Ver.Peruser */
/* ************************************************************************* */

/**
*	�ե����륢�åץ��ɲ���
*
*	@charset	: euc-jp
*/


	include ( 'conf.inc' );		// �����ɤ߹���
	require ( LIB_DEBUGFILE );	// Debug�⥸�塼��

	require ( LIB_FILE );		// �饤�֥���ɤ߹���



	$objDB		= new clsDB();
	$objAuth	= new clsAuth();

	//-------------------------------------------------------------------------
	// DB�����ץ�
	//-------------------------------------------------------------------------
	$objDB->open( "", "", "", "" );


	//-------------------------------------------------------------------------
	// �ѥ�᡼������
	//-------------------------------------------------------------------------
	$aryData	= $_REQUEST;

	$aryData["lngLanguageCode"]		= $_COOKIE["lngLanguageCode"];	// ���쥳����
	$aryData["style"]				= 'segment';					// or "old"
	$aryData["lngFunctionCode"]		= DEF_FUNCTION_E1;				// ���������ɡʸ��Ѹ�����
	$aryData["lngRegistConfirm"]	= 0;							// ��ǧ����ɽ���ե饰

    setcookie("strSessionID", $aryData["strSessionID"], 0, "/");
	//-------------------------------------------------------------------------
	// ����ʸ�����͡����å���󡦸��¥����å�
	//-------------------------------------------------------------------------
	// ʸ��������å�
	$aryCheck["strSessionID"]	= "null:numenglish(32,32)";
	$aryResult	= fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );

	// ���å�����ǧ
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	// �桼���������ɼ���
	$lngUserCode = $objAuth->UserCode;

	// ���³�ǧ
	if( !fncCheckAuthority( DEF_FUNCTION_UP0, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}

	// ���¥��롼�ץ����ɤμ���
	$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );


	//-------------------------------------------------------------------------
	// DB Close
	//-------------------------------------------------------------------------
	$objDB->close();
	$objDB->freeResult( $lngResultID );

// fncDebug( 'parse.txt', $_FILES, __FILE__, __LINE__);
// fncDebug( 'parse.txt', fncGetReplacedHtmlWithBase("search/base_search.html", "upload2/parts.tmpl", $aryData ,$objAuth ), __FILE__, __LINE__);

	/*-------------------------------------------------------------------------
		����
	-------------------------------------------------------------------------*/
	// HTML����
	echo fncGetReplacedHtmlWithBase("search/base_search.html", "upload2/parts.tmpl", $aryData ,$objAuth );


	unset( $objDB, $objAuth, $aryData, $strTmpFileName );
	return true;

?>
