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
//   POSTパラメータ退避
// ------------------------



// ------------------------
//   帳票表示
// ------------------------
// 日本語に対応する場合、この1行が必要
ini_set('default_charset', 'UTF-8');

// 読み込み
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Html;


$file = mb_convert_encoding('template\納品書temple_B社_連絡書付.xlsx', 'UTF-8','EUC-JP' );
$sheetname = mb_convert_encoding('B社専用', 'UTF-8','EUC-JP' );
$cellValue = mb_convert_encoding('個別に値をセット', 'UTF-8','EUC-JP' );
$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);

// ブックに値を設定する
$spreadsheet->GetSheetByName($sheetname)->GetCell('C3')->SetValue($cellValue);

$writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($spreadsheet);
//$outHeader = $writer->generateHTMLHeader();
$outStyle = $writer->generateStyles(true);
$outSheetData = $writer->generateSheetData();
//$outFooter .= $writer->generateHTMLFooter();

//TODO:明細の数だけ繰り返す
$outStyle = mb_convert_encoding($outStyle, 'EUC-JP', 'UTF-8');
$outSheetData = mb_convert_encoding($outSheetData, 'EUC-JP', 'UTF-8');
$aryData["PREVIEW_STYLE"] = $outStyle;
$aryData["PREVIEW_DATA"] = $outSheetData;

//$out2 = mb_convert_encoding($output, 'EUC-JP', 'UTF-8');


// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "sc/regist2/preview.tmpl" );
// テンプレート生成
$objTemplate->replace( $aryData );
$objTemplate->complete();

//header("Content-Type: text/plain");
echo $objTemplate->strTemplate;

return true;




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