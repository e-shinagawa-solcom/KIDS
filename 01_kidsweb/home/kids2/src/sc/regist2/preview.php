<?php
// ----------------------------------------------------------------------------
/**
*       Ǽ�ʽ�ץ�ӥ塼
*
*
*       @package    K.I.D.S.
*       @license    http://www.kuwagata.co.jp/
*       @copyright  KUWAGATA CO., LTD.
*       @author     K.I.D.S. Groups <info@kids-groups.com>
*       @access     public
*       @version    2.00
*
*
*       ��������
*
*       ��������
*
*/
// ----------------------------------------------------------------------------

include('conf.inc');
require (LIB_FILE);
require (SRC_ROOT."sc/cmn/lib_scr.php");
require PATH_HOME . "/vendor/autoload.php";
/*
$json = json_encode($_POST["headerData"], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
$json = htmlspecialchars($json, ENT_QUOTES, 'UTF-8');
$aryData["HEADER_DATA"] = $json;

if ($_POST["jsonHeaderData"]){
	$jsonDecode = json_decode($_POST["jsonHeaderData"], true);
	$data1 = $jsonDecode["data1"];
	header("Content-Type: text/plain");
	echo mb_convert_encoding($data1, 'EUC-JP', 'UTF-8');
	return true;
}
*/
// ------------------------
//   POST�ѥ�᡼������
// ------------------------



// ------------------------
//   Ģɼɽ��
// ------------------------
// ���ܸ���б������硢����1�Ԥ�ɬ��
ini_set('default_charset', 'UTF-8');

// �ɤ߹���
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Html;


$file = mb_convert_encoding('template\Ǽ�ʽ�temple_B��_Ϣ�����.xlsx', 'UTF-8','EUC-JP' );
$sheetname = mb_convert_encoding('B������', 'UTF-8','EUC-JP' );
$cellValue = mb_convert_encoding('���̤��ͤ򥻥å�', 'UTF-8','EUC-JP' );
$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);

// �֥å����ͤ����ꤹ��
$spreadsheet->GetSheetByName($sheetname)->GetCell('C3')->SetValue($cellValue);

$writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($spreadsheet);
//$outHeader = $writer->generateHTMLHeader();
$outStyle = $writer->generateStyles(true);
$outSheetData = $writer->generateSheetData();
//$outFooter .= $writer->generateHTMLFooter();

//TODO:���٤ο����������֤�
$outStyle = mb_convert_encoding($outStyle, 'EUC-JP', 'UTF-8');
$outSheetData = mb_convert_encoding($outSheetData, 'EUC-JP', 'UTF-8');
$aryData["PREVIEW_STYLE"] = $outStyle;
$aryData["PREVIEW_DATA"] = $outSheetData;

//$out2 = mb_convert_encoding($output, 'EUC-JP', 'UTF-8');


// �ƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "sc/regist2/preview.tmpl" );
// �ƥ�ץ졼������
$objTemplate->replace( $aryData );
$objTemplate->complete();

//header("Content-Type: text/plain");
echo $objTemplate->strTemplate;

return true;




/*
define ( "PATH_HOME",	"E:/Source/Repos/solcom-net/KIDS/01_kidsweb/home/kids2" );
require (PATH_HOME . "/vendor/autoload.php");
define ( "REPORT_TMPDIR",	PATH_HOME . "/report_tmp/" );
$filepath = REPORT_TMPDIR . "Ǽ�ʽ�temple_B��_Ϣ�����.xls";

$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
$spreadsheet = $reader->load($filepath);

$writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($spreadsheet);
$output = $writer->generateHTMLHeader();
$output .= $writer->generateStyles(true);
$output .= $writer->generateSheetData();
$output .= $writer->generateHTMLFooter();
echo mb_convert_encoding($output, 'EUC-JP', 'UTF-8');
*/


// --------------------------------
//    ��Ͽ����
// --------------------------------
/*
if($_POST["strMode"] == "regist"){
	// �ȥ�󥶥�����󳫻�
	$objDB->transactionBegin();

	// ���ޥ�����Ͽ
	if (!fncRegistSales($lngSalesNo, $objDB, $objAuth))
	{
		fncOutputError ( 9051, DEF_FATAL, "���ޥ�����Ͽ����", TRUE, "", $objDB );
	}

	// ���������Ͽ
	if (!fncRegistSalesDetail($lngSlipNo, $objDB))
	{
		fncOutputError ( 9051, DEF_FATAL, "���������Ͽ����", TRUE, "", $objDB );
	}

	// Ǽ����ɼ�ޥ�����Ͽ
	if (!fncRegistSlip($strSlipCode, $objDB, $objAuth))	
	{
		fncOutputError ( 9051, DEF_FATAL, "Ǽ����ɼ�ޥ�����Ͽ����", TRUE, "", $objDB );
	}

	// Ǽ����ɼ������Ͽ
	if (!fncRegistSlipDetail($lngSlipNo, $objDB))
	{
		fncOutputError ( 9051, DEF_FATAL, "Ǽ����ɼ������Ͽ����", TRUE, "", $objDB );
	}

	// �ȥ�󥶥�����󥳥ߥå�
	$objDB->transactionCommit();

	// ��Ͽ��λ���̤�ɽ��
	$aryDeleteData = $aryHeadResult;
	$aryDeleteData["strAction"] = "/sc/search2/index.php?strSessionID=";
	$aryDeleteData["strSessionID"] = $aryData["strSessionID"];

	// ���쥳���ɡ����ܸ�
	$aryDeleteData["lngLanguageCode"] = 1;

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "sc/finish2/remove_parts.tmpl" );

	// �ƥ�ץ졼������
	$objTemplate->replace( $aryDeleteData );
	$objTemplate->complete();

	// HTML����
	echo $objTemplate->strTemplate;

	$objDB->close();

	return true;
}

// --------------------------------
//    �ץ�ӥ塼ɽ��
// --------------------------------


return true;


// ���顼���̤ؤ�����
function MoveToErrorPage($strMessage){
	
	// ���쥳���ɡ����ܸ�
	$aryHtml["lngLanguageCode"] = 1;

	// ���顼��å�����������
	$aryHtml["strErrorMessage"] = $strMessage;

	// �ƥ�ץ졼���ɤ߹���
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "/result/error/parts.tmpl" );
	
	// �ƥ�ץ졼������
	$objTemplate->replace( $aryHtml );
	$objTemplate->complete();

	// HTML����
	echo $objTemplate->strTemplate;

	exit;
}
*/

?>