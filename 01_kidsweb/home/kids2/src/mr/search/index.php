<?php

// ----------------------------------------------------------------------------
/**
*       �ⷿĢɼ����  ��������
*
*       ��������
*         ����������ɽ������
*/
// ----------------------------------------------------------------------------

// ������ɤ߹���
include_once ( "conf.inc" );

// �饤�֥���ɤ߹���
require ( LIB_FILE );

$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

//////////////////////////////////////////////////////////////////////////
// POST(����GET)�ǡ�������
//////////////////////////////////////////////////////////////////////////
$aryData = $_REQUEST;

setcookie("strSessionID", $aryData["strSessionID"]);

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ���³�ǧ
// 1900 �ⷿ����
if ( !fncCheckAuthority( DEF_FUNCTION_MR0, $objAuth ) )
{
	fncOutputError ( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// 1902 �ⷿ�����ʸ�����
if ( !fncCheckAuthority( DEF_FUNCTION_MR2, $objAuth ) )
{
	fncOutputError ( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// �ƥ�ץ졼���ɤ߹���
echo fncGetReplacedHtmlWithBase("base_mold.html", "mr/search/mr_search.tmpl", $aryData ,$objAuth );
