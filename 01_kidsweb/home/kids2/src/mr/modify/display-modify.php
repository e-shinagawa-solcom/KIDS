<?php

// ----------------------------------------------------------------------------
/**
*       �ⷿ�������  ��������
*/
// ----------------------------------------------------------------------------

// ������ɤ߹���
include_once ( "conf.inc" );
require ( LIB_FILE );
require_once(SRC_ROOT.'/mold/validation/UtilValidation.class.php');
require_once (SRC_ROOT.'/mold/lib/index/FormMoldHistory.class.php');
require_once (SRC_ROOT.'/mold/lib/UtilBussinesscode.class.php');
require_once (SRC_ROOT.'/mold/lib/UtilMold.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilGroup.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilUser.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilProduct.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilCompany.class.php');

$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

setcookie("strSessionID", $_REQUEST["strSessionID"]);

// ���å�����ǧ
$objAuth = fncIsSession($_REQUEST["strSessionID"], $objAuth, $objDB );

// 1900 �ⷿ����
if ( !fncCheckAuthority( DEF_FUNCTION_MR0, $objAuth ) )
{
	fncOutputError ( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// 1904 �ⷿĢɼ�����ʽ�����
if ( !fncCheckAuthority( DEF_FUNCTION_MR4, $objAuth ) )
{
	fncOutputError ( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// �ѥ�᡼������
$moldReportId = $_REQUEST["MoldReportId"];
$revision = $_REQUEST["Revision"];
$version = $_REQUEST["Version"];

if (!$moldReportId || !(0 <= $revision) || !(0 <= $version))
{
	// ����μ����˼��Ԥ��ޤ���
	fncOutputError(9061, DEF_ERROR, "", TRUE, "", $objDB);
}

// �桼�ƥ���ƥ����饹�Υ��󥹥��󥹼���
$utilMold = UtilMold::getInstance();
$utilValidation = UtilValidation::getInstance();
$utilBussinesscode = UtilBussinesscode::getInstance();
$utilCompany = UtilCompany::getInstance();
$utilGroup = UtilGroup::getInstance();
$utilUser = UtilUser::getInstance();
$utilProduct = UtilProduct::getInstance();

try
{
	// �ⷿĢɼ�κ���
	$report = $utilMold->selectMoldReport($moldReportId, $revision, $version);

	// Ģɼ���ơ���������λ�ξ��
	if ($report[TableMoldReport::Status] == '50')
	{
		fncOutputError(9069, DEF_ERROR, "", TRUE, "", $objDB);
	}

	// �ⷿĢɼ�ܺ٤κ���
	$details = $utilMold->selectMoldReportDetail($moldReportId, $revision);

	// ������(�ܵ�) -> ɽ����ҥ����ɤκ���
	$customerCode = $report[TableMoldReport::CustomerCode];
	$displayCustomerCode = $utilCompany->selectDisplayCodeByCompanyCode($customerCode);

	// ô������ -> ɽ�����롼�ץ����ɤκ���
	$groupCode = $report[TableMoldReport::KuwagataGroupCode];
	$displayGroupCode = $utilGroup->selectDisplayCodeByGroupCode($groupCode);

	// ô���� -> ɽ���桼�������ɤκ���
	$userCode = $report[TableMoldReport::KuwagataUserCode];
	$displayUserCode = $utilUser->selectDisplayCodeByUserCode($userCode);

	switch ($report[TableMoldReport::ReportCategory])
	{
		// ��ư��/�ֵ��Ǥξ��
		case "10":
		case "20":
			// �ݴɹ���
			$srcFactoryCode = $report[TableMoldReport::SourceFactory];
			$displaySrcFactoryCode = $utilCompany->selectDisplayCodeByCompanyCode($srcFactoryCode);
			// ��ư�蹩��
			$dstFactoryCode = $report[TableMoldReport::DestinationFactory];
			$displayDstFactoryCode = $utilCompany->selectDisplayCodeByCompanyCode($dstFactoryCode);
			break;
	}
}
catch (SQLException $e)
{
	// ���顼������
	error_log($e->getMessage(), 0);
	// ����μ����˼��Ԥ��ޤ���
	fncOutputError(9061, DEF_ERROR, "�����ʥǡ������оݤΥǡ������ѹ����줿��ǽ��������ޤ���", TRUE, "", $objDB);
}

// �ִ�ʸ���󷲤κ���
$replacement = array();

// �ⷿĢɼID
$replacement["MoldReportId"] = $moldReportId;
// ��ӥ����
$replacement["Revision"] = sprintf("00", $revision);

// ���ʥ�����
$replacement["Header_ProductCode"] =$report[TableMoldReport::ProductCode];
$replacement["Detail_ProductCode"] =$report[TableMoldReport::ProductCode];

// ������
$replacement[FormMoldReport::RequestDate] = str_replace ( "-", "/", $report[TableMoldReport::RequestDate]);
// ��˾��
$replacement[FormMoldReport::ActionRequestDate] = str_replace ( "-", "/", $report[TableMoldReport::ActionRequestDate]);
// �ֵ�ͽ����
$replacement[FormMoldReport::ReturnSchedule] = str_replace ( "-", "/", $report[TableMoldReport::ReturnSchedule]);

// Ģɼ��ʬ
$replacement[FormMoldReport::ReportCategory] = $report[TableMoldReport::ReportCategory];
// �����ʬ
$replacement[FormMoldReport::RequestCategory] = $report[TableMoldReport::RequestCategory];
// ��ư��ˡ
$replacement[FormMoldReport::TransferMethod] = $report[TableMoldReport::TransferMethod];
// �ؼ���ʬ
$replacement[FormMoldReport::InstructionCategory] = $report[TableMoldReport::InstructionCategory];
// ������ν���
$replacement[FormMoldReport::FinalKeep] = $report[TableMoldReport::FinalKeep];

// ������(�ܵ�)
$replacement[FormMoldReport::CustomerCode] = $displayCustomerCode;
// ô������
$replacement[FormMoldReport::KuwagataGroupCode] = $displayGroupCode;
// ô����
$replacement[FormMoldReport::KuwagataUserCode] = $displayUserCode;

// �ݴɹ���
$replacement[FormMoldReport::SourceFactory] = $displaySrcFactoryCode ;
// ��ư�蹩��
$replacement[FormMoldReport::DestinationFactory] = $displayDstFactoryCode;

// ����¾
$replacement[FormMoldReport::Note] = $report[TableMoldReport::Note];
// ������
$replacement[FormMoldReport::MarginalNote] = $report[TableMoldReport::MarginalNote];

// �ƥ�ץ졼���ɤ߹���
$template = fncGetReplacedHtmlWithBase("base_mold_noframes.html", "mr/modify/mr_modify.tmpl", $replacement ,$objAuth );

// DOMDocument
$doc = new DOMDocument();

// �ѡ������顼����
libxml_use_internal_errors(true);
// DOM�ѡ���
$doc->loadHTML($template);
// �ѡ������顼���ꥢ
libxml_clear_errors();
// �ѡ������顼�������
libxml_use_internal_errors(false);

// ����Ѥߤζⷿ�ꥹ��div�μ���
$initMoldInfo = $doc->getElementById("init-mold-info");

// �ⷿĢɼ�ܺ٤η��ʬ����
foreach ($details as $num => $row)
{
	$index = $num + 1;

	// �ⷿNO
	$moldNo = $row[TableMoldReportDetail::MoldNo];
	// �ⷿ����
	$desc = $row[TableMoldReportDetail::MoldDescription];

	// �ⷿ������������input���Ǻ���
	$inputMoldNo = $doc->createElement("input");
	$inputMoldNo->setAttribute("class", "init-mold-info__record");
	$inputMoldNo->setAttribute("index", $index);
	$inputMoldNo->setAttribute("moldno", toUTF8($moldNo));
	$inputMoldNo->setAttribute("desc",  toUTF8($desc));
	$inputMoldNo->setAttribute("style", "display:none");
	$inputMoldNo->setAttribute("disabled", "disabled");
	// div > input
	$initMoldInfo->appendChild($inputMoldNo);
}

// COOKIE����
setcookie("MoldReportId", $moldReportId);
setcookie("Revision", $revision);
setcookie("Version", $version);

// HTML����
echo $doc->saveHTML();

function toUTF8($str)
{
	return htmlspecialchars(mb_convert_encoding($str, "utf-8", "eucjp-win"));
}
