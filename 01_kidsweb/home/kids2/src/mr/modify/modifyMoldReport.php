<?php

// ----------------------------------------------------------------------------
/**
*       �ⷿĢɼ����  ����
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

// 1904 �ⷿĢɼ����(��Ͽ)
if ( !fncCheckAuthority( DEF_FUNCTION_MR4, $objAuth ) )
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

	// �ⷿĢɼ����μ���
	$moldReportId = $workFormData[FormMoldReport::MoldReportId];
	$revision = $workFormData[FormMoldReport::Revision];
	$version = $workFormData[FormMoldReport::Version];

	// �桼���������ɼ���
	$userCode = $objAuth->UserCode;

	// �ե�����ǡ����˥桼���������ɤ��ɲ�
	$workFormData["UserCode"] = $userCode;

	// Util���󥹥��󥹤μ���
	$utilMold = UtilMold::getInstance();
	$utilMold->setUserCode($userCode);

	// �ȥ�󥶥�����󳫻�
	$objDB->transactionBegin();

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

	// ��¸�ⷿĢɼ�쥳���ɤ�̵����
	if (!$utilMold->disableMoldReport($moldReportId, $revision, $version))
	{
		// DB���顼
		fncOutputError ( 9051, DEF_ERROR, "�оݥǡ������ѹ����줿��ǽ��������ޤ���", TRUE, "", $objDB );
	}

	// ��¸�ⷿĢɼ�ܺ٥쥳���ɤ�̵����
	if (!$utilMold->disableMoldReportDetail($moldReportId, $revision))
	{
		// DB���顼
		fncOutputError ( 9051, DEF_ERROR, "�оݥǡ������ѹ����줿��ǽ��������ޤ���", TRUE, "", $objDB );
	}

	// �ⷿĢɼ�ޥ����ؤ�INSERT
	if ($resultMoldReport = $utilMold->modifyMoldReport($workFormData))
	{
		$result = $resultMoldReport;
	}
	// ��̤������ʤ��ä����
	else
	{
		// DB���顼
		fncOutputError ( 9051, DEF_ERROR, "�ⷿĢɼ�ޥ����ؤ���Ͽ�˼��Ԥ��ޤ�����", TRUE, "", $objDB );
	}

	// �ⷿĢɼ�ܺ٤ؤ�INSERT
	if ($resultMoldReportDetail = $utilMold->insertMoldReportDetail(
			$result[TableMoldReport::MoldReportId],
			$result[TableMoldReport::Revision],
			$workFormData))
	{
		// INSERT����μ���
		$result["MoldCount"] = $resultMoldReportDetail;
	}
	// ��̤������ʤ��ä����
	else
	{
		// DB���顼
		fncOutputError ( 9051, DEF_ERROR, "�ⷿĢɼ�ܺ٤ؤ���Ͽ�˼��Ԥ��ޤ�����", TRUE, "", $objDB );
	}

	// ���Ѥ����ե�����ǡ����򥭥�å���ơ��֥뤫����
	if (!$formCache->remove($aryData["resultHash"]))
	{
		// DB���顼
		fncOutputError ( 9051, DEF_ERROR, "����å���ơ��֥�Υ쥳���ɺ���˼��Ԥ��ޤ�����", TRUE, "", $objDB );
	}

	// ���ߥå�
	$objDB->transactionCommit();

	// ��̳�����ɥ桼�ƥ���ƥ��Υ��󥹥��󥹼���
	$utilBussinesscode = UtilBussinesscode::getInstance();
	// Ģɼ��ʬ�Υ���������(EUC-JP)�����
	$result[TableMoldReport::ReportCategory] =
		$utilBussinesscode->getDescription("Ģɼ��ʬ", $result[TableMoldReport::ReportCategory]);

	// cookie���å�
	setcookie("strSessionID", $_REQUEST["strSessionID"]);
	setcookie(TableMoldReport::MoldReportId, $result[TableMoldReport::MoldReportId]);

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate ();
	$objTemplate->getTemplate ( "/mr/finish/mr_finish.html" );

	// �ץ졼���ۥ�����ִ�
	$objTemplate->replace($result);
	$objTemplate->complete();

	$doc = new DOMDocument();

	// �ѡ������顼����
	libxml_use_internal_errors(true);
	// DOM�ѡ���
	$doc->loadHTML($objTemplate->strTemplate);
	// �ѡ������顼���ꥢ
	libxml_clear_errors();
	// �ѡ������顼�������
	libxml_use_internal_errors(false);

	$preview = $doc->getElementById("preview");
	$preview->setAttribute("moldreportid", $result[TableMoldReport::MoldReportId]);
	$preview->setAttribute("revision", $result[TableMoldReport::Revision]);
	$preview->setAttribute("version", $result[TableMoldReport::Version]);

	// HTML����
	echo $doc->saveHTML();
}
// ����å���(�ե�����)�ǡ��������Ф��ʤ��ä����
else
{
	// ����å�����Ф�����
	fncOutputError(9065, DEF_ERROR, "", TRUE, "", $objDB);
}

// DB��������WithQuery�Υǥ��ȥ饯�����Ĥ���١�����Ū�ˤϹԤ�ʤ�