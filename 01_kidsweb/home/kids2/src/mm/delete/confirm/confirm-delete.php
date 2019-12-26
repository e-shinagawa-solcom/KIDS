<?php

// ----------------------------------------------------------------------------
/**
 * �ⷿ������� �������ɽ��
 */
// ----------------------------------------------------------------------------
include('conf.inc');
require(LIB_FILE);
require_once(SRC_ROOT.'/mold/lib/cache/FormCache.class.php');
require_once (SRC_ROOT.'/mold/lib/UtilBussinesscode.class.php');
require_once (SRC_ROOT.'/mold/lib/UtilMold.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilGroup.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilUser.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilProduct.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilCompany.class.php');
require_once (SRC_ROOT.'/mold/lib/index/FormMoldReport.class.php');
require_once(SRC_ROOT.'/mold/lib/index/TableMoldReport.class.php');
require_once(SRC_ROOT.'/mold/lib/index/TableMoldReportDetail.class.php');

$objDB = new clsDB ();
$objAuth = new clsAuth ();
$objDB->open ( "", "", "", "" );

// ���å�����ǧ
$objAuth = fncIsSession ($_REQUEST["strSessionID"], $objAuth, $objDB);

// 1800 �ⷿĢɼ����
if ( !fncCheckAuthority( DEF_FUNCTION_MM0, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// 1805 �ⷿ����(���)
if ( !fncCheckAuthority( DEF_FUNCTION_MM5, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// �ѥ�᡼������
$moldNo = $_REQUEST["MoldNo"];
$historyNo = $_REQUEST["HistoryNo"];
$version = $_REQUEST["Version"];

if (!$moldNo || !(0 <= $historyNo) || !(0 <= $version))
{
	// ����μ����˼��Ԥ��ޤ���
	fncOutputError(9061, DEF_ERROR, "", TRUE, "", $objDB);
}

// �桼�ƥ���ƥ��Υ��󥹥��󥹼���
$utilMold = UtilMold::getInstance();
$utilBussinesscode = UtilBussinesscode::getInstance();
$utilCompany = UtilCompany::getInstance();
$utilGroup = UtilGroup::getInstance();
$utilUser = UtilUser::getInstance();
$utilProduct = UtilProduct::getInstance();

// �ⷿ����κ���
try
{
	// �ⷿ����
	$record = $utilMold->selectMoldHistory($moldNo, $historyNo, $version);
	$infoMold = $utilMold->selectMold($moldNo);
	$status = $record[TableMoldHistory::Status];
	$descStatus = $utilBussinesscode->getDescription("�ⷿ���ơ�����", $status);

	// ���ʥ�����/̾��
	$productCode = $infoMold[TableMold::ProductCode];
	$reviseCode = $infoMold[TableMold::ReviseCode];
	$productName = $utilProduct->selectProductNameByProductCode($productCode, $reviseCode);

	switch ($status)
	{
		case "10":
		case "20":
			// �ݴɹ���
			$srcFactoryCode = $record[TableMoldHistory::SourceFactory];
			$displaySrcFactoryCode = $utilCompany->selectDisplayCodeByCompanyCode($srcFactoryCode);
			$displaySrcFactoryName = $utilCompany->selectDisplayNameByCompanyCode($srcFactoryCode);
			// ��ư�蹩��
			$dstFactoryCode = $record[TableMoldHistory::DestinationFactory];
			$displayDstFactoryCode = $utilCompany->selectDisplayCodeByCompanyCode($dstFactoryCode);
			$displayDstFactoryName = $utilCompany->selectDisplayNameByCompanyCode($dstFactoryCode);
			break;
	}


}
catch (SQLException $e)
{
	// ����μ����˼��Ԥ��ޤ���
	fncOutputError(9061, DEF_ERROR, "�����ʥǡ������оݤΥǡ������ѹ����줿��ǽ��������ޤ���", TRUE, "", $objDB);
}

// �ִ�ʸ���󷲤κ���
$replacement = $record;
$replacement[TableMoldHistory::ActionDate] = str_replace("-", "/", $record[TableMoldHistory::ActionDate]);
$replacement[TableMoldHistory::Status] = $descStatus;
$replacement[FormMoldHistory::ProductCode] = $productCode;
$replacement[FormMoldHistory::ReviseCode] = $reviseCode;
$replacement[FormMoldHistory::ProductName] = $productName;
$replacement[TableMoldHistory::SourceFactory] = $displaySrcFactoryCode;
$replacement[FormMoldHistory::SourceFactoryName] = $displaySrcFactoryName;
$replacement[TableMoldHistory::DestinationFactory] = $displayDstFactoryCode;
$replacement[FormMoldHistory::DestinationFactoryName] = $displayDstFactoryName;

// �ƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate ();
$objTemplate->getTemplate ("/mm/delete/mm_confirm_delete.html");

// �ץ졼���ۥ�����ִ�
$objTemplate->replace($replacement);
$objTemplate->complete();

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

// �ⷿĢɼID�ȥ�ӥ�����������
$btnDelete = $doc->getElementById("delete-button");
$btnDelete->setAttribute("MoldNo", $moldNo);
$btnDelete->setAttribute("HistoryNo", $historyNo);
$btnDelete->setAttribute("Version", $version);

setcookie("strSessionID", $_REQUEST["strSessionID"]);

// HTML����
echo $doc->saveHTML();
