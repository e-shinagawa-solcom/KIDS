<?php

// ----------------------------------------------------------------------------
/**
*       �ⷿ�������  ��Ͽ
*
*       ��������
*         ����Ͽ����
*         ����Ͽ������λ�塢��Ͽ��λ���̤�
*
*/
// ----------------------------------------------------------------------------
include( 'conf.inc' );
require_once( LIB_FILE );
require_once(SRC_ROOT.'/mold/lib/cache/FormCache.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilBussinesscode.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilMold.class.php');

$objDB   = new clsDB();
$objAuth = new clsAuth();

// DB�����ץ�
$objDB->open("", "", "", "");

// �ꥯ�����ȼ���
$aryData = $_REQUEST;

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 1900 �ⷿĢɼ����
if ( !fncCheckAuthority( DEF_FUNCTION_MR0, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// 1901 �ⷿĢɼ����(��Ͽ)
if ( !fncCheckAuthority( DEF_FUNCTION_MM1, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// ����å��奤�󥹥��󥹤μ���
$formCache = FormCache::getInstance();
$resultFormCache = $formCache->get($aryData["resultHash"]);

// ����å���(�ե�����)�ǡ��������Ф������
if($resultFormCache && pg_num_rows($resultFormCache) == 1)
{
	$result = false;

	// ����å���쥳���ɼ���
	$workCache = pg_fetch_array($resultFormCache, 0, PGSQL_ASSOC);

	// �ǥ��ꥢ�饤��
	$workFormData = FormCache::deserialize($workCache["serializeddata"]);

	// �ǥ��ꥢ�饤������UTF-8�ˤ�����Τ�EUC-JP���᤹
	mb_convert_variables("euc-jp", "utf-8", $workFormData);

	// �ե�����ǡ����˥桼���������ɤ��ɲ�
	$workFormData["UserCode"] = $objAuth->UserCode;

	// Util���󥹥��󥹤μ���
	$utilMold = UtilMold::getInstance();
	$utilMold->setUserCode($objAuth->UserCode);

	// �ȥ�󥶥�����󳫻�
	$objDB->transactionBegin();

	// �ⷿ��Ϣ�ơ��֥�Υ�å�
	pg_query("LOCK m_moldreport");
	pg_query("LOCK t_moldreportdetail");
	pg_query("LOCK t_moldreportrelation");
	pg_query("LOCK t_moldhistory");

	// �ⷿ�ꥹ�Ȥμ��Ф�
	$molds = $workFormData["list_moldno"];

	// �ⷿ����Υ����������Ⱥ���
	$summaryHistory = $utilMold->selectSummaryOfMoldHistory($molds);
	$digestHistory = FormCache::hash_arrays($summaryHistory);

	// �ⷿĢɼ�Υ����������Ⱥ���
	$summaryReport = $utilMold->selectSummaryOfMoldReport($molds);
	$digestReport = FormCache::hash_arrays($summaryReport);

	// ���ڻ��ζⷿ��������������ȤȰۤʤ���
	if ($digestHistory != $workFormData["digest_history"])
	{
		// DB���顼
		fncOutputError ( 9051, DEF_ERROR, "���򤵤줿�ⷿ�����ѹ�����Ƥ��ޤ���", TRUE, "", $objDB );
	}

	// ���ڻ��ζⷿĢɼ�����������ȤȰۤʤ���
	if ($digestReport != $workFormData["digest_report"])
	{
		// DB���顼
		fncOutputError ( 9051, DEF_ERROR, "���򤵤줿�ⷿ�����ѹ�����Ƥ��ޤ���", TRUE, "", $objDB );
	}

	// �ⷿ����ؤ�INSERT�η�̤������ʤ��ä����
	if (!$resultMoldHistory = $utilMold->insertMoldHistoryByFormData($workFormData))
	{
		// DB���顼
		fncOutputError ( 9051, DEF_ERROR, "�ⷿ����ؤ���Ͽ�˼��Ԥ��ޤ�����", TRUE, "", $objDB );
	}

	// ���Ѥ����ե�����ǡ����򥭥�å���ơ��֥뤫����
	if (!$formCache->remove($aryData["resultHash"]))
	{
		// DB���顼
		fncOutputError ( 9051, DEF_ERROR, "����å���ơ��֥�Υ쥳���ɺ���˼��Ԥ��ޤ�����", TRUE, "", $objDB );
	}

	// ���ߥå�
	$objDB->transactionCommit();

	// �ե�����ǡ������ִ�ʸ���󷲤˥��å�
	$replacement = $workFormData;

	// ��̳�����ɥ桼�ƥ���ƥ��Υ��󥹥��󥹼���
	$utilBussinesscode = UtilBussinesscode::getInstance();
	// ���ơ����������μ���
	$replacement["StatusDesc"] = $utilBussinesscode->
		getDescription("�ⷿ���ơ�����", $workFormData[FormMoldHistory::Status]);
	// �ⷿ���
	$replacement["MoldCount"] = count($resultMoldHistory);

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate ();
	$objTemplate->getTemplate ( "/mm/finish/mm_finish.html" );

	// �ץ졼���ۥ�����ִ�
	$objTemplate->replace($replacement);
	$objTemplate->complete();

	// html����
	echo $objTemplate->strTemplate;
}
// ����å���(�ե�����)�ǡ��������Ф��ʤ��ä����
else
{
	// ����å�����Ф�����
	fncOutputError(9065, DEF_ERROR, "", TRUE, "", $objDB);
}

// DB��������WithQuery�Υǥ��ȥ饯�����Ĥ���١�����Ū�ˤϹԤ�ʤ�