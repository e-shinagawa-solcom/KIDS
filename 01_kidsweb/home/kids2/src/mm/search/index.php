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

// 1800 �ⷿ�������
if ( !fncCheckAuthority( DEF_FUNCTION_MM0, $objAuth ) )
{
	fncOutputError ( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// 1802 �ⷿ��������ʸ�����
if ( !fncCheckAuthority( DEF_FUNCTION_MM2, $objAuth ) )
{
	fncOutputError ( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// TODO ���¥����å���Ʊ���˸��¤Τʤ��桼�����ˤϽ���/������̤����Ǥ��ä�ɽ��������

// �ƥ�ץ졼���ɤ߹���
echo fncGetReplacedHtmlWithBase("base_mold.html", "mm/search/mm_search.tmpl", $aryData ,$objAuth );
