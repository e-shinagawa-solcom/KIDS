<?
// 設定読み込み
include 'conf.inc';
//クラスファイルの読み込み
require_once '../lcModel/db_common.php';
require_once '../lcModel/lcModelCommon.php';
require_once '../lcModel/kidscore_common.php';
require_once '../lcModel/report_common.php';
require_once '../lcModel/lcreport.php';
// ライブラリ読み込み
require LIB_FILE;

//値の取得
$postdata = file_get_contents("php://input");
$data = json_decode($postdata, true);
$objDB = new clsDB();
$objAuth = new clsAuth();
$objDB->open("", "", "", "");
//経理サブシステムDB接続
$lcModel = new lcModel();

//値が存在しない場合は通常の POST で受ける
if ($data == null) {
    $data = $_POST;
}
// セッション確認
$objAuth = fncIsSession($data["sessionid"], $objAuth, $objDB);

//ユーザーID取得(半角スペースがあるため)
$usrId = trim($objAuth->UserID);

//結果配列
$result = array();
$result['report_6'] = array();
$result['report_5'] = array();
$result['report_4_open'] = array();
$result['report_4_ship'] = array();
$result['report_3'] = array();
$result['report_2'] = array();
$result['report_1_open'] = array();
$result['report_1_ship'] = array();

// パラメータの取得
// 対象年月
$objectYm = $data["objectYm"];

//通貨区分リストの取得
$currencyClassLst = fncGetCurrencyClassList($objDB);
//通貨区分(未承認含む)リストの取得
$currencyClassAllLst = fncGetCurrencyClassListAll($objDB);
// 銀行マスタ情報の取得
$bankLst = fncGetValidBankInfo($objDB);

// 出力
if ($data["impletterChk"] == "true") {
    if ($currencyClassLst && count($currencyClassLst) > 0) {
        $num = 0;
        for ($i = 0; $i < count($currencyClassLst); $i++) {
            $currencyClass = $currencyClassLst[$i]["currencyclass"];
            // 輸入信用状発行情報の出力
            $report_6 = reportSixOutput($objDB, $spreadsheet, $currencyClass, $bankLst, $data);
            if (!empty($report_6)) {
                $result['report_6'][$num]['report_header'] = $report_6['report_header'];
                $result['report_6'][$num]['report_main'] = $report_6['report_main'];
                $num = $num + 1;
            }
        }
    }
}
$result['sessionid'] = $data['sessionid'];
if ($data["setChk"] == "true") {
    if ($currencyClassLst && count($currencyClassLst) > 0) {
        $num_1_open = 0;
        $num_1_ship = 0;
        $num_2 = 0;
        $num_3 = 0;
        $num_4_open = 0;
        $num_4_ship = 0;
        for ($i = 0; $i < count($currencyClassLst); $i++) {
            $currencyClass = $currencyClassLst[$i]["currencyclass"];
            // LCOpen情報(Beneficiary・BK別合計)ーオープン月の出力
            $report_1_open = reportOneOutput($objDB, $spreadsheet, $currencyClass, $bankLst, $objectYm, 1);
            if (!empty($report_1_open)) {
                $result['report_1_open'][$num_1_open]['report_header'] = $report_1_open['report_header'];
                $result['report_1_open'][$num_1_open]['report_main'] = $report_1_open['report_main'];
                $num_1_open += 1;
            }

            // LCOpen情報(Beneficiary・BK別合計)ー船積月の出力
            $report_1_ship = reportOneOutput($objDB, $spreadsheet, $currencyClass, $bankLst, $objectYm, 2);
            if (!empty($report_1_ship)) {
                $result['report_1_ship'][$num_1_ship]['report_header'] = $report_1_ship['report_header'];
                $result['report_1_ship'][$num_1_ship]['report_main'] = $report_1_ship['report_main'];
                $num_1_ship += 1;
            }

            // L/C Open情報(LC別合計）の出力
            $report_2 = reportTwoOutput($objDB, $spreadsheet, $currencyClass, $objectYm);
            if (!empty($report_2)) {
                $result['report_2'][$num_2]['report_header'] = $report_2['report_header'];
                $result['report_2'][$num_2]['report_main'] = $report_2['report_main'];
                $num_2 += 1;
            }
            // L/C Open情報(LC別明細）の出力
            $report_3 = reportThreeOutput($objDB, $spreadsheet, $currencyClass, $objectYm);
            if (!empty($report_3)) {
                $result['report_3'][$num_3]['report_header'] = $report_3['report_header'];
                $result['report_3'][$num_3]['report_main'] = $report_3['report_main'];
                $num_3 += 1;
            }
            // L/C Open情報（Open月・Beneficiary別L/C発行予定集計表）の出力
            $report_4_open = reportFourOutput($objDB, $spreadsheet, $currencyClass, $objectYm, 3);
            if (!empty($report_4_open)) {
                $result['report_4_open'][$num_4_open]['report_header'] = $report_4_open['report_header'];
                $result['report_4_open'][$num_4_open]['report_main'] = $report_4_open['report_main'];
                $num_4_open += 1;
            }
            // L/C Open情報（船積月・Beneficiary別L/C発行予定集計表）の出力
            $report_4_ship = reportFourOutput($objDB, $spreadsheet, $currencyClass, $objectYm, 4);
            if (!empty($report_4_ship)) {
                $result['report_4_ship'][$num_4_ship]['report_header'] = $report_4_ship['report_header'];
                $result['report_4_ship'][$num_4_ship]['report_main'] = $report_4_ship['report_main'];
                $num_4_ship += 1;
            }
        }
    }

}

if ($data["unsetChk"] == "true") {
    if ($currencyClassAllLst && count($currencyClassAllLst) > 0) {
        $num_5 = 0;
        for ($i = 0; $i < count($currencyClassAllLst); $i++) {
            $currencyClass = $currencyClassAllLst[$i]["currencyclass"];
            // L/C 未決済リストの出力
            $report_5 = reportFiveOutput($objDB, $spreadsheet, $currencyClass, $bankLst, $data);
            if (!empty($report_5)) {
                $result['report_5'][$num_5]['report_header'] = $report_5['report_header'];
                $result['report_5'][$num_5]['report_main'] = $report_5['report_main'];
                $num_5 += 1;
            }
        }
    }
}

$objDB->close();
$lcModel->close();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html lang="ja">
<head>
<title>K.I.D.S.</title>
<meta http-equiv="content-type" content="text/html; charset=euc-jp">
<script src="/cmn/jquery/jquery-3.1.0.js"></script>
	<script src="/cmn/jquery/ui/jquery-ui-1.12.0.js"></script>
	<script src="/cmn/jquery/jquery-cookie-1.4.1.js"></script>
    <script src="/cmn/jquery/validation/jquery.validate.js"></script>
<script type="text/javascript" language="javascript" src="/lc/report/functions.js"></script>
</head>
<?echo "<script>$(function(){lcreport6Init('" . json_encode($result) . "');});</script>"; ?>

<frameset rows="40,1,*" frameborder="0" border="0" framespacing="0">
	<frame src="button.php?strSessionID=<?echo $data["sessionid"]; ?>&printObj=report&nextUrl=" name="button" scrolling="no" noresize>
	<frame src="/lc/report/printset/borders.html" scrolling="no" noresize>
    <frame src="/lc/report/html/6.html" name="report" id="report" resize onload="lcreport()" style="zoom: 1;">
</frameset>
</html>
