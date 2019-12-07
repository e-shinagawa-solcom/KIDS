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

// 1900 �ⷿĢɼ����
if ( !fncCheckAuthority( DEF_FUNCTION_MR0, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// 1903 �ⷿĢɼ����(����)
if ( !fncCheckAuthority( DEF_FUNCTION_MR3, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// �ѥ�᡼������
$moldReportId = $_REQUEST["MoldReportId"];
$revision = $_REQUEST["Revision"];
$version = $_REQUEST["Version"];

// �ⷿĢɼID�λ��꤬�ʤ����
if (!$moldReportId || !(0 <= $revision) || !(0 <= $version))
{
	// ����������顼
	fncOutputError(9061, DEF_ERROR, "�ѥ�᡼���������Ǥ���", TRUE, "", $objDB);
}

// �桼�ƥ���ƥ��Υ��󥹥��󥹼���
$utilMold = UtilMold::getInstance();
$utilBussinesscode = UtilBussinesscode::getInstance();
$utilCompany = UtilCompany::getInstance();
$utilGroup = UtilGroup::getInstance();
$utilUser = UtilUser::getInstance();
$utilProduct = UtilProduct::getInstance();

// �ⷿĢɼ�μ���
if (!$recordMoldReport = $utilMold->selectMoldReport($moldReportId, $revision, $version))
{
	// ����������顼
	fncOutputError(9061, DEF_ERROR, "�ⷿĢɼ�μ����˼��Ԥ��ޤ������оݥǡ������ѹ����줿��ǽ��������ޤ���", TRUE, "", $objDB);
}

// �ⷿĢɼ�ܺ٤μ���
if (!$recordMoldReportDetail = $utilMold->selectMoldReportDetail($moldReportId, $recordMoldReport[TableMoldReport::Revision]))
{
	// ����������顼
	fncOutputError(9061, DEF_ERROR, "�ⷿĢɼ�ܺ٤μ����˼��Ԥ��ޤ������оݥǡ������ѹ����줿��ǽ��������ޤ���", TRUE, "", $objDB);
}

// �ⷿĢɼ/�ⷿĢɼ�ܺ� ����ޡ���
$replacement = array_merge($recordMoldReport, $recordMoldReportDetail);

// ��̳�����ɤ��饳�������������
$replacement["ReportCategoryDesc"] = $utilBussinesscode->getDescription('Ģɼ��ʬ',  $replacement[TableMoldReport::ReportCategory]);
$replacement["RequestCategoryDesc"] = $utilBussinesscode->getDescription('�����ʬ', $replacement[TableMoldReport::RequestCategory]);
$replacement["InstructionCategoryDesc"] = $utilBussinesscode->getDescription('�ؼ���ʬ', $replacement[TableMoldReport::InstructionCategory]);

// �����ɤ���ɽ��̾�����
$replacement["CustomerName"] = $utilCompany->selectDisplayNameByCompanyCode($replacement[TableMoldReport::CustomerCode]);
$replacement["KuwagataGroupName"] = $utilGroup->selectDisplayNameByGroupCode($replacement[TableMoldReport::KuwagataGroupCode]);
$replacement["KuwagataUserName"] = $utilUser->selectDisplayNameByUserCode($replacement[TableMoldReport::KuwagataUserCode]);
$replacement["CreateByName"] = $utilUser->selectDisplayNameByUserCode($replacement[TableMoldReport::CreateBy]);
$replacement["UpdateByName"] = $utilUser->selectDisplayNameByUserCode($replacement[TableMoldReport::UpdateBy]);

// �����ɤ���ɽ�������ɤ��֤�����
$replacement[TableMoldReport::CustomerCode] = $utilCompany->selectDisplayCodeByCompanyCode($replacement[TableMoldReport::CustomerCode]);
$replacement[TableMoldReport::KuwagataGroupCode] = $utilGroup->selectDisplayCodeByGroupCode($replacement[TableMoldReport::KuwagataGroupCode]);
$replacement[TableMoldReport::KuwagataUserCode] = $utilUser->selectDisplayCodeByUserCode($replacement[TableMoldReport::KuwagataUserCode]);
$replacement[TableMoldReport::CreateBy] = $utilUser->selectDisplayCodeByUserCode($replacement[TableMoldReport::CreateBy]);
$replacement[TableMoldReport::UpdateBy] = $utilUser->selectDisplayCodeByUserCode($replacement[TableMoldReport::UpdateBy]);

switch ($recordMoldReport[TableMoldReport::ReportCategory])
{
	case "10":
	case "20":
		// ��̳�����ɤ��饳�������������
		$replacement["TransferMethodDesc"] = $utilBussinesscode->getDescription('��ư��ˡ', $replacement[TableMoldReport::TransferMethod]);
		$replacement["FinalKeepDesc"] = $utilBussinesscode->getDescription('������ν���', $replacement[TableMoldReport::FinalKeep]);
		// �����ɤ���ɽ��̾�����
		$replacement["SourceFactoryName"] = $utilCompany->selectDisplayNameByCompanyCode($replacement[TableMoldReport::SourceFactory]);
		$replacement["DestinationFactoryName"] = $utilCompany->selectDisplayNameByCompanyCode($replacement[TableMoldReport::DestinationFactory]);
		// �����ɤ���ɽ�������ɤ��֤�����
		$replacement[TableMoldReport::SourceFactory] = $utilCompany->selectDisplayCodeByCompanyCode($replacement[TableMoldReport::SourceFactory]);
		$replacement[TableMoldReport::DestinationFactory] = $utilCompany->selectDisplayCodeByCompanyCode($replacement[TableMoldReport::DestinationFactory]);
		break;
}

// ����̾�Τμ���
$replacement["ProductName"] = $utilProduct->selectProductNameByProductCode($replacement[TableMoldReport::ProductCode], $replacement[TableMoldReport::strReviseCode]);

// TO����(���������)�κ��� ����Ū�˺ǽ�ζⷿ�λ��������������
$venderInfo = $utilMold->getVenderInfomation($recordMoldReportDetail[0][TableMoldReportDetail::MoldNo]);
$replacement["SendTo"] = $venderInfo["companydisplaycode"];
$replacement["SendToName"] = $venderInfo["companydisplayname"];

// �ƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate ();
$objTemplate->getTemplate ("/mr/detail/mr_detail.html");

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

// �ⷿ�ơ��֥�μ���
$moldTable = $doc->getElementById("MoldTable");

// �ⷿĢɼ�ܺ٤η��ʬ����
foreach ($recordMoldReportDetail as $i => $record)
{
	$index = $i + 1;

	// �ⷿ�ơ��֥��tr����
	$tr = $doc->createElement("tr");

	// �ⷿ�ơ��֥��td���Ǻ���
	$cellIndex = $doc->createElement("td", $index);
	$cellMoldNo = $doc->createElement("td", toUTF8($record[TableMoldReportDetail::MoldNo]));
	$cellDescription = $doc->createElement("td", toUTF8($record[TableMoldReportDetail::MoldDescription]));

	// td���Ǥ�tr���Ǥ��ɲ�
	$tr->appendChild($cellIndex);
	$tr->appendChild($cellMoldNo);
	$tr->appendChild($cellDescription);

	// �ⷿ�ơ��֥��tr���Ǥ��ɲ�
	$moldTable->appendChild($tr);
}

// HTML����
echo $doc->saveHTML();

