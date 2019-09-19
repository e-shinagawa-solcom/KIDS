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

//include('conf.inc');
//require (LIB_FILE);
//require (SRC_ROOT."sc/cmn/lib_scr.php");

// ���ܸ���б������硢����1�Ԥ�ɬ��
ini_set('default_charset', 'UTF-8');

// �ɤ߹���
include 'conf.inc';
require LIB_FILE;
require PATH_HOME . "/vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Html;

// spreadsheet���Ϥ��ͤ�ɬ��UTF-8���Ϥ����ᡢ
// EUC-JP�ǵ��Ҥ��줿�������ξ����Ϥ��ͤΥ������Ѵ���ɬ��
// ��php���������Ȥ�UTF-8�ˤ�����Ѵ����������ס�
$file = mb_convert_encoding('Ǽ�ʽ�temple_B��_Ϣ�����.xlsx', 'UTF-8','EUC-JP' );
$sheetname = mb_convert_encoding('B������', 'UTF-8','EUC-JP' );
$cellValue = mb_convert_encoding('���̤��ͤ򥻥å�', 'UTF-8','EUC-JP' );
$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);

//
// �֥å����ͤ����ꤹ��
$spreadsheet->GetSheetByName($sheetname)->GetCell('C3')->SetValue($cellValue);
//

$writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($spreadsheet);
$output = $writer->generateHTMLHeader();
$output .= $writer->generateStyles(true);
$output .= $writer->generateSheetData();
$output .= $writer->generateHTMLFooter();

// php���������Ȥ�UTF-8�ξ����Ѵ�ɬ��
// echo mb_convert_encoding($output, 'EUC-JP', 'UTF-8');
echo $output;



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