<?php

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
	$aryData["strActionScriptName"]	= '/upload/parse/parse.php';	// �����꡼�¹ԥ�����ץȥѥ�
	$aryData["lngRegistConfirm"]	= 0;							// ��ǧ����ɽ���ե饰


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





	/*-------------------------------------------------------------------------
		�ƥ�ݥ��ե��������
	-------------------------------------------------------------------------*/
	if( $_FILES )
	{
		// �ƥ�ݥ��ե�����������ե�����̾����
		$strTmpFileName	= getTempFileName( $_FILES['excel_file']['tmp_name'] );

		// �ե��������μ���
		$aryData["exc_name"]			= $_FILES['excel_file']['name'];
		$aryData["exc_type"]			= $_FILES['excel_file']['type'];
		$aryData["exc_tmp_name"]		= $strTmpFileName;
		$aryData["exc_error"]			= $_FILES['excel_file']['error'];
		$aryData["exc_size"]			= $_FILES['excel_file']['size'];

		$aryData["lngRegistConfirm"]	= 1;	// ��ǧ����ɽ���ե饰
	}


//fncDebug( 'parse.txt', $_FILES, __FILE__, __LINE__);
fncDebug( 'parse.txt', fncGetReplacedHtml( "upload/parts.tmpl", $aryData, $objAuth ), __FILE__, __LINE__);

	/*-------------------------------------------------------------------------
		����
	-------------------------------------------------------------------------*/
	// HTML����
	echo fncGetReplacedHtml( "upload/parts.tmpl", $aryData, $objAuth );


	unset( $objDB, $objAuth, $aryData, $strTmpFileName );
	return true;

?>
