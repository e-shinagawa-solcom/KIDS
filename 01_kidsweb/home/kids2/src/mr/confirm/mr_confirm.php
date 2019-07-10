<?php

// ----------------------------------------------------------------------------
/**
 * �ⷿĢɼ���� ��Ͽ��ǧ����*
 *
 * ��������
 * ����Ͽ��ǧ���̤�ɽ��
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

$aryData = $_REQUEST;
// ���å�����ǧ
$objAuth = fncIsSession ( $aryData ["strSessionID"], $objAuth, $objDB);

// 1900 �ⷿĢɼ����
if ( !fncCheckAuthority( DEF_FUNCTION_MR0, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// 1901 �ⷿ����(��Ͽ)
if ( !fncCheckAuthority( DEF_FUNCTION_MR1, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// ����å��奤�󥹥��󥹤μ���
$formCache = FormCache::getInstance();

// ����å���(�ե�����)�ǡ����μ��Ф�
$resultFormCache = $formCache->get($aryData["resultHash"]);

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
	$aryData["ReportCategoryDesc"] = $utilBussinesscode->getDescription('Ģɼ��ʬ',  $workFormData[FormMoldReport::ReportCategory]);
	$aryData["RequestCategoryDesc"] = $utilBussinesscode->getDescription('�����ʬ', $workFormData[FormMoldReport::RequestCategory]);
	$aryData["InstructionCategoryDesc"] = $utilBussinesscode->getDescription('�ؼ���ʬ', $workFormData[FormMoldReport::InstructionCategory]);

	// Ģɼ��ʬ��10:��ư������20:�ֵ��Ǥξ��
	if (($workFormData[FormMoldReport::ReportCategory] == "10" ||
		 $workFormData[FormMoldReport::ReportCategory] == "20"))
	{
		$aryData["TransferMethodDesc"] = $utilBussinesscode->getDescription('��ư��ˡ', $workFormData[FormMoldReport::TransferMethod]);
		$aryData["FinalKeepDesc"] = $utilBussinesscode->getDescription('������ν���', $workFormData[FormMoldReport::FinalKeep]);
	}

	// TO����(���������)�κ��� ����Ū�˺ǽ�ζⷿ�λ��������������
	$venderInfo = $utilMold->getVenderInfomation($workFormData[FormMoldReport::MoldNo."1"]);
	$aryData["SendTo"] = $venderInfo["companydisplaycode"];
	$aryData["SendToName"] = $venderInfo["companydisplayname"];

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate ();
	$objTemplate->getTemplate ( "/mr/confirm/mr_confirm.html" );

	// �ǥ��ꥢ�饤������UTF-8�ˤ�����Τ�EUC-JP���᤹
	mb_convert_variables("eucjp-win", "utf-8", $workFormData);

	// �ץ졼���ۥ�����ִ�
	$objTemplate->replace(array_merge($aryData, $workFormData));
	$objTemplate->complete();

	// �ⷿNO�����
	$listMoldNo = UtilMold::extractArray($workFormData, FormMoldReport::MoldNo);
	// �ⷿ���������
	$listMoldDescription = UtilMold::extractArray($workFormData, FormMoldReport::MoldDescription);

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
		$elmTableCellDescription = $doc->createElement("td");

		// td������Υƥ���������
		$elmTableCellIndex->appendChild($doc->createTextNode($i));
		$elmTableCellMoldNo->appendChild($doc->createTextNode(toUTF8($listMoldNo[FormMoldReport::MoldNo.$i])));
		$elmTableCellDescription->appendChild($doc->createTextNode(toUTF8($listMoldDescription[FormMoldReport::MoldDescription.$i])));

		// td���Ǥ�tr���Ǥ��ɲ�
		$elmTableRacord->appendChild($elmTableCellIndex);
		$elmTableRacord->appendChild($elmTableCellMoldNo);
		$elmTableRacord->appendChild($elmTableCellDescription);

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
