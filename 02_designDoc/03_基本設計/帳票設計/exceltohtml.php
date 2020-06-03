<? 
// このファイル自身がUTF-8であること

// 日本語に対応する場合、この1行が必要
ini_set('default_charset', 'UTF-8');

// 読み込み
include 'conf.inc';
require LIB_FILE;
require PATH_HOME . "/vendor/autoload.php";



use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Html;

$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load('slip2.xlsx');
$writer = new \PhpOffice\PhpSpreadsheet\Writer\Html($spreadsheet);
$output = $writer->generateHTMLHeader();
$output .= $writer->generateStyles(true);
$output .= $writer->generateSheetData();
$output .= $writer->generateHTMLFooter();
echo mb_convert_encoding($output, 'EUC-JP', 'UTF-8');

