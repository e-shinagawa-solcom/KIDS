<?php

// ----------------------------------------------------------------------------
/**
 * �ⷿ������� ��Ͽ��ǧ����*
 *
 * ��������
 * ����Ͽ������̤�ɽ��
 */
// ----------------------------------------------------------------------------
include('conf.inc');
require(LIB_FILE);
require_once(SRC_ROOT.'/mold/lib/cache/FormCache.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilBussinesscode.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilMold.class.php');
require_once (SRC_ROOT.'/mold/lib/index/FormMoldReport.class.php');

$objDB = new clsDB ();
$objAuth = new clsAuth ();
$objDB->open ( "", "", "", "" );

$replacement = $_REQUEST;

// ���å�����ǧ
$objAuth = fncIsSession ( $replacement ["strSessionID"], $objAuth, $objDB);

// 1800 �ⷿ�������
if ( !fncCheckAuthority( DEF_FUNCTION_MM0, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// 1804 �ⷿ�������(����)
if ( !fncCheckAuthority( DEF_FUNCTION_MM4, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// ����å��奤�󥹥��󥹤μ���
$formCache = FormCache::getInstance();

// ����å���(�ե�����)�ǡ����μ��Ф�
$resultFormCache = $formCache->get($replacement["resultHash"]);

// ����å���(�ե�����)�ǡ��������Ф������
if($resultFormCache && pg_num_rows($resultFormCache) == 1)
{
	// ����å���쥳���ɼ���
	$workCache = pg_fetch_array($resultFormCache, 0, PGSQL_ASSOC);

	// �ǥ��ꥢ�饤��
	$workFormData = FormCache::deserialize($workCache["serializeddata"]);

	// �桼�ƥ���ƥ����󥹥��󥹤μ���
	$utilBussinesscode = UtilBussinesscode::getInstance();
	$utilMold = UtilMold::getInstance();

	// ��̳�����ɤ��饳�������������
	$replacement["StatusDesc"] = $utilBussinesscode->getDescription('�ⷿ���ơ�����',  $workFormData[FormMoldHistory::Status]);

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate ();
	$objTemplate->getTemplate ( "/mm/confirm/mm_modify_confirm.html" );

	// �ǥ��ꥢ�饤������UTF-8�ˤ�����Τ�EUC-JP���᤹
	mb_convert_variables("eucjp-win", "utf-8", $workFormData);

	// �ץ졼���ۥ�����ִ�
	$objTemplate->replace(array_merge($replacement, $workFormData));
	$objTemplate->complete();

	// cookie���å�
	setcookie("strSessionID", $_REQUEST["strSessionID"]);
	setcookie("resultHash", $_REQUEST["resultHash"]);

	// html����
	echo $objTemplate->strTemplate;
}
// ����å���(�ե�����)�ǡ��������Ф��ʤ��ä����
else
{
	// ����å�����Ф�����
	fncOutputError(9065, DEF_ERROR, "", TRUE, "", $objDB);
}

function toUTF8($str)
{
	return htmlspecialchars(mb_convert_encoding($str, "utf-8", "eucjp-win"));
}