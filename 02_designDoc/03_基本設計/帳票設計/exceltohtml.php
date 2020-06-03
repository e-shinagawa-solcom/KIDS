<? 
// ���Υե����뼫�Ȥ�UTF-8�Ǥ��뤳��

// ���ܸ���б������硢����1�Ԥ�ɬ��
ini_set('default_charset', 'UTF-8');

// �ɤ߹���
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

