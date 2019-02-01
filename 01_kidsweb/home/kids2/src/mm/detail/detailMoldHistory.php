<?php

// ----------------------------------------------------------------------------
/**
 * �ⷿĢɼ���� �ܺٲ���*
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

$aryData = $_REQUEST;

// ���å�����ǧ
$objAuth = fncIsSession ( $aryData ["strSessionID"], $objAuth, $objDB);

// 1800 �ⷿĢɼ����
if ( !fncCheckAuthority( DEF_FUNCTION_MM0, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// 1803 �ⷿ����(�ܺ�)
if ( !fncCheckAuthority( DEF_FUNCTION_MM3, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}


$moldNo = $_REQUEST["MoldNo"];
$historyNo = $_REQUEST["HistoryNo"];
$version = $_REQUEST["Version"];

// �ѥ�᡼���λ��꤬�ʤ����
if (!$moldNo || !(0 <= $historyNo) || !(0 <= $version))
{
	// ����������顼
	fncOutputError(9061, DEF_ERROR, "�����ʥѥ�᡼���Ǥ���", TRUE, "", $objDB);
}


// �桼�ƥ���ƥ��Υ��󥹥��󥹼���
$utilMold = UtilMold::getInstance();
$utilBussinesscode = UtilBussinesscode::getInstance();
$utilCompany = UtilCompany::getInstance();
$utilGroup = UtilGroup::getInstance();
$utilUser = UtilUser::getInstance();
$utilProduct = UtilProduct::getInstance();

// �ⷿ����μ���
if (!$record = $utilMold->selectMoldHistory($moldNo, $historyNo, $version))
{
	// ����������顼
	fncOutputError(9061, DEF_ERROR, "�ⷿĢɼ�μ����˼��Ԥ��ޤ�����", TRUE, "", $objDB);
}
// �ⷿ�ޥ����μ���
if (!$mold = $utilMold->selectMold($moldNo))
{
	// ����������顼
	fncOutputError(9061, DEF_ERROR, "�ⷿ�ޥ����μ����˼��Ԥ��ޤ�����", TRUE, "", $objDB);
}

// �ⷿ������ִ�ʸ���󷲤��ɲ�
$replacement = $record;
$status = $record[TableMoldHistory::Status];
// ��̳�����ɤ��饳�������������
$replacement[TableMoldHistory::Status."Desc"] = $utilBussinesscode->getDescription('�ⷿ���ơ�����',  $replacement[TableMoldHistory::Status]);

switch($status)
{
	case "10":
	case "20":
		// �����ɤ���ɽ��̾�����
		$replacement["SourceFactoryName"] = $utilCompany->selectDisplayNameByCompanyCode($replacement[TableMoldHistory::SourceFactory]);
		$replacement["DestinationFactoryName"] = $utilCompany->selectDisplayNameByCompanyCode($replacement[TableMoldHistory::DestinationFactory]);
		// �����ɤ���ɽ�������ɤ��֤�����
		$replacement[TableMoldHistory::SourceFactory] = $utilCompany->selectDisplayCodeByCompanyCode($replacement[TableMoldHistory::SourceFactory]);
		$replacement[TableMoldHistory::DestinationFactory] = $utilCompany->selectDisplayCodeByCompanyCode($replacement[TableMoldHistory::DestinationFactory]);
		break;
}

// �����ɤ���ɽ��̾�����
$replacement["CreateByName"] = $utilUser->selectDisplayNameByUserCode($replacement[TableMoldHistory::CreateBy]);
$replacement["UpdateByName"] = $utilUser->selectDisplayNameByUserCode($replacement[TableMoldHistory::UpdateBy]);

// �����ɤ���ɽ�������ɤ��֤�����
$replacement[TableMoldHistory::CreateBy] = $utilUser->selectDisplayCodeByUserCode($replacement[TableMoldHistory::CreateBy]);
$replacement[TableMoldHistory::UpdateBy] = $utilUser->selectDisplayCodeByUserCode($replacement[TableMoldHistory::UpdateBy]);

// ���ʥ����ɤμ���
$replacement[TableMold::ProductCode] = $mold[TableMold::ProductCode];
// ����̾�Τμ���
$replacement["ProductName"] = $utilProduct->selectProductNameByProductCode($replacement[TableMold::ProductCode]);

// �ƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate ();
$objTemplate->getTemplate ("/mm/detail/mm_detail.html");

// �ץ졼���ۥ�����ִ�
$objTemplate->replace($replacement);
$objTemplate->complete();

// HTML����
echo $objTemplate->strTemplate;
