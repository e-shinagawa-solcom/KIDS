<?php

// ----------------------------------------------------------------------------
/**
*       �ⷿ�������  ��������
*
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

// ���³�ǧ
// 1800 �ⷿ����
if ( !fncCheckAuthority( DEF_FUNCTION_MM0, $objAuth ) )
{
	fncOutputError ( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// 1804 �ⷿ�����ʽ�����
if ( !fncCheckAuthority( DEF_FUNCTION_MM4, $objAuth ) )
{
	fncOutputError ( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
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

// �桼�ƥ���ƥ����饹�Υ��󥹥��󥹼���
$utilMold = UtilMold::getInstance();
$utilValidation = UtilValidation::getInstance();
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

	// ���ʥ�����/̾��
	$productCode = $infoMold[TableMold::ProductCode];
	$reviseCode = $infoMold[TableMold::strReviseCode];
echo "reviseCode:" . $reviseCode . "<br>";
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
$replacement[FormMoldHistory::ProductCode] = $productCode;
$replacement[FormMoldHistory::strReviseCode] = $reviseCode;
$replacement[FormMoldHistory::ProductName] = $productName;
$replacement[TableMoldHistory::SourceFactory] = $displaySrcFactoryCode;
$replacement[FormMoldHistory::SourceFactoryName] = $displaySrcFactoryName;
$replacement[TableMoldHistory::DestinationFactory] = $displayDstFactoryCode;
$replacement[FormMoldHistory::DestinationFactoryName] = $displayDstFactoryName;
$replacement["DummyStatus"] = $record[TableMoldHistory::Status];
// �ƥ�ץ졼���ɤ߹���
echo fncGetReplacedHtmlWithBase("base_mold_noframes.html", "mm/modify/mm_modify.tmpl", $replacement ,$objAuth );
