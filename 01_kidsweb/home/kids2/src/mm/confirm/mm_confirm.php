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

// 1801 �ⷿ�������(��Ͽ)
if ( !fncCheckAuthority( DEF_FUNCTION_MM1, $objAuth ) )
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
	$objTemplate->getTemplate ( "/mm/confirm/mm_confirm.html" );

	// �ǥ��ꥢ�饤������UTF-8�ˤ�����Τ�EUC-JP���᤹
	mb_convert_variables("eucjp-win", "utf-8", $workFormData);

	// �ץ졼���ۥ�����ִ�
	$objTemplate->replace(array_merge($replacement, $workFormData));
	$objTemplate->complete();

	// �ⷿNO�����
	$listMoldNo = UtilMold::extractArray($workFormData, FormMoldReport::MoldNo);

	// �ⷿ�ơ��֥������ΰ�DOMDocument�����
	$doc = new DOMDocument();

	// �ѡ������顼����
	libxml_use_internal_errors(true);
	// DOM�ѡ���
	$doc->loadHTML(mb_convert_encoding($objTemplate->strTemplate, "utf8", "eucjp-win"));
	// �ѡ������顼���ꥢ
	libxml_clear_errors();
	// �ѡ������顼�������
	libxml_use_internal_errors(false);

	// �ⷿ�ơ��֥�μ���
	$moldTable = $doc->getElementById("MoldTable");

	// �ⷿNO�η��ʬ����
	for($i = 1; $i <= count($listMoldNo); $i++)
	{
		// �ⷿ�ơ��֥��tr����
		$elmTableRacord = $doc->createElement("tr");

		// �ⷿ�ơ��֥��td���Ǻ���
		$elmTableCellIndex = $doc->createElement("td");
		$elmTableCellMoldNo = $doc->createElement("td");

		// td������Υƥ���������
		$elmTableCellIndex->appendChild($doc->createTextNode($i));
		$elmTableCellMoldNo->appendChild($doc->createTextNode(toUTF8($listMoldNo[FormMoldReport::MoldNo.$i])));

		// td���Ǥ�tr���Ǥ��ɲ�
		$elmTableRacord->appendChild($elmTableCellIndex);
		$elmTableRacord->appendChild($elmTableCellMoldNo);

		// �ⷿ�ơ��֥��tr���Ǥ��ɲ�
		$moldTable->appendChild($elmTableRacord);
	}

	// cookie���å�
	setcookie("strSessionID", $_REQUEST["strSessionID"]);
	setcookie("resultHash", $_REQUEST["resultHash"]);

	// html����
	echo $doc->saveHTML();
}
// ����å���(�ե�����)�ǡ��������Ф��ʤ��ä����
else
{
	// ����å�����Ф�����
	fncOutputError(9065, DEF_ERROR, "", TRUE, "", $objDB);
}

function toUTF8($str)
{
	return htmlspecialchars(mb_convert_encoding($str, "utf-8", "eucjp-win"), ENT_QUOTES, 'utf-8');
}