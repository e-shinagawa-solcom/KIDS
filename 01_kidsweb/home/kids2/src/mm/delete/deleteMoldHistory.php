<?php

// ----------------------------------------------------------------------------
/**
 * �ⷿ������� �������
 */
// ----------------------------------------------------------------------------
include('conf.inc');
require(LIB_FILE);
require_once(SRC_ROOT.'/mold/lib/cache/FormCache.class.php');
require_once (SRC_ROOT.'/mold/lib/UtilBussinesscode.class.php');
require_once(SRC_ROOT.'/mold/validation/UtilValidation.class.php');
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

$aryData = $_REQUEST;

// ���å�����ǧ
$objAuth = fncIsSession ( $aryData ["strSessionID"], $objAuth, $objDB);

// 1800 �ⷿ�������
if ( !fncCheckAuthority( DEF_FUNCTION_MM0, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// 1805 �ⷿ�������(���)
if ( !fncCheckAuthority( DEF_FUNCTION_MM5, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// �ѥ�᡼������
$moldNo = $_REQUEST["MoldNo"];
$historyNo = $_REQUEST["HistoryNo"];
$version = $_REQUEST["Version"];

if (!$moldNo && !(0 <= $historyNo) && !(0 <= $version))
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
	$productName = $utilProduct->selectProductNameByProductCode($productCode);

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

// �ⷿ����쥳���ɤ�̵����
$affect_count = $utilMold->disableMoldHistory($moldNo, $historyNo, $version);

// �ִ�ʸ���󷲤κ���
$replacement = $record;

// �ƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate ();
$objTemplate->getTemplate ("/mm/delete/mm_finish_delete.html");

// �ץ졼���ۥ�����ִ�
$objTemplate->replace($replacement);
$objTemplate->complete();

echo $objTemplate->strTemplate;