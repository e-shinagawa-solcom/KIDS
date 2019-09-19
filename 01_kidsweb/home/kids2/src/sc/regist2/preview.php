<?php
// ----------------------------------------------------------------------------
/**
*       納品書プレビュー
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
*       処理概要
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------

//include('conf.inc');
//require (LIB_FILE);
//require (SRC_ROOT."sc/cmn/lib_scr.php");

// 日本語に対応する場合、この1行が必要
ini_set('default_charset', 'UTF-8');

// 読み込み
include 'conf.inc';
require LIB_FILE;
require PATH_HOME . "/vendor/autoload.php";
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Html;

// spreadsheetに渡す値は必ずUTF-8で渡すため、
// EUC-JPで記述されたソースの場合は渡す値のコード変換が必要
// ※phpソース自身をUTF-8にすれば変換処理は不要）
$file = mb_convert_encoding('納品書temple_B社_連絡書付.xlsx', 'UTF-8','EUC-JP' );
$sheetname = mb_convert_encoding('B社専用', 'UTF-8','EUC-JP' );
$cellValue = mb_convert_encoding('個別に値をセット', 'UTF-8','EUC-JP' );
$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);

//
// ブックに値を設定する
$spreadsheet->GetSheetByName($sheetname)->GetCell('C3')->SetValue($cellValue);
//

$writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($spreadsheet);
$output = $writer->generateHTMLHeader();
$output .= $writer->generateStyles(true);
$output .= $writer->generateSheetData();
$output .= $writer->generateHTMLFooter();

// phpソース自身をUTF-8の場合は変換必要
// echo mb_convert_encoding($output, 'EUC-JP', 'UTF-8');
echo $output;



/*
define ( "PATH_HOME",	"E:/Source/Repos/solcom-net/KIDS/01_kidsweb/home/kids2" );
require (PATH_HOME . "/vendor/autoload.php");
define ( "REPORT_TMPDIR",	PATH_HOME . "/report_tmp/" );
$filepath = REPORT_TMPDIR . "納品書temple_B社_連絡書付.xls";

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
//    登録処理
// --------------------------------
/*
if($_POST["strMode"] == "regist"){
	// トランザクション開始
	$objDB->transactionBegin();

	// 売上マスタ登録
	if (!fncRegistSales($lngSalesNo, $objDB, $objAuth))
	{
		fncOutputError ( 9051, DEF_FATAL, "売上マスタ登録失敗", TRUE, "", $objDB );
	}

	// 売上明細登録
	if (!fncRegistSalesDetail($lngSlipNo, $objDB))
	{
		fncOutputError ( 9051, DEF_FATAL, "売上明細登録失敗", TRUE, "", $objDB );
	}

	// 納品伝票マスタ登録
	if (!fncRegistSlip($strSlipCode, $objDB, $objAuth))	
	{
		fncOutputError ( 9051, DEF_FATAL, "納品伝票マスタ登録失敗", TRUE, "", $objDB );
	}

	// 納品伝票明細登録
	if (!fncRegistSlipDetail($lngSlipNo, $objDB))
	{
		fncOutputError ( 9051, DEF_FATAL, "納品伝票明細登録失敗", TRUE, "", $objDB );
	}

	// トランザクションコミット
	$objDB->transactionCommit();

	// 登録完了画面の表示
	$aryDeleteData = $aryHeadResult;
	$aryDeleteData["strAction"] = "/sc/search2/index.php?strSessionID=";
	$aryDeleteData["strSessionID"] = $aryData["strSessionID"];

	// 言語コード：日本語
	$aryDeleteData["lngLanguageCode"] = 1;

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "sc/finish2/remove_parts.tmpl" );

	// テンプレート生成
	$objTemplate->replace( $aryDeleteData );
	$objTemplate->complete();

	// HTML出力
	echo $objTemplate->strTemplate;

	$objDB->close();

	return true;
}

// --------------------------------
//    プレビュー表示
// --------------------------------


return true;

// エラー画面への遷移
function MoveToErrorPage($strMessage){
	
	// 言語コード：日本語
	$aryHtml["lngLanguageCode"] = 1;

	// エラーメッセージの設定
	$aryHtml["strErrorMessage"] = $strMessage;

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "/result/error/parts.tmpl" );
	
	// テンプレート生成
	$objTemplate->replace( $aryHtml );
	$objTemplate->complete();

	// HTML出力
	echo $objTemplate->strTemplate;

	exit;
}

*/

?>